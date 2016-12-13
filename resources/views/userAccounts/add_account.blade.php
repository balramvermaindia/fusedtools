@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
					<span>Add Account</span>
				</div>

                <div class="panel-body">
					@if ( Session::has('error') )
						<span class="help-block" style=" color:#C24842; margin-left:34%;">
							<strong>{{ Session::get('error') }}</strong>
						</span>
                     @endif
					 <form class="form-horizontal" role="form" method="POST" action="{{ url('/manage-accounts/add') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('client_id') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Client ID</label>

                            <div class="col-md-6">
                                <input id="client_id" type="text" class="form-control" name="client_id" value="{{ old('client_id') }}">

                                @if ($errors->has('client_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('client_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('client_secret') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">Client Secret</label>

                            <div class="col-md-6">
                                <input id="client_secret" type="text" class="form-control" name="client_secret" value="{{ old('client_secret') }}">

                                @if ($errors->has('client_secret'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('client_secret') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-user"></i> Add
                                </button>
                            </div>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
