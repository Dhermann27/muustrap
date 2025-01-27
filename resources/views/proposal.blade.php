@extends('layouts.appstrap')

@section('title')
    MUUSA Workshop Proposal
@endsection

@section('heading')
    Send us your ideas for next year's favorite camper-led activity!
@endsection

@section('content')
    <div class="container">
        <form id="proposal" class="form-horizontal" role="form" method="POST" action="{{ url('/proposal') }}">

            @include('snippet.flash')

            @include('snippet.address', ['camper' => $camper])

            @include('snippet.formgroup', ['type' => 'select',
                'label' => 'Type of Activity', 'attribs' => ['name' => 'type'],
                'default' => 'Which kind of activity are you suggesting?',
                'list' => [['id' => 'Workshop', 'name' => 'Workshop (Usually multiple days throughout the week)'],
                            ['id' => 'Vespers', 'name' => 'Vespers (usually a single session in the evening)'],
                            ['id' => 'Special Event', 'name' => 'Special Event (anything else)']], 'option' => 'name'])

            @include('snippet.formgroup', ['label' => 'Proposed Title', 'attribs' => ['name' => 'name']])

            @include('snippet.formgroup', ['type' => 'text', 'label' => 'Synopsis for Brochure (500 letters or fewer)',
                'attribs' => ['name' => 'message']])

            @include('snippet.formgroup', ['type' => 'text', 'label' => 'Your Qualifications',
                'attribs' => ['name' => 'qualifications']])

            @include('snippet.formgroup', ['label' => 'Have you offered this at MUUSA before? If so, when?',
                'attribs' => ['name' => 'atmuusa']])

            @include('snippet.formgroup', ['label' => 'Have you offered this elsewhere before? If so, where and when?',
                'attribs' => ['name' => 'atelse']])

            @include('snippet.formgroup', ['type' => 'select',
                'label' => 'For which age groups is this appropriate?', 'attribs' => ['name' => 'ages'],
                'list' => [['id' => 'Adults', 'name' => 'Adults'], ['id' => 'All Ages', 'name' => 'All Ages'],
                            ['id' => 'Young Adults', 'name' => 'Young Adults'],
                            ['id' => 'Senior High', 'name' => 'Senior High'],
                            ['id' => 'Junior High', 'name' => 'Junior High']], 'option' => 'name'])

            @include('snippet.formgroup', ['type' => 'select',
                'label' => 'On which days would you like to offer this?', 'attribs' => ['name' => 'days'],
                'list' => [['id' => 'M-F', 'name' => 'Monday through Friday'],
                            ['id' => 'MWF', 'name' => 'Monday, Wednesday, and Friday'],
                            ['id' => 'TuTh', 'name' => 'Tuesday and Thursday'],
                            ['id' => 'Single', 'name' => 'Any single day']], 'option' => 'name'])

            <div class="form-group row{{ $errors->has('timeslot') ? ' has-danger' : '' }}">
                <label for="timeslot" class="col-md-4 control-label">During which time slot would you
                    prefer to offer this?</label>

                <div class="col-md-6">
                    <select id="timeslot" name="timeslot"
                            class="form-control{{ $errors->has('timeslot') ? ' is-invalid' : '' }}">
                        @foreach($timeslots as $timeslot)
                            <option value="{{ $timeslot->name }}"{{ (old('timeslot') == $timeslot->name) ? " selected" : "" }}>
                                {{ $timeslot->name }}
                                @if($timeslot->id != '1005')
                                    ({{ $timeslot->start_time->format('g:i A') }}
                                    - {{ $timeslot->end_time->format('g:i A') }})
                                @endif
                            </option>
                        @endforeach
                    </select>

                    @if ($errors->has('timeslot'))
                        <span class="invalid-feedback">
                            <strong>{{ $errors->first('timeslot') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            @include('snippet.formgroup', ['label' => 'Please add any details about your time slot preference.',
                'attribs' => ['name' => 'timeslotpref']])


            @include('snippet.formgroup', ['label' => 'Room Requirements (table, chairs, etc.)',
                'attribs' => ['name' => 'room']])

            @include('snippet.formgroup', ['label' => 'Equipment Requirements (flip chart, projector, etc.)',
                'attribs' => ['name' => 'equip']])

            @include('snippet.formgroup', ['label' => 'Participant Fee', 'attribs' => ['name' => 'fee']])

            @include('snippet.formgroup', ['label' => 'Maximum Number of Participants',
                'attribs' => ['name' => 'capacity']])

            @include('snippet.formgroup', ['type' => 'select', 'title' => 'waivecredit',
                'label' => 'Are you willing to volunteer your time and waive camp credit?', 'attribs' => ['name' => 'waive'],
                'list' => [['id' => 'No', 'name' => 'No'],
                            ['id' => 'Yes', 'name' => 'Yes']], 'option' => 'name'])

            @include('snippet.formgroup', ['label' => 'Please list any additional details of which we should be aware.',
                'attribs' => ['name' => 'details']])

            @include('snippet.formgroup', ['type' => 'captcha', 'label' => 'CAPTCHA Test',
                'attribs' => ['name' => 'g-recaptcha-response']])

            @include('snippet.formgroup', ['type' => 'submit', 'label' => '', 'attribs' => ['name' => 'Send Proposal']])
        </form>
    </div>
@endsection

@section('script')
    {!! NoCaptcha::renderJs() !!}
@endsection