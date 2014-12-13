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
  $_GET["competitie"] = $_SESSION["competitie_session"];
  $competitie = new competitie();
  $competitie_geg = $competitie->competitie_gegevens($_GET['competitie']);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
<head>
   <title>SV Nieuwerkerk | Archief</title>
   
   <meta name="author" content="Rob Hoogland" />
   <meta name="copyright" content="&copy; 2010 jeugdschaken.nl" />
   <meta name="description" content="Welkom - mijn-2e-huis.nl" />
   <meta name="keywords" content="Share documents, School Project, information, file sharing" />
   <meta name="robots" content="index,nofollow" />
    
   <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />

   <link rel="stylesheet" type="text/css" href="../style.css" />
   <link rel="stylesheet" type="text/css" href="../rrip.css" />



</head>

<body>

    <? $menu->menu_main($login->LOGGED_IN);
      // $menu->menu_competitie();
    ?>            
        
    <div id="text">
        <h1>Competities</h1>
    <?
    //COMPETITIE GEGEVENS
    if(isset($_GET['competitie']))
    {
        $competitie->site($_GET['competitie']);
    }
    
    ?>                                                       
    </div>    
    

</body>
</html>

