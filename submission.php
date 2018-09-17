<?php
error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);
if(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'){
    $_SERVER['HTTPS']='on'; 
}
require_once 'ims-blti/blti.php';
$lti = new BLTI("secret", false, false);

$submission_id= $_GET['submission_id'];
// include_once("cas-go.php");
include_once('../../connectFiles/connect_ar.php');
$query = $elc_db->prepare("Select * from Audio_files inner join Prompts on Prompts.prompt_id=Audio_files.prompt_id where id=?");
$query->bind_param("s", $submission_id);
$query->execute();
$result = $query->get_result();
$result = $result->fetch_assoc();

echo "<div style='width: 20em; padding: 1em;border: 1px solid black; background-color: white;margin: 1em auto;font-family: LatoWeb,Helvetica Neue,Helvetica,Arial,sans-serif;'>";
echo "<p>Prompt: ".$result['text']."<br /><br />"; 
echo "You have {$result['prepare_time']} seconds to prepare and {$result['response_time']} seconds to respond.</p><br /><br />";

echo "<audio id='review' controls><source src='".$result['filename']."' type='".$result['filetype']."'></audio>";
if ($result['transcription'] !== NULL) {
    echo "<div>Transcription: {$result['transcription']}</div>";
}
echo "</div>";
?>