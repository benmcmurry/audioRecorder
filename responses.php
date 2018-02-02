<?php
$prompt_id = $_GET['prompt_id'];
include_once("cas-go.php");
include_once('../../connectFiles/connect_ar.php');
include_once('addUser.php');



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

        <script type="text/javascript"></script>
        </script>
        </head>

    <body>
        <div id='content-wrapper'>
            <div id="header">
                <?php include_once("common_content/header.php");?>
            </div>
            <div id='content'>
           
                
                <div id='promptList'>
                    <h3>Prompts</h3>
                    <?php
                    
                        $query = $elc_db->prepare("Select * from Audio_files natural join Users where prompt_id=? order by date_created DESC");
                        $query->bind_param("s", $prompt_id);
                        $query->execute();
                        $result = $query->get_result();
                        while ($row = $result->fetch_assoc()) {
                            echo "<div class='prompt' id='".$row['prompt_id']."'>";
                            echo "<div class='title'>".$row['name']."</div>";
                            echo "<audio controls><source src='".$row['filename']."' type='".$row['filetype']."'></audio>";
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