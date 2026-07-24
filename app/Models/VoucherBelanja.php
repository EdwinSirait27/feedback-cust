<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherBelanja extends Model
{
    use HasFactory;
    protected $connection = 'mysql_second'; // kone ke db 2 seken
    protected $table = 'voucher_belanja';
    protected $fillable = ['NO_EJ', 'NO_VCHR','ID','TGL','EXPIRED','MEMBER','KET','NILAI_VOUCHER','TARIK','NO_EJB','TGLTRX','BO'];
}
