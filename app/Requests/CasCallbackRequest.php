<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CasCallbackRequest extends FormRequest
{
    public function rules()
    {
        return [
            'ticket' => 'required|string'
        ];
    }

    public function messages()
    {
        return [
            'ticket.required' => 'O ticket CAS é obrigatório',
            'ticket.string' => 'O ticket deve ser uma string válida'
        ];
    }
}
