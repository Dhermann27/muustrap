@extends('layouts.appstrap')

@section('title')
    Program Participants
@endsection

@section('content')
    @component('snippet.navtabs', ['tabs' => $programs, 'id'=> 'id', 'option' => 'name'])
        @foreach($programs as $program)
            <div class="tab-pane fade{!! $loop->first ? ' active show' : '' !!}" id="tab-{{ $program->id }}"
                 role="tabpanel">
                @if(count($program->participants) > 0)
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Pronoun</th>
                            <th>Name</th>
                            <th>Age</th>
                            @if($program->is_minor)
                                <th>Parent/Sponsor</th>
                                <th>Room</th>
                                <th>Phone Number</th>
                            @endif
                            <th class="d-print-none">Controls</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($program->participants as $participant)
                            <tr>
                                <td>{{ $participant->pronounname }}</td>
                                <td>
                                    {{ $participant->lastname }}, {{ $participant->firstname }}
                                    @if(isset($participant->email))
                                        <a href="mailto:{{ $participant->email }}" class="d-print-none">
                                            <i class="far fa-envelope"></i>
                                        </a>
                                    @endif
                                </td>
                                <td>{{ $participant->age }}
                                    @if(isset($participant->medicalresponse))
                                        <i class="far fa-pencil-alt"
                                           title="This camper has submitted their medical response."></i>
                                    @endif
                                </td>
                                @if($program->is_minor)
                                    <td>
                                        @if(!empty($participant->sponsor))
                                            <i class='fa fa-id-badge'></i> {{ $participant->sponsor }}
                                        @else
                                            <i class='fa fa-male'></i>
                                            @if (count($participant->parents) == 1)
                                                {{ $participant->parents->first()->firstname }} {{ $participant->parents->first()->lastname }}
                                            @elseif (count($participant->parents) > 1)
                                                @if ($participant->parents[0]->lastname == $participant->parents[1]->lastname)
                                                    {{ $participant->parents[0]->firstname }}
                                                    &amp; {{ $participant->parents[1]->firstname}} {{ $participant->parents[0]->lastname }}
                                                @else
                                                    {{ $participant->parents[0]->firstname }} {{ $participant->parents[0]->lastname }}
                                                    &amp; {{ $participant->parents[1]->firstname}} {{ $participant->parents[1]->lastname }}
                                                @endif
                                            @else
                                                <i>Unsponsored Minor</i>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($participant->sponsor))
                                            N/A
                                        @elseif(count($participant->parents->where('roomid', '!=', '0')) > 0)
                                            {{ $participant->parents->where('roomid', '!=', '0')->first()->buildingname }}
                                            {{ $participant->parents->where('roomid', '!=', '0')->first()->room_number }}
                                        @else
                                            Unassigned
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($participant->sponsor))
                                            N/A
                                        @elseif(count($participant->parents->where('phonenbr', '!=', '')) > 0)
                                            {{ $participant->parents->where('phonenbr', '!=', '')->first()->formatted_phone }}
                                        @endif
                                    </td>
                                @endif
                                <td class="d-print-none">
                                    @include('admin.controls', ['id' => 'c/' . $participant->id])
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="{{ $program->is_minor ? '8' : '4' }}" align="right">
                                <strong>Total Campers: </strong> {{ count($program->participants) }}
                            </td>
                        </tr>
                        <tr class="d-print-none">
                            <td colspan="{{ $program->is_minor ? '8' : '4' }}">
                                Distribution
                                list: {{ $program->participants->where('email', '!=', '')->implode('email', '; ') }}
                            </td>
                        </tr>
                        @if($program->is_minor)
                            <tr class="d-print-none">
                                <td colspan="8">
                                    Parent Distribution list:
                                    @foreach($program->participants as $participant)
                                        @foreach($participant->parents->where('email', '!=', '') as $parent)
                                            {{ $parent->email }};
                                        @endforeach
                                    @endforeach
                                </td>
                            </tr>
                        @endif
                        </tfoot>
                    </table>
                @else
                    <h5>No participants registered yet</h5>
                @endif
            </div>
        @endforeach
    @endcomponent
@endsection

