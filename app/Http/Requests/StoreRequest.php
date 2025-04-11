<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'google_map_url' => [
                'required',
                'url',
                'regex:/^https:\/\/(goo\.gl\/maps|maps\.app\.goo\.gl)\/[A-Za-z0-9_-]{5,}$/',
            ],
        ];
    }

    public function messages()
    {
        return [
        'google_map_url.regex' => '有効なGoogleマップの共有リンクを入力してください。',
        ];
    }

    public function attributes()
    {
        return [
            'google_map_url' => 'GoogleMapの共有リンク',
        ];
    }
}
