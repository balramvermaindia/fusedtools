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
					<h4><span>Step 3</span>CSV Import Settings</h4>
				</div>
				<form name="step3" method="post" action="{{ url('import-step4') }}" id="myForm">
					{{ csrf_field() }}
<!--
					<h4 class="sub-title" style="margin-top:0px;">Options Level 1</h4>
					  <div class="form-group col-md-12">
							
							<div>
								<label class="radio-inline">
									<input type="radio" name="filter_display" value="dispaly"> Display for a manual dedup
								</label>
								<label class="radio-inline">
									<input type="radio" name="filter_display"  value="update" checked> Just update them with the information in the file
								</label>
							</div>
					  </div>
					  <h4 class="sub-title">Options Level 2 -</h4>
-->
					  <div class="form-group col-md-6" style="min-height:120px;">
							<label>Import settings for customers</label>
							<div class="radio">
								  <label>
									<input type="radio" name="filter_contact" value="both" >
									Create & Update customers
								  </label>
							</div>
							<div class="radio">
								  <label>
									<input type="radio" name="filter_contact" value="create" >
									Create customers only
								  </label>
							</div>
							<div class="radio">
								  <label>
									<input type="radio" name="filter_contact" value="update" checked>
									Update Customers only
								  </label>
							</div>
					  </div>
					  
					  <div class="form-group col-md-6" style="min-height:130px;">
							<div class="form-group">
							  <label>Would you like to apply tag(s) to ALL of these contacts</label>
								<div>
									<label class="radio-inline">
										<input type="radio" name="apply_tags" value="yes" checked> Yes
									</label>
									<label class="radio-inline">
										<input type="radio" name="apply_tags" value="no" > No
									</label>
								</div>
						  </div>
				  
						 <div class="form-group" id="search-tags">
							<label for="exampleInputFile">Search and pick tags to apply</label>
							<input type="text" id="select-tags" name="tags">
							<span class="help-block error_msg" style="display:none; color:#C24842;">
								<strong>Please pick atleast one tag .</strong>
							</span>
						</div>
					  </div>
					    <div class="form-group col-md-6"  >
							<div class="col-md-6" style="padding:0px;margin-top: -20px;">
								<label for="exampleInputFile"style="display:block;">Notification Email<span>*</span></label>
								<input type="email" id="notification_email" name="notification_email" class="form-control">
								<span class="help-block error_email_msg" style="display:none; color:#C24842;">
									
								</span>
							</div>
						</div>
					  
					  
					  <?php
							if ( Session::has('CSV_import') ) {
								$csv_import 		= Session::get('CSV_import');
								$fields_arr 		= $csv_import['fields_arr'];
								$display_company 	= false;
								$display_email   	= false;
								$display_fname   	= false;
								$display_lname   	= false;
								$display_phone   	= false;
								$phone_arr       	= array('Phone1','Phone2','Phone3','Phone4','Phone5');
								
								if ( count($fields_arr) ) {
									
									if ( in_array('Company', $fields_arr ) ) {
										$display_company = true;
									} 
									if ( in_array('Email', $fields_arr ) ) {
										$display_email = true;
									}
									if ( in_array('FirstName', $fields_arr ) ) {
										$display_fname = true;
									}
									if ( in_array('LastName', $fields_arr ) ) {
										$display_lname = true;
									}
									
									if ( count( array_intersect($fields_arr, $phone_arr  ) )  ) {
										$display_phone = true;
									}
								}
								
							}
					   ?>
					  
					  @if ( $display_company )
						   <div class="form-group col-md-6" style="min-height:120px;">
								<label>Import settings for companies</label>
								<div class="radio">
									  <label>
										<input type="radio" name="filter_company" value="both">
										Create & Match companies 
									  </label>
								</div>
								<div class="radio">
									  <label>
										<input type="radio" name="filter_company" value="create" checked>
										Create companies only
									  </label>
								</div>
								<div class="radio">
									  <label>
										<input type="radio" name="filter_company" value="match" checked>
										Match companies only
									  </label>
								</div>
								<div class="radio">
									  <label>
										<input type="radio" name="filter_company" value="ignore" checked>
										Ignore companies all together
									  </label>
								</div>
						  </div>
					@endif
					@if ( $display_email || $display_phone || $display_fname && $display_lname ) 
						  <div class="form-group col-md-6" style="min-height:80px;">
								<label>How should we match duplicate customers </label>
								@if( $display_email || $display_fname && $display_lname )
									<div>
										@if( $display_email )
											<label class="radio-inline col-md-5">
												<input type="radio" name="filter_duplicate" value="1" checked> Email
											</label>
										@endif
										@if ( $display_fname && $display_lname)
											<label class="radio-inline col-md-5">
												<input type="radio" name="filter_duplicate"  value="2"> First Name & Last Name
											</label>
										@endif
									</div>
								@endif
								@if( $display_email || $display_phone )
									<div>
										@if( $display_email && $display_phone )
											<label class="radio-inline col-md-5">
												<input type="radio" name="filter_duplicate" value="3"> Email & Phone
											</label>
										@endif
										@if( $display_phone )
											<label class="radio-inline col-md-5">
												<input type="radio" name="filter_duplicate"  value="4"> Phone
											</label>
										@endif
									</div>
								@endif
								@if( $display_email && $display_fname )
									<div>
										<label class="radio-inline col-md-5">
											<input type="radio" name="filter_duplicate" value="5"> Email & First Name
										</label>
									</div>
								@endif
						  </div>
					  @endif
					  
					 
					  
					  
					  
					  
					  <div class="form-group col-md-12">
						<a class="btn btn-info pull-left"  href="{{ url('import-step2') }}"> <- Back</a>
						<input class="btn btn-info pull-right" type="button" value="Next -> ">
					  </div>
				</form>
			</div>
		</div>
	</div>

@endsection

@section('script')
	<script type="text/javascript">
		$(document).ready(function(){
			
			var val = $('input[name=apply_tags]:checked', '#myForm').val();
			if( val == "yes" ) {
				$('#search-tags').css('display','block');
				$('.error_msg').css('display','none');
			} else {
				$('#search-tags').css('display','none');
				$('.error_msg').css('display','none');
			}
			
			
			var tags = <?php echo $tags; ?>;
			//~ console.log(JSON.stringify(tags));
			$("#select-tags").tokenInput(tags,{
			  propertyToSearch: "GroupName",
			  hintText: "Select Tags",
			  noResultsText: "No results",
			  searchingText: "Searching...",
              preventDuplicates: true,
			  allowFreeTagging: true,
			});
			
			$('#myForm input').on('change', function() {
				var val = $('input[name=apply_tags]:checked', '#myForm').val();
				if( val == "yes" ) {
					$('#search-tags').css('display','block');
					$('.error_msg').css('display','none');
				} else {
					$('#search-tags').css('display','none');
					$('.error_msg').css('display','none');
				}
			});
			
			$("input[type=button]").click(function () {
				
				 var val = $('#select-tags').tokenInput("get");
				 var radioVal = $('input[name=apply_tags]:checked', '#myForm').val();
				 var emailVal = $('input[name=notification_email]','#myForm').val();
				 if( radioVal == "yes" && val == '' ) {
					 $('.error_msg').css('display','block');
					 return false; 
				 } else {
					 $('.error_msg').css('display','none');
				 }
				 if( emailVal == '' ) {
					 $('.error_email_msg').html('<strong> Please enter email address. </strong>');
					 $('.error_email_msg').css('display','block');
					 return false;
				 } else {
					  if (!ValidateEmail(emailVal) ) {
							 $('.error_email_msg').html('');
							 $('.error_email_msg').html('<strong>Invalid email address.</strong>');
							 $('.error_email_msg').css('display','block');
							 return false;
						} else {
							$('.error_email_msg').css('display','none');
						}
				 }
				//$("#hiddenfield").val(val);
				$('#myForm').submit();	
        });
        
        function ValidateEmail(email) {
			
			var expr = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
			return expr.test(email);
			
		};
	});
	</script>
@endsection

