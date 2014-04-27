<?
 //WEBSITE STARTUP
  include_once('../class_login2.php');
  include('../class_database.php');
  $database = new database();
  $login = new login(1, 0);
  $login->main();
  
  include('../class_menu.php');
  $menu = new menu();
  include('../class.ratinglijst.php');
  $ratinglijst = new ratinglijst();
  $ratinglijst->read_data_xaa();
  $ratinglijst->saveRatinglist();
  
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
        <h1>KNSB ledenbestand verwerken</h1>   
        !Pas Op! Slechts 1 maal verwerken wanneer er een nieuwe ratinglijst is en deze op http://drl.tuxtown.net/club.php?club=1428 verwerkt is!     
        <form enctype="multipart/form-data" action="knsb_verwerken.php" method="POST">
           <input type="submit" value="Verwerken" />
        </form>
        
    </div>    
    

</body>
</html>

