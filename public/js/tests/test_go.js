$(document).ready(function(){ 

	var next_item_num = 1;
	var item_answer = '';

	function show_next_item() {

		var item_class = '.question-item'+next_item_num;

		$('.question-item').hide();
		$(item_class).show();

		item_answer = $(item_class).children('.question-item-answer').text();	

		next_item_num = next_item_num + 1;

	}	 

	show_next_item();

	$('.buttons .b-next').click(function(){
		console.log('click');
		show_next_item();
	});

	$('.simple-choice input').prop('disabled', false);

	$('.simple-choice input').click(function(){
		console.log('hello');
		var chosen_answer = $(this).next('label').text();
		if (chosen_answer == item_answer) {
			$(this).next('label').addClass('correct-answer');	
		} else {
			$(this).next('label').addClass('wrong-answer');	
		}
	});

});