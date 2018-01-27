<?php
include_once('../../connectFiles/connect_ar.php');

$targetdir = '/Users/Ben/Sites/audioRecorder/uploads/';   
// name of the directory where the files should be stored
$rightNow = date('l jS \of F Y h:i:s A');
$targetFile = $targetdir.$rightNow."-test.webm";
echo $_POST['prompt'];


if (move_uploaded_file($_FILES['myBlob']['tmp_name'], $targetFile)) {
echo "uploaded";
} else { 
    echo "not uploaded";
}


$query = $elc_db->prepare("Insert into audio_files (prompt_id, owner, filename, filesize, filetype) Values (?,?,?,?,?)");
$query->bind_param("sssss", $_POST['prompt_id'], $_POST['owner'], $targetFile, $_FILES['myBlob']['size'], $_FILES['myBlob']['type']);
$query->execute();
$result = $query->get_result();


?>