<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ToolsController extends Controller
{
    public function cognoscenti()
    {
        return view('tools.cognoscenti',
            ['staff' => \App\Thisyear_Staff::where('pctype', '!=', '0')->orderBy('staffpositionid')->orderBy('lastname')
                ->get()->groupBy('pctype')]);
    }


    public function nametags()
    {
        return view('tools.nametags', ['campers' => \App\Thisyear_Camper::orderBy('familyname')
            ->orderBy('familyid')->orderBy('birthdate')->get()]);
    }

    public function nametagsList()
    {
        return view('tools.nametagslist', ['campers' => \App\Thisyear_Camper::orderBy('familyname')
            ->orderBy('familyid')->orderBy('birthdate')->get()]);
    }

    public function nametagsPrint(Request $request)
    {
        $ids = array();
        foreach ($request->all() as $key => $value) {
            if (preg_match('/(\d+)-print/', $key, $matches) && $value == 'on') {
                array_push($ids, $matches[1]);
            }
        }
        $campers = \App\Thisyear_Camper::whereIn('id', $ids)->orderBy('familyname')->orderBy('familyid')
            ->orderBy('birthdate')->get();
        return view('tools.nametags', ['campers' => $campers]);
    }

    public function nametagsFamily($i, $id)
    {
        return view('tools.nametags', ['campers' => \App\Thisyear_Camper::where('familyid', $this->getFamilyId($i, $id))
            ->orderBy('familyname')->orderBy('birthdate')->get()]);
    }

    private function getFamilyId($i, $id)
    {
        return $i == 'c' ? \App\Camper::find($id)->familyid : $id;
    }

    public function positionStore(Request $request)
    {
        foreach ($request->all() as $key => $value) {
            $matches = array();
            if (preg_match('/(\d+)-(\d+)-delete/', $key, $matches)) {
                $ya = \App\Thisyear_Camper::find($matches[1]);
                if ($ya) {
                    $assignment = \App\Yearattending__Staff::where('yearattendingid', $ya->yearattendingid)->where('staffpositionid', $matches[2]);
                } else {
                    $assignment = \App\Camper__Staff::where('camperid', $matches[1])->where('staffpositionid', $matches[2]);
                }
                if ($value == 'on') {
                    $assignment->delete();
                }
            }
        }
        foreach (\App\Program::all() as $program) {
            if ($request->input($program->id . '-camperid') != '' && $request->input($program->id . '-staffpositionid') != '') {
                $ya = \App\Thisyear_Camper::find($request->input($program->id . '-camperid'));
                if (!empty($ya)) {
                    $assignment = new \App\Yearattending__Staff();
                    $assignment->yearattendingid = $ya->yearattendingid;
                    $assignment->staffpositionid = $request->input($program->id . '-staffpositionid');
                    $assignment->is_eaf_paid = 1;
                    $assignment->save();
                } else {
                    $assignment = new \App\Camper__Staff();
                    $assignment->camperid = $request->input($program->id . '-camperid');
                    $assignment->staffpositionid = $request->input($program->id . '-staffpositionid');
                    $assignment->save();
                }
            }
        }
        DB::statement('CALL generate_charges(getcurrentyear());');

        $request->session()->flash('success', 'Assigned. Suckers! No backsies.');

        return redirect()->action('ToolsController@positionIndex');
    }

    public function positionIndex()
    {
        $year = \App\Year::where('is_current', '1')->first()->year;
        return view('tools.positions', ['programs' => \App\Program::with(['staffpositions' => function ($query) use ($year) {
            $query->where('start_year', '<=', $year)->where('end_year', '>=', $year);
        }])->with('assignments')->orderBy('order')->get()]);
    }

    public function programStore(Request $request)
    {
        foreach (\App\Program::all() as $program) {
            if ($request->input($program->id . '-blurb') != '<p><br></p>') {
                $program->blurb = $request->input($program->id . '-blurb');
                $program->letter = $request->input($program->id . '-letter');
                $program->covenant = $request->input($program->id . '-covenant');
                $program->calendar = $request->input($program->id . '-calendar');
                $program->save();
            }
        }
        $request->session()->flash('success', 'Psssssh, great job updating your program. Yeah, you\'re a big deal now, I\'m sure. Whatever.');

        return redirect()->action('ToolsController@programIndex');
    }

    public function programIndex()
    {
        return view('tools.programs', ['programs' => \App\Program::orderBy('order')->get()]);
    }

    public function workshopStore(Request $request)
    {
        foreach ($request->all() as $key => $value) {
            $matches = array();
            if (preg_match('/(\d+)-(timeslotid|name|led_by|roomid|order|capacity)/', $key, $matches)) {
                $workshop = \App\Workshop::findOrFail($matches[1]);
                $workshop->{$matches[2]} = $value;
                $workshop->save();
            }
        }

        if ($request->input('name') != '') {
            $workshop = new \App\Workshop();
            $workshop->timeslotid = $request->input('timeslotid');
            $workshop->name = $request->input('name');
            $workshop->led_by = $request->input('led_by');
            $workshop->roomid = $request->input('roomid');
            $workshop->order = $request->input('order');
            $workshop->blurb = $request->input('blurb');
            $workshop->m = $request->input('days-m') == 'on' ? '1' : '0';
            $workshop->t = $request->input('days-t') == 'on' ? '1' : '0';
            $workshop->w = $request->input('days-w') == 'on' ? '1' : '0';
            $workshop->th = $request->input('days-h') == 'on' ? '1' : '0';
            $workshop->f = $request->input('days-f') == 'on' ? '1' : '0';
            $workshop->capacity = $request->input('capacity');
            $workshop->fee = 0;
            $workshop->year = DB::raw('getcurrentyear()');
            $workshop->save();
        }

        $request->session()->flash('success', 'Underwater New Age Basket Weaving? AGAIN??');

        return redirect()->action('ToolsController@workshopIndex');
    }

    public function workshopIndex()
    {
        return view('tools.workshops', ['timeslots' => \App\Timeslot::all(),
            'rooms' => \App\Room::where('is_workshop', '1')->get()]);
    }
}
