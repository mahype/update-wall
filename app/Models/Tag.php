<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = ['name', 'color'];

    public function machines(): BelongsToMany
    {
        return $this->belongsToMany(Machine::class);
    }

    public function badgeClasses(): string
    {
        return match ($this->color) {
            'red'    => 'bg-red-100 text-red-700',
            'yellow' => 'bg-yellow-100 text-yellow-700',
            'green'  => 'bg-green-100 text-green-700',
            'blue'   => 'bg-blue-100 text-blue-700',
            'indigo' => 'bg-indigo-100 text-indigo-700',
            'purple' => 'bg-purple-100 text-purple-700',
            'pink'   => 'bg-pink-100 text-pink-700',
            default  => 'bg-gray-100 text-gray-700',
        };
    }

    public function ringClasses(): string
    {
        return match ($this->color) {
            'red'    => 'ring-red-400',
            'yellow' => 'ring-yellow-400',
            'green'  => 'ring-green-400',
            'blue'   => 'ring-blue-400',
            'indigo' => 'ring-indigo-400',
            'purple' => 'ring-purple-400',
            'pink'   => 'ring-pink-400',
            default  => 'ring-gray-400',
        };
    }
}
