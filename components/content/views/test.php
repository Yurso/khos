<div class="q-page">
	<p><input type="text" name="q" id="q"></p>
	<p id="q-data"></p>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		
		$('#q').keyup(function(){

			var q = $(this).val();
			
			$.ajax({
				method: "POST",
				url: "/content/blog/test_ajax",
				data: { q: q },
				dataType: 'json'
			}).done(function( msg ) {
				
				var html = '<div class="q-items">';

				msg.forEach(function(element) {
					html += '<div class="q-item">';
					html += '<p class="q-text">'+element.q_text+'</p>';
					html += '<p class="q-answer">'+element.q_answer+'</p>';
					html += '</div>';
				});

				html += '</div>';

				$('#q-data').html(html);

			});

		});
		
	});
</script>