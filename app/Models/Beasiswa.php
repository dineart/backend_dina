<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Beasiswa extends Model
{
    protected $table = 'BEASISWA';
    protected $primaryKey = 'ID_BEASISWA';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'ID_BEASISWA',
        'NAMA_BEASISWA',
        'PERSEN_POTONGAN',
    ];
}