<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    public function building()
    {
        return $this->hasOne(Building::class);
    }

    public function program()
    {
        return $this->hasOne(Program::class);
    }
}
