<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\regions;

class Commune extends Model
{
    protected $fillable = ['id_reg', 'description', 'status'];
    protected $with = ['region'];

    public function region()
    {
        return $this->belongsTo(regions::class, 'id_reg' );
    }
}
