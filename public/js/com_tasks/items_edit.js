$(document).ready(function(){

	function updateProjectsList(customer_id) {

		$.ajax({
            method: "POST",
            url: "/admin/tasks/projects/ajaxGetList",
            data: {customer_id: customer_id},
            dataType: 'json',
            beforeSend: function(){
                $('#project_id').html('<option value="0" selected>Без проекта</option>');                
            },
            success: function(data){            	
                if (data && data.length) {
                	$('#project_id').prop('disabled', false);
                	var options = '';
                	var selected_id = $('#selected_project_id').val();
                	data.forEach(function(element) {
                		var args = '';
                		if (element.id == selected_id) {
                			args += 'selected';
                		}
						options += '<option value="'+element.id+'" '+args+'>'+element.title+'</option>';
					});
					$('#project_id').html(options);
                }
            }
        });

	}

	$( "#autoanswer_create" ).change(function() {  

        if ($(this).prop( "checked")) {

            var message = "";
            var url = $("#url").val();
            var description = $("#description").text();

            message = message + "Добрый день.\n";
            message = message + "Выполнено.\n\n";

            if (url) {
                message = message + url + "\n\n";
            }

            var lines = description.split(/[\n\r]+/);

            lines.forEach(function(line) {
                            
                message = message + "> " + line + "\n";

            });

            $("#autoanswer_textaerea").html(message);

            $("#autoanswer").slideDown();

            $("#description").animate({height: "100px"}, 500);

        } else {
            $("#autoanswer").slideUp();
        }

    });

	$('#customer_id').change(function(){
		updateProjectsList($(this).val());
	});

	updateProjectsList($('#customer_id').val());	

});