<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoffeeController extends Controller
{
    public function store(Request $request)
    {
        $camper = null;
        $readonly = \Entrust::can('read') && !\Entrust::can('write');
        $camper = \App\Thisyear_Camper::where('email', Auth::user()->email)->first();
        if (isset($camper)) {
            foreach ($camper->yearattending->positions as $position) {
                if ($position->staffpositionid == '1117' || $position->staffpositionid == '1103') {
                    $readonly = false;
                }
            }
        }

        if ($readonly === false) {
            $year = \App\Year::where('is_current', '1')->first();
            foreach ($request->all() as $key => $value) {
                $matches = array();
                if (preg_match('/(\d+)-(delete|order|is_onstage)/', $key, $matches)) {
                    $workshop = \App\Coffeehouseact::find($matches[1]);
                    if ($matches[2] == "delete") {
                        $workshop->delete();
                    } elseif ($workshop) {
                        if ($workshop->{$matches[2]} != $value) {
                            $workshop->{$matches[2]} = $value;
                            $workshop->updated_at = Carbon::now('America/Chicago')->toDateTimeString();
                            $workshop->save();
                        }
                    }
                }
            }

            if ($request->input('name') != '') {
                $workshop = new \App\Coffeehouseact();
                $workshop->year = $year->year;
                $workshop->name = $request->input('name');
                $workshop->equipment = $request->input('equipment');
                $workshop->date = Carbon::createFromFormat('Y-m-d', $year->start_date, 'America/Chicago')
                    ->addDays((int)$request->input('day'))->toDateString();
                $workshop->order = $request->input('order');
                $workshop->save();
            }

            $request->session()->flash('success', 'Laurel. It\'s your conscience. You need to be more truthful in your Tindr profile.');
        } else {
            $request->session()->flash('error', 'No access to this function. How did you get here?');
        }

        return redirect()->action('CoffeeController@index', ['day' => $request->input('day')]);
    }

    public function index($day = null)
    {
        $year = \App\Year::where('is_current', '1')->first();
        $firstday = Carbon::createFromFormat('Y-m-d', $year->start_date, 'America/Chicago');
        $acts = \App\Coffeehouseact::where('year', $year->year)->orderBy('order')->get();
        $starttime = Carbon::now('America/Chicago')->hour(21)->minute(50);

        $camper = null;
        $readonly = \Entrust::can('read') && !\Entrust::can('write');
        $camper = \App\Thisyear_Camper::where('email', Auth::user()->email)->first();
        if (isset($camper)) {
            foreach ($camper->yearattending->positions as $position) {
                if ($position->staffpositionid == '1117' || $position->staffpositionid == '1103') {
                    $readonly = false;
                }
            }
        }

        return view('coffeehouse', ['firstday' => $firstday, 'day' => $day, 'acts' => $acts,
            'starttime' => $starttime, 'readonly' => $readonly]);
    }
}
