<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {

        $this->merge([
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $bookId = $this->route('book')->id;
        return [
            'stars'   => 'required|integer|min:1|max:5',
            'body' => 'required|string|max:255',
            'user_id' => Rule::unique('reviews')->where(function ($query) use ($bookId) {
                return $query->where('book_id', $bookId);
            }),
        ];
    }


    public function messages(): array
    {
        return [
            'user_id.unique' => 'You have already reviewed this book.',
        ];
    }

}
