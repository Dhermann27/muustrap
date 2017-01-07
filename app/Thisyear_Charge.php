<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Thisyear_Charge extends Model
{
    protected $table = "thisyear_charges";

    public function family()
    {
        return $this->hasOne(Family::class);
    }

    public function camper()
    {
        return $this->hasOne(Camper::class);
    }

    public function chargetype()
    {
        return $this->hasOne(Chargetype::class);
    }
}