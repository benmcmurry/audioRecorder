<?php
include_once('../../connectFiles/connect_ar.php');
define ('SITE_ROOT', realpath(dirname(__FILE__)));

$targetdir = '/uploads/';     
// name of the directory where the files should be stored
$time = date('Y-m-d-His');
$targetFile = SITE_ROOT.$targetdir."prompt_".$_POST['prompt_id']."-".$_POST['owner']."-".$time.".webm";


if (move_uploaded_file($_FILES['myBlob']['tmp_name'], $targetFile)) {
echo "Your response has been saved.";
} else { 
    echo "There was an error. Please refresh and try again.";
}


$query = $elc_db->prepare("Insert into Audio_files (prompt_id, owner, filename, filesize, filetype, date_created) Values (?,?,?,?,?,now())");
$query->bind_param("sssss", $_POST['prompt_id'], $_POST['owner'], $targetFile, $_FILES['myBlob']['size'], $_FILES['myBlob']['type']);
$query->execute();
$result = $query->get_result();


?>