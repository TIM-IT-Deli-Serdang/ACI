<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Laporan extends Model
{
    protected $table = 'laporan';

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'file_masyarakat',
        'kategori_laporan_id',
        'deskripsi',
        'kecamatan_id',
        'kelurahan_id',
        'alamat',
        'latitude',
        'longitude',
        'status_laporan',
        'user_id',
        'verif_id',
        'verif_keterangan',
        'verif_file',
        'verif_tgl',
        'verif_keterangan_tolak',
        'verif_tgl_tolak',
        'penanganan_id',
        'penanganan_keterangan',
        'penanganan_tgl',
        'penanganan_keterangan_tolak',
        'penanganan_tgl_tolak',
        'penerima_id',
        'penerima_keterangan',
        'penerima_keterangan_tolak',
        'penerima_tgl',
        'penerima_tgl_tolak',
        'upt_id',
        'created_at',
        'updated_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->getKey()) {
                $model->id = (string) Str::uuid();
            }
        });
    }
}
