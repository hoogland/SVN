<?php
    /***************************************************
    * Security.class.php
    * Created to serve as a filter for all human input
    * 
    * Latest Change: 2012-04-16 15:28h
    * H - 2012-04-16 - Added latest Change :)
    * 
    */

    class security
    {
        var $repository;
        var $errorClass;

        function __construct($repository = 0, $error = 0)
        {
            $this->repository = $repository;
            $this->errorClass = $error;

            /*Variables
            $get["$_GETname"] = array("SaveName", "Action", "Error code");
            */
            // !!! IMPORTANT: Before going live, all skips (except passwords) should be gone !!!
            
            //SYSTEM VARIABLES
            $server["REMOTE_ADDR"]      = array("userIPv4", "skip", 600); 
            $session["idSession"]       = array("idSession", "clean_hash", 600);
            $session["idSessionHash"]   = array("idSessionHash", "clean_hash", 600);
            $session["referral"]        = array("referral", "skip", 600);

            //INPUT VARIABLES
            $post["email"]                         = array("userEmail", "clean_email", 600);
            $post["password"]                      = array("userPassword", "skip", 600);
            $post["participantsSorting"]           = array("participantsSorting", "clean_string", 600);
            $get["removePlayer"]                   = array("removePlayer", "clean_integer", 600);
            $post["removePlayer"]                  = array("removePlayer", "clean_integer", 600);
            $post["addPlayer"]                     = array("addPlayer", "clean_integer", 600);
            $post["compName"]                      = array("compName", "clean_string", 600);
            $post["compNameExtended"]              = array("compNameExtended", "clean_string", 600);
            $post["tempo"]                         = array("tempo", "clean_integer", 600);
            $post["tempoExtended"]                 = array("tempoExtended", "clean_string", 600);
            $post["arbiter"]                       = array("arbiter", "clean_string", 600);
            $post["arbiterMail"]                   = array("arbiterMail", "clean_string", 600);
            $post["place"]                         = array("place", "clean_string", 600);
            $post["country"]                       = array("country", "clean_string", 600);
            $post["compSorting"]                   = array("compSorting", "clean_string", 600);
            $post["compDisplay"]                   = array("compDisplay", "clean_string", 600);
            $post["typeCompetition"]               = array("typeCompetition", "clean_integer", 600);
            $get["seizoen"]                        = array("seizoen", "clean_integer", 600);
            $post["seizoen"]                       = array("seizoen", "clean_integer", 600);
            $get["competitie"]                     = array("competitie", "clean_integer", 600);
            $post["competitie"]                    = array("competitie", "clean_integer", 600);
            $get["player"]                         = array("player", "clean_integer", 600);
            $post["player"]                        = array("player", "clean_integer", 600);
            $get["ronde"]                          = array("round", "clean_integer", 600);
            $post["ronde"]                         = array("round", "clean_integer", 600);
            $get["matchId"]                        = array("matchId", "clean_integer", 600);
            $post["matchId"]                       = array("matchId", "clean_integer", 600);
            $post["pgnText"]                       = array("pgnText", "clean_string", 600);
            $post["pgnId"]                         = array("pgnId", "clean_integer", 600);
            $get["pgnRemove"]                      = array("pgnRemove", "clean_integer", 600);
            $post["TPRmethod"]                     = array("TPRmethod", "clean_string", 600);
            $post["TPRdamped"]                     = array("TPRdamped", "clean_integer", 600);
            $post["compSystem"]                    = array("compSystem", "clean_string", 600);
            $post["keizerIteraties"]               = array("keizerIteraties", "clean_integer", 600);
            $post["keizerInitialSorting"]          = array("keizerInitialSorting", "clean_string", 600);
            $post["keizerMaxValue"]                = array("keizerMaxValue", "clean_integer", 600);
            $get["memberId"]                       = array("memberId", "clean_integer", 600);
            $post["memberId"]                      = array("memberId", "clean_integer", 600);
            $post["memberKNSB"]                    = array("memberKNSB", "clean_string", 600);
            $post["memberFirstname"]               = array("memberFirstname", "clean_string", 600);
            $post["memberSurname"]                 = array("memberSurname", "clean_string", 600);
            $post["memberMiddlename"]              = array("memberMiddlename", "clean_string", 600);
            $post["memberInitials"]                = array("memberInitials", "clean_string", 600);
            $post["memberAction"]                  = array("memberAction", "clean_integer", 600);
            $get["asynchAction"]                   = array("asynchAction", "clean_integer", 600);
            $post["asynchAction"]                  = array("asynchAction", "clean_integer", 600);
            $get["searchFilter"]                   = array("searchFilter", "clean_string", 600);
            $post["searchFilter"]                  = array("searchFilter", "clean_string", 600);
            $get["teamId"]                         = array("teamId", "clean_string", 600);
            $post["teamId"]                        = array("teamId", "clean_string", 600);
            $get["date"]                           = array("date", "clean_string", 600);
            $post["date"]                          = array("date", "clean_string", 600);
            $post["externalAway"]                  = array("externalAway", "clean_integer", 600);
            $post["externalGroup"]                 = array("externalGroup", "clean_integer", 600);
            $post["ratingTeam"]                    = array("ratingTeam", "clean_integer", 600);
            $post["ratingOpponent"]                = array("ratingOpponent", "clean_integer", 600);
            $post["opponentName"]                  = array("opponentName", "clean_string", 600);
            $post["opponentTeam"]                  = array("opponentTeam", "clean_integer", 600);
            $post["scoreTeam"]                     = array("scoreTeam", "clean_string", 600);
            $post["scoreOpponent"]                 = array("scoreOpponent", "clean_string", 600);
            $post["gameId1"]                       = array("gameId1", "clean_integer", 600);
            $post["gameId2"]                       = array("gameId2", "clean_integer", 600);
            $post["gameId3"]                       = array("gameId3", "clean_integer", 600);
            $post["gameId4"]                       = array("gameId4", "clean_integer", 600);
            $post["gameId5"]                       = array("gameId5", "clean_integer", 600);
            $post["gameId6"]                       = array("gameId6", "clean_integer", 600);
            $post["gameId7"]                       = array("gameId7", "clean_integer", 600);
            $post["gameId8"]                       = array("gameId8", "clean_integer", 600);
            $post["gameId9"]                       = array("gameId9", "clean_integer", 600);
            $post["gameId10"]                      = array("gameId10", "clean_integer", 600);
            $post["memberId1"]                     = array("memberId1", "clean_integer", 600);
            $post["memberId2"]                     = array("memberId2", "clean_integer", 600);
            $post["memberId3"]                     = array("memberId3", "clean_integer", 600);
            $post["memberId4"]                     = array("memberId4", "clean_integer", 600);
            $post["memberId5"]                     = array("memberId5", "clean_integer", 600);
            $post["memberId6"]                     = array("memberId6", "clean_integer", 600);
            $post["memberId7"]                     = array("memberId7", "clean_integer", 600);
            $post["memberId8"]                     = array("memberId8", "clean_integer", 600);
            $post["memberId9"]                     = array("memberId9", "clean_integer", 600);
            $post["memberId10"]                    = array("memberId10", "clean_integer", 600);
            $post["memberRating1"]                 = array("memberRating1", "clean_integer", 600);
            $post["memberRating2"]                 = array("memberRating2", "clean_integer", 600);
            $post["memberRating3"]                 = array("memberRating3", "clean_integer", 600);
            $post["memberRating4"]                 = array("memberRating4", "clean_integer", 600);
            $post["memberRating5"]                 = array("memberRating5", "clean_integer", 600);
            $post["memberRating6"]                 = array("memberRating6", "clean_integer", 600);
            $post["memberRating7"]                 = array("memberRating7", "clean_integer", 600);
            $post["memberRating8"]                 = array("memberRating8", "clean_integer", 600);
            $post["memberRating9"]                 = array("memberRating9", "clean_integer", 600);
            $post["memberRating10"]                = array("memberRating10", "clean_integer", 600);
            $post["opponentName1"]                 = array("opponentName1", "clean_string", 600);
            $post["opponentName2"]                 = array("opponentName2", "clean_string", 600);
            $post["opponentName3"]                 = array("opponentName3", "clean_string", 600);
            $post["opponentName4"]                 = array("opponentName4", "clean_string", 600);
            $post["opponentName5"]                 = array("opponentName5", "clean_string", 600);
            $post["opponentName6"]                 = array("opponentName6", "clean_string", 600);
            $post["opponentName7"]                 = array("opponentName7", "clean_string", 600);
            $post["opponentName8"]                 = array("opponentName8", "clean_string", 600);
            $post["opponentName9"]                 = array("opponentName9", "clean_string", 600);
            $post["opponentName10"]                = array("opponentName10", "clean_string", 600);
            $post["opponentKNSB1"]                 = array("opponentKNSB1", "clean_integer", 600);
            $post["opponentKNSB2"]                 = array("opponentKNSB2", "clean_integer", 600);
            $post["opponentKNSB3"]                 = array("opponentKNSB3", "clean_integer", 600);
            $post["opponentKNSB4"]                 = array("opponentKNSB4", "clean_integer", 600);
            $post["opponentKNSB5"]                 = array("opponentKNSB5", "clean_integer", 600);
            $post["opponentKNSB6"]                 = array("opponentKNSB6", "clean_integer", 600);
            $post["opponentKNSB7"]                 = array("opponentKNSB7", "clean_integer", 600);
            $post["opponentKNSB8"]                 = array("opponentKNSB8", "clean_integer", 600);
            $post["opponentKNSB9"]                 = array("opponentKNSB9", "clean_integer", 600);
            $post["opponentKNSB10"]                = array("opponentKNSB10", "clean_integer", 600);
            $post["opponentRating1"]               = array("opponentRating1", "clean_integer", 600);
            $post["opponentRating2"]               = array("opponentRating2", "clean_integer", 600);
            $post["opponentRating3"]               = array("opponentRating3", "clean_integer", 600);
            $post["opponentRating4"]               = array("opponentRating4", "clean_integer", 600);
            $post["opponentRating5"]               = array("opponentRating5", "clean_integer", 600);
            $post["opponentRating6"]               = array("opponentRating6", "clean_integer", 600);
            $post["opponentRating7"]               = array("opponentRating7", "clean_integer", 600);
            $post["opponentRating8"]               = array("opponentRating8", "clean_integer", 600);
            $post["opponentRating9"]               = array("opponentRating9", "clean_integer", 600);
            $post["opponentRating10"]              = array("opponentRating10", "clean_integer", 600);
            $post["externalScore1"]                = array("externalScore1", "clean_string", 600);
            $post["externalScore2"]                = array("externalScore2", "clean_string", 600);
            $post["externalScore3"]                = array("externalScore3", "clean_string", 600);
            $post["externalScore4"]                = array("externalScore4", "clean_string", 600);
            $post["externalScore5"]                = array("externalScore5", "clean_string", 600);
            $post["externalScore6"]                = array("externalScore6", "clean_string", 600);
            $post["externalScore7"]                = array("externalScore7", "clean_string", 600);
            $post["externalScore8"]                = array("externalScore8", "clean_string", 600);
            $post["externalScore9"]                = array("externalScore9", "clean_string", 600);
            $post["externalScore10"]               = array("externalScore10", "clean_string", 600);
            $post["save"]                          = array("save", "clean_integer", 600);
            $get["color"]                          = array("color", "clean_integer", 600);
            $post["color"]                         = array("color", "clean_integer", 600);
            $get["opponent"]                       = array("opponent", "clean_integer", 600);
            $post["opponent"]                      = array("opponent", "clean_integer", 600);
            $post["report"]                        = array("report", "clean_string", 600);
            $post["reportOpponent"]                = array("reportOpponent", "clean_string", 600);
                       
            //Special case contactType
            if(isset($_POST["contactType"]) && $this->clean_integer($_POST["contactType"]))
            {
                switch($this->clean_integer($_POST["contactType"]))
                {
                    case 1: $post["contactValue"][1] = "clean_string";break;
                    case 2: $post["contactValue"][1] = "clean_string";break;
                    case 3: $post["contactValue"][1] = "clean_email";break;
                    case 4: $post["contactValue"][1] = "clean_string";break;
                    case 5: $post["contactValue"][1] = "clean_string";break;
                    case 6: $post["contactValue"][1] = "clean_string";break;
                    case 7: $post["contactValue"][1] = "clean_string";break;
                    case 8: $post["contactValue"][1] = "clean_string";break;
                    case 9: $post["contactValue"][1] = "clean_string";break;
                }
            }

            // Execute security
            $this->secure($get, $_GET);
            $this->secure($post, $_POST);
            $this->secure($session, $_SESSION);
            $this->secure($server, $_SERVER);
            $this->secure($files, $_FILES);

            //Special cases execute
            if(isset($_SESSION["errors"]))
            {
                $data = $this->clean_errors($_SESSION["errors"]);
                if($data === false)
                {
                    // Special case since the hacker is tempering with the errors
                    unset($_SESSION['errors']);
                    $this->errorClass->add_error(667);
                }
                else
                    $this->repository->set_data("errors", $data);
            }
        }

        public function data_validation_check($var, $function)
        {
            switch($function)
            {
                case "clean_hash": {$data = $this->clean_hash($var);break;} 
                case "clean_email": {$data = $this->clean_email($var);break;} 
                case "clean_ipv4": {$data = $this->clean_ipv4($var);break;} 
                case "clean_integer": {$data = $this->clean_integer($var);break;} 
                case "clean_date": {$data = $this->clean_date($var);break;}      
                case "clean_errors": {$data = $this->clean_errors($var); break;}
                case "clean_string": {$data = $this->clean_string($var);break;}  
                case "clean_photo": {$data = $this->clean_photo($var);break;}  
                case "skip": {$data = $var;break;} 
            }
            if($data !== false)
                return true;
            else
                return false;
        }

        function secure($totalvars, $input)
        {
            foreach($input as $key => $var)
            {
                $data;
                if(isset($totalvars[$key]) && $var != "")
                {
                    $process = $totalvars[$key];
                    switch($process[1])
                    {
                        case "clean_hash": {$data = $this->clean_hash($var);break;} 
                        case "clean_email": {$data = $this->clean_email($var);break;} 
                        case "clean_ipv4": {$data = $this->clean_ipv4($var);break;} 
                        case "clean_integer": {$data = $this->clean_integer($var);break;} 
                        case "clean_date": {$data = $this->clean_date($var);break;}      
                        case "clean_errors": {$data = $this->clean_errors($var); break;}
                        case "clean_string": {$data = $this->clean_string($var);break;}  
                        case "clean_photo": {$data = $this->clean_photo($var);break;}  
                        case "clean_oneDimArray": {$data = $this->clean_oneDimArray($var, $process["type"]);break;}  
                        case "clean_twoDimArray": {$data = $this->clean_twoDimArray($var, $process["types"]);break;}  
                        case "skip": {$data = $var;break;} 
                    }

                    if($data === false)                                                         
                        $this->errorClass->add_error($process[2]);         
                    else
                        $this->repository->set_data($process[0], $data);
                }
            }
        }

        //Clean the hash variable
        function clean_hash($hash)
        {
            if (filter_var($hash, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z0-9]+$/")))) 
                return $hash;
            else
                return false;
        }

        //Clean the email variable
        function clean_email($email)
        {
            if(filter_var($email, FILTER_VALIDATE_EMAIL))
                return $email;
            else 
                return false;
        }

        //Clean the string variable
        //TODO XSS
        function clean_string($string)
        {
            if(get_magic_quotes_gpc())
            {
                $string = stripslashes($string);
            }

            include('database.inc');
            $string = mysql_real_escape_string($string);
            mysql_close($verbinding);

            return $string;
        }

        //Clean the ipv4 variable
        function clean_ipv4($ipv4)
        {
            if(filter_var($ipv4, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE))
                return $ipv4; 
            else
                return false;
        }

        //Clean the integer variable 
        function clean_integer($integer)
        {
            // Int(0) gets cast by PHP to false, therefore the '===0' is added
            // Note: +0 and -0 are still not allowed
            if(filter_var($integer, FILTER_VALIDATE_INT)===0 || !filter_var($integer, FILTER_VALIDATE_INT)===False)
                return $integer;
            else 
                return false;
        }

        //Clean the date variable 
        function clean_date($date)
        {
            //Check the format (DD-MM-YYYY)
            if (preg_match ("/^([0-9]{2})-([0-9]{2})-([0-9]{4})$/", $date, $dateParts))
            {
                //Check the date
                if (checkdate($dateParts[2],$dateParts[1],$dateParts[3]))
                    return $date;
                else    
                    return false;
            }   
            else
                return false; 
        }

        //Clean the error variables
        function clean_errors($errors)
        {
            if(is_array($errors))
            {
                $return;
                foreach($errors as $error)
                {
                    if($this->clean_integer($error))
                        $return[] = $error;
                    else
                        return false;
                }
                return $return;
            }
            else
                return false;
        }

        //Clean the photo variable & return reference to location + info
        function clean_photo($photo)
        {   
            //Set default file extension whitelist
            $whitelist_ext = array('jpg','png', 'jpeg', 'jpe');
            //Set default file type whitelist
            $whitelist_type = array('image/jpeg', 'image/png');  
            //Set Maximum file size (10MB)
            $max_size = 10 * 1024 * 1024;

            //Make sure that there is a file
            if((!empty($photo)) && ($photo['error'] == 0) && $photo != "") {

                // Get filename
                $file_info = pathinfo($photo['name']);
                $name = $file_info['filename'];
                $ext = $file_info['extension'];

                //Check file has the right extension
                if (!in_array(strtolower($ext), $whitelist_ext)) 
                    return false;

                //Check that the file is of the right type
                if (!in_array($photo["type"], $whitelist_type)) 
                    return false;

                //Check that the file is not too big
                if ($photo["size"] > $max_size) 
                    return false;

                //Check that the files contains valid headers and dimensions
                if(!getimagesize($photo["tmp_name"]))
                    return false;

                //If all succeeds
                return $photo;
            }
            else
                return false;               

        }

        //Checks is an one dimensional array is clean, type = the type of vars that should be in there
        function clean_OneDimArray($array, $type)
        {
            $clean = true;
            foreach($array as $value)
            {
                $result = $this->checkClean($type, $value);
                if(!$result)
                    $clean = false;  
            }
            if($clean)
                return $array;
            else
                return false;   
        }

        //Checks is an two dimensional array is clean, type = array with the types of vars that should be in there
        function clean_twoDimArray($array, $types)
        {
            $clean = true;
            foreach($array as $subArray)
            {
                if(count($subArray) != count($types))
                    return false;
                for($a = 0; $a < count($types); $a++)
                {
                    $result = $this->checkClean($type[$a], $subArray[$a]);
                    if(!$result)
                        $clean = false;  
                }
            }
            if($clean)
                return $array;
            else
                return false;   
        }

        //The check clean function for the arrays
        function checkClean($type, $input)
        {
            $data;
            switch($type)
            {
                case "clean_hash": {$data = $this->clean_hash($input);break;} 
                case "clean_email": {$data = $this->clean_email($input);break;} 
                case "clean_ipv4": {$data = $this->clean_ipv4($input);break;} 
                case "clean_integer": {$data = $this->clean_integer($input);break;} 
                case "clean_date": {$data = $this->clean_date($input);break;}      
                case "clean_errors": {$data = $this->clean_errors($input); break;}
                case "clean_string": {$data = $this->clean_string($input);break;}  
                case "clean_photo": {$data = $this->clean_photo($input);break;}  
                case "skip": {$data = $input;break;} 
            }
            if($data === false)
                return false;
            else
                return true;
        }


    }
?>
