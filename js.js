$("document").ready(function() {
    'use strict';
    navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;

    if ($("#review").is(":visible")) {
        review = document.querySelector("#review");
        fixPlayback(review);

        $("#breakIn").on("click", function() {
            $("#warningPrompt").show();
            $(".repeatPassword").focus();

        });
        $(".repeatPassword").on("keydown", function(e) {
            if (e.keyCode == 13 && $(".repeatPassword").val() == "repeat") {
                e.preventDefault();
                $("#main,#mainReview, #warningPrompt").toggle();
                var data = this.id.split("-");
                console.log(data[0]);
                console.log(data[1]);
                $("#placeholder").load("removeDBentry.php?prompt_id=" + data[0] + "&netid=" + data[1]);

            }
        });

    } else {

        $("#rec").on("click", startRecording);
        $("#stop").on("click", stopRecording);
        audioElement = document.querySelector("#audioRecording");
        audioElement.controls = false;
        testAudio = document.querySelector("#test");
        $("#test_record").on("click", testStartRecording);

        var mediaRecorder;
        chunks = [];
        var count = 0;
    }



});

function fixPlayback(audioInQuestion) {
    audioInQuestion.onloadedmetadata = function() {
        console.log(' duration: ' + audioInQuestion.duration);
        if (audioInQuestion.duration === Infinity) {
            audioInQuestion.currentTime = 1e101;
            audioInQuestion.ontimeupdate = function() {
                this.ontimeupdate = () => { return; }
                console.log('after workaround: ' + audioInQuestion.duration);
                audioInQuestion.currentTime = 0.0001;
            }
        }
    }
}

function record(stream) {
    audioElement.controls = false;
    mediaRecorder = new MediaRecorder(stream);
    mediaRecorder.start(10);
    var url = window.URL || window.webkitURL;
    mediaRecorder.ondataavailable = function(e) {
        chunks.push(e.data);
    }
    mediaRecorder.onstop = function() {
        var blob = new Blob(chunks, { 'type': 'audio/ogg; codecs=opus' });
        // chunks = [];
        var audioURL = window.URL.createObjectURL(new Blob(chunks));
        audioElement.src = audioURL;

        fixPlayback(audioElement);

        console.log("url: " + audioURL);
        uploadRecording(blob);
    }
}

function startRecording() {
    // this sets a timer before the prepare timer starts
    (function() {
        setTimeout(function() {
            timer(prepare_time, "Prepare ")
            $("#timer_container").css({
                "display": "flex",
                "flex-direction": "row",
                "justify-content": "space-between",
                "align-items": "center"
            });
        }, 5000);

    })();
    // end timer waiter


    $("#first_screen").hide();
    $("#prompt").show().css({
        "display": "flex",
        "flex-direction": "column",
        "justify-content": "space-between",
        "align-items": "space-between"
    });

    //this is a time to wait before recording begins
    (function() {
        setTimeout(function() {
            navigator.getUserMedia({ "audio": true }, record, errorCallback);
            console.log("start recording");
            $("img#type").attr("src", "images/record.jpg");
            timer(response_time, "Recording ");
        }, 5000 + prepare_time * 1000);

    })();

    (function() {
        setTimeout(function() {


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

    (function move() {
        if (time > 0) {

            $("#timer").text(timerType + time + "s");
            setTimeout(move, 1000);
            time = time - 1;
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
    fd.append('netid', netid);
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

// Microphone test

function testRecord(stream) {
    // testAudio.controls = true;
    mediaRecorder = new MediaRecorder(stream);
    mediaRecorder.start(10);
    var url = window.URL || window.webkitURL;
    console.log(testAudio);
    // testAudio.src = url ? url.createObjectURL(stream) : stream;
    // testAudio.play();
    mediaRecorder.ondataavailable = function(e) {
        testMicrophone.push(e.data);
    }
    mediaRecorder.onstop = function() {
        var blob = new Blob(testMicrophone, {
            type: "audio/webm",
            name: "myRecording.webm"
        });
        testMicrophone = [];
        var audioURL = window.URL.createObjectURL(blob);
        testAudio.src = audioURL;
        console.log("url: " + audioURL);
    }
}

function testStartRecording() {
    // this sets a timer before the prepare timer starts


    $("#test_record").hide();
    $("#testing").show();

    //this is a time to wait before recording begins
    navigator.getUserMedia({
        "audio": true
    }, testRecord, errorCallback);
    console.log("start recording");


    (function() {
        setTimeout(function() {

            console.log("stop recording");
            testStopRecording();
        }, 5000);

    })();
}


function testStopRecording() {
    $("#testing").hide();
    $("#listening").show();
    mediaRecorder.stop();
    testAudio.controls = false;

    (function() {
        setTimeout(function() {

            $("#listening").hide();
            $("#test_record").text("Re-test the Microphone").show();

        }, 5000);

    })();
}