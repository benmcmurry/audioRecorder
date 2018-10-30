$("document").ready(function() {
    
    $("a.archive").on("click", archivePrompt);
    $("a#save").on("click", savePrompt);
    'use strict';
    navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;

    if ($("#review").is(":visible")) {
        $("#breakIn").css("display", "inline-block");
        review = document.querySelector("#review");
        fixPlayback(review);

        $("body").keydown(function(e) {
            if (e.keyCode == 191 && e.metaKey) {
            $("#warningPrompt").toggle();
            $(".repeatPassword").focus();
            }
        });
        $(".repeatPassword").on("keydown", function(e) {
            if (e.keyCode == 13 && $(".repeatPassword").val() == "repeat") {
                e.preventDefault();
                var data = this.id.split("-");
                $("#placeholder").load("removeDBentry.php?prompt_id=" + data[0] + "&netid=" + data[1]);
                console.log("removeDBentry.php?prompt_id=" + data[0] + "&netid=" + data[1]);
                $("#main,#mainReview, #warningPrompt, #breakIn").toggle();

                console.log(data[0]);
                console.log(data[1]);

            }
        });

    }
    
    $("#rec").on("click", startRecording);
    $("#stop").on("click", stopRecording);
    audioElement = document.querySelector("#audioRecording");
    audioElement.controls = false;
    testAudio = document.querySelector("#test");
    $("#test_record").on("click", testStartRecording);

    var mediaRecorder;
    chunks = [];
    var count = 0;

    bars = [];
    recordingStatus = false;
    samples = 0;


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
    visualizationOfSound(stream);
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
            timer(prepare_time, "Prepare ")
            $("#timer_container").css({
                "display": "flex",
                "flex-direction": "row",
                "justify-content": "space-between",
                "align-items": "center"
            });
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
            recordingStatus = true;
        }, prepare_time * 1000 + 1000);

    })();

    (function() {
        setTimeout(function() {


            $("#timer").hide();
            stopRecording();
        }, prepare_time * 1000 + response_time * 1000 + 2000);

    })();
}


function stopRecording() {
    recordingStatus = false;
    console.log("stop recording");
    mediaRecorder.stop();

    $("#display-box").show();
    $("#timer_container").hide();
    audioElement.controls = true;

}

function timer(time, timerType) {

    (function move() {
        if (time > -1) {

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
    fd.append('transcription', transcription);
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
    // these next lines are for visualization
    
    visualizationOfSound(stream);
    
    mediaRecorder = new MediaRecorder(stream);
    mediaRecorder.start();
    
    
    var url = window.URL || window.webkitURL;
    console.log(testAudio);
    // testAudio.src = url ? url.createObjectURL(stream) : stream;
    // testAudio.play();
    mediaRecorder.ondataavailable = function(e) {
        testMicrophone.push(e.data);

    }
    mediaRecorder.onstop = function() {
        var blob = new Blob(testMicrophone, {
            type: "audio/ogg",
            name: "myRecording.ogg"
        });
        testMicrophone = [];
        var audioURL = window.URL.createObjectURL(blob);
        testAudio.src = audioURL;
        console.log("url: " + audioURL);
    }
}

function testStartRecording() {
    // this sets a timer before the prepare timer starts
    recordingStatus = true;

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

function visualizationOfSound(stream) {
    audioContext = new AudioContext();
    analyser = audioContext.createAnalyser();
    scriptProcessor = audioContext.createScriptProcessor(2048, 1, 1);
    analyser.smoothingTimeConstant = 0.3;
    analyser.fftSize = 1024;
    input = audioContext.createMediaStreamSource(stream);
    input.connect(analyser);
    analyser.connect(scriptProcessor);
    scriptProcessor.connect(audioContext.destination);
    scriptProcessor.onaudioprocess = processInput;
}
function processInput () {
    if (recordingStatus){
    array = new Uint8Array(analyser.frequencyBinCount);

      analyser.getByteFrequencyData(array);
      length = array.length;
      let values = 0;  
      let i = 0;
      for (; i < length; i++) {
          values +=array[i];
          }
          volume = (values / length) *3 + 10;
         
          $(".volbox").css("background-color", "white");
       
        
          if(volume > 320 ) {$(".volbox-20").css("background-color", "red");}
          if(volume > 304 ) {$(".volbox-19").css("background-color", "red");}
          if(volume > 288 ) {$(".volbox-18").css("background-color", "yellow");}
          if(volume > 272 ) {$(".volbox-17").css("background-color", "yellow");}
          if(volume > 256 ) {$(".volbox-16").css("background-color", "yellow");}
          if(volume > 240) {$(".volbox-15").css("background-color", "yellow");}
          if(volume > 224 ) {$(".volbox-14").css("background-color", "yellow");}
          if(volume > 208 ) {$(".volbox-13").css("background-color", "rgb(129, 245, 129)");}
          if(volume > 192 ) {$(".volbox-12").css("background-color", "rgb(129, 245, 129)");}
          if(volume > 176 ) {$(".volbox-11").css("background-color", "rgb(129, 245, 129)");}
          if(volume > 160 ) {$(".volbox-10").css("background-color", "rgb(129, 245, 129)");}
          if(volume > 144 ) {$(".volbox-9").css("background-color", "rgb(129, 245, 129)");}
          if(volume > 128 ) {$(".volbox-8").css("background-color", "rgb(129, 245, 129)");}
          if(volume > 112 ) {$(".volbox-7").css("background-color", "rgb(129, 245, 129)");}
          if(volume > 96 ) {$(".volbox-6").css("background-color", "rgb(129, 245, 129)");}
          if(volume > 80 ) {$(".volbox-5").css("background-color", "rgb(129, 245, 129)");}
          if(volume > 64 ) {$(".volbox-4").css("background-color", "rgb(129, 245, 129)");}
          if(volume > 48 ) {$(".volbox-3").css("background-color", "rgb(129, 245, 129)");}
          if(volume > 32 ) {$(".volbox-2").css("background-color", "rgb(129, 245, 129)");}
          if(volume > 16 ) {$(".volbox-1").css("background-color", "rgb(129, 245, 129)");}

          
        //   if (volume < 250) {$("#volume").css("background-color", "rgb(129, 245, 129)");}
        //   if (volume > 249) { $("#volume").css("background-color", "yellow");}
        //   if (volume > 320) {volume = 320; $("#volume").css("background-color", "red");}
        //   console.log (volume);
        //   $("#volume").css("width", volume);

    
        }
}

function testStopRecording() {
    recordingStatus = false;
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


//teacher scripts
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
    console.log(title);
    $.ajax({
        type: 'POST',
        url: 'savePrompt.php',
        data: { prompt_id: prompt_id, title: title, text: text, prepare_time: prepare_time, response_time: response_time, transcription: transcription },
    }).done(function(phpfile) {
        $("#display_box").html(phpfile);
    });
}

function saveTranscription(prompt_id, netid, attempt){
    
    if (attempt == 1) {
    savedTranscription = $("#transcription1").text();
    } else {
        savedTranscription = $("#transcription2").text();
    }
    console.log(savedTranscription);
    $.ajax({
        type: 'POST',
        url: 'saveTranscription.php',
        data: {prompt_id: prompt_id, savedTranscription: savedTranscription, netid: netid}
    }).done(function(phpfile){
        $(".saveStatus").html(phpfile);
        
    });
    
}
