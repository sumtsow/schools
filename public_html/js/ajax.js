$( document ).ready(function() {
	
	$('#ajax').click(function() {
		var url = $('#edbo_url').html();
		var university_name = $('#university-name').html();
		var encoded_name = encodeURI(university_name);
		var data = 'action=universities&university-id=&qualification=1&education-base=40&speciality=&program=&education-form=&course=&region=&university-name=' + encoded_name;
		var length = data.length;
		$.ajax({
			url: url,
			method: 'POST',
			data: "action=universities&university-id=&qualification=1&education-base=40&speciality=&program=&education-form=&course=&region=&university-name=%D0%A5%D0%B0%D1%80%D0%BA%D1%96%D0%B2%D1%81%D1%8C%D0%BA%D0%B8%D0%B9+%D0%BD%D0%B0%D1%86%D1%96%D0%BE%D0%BD%D0%B0%D0%BB%D1%8C%D0%BD%D0%B8%D0%B9+%D1%83%D0%BD%D1%96%D0%B2%D0%B5%D1%80%D1%81%D0%B8%D1%82%D0%B5%D1%82+%D1%96%D0%BC%D0%B5%D0%BD%D1%96+%D0%92.+%D0%9D.+%D0%9A%D0%B0%D1%80%D0%B0%D0%B7%D1%96%D0%BD%D0%B0",
			dataType: 'json',
			/*beforeSend: function(xhr) {
			 * xhr.setRequestHeader('X-Alt-Referer', 'http://www.google.com');
			 * xhr.setRequestHeader('Referer', url);
			},*/
			complete: function(jqXHR) {
				console.log(jqXHR);
			},
			success: function(data) {
				$('#ajax_result').css('display', 'block');
				$('#ajax_result').html(data);
			}			
		});
	});
});
