<div class="pull-right"><a class="btn btn-sm btn-info" href="{{ url('manage-accounts/add') }}">ADD</a></div>
<br/>
<br/>
<table class="table table-striped">
	<tr>
		<th>Account</th>
		<th>Date Created</th>
		<th>Status</th>
		<th>Action</th>
	</tr>
	@if ( count($accounts) > 0 )
		@foreach ($accounts as $account )
			<tr>
				<td>{{ @$account->account }}</td>
				<td>{{ date('d/m/Y h:i a', strtotime($account->created_at) ) }}</td>
				@if( @$account->active == 1)
					<td><span style="color:green">Active</span</td>
				@else 
					<td><span style="color:red">Inactive</span></td>
				@endif
				
				<td>
					@if( @$account->active == 1)
						{{--*/ $class 	= 'fa-lock' /*--}}
						{{--*/ $status 	= 'active' /*--}}
						{{--*/ $title 	= 'Inactive' /*--}}
						
					@else
						{{--*/ $class 	= 'fa-unlock' /*--}}
						{{--*/ $status 	= 'inactive' /*--}}
						{{--*/ $title 	= 'active' /*--}}
						
					@endif
					<a  class=" action-icons change-status" href="javascript:void(0)" data-id="{{@$account->id}}" data-status="{{ $status }}" title="{{ $title }}"><i class="fa {{ $class }}" aria-hidden="true"></i></a>
					<a href="javascript:void(0)" class=" action-icons delete-account" data-id="{{@$account->id}}" ><i class="fa fa-trash-o" aria-hidden="true" title="Delete"></i></a>
				</td>
			</tr>
		@endforeach
	@else 
		<tr>
			<td colspan="4" class="text-center">No Record Found.</td>
		</tr>
	@endif
</table>
<style>
	.action-icons i {
		font-size:18px;
		margin-right:5px;
	}
</style>
