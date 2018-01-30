<?php
$prompt_id = $_GET['prompt_id'];
include_once("cas-go.php");
include_once('../../connectFiles/connect_ar.php');




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
        <title>ELC Audio Recorder</title>
        <link rel="stylesheet" href="style.css" />
        <script src="jquery.js"></script>

        <script type="text/javascript">
        </script>
        <script src="js.js"></script>

    </head>

    <body>
        <div id='content-wrapper'>
            <div id="header">
                <?php include_once("common_content/header.php");?>
            </div>
            <div id='content'>
                <div id='newPrompt'>
                    <div class='prompt'>
                        <a id='addPrompt'>+</a>
                        <p>New Prompt</p>
                    </div>
                </div>
                <div id='promptList'>
                    <?php
                        $query = $elc_db->prepare("Select * from Prompts where owner=?");
                        $query->bind_param("s", $netid);
                        $query->execute();
                        $result = $query->get_result();
                        while ($row = $result->fetch_assoc()) {
                            echo "<div class='prompt'>";
                            echo "<div class='title'>".$row['title']."</div>";
                            echo "<div class='link'> https:".$_SERVER['SERVER_NAME']."?prompt_id=".$row['prompt_id']."</div>";
                            echo "</div>";
                        }
                        
                    ?>

                </div>

            </div>
            <!-- end content div -->
            <div id='footer'>
                <?php include_once("common_content/footer.php"); ?>
            </div>
        </div>
        <!-- end prompt-wrapper div -->



    </html>