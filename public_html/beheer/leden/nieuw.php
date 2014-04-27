<?
 //WEBSITE STARTUP
  include_once('../class_login2.php');
  include('../class_database.php');
  $database = new database();
  $login = new login(1, 0);
  $login->main();
  
  include('../class_menu.php');
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

   <link rel="stylesheet" type="text/css" href="../style.css" />
</head>

<body>

    <? $menu->menu_main($login->LOGGED_IN);?>        
    
        
    <div id="text">
        <h1>Nieuw lid</h1>
        
        <form action="nieuw_verwerken.php" method="post" name="myform">
            Voorletters: <input type="text" name="voorletters"><br>
            Tussenvoegsel: <input type="text" name="tussenvoegsel"><br>
            Achternaam: <input type="text" name="achternaam"><br>
            KNSB: <input type="text" name="knsb"><br>
            adres: <input type="text" name="adres"><br>
            postcode: <input type="text" name="postcode"><br>
            plaats: <input type="text" name="plaats"><br>
            telefoonnr: <input type="text" name="telefoon"><br>
            geslacht: <select name="geslacht"><option value="1">man<option value="2">Vrouw</select><br>
            type lid: <select name="type_lid"><option value="1">Senior lid<option value="2">Aspirant lid<option value="3">Senior dubbellid<option value="4">Erelid</select><br>
            geboortedatum: <input type="text" name="geb_dat"> <i>(jjjj-mm-dd)</i><br>
            emailadres: <input type="text" name="email"><br>
            <input type="submit" value="Invoeren">    
        </form>
    </div>    
    

</body>
</html>

