<?php
$prompt_id = $_GET['prompt_id'];
include_once("cas-go.php");
include_once('../../connectFiles/connect_ar.php');

if ($_SERVER['SERVER_NAME'] == 'localhost') {$server = $_SERVER['SERVER_NAME']."/~Ben";} else {$server=" https://".$_SERVER['SERVER_NAME'];}

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
        <script src="teacher_js.js"></script>

    </head>

    <body>
        <div id='content-wrapper'>
            <div id="header">
                <?php include_once("common_content/header.php");?>
            </div>
            <div id='content'>
            <?php 
            echo $name;
//             foreach (phpCAS::getAttributes() as $key => $value) {
//     if (is_array($value)) {
//         echo '<li>', $key, ':<ol>';
//         foreach ($value as $item) {
//             echo '<li><strong>', $item, '</strong></li>';
//         }
//         echo '</ol></li>';
//     } else {
//         echo '<li>', $key, ': <strong>', $value, '</strong></li>' . PHP_EOL;
//     }
// }
    ?>
            <div id='newPrompt'>
                    
                    <a id='addPrompt' title='Create New Prompt'>+</a>
                    
                
                </div>
                
                <div id='promptList'>
                    <h3>Prompts</h3>
                    <?php
                    
                        $query = $elc_db->prepare("Select * from Prompts where owner=? and archive=0 order by date_created DESC");
                        $query->bind_param("s", $netid);
                        $query->execute();
                        $result = $query->get_result();
                        while ($row = $result->fetch_assoc()) {
                            echo "<div class='prompt' id='".$row['prompt_id']."'>";
                            echo "<div class='title'>Title: <strong>".$row['title']."</strong> (last modified: ".$row['date_created'].")</div>";
                            echo "<div class='link'>Link: ".$server."/audioRecorder/?prompt_id=".$row['prompt_id']."</div>";
                            echo "<div class='action_list'><a class='archive' data-promptId='".$row['prompt_id']."'>Archive</a><a class='edit' href='editor.php?prompt_id=".$row['prompt_id']."'>Edit</a><a class='responses' href='responses.php?prompt_id=".$row['prompt_id']."'>View Responses</a></div>";
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