@extends('layouts.app')

@section('title')
    Positions
@endsection

@section('content')
    <div class="container">
        <form class="form-horizontal" role="form" method="POST" action="{{ url('/admin/positions') }}">
            @include('snippet.flash')

            @include('snippet.navtabs', ['tabs' => $programs, 'option' => 'name'])

            <div class="tab-content">
                @foreach($programs as $program)
                    <div role="tabpanel" class="tab-pane fade{{ $loop->first ? ' active show' : '' }}"
                         aria-expanded="{{ $loop->first ? 'true' : 'false' }}" id="{{ $program->id }}">
                        <p>&nbsp;</p>
                        <table class="table table-responsive table-condensed">
                            <thead>
                            <tr>
                                <th id="name">Name</th>
                                <th id="compensationlevelid" class="select">Compensation Level</th>
                                <th>Maximum Compensation</th>
                                <th>Delete?</th>
                            </tr>
                            </thead>
                            <tbody class="editable">
                            @foreach($program->staffpositions()->orderBy('name')->get()->load('compensationlevel') as $position)
                                <tr id="{{ $position->id }}">
                                    <td>{{ $position->name }}</td>
                                    <td>{{ $position->compensationlevel->name }}</td>
                                    <td>
                                        ${{ money_format('%.2n', $position->compensationlevel->max_compensation) }}
                                    </td>
                                    <td class="btn-group" data-toggle="buttons">
                                        <label class="btn btn-default">
                                            <input type="checkbox" name="{{ $position->id }}-delete"
                                                   autocomplete="off"/>
                                            Delete
                                        </label>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        @include('snippet.formgroup', ['label' => 'Add New Position',
                            'attribs' => ['name' => $program->id . '-position', 'placeholder' => 'Position Name']])

                        @include('snippet.formgroup', ['type' => 'select', 'class' => ' compensationlevelid',
                            'label' => 'Compensation Level', 'attribs' => ['name' => $program->id . '-compensationlevel'],
                            'default' => 'Choose a compensation level', 'list' => $levels, 'option' => 'name'])

                    </div>
                @endforeach
            </div>
            @include('snippet.formgroup', ['type' => 'submit', 'label' => '', 'attribs' => ['name' => 'Save Changes']])
        </form>
    </div>
@endsection

