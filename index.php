<?php
$prompt_id = $_GET['prompt_id'];
include_once("cas-go.php");
include_once('../../connectFiles/connect_ar.php');

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
        <link rel="stylesheet" href="style.css" />
        <script src="jquery.js"></script>

        <script type="text/javascript">
            var audioElement;
            var chunks = [];
            var prompt_id = <?php echo $prompt_id;?>;
            var netid = "<?php echo $netid; ?>";
            var prepare_time = <?php echo $result['prepare_time']; ?>;
            var response_time = <?php echo $result['response_time']; ?>;
            var testAudio;
            var testMicrophone = [];
        </script>
        <script src="js.js"></script>

    </head>

    <body>
        <div id='content-wrapper'>
        <div id="header">
                    <?php include_once("common_content/header.php");?>
                </div>
            <div id='content'>
                
                <div id='main'>
                    <div id='first_screen'>                    
                        <audio id='test' autoplay></audio>
                        <a class='button' id='test_record'>Test Microphone</a>
                        <a class='button' id='testing'>Please speak</a>
                        <a class='button' id='listening'>Please Listen</a>
                        <a class='button' id="rec">Begin</a>
                    </div>
                    <div id='prompt'>
                        <?php 
                       
                        echo "<p>".$result['text']."<br /><br />"; 
                        echo "You have {$result['prepare_time']} seconds to prepare and {$result['response_time']} seconds to respond.</p>";
                        ?>
                        <audio id='audioRecording' controls>
                        </audio>
                        <div id='timer_container'>
                            <img id='type' src='images/lightbulb.jpg' />
                            <div id='timer'></div>
                        </div>
                    <div id="display-box"></div>
                    
                    </div> <!-- end prompt div -->
                    
                </div> <!-- end main div -->
                
            </div> <!-- end content div -->
            <div id='footer'>
                <?php include_once("common_content/footer.php"); ?>
</div>
        </div> <!-- end prompt-wrapper div -->



    </html>