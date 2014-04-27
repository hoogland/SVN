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
  
  $comp_gegevens = $competitie->competitie_gegevens($_SESSION['competitie_session']);
  
  $options = "";
  for($a = 1; $a < count($comp_gegevens["speeldata"]) + 1; $a++)
      $options .= "<OPTION value=\"".$a."\">".$a." - ".$comp_gegevens["speeldata"][$a - 1]["datum"];
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
        <h1>Competitie: <? echo $_SESSION['competitie_naam']; ?></h1>
        
        <form target="_blank" action="rating_rapport_create.php" method="post">
        Vanaf Ronde: <SELECT name="van"><? echo $options;?></SELECT><BR>
        T/m Ronde: <SELECT name="tot"><? echo $options;?></SELECT><BR>
        
        <input type="submit" value="Aanmaken">
        </form>
     </div>    
    
    <BR><B>Standaard data inzenden:</b>
<pre>   
  Speeldata                 Weergegeven als              Inzenden voor       Verwerkt 
- 1 januari t/m 31 maart    201X.01.01 t/m 201x.31.03 => 31 maart         => mei
- 1 april t/m 30 juni       201X.04.01 t/m 201x.30.06 => 30 juni          => augustus
- 1 juli t/m 30 september   201X.07.01 t/m 201x.30.09 => 30 september     => november
- 1 oktober t/m 31 december 201X.10.01 t/m 201x.31.12 => 31 december      => februari
</pre>
</body>
</html>

