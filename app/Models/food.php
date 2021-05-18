<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class food extends Model
{
    protected $connection = 'resConn';
    public $timestamps = false;
    use HasFactory;
}
