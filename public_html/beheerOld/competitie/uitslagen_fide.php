<?
 //WEBSITE STARTUP
  include_once('../class_login2.php');
  include_once('../class_database.php');
  include_once('../class_menu.php');
  include_once('../class_competitie.php');

  $database = new database();
  $login = new login(1, 0);
  $login->main();
  
  $menu = new menu();
  $competitie = new competitie();
  $result = $competitie->fide_naar_partijen($_POST['rapportage']);
  $stand = $competitie->stand($result[0],$result[1]);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
<head>
   <title>SV Nieuwerkerk | Beheer</title>
   
   <meta name="author" content="Rob Hoogland" />
   <meta name="copyright" content="&copy; 2010 jeugdschaken.nl" />
   <meta name="description" content="Welkom - mijn-2e-huis.nl" />
   <meta name="keywords" content="Share documents, School Project, information, file sharing" />
   <meta name="robots" content="index,nofollow" />
    
   <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />

   <link rel="stylesheet" type="text/css" href="../style.css" />


</head>

<body>

    <? $menu->menu_main($login->LOGGED_IN);?>        
    
        
    <div id="text">
        <h1><? echo $_SESSION['competitie_naam']; ?> rating rapportage invoeren</h1>
        <?
           if(isset($_POST['rapportage']))
           {
            $target = "uitslagen_fide_verwerken.php";
            $button = "Verwerken";
           }
           else
           {
            $target = $_SERVER['PHP_SELF'];
            $button = "Controleren";
           }
        ?>
        <form method="post" action="<? echo $target;?>">
            <textarea name="rapportage"><? echo $_POST['rapportage'];?></textarea>
            <input type="submit" value="<? echo $button;?>">
        </form> 
        <? if(isset($_POST['rapportage']))
        {
        ?>
        Invoer:<BR>
        <table>
        <?
            $competitie->stand_print($result[0],$stand,array('knsb','naam','PUNTEN','WP','SB'));
        
        ?>
        </table>
        
        <?
        }
        ?>         
    </div>    
    

</body>
</html>

