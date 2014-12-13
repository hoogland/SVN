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
        <h1>Nieuwe competitie seizoen: <? echo $_SESSION['seizoen_naam']; ?></h1>

        <form action="nieuw_verwerken.php" method="post">
            Naam competitie: <input type="text" name="naam"><br>
            Naam uitgebreid (Rating rapportage): <input type="text" name="naam_uitgebreid"><br>
            Type competitie: <select name="type_comp"><option value="1">Interne competitie<option value="2">Externe competitie</select><br>
            Tempo competitie: <select name="stand_tempo"><option value="1">5 min. p.p.p.p.<option value="2">45 min p.p.p.p. (Rapid)<option value="3">1:45u (Lang)</select><br>
            Tempo getypt:  <input type="text" name="speeltempo"><br>
            Wedstrijdleider + email:  <input type="text" name="wedstrijdleider"><br>
            Plaats  <input type="text" name="plaats" value="Nieuwerkerk aand en IJssel"><br>
            Land  <input type="text" name="land" value="Nederland"><br>
        <input type="submit" value="Invoeren">
        </form>            
        
        
    </div>    
    

</body>
</html>

