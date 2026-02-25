<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hostname' => 'required|string|max:255',
            'timestamp' => 'required|date',
            'total_updates' => 'required|integer|min:0',
            'has_security' => 'required|boolean',
            'checkers' => 'required|array|min:1',
            'checkers.*.name' => 'required|string|max:100',
            'checkers.*.summary' => 'required|string',
            'checkers.*.error' => 'nullable|string',
            'checkers.*.updates' => 'nullable|array',
            'checkers.*.updates.*.name' => 'required|string|max:255',
            'checkers.*.updates.*.current_version' => 'required|string|max:100',
            'checkers.*.updates.*.new_version' => 'required|string|max:100',
            'checkers.*.updates.*.type' => 'required|string|in:security,regular,plugin,theme,core,image,distro',
            'checkers.*.updates.*.priority' => 'required|string|in:critical,high,normal,low',
            'checkers.*.updates.*.source' => 'nullable|string|max:255',
            'checkers.*.updates.*.phasing' => 'nullable|string|max:100',
        ];
    }
}
