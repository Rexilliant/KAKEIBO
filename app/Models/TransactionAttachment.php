<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionAttachment extends Model
{
    protected $fillable = [
        'transaction_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}