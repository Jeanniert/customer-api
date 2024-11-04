<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class regions extends Model
{
    /** @use HasFactory<\Database\Factories\RegionsFactory> */
    use HasFactory;
    
    protected $fillable = [
        'description',
        'status',   
    ];
}
