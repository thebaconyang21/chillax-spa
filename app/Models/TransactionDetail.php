<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TransactionDetail extends Model
{
    protected $fillable = [
        'transaction_id',
        'service_id',
        'therapist_id',
        'service_price',
        'quantity',
        'subtotal',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function therapist(): BelongsTo
    {
        return $this->belongsTo(Therapist::class);
    }

    public function detailAddons(): HasMany
    {
        return $this->hasMany(DetailAddon::class);
    }

    public function addons(): HasMany
    {
        return $this->hasMany(DetailAddon::class, 'transaction_detail_id');
    }

    public function commission(): HasOne
    {
        return $this->hasOne(Commission::class);
    }
}
