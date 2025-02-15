<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>FusedTools</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" integrity="sha384-XdYbMnZ/QjLh6iI4ogqCTaIjrFk87ip+ekIjefZch0Y+PvJ8CDYtEs1ipDmPorQ+" crossorigin="anonymous">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">


    <!-- Styles -->
     <link rel="stylesheet" href="{{ url('assets/css/style.css')}}" crossorigin="anonymous">
     <link rel="stylesheet" href="{{ url('assets/css/token-input.css')}}" crossorigin="anonymous">
     <link rel="stylesheet" href="{{ url('assets/css/bootstrap.min.css')}}" crossorigin="anonymous">
    {{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}

</head>
<body id="app-layout">
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/home') }}">
                    FusedTools
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
<!--
                    <li><a href="{{ url('/home') }}">Home</a></li>
-->
                   
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
<!--
                        <li><a href="{{ url('/login') }}">Login</a></li>
                        <li><a href="{{ url('/register') }}">Register</a></li>
-->
                    @else
<!--
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
                            </ul>
                        </li>
-->
						<li><a href="#profile">My Profile</a></li>
						<li><a href="{{ url('/manage-accounts') }}">Manage IS Accounts</a></li>
						<li><a href="{{ url('/logout') }}">Logout</a></li>
						
                    @endif
                </ul>
            </div>
        </div>
    </nav>
	<div class="container-fluid">
		<div class="row">
			@if( Auth::user() )
				<div class="col-md-2">
					<nav class="nav-sidebar">
						<ul class="nav">
							<li class="@if( Request::is('home') || Request::is('/')  ) active @endif"><a href="{{ url('/home') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
							<li class="@if( Request::is('import-step1') || Request::is('import-step1/*')|| Request::is('import-step2') || Request::is('import-step3') || Request::is('import-step4') || Request::is('import-step5'))  active @endif"><a href="{{ url('import-step1') }}"><i class="fa fa-cloud-download"></i> Import CSV</a></li>
							<li class="@if( Request::is('manage-accounts') ) active @endif"><a href="{{ url('/manage-accounts') }}"><i class="fa fa-plug"></i> Manage IS Account</a></li>
						</ul>
					 </nav>
				</div>
			@endif
    @yield('content')
    
		</div>
	</div>
    
    <!-- jQuery -->
    <script src="{{ URL::to('assets/js/jquery.js') }}"></script>
    <script src="{{ URL::to('assets/js/jquery.validate.min.js') }}"></script>
    <script src="{{ URL::to('assets/js/bootstrap.min.js') }}"></script>
    {{-- <script src="{{ elixir('js/app.js') }}"></script> --}}
    <script src="{{ URL::to('assets/js/jquery.tokeninput.js') }}"></script>
    @yield('script')
</body>
</html>
