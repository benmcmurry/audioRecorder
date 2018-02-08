<?php

include_once("cas-go.php");
include_once('../../connectFiles/connect_ar.php');

$query = $elc_db->prepare("Insert into Prompts (prompt_id) values (null)");
$query->execute();
$result = $query->get_result();
$prompt_id = $query->insert_id;
header('Location: responses.php?prompt_id='.$prompt_id);
?>