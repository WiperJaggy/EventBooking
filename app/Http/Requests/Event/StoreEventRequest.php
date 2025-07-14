<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
class StoreEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'=>['required','string','max:255'],
             'description' => ['required', 'string'],
            // Date must be a valid date format and in the future
            'date' => ['required', 'date', 'after_or_equal:today'],
            'capacity' => ['required', 'integer', 'min:1'], // Capacity must be a positive integer
            // Image is optional but if provided, must be an image file
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ];
    }

        protected function prepareForValidation(): void
    {
        // Add the authenticated user's ID to the request for the 'created_by' field
        // This ensures the event is always associated with the user creating it.
        $this->merge([
            'created_by' => Auth::id(),
        ]);
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
