$( document ).ready(function() {
	$('#ajax').click(function() {
		var url = $('#edbo_url').html();
		var data = {
			'qualification': '1',
			'education-base': '40',
			'university-name': $('#university-name').html(),
		};
		$.ajax({
			url: url,
			method: 'post',
			data: data,
			dataType: 'json',
			complete: (function(data) {
				$('#ajax_result').css('display', 'block');
				$('#ajax_result').html(data);
				console.log(data);
			})
		});
	});
});