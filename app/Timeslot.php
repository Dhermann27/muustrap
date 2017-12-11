<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Timeslot extends Model
{
    public $timestamps = false;

    protected $dates = ['start_time', 'end_time'];

    public function workshops()
    {
        return $this->hasMany(Workshop::class, 'timeslotid', 'id')->where('year',
            $year = \App\Year::where('is_current', '1')->first()->year);
    }
}
