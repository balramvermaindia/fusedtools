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
					<h4><span>Step 1</span></h4>
				</div>
				<form name="step1" id="step1" method="Post" action="{{ url('import-step2') }}" enctype="multipart/form-data">
					<br/>
					{{ csrf_field() }}
					<div class="form-group">
						<div class="row">
							<div class="col-md-5">
								<select class="form-control" name="account" id="account">
								 @if ( count($infusionsoftAccounts) > 0 )
									 <option value=''>Select Infusionsoft Account</option>
									 <?php 
									 
									 if( Session::has('CSV_import') ) {
										$CSV_import = Session::get('CSV_import');
										
										if ( isset($CSV_import['is_account_id']) && !empty($CSV_import['is_account_id']) ) {
											$is_account_id = $CSV_import['is_account_id'];
										}
										if ( isset($CSV_import['csv_file']) && !empty($CSV_import['csv_file']) ) {
											$csv_file = $CSV_import['csv_file'];
										}
									 }
									 
									 ?>
									 @foreach( $infusionsoftAccounts as $id => $account )
											
										<?php 
											$i = 1;
											$select = '';
											if( isset($CSV_import) ) {
												if( $id == $is_account_id ) {
													$select = "selected='selected'";
												}
											} else {
												 
												if( count($infusionsoftAccounts) == 1 && $i = 1 ) { 
													$select = "selected='selected'"; 
													} 
											}		
										?>
										 <option value="{{ $id }}" <?php  echo $select; ?>>{{ $account }}</option>
										 <?php $i++; ?>
									@endforeach
								@else
									 <option value=''>Select Infusionsoft Account</option>
								@endif
								</select>
								<div class="account_error"></div>
							</div>
						</div>
					</div>
					@if ( isset( $CSV_import ) )
						<div class="form-group" id="uploaded-file-div">
							<div class="col-md-3">
								<label for="exampleInputFile">Uploaded File: </label>{{ $csv_file }}
							</div>
							<div class="col-md-5">
								<input class="btn btn-sm btn-info" type="button" name="change" value="Change" id="change"/>
							</div>
						</div>
					 @endif 
					
						<div class="form-group" id="select-file-div" @if( isset( $CSV_import ) ) style='display:none' @endif>
							<label for="exampleInputFile">Select file to upload</label>
							<input type="file" name="csv_file">
						 </div>
					<input type="hidden" name="parm" id="parm">
				   <div class="form-group">
					<input class="btn btn-info pull-right submit" type="submit" value="Next ->">
				   </div>
				</form>
			</div>
		</div>
	</div>

@endsection

@section('script')
<script>
	$(document).ready(function(){
		
		$(document).off('click','.submit').on('click','.submit',function(e){
			e.preventDefault();
			if($('#select-file-div').is(':visible')){
				$("#step1").validate({ 
				  rules: {
					 account: { required: true },
					 csv_file: {
						required: true,
						//~ accept: 'in:txt|csv|xls|xlsx'
					}
				  },
				  messages: {
					account: { required: "Please select Infusionsoft Account."},
					csv_file: {
						required: "Please upload file.",
						//~ accept: "Invalid file extension, valid extensions are in:txt,csv,xls,xlsx."
					},
				  }
				});
				 if ( $("#step1").valid() == true ) {
					 $("#step1").submit();
				 } else {
					 return false;
				 }
			} else {
			
				$("#step1").validate({ 
					  rules: {
						 account: { required: true },
					  },
					  messages: {
						account: { required: "Please select Infusionsoft Account."},
					  }
				});
				 if ( $("#step1").valid() == true ) {
					 $('#parm').val('back');
					 $("#step1").submit();
				 } else {
					 return false;
					  $('#parm').val('');
				 }
			}
			  
		});
		
		$(document).off('click','#change').on('click','#change',function(e) {
			e.preventDefault();
			$('#uploaded-file-div').css('display','none');
			$('#select-file-div').css('display','block');
		});
	});

</script>
@endsection

