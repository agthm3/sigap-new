<?php

namespace App\Repositories;

use App\Models\PersonalDocument;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PersonalDocumentRepository
{
 public function storeFor(User $owner, array $data, ?UploadedFile $file, ?User $uploader = null): PersonalDocument
    {
        if ($file) {
            $path = $file->store("pegawai/{$owner->id}/private", 'private');
            $data['path'] = $path;
            $data['mime'] = $file->getClientMimeType();
            $data['size'] = $file->getSize() ?: 0;
        }

        // tangani kode akses bila ada
        if (!empty($data['access_code_plain'])) {
            $data['access_code_hash']  = Hash::make($data['access_code_plain']);
            $data['access_code_set_at'] = now();
            unset($data['access_code_plain']); // jangan pernah simpan plaintext
        }
        
        if (!empty($data['access_code_plain'])) {
            $plain = $data['access_code_plain'];
            $data['access_code_hash']   = Hash::make($plain);
            $data['access_code_enc']    = Crypt::encryptString($plain);
            $data['access_code_set_at'] = now();
            unset($data['access_code_plain']);
        }

        $data['user_id']     = $owner->id;
        $data['uploaded_by'] = $uploader?->id ?? $owner->id;

        return PersonalDocument::create($data);
    }

    public function setAccessCode(PersonalDocument $doc, string $plain, ?string $hint = null): PersonalDocument
    {
        $doc->update([
            'access_code_hash'   => Hash::make($plain),
            'access_code_enc'    => Crypt::encryptString($plain),
            'access_code_hint'   => $hint,
            'access_code_set_at' => now(),
        ]);
        return $doc;
    }


    public function clearAccessCode(PersonalDocument $doc): PersonalDocument
    {
        $doc->update([
            'access_code_hash'   => null,
            'access_code_enc'    => null,
            'access_code_hint'   => null,
            'access_code_set_at' => null,
        ]);
        return $doc;
    }
}
