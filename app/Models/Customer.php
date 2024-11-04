<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['dni', 'id_reg', 'id_com', 'email', 'name', 'last_name', 'address', 'date_reg', 'status'];
    protected $with = ['region', 'commune'];

    
    public function region()
    {
        return $this->belongsTo(regions::class, 'id_reg');
    }

    public function commune()
    {
        return $this->belongsTo(Commune::class, 'id_com');
    }
}
