<script type="text/javascript">

	function noticeClose(id) {
		$('.notice'+id).hide();
	}

	function noticeGet() {
		$.ajax({
            url: '/admin/system/notices/json_view',                    
        }).done(function(result) {

            console.log(result);
            
            var arr = jQuery.parseJSON(result);

            console.log(arr);

            arr.forEach(function(item) {

            	$result = '<div class="notice-item notice'+item.id+'">';
            	$result = $result + '<div class="notice-title">'+item.title+'</div>';
            	$result = $result + '<div class="notice-desc">'+item.description+'</div>';
                if (item.url) {
                    $result = $result + '<div class="notice-url"><a href="'+item.url+'">Открыть</a></div>';
                }
            	$result = $result + '<div class="notice-close" onclick="noticeClose('+item.id+');"><i class="fa fa-times" aria-hidden="true"></i></div>';
            	$result = $result + '</div>';

            	$(".notices").append($result);

            });

        });
	}
	
	$(document).ready(function(){

		noticeGet();

		setInterval(function() {
    		noticeGet();
		}, 10000);
	
	});

</script>

<div class="notices"></div>