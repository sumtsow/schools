$( document ).ready(function() {
	
	let $updateBtns = $('.update');
	let $jsonFileInput = $('input#jsonFile');
	let id_school;
	//let id_program;
	//let programs;

	$jsonFileInput.change(function() {
		return this.files[0];
	});

	$updateBtns.click( function(event) {
		event.stopPropagation();		
		event.preventDefault();
		$jsonFileInput.trigger('click');
		let file = $jsonFileInput.triggerHandler('change');
		if(file) readFile(file);
		id_school = $(this).parents('tr').first().find('.id_school').text();
	});
	
	function readFile(file) {
		let reader = new FileReader();
		reader.readAsText(file);		
		reader.onload = function() {
			let data = reader.result;
			$.ajax({
				url: '/admin/update/' + id_school,
				method: 'post',
				data: 'data=' + data,
				dataType: 'json',
				success: function(data) {
					console.log(data);
				},
				error: function(jqXHR, textStatus, errorThrown) {
					console.log(textStatus, errorThrown);
				}
			});
		}
	};
});