$( document ).ready(function() {
	
	$('.update').click( function(event) {
		event.stopPropagation();		
		event.preventDefault();
		let id_school = $(this).parents('tr').first().find('.id_school').text();
		$.ajax({
			url: '/admin/update/' + id_school,
			method: 'get',
			success: function(data) {
				console.log(data);
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.log(textStatus, errorThrown);
			}
		});
	});
	
});