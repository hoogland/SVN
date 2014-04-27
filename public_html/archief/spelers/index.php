<?
 //WEBSITE STARTUP
  include_once('../database.inc');
  include_once('../class_competitie.php');
  include('../class_menu.php');
  $menu = new menu();
  $competitie = new competitie();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
<head>
   <title>SV Nieuwerkerk | Archief</title>
   
   <meta name="author" content="Rob Hoogland" />
   <meta name="copyright" content="&copy; 2010 svnieuwerkerk.nl" />
   <meta name="description" content="Welkom - archiefsite SV Nieuwerkerk" />
   <meta name="keywords" content="SV Nieuwerkerk, svnieuwerkerk, svn, schaken, archief" />
   <meta name="robots" content="index,nofollow" />
    
   <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />

   <link rel="stylesheet" type="text/css" href="../style.css" />
 <link href="../rrip.css" rel="stylesheet" type="text/css"> 



</head>

<body>

    <? $menu->menu_main($login->LOGGED_IN);
       $menu->menu_speler();
    ?>            
        
    <div id="text">
        <h1><? echo $_SESSION["speler_naam"];?></h1>
    <?
    //COMPETITIE GEGEVENS
    if(isset($_SESSION['speler']))
    {
        $competitie->speler($_SESSION['speler']);
    }
    
    ?>                                                       
    </div>    
    

</body>
</html>

