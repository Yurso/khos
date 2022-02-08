$(document).ready(function(){ 

	$(".messages .messages-inner").hide().slideDown('fast').delay(3000).slideUp('fast');	

	$(".form-reset").click(function(){
		
		//$(this).parents("form").children("input[type=text], select").val("");		

		$("form input[type=text]").val("");
		$("form select").val("");
		
		$(this).parents("form").submit();		

	});

	$(".toggle_advansed_filters").click(function(){
		
		$(".advansed_filters").slideToggle("fast");

	});

	$("#menu-button").click(function(){
		$('.mainmenu').show('fast');
	});
	$("#menu-button-close").click(function(){
		$('.mainmenu').hide('fast');
	});
	
	$("#filtersForm").hide();

	$(".block-filters .block-title").click(function(){
		$("#filtersForm").slideToggle();
	});

});