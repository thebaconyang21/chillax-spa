<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Addon extends Model
{
    protected $fillable = [
        'addon_name',
        'price',
    ];

    public function detailAddons(): HasMany
    {
        return $this->hasMany(DetailAddon::class);
    }
}
