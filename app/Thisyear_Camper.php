<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Thisyear_Camper extends Model
{
    protected $table = "thisyear_campers";

    public function church()
    {
        return $this->hasOne(Church::class, 'id', 'churchid');
    }

    public function foodoption()
    {
        return $this->hasOne(Foodoption::class, 'id', 'foodoptionid');
    }

    public function pronoun()
    {
        return $this->hasOne(Pronoun::class, 'id', 'pronounid');
    }

    public function yearattending() {
        return $this->hasOne(Yearattending::class, 'id', 'yearattendingid');
    }

    public function history() {
        return \App\Byyear_Camper::where('id', $this->id)->where('year', '>', DB::raw('getcurrentyear()-5'))->orderBy('year')->get();
    }

    public function getFormattedPhoneAttribute()
    {
        if (preg_match('/^(\d{3})(\d{3})(\d{4})$/', $this->phonenbr, $matches)) {
            $result = $matches[1] . '-' . $matches[2] . '-' . $matches[3];
            return $result;
        }
        return "";
    }
}
