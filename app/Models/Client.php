<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'document',
        'full_name',
        'email',
        'phone',
        'balance',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        // 'id' => 'string',
    ];
}
