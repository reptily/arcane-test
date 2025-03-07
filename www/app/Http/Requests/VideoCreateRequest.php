<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VideoCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'min:3', 'max:1000'],
            'url'   => ['required', 'regex:/^(http:\/\/|https:\/\/|www\.).*(\.mp4|\.avi|\.mpeg|\.mpeg4|\.mkv|\.webm)$/u']
        ];
    }

    public function messages()
    {
        return [
          'url.regex'  => 'The url format is invalid. Example: http://example.com/video.mp4',
        ];
    }
}
