@extends('layouts.appstrap')

@section('title')
    Online Directory
@endsection

@section('heading')
    This handy resource, in the interest of privacy, only contains entries of campers who attended the same year(s) as yourself.
@endsection

@section('content')
    <div class="alert alert-info">
        Unfortunately, we do not have historical data from Lake Geneva Summer Assembly.
    </div>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center pt-3">
            <li class="page-item">
                <a href="#" class="page-link" id="prev" aria-label="Previous">
                    <i class="far fa-chevron-left fa-fw"></i> Previous
                </a>
            </li>
            @foreach($letters->groupBy('first') as $letter => $families)
                <li class="page-item letters{!! $loop->first ? ' active' : '' !!}">
                    <a href="#" class="page-link">{{ $letter }}</a>
                </li>
            @endforeach
            <li class="page-item">
                <a href="#" class="page-link" id="next" aria-label="Next">
                    Next <i class="far fa-chevron-right fa-fw"></i>
                </a>
            </li>
        </ul>
    </nav>
    <div class="row">
        <div class="col-md-4 m-2">
            <input id="search" class="form-control" placeholder="Search" autocomplete="off"/>
        </div>
        <div class="col-md-7 pt-3" align="right">Total Number of Families: {{ count($letters) }}</div>
    </div>
    @foreach($letters->groupBy('first') as $letter => $families)
        <div id="{{ $letter }}" class="letterdiv" {!!  !$loop->first ? ' style="display: none;"' : '' !!}>
            <table class="table">
                <thead>
                <tr>
                    <th width="25%">Family</th>
                    <th width="50%">Address</th>
                    <th width="25%">Years Attended</th>
                </tr>
                </thead>
                @foreach($families as $family)
                    <tr class="family">
                        <td>{{ $family->name }}</td>
                        <td>{{ $family->address1 }} |
                            @if(!empty($family->address2))
                                {{ $family->address2 }} |
                            @endif
                            {{ $family->city }}, {{ $family->statecd }} {{ $family->zipcd }}</td>
                        <td align="right">{!! $family->formatted_years !!}</td>
                    </tr>
                    <tr class="members">
                        <td colspan="3">
                            <table class="table table-sm">
                                @foreach($campers[$family->id] as $camper)
                                    <tr>
                                        <td width="34%" class="name align-middle">{{ $camper->lastname }},
                                            {{ $camper->firstname }}
                                        </td>
                                        <td width="33%" class="align-middle">
                                            @if(isset($camper->email))
                                                <a href="mailto:{{ $camper->email }}">{{ $camper->email }}</a>
                                            @endif
                                        </td>
                                        <td width="33%" class="align-middle"><a href="tel:+1{{ $camper->phonenbr }}">
                                                {{ $camper->formatted_phone }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endforeach
@endsection

@section('script')
    <script>
        $(function () {
            $(".letters").on('click', function () {
                if (!$(this).hasClass("disabled")) {
                    $("li.active").removeClass("active");
                    $(this).addClass("active");
                    $(".letterdiv:visible").fadeOut(250);
                    $("#" + $(this).find("a").text()).fadeIn();
                }
            });

            $("#prev").on('click', function () {
                var active = $('li.active');
                var prev = active.prevAll("li:not('.disabled')").first();
                if (prev.find("a").attr("id") !== "prev") {
                    active.removeClass("active");
                    prev.addClass("active");
                    $(".letterdiv:visible").fadeOut(250);
                    $("#" + prev.find("a").text()).fadeIn();
                }
            });

            $("#next").on('click', function () {
                var active = $('li.active');
                var next = active.nextAll(":not('.disabled')").first();
                if (next.find("a").attr("id") !== "next") {
                    active.removeClass("active");
                    next.addClass("active");
                    $(".letterdiv:visible").fadeOut(250);
                    $("#" + next.find("a").text()).fadeIn();
                }
            });

            $("#search").keyup(function () {
                $("tr.family").each(function () {
                    $(this).hide().next().hide();
                });
                $(".letters").addClass("disabled");
                $("tr.family:contains('" + $(this).val() + "')").each(function () {
                    $(this).show().next().show();
                    $(".letters:contains('" + $(this).find("td:first").text().substr(0, 1) + "')").removeClass("disabled");
                });
                $("td.name:contains('" + $(this).val() + "')").each(function () {
                    var letter = $(this).parents(".members").show().prev().show().find("td:first").text().substr(0, 1);
                    $(".letters:contains('" + letter + "')").removeClass("disabled");
                });
                if ($("li.active").hasClass("disabled")) {
                    $(".letters:not('.disabled'):first a").click();
                }
            });
        });
    </script>
@endsection