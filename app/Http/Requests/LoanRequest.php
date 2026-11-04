<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isStaff();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'asset_id' => ['required', 'exists:assets,id'],
            'borrower_id' => ['required', 'exists:users,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:1000'],
            'purpose' => ['required', 'string', 'max:160'],
            'room' => ['nullable', 'string', 'max:80'],
            'borrowed_at' => ['nullable', 'date', 'before_or_equal:now'],
            'due_at' => ['nullable', 'date', 'after_or_equal:today'],
            'initial_photo' => ['nullable', 'image', 'max:5120'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'asset_id' => 'barang',
            'borrower_id' => 'peminjam',
            'due_at' => 'tenggat pengembalian',
            'borrowed_at' => 'tanggal pinjam',
        ];
    }
}