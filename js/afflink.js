jQuery(document).ready(function($){	
		
	//end of new function
		$("#ssdate").datepicker({ dateFormat: 'yy-mm-dd' });
		$("#eedate").datepicker({ dateFormat: 'yy-mm-dd' });
	//custom function for delete and search
	function ajaxCall(){
		var tdId = jQuery(this).attr("id");		
		var conf = confirm("Are You Sure!");
		if(conf == false){
			return false;
		}		
		$.ajax({
						
			async: true,
			type:'post',
			url:AffAjax.ajaxurl,
			dataType: "html",
			cache:false,
			timeout:10000,
			data:{
				'action':'tracking_ajax_data',
				'id':tdId,
				'nonce':AffAjax.nonce
			},
			
			success:function(result){
				
				alert(result);
				//reloading page
				window.location = AffAjax.pageurl;
				return false;
			},
			
			error: function(jqXHR, textStatus, errorThrown){
				jQuery('#footer').html(textStatus);
				alert(textStatus);
				return false;
				}
		});
	
		return false;
	}//end of the custom function
	
	
	//pagination of main menu page
	var totalPage = AffAjax.pageno;
	var tableHtml = null;
	//alert(totalPage);
	$('#page-numbers_1').css({'background-color':'#B4EDAF'});
	$('#page-numbers1').css({'background-color':'#B4EDAF'});
	var pageNo = null;
	$('.page-numbers').click(function(){
		pageNo = $(this).html();
		pageId = '#'+$(this).attr("id");
				
		var start = (pageNo-1)*10;
		var sty = start + 1;
		var end = start+10;
		
		
		//starting ajax
		$.ajax({
						
			async: true,
			type:'post',
			url:AffAjax.ajaxurl,
			dataType: "html",
			cache:false,
			timeout:10000,
			data:{
				'action':'pagination_ajax_data',
				'start':start,
				'end':end,
				'nonce':AffAjax.nonce
			},
			
			success:function(result){
				var message = 'Displaying '+ sty +'-'+end+' of';
				
				$('#displaying-num-ajax').html(message);
				$('#displaying-num-ajaxx').html(message);
				//pagination page coloring
				for(i=1;i<=totalPage;i++){
					if(i==pageNo){
						$('#page-numbers_'+i).css({'background-color':'#B4EDAF'});
						$('#page-numbers'+i).css({'background-color':'#B4EDAF'});
					}
					else{
						$('#page-numbers_'+i).css({'background-color':'#FFFFFF'});
						$('#page-numbers'+i).css({'background-color':'#FFFFFF'});
					}
				}
			
				$('#table-div-for-ajax').html(result);
				
				
				return false;
			},
			
			error: function(jqXHR, textStatus, errorThrown){
				jQuery('#footer').html(textStatus);
				alert(textStatus);
				return false;
				}
			
		});							
	
		return false;
	});//end of pagination
	
	
	//tracking results default and clearing options
	
	$('.remove-data-afflink-single').click(ajaxCall);
		
		
	
	//starting of advanced searching
	$('#advanced-select').change(function(){
		
		var item = $(this).val();
		//alert(item);
		if(item == 'date'){
			tableHtml = $('#default-result').html();
			$('#tracking-result-form').removeClass('form-hiding');
			$('#tracking-result-form').addClass('form-showing');
		}
		else{
			$('#default-result').html(tableHtml);
			$('.remove-data-afflink-single').click(ajaxCall);
			$('#tracking-result-form').removeClass('form-showing');
			$('#tracking-result-form').addClass('form-hiding');			
		}		
		
	});
	
	//taking the dates
	$('#sseesubmit').click(function(){
		var sdate = $('#ssdate').val();
		var edate = $('#eedate').val();
		var slug = $('#slug-ajax').val();
		//alert(sdate);
		//alert(edate);
		//alert(slug);
		//calling ajax
		
		$.ajax({
						
			async: true,
			type:'post',
			url:AffAjax.ajaxurl,
			dataType: "html",
			cache:false,
			timeout:10000,
			data:{
				'action':'advanced-search-trackig',
				'sdate':sdate,
				'nonce':AffAjax.nonce,
				'edate':edate,
				'slug':slug
			},
			
			success:function(result){
				
				if(result == 'nor'){
					alert('No Record Found!');
				}
				else if(result == 'datep'){
					alert('Please Check your date');
				}
				else{
					$('#default-result').html(result);
				}
				$('.remove-data-afflink-single').click(ajaxCall);
				
				//reloading page				
				return false;
			},
			
			error: function(jqXHR, textStatus, errorThrown){
				jQuery('#footer').html(textStatus);
				alert(textStatus);
				return false;
				}
			
		});	
		return false;
			
	});	
	
});
