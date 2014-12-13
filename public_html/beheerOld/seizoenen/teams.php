<?
 //WEBSITE STARTUP
  include_once('../class_login2.php');
  include_once('../class_database.php');
  include_once('../class_competitie.php');
  include_once('../class_menu.php');

  $database = new database();
  $competitie = new competitie();  
  $login = new login(1, 0);
  $login->main();
  
  $menu = new menu();
  
  
  
  if(isset($_POST['speler_add']))       //SPELER TOEVOEGEN
  {
      $sql = "INSERT INTO svn_leden_teams (team_id, speler_id) VALUES (".$_POST["team"].",".$_POST['speler_add'].")";
      mysql_query($sql);
  }  
 if(isset($_GET['actie']) && $_GET['actie'] == "verwijderen")     //SPELER VERWIJDEREN
  {
        $sql = "DELETE FROM svn_leden_teams WHERE team_id = ".$_GET['team']." AND speler_id = ".$_GET['speler'];
        mysql_query($sql); 
  }   
        
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
        <h1>Teams</h1>
        <form action="team_aanmaken.php" method="post">Naam team: <input type="text" name="naam"><input type="submit" value="Aanmaken"></form>
        Geef hier de basisspelers van de teams aan.
        
        <TABLE>
        <?
            //SELECT TEAMS
            $sql        = "SELECT * FROM svn_teams WHERE seizoen_id = ".$_SESSION['seizoen'];
            $result     = mysql_query($sql);
            if(mysql_num_rows($result) > 0)
            {
                echo "<TR>";
                for($a = 0; $a < mysql_num_rows($result); $a++)
                {
                    $team = mysql_fetch_array($result);
                    echo "<TD valign=\"top\"><TABLE><TR><TD colspan=3>".$team["naam"];
                    ?>
                    <form action="teams.php" method="post">
                    <select name="speler_add"><?  $competitie->spelers_select(); ?></select>
                    <input type="hidden" name="team" value="<? echo $team["id"]; ?>">
                    <input type="submit" value="Toevoegen">
                    </form>                   
                    <?
                    
                    $sql2 = "SELECT * FROM svn_leden_teams WHERE team_id = ".$team["id"];
                    $result2 = mysql_query($sql2);
                    for($b = 1; $b < mysql_num_rows($result2) + 1; $b++)
                    {
                        $speler_id = mysql_fetch_array($result2);
                        $speler = $competitie->speler_gegevens($speler_id["speler_id"]);
                        echo "<TR><TD>".$b.".<TD>".$speler["naam"]."<TD><a href=\"teams.php?actie=verwijderen&speler=".$speler_id["speler_id"]."&team=".$team["id"]."\">Verwijderen</a>";
                    }
                    
                    echo "</TABLE>";
                }
            }
        ?>
        </TABLE>
        
        
    </div>    
    

</body>
</html>

