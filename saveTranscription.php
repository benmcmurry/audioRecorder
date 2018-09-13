<?php
include_once('../../connectFiles/connect_ar.php');
error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);


$query = $elc_db->prepare("Update Audio_files set transcription = ? where prompt_id = ? and netid=?");
$query->bind_param("sss", $_POST['savedTranscription'], $_POST['prompt_id'], $_POST['netid']);
$query->execute();
$result = $query->get_result();
echo "Transcript Saved!";

?>