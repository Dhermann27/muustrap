<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Thisyear_Camper extends Model
{
    protected $table = "thisyear_campers";
//    protected $dates = ['birthdate'];

    public function church()
    {
        return $this->hasOne(Church::class, 'id', 'churchid');
    }

    public function family()
    {
        return $this->hasOne(Thisyear_Family::class, 'id', 'familyid');
    }

    public function foodoption()
    {
        return $this->hasOne(Foodoption::class, 'id', 'foodoptionid');
    }

    public function medicalresponse()
    {
        return $this->hasOne(Medicalresponse::class, 'yearattendingid', 'yearattendingid');
    }

    public function program()
    {
        return $this->hasOne(Program::class, 'id', 'programid');
    }

    public function pronoun()
    {
        return $this->hasOne(Pronoun::class, 'id', 'pronounid');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function yearattending()
    {
        return $this->hasOne(Yearattending::class, 'id', 'yearattendingid');
    }

    public function yearsattending()
    {
        return $this->hasMany(Yearattending::class, 'camperid', 'id')
            ->orderBy('year', 'desc');
    }

    public function getFormattedPhoneAttribute()
    {
        if (preg_match('/^(\d{3})(\d{3})(\d{4})$/', $this->phonenbr, $matches)) {
            $result = $matches[1] . '-' . $matches[2] . '-' . $matches[3];
            return $result;
        }
        return "";
    }

    public function parents()
    {
        return $this->hasMany(Thisyear_Camper::class, 'familyid', 'familyid')
            ->where('age', '>', '17')->orderBy('birthdate');
    }

    public function getEachCalendarAttribute()
    {
        $cal = explode(';', $this->program->calendar);
        switch (count($cal)) {
            case 3:
                if ($this->age < 8) {
                    return $cal[0];
                } elseif ($this->age > 9) {
                    return $cal[2];
                } else {
                    return $cal[1];
                }
                break;

            case 2:
                return $this->age > 3 ? $cal[1] : $cal[0];
                break;

            default:
                return $cal[0];
        }
    }

    public function getNametagBackAttribute()
    {
        switch ($this->programid) {
            case 1001:
                return "Leader: ________________________________<br /><br />________________________________<br />________________________________<br />________________________________<br />________________________________<br />________________________________<br />________________________________";
                break;
            case 1002:
            case 1007:
                $parents = "";
                foreach ($this->family->campers()->where('age', '>', '17')->orderBy('birthdate')->get() as $parent) {
                    $parents .= "<u>" . $parent->firstname . " " . $parent->lastname . "</u><br />";
                    $parents .= "Room: " . $parent->buildingname . " " . $parent->room_number . "<br />";
                    if (count($parent->yearattending->workshops()->where('is_enrolled', '1')->get()) > 0) {
                        foreach ($parent->yearattending->workshops()->where('is_enrolled', '1')->get() as $workshop) {
                            if ($workshop->workshop->timeslotid == 1001 || $workshop->workshop->timeslotid == 1002) {
                                $parents .= $workshop->workshop->timeslot->name . " (" . $workshop->workshop->display_days . ") " . $workshop->workshop->room->room_number . "<br />";
                            }
                        }
                    }
                }
                return $parents;
                break;
            default:
                $workshops = "";
                if (count($this->yearattending->workshops()->where('is_enrolled', '1')->get()) > 0) {
                    foreach ($this->yearattending->workshops()->where('is_enrolled', '1')->get() as $workshop) {
                        $workshops .= $workshop->workshop->timeslot->name . " (" . $workshop->workshop->display_days . "): " . $workshop->workshop->name . " in " . $workshop->workshop->room->room_number . "<br />";
                    }
                }
                return $workshops;
                break;
        }
    }
}
