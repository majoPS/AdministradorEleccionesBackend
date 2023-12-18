<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FrenteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'NOMBRE_FRENTE' => [
                'required',
                'min:2',
                'max:30',
                //Rule::unique('frentes', 'NOMBRE_FRENTE')->ignore($this->route('frente')),
            ],
            'SIGLA_FRENTE' => [
                'required',
                'min:2',
                'max:15',
                //Rule::unique('frentes', 'SIGLA_FRENTE')->ignore($this->route('frente')),
                //'lt:' . strlen($this->input('NOMBRE_FRENTE')),
            ],
            'LOGO' => 'image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'NOMBRE_FRENTE.required' => 'El nombre del frente es obligatorio.',
            'NOMBRE_FRENTE.min' => 'El nombre del frente debe tener al menos :min caracteres.',
            'NOMBRE_FRENTE.max' => 'El nombre del frente no puede tener más de :max caracteres.',
            'NOMBRE_FRENTE.unique' => 'Ya existe un frente con el nombre ":value".',

            'SIGLA_FRENTE.required' => 'La sigla del frente es obligatoria.',
            'SIGLA_FRENTE.min' => 'La sigla del frente debe tener al menos :min caracteres.',
            'SIGLA_FRENTE.max' => 'La sigla del frente no puede tener más de :max caracteres.',
            'SIGLA_FRENTE.unique' => 'Ya existe un frente con la sigla ":value".',
            'SIGLA_FRENTE.lt' => 'La sigla del frente debe ser más corta que el nombre del frente.',

            'LOGO.required' => 'El logo es obligatorio.',
            'LOGO.image' => 'El archivo debe ser una imagen.',
            'LOGO.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg.',
            'LOGO.max' => 'La imagen no puede ser más grande que :max kilobytes.',
        ];
    }
}
