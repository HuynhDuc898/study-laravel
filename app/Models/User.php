<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use App\Casts\Json;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'first_name',
        'last_name',
        'options'

    ];
    protected $primaryKey = 'id';
    protected $table = 'users';
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime:d-M-Y',
        'updated_at' => 'datetime:d-M-Y',
        // 'updated_at' => 'datetime:d-M-Y H:i:m',
        // 'options' => 'json'
        // 'options' => 'array'
        // 'options' => AsArrayObject::class
        'options' => Json::class
    ];
    //------ép kiểu khi gọi eloquent-----------
    // protected $casts = [
    //     'options' => 'array'
    //     // 'created_at' => 'datetime:Y-m-d',
    // ];

    protected static function boot()
    {
        parent::boot();
        // static::updating(function($model){
        //     $model->first_name = strtoupper($model->first_name);
        //     $model->last_name = strtoupper($model->last_name);
        // });

        static::saving(function ($model) {
            $model->name = $model->first_name.' '.$model->last_name;
        });

        
    }

    //--------------------------
    public function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = strtoupper($value);
    }
    //----------------------------
    public function setLastNameAttribute($value)
    {
        $this->attributes['last_name'] = strtoupper($value);
    }

    

    
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
    

     /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }
}
