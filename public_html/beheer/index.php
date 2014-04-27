<?
 //WEBSITE STARTUP
  include_once('class_login2.php');
  include('class_menu.php');

  $login = new login(0, 2);
  $login->main();
  
  $menu = new menu();
  
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

   <link rel="stylesheet" type="text/css" href="style.css" />


</head>

<body>

    <? $menu->menu_main($login->LOGGED_IN);?>        
    <div id="text">
        <h1>Beheer SV Nieuwerkerk</h1>
    </div>    
    

</body>
</html>

