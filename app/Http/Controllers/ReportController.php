<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function campers($year = 0, $order = 'name')
    {
        $year = $year == 0 ? $this->year->year : (int)$year;
        $years = \App\Year::where('year', '>', '2008')->orderBy('year', 'DESC')->get();
        $families = \App\Byyear_Family::where('year', $year);
        if ($order == 'name') {
            $families->orderBy('name');
        } else {
            $families->orderBy('created_at', 'DESC');
        }
        $campers = \App\Byyear_Camper::where('year', $year)->orderBy('birthdate')->get()->groupBy('familyid');

        return view('reports.campers', ['title' => 'Registered Campers', 'years' => $years,
            'families' => $families->get(), 'campers' => $campers, 'thisyear' => $year, 'order' => $order]);
    }

    public function campersExport($year = 0)
    {
        $year = $year == 0 ? $this->year->year : (int)$year;
        Excel::create('MUUSA_' . $year . '_Campers_' . Carbon::now()->toDateString(), function ($excel) use ($year) {
            $excel->sheet('campers', function ($sheet) use ($year) {
                $sheet->setOrientation('landscape');
                $sheet->with(\App\Byyear_Camper::select('familyname', 'address1', 'address2', 'city', 'statecd',
                    'zipcd', 'country', 'pronounname', 'firstname', 'lastname', 'email', 'phonenbr', 'birthday', 'age',
                    'programname', 'roommate', 'sponsor', 'churchname', 'churchcity', 'churchstatecd', 'days',
                    'room_number', 'buildingname')->where('year', $year)
                    ->orderBy('familyname')->orderBy('familyid')->orderBy('birthdate')->get());
            });
        })->export('xls');
    }

    public function chart()
    {
        $mergeddates = array();
        $dates = DB::select(DB::raw('SELECT ya.year AS theleft, RIGHT(DATE(ya.created_at),5) AS theright, DATE(ya.created_at) AS thedate, 
            (SELECT COUNT(*) FROM yearsattending yap WHERE yap.year=MAX(ya.year) AND DATE(yap.created_at) <= thedate) AS total
            FROM yearsattending ya, staticdates sd WHERE RIGHT(DATE(ya.created_at), 5)=RIGHT(DATE(sd.date), 5) 
                                                  AND ya.year>getcurrentyear()-7 AND ya.year<=getcurrentyear() 
            GROUP BY DATE(ya.created_at) ORDER BY sd.day'));
        $summaries = DB::select(DB::raw('SELECT ya.year, COUNT(*) total, SUM(IF((SELECT COUNT(*) FROM yearsattending yap WHERE ya.year>yap.year AND c.id=yap.camperid)=0, 1, 0)) newcampers, 
            SUM(IF((SELECT COUNT(*) FROM yearsattending yap WHERE ya.year-1=yap.year AND c.id=yap.camperid)=0 AND (SELECT COUNT(*) FROM yearsattending yap WHERE ya.year-2=yap.year AND c.id=yap.camperid)=1,1,0)) oldcampers, 
            SUM(IF((SELECT COUNT(*) FROM yearsattending yap WHERE ya.year-3<yap.year AND yap.year<ya.year AND c.id=yap.camperid)=0 AND (SELECT COUNT(*) FROM yearsattending yap WHERE ya.year-3>=yap.year AND c.id=yap.camperid)>0,1,0)) voldcampers, 
            (SELECT COUNT(*) FROM yearsattending yap WHERE ya.year-1=yap.year AND (SELECT COUNT(*) FROM yearsattending yaq WHERE ya.year=yaq.year AND yap.camperid=yaq.camperid)=0) lostcampers 
            FROM campers c, yearsattending ya WHERE c.id=ya.camperid AND ya.year>getcurrentyear()-7 AND ya.year<=getcurrentyear()
            GROUP BY ya.year ORDER BY ya.year'));
        foreach ($dates as $date) {
            if (!array_has($mergeddates, $date->theright)) {
                $mergeddates[$date->theright] = array();
            }
            $mergeddates[$date->theright][$date->theleft] = $date->total;
        }
        return view('reports.chart', ['years' => \App\Year::where('year', '>', DB::raw($this->year->year-7))
            ->where('year', '<=', $this->year->year)->get(), 'summaries' => $summaries, 'dates' => $mergeddates]);
    }

    public function conflicts()
    {
        $conflicts = DB::select(DB::raw('SELECT tc.id, tc.firstname, tc.lastname, w.name AS nameone, wp.name AS nametwo
            FROM thisyear_campers tc, yearattending__workshop yw, workshops w, yearattending__workshop ywp, workshops wp 
            WHERE tc.yearattendingid=yw.yearattendingid AND tc.yearattendingid=ywp.yearattendingid
              AND yw.workshopid=w.id AND ywp.workshopid=wp.id AND yw.yearattendingid=ywp.yearattendingid 
              AND yw.workshopid!=ywp.workshopid AND w.timeslotid=wp.timeslotid 
              AND ((w.m=1 AND wp.m=1) OR (w.t=1 AND wp.t=1) OR (w.w=1 AND wp.w=1) OR (w.th=1 AND wp.th=1) OR (w.f=1 AND wp.f=1))
              GROUP BY yw.yearattendingid, w.timeslotid ORDER BY tc.lastname, tc.firstname'));
        return view('reports.conflicts', ['campers' => $conflicts]);
    }

    public function depositsMark($id)
    {
        \App\Charge::where('chargetypeid', $id)->where('deposited_date', null)
            ->update(['deposited_date' => Carbon::now()->toDateString()]);
        return redirect()->action('ReportController@deposits');
    }

    public function deposits()
    {
        $chargetypes = \App\Chargetype::where('is_deposited', '1')->get();
        return view('reports.deposits', ['chargetypes' => $chargetypes,
            'charges' => \App\Thisyear_Charge::whereIn('chargetypeid', $chargetypes->pluck('id')->toArray())
                ->orderBy('deposited_date')->orderBy('timestamp', 'desc')->get()->groupBy('deposited_date')
        ]);
    }

    public function firsttime()
    {
        $families = \App\Thisyear_Family::whereRaw('id IN (SELECT c.familyid FROM campers c, yearsattending ya WHERE c.id=ya.camperid GROUP BY c.familyid HAVING COUNT(ya.id)=1)')->orderBy('name')->get();
        $campers = \App\Thisyear_Camper::whereIn('familyid', $families->pluck('id'))->get()->groupBy('familyid');
        return view('reports.campers', ['title' => 'First-time Campers', 'families' => $families, 'campers' => $campers]);
    }

    public function guarantee()
    {
        $groups = DB::table('thisyear_campers')->selectRaw('side, age, IF(age>12,0,IF(age>5,1,2)) AS agegroup, COUNT(*) AS count')
            ->where('days', '>', 5)->leftJoin('buildings', 'thisyear_campers.buildingid', 'buildings.id')
            ->groupBy('side')->groupBy('agegroup')->orderBy('side')->orderBy('agegroup')->get();
        return view('reports.guarantee', ['groups' => $groups]);
    }

    public function outstandingMark(Request $request, $id)
    {
        $charge = new \App\Charge();
        $charge->camperid = $id;
        $charge->amount = $request->input('amount');
        $charge->memo = $request->input('memo');
        $charge->chargetypeid = $request->input('chargetypeid');
        $charge->timestamp = Carbon::now()->toDateString();
        $charge->year = DB::raw('getcurrentyear()');
        $charge->save();

        $request->session()->flash('success', 'This payment was totally ignored, but the green message still seems congratulatory.');
        return redirect()->action('ReportController@outstanding');
    }

    public function outstanding($filter = 'all')
    {
        $chargetypes = \App\Chargetype::where('is_shown', '1')->orderBy('name')->get();
        $charges = \App\Thisyear_Charge::select(DB::raw('MAX(`familyid`) AS familyid'),
            DB::raw('MAX(`camperid`) AS camperid'), DB::raw('SUM(`amount`) as amount'));
        if ($filter == 'unpaid') {
            $charges->where('chargetypeid', '1003')->whereOr('amount', '<', 0);
        }
        $charges->groupBy('familyid')->having(DB::raw('SUM(`amount`)'), '!=', '0.0')
            ->join('families', 'families.id', 'thisyear_charges.familyid');
        return view('reports.outstanding', ['chargetypes' => $chargetypes,
            'readonly' => \Entrust::can('read') && !\Entrust::can('write'),
            'charges' => $charges->orderBy('families.name')->get()
        ]);
    }

    public function payments($year = 0)
    {
        $year = $year == 0 ? $this->year->year : (int)$year;
        $years = \App\Byyear_Charge::where('year', '>', '2008')->groupBy('year')->distinct()
            ->orderBy('year', 'DESC')->get();
        return view('reports.payments', ['charges' => \App\Byyear_Charge::where('amount', '!=', '0.0')
            ->where('year', $year)->with('camper')->with('family')->get(), 'thisyear' => $year, 'years' => $years]);
    }

    public function paymentsExport($year = 0)
    {
        $year = $year == 0 ? $this->year->year : (int)$year;
        Excel::create('MUUSA_' . $year . '_Ledger_' . Carbon::now()->toDateString(), function ($excel) use ($year) {
            $excel->sheet('payments', function ($sheet) use ($year) {
                $sheet->setOrientation('landscape');
                $sheet->with(\App\Byyear_Charge::select('families.name', 'campers.firstname', 'campers.lastname',
                    'byyear_charges.amount', 'byyear_charges.chargetypename', 'byyear_charges.timestamp',
                    'byyear_charges.memo')
                    ->join('campers', 'byyear_charges.camperid', 'campers.id')
                    ->join('families', 'campers.familyid', 'families.id')->orderBy('families.name')
                    ->where('year', $year)
                    ->orderBy('byyear_charges.familyid')->orderBy('byyear_charges.timestamp')->get());
            });
        })->export('xls');
    }

    public function programs()
    {
        return view('reports.programs', ['programs' => \App\Program::where('name', '!=', 'Adult')
            ->with('participants.parents')->with('participants.medicalresponse')->orderBy('order')->get()]);
    }

    public function ratesMark(Request $request)
    {
        foreach ($request->all() as $key => $value) {
            $matches = array();
            if (preg_match('/(\d+)-rate/', $key, $matches)) {
                $rate = \App\Rate::findOrFail($matches[1]);
                $rate->rate = $value;
                $rate->save();
            }
        }

        if ($request->input('buildingid') != '0') {
            $rate = \App\Rate::where('buildingid', $request->input('buildingid'))
                ->where('programid', $request->input('programid'))
                ->where('min_occupancy', $request->input('min_occupancy'))
                ->where('max_occupancy', $request->input('max_occupancy'))
                ->where('start_year', '<', DB::raw('getcurrentyear()'))->where('end_year', '2100')->first();
            if ($rate) {
                $rate->end_year = $year - 1;
                $rate->save();
            }

            $rate = new \App\Rate;
            $rate->buildingid = $request->input('buildingid');
            $rate->programid = $request->input('programid');
            $rate->min_occupancy = $request->input('min_occupancy');
            $rate->max_occupancy = $request->input('max_occupancy');
            $rate->rate = floatval($request->input('rate'));
            $rate->start_year = DB::raw('getcurrentyear()');
            $rate->end_year = '2100';
            $rate->save();
        }

        DB::statement('CALL generate_charges(getcurrentyear());');

        $request->session()->flash('success', 'I would say that your attempt has a good success... rate YYYYEEEEAAAAHHHHHH');

        return redirect()->action('ReportController@rates');
    }

    public function rates()
    {
        return view('reports.rates', ['years' => \App\Year::where('year', '>', 2014)->orderBy('year', 'DESC')->get(),
            'buildings' => \App\Building::all(), 'rates' => \App\Rate::all(),
            'programs' => \App\Program::orderBy('order')->get()]);
    }

    public function rooms($year = 0)
    {
        $year = $year == 0 ? $this->year->year : (int)$year;
        $years = \App\Byyear_Camper::where('year', '>', '2008')->groupBy('year')->distinct()
            ->orderBy('year', 'DESC')->get();
        $campers = \App\Byyear_Camper::where('year', $year)->whereNotNull('roomid')
            ->orderBy('room_number')->orderBy('familyid')->orderBy('birthdate')->get();
        return view('reports.rooms', ['campers' => $campers, 'buildings' => \App\Building::all(),
            'thisyear' => $year, 'years' => $years]);
    }

    public function roomsExport($year = 0)
    {
        $year = $year == 0 ? $this->year->year : (int)$year;
        Excel::create('MUUSA_' . $year . '_Rooms_' . Carbon::now()->toDateString(), function ($excel) use ($year) {
            $buildings = \App\Byyear_Camper::where('year', $year)->whereNotNull('roomid')->groupBy('buildingid')
                ->distinct()->get();
            foreach ($buildings as $building) {
                $excel->sheet($building->buildingname, function ($sheet) use ($building, $year) {
                    $sheet->setOrientation('landscape');
                    $sheet->with(\App\Byyear_Camper::select('room_number', 'firstname', 'lastname', 'address1',
                        'address2', 'city', 'statecd', 'zipcd', 'age')
                        ->where('year', $year)->where('buildingid', $building->buildingid)
                        ->orderBy('room_number')->orderBy('familyid')->orderBy('birthdate')->get());
                });
            }
        })->export('xls');
    }

    public function roommates()
    {
        return view('reports.roommates', ['campers' => \App\Thisyear_Camper::where('roommate', '!=', '')
            ->orderBy('lastname')->orderBy('firstname')->get()]);
    }

    public function states()
    {
        $years = \App\Byyear_Camper::where('year', '>', DB::raw('getcurrentyear()-8'))
            ->select('year', 'churchstatecd AS code', DB::raw('COUNT(*) AS total'))
            ->where('churchid', '!=', '2084')->groupBy('year', 'churchstatecd')
            ->orderBy('year', 'desc')->orderBy('total', 'desc')->get()->groupBy('year');
        $churches = \App\Byyear_Camper::where('year', '>', DB::raw('getcurrentyear()-8'))
            ->select('year', 'churchstatecd', 'churchname', 'churchcity', DB::raw('COUNT(*) AS total'))
            ->where('churchid', '!=', '2084')->groupBy('year', 'churchid')
            ->orderBy('total', 'desc')->get();
        return view('reports.states', ['years' => $years, 'churches' => $churches]);
    }

    public function unassigned()
    {
        $families = \App\Thisyear_Family::whereRaw('assigned != count')->orderBy('name')->get();
        $campers = \App\Thisyear_Camper::whereIn('familyid', $families->pluck('id'))->get()->groupBy('familyid');
        return view('reports.campers', ['title' => 'Unassigned Campers', 'families' => $families, 'campers' => $campers]);
    }

    public function volunteers()
    {
        $years = \App\Byyear_Camper::where('year', '>', DB::raw('getcurrentyear()-4'))
            ->join('yearattending__volunteer', 'byyear_campers.yearattendingid', 'yearattending__volunteer.yearattendingid')
            ->join('volunteerpositions', 'yearattending__volunteer.volunteerpositionid', 'volunteerpositions.id')
            ->orderBy('byyear_campers.year')->orderBy('byyear_campers.lastname')->orderBy('byyear_campers.firstname')->get()
            ->groupBy('year');
        return view('reports.volunteers', ['years' => $years, 'positions' => \App\Volunteerposition::orderBy('name')->get()]);
    }

    public function workshops()
    {
        DB::raw('CALL workshops()');
        return view('reports.workshops', ['timeslots' => \App\Timeslot::with('workshops.choices.yearattending.camper')->get()]);

    }
}
