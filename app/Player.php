<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $fillable = [
        'id',
        'balance',
        'active',
    ];
    protected $attributes = [
        'balance' => 1000,
        'active' => 0,
    ];
    protected $table = 'player';
    public $timestamps = false;
    public $incrementing = false;
}
