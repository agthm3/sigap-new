<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResearchStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // sesuaikan dengan policy/role-mu nanti
    }

    public function rules(): array
    {
        return [
            'title'        => ['required','string','max:255'],
            'year'         => ['required','integer','min:2000','max:'.now()->year],
            'type'         => ['nullable','in:internal,kolaborasi,eksternal'],

            'abstract'     => ['required','string'],
            'method'       => ['nullable','string','max:1000'],

            // authors: array of objects
            'authors'                  => ['required','array','min:1'],
            'authors.*.name'           => ['required','string','max:255'],
            'authors.*.affiliation'    => ['nullable','string','max:255'],
            'authors.*.role'           => ['nullable','string','max:100'],
            'authors.*.orcid'          => ['nullable','string','max:50'],

            // corresponding: single object
            'corresponding'            => ['nullable','array'],
            'corresponding.name'       => ['nullable','string','max:255'],
            'corresponding.email'      => ['nullable','email','max:255'],
            'corresponding.phone'      => ['nullable','string','max:50'],

            'tags'         => ['nullable','array'],
            'tags.*'       => ['string','max:60'],

            'stakeholders' => ['nullable','array'],
            'stakeholders.*' => ['string','max:100'],

            'doi'          => ['nullable','string','max:255'],
            'ojs_url'      => ['nullable','url','max:255'],
            'funding'      => ['nullable','string','max:255'],
            'ethics'       => ['nullable','string','max:255'],

            'version'      => ['nullable','string','max:20'],
            'release_note' => ['nullable','string','max:255'],

            'access'       => ['nullable','in:Public,Restricted'],
            'access_reason'=> ['nullable','string','max:500'],
            'license'      => ['nullable','string','max:60'],

            'pdf_file'     => ['required','file','mimes:pdf','max:20480'], // 20MB
            'thumbnail'    => ['nullable','image','max:2048'],

            // datasets: array of {label, file}
            'datasets'             => ['nullable','array'],
            'datasets.*.label'     => ['nullable','string','max:255'],
            'datasets.*.file'      => ['nullable','file','max:51200'], // 50MB per lampiran (opsional)
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'    => 'Judul wajib diisi.',
            'year.required'     => 'Tahun wajib dipilih.',
            'abstract.required' => 'Abstrak wajib diisi.',
            'authors.required'  => 'Minimal 1 penulis.',
            'pdf_file.required' => 'File PDF utama wajib diunggah.',
        ];
    }
}
