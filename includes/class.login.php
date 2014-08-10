<?php
    /***************************************************
    * login.user.class.php
    * Created to serve as a login & registration functionality
    * 
    * Latest Change: 2012-10-05 14:02
    * RH - 2012-05-11 - Made registration, activation & login the password case-sensitive
    * RvdB - 2012-10-05 - Added functionality for the reference number of the user/client
    * 
    */    

    class login
    {
        //DEFINE VARIABLES
        var $DEFAULT_PAGE;                                                                  //HOMEPAGE OF SITE
        var $PATH                     = "/beheerV2/";                                         //THE SUBFORLDER OF THE SITE
        var $LOG_MAP                  = "../logs/";                                         //FOLDER FOR LOGGFILES
        var $LOGFILE                  = 1;                                                  // 0 = NO LOG FILE, 1 = CREATE LOG FILE 
        var $HASH_KEY                 = Setting::userLoginHashKey;                                        
        //GENERAL USER HASH 
        //DATABASE&TABLE SETTINGS
        var $USER_TABLE               = "Users";
        var $USER_ID                  = "id";
        var $USER_NAME                = "username";
        var $USER_PWD                 = "password";
        var $USER_NONCE               = "nonce";
        var $USER_CREATION            = "created_on";
        // var $USER_EMAIL               = "email";
        var $USER_IP_REGISTER         = "created_ip_v4";
        var $SESSION_TABLE            = "Sessions";
        var $SESSION_ID               = "id";
        var $SESSION_ID_HASH          = "hash";
        var $SESSION_IPv4             = "created_ip_v4";
        var $SESSION_TIME             = "created_on";
        var $SESSION_USER             = "user";

        //PAGE SETTINGS                                                                                    
        var $DEMAND_LOGIN             = 0;                                                  //SETTING IF USER HAS TO BE LOGGED IN
        var $LOGGED_IN                = 0;
        var $RETURN                   = "";
        var $ERROR_REPORT             = 0;

        //GENERAL ITEMS
        var $repository;
        var $errorClass;
        var $notificationClass;
        var $debug                    = false;

        //CREATE CONSTRUCT
        function __construct($demand_login = 0, $error = 0, $repository = 0, $errorClass = 0, $notificationClass)
        {
            $this->DEFAULT_PAGE = Setting::baseUrl.$this->PATH;

            session_start();
            $this->repository = $repository;
            $this->errorClass = $errorClass;
            $this->notificationClass = $notificationClass;

            include_once('database.inc');
            //ERROR REPORT GOEDZETTEN
            $this->ERROR_REPORT = 0;
            $this->error_report();

            // include_once('class_email.php');
            $this->DEMAND_LOGIN = $demand_login;  

            //RETURN CORRECT ZETTEN  
            $root    =  substr_count($_SERVER['PHP_SELF'],"/") - 1;
            $return    = "";
            for($a = 0; $a < $root; $a++)                                                                                                                       
                $this->RETURN    .= "../";

            //AANZETTEN LOGFILE
            if($this->LOGFILE == 1)
                $this->logsys();

            if($_SESSION['foutmelding']['tijd'] == 1)
                $_SESSION['foutmelding']['tijd'] = 2;
            else
                unset($_SESSION['foutmelding']);

            if($this->repository->get_data('createAccount') == 2)
                $this->register();
            /* if($this->repository->get_data('confirmHash') != "" && $this->repository->get_data('activateAccount') == 3)
            $this->activate();      */
            if(!is_array($this->errorClass->errors))   
                $this->main(); 
        }      

        //MAIN LOGIN FUNCTION
        function main()
        {
            //STEP 0 CHECK IF LOGGING OUT
            if(isset($_GET['logout']))
            {
                //Check status of login
                $this->check_login();
                //Unset the login
                $this->logout();
            }
            elseif($this->DEMAND_LOGIN != -1)
            {

                //STEP 0 Check Linkedin Login
                //$this->linkedin();

                //STEP 1 Check if already logged in
                if($this->repository->get_data("idSession"))
                    $this->check_login();
                //STEP 2 Login if needed
                //if(!$this->repository->get_data("idSession"))
                if($this->LOGGED_IN !== 1)
                    $this->login();
                //STEP 3 check if has access if needed
                $this->acces();   
            }
        }

        //FUNCTION TO HASH the password
        function hash_password($password, $nonce) 
        {
            return hash_hmac('sha512', $password . $nonce, $this->HASH_KEY);
        }

        //FUNCTION TO CREATE nonce
        function create_nonce()
        {
            return hash_hmac('sha512', uniqid(), $this->HASH_KEY);
        }     

        /**
        * Function to retreive a nonce based upon the emailadress
        * 
        * @param mixed $email
        */
        function get_nonce($userEmail = false)
        {
            if(!$userEmail && $this->repository->get_data('userEmail'))
                $userEmail = $this->repository->get_data('userEmail');
            else
                return false;
            //GET NONCE
            $query = sprintf("SELECT ".$this->USER_NONCE." FROM ".$this->USER_TABLE." WHERE ".$this->USER_NAME." = '%s' LIMIT 1",$userEmail);

            $result = mysql_query($query);
            if($result && mysql_num_rows($result) == 1)
            {
                $row = mysql_fetch_array($result);
                $nonce = $row[$this->USER_NONCE]; 
                return $nonce; 
            }      
            else
                return false;
        } 

        function getConfirmationHash($userEmail = false)
        {
            if(!$userEmail && $this->repository->get_data('userEmail'))
                $userEmail = $this->repository->get_data('userEmail');
            elseif(!userEmail)
                return false;  
            //GET Hash
            $query = sprintf("SELECT confirm_hash FROM ".$this->USER_TABLE." WHERE ".$this->USER_NAME." = '%s' LIMIT 1",$userEmail);

            $result = mysql_query($query);
            if($result && mysql_num_rows($result) == 1)
            {
                $row = mysql_fetch_array($result);
                $hash = $row["confirm_hash"]; 
                return $hash; 
            }      
            else
                return false;
        }

        function create_session_id()
        {
            //CREATE UNIQUE CODE;
            $session_id = $this->create_nonce();

            //CHECK IF ALREADY EXIST IN SESSION TABLE
            $sql = "SELECT * FROM ".$this->SESSION_TABLE." WHERE ".$this->SESSION_ID." = '".$session_id."'";
            $query = mysql_query($sql);

            if(mysql_num_rows($query) > 0)
                $session_id = $this->create_session_id();       //REPEAT IF ID ALREADY EXIST
            return $session_id;   
        }

        //REGISTER FUCTION
        function register()
        {
            include('database.inc');
            //ALL VARIABLES PRESENT AND PASSWORDS MATCH?
            if($this->repository->get_data('createAccount') == 2  && strlen($this->repository->get_data('userPassword')) <= 25 && $this->repository->get_data('userPassword') == $this->repository->get_data('userPassword2') && $this->repository->get_data('userEmail') != "")
            {          
                //VALIDATE PASSWORD
                if(strlen($this->repository->get_data('userPassword')) > 5)
                {
                    //SETTING USERNAME
                    $email = $this->repository->get_data('userEmail');

                    //CHECK FOR DOUBLE ENTRIES
                    $query = "SELECT ".$this->USER_ID." FROM ".$this->USER_TABLE." WHERE ".$this->USER_NAME." = '".$email."'";
                    echo $query;
                    $result = mysql_query($query);
                    if($result && mysql_num_rows($result) > 0)
                        $this->errorClass->add_error(201);
                    else
                    {
                        $nonce = $this->create_nonce();
                        $password = $this->hash_password($this->repository->get_data('userPassword'), $nonce);
                        $user_ip = $this->repository->get_data('userIPv4');
                        $email_hash =  $this->hash_password($email, $nonce);

                        //Define the reference number for the user
                        $sql = "SELECT MAX(`reference_number`) as maximum FROM Users WHERE created_on >= '".date("Y")."-01-01' AND created_on <= '".date("Y")."-12-31'";
                        $result = mysql_fetch_assoc(mysql_query($sql));
                        $reference_number = $result["maximum"] + 1;

                        //Create public profile hash
                        $profile_hash = $this->hash_password($reference_number, $nonce);

                        //INSERT NEW USER TO DB
                        $query = sprintf("INSERT INTO ".$this->USER_TABLE." (".$this->USER_PWD.", ".$this->USER_NAME.", ".$this->USER_IP_REGISTER.", ".$this->USER_CREATION.", confirm_hash, nonce, account_status, reference_number, profile_hash) VALUES ('%s','%s','%s', NOW(), '%s','%s', 416,'%s','%s')",$password,$email,$user_ip, $email_hash, $nonce, $reference_number, $profile_hash);
                        $result = mysql_query($query);

                        if(!$result)
                        {
                            $this->errorClass->add_error(202);
                        }
                        else
                        {
                            //INSERT Clientnr to DB            
                            $sql = "SELECT * FROM Users WHERE id = ".mysql_insert_id();
                            $result = mysql_query($sql);
                            $user = mysql_fetch_assoc($result);
                            $date = strtotime($user["created_on"]);
                            $sql = "UPDATE Users SET client_number = \"K".date('ym',$date)."".sprintf('%04d',$user["reference_number"])."\" WHERE id = ".$user["id"];
                            $result = mysql_query($sql); 

                            //$this->sendConfirmationnMail($email, $email_hash);  
                            //$this->notificationClass->add_note("Bedankt voor je inschrijving. Je ontvangt binnen enkele ogenblikken een e-mail om je registratie te voltooien. Controleer ook je spamfolder.");      
                        }     
                    } 
                }
                else
                    $this->notificationClass->add_note("Uw wachtwoord is niet lang genoeg. <a href=\"login.php\">Probeer het nogmaals.</a>");
            }                                                                                                         
            else
                $this->notificationClass->add_note("Niet alle gegevens zijn aanwezig of correct ingevoerd. <a href=\"login.php\">Probeer het nogmaals.</a>");

        }

        /**
        * Sending the confirmation mail
        * 
        * reason 1: first registration
        * reason 2: resending registration
        * 
        * @param mixed $email
        * @param mixed $email_hash
        * @param mixed $reason
        */
        function sendConfirmationnMail($email, $reason = 1)
        {
            $confirm_hash = $this->getConfirmationHash($email);
            require_once("mailer.class.php");
            $mailer = new mailer();

            //SEND CONFIRMATION EMAIL
            $confirmation_mail = $email.",<BR><BR>Bedankt voor je registratie op www.innovenio.nl.<BR>Klik op de onderstaande link om je registratie te voltooien :<BR><BR>".Setting::baseUrl."/users/activate_account.php?confirmHash=".$confirm_hash."<BR><BR>Met vriendelijke groet,<BR><BR>Innovenio.nl";
            $mailer->send_mail($email, "Registration Innovenio",$confirmation_mail);
            if($reason == 1)
                $this->notificationClass->add_note("<h1>Bericht verzonden</h1>Bedankt voor het registreren, ter bevestiging is er een email gestuurd met daarin de laatste stap voor de registratie.");
            elseif($reason == 2)
                $this->notificationClass->add_note("<h1>Bericht verzonden</h1>Er is een email verzonden met daarin de laatste stap voor de registratie.");
        }

        //ACTIVATE MEMBERSHIP  
        function activate()
        {
            //Check if confirmHash exists in db & get nonce
            $sql = "SELECT nonce FROM Users WHERE confirm_hash = '".$this->repository->get_data("confirmHash")."'";
            $result = mysql_query($sql);

            //EMAIL CONFIRM HASH IS NOT KNOWN 
            if(!$result || mysql_num_rows($result) < 1)
                $this->errorClass->add_error(203);
            else
            {
                $row = mysql_fetch_array($result);
                $nonce = $row["nonce"];
                //echo  $this->repository->get_data('userEmail');
                //echo $this->hash_password($this->repository->get_data('userEmail'), $nonce);
                //CHECK if email hash = confirmHash
                if($this->repository->get_data('confirmHash') == $this->hash_password($this->repository->get_data('userEmail'), $nonce))
                {
                    //CHECK IF LOGIN IS CORRECT
                    $query = sprintf("SELECT * FROM ".$this->USER_TABLE." WHERE ".$this->USER_PWD." = '".$this->hash_password($this->repository->get_data('userPassword'), $nonce)."' AND ".$this->USER_NAME." = '%s' LIMIT 1",$this->repository->get_data('userEmail'));
                    $result = mysql_query($query);

                    if(!$result || mysql_num_rows($result) < 1)
                    {
                        $this->errorClass->add_error(205);
                    }
                    else
                    {

                        $data = mysql_fetch_assoc($result);
                        //SET THE ACCOUNT ACTIVE
                        $query = sprintf("UPDATE ".$this->USER_TABLE." SET account_status = 0 AND failed_logins = 0 WHERE confirm_hash = '".$this->repository->get_data("confirmHash")."' AND ".$this->USER_NAME." = '%s' LIMIT 1",$this->repository->get_data('userEmail'));
                        $result = mysql_query($query);

                        //CHECK IF FIRST TIME ACTIVATED --> redirect to complete_account.php
                        if($data["get_started_completed"] == 0)
                        {
                            header("Location:".Setting::baseUrl."/users/complete_account.php");
                            $this->login();
                            exit();
                        }
                        return 1;
                    }
                }          
                else
                {
                    $this->errorClass->add_error(205); 
                } 
            }  
        }

        function recover()
        {
            //Check if confirmHash exists in db & get nonce
            $sql = "SELECT nonce FROM Users WHERE confirm_hash = '".$this->repository->get_data("confirmHash")."'";
            $result = mysql_query($sql);

            //EMAIL CONFIRM HASH IS NOT KNOWN 
            if(!$result || mysql_num_rows($result) < 1)
                $this->errorClass->add_error(203);
            else
            {
                $row = mysql_fetch_array($result);
                $nonce = $row["nonce"];

                //CHECK if email hash = confirmHash
                if($this->repository->get_data('confirmHash') == $this->hash_password($this->repository->get_data('userEmail'), $nonce))
                {
                    //Check if passwords are the same
                    if($this->repository->get_data('userPassword') == $this->repository->get_data('userPassword2'))
                    {
                        //Update password and activate account
                        $passwordHash = $this->hash_password($this->repository->get_data('userPassword'), $nonce);
                        $query = "UPDATE Users SET password = '".$passwordHash."', account_status = 0, failed_logins = 0 WHERE username = '".$this->repository->get_data('userEmail')."'";
                        $result = mysql_query($query);
                        $this->notificationClass->add_note("Uw wachtwoord is gewijzigd en uw account weer geactiveerd.");
                        return 1;    
                    }
                    else
                        $this->errorClass->add_error(666);
                }          
                else
                    $this->errorClass->add_error(205);  
            }  

        }

        function linkedin()
        {
            $cookie_name = "linkedin_oauth_".SETTING::linkedInAPI;
            //Check if Cookie exists &&  Login is enabled
            if($this->repository->get_data("linkedinLogin") && isset($_COOKIE[$cookie_name]) && $_COOKIE[$cookie_name])
            {
                $credentials_json = $_COOKIE[$cookie_name]; // where PHP stories cookies
                $credentials = json_decode($credentials_json);

                // validate signature
                if ($credentials->signature_version == 1) {
                    if ($credentials->signature_order && is_array($credentials->signature_order)) {
                        $base_string = '';
                        // build base string from values ordered by signature_order
                        foreach ($credentials->signature_order as $key) {
                            if (isset($credentials->$key)) {
                                $base_string .= $credentials->$key;
                            } else {
                                print "missing signature parameter: $key";
                            }
                        }
                        // hex encode an HMAC-SHA1 string
                        $signature =  base64_encode(hash_hmac('sha1', $base_string, SETTING::linkedInSecret, true));
                        // check if our signature matches the cookie's
                        if ($signature == $credentials->signature) {
                            //Signature is authentic, use the stuff
                            require_once('linkedin_3.3.0.class.php');
                            $linkedin = new LinkedIn(array('appKey' => SETTING::linkedInAPI,'appSecret' => SETTING::linkedInSecret,'callbackUrl'  => NULL ));
                            $tokens = $linkedin->exchangeToken($credentials->access_token);


                            //Find member_id
                            $sql = "SELECT id, nonce  FROM Users WHERE linkedin_id = '".$credentials->member_id."'";
                            $result = mysql_query($sql);

                            if(mysql_num_rows($result) == 1)
                            {
                                $row = mysql_fetch_assoc($result);
                                $userId =  $row["id"];
                                $this->setSession($row["id"], $row["nonce"]);

                                //UPDATE LinkedinToken
                                $sql = "UPDATE Users SET linkedin_token = '".$tokens["linkedin"]["oauth_token"]."', linkedin_token_secret = '".$tokens["linkedin"]["oauth_token_secret"]."' WHERE ".$this->USER_ID." = ".$userId;
                                $query = mysql_query($sql);
                            }

                            //No member found, check if user already exists based upon usernam = email
                            if(mysql_num_rows($result) == 0)
                            {
                                //print_r($tokens);
                                $linkedin->setTokenAccess($tokens["linkedin"]); 
                                $result = $linkedin->profile("~:(email-address)?format=json");
                                $result = json_decode($result["linkedin"], true);
                                $sql = "SELECT id, nonce FROM Users WHERE ".$this->USER_NAME." = '".$result["emailAddress"]."'";
                                $query = mysql_query($sql);
                                if(mysql_num_rows($query) == 1)
                                {
                                    //Combination is known
                                    $row = mysql_fetch_assoc($query);
                                    //print_r($row);
                                    $userId =  $row["id"];
                                    $this->setSession($row["id"], $row["nonce"]);
                                    //UPDATE LinkedinToken
                                    $sql = "UPDATE Users SET linkedin_token = '".$tokens["linkedin"]["oauth_token"]."', linkedin_token_secret = '".$tokens["linkedin"]["oauth_token_secret"]."', linkedin_id = '".$credentials->member_id."' WHERE ".$this->USER_ID." = ".$userId;
                                    $query = mysql_query($sql);

                                    //Perform import of LinkedIn data
                                    require_once("user.class.php");
                                    $user = new user($userId, $this->errorClass, $this->notificationClass);
                                    $user->getLinkedInData();
                                }
                                else
                                {
                                    //Combination is not known, create new profile if stated
                                    if($this->repository->get_data("linkedinRegister") == 1)
                                    {
                                        $email = $result["emailAddress"];
                                        $nonce = $this->create_nonce();
                                        $user_ip = $this->repository->get_data('userIPv4');
                                        $email_hash =  $this->hash_password($email, $nonce);

                                        //Define the reference number for the user
                                        $sql = "SELECT MAX(`reference_number`) as maximum FROM Users WHERE created_on >= '".date("Y")."-01-01' AND created_on <= '".date("Y")."-12-31'";
                                        $result = mysql_fetch_assoc(mysql_query($sql));
                                        $reference_number = $result["maximum"] + 1;

                                        //Create public profile hash
                                        $profile_hash = $this->hash_password($reference_number, $nonce);

                                        //INSERT NEW USER TO DB
                                        $query = sprintf("INSERT INTO ".$this->USER_TABLE." (".$this->USER_NAME.", ".$this->USER_IP_REGISTER.", ".$this->USER_CREATION.", confirm_hash, nonce, account_status, reference_number, profile_hash) VALUES ('%s','%s', NOW(), '%s','%s', 0,'%s','%s')",$email,$user_ip, $email_hash, $nonce, $reference_number, $profile_hash);
                                        $result = mysql_query($query);

                                        if(!$result)
                                        {
                                            $this->errorClass->add_error(202);
                                        }
                                        else
                                        {
                                            //INSERT Clientnr to DB            
                                            $sql = "SELECT * FROM Users WHERE id = ".mysql_insert_id();
                                            $result = mysql_query($sql);
                                            $user = mysql_fetch_assoc($result);
                                            $date = strtotime($user["created_on"]);
                                            $sql = "UPDATE Users SET client_number = \"K".date('ym',$date)."".sprintf('%04d',$user["reference_number"])."\" WHERE id = ".$user["id"];
                                            $result = mysql_query($sql);     

                                            $this->setSession($user["id"], $nonce);
                                            //UPDATE LinkedinToken
                                            $sql = "UPDATE Users SET linkedin_token = '".$tokens["linkedin"]["oauth_token"]."', linkedin_token_secret = '".$tokens["linkedin"]["oauth_token_secret"]."', linkedin_id = '".$credentials->member_id."' WHERE ".$this->USER_ID." = ".$user["id"];
                                            $query = mysql_query($sql);

                                            //Perform import of LinkedIn data
                                            require_once("user.class.php");
                                            $user = new user($user["id"], $this->errorClass, $this->notificationClass);
                                            $user->getLinkedInData();
                                            header("Location:".Setting::baseUrl."/users/complete_account.php");
                                            exit();


                                        }     

                                    }
                                    else
                                    {
                                        $this->notificationClass->add_note("Het emailadres die je gebruikt bij LinkedIn is niet bij ons bekend. Log in met je emailadres en wachtwoord en koppel je profiel met LinkedIn in je accountinstellingen of <a href=\"index.php?linkedinLogin=1&linkedinRegister=1\">registreer</a> met je LinkedIn account");
                                    }
                                }

                            }


                            //print "signature validation succeeded";
                        } else {
                            print "signature validation failed";    
                        }
                    } else {
                        print "signature order missing";
                    }
                } else {
                    print "unknown cookie version";
                }                

            }
        }

        //LOGIN
        function login()
        {                                   
            if($this->repository->get_data('userEmail') != "" && $this->repository->get_data('userPassword') && !$this->repository->get_data('createAccount'))
            {         
                $userEmail = $this->repository->get_data('userEmail');
                $password = $this->repository->get_data('userPassword');
                //GET NONCE
                $query = sprintf("SELECT ".$this->USER_NONCE." FROM ".$this->USER_TABLE." WHERE ".$this->USER_NAME." = '%s' LIMIT 1",$userEmail);
                if($this->debug)   
                    echo "\r\nGET NONCE - ".$query."\r\n";   
                $result = mysql_query($query);
                if($result && mysql_num_rows($result) == 1)
                {
                    $row = mysql_fetch_array($result);
                    $nonce = $row[$this->USER_NONCE];
                    //CHECK PWD
                    $query = sprintf("SELECT * FROM ".$this->USER_TABLE." WHERE ".$this->USER_PWD." = '".$this->hash_password($password, $nonce)."' AND ".$this->USER_NAME." = '%s' LIMIT 1",$userEmail);
                    if($this->debug)   
                        echo "\r\nGET PWD - ".$query."\r\n";   
                    $result = mysql_query($query);
                    $userResult = $result;

                    if(!$result || mysql_num_rows($result) < 1)
                    {
                        $this->errorClass->add_error(418);
                        //Increase failes attempts
                        $sql = sprintf("UPDATE Users SET failed_logins = failed_logins + 1 WHERE ".$this->USER_NAME." = '%s' LIMIT 1",$userEmail);
                        $result = mysql_query($sql);
                        if($this->debug)   
                            echo "\r\nFailed attempt - ".$query."\r\n";   

                        //Check if now already 3 failed attempts
                        $sql = sprintf("SELECT failed_logins FROM Users WHERE ".$this->USER_NAME." = '%s' LIMIT 1",$userEmail); 
                        $result = mysql_query($sql);
                        $row = mysql_fetch_assoc($result);
                        if($row["failed_logins"] == 3)
                        {
                            $userData = mysql_fetch_assoc($userResult);

                            //add extra error notification
                            $this->errorClass->add_error(419);
                            //Block account
                            $sql = sprintf("UPDATE Users SET account_status = 2 WHERE ".$this->USER_NAME." = '%s' LIMIT 1",$userEmail);
                            $result = mysql_query($sql);

                            //Get confirm hash
                            $query = sprintf("SELECT confirm_hash FROM ".$this->USER_TABLE." WHERE ".$this->USER_NAME." = '%s' LIMIT 1",$userEmail);
                            $result = mysql_query($query);
                            $row = mysql_fetch_assoc($result);
                            $confirm_hash = $row["confirm_hash"];     

                            //Send mail to user with solution
                            require_once("user.class.php");
                            $user = new user($userData["id"], $this->errorClass, $this->notificationClass);
                            $user->send_temp_pwd($userEmail, $this, $this->notificationClass, true);
                        }
                    }
                    else
                    {
                        $row = mysql_fetch_array($result);
                        if($row['account_status'] == "" || $row['account_status'] == 0)
                        {
                            $this->setSession($row[$this->USER_ID], $nonce);
                            $this->check_login();
                            $this->notificationClass->add_note("Je bent ingelogd.");
                        }
                        else
                            $this->errorClass->add_error(intval($row['account_status']));
                    }
                }
                else
                {
                    $this->errorClass->add_error(418);
                }   
            }     
        }

        /**
        * Set the Session of a user
        * 
        * Used by login() and linkedin()
        * 
        */
        function setSession($userId, $nonce)
        {
            //CREATE SESSION ID
            $idSession = $this->create_session_id();

            //exit();
            //SET SESSION LOGGED IN
            $_SESSION['idSession'] = $idSession;
            $_SESSION['idSessionHash'] = $this->hash_password($idSession, $nonce);
            $this->repository->set_data("idSessionHash", $_SESSION['idSessionHash']);
            $this->repository->set_data("idSession", $idSession);
            //STORE SESSION ID IN DB
            $sql = "INSERT INTO ".$this->SESSION_TABLE." VALUES ('".$idSession."', '".$this->hash_password($idSession, $nonce)."', ".$userId.", NOW(),'".$_SERVER['REMOTE_ADDR']."')";
            $result = mysql_query($sql);
            if($this->debug)   
                echo "\r\nSet Session - ".$query."\r\n";   
            if (!$result) {
                //die('Invalid query: ' . mysql_error());
            }

            //UPDATE LOGFILES
            //SET idSession, userId, timestamp, ip-adress & failed_logins
            $query = "UPDATE ".$this->USER_TABLE." SET last_login_ip_v4 = \"".$_SERVER['REMOTE_ADDR']."\", last_login_on = NOW(), failed_logins = 0 WHERE ".$this->USER_ID." = ".$userId;
            $result = mysql_query($query);            
        }

        /**************************************** 
        * CHECK LOGIN
        * Latest change: 2012-08-17 14:23h
        * H - 2012-04-16 - Add check for length session
        * H - 2012-04-16 - Update Session Time on view page
        * H - 2012-08-17 - Update for expired sessions
        */ 

        function check_login()
        {
            include('database.inc');
            if($this->repository->get_data('idSession') != "" && $this->repository->get_data('idSessionHash') != "")
            {                                    
                //Check if Session still exists & get userId & Timestamp
                $sql = "SELECT ".$this->SESSION_USER.", ".$this->SESSION_TIME.", NOW() as now FROM ".$this->SESSION_TABLE." WHERE ".$this->SESSION_ID." = '".$this->repository->get_data('idSession')."' LIMIT 1";
                $query = mysql_query($sql);
                if (!$query) {
                    die('Invalid query: ' . mysql_error());
                }

                $session = array();
                if(mysql_num_rows($query) > 0)
                    $session = mysql_fetch_array($query);
                else
                {  
                    $this->errorClass->add_error(409); //MAKE ERROR THAT SESSION IS NOT EXISTING 
                    session_unset(); //Destroy all session information
                } 
                //Check if Session is outdated (not more than half hour)
                if(strtotime($session["created_on"]) < strtotime($session["now"]) - (30*60))
                {
                    if(strtotime($session["created_on"]) > strtotime($session["now"]) - (45*60) && $this->DEMAND_LOGIN == 1)
                        $this->errorClass->add_error(417); //The Session is Outdated (between 30 & 45 inactive minutes)               
                    elseif($this->DEMAND_LOGIN == 1)
                        $this->errorClass->add_error(401); //The Session is Outdated (more than 45 inactive minutes)   
                    $this->logout();            
                }
                else 
                { 

                    //GET NONCE OF USER
                    $sql = "SELECT nonce FROM ".$this->USER_TABLE." WHERE ".$this->USER_ID." = ".$session[$this->SESSION_USER]." LIMIT 1";
                    $query = mysql_query($sql);

                    $user = array();
                    if(mysql_num_rows($query) > 0)
                        $user = mysql_fetch_array($query);
                    else
                    {
                        $this->errorClass->add_error(410);    //MAKE ERROR THAT NONCE IS NOT EXISTING
                    } 

                    //Check if Hash Session is correct
                    if($this->hash_password($_SESSION['idSession'], $user["nonce"]) == $this->repository->get_data('idSessionHash'))
                    {
                        //Set user as Logged In
                        $this->LOGGED_IN = 1;
                        //Add userId to repository
                        $this->repository->set_data("userId", $session[$this->SESSION_USER]);
                        //Update TimeStamp
                        $sql = "UPDATE Sessions SET created_on = NOW() WHERE ".$this->SESSION_ID." = '".$this->repository->get_data('idSession')."' LIMIT 1";
                        $query = mysql_query($sql); 

                        //Set amount of messages / unchecked reactions to projects
                        //messages
                        $sql = "SELECT * FROM  Messages WHERE receiver = ".$this->repository->get_data("userId")." AND `read` = 0";
                        $result = mysql_query($sql);
                        $this->repository->set_data("messages", mysql_num_rows($result));
                        //Reactions
                        $sql = "SELECT * FROM  SearchProfiles as SP, UserProjectInterest AS UPI WHERE SP.user = ".$this->repository->get_data("userId")." AND UPI.project = SP.id AND `seen_by_project_owner` = 0 AND interested = 1";
                        $result = mysql_query($sql);
                        $this->repository->set_data("unseenProjectReactions", mysql_num_rows($result));


                    }
                    else
                    {
                        $this->errorClass->add_error(409); //There is tempered with the Session
                        $this->logout();
                    }
                }
            }
        }

        //LOGOUT
        function logout()
        {
            $sql = "DELETE FROM ".$this->SESSION_TABLE." WHERE ".$this->SESSION_ID." = '".$_SESSION['idSession']."' LIMIT 1";
            $query = mysql_query($sql);

            //DELETE SESSION INFO
            unset($_SESSION['idSession']);
            unset($_SESSION['idSessionHash']);

            //DELETE LINKEDIN SESSION
            $cookie_name = "linkedin_oauth_".SETTING::linkedInAPI;
            unset($_COOKIE[$cookie_name]);

            //GOTO HOMEPAGE
            header("Location:".$this->DEFAULT_PAGE);    
        }

        //CHECK ACCESS
        function acces()
        {
            //IF LOGIN IS DEMANDED
            if($this->DEMAND_LOGIN != 0)
            {
                if($this->LOGGED_IN != 1)
                {
                    $this->errorClass->add_error(401);
                }
                else
                {
                }    
                //FROM HERE THE CHECK IF USER IS ALLOWED TO SEE SPECIFIC PAGES        
            }
        }

        //LOG SYS
        function logsys()
        {
            /* $filename = $this->RETURN."".$this->LOG_MAP."log_".date('M_Y').".csv";              //SETTING FILENAME
            if(!file_exists ($filename))                                            //CREATE LOGFILE IF NOT EXISTING
            {
            $p = fopen($filename,'a');
            fwrite($p,"Datum + tijd;IP ADRES;USER ID;USER NAME;PAGINA\r\n");    //WRITING FIRST LINE OF FILE
            }

            $f = fopen($filename,'a');
            //STRING FORMAT = DATUM + TIJD ; IP ADRES ; USER ID ; PAGINA
            $string = date('Y-m-d H:i:s').";".$_SERVER['REMOTE_ADDR'].";".$_SESSION['user_id'].";".$_SESSION['user_name'].";".$_SERVER['REQUEST_URI']."\r\n";
            fwrite($f,$string);                                                     //PRINTING DATA TO FILE 
            fclose($f);  */                                                        
        }

        function log_error($error)
        {
            /* $filename = $this->RETURN."".$this->LOG_MAP."error_".date('M_Y').".csv";              //SETTING FILENAME
            if(!file_exists ($filename))                                            //CREATE LOGFILE IF NOT EXISTING
            {
            $p = fopen($filename,'a');
            fwrite($p,"Datum + tijd;IP ADRES;USER ID;USER NAME;PAGINA;ERROR\r\n");    //WRITING FIRST LINE OF FILE
            }

            $f = fopen($filename,'a');
            //STRING FORMAT = DATUM + TIJD ; IP ADRES ; USER ID ; PAGINA
            $string = date('Y-m-d H:i:s').";".$_SERVER['REMOTE_ADDR'].";".$_SESSION['user_id'].";".$_SESSION['user_name'].";".$_SERVER['REQUEST_URI'].";".$error."\r\n";
            fwrite($f,$string);                                                     //PRINTING DATA TO FILE 
            fclose($f);  */                                                        
        }

        function error_report()
        {
            switch($this->ERROR_REPORT){
                case 0: {error_reporting(0); ini_set("display_errors", 0); break;}
                case 1: {error_reporting(E_ALL); ini_set("display_errors", 1);break;}
                case 1: {error_reporting(E_ERROR); ini_set("display_errors", 1);break;}
            }
        }

    }
?>
