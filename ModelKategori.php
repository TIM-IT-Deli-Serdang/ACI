<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'master_kategori';

    protected $fillable = [
        'id',
        'nm_kategori',
        'created_at',
        'updated_at'
    ];
}
