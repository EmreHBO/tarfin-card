<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceivedRepayment extends Model
{
    use HasFactory;

    protected $table = 'received_repayments';

    protected $guarded =[
        'id',
    ];

    public $timestamps = false;

    public function scheduledRepayment(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }
}
