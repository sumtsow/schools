$(document).ready(function() {
    var $raw_data = $('#raw-data');
    var contentJson = JSON.parse($raw_data.text());
	$raw_data.remove();	
	var $header = $('#school-name');
	var $branch_tpl = $('.branch').first();
	var $specialty_tpl = $('.specialty').first();
	var $program_tpl = $('.program').first();
	$header.append(contentJson.universities.university['@attributes'].name);
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
        $branch_div.removeClass('d-none').addClass('d-block');
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
			$specialty_card.find('accordion').text(specialty['@text']);
			$specialty_card.find('.card-header').attr('id', specialty['@attributes'].id);
			$specialty_card.removeClass('d-none').addClass('d-block');
			$branch_div.find('.accordion').append($specialty_card);
			
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
				var card_text = $program_card.find('.card-text').find('span');
				$(card_text[0]).after(program['@attributes'].level_title);
				$(card_text[1]).after(program['@attributes'].form_title);
				$(card_text[2]).after(program['@attributes'].period);
				var scores = 'мін = ' + program['@attributes'].min_rate + '; сер = ' + program['@attributes'].ave_rate + '; макс = ' + program['@attributes'].max_rate;
				$(card_text[3]).after(scores);
				
				var subjects = [];
				if (!$.isArray(program.subjects.subject)) {
					subjects[0] = program.subjects.subject;
				} else {
					subjects = program.subjects.subject.slice();
				}
				i = 1;
				subjects.forEach( function(subject, index) {
					var text = subject['@text'];
					var required = (subject['@attributes'].required == 1);
					text += required ? ' - <span class="font-weight-bold">обов&apos;язковий</span> ' : ' ';
					text += '(Мін бал = ' + subject['@attributes'].rating +'; K' + i + ' = ' + subject['@attributes'].coefficient + ')<br/>';
					if (required) { i++; } 
					$program_card.find('p.subjects').append(text);
				});
				$specialty_card.find('.card-columns').append($program_card);
			});
		});
    });
});