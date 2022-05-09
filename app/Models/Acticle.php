<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','content','writter_id'
    ];
    protected $primaryKey = 'id';
    protected $table = 'acticles';

    protected $casts = [
        'created_at' => 'datetime:d-M-Y',
        'updated_at' => 'datetime:d-M-Y',
    ];
}
