@extends('layouts.appstrap')

@section('title')
    Bank Deposits
@endsection

@section('content')
    @component('snippet.navtabs', ['tabs' => $chargetypes, 'id'=> 'id', 'option' => 'name'])
        @foreach($chargetypes as $chargetype)
            <div class="tab-pane fade{!! $loop->first ? ' active show' : '' !!}" id="tab-{{ $chargetype->id }}" role="tabpanel">
                @component('snippet.accordion', ['id' => $chargetype->id])
                    @foreach($charges as $ddate => $dcharges)
                        @if(count($dcharges->filter(function ($value) use ($chargetype) {
                                return $value->chargetypeid == $chargetype->id;
                            })) > 0)
                            @component('snippet.accordioncard', ['id' => $chargetype->id, 'loop' => $loop, 'heading' => $ddate, 'title' => empty($ddate) ? 'Undeposited' : $ddate])
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Camper Name</th>
                                        <th>Amount</th>
                                        <th>Timestamp</th>
                                        <th>Memo</th>
                                        <th>Controls</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($dcharges->filter(function ($value) use ($chargetype) {
                                        return $value->chargetypeid == $chargetype->id;
                                    }) as $charge)
                                        <tr>
                                            <td>{{ $charge->camper->firstname }} {{ $charge->camper->lastname }}</td>
                                            <td>{{ money_format('$%.2n', $charge->amount) }}</td>
                                            <td>{{ $charge->timestamp }}</td>
                                            <td>{{ $charge->memo }}</td>
                                            <td>
                                                @include('admin.controls', ['id' => 'c/' . $charge->camper->id])
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="5" align="right">Total Deposit: {{ money_format('$%.2n', abs($dcharges->filter(function ($value) use ($chargetype) {
                                                            return $value->chargetypeid == $chargetype->id;
                                                        })->sum('amount'))) }}
                                        </td>
                                    </tr>
                                    @role(['admin'])
                                    @if(empty($ddate))
                                        <tr>
                                            <td colspan="5" align="right">
                                                <form role="form" method="POST"
                                                      action="{{ url('/reports/deposits/' . $chargetype->id) }}">
                                                    {{ csrf_field() }}
                                                    <div class="form-group">
                                                        <button type="submit"
                                                                class="btn btn-primary">
                                                            Mark As Deposited
                                                        </button>
                                                    </div>
                                                </form>
                                            </td>
                                        </tr>
                                    @endif
                                    @endrole
                                    </tfoot>
                                </table>
                            @endcomponent
                        @endif
                    @endforeach
                @endcomponent
            </div>
        @endforeach
    @endcomponent
@endsection

