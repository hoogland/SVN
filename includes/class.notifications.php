<?php
    class notification
    {
        var $notes;

        function add_note($note)
        {
            $this->notes[] = $note;
        }

        /**
        * The display function is called from the display page and retrieves the errors from the repository
        */
        function display_notes()
        {
            if(is_array($this->notes))
            {
                echo "<div class=\"alert alert-success\">";
                echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
                foreach($this->notes as $note)
                {
                    //Print Notification
                    echo $note."<br />";
                }
                echo  "</div>"; 
            }        
            
        }  
    }
?>
