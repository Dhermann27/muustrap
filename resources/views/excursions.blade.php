@extends('layouts.app')

@section('content')
    <p>&nbsp;</p>
    <div class="container">
        <div class="row">
            <h2>Excursions</h2>
        </div>
        <div>
            @foreach($workshops as $workshop)
                <ul class="list-group">
                    <li class="list-group-item">
                        @if($workshop->capacity == 999)
                            <span class="alert alert-success badge">Unlimited Enrollment</span>
                        @elseif($workshop->enrolled >= $workshop->capacity)
                            <span class="alert alert-danger badge">Waitlist Available</span>
                        @elseif($workshop->enrolled >= ($workshop->capacity * .75))
                            <span class="alert alert-warning badge">Filling Fast!</span>
                        @else
                            <span class="alert alert-info badge">Open For Enrollment</span>
                        @endif
                        <h3>{{ $workshop->name }}</h3>
                        <h5>Led by {{ $workshop->led_by }}</h5>
                        <p>{{ $workshop->blurb }} <i>Days: {{ $workshop->displayDays }}</i></p>
                    </li>
                </ul>
            @endforeach
        </div>
    </div>
@endsection