<?
 //WEBSITE STARTUP
  include_once('database.inc');
  include('class_menu.php');
  $menu = new menu();
  
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

   <link rel="stylesheet" type="text/css" href="style.css" />


</head>

<body>

    <? $menu->menu_main($login->LOGGED_IN);?>
        
    <div id="text">
        <h1>Archiefsite SV Nieuwerkerk</h1>
                                                           
    </div>    
    

</body>
</html>

