<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFormatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'title'       => ['sometimes','required','string','max:255'],
            'category'    => ['sometimes','required','string','max:100'],
            'description' => ['nullable','string'],
            'lang'        => ['nullable','in:id,en'],
            'orientation' => ['nullable','in:portrait,landscape'],
            'file_type'   => ['nullable','in:DOCX,PDF,PNG,PPTX,XLSX,SVG'],
            'file'        => ['nullable','file','max:10240'], // 10MB
            'privacy'     => ['sometimes','required','in:public,private'],
            'access_code' => ['nullable','min:6'], // hanya dipakai kalau ingin diganti
            'tags'        => ['nullable','string'],
        ];
    }
}
