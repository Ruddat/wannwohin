<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ModCurrencyExchangeRate extends Model
{
    use HasFactory;

    protected $fillable = ['base_currency', 'target_currency', 'exchange_rate', 'last_updated'];

    public $timestamps = false;

}
