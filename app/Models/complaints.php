<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class complaints extends Model
{
    use HasFactory;

    protected $fillable = [
        'manual_id',
        'user_id',
        'complaint',
    ];

    public function manuals(){
        return $this->belongsTo(manuals::class, 'manual_id', 'id');
    }
}
