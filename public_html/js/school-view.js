$(document).ready(function() {
    var $raw_data = $('#raw-data');
    var contentJson = JSON.parse($raw_data.text());
	$raw_data.remove();	
	var $header = $('#school-name');
	var $branch_tpl = $('.branch').first();
	var $specialty_tpl = $('.specialty').first();
	var $program_tpl = $('.program').first();
	var $required_tpl = $program_tpl.find('.required');
	var required_label = $required_tpl.html();
	var $min_tpl = $program_tpl.find('.min');
	var min_label = $min_tpl.html();
	$required_tpl.remove();
    $min_tpl.remove();
	var attributes = contentJson.universities.university['@attributes'];
	$header.append(attributes.name);
	$header.find('img').attr('src', contentJson.universities.university['@attributes'].logo);
	
	var branches = [];
	if (!$.isArray(contentJson.universities.university.branches.branch)) {
		branches[0] = contentJson.universities.university.branches.branch;
	} else {
		branches = contentJson.universities.university.branches.branch.slice();
	}
    contentJson.universities.university.branches.branch.forEach( function(branch) {
        var $branch_div = $branch_tpl.clone();
		var $branch_title = $branch_div.find('.branch-title');
        $branch_title.text($branch_title.text() + branch['@attributes'].code + ' ' + branch['@text']);
        $branch_div.removeClass('d-none').addClass('d-block').attr('id', branch['@attributes'].id);
        $('#branches').append($branch_div);
		
		var specialties = [];
		if(!$.isArray(branch.specialties.specialty)) {
			specialties[0] = branch.specialties.specialty;
		} else {
			specialties = branch.specialties.specialty.slice();
		}
		specialties.forEach( function(specialty) {
			var $specialty_card = $specialty_tpl.clone();
			var label = 'collapse_' + specialty['@attributes'].id;
			$specialty_card.attr('id', specialty['@attributes'].id)
			$specialty_card.find('button').append(specialty['@attributes'].code + ' ' + specialty['@text']).attr({
				'data-target': '#' + label,
				'aria-controls': label,
			});
			$specialty_card.find('.collapse').attr({
				'id': label,
				'aria-labelledby': specialty['@attributes'].id,
				'data-parent': '#' + specialty['@attributes'].id,
			});
			$specialty_card.find('accordion').text(specialty['@text']).attr('id', specialty['@attributes'].id);
			//$specialty_card.find('.card-header');
			$specialty_card.removeClass('d-none').addClass('d-block');
			$branch_div.append($specialty_card);
			
			var programs = [];
			if (!$.isArray(specialty.programs.program)) {
				programs[0] = specialty.programs.program;
			} else {
				programs = specialty.programs.program.slice();
			}
			programs.forEach( function(program) {
				var $program_card = $program_tpl.clone();
				$program_card.removeClass('d-none').addClass('d-inline-block');
				$program_card.find('.card-title').append(program['@text']);
				var card_text = $program_card.find('.program-param').find('span');
				$(card_text[0]).after(program['@attributes'].level_title);
				$(card_text[1]).after(program['@attributes'].form_title);
				$(card_text[2]).after(program['@attributes'].period);
				$(card_text[4]).append(program['@attributes'].min_rate);
				$(card_text[5]).append(program['@attributes'].ave_rate);
                $(card_text[6]).append(program['@attributes'].max_rate);
				
				var subjects = [];
				if (!$.isArray(program.subjects.subject)) {
					subjects[0] = program.subjects.subject;
				} else {
					subjects = program.subjects.subject.slice();
				}
				var i = 1;
				subjects.forEach( function(subject, index) {
					var text = subject['@text'];
					var required = (subject['@attributes'].required == 1);
					text += required ? required_label : ' ';
					text += min_label + subject['@attributes'].rating +'; K' + i + '&nbsp;=&nbsp;' + parseFloat(subject['@attributes'].coefficient) + ')<br/>';
					if (required) { i++; } 
					$program_card.find('.subjects').append(text);
				});
				$specialty_card.find('.card-columns').append($program_card);
			});
		});
    });
	$('#address').after(attributes.address);
	$('#phone').after(attributes.phone);
	var email = attributes.email;
	if(email.search(',')) {
		email = email.split(',');
		var mails = '';
		email.forEach( function(addr, index) {
			mails += '<a id="email' + index + '" target="_blank" href="' + 'mailto://' + addr + '">' + addr + '</a>\t';
		});
		$('#email').replaceWith(mails);
	} else {
		$('#email').html(email).attr('href', 'mailto://' + email);
	}
	$('#http').html(attributes.http).attr('href', attributes.http);
	attributes.info ? $('#info').after(attributes.info) : $('#info').parents('tr').remove();
	attributes.high ? $('#region').after(attributes.region) : $('#area').after(attributes.area);
	attributes.map ? $('#map').after(attributes.map) : $('#map').parents('tr').remove();
});