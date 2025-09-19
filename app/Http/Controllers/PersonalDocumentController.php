<?php

namespace App\Http\Controllers;

use App\Models\PersonalDocument;
use App\Repositories\PersonalDocumentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PersonalDocumentController extends Controller
{
    public function __construct(private PersonalDocumentRepository $repo){}

    // unggah sendiri (self-serve)
    public function storeSelf(Request $request)
    {
        $user = Auth::user();
        if ($user->status !== 'active') abort(403);

        $data = $request->validate([
            'type'             => ['required','in:ktp,kk,npwp,bpjs,ijazah,sk,other'],
            'title'            => ['required','string','max:255'],
            'file'             => ['required','file','mimes:pdf,jpg,jpeg,png','max:4096'],
            'access_code'      => ['nullable','string','min:4','max:50'],
            'access_code_hint' => ['nullable','string','max:100'],
        ]);

        $payload = [
            'type'               => $data['type'],
            'title'              => $data['title'],
            'status'             => 'pending',
            'access_code_enc'    => null,
            'access_code_hash'   => null,
            'access_code_set_at' => null,
            'access_code_hint'   => $data['access_code_hint'] ?? null,
        ];

        if (filled($data['access_code'])) {
            $payload['access_code_enc']    = Crypt::encryptString($data['access_code']);
            $payload['access_code_hash']   = Hash::make($data['access_code']);
            $payload['access_code_set_at'] = now();
        }

        // jangan simpan plain ke DB
        // unset($payload['access_code_plain']);

        $this->repo->storeFor($user, $payload, $request->file('file'), $user);

        return back()->with('success','Berkas diunggah. (Jika diatur) kode akses aktif.');
    }
// --- set / ganti kode akses (pemilik atau admin) ---
    public function setAccessCode(Request $request, PersonalDocument $doc)
    {
        $user = Auth::user();
        // pemilik atau admin boleh
        if (!($doc->user_id === $user->id || $user->hasRole('admin'))) abort(403);

        $data = $request->validate([
            'access_code'      => ['required','string','min:4','max:50','confirmed'],
            'access_code_hint' => ['nullable','string','max:100'],
        ]);

        $this->repo->setAccessCode($doc, $data['access_code'], $data['access_code_hint'] ?? null);

        return back()->with('success','Kode akses diperbarui.');
    }

    // --- hapus kode akses (pemilik atau admin) ---
    public function clearAccessCode(PersonalDocument $doc)
    {
        $user = Auth::user();
        if (!($doc->user_id === $user->id || $user->hasRole('admin'))) abort(403);

        $this->repo->clearAccessCode($doc);

        return back()->with('success','Kode akses dihapus.');
    }

    // --- download (enforcement penuh kita pasang nanti di halaman publik) ---
    public function download(Request $request, PersonalDocument $doc): StreamedResponse
    {
        $user = Auth::user();

        // ADMIN atau PEMILIK dokumen boleh langsung download tanpa kode
        if (!($user->hasRole('admin') || $doc->user_id === $user->id)) {
            // Untuk sekarang kita stop di sini. Nanti alur publik akan melakukan verifikasi kode
            // lalu set session flag sebelum sampai ke sini.
            abort(403, 'Akses terbatas. Verifikasi kode akses via halaman publik.');
        }

        // log akses
        \DB::table('personal_document_logs')->insert([
            'personal_document_id' => $doc->id,
            'acted_by'   => $user->id,
            'action'     => 'download',
            'ip'         => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 1000),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return Storage::disk('private')->download(
            $doc->path,
            "{$doc->title}.".$this->extFromMime($doc->mime)
        );
    }

    private function extFromMime(?string $mime): string
    {
        return match($mime) {
            'application/pdf' => 'pdf',
            'image/jpeg'      => 'jpg',
            'image/png'       => 'png',
            default           => 'file'
        };
    }


    public function show(PersonalDocument $doc)
    {
        $user = Auth::user();

        // Hanya admin atau pemilik yang boleh membuka halaman detail (lihat hint/deskripsi + tombol)
        if (!($user->hasRole('admin') || $doc->user_id === $user->id)) {
            abort(403);
        }

        return view('dashboard.pegawai.doc_show', [
            'doc' => $doc,
            'isOwner' => $doc->user_id === $user->id,
            'isAdmin' => $user->hasRole('admin'),
        ]);
    }

    public function reveal(Request $request, PersonalDocument $doc)
    {
        $user = Auth::user();

        if (!($user->hasRole('admin') || $doc->user_id === $user->id)) {
            abort(403);
        }

        // Owner wajib masukkan password akun sendiri
        if ($doc->user_id === $user->id && !$user->hasRole('admin')) {
            $data = $request->validate([
                'password' => ['required','string'],
            ]);

            if (!Hash::check($data['password'], $user->password)) {
                return back()->withErrors(['password' => 'Password salah.'])->withInput();
            }
        }

        // Ambil plaintext dari kolom terenkripsi
        $code = $doc->access_code_enc ? Crypt::decryptString($doc->access_code_enc) : null;

        // Audit log
        DB::table('personal_document_logs')->insert([
            'personal_document_id' => $doc->id,
            'acted_by'   => $user->id,
            'action'     => 'reveal_code',
            'ip'         => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 1000),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (!$code) {
            return back()->with('warning','Dokumen ini belum memiliki kode akses.');
        }

        // Kirim ke view dalam sesi (flash) agar tidak tampil permanen di HTML statis/cache
        return back()->with('revealed_code', $code);
    }

    public function preview(Request $request, PersonalDocument $doc): StreamedResponse
    {
        $user = Auth::user();

        // Hanya admin atau pemilik yang boleh PREVIEW (sama seperti show)
        if (!($user->hasRole('admin') || $doc->user_id === $user->id)) {
            abort(403);
        }

        // Log preview
        DB::table('personal_document_logs')->insert([
            'personal_document_id' => $doc->id,
            'acted_by'   => $user->id,
            'action'     => 'preview',
            'ip'         => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 1000),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $disk = Storage::disk('private');
        if (!$disk->exists($doc->path)) {
            abort(404);
        }

        $mime = $doc->mime ?: $disk->mimeType($doc->path) ?: 'application/octet-stream';
        $filename = ($doc->title ?: 'dokumen').'.'.$this->extFromMime($mime);

        // Stream inline (bukan attachment) agar bisa dipreview di browser
        return new StreamedResponse(function () use ($disk, $doc) {
            $stream = $disk->readStream($doc->path);
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type'        => $mime,
            'Content-Disposition' => 'inline; filename="'.addslashes($filename).'"',
            'X-Content-Type-Options' => 'nosniff',
            // Opsi cache ringan (sesuaikan kebutuhan):
            // 'Cache-Control' => 'private, max-age=60',
        ]);
    }
}
