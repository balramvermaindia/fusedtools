@extends('layouts.app')

@section('content')
	<div class="col-md-10">
		@if ( Session::has('success') )
			<span class="help-block text-center" style=" color:green;">
				<strong>{{ Session::get('success') }}</strong>
			</span>
		@endif
		@if ( Session::has('error') )
			<span class="help-block text-center" style=" color:#C24842;">
				<strong>{{ Session::get('error') }}</strong>
			</span>
		@endif
		<div class="panel panel-default">
			<div class="panel-heading">
				<span>Import CSV</span>
			</div>
			<div class="panel-body">
				<div class="steps">
					<h4><span>Step 5: Complete</span></h4>
				</div>
					<h5 class="sub-title text-center" style="margin-top:0px;">{{ $message }}</h5>
				<div class="row">
					<div class="col-md-5 col-md-offset-1" style="margin-top:20px;">
						<a class="pull-right btn" href=" {{ url('import-step1') }}" style="color:black;"><strong>Import another List</strong></a>
					</div>
					<div class="col-md-5" style="margin-top:20px; color:black;">
						<a class="pull-left btn" href="http://{{ $account }}" target="_blank" style="color:black;"><strong>Go to InfusionSoft</strong></a>
					</div>
				</div>
				</div>
			</div>
		</div>
	</div>

@endsection




