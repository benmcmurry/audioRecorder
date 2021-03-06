$(document).ready(function() {
    $("a.archive").on("click", archivePrompt);
    $("a#save").on("click", savePrompt);
    $(".title").on("click", function(){
        $(this).next().slideToggle();
    });
    
});


function archivePrompt() {
    prompt_id = $(this).attr('data-promptID');

    console.log(prompt_id);
    $.ajax({
        type: 'POST',
        url: 'archive.php',
        data: { prompt_id: prompt_id },
    }).done(function(phpfile) {
        $("#" + prompt_id).slideToggle();
    });
}

function savePrompt() {

    title = $("#title").text();
    text = $("#text").text();
    prepare_time = $("#prepare_time").text();
    response_time = $("#response_time").text();
    transcription = $('input[name=transcriptionReq]:checked').val();

    console.log(title);
    $.ajax({
        type: 'POST',
        url: 'savePrompt.php',
        data: { prompt_id: prompt_id, title: title, text: text, prepare_time: prepare_time, response_time: response_time, transcription: transcription },
    }).done(function(phpfile) {
        $("#display_box").html(phpfile);
    });
}