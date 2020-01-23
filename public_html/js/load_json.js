$( document ).ready(function() {
    $('#jsonFile').change( function(e) {
        var file = e.target.files[0];        
        $('input#jsonFile').attr('file_selected', file.name);
        var $labelBrowse = $('label[for="jsonFile"]');
        var $upload = $('#jsonFileUpload');
        $labelBrowse.text(file.name);
        $labelBrowse.css('font-weight', 'normal');
        $upload.css('font-weight', 'bold')
        var reader = new FileReader;
        var json;
        var $dataBlock = $('#json');
        $dataBlock.empty();
        reader.readAsText(file); 
        reader.onloadend = function (evt) {
            json = JSON.parse(reader.result);
            $dataBlock.append('<p>' + json.universities[0][0] + '</p>'); // 62
            $dataBlock.append('<p>' + json.universities[0][1] + '</p>'); // ХНУ
            $dataBlock.append('<p>' + json.universities[0][2] + '</p>'); // 174
            $dataBlock.append('<p>' + json.universities[0][3] + '</p>'); // offers
            $dataBlock.append('<p>' + json.universities[0][4] + '</p>'); // Харків
        };
        $dataBlock.css('display', 'block');
    });
});