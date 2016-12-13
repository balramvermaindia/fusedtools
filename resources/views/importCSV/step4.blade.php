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
					<h4><span>Step 4: Tags</span></h4>
				</div>
				<form name="step3" method="post" action="{{ url('import-step5') }}" id="myForm">
					{{ csrf_field() }}
				
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
				
					
					  <div class="form-group pull-right">
							<a class="btn btn-info"  href="{{ url('/') }}">Cancel Import</a>
							<input class="btn btn-info" type="button" value="Finish Import">
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
				 if( radioVal == "yes" && val == '' ) {
					 $('.error_msg').css('display','block');
					 return false; 
				 }
				//$("#hiddenfield").val(val);
				$('#myForm').submit();	
        });
	});
	</script>
@endsection

