	   
	function add_access(id,data){
		$.ajax({
				url: siteURL+'rule/add_access/',
				data:"&val="+id+"&data="+data,
				dataType:"json",
				type:"post",
				success:function(response){
					alert("data berhasil disimpan");
				}
			});
	   }
	   
	function edit_access(id,data){
		$.ajax({
				url: siteURL+'rule/edit_access/',
				data:"&val="+id+"&data="+data,
				dataType:"json",
				type:"post",
				success:function(response){
					alert("data berhasil disimpan");
				}
			});
	}
	
	function delete_access(id,data){
		$.ajax({
				url: siteURL+'rule/delete_access/',
				data:"&val="+id+"&data="+data,
				dataType:"json",
				type:"post",
				success:function(response){
					alert("data berhasil disimpan");
				}
			});
	}
	
	function publish_access(id,data){
		$.ajax({
				url: siteURL+'rule/publish_access/',
				data:"&val="+id+"&data="+data,
				dataType:"json",
				type:"post",
				success:function(response){
					alert("data berhasil disimpan");
				}
			});
	}
	
	function remove_access(id){
		$.ajax({
				url: siteURL+'rule/remove_access/',
				data:"&val="+id,
				dataType:"json",
				type:"post",
				success:function(response){
					window.location.reload();
				}
			});
	}
	 
	 
	 
	 function delete_rule(id){
		var answer = confirm("Are you sure that you want to delete?")
		if (answer){
				$.ajax({
						url: siteURL+'rule/delete_rule',
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

	   
	   
	$('#submit').click(function(event) {
       form = $("#dataform").serialize();
			$.ajax({
				type: "POST",
				url: siteURL+'rule/actmodule/',
				data: form,
				success: function(data){
				   window.location.reload();
			   }
			 });
		 event.preventDefault();
		 return false;

	  });
	  
	  function edit_rule_name(id){
		name = $("#namerule").val();
		$.ajax({
				url: siteURL+'rule/edit_rule_name/',
				data:"&val="+id+"&name="+name,
				dataType:"json",
				type:"post",
				success:function(response){
					alert("data berhasil disimpan");
				}
			});
		}