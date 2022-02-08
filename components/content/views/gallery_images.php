<script type="text/javascript">
	$(document).ready(function(){    
	    $(".blocks .block25 .caption").hide();    
	    
	    // $(".blocks .block25").hover(function () {
	    //     $(this).children('.caption').show();
	    //     $(this).children('a').children('img').animate({top: "-40"}, 150);        
	    // },function () {
	    //     $(this).children('.caption').animate({ opacity: "hide" }, "fast");
	    //     $(this).children('a').children('img').animate({top: "0"}, 150);        
	    // });
	    
	    $('.top-menu li a').click(function(){                  
	        
	        var href = $(this).attr("href")+"?clean=1";
	       
	        // Çàãðóæàåì ñòðàíèöó        
	        $.ajax({
	          url: href,
	          cache: false,
	          success: function(html){
	              // Çàïîëíÿåì áëîê
	              $("#content-block .wrapper").html(html); 
	              // Çàêðûâàåì äðóãîé áëîê
	              $("#content-block2").slideUp('fast').removeClass('active');
	              // ïîêàçûâàåì áëîê è äîáàâëÿåì êëàññ active
	              if ($("#content-block").hasClass('active') == false) {
	                $("#content-block").slideDown('fast').addClass('active');                                    
	              }                                 
	          }
	        });
	        
	        $('.top-menu li a').removeClass("active");
	        $(this).toggleClass("active");        
	               
	        return false;
	    });
	    // Çàãðóçêà ïî êëèêó â áëîêàõ
	    $('.blocks .item a.view').click(function(){                  
	        var href = $(this).attr("href")+"?clean=1";               
	        // î÷èùàåì âðàïïåð
	        $("#content-block2 .wrapper").html('');
	        // ïîêàçûâàåì çàãðóçêó
	        $("#content-block2 .loading").show(); 
	        // ïîêàçûâàåì áëîê
	        $("#content-block2").animate({opacity: "show"}, "fast").addClass('active');                                                    
	        // Çàãðóæàåì ñòðàíèöó        
	        $.ajax({
	          url: href,
	          cache: false,
	          success: function(html){
	              // ñêðûâàåì çàãðóçêó
	              $("#content-block2 .loading").hide();                                 
	              // Çàïîëíÿåì áëîê
	              $("#content-block2 .wrapper").html(html); 
	              // Çàêðûâàåì äðóãîé áëîê
	              $("#content-block").slideUp('fast').removeClass('active');              
	          }
	        });    
	        return false;
	    });
	    
	    $('.close').click(function(){                  
	        $(this).parent().animate({opacity: "hide"}, "fast").removeClass('active');
	    });
	    
	});
</script>
<div class="blocks">
	<?php foreach ($this->items as $item) : ?>
		<div class="block25 blocks-item">
			<a href="<?php echo $item->pathway.$item->filename; ?>" class="fancybox" rel="group">                
				<img src="<?php echo $item->pathway.'thumbs/'.$item->filename; ?>" alt="">
			</a>
			<?php if (false) : ?>
				<div class="caption">
					<?php echo $item->title; ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>