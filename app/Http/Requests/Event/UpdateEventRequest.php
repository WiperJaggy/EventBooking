<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
class UpdateEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
       $event = $this->route('event');
         return $event && $event->created_by === Auth::id();
    }
 /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'], // 'sometimes' means validate only if present
            'description' => ['sometimes', 'string'],
            'date' => ['sometimes', 'date', 'after_or_equal:today'],
            'capacity' => ['sometimes', 'integer', 'min:1'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ];
    }
      public function messages(): array
    {
        return [
            'date.after_or_equal' => 'The event date must be today or in the future.',
            'capacity.min' => 'The event capacity must be at least 1.',
            'image.max' => 'The image must not be larger than 2MB.',
        ];
    }
}
