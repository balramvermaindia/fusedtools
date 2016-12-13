@extends('layouts.app')

@section('content')

	<div class="col-md-10">
		
		<div class="panel panel-default">
			<div class="panel-heading">
				<span>Import CSV</span>
			</div>
			<div class="panel-body">
				<div class="steps">
					<h4><span>Step 2</span></h4>
					<p> Now let's match the columns in your uploaded file to your Infusion Soft list. </p>
				</div>
				<span class="help-block text-center error_msg" style="display:none; color:#C24842;">
					<strong>Please select Infusionsoft fields.</strong>
				</span>
				@if ( count($file_fields_arr) )
					<div class="form-group">
						<div class="row">
							<div class="col-md-2 col-md-offset-3">
								<b>Your CSV Fields</b>
							</div>
							<div class="col-md-3">
								<b>Infusionsoft Fields</b>
							</div>
						</div>
					</div>
					<?php 
						if( Session::has('CSV_import') ) {
							$CSV_import = Session::get('CSV_import');
							if ( isset($CSV_import['fields_arr']) && !empty($CSV_import['fields_arr']) ) {
								$fields_arr = $CSV_import['fields_arr'];
								//~ echo "<pre>"; print_r($CSV_import['fields_arr']); die;
							}
						}
					?>
					
					<form name="step2" method="Post" action="{{ url('import-step3') }}" id="step2">
						<br/>
						{{ csrf_field() }}
						@foreach( $file_fields_arr as $key => $fieldname)
							
							<div class="form-group">
								<div class="row">
									<div class="col-md-2 col-md-offset-3">
										{{ $fieldname }} 
										<input type="hidden"  name="csv_fields[]" value="{{ $fieldname }}"/>
									</div>
									<div class="col-md-3">
										<select class="form-control map-fields" name="infusionsoft_fields[]">
										  <option value='0' class="fields">Skip this Field</option>
										  @if ( count($IS_fields) ) 
											@foreach( $IS_fields as $is_field => $data_type )
												<?php 
													if( isset($fields_arr) && !empty($fields_arr)  ) {
														$flag = false;
														if(array_key_exists( $fieldname , $fields_arr ) ) {
															$Is_field = $fields_arr[$fieldname];
															
															if( $Is_field == $is_field ) {
																$flag = true;
															} 
														} 
													}
												?>
												<option value='{{ $is_field }}' class="fields" <?php if( isset($flag) && ($flag == true) ) echo "selected='selected'"; ?>> {{ $is_field }}</option>
											@endforeach
										  @endif
										</select>
									</div>
								</div>
							</div>
						@endforeach
						<div class="form-group">
							<a class="btn btn-info pull-left"  href="{{ url('import-step1/back') }}"> <- Back</a>
							<input class="btn btn-info pull-right submit" type="button" value="Next -> ">
					   </div>
					</form>
				@else 
					<div>
						Uploaded file is empty
					</div>
				@endif
			</div>
		</div>
	</div>

@endsection

@section('script')
	<script>
		$(document).ready(function(){
			
			$(document).off('click','.submit').on('click','.submit',function() {
				var empty_val = 1;
				$("#step2").find(".map-fields").each(function() {
					if($(this).val() != 0){
						empty_val = 0;
					}
				});
				if(empty_val){
					$('.error_msg').css('display','block');
				} else {
					$('#step2').submit();
				}
			});
		});
	</script>
@endsection

