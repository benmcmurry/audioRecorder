<?php
$prompt_id = $_POST['prompt_id'];
include_once("cas-go.php");
include_once('../../connectFiles/connect_ar.php');

$query = $elc_db->prepare("Update Prompts set archive=1 where prompt_id=?");
$query->bind_param("s", $prompt_id);
$query->execute();


?>