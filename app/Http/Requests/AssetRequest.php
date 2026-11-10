<?php

namespace App\Http\Requests;

use App\Enums\AssetCondition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isStaff();
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $assetId = $this->route('asset')?->getKey();

        return [
            'code' => ['nullable', 'string', 'max:48', Rule::unique('assets', 'code')->ignore($assetId)],
            'name' => ['required', 'string', 'max:120'],
            'branch' => ['required', 'string', Rule::in(['Semarang 1', 'Semarang 2', 'Bandung'])],
            'serial_number' => ['nullable', 'string', 'max:120'],
            'brand' => ['nullable', 'string', 'max:80'],
            'category_id' => ['required', 'exists:categories,id'],
            'location_id' => ['nullable', 'exists:locations,id'],
            'room' => ['nullable', 'string', 'max:80'],
            'owner_unit' => ['nullable', 'string', 'max:64'],
            'quantity' => ['required', 'integer', 'min:0', 'max:100000'],
            'minimum_stock' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'condition' => ['required', Rule::enum(AssetCondition::class)],
            'purchase_date' => ['nullable', 'date', 'before_or_equal:today'],
            'purchase_price' => ['nullable', 'numeric', 'min:0', 'max:999999999999'],
            'description' => ['nullable', 'string', 'max:2000'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_photo' => ['nullable', 'boolean'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'name' => 'nama barang',
            'branch' => 'cabang',
            'serial_number' => 'serial number',
            'category_id' => 'kategori',
            'location_id' => 'lokasi',
            'minimum_stock' => 'stok minimal',
            'purchase_date' => 'tanggal beli',
            'purchase_price' => 'harga beli',
            'photo' => 'foto barang',
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'quantity.min' => 'Stok tidak boleh negatif.',
            'purchase_date.before_or_equal' => 'Tanggal beli tidak boleh melebihi hari ini.',
        ];
    }
}