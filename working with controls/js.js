$("document").ready(function() {
    'use strict';
    navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;
    $("#rec").on("click", startRecording);
    $("#pauseRes").on("click", pauseResume);
    $("#stop").on("click", stopRecording);
    audioElement = document.querySelector("audio");
    audioElement.controls = false;
    console.log(audioElement);
    var mediaRecorder;
    chunks = [];
    var count = 0;

});

function record(stream) {
    audioElement.controls = false;
    mediaRecorder = new MediaRecorder(stream);
    $("#pauseRes").text("Pause");
    mediaRecorder.start(10);
    var url = window.URL || window.webkitURL;
    console.log(audioElement);
    // audioElement.src = url ? url.createObjectURL(stream) : stream;
    // audioElement.play();
    mediaRecorder.ondataavailable = function(e) {
        chunks.push(e.data);
    }
    mediaRecorder.onstop = function() {
        var blob = new Blob(chunks, { type: "audio/webm", name: "myRecording.webm" });
        chunks = [];
        var audioURL = window.URL.createObjectURL(blob);
        audioElement.src = audioURL;
        console.log("url: " + audioURL);
        downloadLink.href = audioURL;
        downloadLink.innerHTML = 'Download audio file';
        name = 'audiofile.webm';
        downloadLink.setAttribute("download", name);
        downloadLink.setAttribute("name", name);
        uploadRecording(blob);
    }
}

function startRecording() {
    console.log("start recording");
    navigator.getUserMedia({ "audio": true }, record, errorCallback);
    $("#rec").attr("disabled", true);
    $("#pauseRes").attr("disabled", false);
    $("#stop").attr("disabled", false);
}

function pauseResume() {
    console.log("Pause/Resume");
    if ($("#pauseRes").text() == "Pause") {
        $("#pauseRes").text('Resume');
        mediaRecorder.pause();
        $("#stop").attr("disabled", true);
    } else {
        $("#pauseRes").text('Pause');
        mediaRecorder.resume();
        $("#stop").attr("disabled", false);
    }
}

function stopRecording() {
    console.log("stop recording");
    mediaRecorder.stop();
    audioElement.controls = true;
    $("#rec").attr("disabled", false);
    $("#pauseRes").attr("disabled", true);
    $("#stop").attr("disabled", true);

}

function errorCallback(error) {
    console.log('navigator.getUserMedia error: ', error);
}

function uploadRecording(blob) {
    console.log(blob);
    var fd = new FormData();
    fd.append('myBlob', blob);
    fd.append('prompt_id', prompt_id);
    fd.append('owner', netid);
    $.ajax({
        type: 'POST',
        url: 'upload.php',
        data: fd,
        processData: false,
        contentType: false
    }).done(function(phpfile) {
        $("#display_box").html(phpfile);
    });

}