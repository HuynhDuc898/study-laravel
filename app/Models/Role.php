<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $connection = 'mysql2';
    protected $fillable = [
        'name',
    ];
    protected $primaryKey = 'id';
    protected $table = 'roles';

    protected $casts = [
        'created_at' => 'datetime:d-M-Y',
        'updated_at' => 'datetime:d-M-Y',
    ];
    

    public function user()
    {
        return $this->hasMany(User::class, 'role_id');
    }
}
