<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JsonException;

class ChatbotRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // Cambia esto si necesitas autorización específica
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'type' => 'required|string|in:Basado en Reglas,PLN,Híbrido',
            'knowledgeBase' => 'nullable|string',
            'link' => 'nullable|string|url',
            'temperature' => 'nullable|numeric|min:0|max:1|required_if:type,Híbrido',
            'maxTokens' => 'nullable|integer|min:1|required_if:type,Híbrido',
            'document' => 'nullable|file|mimes:pdf|max:2048',
        ];
    }
}
