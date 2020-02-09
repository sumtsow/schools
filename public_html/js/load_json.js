$( document ).ready(function() {

	$('.download').click( function(event) {
		event.stopPropagation();		
		event.preventDefault();
		var id_school = $(this).parents('tr').first().find('.id_school').text();
		var id_region = $(this).parent('td').find('span.id_region').text();
		$.ajax({
			url: '/admin/download/' + id_school + '/?region=' + id_region,
			method: 'get',
			success: function(data, textStatus, jqXHR) {
				console.log(data);
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.log(jqXHR.responseText);
			}
		});
	});

	$('.update').click( function(event) {
		event.stopPropagation();		
		event.preventDefault();
		var id_school = $(this).parents('tr').first().find('.id_school').text();
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

	$('[id^="th-"]').click(function() {
		var id_region = $('#region').find('option:selected').val();
		var field = this.id.split('-')[1];
		var lastSortField = $('#sort-field').text();
		var order = $('#sort-order').text();
		if (field != lastSortField) {
			order = '';
		}
		var href = '/admin/1/?field=' + field;
		if(order) {
			order = 'DESC';
		} else {
			href += '&order=DESC';
		}
		if(id_region) {
			href += '&region=' + id_region;
		}

		location.href = href;
	}).css('cursor', 'pointer');
});