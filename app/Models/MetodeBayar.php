<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MetodeBayar extends Model
{
    protected $table = 'METODE_BAYAR';
    protected $primaryKey = 'ID_METODE';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'ID_METODE',
        'NAMA_METODE',
        'NO_REKENING',
    ];
}