<?
 //WEBSITE STARTUP
  require_once('../class_login2.php');
  require_once('../class_database.php');
  require_once('../class_menu.php');
  require_once('../class_competitie.php');
  
  $login = new login(1, 0);
  $login->main();
  
  $menu = new menu();
 // $competitie = new competitie();
 // $database = new database();
  
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

    <? $menu->menu_main(0);?>        
    
        
    <div id="text">
        <h1>Test</h1>

        
    </div>    
    

</body>
</html>

