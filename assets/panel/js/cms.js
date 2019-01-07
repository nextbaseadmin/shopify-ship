	$(document).ready(function() {
		$('#tabledata').DataTable( {
			"bPaginate": false,
			"sDom": '<"bottom"flp><"clear">'
		  } );
		$('.datepicker').datetimepicker();
	} );
	
	
	function act_form(id){
		$.ajax({
				url:  siteURL+segmentURL+'/form',
				data:{val:id},
				dataType:"html",
				type:"post",
				success:function(response){
					$(".content").html(response);
				}
			});
	   }
	   
	function delete_form(id){
		var answer = confirm("Are you sure that you want to delete?")
		if (answer){
				$.ajax({
						url: siteURL+segmentURL+'/delete',
						data:{val:id},
						dataType:"json",
						type:"post",
						success:function(response){
							location.reload();
						}
					});
		}else{
				
		}
	}
	 
	function replace_slug(){
		  var val = $('#title').val();
		  val = val.toLowerCase().replace(/ /g, '_').replace(/[^a-z0-9_\-]/g, '');
		  $('#slug').val(val);
	  }
	
	function imgError(image) {
		image.onerror = "";
		image.src = siteURL+'/assets/uploads/images/default.png';
		return true;
	}