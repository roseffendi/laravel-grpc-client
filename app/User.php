<?php

namespace App;

use App\Services\Grpc\GrpcUserProvider;
use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function validateForPassportPasswordGrant($password)
    {
        $grpcUserProvider = app()->auth->createUserProvider('grpc');

        return $grpcUserProvider->retrieveByCredentials([
            'email' => $this->email,
            'password' => $password,
        ]);
    }

    public function findForPassport($email)
    {
        $grpcUserProvider = app()->auth->createUserProvider('grpc');

        return $grpcUserProvider->retrieveByEmail($email);
    }
}
