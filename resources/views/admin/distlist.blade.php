@extends('layouts.app')

@section('content')
    <p>&nbsp;</p>
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">Distribution Lists</div>
            <div class="panel-body">
                <form class="form-horizontal" role="form" method="POST" action="{{ url('/admin/distlist') }}">
                    {{ csrf_field() }}

                    @if(!empty($rows))
                        <h4>Count: {{ count($rows) }}</h4>
                        <table class="table table-responsive">
                            <thead>
                            <tr>
                                @foreach($columns as $column)
                                    <th>{{ $column }}</th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($rows as $row)
                                <tr>
                                    @foreach($columns as $column)
                                        <td>{{ $row->{$column} }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif

                    <div class="form-group{{ $errors->has('campers') ? ' has-error' : '' }}">
                        <label for="campers" class="col-md-4 control-label">Base Camper List</label>

                        <div class="col-md-6">
                            <select id="campers" name="campers" class="form-control">
                                <option value="all"{{ old('campers', $request->input('campers')) == 'all' ? ' selected' : '' }}>
                                    All campers
                                </option>
                                <option value="reg"{{ old('campers', $request->input('campers')) == 'reg' ? ' selected' : '' }}>
                                    All registered
                                    campers
                                </option>
                                <option value="prereg"{{ old('campers', $request->input('campers')) == 'prereg' ? ' selected' : '' }}>
                                    All campers
                                    registered before 9/30
                                </option>
                                <option value="threeyears"{{ old('campers', $request->input('campers')) == 'threeyears' ? ' selected' : '' }}>
                                    All
                                    Campers from the last 3 years
                                </option>
                            </select>

                            @if ($errors->has('campers'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('campers') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label for="email" class="col-md-4 control-label">Restrict to Campers with Email
                            Addresses?</label>

                        <div class="col-md-6">
                            <select id="email" name="email" class="form-control">
                                <option value="1">Yes</option>
                                <option value="0"{{ old('email', $request->input('email')) == '0' ? ' selected' : '' }}>
                                    No
                                </option>
                            </select>

                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('ecomm') ? ' has-error' : '' }}">
                        <label for="ecomm" class="col-md-4 control-label">Restrict to Campers with Email
                            Preference?</label>

                        <div class="col-md-6">
                            <select id="ecomm" name="ecomm" class="form-control">
                                <option value="1">Yes</option>
                                <option value="0"{{ old('ecomm', $request->input('ecomm')) == '0' ? ' selected' : '' }}>
                                    No
                                </option>
                            </select>

                            @if ($errors->has('ecomm'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('ecomm') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="programs" class="col-md-4 control-label">Programs (all off by default)</label>
                        <div class="col-md-6 btn-group" data-toggle="buttons">
                            @foreach($programs as $program)
                                <label class="btn btn-default {{ old('program-' . $program->id, $request->input('program-' . $program->id)) == 'on' ? 'active' : '' }}">
                                    <input type="checkbox" name="program-{{ $program->id }}" autocomplete="off"
                                            {{ old('program-' . $program->id, $request->input('program-' . $program->id)) == 'on' ? ' checked="checked"' : '' }}/>
                                    {{ $program->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="family" class="col-md-4 control-label">Family Data to Display (all off by
                            default)</label>
                        <div class="col-md-6 btn-group" data-toggle="buttons">
                            <label class="btn btn-default {{ old('family-name', $request->input('family-name')) == 'on' ? 'active' : '' }}">
                                <input type="checkbox" name="family-name" autocomplete="off"
                                        {{ old('family-name', $request->input('family-name')) == 'on' ? ' checked="checked"' : '' }} />
                                Family Name
                            </label>


                            <label class="btn btn-default {{ old('family-address', $request->input('family-address')) == 'on' ? 'active' : '' }}">
                                <input type="checkbox" name="family-address" autocomplete="off"
                                        {{ old('family-address', $request->input('family-address')) == 'on' ? ' checked="checked"' : '' }} />
                                Address
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="camper" class="col-md-4 control-label">Camper Data to Display (all off by
                            default)</label>
                        <div class="col-md-6 btn-group" data-toggle="buttons">
                            <label class="btn btn-default {{ old('camper-firstname', $request->input('camper-firstname')) == 'on' ? 'active' : '' }}">
                                <input type="checkbox" name="camper-firstname" autocomplete="off"
                                        {{ old('camper-firstname', $request->input('camper-firstname')) == 'on' ? ' checked="checked"' : '' }} />
                                First Name
                            </label>

                            <label class="btn btn-default {{ old('camper-lastname', $request->input('camper-lastname')) == 'on' ? 'active' : '' }}">
                                <input type="checkbox" name="camper-lastname" autocomplete="off"
                                        {{ old('camper-lastname', $request->input('camper-lastname')) == 'on' ? ' checked="checked"' : '' }} />
                                Last Name
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-2 col-md-offset-8">
                            <button type="submit" class="btn btn-primary">Display Data</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection