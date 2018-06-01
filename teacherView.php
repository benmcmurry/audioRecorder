
<div id='content' class='editor' width='100%'>


    <div id='editorWrapper'>
        <div id='promptInfo'>
            <h3>Prompt Info</h3>
            <?php
                        $promptQuery = $elc_db->prepare("Select * from Prompts where prompt_id=?");
                        $promptQuery->bind_param("s", $prompt_id);
                        $promptQuery->execute();
                        $promptResult = $promptQuery->get_result();
                        $promptRow = $promptResult->fetch_assoc();
                        echo "<h4>Title</h4><div id='title' contenteditable='true' class='editable'>".$promptRow['title']."</div>";
                        echo "<h4>Prompt</h4><div id='text' contenteditable='true' class='editable'>".$promptRow['text']."</div>";
                        echo "<h4>Preparation Time</h4><div id='prepare_time' contenteditable='true' class='editable'>".$promptRow['prepare_time']."</div>";
                        echo "<h4>Response Time</h4><div id='response_time' contenteditable='true' class='editable'>".$promptRow['response_time']."</div>";
                        
            ?>
                <a class='button' id='save'>Save</a>
                <div id='display_box'></div>
        </div>
        <div id='responses'>
            <h3>Responses</h3>

            <?php
                    
                        $query = $elc_db->prepare("Select * from Audio_files natural join Users where prompt_id=? order by date_created DESC");
                        $query->bind_param("s", $prompt_id);
                        $query->execute();
                        $result = $query->get_result();
                        while ($row = $result->fetch_assoc()) {
                            echo "<div class='response' id='".$row['prompt_id']."'>";
                            echo "<div class='title'>".$row['name']." : ".$row['date_created']."</div>";
                            echo "<audio controls><source src='".$row['filename']."' type='".$row['filetype']."'></audio>";
                            echo "</div>";
                        }
                        
                    ?>
        </div>
        <!-- end responses div -->
    </div>
    <!-- end editorwrapper div -->

</div>