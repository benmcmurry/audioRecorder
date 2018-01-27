<?php
include_once('../../connectFiles/connect_ar.php');
$prompt_id = $_GET['prompt_id'];
$netid = "blm39";

$query = $elc_db->prepare("Select * from prompts where prompt_id=?");
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
    <title>Document</title>
    <link rel="stylesheet" href="style.css" />
    <script src="jquery.js"></script>
    
    <script type="text/javascript">
    var audioElement;
    var chunks = [];
    var prompt_id = <?php echo $prompt_id;?>;
    var netid = "<?php echo $netid; ?>";
    var prepare_time = <?php echo $result['prepare_time']; ?>;
    var response_time = <?php echo $result['prepare_time']; ?>;

    </script>
    <script src="js.js"></script>

</head>
<body>
    <p><?php echo $result['text']; ?>
    <div>
<audio controls autoplay></audio><br>
<button id="rec">Record</button>
<button id="pauseRes" >Pause</button>
<button id="stop" >Stop</button>
</div>
<a id="downloadLink" download="mediarecorder.webm" name="mediarecorder.webm" href></a>

<div id="display_box"></div>
</html>