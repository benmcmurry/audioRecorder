<?php
error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);
if(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'){
    $_SERVER['HTTPS']='on'; 
}
require_once 'ims-blti/blti.php';
$lti = new BLTI("secret", false, false);

$prompt_id = $_GET['prompt_id'];
$instructor = strpos($_POST['roles'], "Instructor");
$lis_outcome_service_url = $_POST['lis_outcome_service_url'];
$lis_result_sourcedid = $_POST['lis_result_sourcedid'];
session_start();
$_SESSION['prompt_id'] = $prompt_id;
$_SESSION['lis_outcome_service_url'] = $lis_outcome_service_url;
$_SESSION['lis_result_sourcedid'] = $lis_result_sourcedid;
include_once("cas-go.php");
include_once('../../connectFiles/connect_ar.php');
include_once('addUser.php');

$query = $elc_db->prepare("Select * from Prompts where prompt_id=?");
$query->bind_param("s", $prompt_id);
$query->execute();
$result = $query->get_result();
$result = $result->fetch_assoc();




?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="mobile-web-app-capable" content="yes">
        <meta id="theme-color" name="theme-color" content="#fff">
        <title>ELC Audio Recorder</title>
        <link rel="stylesheet" href="style.css?=time()" />
        <!-- <link rel="stylesheet" href="style-canvas.css?=time()" /> -->

        <?php
        if ($lti->valid) { 
            if ($instructor !== FALSE) {
                echo "<style>div#content-wrapper {height: 40em;} div#content.editor {max-width: 100%;} </style>";
            } 
            echo  "<link rel='stylesheet' href='style-canvas.css?=time()' />";
            
            }
        ?>
        <script src="jquery.js"></script>

        <script type="text/javascript">
            var audioElement;
            var review;
            var chunks = [];
            var prompt_id = <?php echo $prompt_id;?>;
            var netid = "<?php echo $netid; ?>";
            var transcription = <?php echo $result['transcription']; ?>;
            var prepare_time = <?php echo $result['prepare_time']; ?>;
            var response_time = <?php echo $result['response_time']; ?>;
            var testAudio;
            var testMicrophone = [];
        </script>
        <script src="js.js"></script>
       

    </head>
    <?php
        if (isset($_GET['submission_id'])) {
            include_once("submission.php");
        } else { ?>
    <body>
        
        <div id='content-wrapper'>
        
        <div id="header">
            <?php include_once("common_content/header.php");?>
        </div>
        <?php
        //for lti info

        //    foreach($_POST as $key => $value) {
        //     print "$key=$value\n";
       // }

?>
        <?php
        if ($instructor !== FALSE) {
            include_once("responses.php");
            echo "<script type='text/javascript'> $('#header, #footer').hide();</script>";
        //    echo "You can see student responses in speedgrader OR visit http://elc.byu.edu/audioRecorder/responses.php?prompt_id=$prompt_id.";
            
        } else {
            ?>

            <div id='content'>
            <?php
                    
            $query2 = $elc_db->prepare("Select * from Audio_files where prompt_id=? and netid=?");
            $query2->bind_param("ss", $prompt_id, $netid);
            $query2->execute();
            $result2 = $query2->get_result();
            $result2 = $result2->fetch_assoc();
            if (isset($result2)) {
                $alreadyDone = TRUE;
                echo "<div id='mainReview'>";
                echo "<div id='mainReviewContent'><p>You have already answered this prompt.</p><p> You can play your answer below.</p>";
                echo "<audio class='audioBox' id='review' controls><source src='".$result2['filename']."' type='".$result2['filetype']."'></audio>";
                if ($result['transcription'] == 1) {
                    echo "<p>You can update your transcription here.</p>";
                    echo "<div id='transcription2' contenteditable='true' class='transcription'>{$result2['transcription_text']}</div>";
                    echo "<a align='center' class='button saveTranscription' id='saveTranscription2' onClick='saveTranscription($prompt_id , \"$netid\", 0)'>Save Transcription</a>";
                }
                echo "<div id='placeholder'></div>";
                
                echo "<div id='warningPrompt'>Please enter the password to allow the student to re-record. Please be aware that any previous recordings will be deleted.";
                echo "<br /><input class='repeatPassword' id='".$result2['prompt_id']."-".$netid."' style='font-size: 1em;margin: .2em;' type='password' width='5em'></input>";
                echo "</div>"; // End warning Prompt/
                echo "</div>"; //end mainReviewContent
                echo "</div>"; //end mainReview
            } else {$alreadyDone=FALSE;}
        ?>
                <div id='main' <?php if ($alreadyDone) {echo "style='display: none'}";} ?>>
                    <div id='first_screen'>                    
                        <audio id='test' autoplay></audio>
                        <a class='button' id='test_record'>Test Microphone</a>
                        <a class='button' id='testing'>Please speak</a>
                        <a class='button' id='listening'>Please Listen</a>
                        <a class='button' id="rec">Begin</a>
                        <div id='volume'>
                            <div class='volbox' id='volbox-1'></div>
                            <div class='volbox' id='volbox-2'></div>
                            <div class='volbox' id='volbox-3'></div>
                            <div class='volbox' id='volbox-4'></div>
                            <div class='volbox' id='volbox-5'></div>
                            <div class='volbox' id='volbox-6'></div>
                            <div class='volbox' id='volbox-7'></div>
                            <div class='volbox' id='volbox-8'></div>
                            <div class='volbox' id='volbox-9'></div>
                            <div class='volbox' id='volbox-10'></div>
                        </div>
                    </div>
                    <div id='prompt'>
                        <?php 
                       
                        echo "<p>".$result['text']."<br /> <br />"; 
                        echo "You have {$result['prepare_time']} seconds to prepare and {$result['response_time']} seconds to respond.</p>";
                        ?>
                        <div class='audioBox'><audio id='audioRecording' controls>
                        </audio></div>
                        <div id='timer_container'>
                            <img id='type' src='images/lightbulb.jpg' />
                            <div id='timer'></div>
                        </div>
                    <div id="display-box"></div>
                    
                    </div> <!-- end prompt div -->
                    
                </div> <!-- end main div -->
                    
                
            </div> <!-- end content div -->
                <?php } ?>
            <div id='footer'>
                <?php include_once("common_content/footer.php"); ?>
</div>
        </div> <!-- end prompt-wrapper div -->



    </html>

<?php

        }
        ?>