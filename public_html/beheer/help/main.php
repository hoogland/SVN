<?
 //WEBSITE STARTUP
  include_once('../class_login2.php');
  include_once('../class_database.php');
  include_once('../class_menu.php');
  
  $database = new database();
  $login = new login(1, 0);
  $login->main();
  
  $menu = new menu();
  
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
<head>
   <title>SV Nieuwerkerk | Beheer - Help</title>
   
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
        <h1>Help Algemeen</h1>
        Deze pagina's zijn gemaakt om uitleg te geven over de werking van het beheer systeem van SV Nieuwerkerk.
        
        <h2>Menu</h2>
        De bovenste balk (tussen het groen en oranje) is het hoofdmenu. Wanneer hier op één van deze onderdelen wordt geklikt verschijnt eronder het submenu voor meer specifiekere onderdelen.
        
        <h2>Seizoen & Competities selecteren</h2>
        Aan de rechterkant van het menu is er de mogelijkheid om het seizoen en de competitie te kiezen dat bewerkt moet worden. Dit wordt onthouden door het systeem en ook op vervolgpagina's gebruikt.
        
        
        
    </div>    
    

</body>
</html>

