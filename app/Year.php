<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Year extends Model
{
    public $timestamps = false;
    protected $fillable = ['start_date', 'start_open', 'is_live', 'is_crunch', 'is_accept_paypal', 'is_workshop_proposal', 'is_artfair'];


    public function hasBrochure()
    {
        return Storage::disk('local')->exists('public/MUUSA_' . $this->year . '_Brochure.pdf');
    }

    public function getFirstDayAttribute()
    {
        $date = Carbon::createFromFormat('Y-m-d', $this->start_date, 'America/Chicago');
        $date->year = $this->year;
        return $date->format('l F jS');
    }

    public function getLastDayAttribute()
    {
        $date = Carbon::createFromFormat('Y-m-d', $this->start_date, 'America/Chicago');
        $date->year = $this->year;
        return $date->addDays(6)->format('l F jS');
    }

    public function getNextDayAttribute()
    {
        $lastfirst = Carbon::createFromFormat('Y-m-d', \App\Year::where('year', $this->year - 1)->first()->start_date, 'America/Chicago');
        $now = Carbon::now('America/Chicago');
        if ($now->between($lastfirst, $lastfirst->addDays(7))) {
            return $now;
        }
        return $now->max(Carbon::createFromFormat('Y-m-d', $this->start_date, 'America/Chicago'));
    }

    public function getNextWeekdayAttribute()
    {
        return Carbon::now('America/Chicago')->max(Carbon::createFromFormat('Y-m-d', $this->start_date, 'America/Chicago')->addDay());
    }

    public function getNextMuseAttribute()
    {
        $now = Carbon::now('America/Chicago');
        if (Storage::disk('local')->exists('public/muses/' . $now->format('Ymd') . '.pdf')) {
            return "Today's Muse";
        } elseif (Storage::disk('local')->exists('public/muses/' . $now->subDay()->format('Ymd') . '.pdf')) {
            return "Yesterday's Muse";
        } elseif (Storage::disk('local')->exists('public/muses/' . $this->year . '0601.pdf')) {
            return "Pre-Camp Muse";
        } else {
            return false;
        }
    }
}
