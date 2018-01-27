$("document").ready(function() {
    'use strict';
    navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;
    $("#rec").on("click", startRecording);
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
    mediaRecorder.start(10);
    var url = window.URL || window.webkitURL;
    console.log(audioElement);
    mediaRecorder.ondataavailable = function(e) {
        chunks.push(e.data);
    }
    mediaRecorder.onstop = function() {
        var blob = new Blob(chunks, { type: "audio/webm", name: "myRecording.webm" });
        chunks = [];
        var audioURL = window.URL.createObjectURL(blob);
        audioElement.src = audioURL;
        console.log("url: " + audioURL);
        uploadRecording(blob);
    }
}

function startRecording() {
    // this sets a timer before the prepare timer starts
    (function() {
        setTimeout(function() {
            timer(prepare_time, "p")
            $("#timer_container").css({
                "display": "flex",
                "flex-direction": "row",
                "justify-content": "space-between",
                "align-items": "center"
            });
        }, 5000);

    })();
    // end timer waiter

    console.log("start recording");
    $("#rec").hide();
    $("#prompt").show().css({
        "display": "flex",
        "flex-direction": "column",
        "justify-content": "space-between",
        "align-items": "center"
    });

    //this is a time to wait before recording begins
    (function() {
        setTimeout(function() {
            navigator.getUserMedia({ "audio": true }, record, errorCallback);
            console.log("start recording");
            $("img#type").attr("src", "images/record.jpg");
            timer(response_time, "r");
        }, 5000 + prepare_time * 1000);

    })();

    (function() {
        setTimeout(function() {

            console.log("stop recording");
            $("#timer").hide();
            stopRecording();
        }, 5000 + prepare_time * 1000 + response_time * 1000);

    })();
}


function stopRecording() {
    console.log("stop recording");
    mediaRecorder.stop();
    $("#display-box").show();
    $("#timer_container").hide();
    audioElement.controls = true;
}

function timer(time, timerType) {
    if (timerType == "p") {
        timerType = "Prepare ";
        time = time + 1;
    } else {
        timerType = "Recording ";
        time = time + 2;
    }

    (function move() {
        if (time > 0) {
            time = time - 1;
            $("#timer").text(timerType + time + "s");
            setTimeout(move, 1000);
        }

    })();
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
        $("#display-box").html(phpfile);
    });
}