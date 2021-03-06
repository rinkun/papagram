<?php

namespace App\Http\Requests\Admin\Slip;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Lang;

class CardsUpdateRequest extends FormRequest
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
             'receipt_date' => [
                'required',
                Rule::unique('cards')->ignore($this->id),
            ]
         ];
     }

     public function attributes()
     {
         return [
             'receipt_date' => Lang::get('card.receipt_date')
         ];
     }
}
