$( document ).ready(function() {
	
	let $updateBtns = $('.update');
	let $schools = $('.school');
	let id_edbo = [];
	let id_program;
	let programs;
	$schools.each(function(index, school) {
		id_edbo[index] = $(school).attr('id_edbo');
	});
	
	$updateBtns.click( function(event) {
		event.preventDefault();
		event.stopPropagation();
		let id = $(this).attr('href').slice(1);
		$.ajax({
			url: '/secure/' + id + '/Universities.json',
			dataType: 'text',
			success: function(data) {
				let json = JSON.parse(data);
				let title_edbo = json.universities[0][1];
				let title_local = $('[id_edbo=' + id + ']').text();
				if(title_local != title_edbo) alert('Titles are difference');
				id_program = json.universities[0][3];
				console.log(json);
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.log(textStatus, errorThrown);
			}
		});
		$.ajax({
			
			url: '/secure/' + id + '/Offers.json',
			dataType: 'text',
			success: function(data) {
				let json = JSON.parse(data);
				console.log(json);
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.log(textStatus, errorThrown);
			}
		});		
		/*$.ajax({
			url: '/admin/update/' + id_school,
			method: 'post',
			data: data,
			dataType: 'json',
			success: function(data) {
				alert(data);
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.log(textStatus, errorThrown);
			}
		});*/
	});
});