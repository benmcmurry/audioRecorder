<?php
$prompt_id = $_GET['prompt_id'];
include_once("cas-go.php");
include_once('../../connectFiles/connect_ar.php');
$prompt_id = $_POST['prompt_id'];
$title = $_POST['title'];
$text = $_POST['text'];
$prepare_time = $_POST['prepare_time'];
$response_time = $_POST['response_time'];

$query = $elc_db->prepare("Update Prompts set title = ?, text = ?, prepare_time = ?, response_time = ? where prompt_id = ?");
$query->bind_param("sssss", $title, $text, $prepare_time, $response_time, $prompt_id);
$query->execute();
$result = $query->get_result();
echo "Saved!"





?>