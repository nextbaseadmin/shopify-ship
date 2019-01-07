	function pass_key(){
		  var val = $('#password').val();
		  $('#repassword').val(val);
	  }
	  
	  function photo_change(){
		   var val = $('#photo').val().replace(/C:\\fakepath\\/i, '');
		  $('#rephoto').val(val);
	  }