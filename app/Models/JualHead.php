<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JualHead extends Model
{
    use HasFactory;
    protected $connection = 'mysql_second'; // kone ke db 2 seken
    protected $table = 'jual_head';
    protected $fillable = ['TGL','NO_EJ', 'NETTO'];
}