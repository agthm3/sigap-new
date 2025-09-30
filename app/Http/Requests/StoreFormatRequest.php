<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'title'       => ['required','string','max:255'],
            'category'    => ['required','string','max:100'],
            'description' => ['nullable','string'],
            'lang'        => ['nullable','in:id,en'],
            'orientation' => ['nullable','in:portrait,landscape'],
            'file_type'   => ['required','in:DOCX,PDF,PNG,PPTX,XLSX,SVG'],
            'file'        => ['required','file','max:10240'], // 10MB
            'privacy'     => ['required','in:public,private'],
            'access_code' => ['nullable','required_if:privacy,private','min:6'],
            'tags'        => ['nullable','string'],
        ];
    }
}
