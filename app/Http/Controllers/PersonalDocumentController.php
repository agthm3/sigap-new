<?php

namespace App\Http\Controllers;

use App\Models\PersonalDocument;
use App\Repositories\PersonalDocumentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PersonalDocumentController extends Controller
{
    public function __construct(private PersonalDocumentRepository $repo){}

    /**
     * Otorisasi KHUSUS Manajemen (Ubah PIN, Hapus PIN, Reveal PIN).
     * Hanya boleh dilakukan oleh Pemilik atau Admin/Superadmin.
     */
    private function authorizeManagement(PersonalDocument $doc): void
    {
        $user = Auth::user();
        if ($doc->user_id !== $user->id && !$user->hasAnyRole(['admin', 'superadmin'])) {
            abort(403, 'Akses ditolak. Anda tidak memiliki hak untuk mengelola dokumen ini.');
        }
    }

    /**
     * Penentu apakah gembok dokumen terbuka (Unlocked)
     */
    private function isUnlocked(PersonalDocument $doc): bool
    {
        $user = Auth::user();
        
        // 1. Pemilik Dokumen, Admin, dan Superadmin SELALU TERBUKA otomatis tanpa PIN
        if ($doc->user_id === $user->id || $user->hasAnyRole(['admin', 'superadmin'])) {
            return true;
        }

        // 2. Karyawan lain: Jika dokumen TIDAK diatur PIN, maka permanen tidak bisa diakses
        if (!$doc->access_code_hash) {
            return false;
        }

        // 3. Karyawan lain: Jika ada PIN, cek apakah sudah divalidasi di sesi ini
        return session()->has('unlocked_doc_' . $doc->id);
    }

    // --- method untuk MEMBUKA KUNCI dengan PIN ---
    public function unlock(Request $request, PersonalDocument $doc)
    {
        $request->validate(['access_code' => 'required|string']);

        if (!$doc->access_code_hash) {
            return back()->with('warning', 'Dokumen ini bersifat privat dan tidak dapat dibuka.');
        }

        if (Hash::check($request->access_code, $doc->access_code_hash)) {
            session()->put('unlocked_doc_' . $doc->id, true);
            return back()->with('success', 'PIN Benar! Akses dokumen dibuka.');
        }

        return back()->withErrors(['access_code' => 'PIN yang Anda masukkan salah.']);
    }

    // --- unggah sendiri (self-serve) ---
    public function storeSelf(Request $request)
    {
        $user = Auth::user();
        if ($user->status !== 'active') abort(403, 'Akun Anda tidak aktif.');

        $data = $request->validate([
            'type'             => ['required','in:ktp,kk,npwp,bpjs,ijazah,sk,buku_rekening,other'],
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

        $this->repo->storeFor($user, $payload, $request->file('file'), $user);
        return back()->with('success', 'Berkas pribadi berhasil diunggah secara privat.');
    }

    // --- set / ganti kode akses ---
    public function setAccessCode(Request $request, PersonalDocument $doc)
    {
        $this->authorizeManagement($doc);

        $data = $request->validate([
            'access_code'      => ['required','string','min:4','max:50','confirmed'],
            'access_code_hint' => ['nullable','string','max:100'],
        ]);

        $this->repo->setAccessCode($doc, $data['access_code'], $data['access_code_hint'] ?? null);
        return back()->with('success','Kode akses diperbarui.');
    }

    // --- hapus kode akses ---
    public function clearAccessCode(PersonalDocument $doc)
    {
        $this->authorizeManagement($doc);

        $this->repo->clearAccessCode($doc);
        return back()->with('success','Kode akses dihapus.');
    }

    // --- download berkas ---
    public function download(Request $request, PersonalDocument $doc): StreamedResponse
    {
        if (!$this->isUnlocked($doc)) abort(403, 'Akses ditolak. Silakan masukkan PIN dokumen.');

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

    // --- halaman detail dokumen (show) ---
    public function show(PersonalDocument $doc)
    {
        $user = Auth::user();

        // Tidak ada authorizeManagement di sini, semua karyawan bisa membuka view ini
        // Tapi logic isUnlocked yang akan menentukan mereka lihat gembok atau isi file.
        return view('dashboard.pegawai.doc_show', [
            'doc' => $doc,
            'isOwner' => $doc->user_id === $user->id,
            'isAdmin' => $user->hasAnyRole(['admin', 'superadmin']),
            'isUnlocked' => $this->isUnlocked($doc),
        ]);
    }

    // --- lihat PIN asli ---
    public function reveal(Request $request, PersonalDocument $doc)
    {
        $this->authorizeManagement($doc);

        $user = Auth::user();
        $isAdmin = $user->hasAnyRole(['admin', 'superadmin']);

        // Owner (bukan admin) wajib masukkan password akun sendiri
        if ($doc->user_id === $user->id && !$isAdmin) {
            $data = $request->validate(['password' => ['required','string']]);
            if (!Hash::check($data['password'], $user->password)) {
                return back()->withErrors(['password' => 'Password salah.'])->withInput();
            }
        }

        $code = $doc->access_code_enc ? Crypt::decryptString($doc->access_code_enc) : null;
        if (!$code) return back()->with('warning','Dokumen ini belum memiliki PIN.');

        return back()->with('revealed_code', $code);
    }

    // --- stream / preview ---
    public function preview(Request $request, PersonalDocument $doc): StreamedResponse
    {
        if (!$this->isUnlocked($doc)) abort(403, 'Akses ditolak. Silakan masukkan PIN dokumen.');

        $disk = Storage::disk('private');
        if (!$disk->exists($doc->path)) abort(404, 'File tidak ditemukan.');

        $mime = $doc->mime ?: $disk->mimeType($doc->path) ?: 'application/octet-stream';
        $filename = ($doc->title ?: 'dokumen').'.'.$this->extFromMime($mime);

        return new StreamedResponse(function () use ($disk, $doc) {
            $stream = $disk->readStream($doc->path);
            fpassthru($stream);
            if (is_resource($stream)) fclose($stream);
        }, 200, [
            'Content-Type'        => $mime,
            'Content-Disposition' => 'inline; filename="'.addslashes($filename).'"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}