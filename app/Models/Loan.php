<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    use HasFactory;

    protected $table = 'loans';

    protected $guarded =[
        'id',
    ];

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scheduledRepayments(): HasMany
    {
        return $this->hasMany(ScheduledRepayment::class);
    }

    public function receivedRepayments(): HasMany
    {
        return $this->hasMany(ReceivedRepayment::class);
    }
}
