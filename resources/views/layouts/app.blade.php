<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Midwest Unitarian Universalist Summer Assembly</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="/css/muustrap.css"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans|Roboto:500"/>
    <script src="//use.fontawesome.com/9364904132.js"></script>

@role(['admin', 'council'])
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.min.css"/>
@endrole

@yield('css')

<!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <!-- Collapsed Hamburger -->
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#myNavbar">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">
                <h4>
                    <img alt="Brand" src="/images/brand.png"> <span class="hidden-xs">Midwest Unitarian
                        Universalist Summer Assembly</span>
                </h4>
            </a>
        </div>
        <div class="collapse navbar-collapse" id="myNavbar">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="{{ url('/MUUSA_2017_Brochure.pdf') }}">Web Brochure</a></li>
                <li><a href="{{ url('/contact') }}">Contact Us</a></li>
                <!-- Authentication Links -->
                @if (Auth::guest())
                    <li><a href="{{ url('/login') }}">Login</a></li>
                    <li><a href="{{ url('/register') }}">Create Account</a></li>
                @else
                    <li><a href="{{ url('/logout') }}"
                           onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                            Logout
                        </a>

                        <form id="logout-form" action="{{ url('/logout') }}" method="POST"
                              style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>
@role(['admin', 'council'])
<ul class="nav nav-pills">
    <li role="presentation">
        <form class="navbar-form navbar-left">
            <div class="form-group">
                <input type="text" id="camper" class="form-control camperlist" placeholder="Camper Name">
                <input id="camperid" type="hidden">
                {{--<button id="quickregister" class="btn btn-default fa fa-bolt action" data-toggle="tooltip"--}}
                        {{--title="Quick Register!"></button>--}}
                <button id="household" class="btn btn-default fa fa-home action" data-toggle="tooltip"
                        title="Household Information"></button>
                <button id="camper" class="btn btn-default fa fa-group action" data-toggle="tooltip"
                        title="Camper Listing"></button>
                <button id="payment" class="btn btn-default fa fa-money action" data-toggle="tooltip"
                        title="Payment"></button>
                {{--<button id="workshopchoice" class="btn btn-default fa fa-rocket action" data-toggle="tooltip"--}}
                        {{--title="Workshop Preferences"></button>--}}
                {{--<button id="assignroom" class="btn btn-default fa fa-bed action" data-toggle="tooltip"--}}
                        {{--title="Assign Room"></button>--}}
            </div>
        </form>
    </li>
    <li role="presentation" class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
           aria-expanded="false">
            Administration <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
            <li><a href="{{ url('/admin/roles') }}">User Roles</a></li>
        </ul>
    </li>
    <li role="presentation" class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
           aria-expanded="false">
            Reports <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
            <li><a href="{{ url('/reports/campers') }}">Registered Campers</a></li>
            <li><a href="{{ url('/reports/rooms') }}">Room List</a></li>
            <li><a href="{{ url('/reports/staffpositions') }}">Staff Positions</a></li>
        </ul>
    </li>
</ul>
@endrole

@yield('content')

<script
        src="//code.jquery.com/jquery-3.1.1.min.js"
        integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
        crossorigin="anonymous"></script>
<script src="/js/bootstrap.min.js"></script>

@role(['admin', 'council'])
<script
        src="//code.jquery.com/ui/1.12.1/jquery-ui.min.js"
        integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU="
        crossorigin="anonymous"></script>
<script type="text/javascript">
    $('[data-toggle="tooltip"]').tooltip();
    $('button.action').on('click', function (e) {
        e.preventDefault();
        $(this).parents('form').attr('action', '/' + $(this).attr('id') + '/c/' + $("#camperid").val()).submit();
    });
    $('input.camperlist').each(function() {
        $(this).autocomplete({
            source: "/data/camperlist",
            minLength: 3,
            select: function (event, ui) {
                $(this).val(ui.item.lastname + ", " + ui.item.firstname);
                $(this).next("input").val(ui.item.id);
                return false;
            }
        }).autocomplete('instance')._renderItem = function (ul, item) {
            return $("<li>").append("<div>" + item.lastname + ", " + item.firstname + "</div>").appendTo(ul);
        };
    });
</script>
@endrole

@yield('script')

</body>
</html>
