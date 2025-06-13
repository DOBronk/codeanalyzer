<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: "Lato", sans-serif;
            margin: 0;
        }

        .topbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 50px;
            background-color: #333;
            color: white;
            line-height: 50px;
            padding: 0 0px;
            font-size: 20px;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .sidenav {
            height: 100%;
            width: 200px;
            position: fixed;
            z-index: 1;
            top: 50px;
            /* below topbar */
            left: 0;
            background-color: #111;
            overflow-x: hidden;
            padding-top: 20px;
        }

        .sidenav a {
            padding: 6px 8px 6px 16px;
            text-decoration: none;
            font-size: 25px;
            color: #818181;
            display: block;
        }

        .sidenav a:hover {
            color: #f1f1f1;
        }

        .maincontent {
            margin-left: 160px;
            /* same as sidenav width */
            margin-top: 50px;
            /* same as topbar height */
            font-size: 28px;
            padding: 0 100px;
        }

        @media screen and (max-height: 450px) {
            .sidenav {
                padding-top: 15px;
            }

            .sidenav a {
                font-size: 18px;
            }
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
    <div class="topbar">
        <div style="padding: 0 20px;">Code Analyzer - @yield('page')</div>
        <div>{{ Auth::user()->name }}:<a style="color: white;" :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">{{ __('Uitloggen') }}</a></div>
    </form>
    </div>

    <div class="sidenav">
        <a href="{{ route('codeanalyzer.index') }}">Jobs</a>
        <a href="{{ route('codeanalyzer.issues') }}">Issues</a>
        <a href="{{ route('codeanalyzer.settings') }}">Instellingen</a>
    </div>

    <div class="maincontent">
        @yield('content')
    </div>

</body>

</html>