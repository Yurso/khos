<script type="text/javascript">

	var last_comment_id = 0;

	function load_comments() {		

		url = "/comments/json_list/<?php echo $this->params['controller']; ?>/<?php echo $this->params['item_id']; ?>/"+last_comment_id;

		$.ajax(url).done(function(data) {
			
			var obj = jQuery.parseJSON(data);
			var html = '';

			if (obj.length > 0) {

				$.each(obj, function( index, value ) {

					last_comment_id = value.id;

					html = html + '<div class="comments-item item'+value.id+'">';
					html = html + '	<div class="comments-item-head"><strong>'+value.name+'</strong> <span style="font-size:10px;">('+value.create_date+')</span></div>'
					html = html + '	<p class="comments-item-body">'+value.comment+'</p>';
					html = html + '	<div class="comments-item-buttons">'
					if (value.have_access) {					
						html = html + '		<a class="delete" onclick="return delete_comment('+value.id+');" href="/comments/delete/'+value.id+'" title="Удалить комментарий"><span class="ui-icon ui-icon-close"></span></a>';
					}
					html = html + '	</div>';
					html = html + '</div>';				

				});

				$('.comments-list').append(html);

				var objDiv = document.getElementById("comments-list");
				objDiv.scrollTop = objDiv.scrollHeight;

			}
			
		});

		console.log("Обновил список комментариев");

	}

	function delete_comment(id) {
		
		var url = '/comments/delete/'+id;

		$.ajax(url).done(function(data) {
			var obj = jQuery.parseJSON(data);
			if (obj.success == true) {						
				$(".item"+id).animate({ backgroundColor: "#fbc7c7" }, "fast").animate({ opacity: "hide" }, 250);
			} else {
				alert(obj.message);						
			}
		});

		return false;
	}

	$(function(){ 
		
		$(".comments-item-buttons a").click(function(){
			var url = $(this).attr("href");

			$.ajax(url).done(function(data) {
				var obj = jQuery.parseJSON(data);
				if (obj.success == true) {						
					load_comments();
				} else {
					alert(obj.message);						
				}
			});

			return false;
		});

		$( "#comments-form" ).submit(function(event) {
			$.ajax({
				type: 'POST',
				url:  $(this).attr("action"),
				data: $(this).serialize(),
				success: function(data) {
					var obj = jQuery.parseJSON(data);
					
					if (obj.success == true) {												
						//load_comments();
					} else {
						$('.results').html(obj.message);						
					}					
				},
				error:  function(xhr, str) {
				    alert('Возникла ошибка: ' + xhr.responseCode);
				}
			});

			document.getElementById("comments-form").reset();

			return false;	
		});
		
	});

	setInterval(function() {
    	load_comments();
	}, 1000);	

</script>

<div class="comments-inner">
	
	<div class="comments-list" id="comments-list">	
		<?php # include('comments_list.php'); ?>
	</div>	

	<div class="comments-form">
		<form method="post" action="/comments/save" id="comments-form">
			<p><strong>Оставить комментарий</strong></p>
			<input type="text" name="comment" autocomplete="off">
			<!-- <textarea id="comment"></textarea> -->
			<input type="hidden" name="item_id" value="<?php echo $this->params['item_id']; ?>">
			<input type="hidden" name="controller" value="<?php echo $this->params['controller']; ?>">
			<input type="submit" value="Отправить" style="margin-top:10px;" />
		</form>
		<div class="results"></div>
	</div>	

</div>
