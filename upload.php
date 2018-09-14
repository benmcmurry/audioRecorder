<?php
include_once('../../connectFiles/connect_ar.php');
define ('SITE_ROOT', realpath(dirname(__FILE__)));

$targetdir = '/uploads/';     
// name of the directory where the files should be stored
$time = date('Y-m-d-His');
$fileName="prompt_".$_POST['prompt_id']."-".$_POST['netid']."-".$time.".ogg";
$targetFile = SITE_ROOT.$targetdir."prompt_".$_POST['prompt_id']."-".$_POST['netid']."-".$time.".ogg";
$fileLocation = "uploads/prompt_".$_POST['prompt_id']."-".$_POST['netid']."-".$time.".ogg";

if (move_uploaded_file($_FILES['myBlob']['tmp_name'], $targetFile)) {
echo "<p align='center'>Your response has been saved.</p>";
if ($_POST['transcription'] == 1) {
   
    echo "<p>Now, please transcribe what you recorded. You can refer back to the audio above.</p>";
    echo "<div id='transcription1' contenteditable='true' class='transcription'></div>";
    echo "<a align='center' class='button saveTranscription' id='saveTranscription' onClick='saveTranscription({$_POST['prompt_id']}, \"{$_POST['netid']}\", 1)'>Save Transcription</a>";
   
}

} else { 
    echo "There was an error. Please refresh and try again.";
}


$query = $elc_db->prepare("Insert into Audio_files (prompt_id, netid, filename, filesize, filetype, date_created) Values (?,?,?,?,?,now())");
$query->bind_param("sssss", $_POST['prompt_id'], $_POST['netid'], $fileLocation, $_FILES['myBlob']['size'], $_FILES['myBlob']['type']);
$query->execute();
$result = $query->get_result();
$last_id = $elc_db->insert_id;

// session_start();
$launchlti = "https://elc.byu.edu/audioRecorder/?submission_id=".$last_id;

if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
 error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
} else { 
 error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
}
require_once("ims-blti/OAuthBody.php");
$method="POST";
$oauth_consumer_secret = 'secret';
$sourcedid = $_SESSION['lis_result_sourcedid'];
$oauth_consumer_key = '12345';
$endpoint = $_SESSION['lis_outcome_service_url'];
$content_type = "application/xml";
if (get_magic_quotes_gpc()) $sourcedid = stripslashes($sourcedid);


$body = '<?xml version = "1.0" encoding = "UTF-8"?>  
<imsx_POXEnvelopeRequest xmlns = "http://www.imsglobal.org/lis/oms1p0/pox">      
    <imsx_POXHeader>         
        <imsx_POXRequestHeaderInfo>            
            <imsx_version>V1.0</imsx_version>  
            <imsx_messageIdentifier>MESSAGE</imsx_messageIdentifier>         
        </imsx_POXRequestHeaderInfo>      
    </imsx_POXHeader>      
    <imsx_POXBody>         
        <OPERATION>            
            <resultRecord>
                <sourcedGUID>
                    <sourcedId>SOURCEDID</sourcedId>
                </sourcedGUID>
                <result>
                    <resultScore>
                        <language>en-us</language>
                        <textString>GRADE</textString>
                    </resultScore>
                    <resultData>
                      <ltiLaunchUrl>LAUNCHLTI</ltiLaunchUrl>
                    </resultData>
                </result>
            </resultRecord>       
        </OPERATION>      
    </imsx_POXBody>   
</imsx_POXEnvelopeRequest>';

//set variables for response
    $operation = 'replaceResultRequest';
    $postBody = str_replace(
    array('SOURCEDID', 'GRADE', 'OPERATION','MESSAGE', 'LAUNCHLTI'), 
    array($sourcedid, '', $operation, uniqid(), $launchlti),
    $body);

//send response
if (isset($sourcedid)){
$response = sendOAuthBodyPOST($method, $endpoint, $oauth_consumer_key, $oauth_consumer_secret, $content_type, $postBody);
}
?>