$( document ).ready(function() {

	var $tbody = $('#json-table').find('tbody');
	var jsonString = $('#json-string').text();
	var schoolsString = $('#schools-string').text();
	var json = JSON.parse('{"root": ' + jsonString + '}');
	var schools = JSON.parse(schoolsString);
	var universities = json.root;
	var region = $('#region').find('option:selected').val();
	var lastSortField;
	var sortCounter;
	var sorted = universities.slice();
	
	function updatePage() {
		$tbody.empty();
		$.each(sorted, function(index, university) {
			// td checkbox
			var $tr = $('<tr/>');
			var $td = $('<td/>');
			var $element = $('<input/>');
			$element.attr({
				'id': 'select_' + university.university_id,
				'type': 'checkbox',
				'name': 'id_university[' + university.university_id + ']',
				'form': 'university_select_form'
			}).addClass('university_select').appendTo($td);
			$td.appendTo($tr);
			// td id
			$('<td/>').html(schools[university.university_id]).appendTo($tr);
			// td name
			$('<td/>').html(university.university_name).appendTo($tr);
			// td action
			$td = $('<td/>');
			$element = $('<a/>');
			$element.attr({
				'title': 'Import from EDBO to local DB',
				'href' : '/import/import/' + university.university_id + '/?region=' + region
			}).addClass('fas fa-download pr-3 update').appendTo($td);
			$td.appendTo($tr);
			// td id_edbo
			$('<td/>').html(university.university_id).addClass('university_id').appendTo($tr);
			// td type
			$('<td/>').html(university.education_type_name).appendTo($tr);
			$tr.appendTo($tbody);
		});
		    
		var $university_select = $('.university_select');
		$university_select.change( function() {
			var checked = false;
			$university_select.each( function() {
				if ($(this).is( ':checked' )) {
					checked = true;
					return;
				}
			});
			if (checked) {
				$('.btn-import-selected').removeAttr('disabled');
				$('#select_form_submit_top, #select_form_submit_bottom').click( function() {
					$('#university_select_form')[0].submit();
				});
			} else {
				$('.btn-import-selected').prop('disabled', true);
				$('#select_form_submit_top, #select_form_submit_bottom').off('click');
			}
		});
	}
    $('#select_all_top, #select_all_bottom').change( function() {
        if ($(this).is( ':checked' )) {
            $('.university_select').prop('checked', true);
        } else {
            $('.university_select').prop('checked', false);
        }
    });
		
	$('#import_all_top, #import_all_bottom').click( function() {
		var region = $('#region').find('option:selected').val();
		var data = [];
		$('.university_id').each( function(index, university) {
			data[index] = { university_id: $(university).text() };
		});
		data = JSON.stringify(data);
		$.ajax({
			url: '/import/import?all=1&region=' + region,
			type: 'post',
			dataType: 'json',
			data: 'id_university=' + data,
			success: function() {
				alert('Import complete!');
			}
		});
	});

	$('#th-university_name, #th-university_id, #th-education_type_name').click( function() {
		var field = this.id.split('-')[1];
		if (field !== lastSortField) {
			$('#th-' + lastSortField).find('i').removeClass().addClass('none');
			sortCounter = 0;
		}
		sortCounter++;
		lastSortField = field;
		//sorted = universities.slice();
		sorted.sort(function(a, b){
			if (isNaN(parseInt(a[field], 10))) {
				var x = a[field].toLowerCase();
				var y = b[field].toLowerCase();
			} else {
				var x = parseInt(a[field], 10);
				var y = parseInt(b[field], 10);
			}
			if (x < y) {return -1;}
			if (x > y) {return 1;}
			return 0;
		});
		switch(sortCounter % 3) {
		case 1:
			$(this).find('i').removeClass().addClass('fas fa-long-arrow-alt-up');
		break;
		case 2:
			$(this).find('i').removeClass().addClass('fas fa-long-arrow-alt-down');
			sorted.reverse();
		break;
		default:
			$(this).find('i').removeClass().addClass('none');
			sorted = universities.slice();		
		}
		length = sorted.length;
		updatePage();
	}).css('cursor', 'pointer');
	
	updatePage();
});