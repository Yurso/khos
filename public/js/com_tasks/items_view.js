$(function () {

	function loadTaskMessages() {
		var task_id = $('#id').val();		
		$('.tasks-messages').load('/admin/tasks/messages/get?task_id='+task_id);    	
    }

	$('.tasks-messages .tasks-messages-item .block-item').each(function(){

		var height = $(this).height();

		if (height > 250) {

			$(this).height('250');
			$(this).next('.show-more').show();

		}

	});

	$('.tasks-messages .tasks-messages-item .show-more').click(function(event){

		$(this).prev('.block-item').height('auto');

		$(this).remove();

		event.preventDefault();

	});


	$('#message-send').click(function(){

        $.ajax({
            method: "POST",
            url: "/admin/tasks/messages/save",
            data: $('#itemForm').serialize(),
            dataType: 'json',
            beforeSend: function(){
                $('.message-send-spinner').show();
            },
            error: function(){
                $('.message-send-spinner').hide();
            },
            success: function(msg){
                //console.log(msg);
                if (msg.success == true) {
                    $('#message-text').val(""); 
                    // update messages block or reload page  
                    loadTaskMessages();
                    //location.reload();                 
                } else {
                    // display messages
                }
                $('.message-send-spinner').hide();
            }
        });

    });   

	loadTaskMessages();

});