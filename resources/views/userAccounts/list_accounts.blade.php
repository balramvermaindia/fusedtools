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
				<span>Manage Accounts</span>
			</div>
			<div class="panel-body">
				@include('userAccounts/list_accounts_ajax')
			</div>
		</div>
	</div>
    
	<style>
		.action-icons i {
			font-size:18px;
			margin-right:5px;
		}
	</style>
	
@endsection

@section('script')
	<script>
	$(document).ready(function(){
		
		/* Change status start */
		$(document).off('click','.change-status').on('click','.change-status',function(e){
			e.preventDefault();
			var accountID = $(this).attr('data-id');
			var status    = $(this).attr('data-status');
			if( accountID && status ) {
				$.ajax({
					'type': 'post',
					'url' : 'manage-accounts/change_status',
					'data': { 'accountID':accountID,'status':status,'_token':'{{ csrf_token() }}' },
					'dataType':'html',
					success: function(response){
						if( response ) {
							$('.panel-body').html('');
							$('.panel-body').html(response);
						} 
					}
				});
			} else {
				return false;
			}
		});
		/* Change status ends */
		
		/* Delete Account start */
		$(document).off('click','.delete-account').on('click','.delete-account',function(e){
			e.preventDefault();
			var status = confirm('Are you sure you want to delete this account?');
			if ( status == true ) {
				var accountID = $(this).attr('data-id');
				if( accountID ) {
					$.ajax({
						'type': 'post',
						'url' : 'manage-accounts/delete',
						'data': { 'accountID':accountID,'_token':'{{ csrf_token() }}' },
						'dataType':'html',
						success: function(response){
							if( response ) {
								$('.panel-body').html('');
								$('.panel-body').html(response);
							} 
						}
					});
				} else {
					return false;
				}
			} else {
				return false;
			}
		});
		/* Delete account ends */
	});

</script>
@endsection
