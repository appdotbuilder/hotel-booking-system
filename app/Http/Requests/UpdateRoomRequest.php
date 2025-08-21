<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $roomId = $this->route('room')?->id;
        
        return [
            'number' => "required|string|max:20|unique:rooms,number,{$roomId}",
            'type' => 'required|string|max:50',
            'description' => 'nullable|string|max:1000',
            'capacity' => 'required|integer|min:1|max:10',
            'price_per_night' => 'required|numeric|min:0|max:9999.99',
            'status' => 'required|in:available,maintenance,out_of_order',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string|max:100',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'number.required' => 'Room number is required.',
            'number.unique' => 'This room number already exists.',
            'type.required' => 'Room type is required.',
            'capacity.required' => 'Room capacity is required.',
            'capacity.min' => 'Room must accommodate at least 1 guest.',
            'capacity.max' => 'Room capacity cannot exceed 10 guests.',
            'price_per_night.required' => 'Price per night is required.',
            'price_per_night.min' => 'Price must be greater than or equal to 0.',
            'status.required' => 'Room status is required.',
            'status.in' => 'Invalid room status selected.',
        ];
    }
}