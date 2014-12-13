<?php
  class login
  {
      //DEFINE VARIABLES
      var $DEFAULT_PAGE             = "http://svnieuwerkerk.nl/beheerOld";                   //HOMEPAGE OF SITE
      var $REFERRER                 = "http://svnieuwerkerk.nl/beheerOld";                   //BASE NAME OF SITE
      var $LOG_MAP                  = "beheerOld/log/";                                             //FOLDER FOR LOGGFILES
      var $LOGFILE                  = 0;                                                  // 0 = NO LOG FILE, 1 = CREATE LOG FILE 
      var $HASH_KEY                 = "appelflap";                                        //HASH KEY FOR USER ID'S 
      var $DEMAND_LOGIN             = 0;                                                  //SETTING IF USER HAS TO BE LOGGED IN
      var $LOGGED_IN                = 0;
      var $RETURN                   = "";
      var $ERROR_REPORT             = 0;

      //CREATE CONSTRUCT
      function __construct($demand_login, $error)
      {
          session_start();
          include_once('database.inc');
          
          //ERROR REPORT GOEDZETTEN
          $this->ERROR_REPORT = $error;
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
      }      
      
      //MAIN LOGIN FUNCTION
      function main()
      {
            //STEP 0 CHECK IF LOGGING OUT
            if(isset($_GET['logout']))
                $this->logout();
            else
            {
                //STEP 1 try to log in
                $this->login();
                //STEP 2 check if logged in
                $this->check_login();
                //STEP 3 check if has access if needed
                $this->acces();    
            }
      }
      
      //REGISTER FUCTION --> READY
      function register()
      {
            //require_once("class_mailer.php");
            //$mail = new mailer();
            //ALL VARIABLES PRESENT AND PASSWORDS MATCH?
            if($_POST['create_account'] == 2 && strlen($_POST['user_name']) <= 25 && strlen($_POST['user_password']) <= 25 && $_POST['user_password'] == $_POST['user_password2'])
            {
                echo "register";
                //VALIDATE USERNAME & PASSWORD
                if(($this->validate_accountname($_POST['user_name']) && strlen($_POST['user_password']) > 6))
                {
                    //SETTING USERNAME
                    $user_name = trim(strtolower($_POST['user_name']));
                    $email = "";
                    
                    //CHECK FOR DOUBLE ENTRIES
                    $query = "SELECT user_id FROM svn_users WHERE user_name = '".$user_name."'";
                    $result = mysql_query($query);
                    if($result && mysql_num_rows($result) > 0)
                        $this->error(201);
                    else
                    {
                        $password = sha1($_POST['user_password']."".$this->HASH_KEY);
                        $user_ip = $_SERVER['REMOTE_ADDR'];
                        $email_hash =  sha1($email."".$this->HASH_KEY);
                        
                        //INSERT NEW USER TO DB
                        $query = sprintf("INSERT INTO svn_users (user_password, user_name, user_ip_register, user_email, user_creation_date, confirm_hash, active) VALUES ('%s','%s','%s','%s', NOW(), '%s', 1)",$password,$user_name,$ip, $email,$email_hash);
                        $result = mysql_query($query);
                        echo $sql;
                        if(!$result)
                            $this->error(202);
                        else
                        {
                            //SEND CONFIRMATION EMAIL
                            //$confirmation_mail = $user_name.",<BR><BR>Bedankt voor het registeren op www.share-school.nl. Klik op de volgende link om de registratie af te maken:<BR><BR>http://www.share-school.nl/create_account.php?hash=$email_hash&email=$email <BR><BR>Hier krijgt u een bevestiging te zien dat u bent geregistreerd.<BR><BR>Met vriendelijke groet,<BR><BR>Share-school.nl";
                            //$mail->send_mail($email, "Registratie www.school-share.nl",$confirmation_mail);
                            echo "<h1>Gebruiker Geregistreerd</h1>Bedankt voor het registreren, ter bevestiging is er een email gestuurd met daarin de laatste stap voor de registratie.";
                        }
                     } 
                }
                else
                    echo "Gebruikersnaam danwel wachtwoord is niet correct"; 
            }
            else
                echo "Niet alle gegevens zijn aanwezig";    
      }
      
      //VALIDATE ACCOUNTNAME --> READY  
      function validate_accountname($account)
      {
        //ALLOWED CHARACTERS
        $span_str = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-1234567890@.";
        //MUST HAVE AT LEAST 1 CHAR
        if(strspn($account,$span_str) == 0)
            return false;
            
        //MUST CONTAIN ALL LEGAL CHARACTERS
        if(strspn($account,$span_str) != strlen($account))
            return false;
        
        //MIN AND MAX LENGTH
        if(strlen($account) > 25 || strlen($account) < 7)
            return false;
        
        //ILLEGAL NAMES
        if(eregi("^((root)|(bin)|(daemon)|(adm)|(lp)|(sync)|(shutdown)|(halt)|(mail)|(news)|(uucp)|(operator)|(games)|(mysql)|(httpd)|(nobody)|(dummy)|(www)|(cvs)|(shell)|(ftp)|(irc)|(debian)|(ns)|(download))$",$account))
            return false;
        
        return true;    
      }
      
      //VALIDATE EMAIL ACCOUNT --> READY  
      function validate_email($email)
      {
        // First, we check that there's one @ symbol, and that the lengths are right
        if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) 
            // Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
            return false;
            
        // Split it into sections to make life easier
        $email_array = explode("@", $email);
        $local_array = explode(".", $email_array[0]);
        for ($i = 0; $i < sizeof($local_array); $i++) 
        {
            if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i]))
            return false;
        }
        if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) 
        { // Check if domain is IP. If not, it should be valid domain name
            $domain_array = explode(".", $email_array[1]);
            if (sizeof($domain_array) < 2)
                return false; // Not enough parts to domain
                
            for ($i = 0; $i < sizeof($domain_array); $i++) 
            {
                if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i]))
                    return false;
            }
        }
        return true;
      }            
      
      //ACTIVATE MEMBERSHIP --> READY  
      function activate($email, $email_hash)
      {
        //CHECK email & hash
        if($this->validate_email($email) && $email_hash == sha1($email."".$this->HASH_KEY))
        {
            $query = "SELECT user_id, user_name FROM svn_users WHERE confirm_hash = '$email_hash'";
            $result = mysql_query($query);

            if(!$result || mysql_num_rows($result) < 1)
                //EMAIL CONFIRM HASH IS NOT KNOWN
                $this->error(203);
            else
            {
                //SET THE ACCOUNT ACTIVE
                $query = "UPDATE svn_users SET active = 1 WHERE confirm_hash = '$email_hash'";
                $result = mysql_query($query);
                return 1;    
            }
        }          
        else
            $this->error(205);    
      }
      
      //LOGIN --> READY
      function login()
      {
            if(isset($_POST['user_name']) && isset($_POST['user_password']) && !isset($_POST['create_account']))
            {
                $user_name = strtolower($_POST['user_name']);
                $password = strtolower($_POST['user_password']);
                
                if($this->validate_accountname($user_name))
                {
                    $query = sprintf("SELECT * FROM svn_users WHERE user_password = '".sha1($password."".$this->HASH_KEY)."' AND user_name = '%s'",$user_name);
                    $result = mysql_query($query);
                    if(!$result || mysql_num_rows($result) < 1)
                    {
                        $this->error(101);  
                    }
                    else
                    {
                        $row = mysql_fetch_array($result);
                        if($row['active'] == 1)
                        {
                            //SET SESSION LOGGED IN
                            $_SESSION['user_id'] = $row['user_id'];
                            $_SESSION['user_id_hash'] = sha1($_SESSION['user_id'].$this->HASH_KEY);
                            $_SESSION['user_name'] = $row['user_name'];
                            
                            //UPDATE LAST LOGIN
                            $query = "UPDATE svn_users SET user_ip_last_login = \"".$_SERVER['REMOTE_ADDR']."\", user_last_login = NOW() WHERE user_id = ".$row['user_id'];
                            $result = mysql_query($query);
                        }
                        else
                            $this->error(105);
                    }
                }
            }    
      }
      
      //CHECK LOGIN --> READY
      function check_login()
      {
          if(isset($_SESSION['user_id']) && isset($_SESSION['user_id_hash']))
          {
              $hash = sha1($_SESSION['user_id'].$this->HASH_KEY);
              if($hash == $_SESSION['user_id_hash'])
                $this->LOGGED_IN = 1;
              else
                $this->error(103);
          }
      }
      
      //LOGOUT --> READY
      function logout()
      {
        //DELETE SESSION INFO
        unset($_SESSION['user_id']);
        unset($_SESSION['user_id_hash']);
        unset($_SESSION['user_name']);
        //GOTO HOMEPAGE
        header("Location:".$this->DEFAULT_PAGE);    
      }
      
      //CHECK ACCES
      function acces()
      {
            //IF LOGIN IS DEMANDED
            if($this->DEMAND_LOGIN != 0)
            {
                if($this->LOGGED_IN != 1)
                {
                    $this->error(100);
                }
                else
                {
                    echo "";
                }    
                //FROM HERE THE CHECK IF USER IS ALLOWED TO SEE SPECIFIC PAGES        
            }
      }
      
      //LOG SYS --> READY
      function logsys()
      {
        $filename = $this->RETURN."".$this->LOG_MAP."log_".date('M_Y').".csv";              //SETTING FILENAME
        if(!file_exists ($filename))                                            //CREATE LOGFILE IF NOT EXISTING
        {
            $p = fopen($filename,'a');
            fwrite($p,"Datum + tijd;IP ADRES;USER ID;USER NAME;PAGINA\r\n");    //WRITING FIRST LINE OF FILE
        }
    
        $f = fopen($filename,'a');
        //STRING FORMAT = DATUM + TIJD ; IP ADRES ; USER ID ; PAGINA
        $string = date('Y-m-d H:i:s').";".$_SERVER['REMOTE_ADDR'].";".$_SESSION['user_id'].";".$_SESSION['user_name'].";".$_SERVER['REQUEST_URI']."\r\n";
        fwrite($f,$string);                                                     //PRINTING DATA TO FILE 
        fclose($f);                                                          
      }
      
      //ERROR REPORTING
      function error($error)
      {
          $_SESSION['foutmelding']['tijd'] = 1;
            switch ($error)
            {
                case "302": $_SESSION['foutmelding']['tekst'][] = "Je hebt niet de rechten dit project te verwijderen";           $return_page = "project_overview";  break;
                case "301": $_SESSION['foutmelding']['tekst'][] = "Je maximaal aantal projecten is bereikt";                      $return_page = "project_overview";  break;
                case "201": $_SESSION['foutmelding']['tekst'][] = "Gebruikersnaam en of email bestaat al";                        $return_page = "register";  break;
                case "202": $_SESSION['foutmelding']['tekst'][] = "Database error, probeer het nog een keer";                     $return_page = "register";  break;
                case "203": $_SESSION['foutmelding']['tekst'][] = "Hash niet gevonden, probeer het nog een keer";                 $return_page = "register";  break;
                case "204": $_SESSION['foutmelding']['tekst'][] = "Email en hash waarden kloppen niet.";                          $return_page = "register";  break;
                case "100": $_SESSION['foutmelding']['tekst'][] = "U moet ingelogd zijn om de opgevraagde pagina te bekijken";$_SESSION['foutmelding']['return_adress'] = $_SERVER['REQUEST_URI'];    $return_page = "login";     break;
                case "101": $_SESSION['foutmelding']['tekst'][] = "Verkeerde combinatie wachtwoord <-> gebruiker";                $return_page = "login";     break;
                case "102": $_SESSION['foutmelding']['tekst'][] = "Niet gemachtigd deze pagina te bekijken";                      $return_page = "user_page"; break;
                case "103": $_SESSION['foutmelding']['tekst'][] = "User_id en User_id_hash kloppen niet meer";                    $return_page = "main";      break;
                case "104": $_SESSION['foutmelding']['tekst'][] = "U probeert de site vanaf een verkeerde locatie te benaderen";  $return_page = "main";break;
            }
            $this->log_error($error.";".$_SESSION['foutmelding'].";".print_r($_POST, true));
            switch($return_page)
            {
                case "register": header("Location:".$this->DEFAULT_PAGE."/create_account.php");exit;
                case "login": header("Location:".$this->DEFAULT_PAGE."/login.php");exit;
                case "main"; $this->logout();exit;
                case "user_page"; header("Location:".$this->DEFAULT_PAGE);exit;
                case "project_overview"; header("Location:".$this->DEFAULT_PAGE."/index.php");exit;
            }     
      }
      
      function log_error($error)
      {
        $filename = $this->RETURN."".$this->LOG_MAP."error_".date('M_Y').".csv";              //SETTING FILENAME
        if(!file_exists ($filename))                                            //CREATE LOGFILE IF NOT EXISTING
        {
            $p = fopen($filename,'a');
            fwrite($p,"Datum + tijd;IP ADRES;USER ID;USER NAME;PAGINA;ERROR\r\n");    //WRITING FIRST LINE OF FILE
        }
    
        $f = fopen($filename,'a');
        //STRING FORMAT = DATUM + TIJD ; IP ADRES ; USER ID ; PAGINA
        $string = date('Y-m-d H:i:s').";".$_SERVER['REMOTE_ADDR'].";".$_SESSION['user_id'].";".$_SESSION['user_name'].";".$_SERVER['REQUEST_URI'].";".$error."\r\n";
        fwrite($f,$string);                                                     //PRINTING DATA TO FILE 
        fclose($f);                                                          
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
