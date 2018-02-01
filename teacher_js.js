$(document).ready(function() {
    $("a#addPrompt").on("click", addPrompt);
    $("a.archive").on("click", archivePrompt);
});

function addPrompt() {

}

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