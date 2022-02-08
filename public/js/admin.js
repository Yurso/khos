function toggle(source) {
	checkboxes = document.getElementsByName('checked[]');
	for(var i=0, n=checkboxes.length;i<n;i++) {
		checkboxes[i].checked = source.checked;
	}
}

function checkForm(fromid) {
	
	var errors = 0;

	$(fromid + " input.error").removeClass("error");

	$(fromid + " input.required").each(function( index ) {						
		if (!$(this).val()) {
			$(this).addClass("error");
			errors++;
		}
	});

	if (errors > 0) {
		return false;
	}

	return true;

}

function submitForm(form, action, target) {

	var action_before = form.action;
	var target_before = form.target;

	if (action != undefined)
		form.action = form.action + action;

	if (target != undefined)
		form.target = target;

	form.submit();

	form.action = action_before;
	form.target = target_before;

	return false;

}

function declOfNum(number, titles) {  
    cases = [2, 0, 1, 1, 1, 2];  
    return titles[ (number%100>4 && number%100<20)? 2 : cases[(number%10<5)?number%10:5] ];  
}

function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

$(document).ready(function(){ 

	$(".fancybox").fancybox();             
    
  //   $('.mainmenu a.ajax').click(function(){    

  //   	var url = $(this).attr("href");

  //   	$('.mainmenu a').removeClass('active');

  //   	$(this).addClass("active");    	

		// loadPage(url);

		// return false;
               
  //   });

    //$('#data-table').DataTable();

 //    $('.redactor').redactor({
	//     minHeight: 200,
	//     imageUpload: '/admin/blog/upload_image',
	//     fileUpload: '/admin/blog/upload_file',
	//     imageGetJson: '/admin/blog/uploaded_images2'
	// });

	$( ".datepicker" ).datepicker({        
		dateFormat: "yy-mm-dd",
		firstDay: 1,
		onSelect: function (dateText, inst) {
			$(this).val(dateText + ' 00:00:00');
		}
	});

	$( ".datepicker_date" ).datepicker({        
		dateFormat: "yy-mm-dd",
		firstDay: 1,
		onSelect: function (dateText, inst) {
			$(this).val(dateText);
		}
	});

	$(".messages").click(function(){
		$(this).slideUp('fast');
	});

	$(".messages .messages-inner").hide().slideDown('fast').delay(3000).slideUp('fast');	

	$(".form-reset").click(function(){
		
		//$(this).parents("form").children("input[type=text], select").val("");		

		$("form input[type=text]").val("");
		$("form select").val("");
		
		$(this).parents("form").submit();		

	});

	$( ".adminform.ajax" ).submit(function( event ) {        
        
        $.ajax({
            type: 'POST',
            url:  $(this).attr("action"),
            data: $(this).serialize(),
            cache: false,
            success: function(data) {
            	console.log(data);
                var obj = jQuery.parseJSON(data);
                var msg = '';
                                        
                if (obj.messages) {
                    obj.messages.forEach(function(message) {                                
                        msg = msg + '\n' + message;
                    });
                    alert(msg);
                }

                if (obj.redirect) {
                    window.location.replace(obj.redirect);
                } 
                // if (obj.messages) {
                // 	$('.messages').hide().html('<div class="messages-inner"></div>');                            
                    
                //     obj.messages.forEach(function(message) {
                //         $('.messages .messages-inner').append('<div class="sysmsg sysmsgmessage">'+message+'</div>');
                //         //alert(message);
                //     });                        

                //     $('.messages').slideDown('fast').delay(3000).slideUp('fast');
                // }
                                                      
            },
            error:  function(xhr, str) {
                alert('Возникла ошибка: ' + xhr.responseCode);
            }
        });

        event.preventDefault();

    });

	$(".toggle_advansed_filters").click(function(){
		
		$(".advansed_filters").slideToggle("fast");

	});

	tinymce.init({
        selector:'.wysywig',
        height : 300,
        language: 'ru',
        theme: 'modern',
		plugins: [
			'advlist autolink lists link image charmap print preview hr anchor',
			'searchreplace visualblocks visualchars code fullscreen',
			'insertdatetime media nonbreaking table contextmenu directionality',
			'template paste textcolor colorpicker textpattern imagetools codesample toc'
		],
		toolbar1: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
		toolbar2: 'print preview media | forecolor backcolor | codesample',
		image_advtab: true,
	    relative_urls: false,
	    resize: 'both',
	    file_browser_callback: function(field_name, url, type, win) {
	    	tinymce.activeEditor.windowManager.open({
				title: 'File Manager',
				url: '/admin/content/files?theme_type=clean',
				width: 620,
				height: 340,
				resizable : "no",
				buttons: [{
			        text: 'Insert',
			        classes: 'widget btn primary first abs-layout-item',
			        //id: 'uniqueid',
			        //disabled: true,
			        onclick: function(){
			        	url = tinymce.activeEditor.windowManager.getParams().url;
			        	win.document.getElementById(field_name).value = url;
			        	tinymce.activeEditor.windowManager.close();
			        }
			    },{
					text: 'Close',
					onclick: 'close',
					window : win,
					input : field_name
				}]
			}, {
				url: '',
	        	// onselect: function(url) {
	         //    	win.document.getElementById(field_name).value = url;
	         //    	//tinymce.activeEditor.windowManager.close();
	        	// }
	        });
		}
    });

});

window.onscroll = function() {
	var scrolled = window.pageXOffset || document.documentElement.scrollLeft;
	var marginLeft = 200 - scrolled;

	if (marginLeft > 0) {
		$(".buttons").css("marginLeft", marginLeft);
	}
	else {
		$(".buttons").css("marginLeft", 0);
	}
}