<?php
    class errorInnovenio
    {
        var $DEFAULT_PAGE             = Setting::baseUrl;                        //HOMEPAGE OF SITE
        var $DEFAULT_REDIRECT         = "login.php";
        var $errors;
        var $redirect;

        function __construct($redirect = 0, $base)
        {
            $this->redirect = $redirect;
            $this->DEFAULT_PAGE = $this->DEFAULT_PAGE."/".$base."/";
        }

        function add_error($error)
        {
            $this->errors[] = $error;
        }

        function execute_errors()
        {
            if(is_array($this->errors))
            {
                foreach($this->errors as $error)
                    $_SESSION["errors"][] = $error;
                foreach($this->errors as $error)
                {
                    //Add error to display

                   /* $errData = $this->get_error($error);
                    if($errData["force_redirect"] == 1)
                        $this->redirect = 0;
                    //Perform action
                    switch($this->redirect)
                    {
                        case 0: $this->perform_redirect();break;
                        case 2: $this->perform_json_error();break;
                    }*/
                }
            }
        }

        function perform_redirect()
        {
            foreach($this->errors as $error)
            {
                //Get error data
                $errData = $this->get_error($error);
                
                //Safety Redirect to Login.php if redirect = empty
                if($errData["redirect"] == "")
                    $errData["redirect"] = $this->DEFAULT_REDIRECT;
                unset($_POST);
                //Redirect error (Default) 
                //Set current page
                $_SESSION["referral"] = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                header("Location:".$this->DEFAULT_PAGE."".$errData["redirect"]);
                exit();
            }
        }

        function perform_json_error()
        {
            $errors = array();
            foreach($this->errors as $number)
            { 
                $result = $this->get_error($number);
                $errors[$number] = $result["description"]; 
            }
            echo json_encode($errors);
            unset($_SESSION["errors"]); 
            exit();     
        }


        //The display function is called from the display page and retrieves the errors from the repository
        function display_errors($errors)
        {
            if(is_array($this->errors))
                $errors = $this->errors;
            if(is_array($errors))
            {
                echo "<div class=\"row\"><div class=\"contentContainer moduletable_mx_red span12\">";
                if(count(array_unique($errors)) > 1)
                    echo "     <h1>Oeps, er waren meerdere foutmeldingen!</h1>";
                    //Print Title
                    if(count($errors) == 1)
                    {
                        $errData = $this->get_error($errors[0]);
                        echo "     <h1>".$errData["title"]."</h1>";
                    }
                echo "<div>";
                //Change this to textual and what is actually allowed to show
                foreach(array_unique($errors) as $error)
                {
                    $errData = $this->get_error($error); 
                    //Print Error
                    echo $errData["description"]."<BR>";
                }
                echo  "</div></div></div>"; 
            }    
            unset($_SESSION["errors"]);     
        }  

        function get_error($error)
        {
            include('database.inc');
            $sql = "SELECT * FROM Errors WHERE id = ".$error;
            $result = mysql_query($sql);

            if(mysql_num_rows($result) != 0)
            {
                $data = mysql_fetch_assoc($result);
                return $data;
            }
            else
                return;
        }  

        /**
        * DEBUGGING FUNCTION FOR BREAKDOWNS :)
        * ("Test", "Description", "true",debug_backtrace());
        * 
        * @param mixed $var
        * @param mixed $location
        * @param string $text
        * @param mixed $backtrace
        */
        function debug($var, $location, $text, $backtrace)
        {
            include('database.inc');
            if(is_array($text))
                $text = mysql_escape_string(var_export($text, true));
            $sql = "INSERT INTO Debug (datetime, var, text, page, location, file, line, class, function, object, type, args) VALUES (NOW(), '".$var."','".$text."','".$_SERVER["PHP_SELF"]."','".$location."', '".$backtrace["0"]["file"]."', '".$backtrace["0"]["line"]."', '".$backtrace["0"]["class"]."', '".$backtrace["0"]["function"]."', '".mysql_escape_string(print_r($backtrace["0"]["object"], true))."', '".$backtrace["0"]["type"]."', '".mysql_escape_string(print_r($backtrace["0"]["args"], true))."')";
            $result = mysql_query($sql);
        }

        function debugSimple($text)
        {
            include('database.inc'); 
            $sql = "INSERT INTO Debug (text) VALUES ('".mysql_escape_string($text)."')";
            $result = mysql_query($sql);
        }
    }
?>