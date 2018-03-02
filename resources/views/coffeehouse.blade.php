@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.min.css"/>
    <style>
        .sortable {
            list-style-type: none;
            margin: 0;
            padding: 0;
            width: 60%;
        }

        .sortable li:hover {
            cursor: pointer;
        }
    </style>
@endsection

@section('title')
    Coffeehouse
@endsection

@section('heading')
    Use this schedule to determine who's onstage tonight
@endsection

@section('content')
    <div class="container">
        <form id="coffeform" class="form-horizontal" role="form" method="POST" action="{{ url('/coffeehouse') }}">
            @include('snippet.flash')

            <ul class="nav nav-tabs flex-column flex-lg-row" role="tablist">
                <li role="presentation" class="nav-item">
                    <a href="#1" aria-controls="1" role="tab" class="nav-link active" data-toggle="tab">Monday</a>
                </li>
                <li role="presentation" class="nav-item">
                    <a href="#2" aria-controls="1" role="tab" class="nav-link" data-toggle="tab">Tuesday</a>
                </li>
                <li role="presentation" class="nav-item">
                    <a href="#3" aria-controls="1" role="tab" class="nav-link" data-toggle="tab">Wednesday</a>
                </li>
                <li role="presentation" class="nav-item">
                    <a href="#4" aria-controls="1" role="tab" class="nav-link" data-toggle="tab">Thursday (Raunch
                        Night)</a>
                </li>
                <li role="presentation" class="nav-item">
                    <a href="#5" aria-controls="1" role="tab" class="nav-link" data-toggle="tab">Friday</a>
                </li>
            </ul>
            <div class="tab-content">
                @while($firstday->dayOfWeek < 6)
                    <div role="tabpanel"
                         class="tab-pane fade{{ $firstday->addDay()->dayOfWeek == 1 ? ' active show' : '' }}"
                         aria-expanded="{{ $firstday->dayOfWeek ==  1 ? 'true' : 'false' }}"
                         id="{{ $firstday->dayOfWeek }}">
                        <h5>{{ $firstday->toDateString() }}</h5>
                        <ul class="list-group sortable col-md-4 col-sm-6">
                            @foreach($acts->where('date', $firstday->toDateString())->all() as $act)
                                <li id="{{ $act->id }}" class="list-group-item list-group-item-action">
                                    @if($readonly === false)
                                        <div class="float-right">
                                            <label for="{{ $act->id }}-delete" class="sr-only">Delete</label>
                                            <input id="{{ $act->id }}-delete" name="{{ $act->id }}-delete"
                                                   type="checkbox"/>
                                            Delete?
                                        </div>
                                        <input type="hidden" id="{{ $act->id }}-order" name="{{ $act->id }}-order"/>
                                    @endif
                                    {{ $act->name }}
                                    @if($readonly === false)
                                        ({{ $act->equipment }})
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                        @include('snippet.formgroup', ['type' => 'next', 'label' => '', 'attribs' => ['name' => 'Next Day ']])
                    </div>
                @endwhile
            </div>
            @if($readonly === false)
                <div class="well">
                    <h4>Add New Act</h4>
                    @include('snippet.formgroup', ['type' => 'select', 'label' => 'Days', 'attribs' => ['name' => 'day'],
                        'list' => [['id' => '1', 'name' => 'Monday'], ['id' => '2', 'name' => 'Tuesday'],
                        ['id' => '3', 'name' => 'Wednesday'], ['id' => '4', 'name' => 'Thursday'],
                        ['id' => '5', 'name' => 'Friday']], 'option' => 'name'])

                    @include('snippet.formgroup', ['label' => 'Act Name',
                        'attribs' => ['name' => 'name', 'placeholder' => 'Brief title']])

                    @include('snippet.formgroup', ['label' => 'Equipment',
                        'attribs' => ['name' => 'equipment', 'placeholder' => 'Will appear to A/V crew, not campers']])

                    @include('snippet.formgroup', ['label' => 'Display Order',
                        'attribs' => ['name' => 'order', 'data-number-to-fixed' => '0',
                        'placeholder' => 'Position in which to display the act', 'min' => '1']])

                    @include('snippet.formgroup', ['type' => 'submit', 'label' => '', 'attribs' => ['name' => 'Save Changes']])
                </div>
            @endif
        </form>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $("ul.sortable").sortable({
            placeholder: "ui-state-highlight"
        });

        $('#coffeform').on('submit', function (e) {
            $("ul.sortable").each(function () {
                $(this).find("li").each(function (index) {
                    $("#" + $(this).attr("id") + "-order").val(index + 1);
                });
            });
            return true;
        });
    </script>
@endsection