<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class manuals extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'img_path',
    ];

    public function complaints()
    {
        return $this->hasMany(Complaints::class, 'manual_id', 'id');
    }

    public function userr()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // protected $hidden = ['status'];
}
