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
 
  if(isset($_GET['actie']) && $_GET['actie'] == "verwijderen")     //SPELER VERWIJDEREN
  {
        $sql = "DELETE FROM svn_comp_deelname WHERE  comp_id = ".$_SESSION["competitie_session"]." AND speler_id = ".$_GET['speler'];
        mysql_query($sql);    
        
        //SPELERS HERSCHIKKEN
        $deelnemers = $competitie->spelers_deelname($_SESSION['competitie_session']);
        $a = 1;
        foreach($deelnemers as $key => $deelnemer)
        {
            $sql = "UPDATE svn_comp_deelname SET plaats = ".$a." WHERE comp_id = ".$_SESSION["competitie_session"]." AND speler_id = ".$key;
            mysql_query($sql);
            $a++;
        }
  }
  if(isset($_GET['actie']) && $_GET['actie'] == "omhoog")           //SPELER VERHOGEN
  {
        $sql = "UPDATE svn_comp_deelname SET plaats = 0 WHERE comp_id = ".$_SESSION["competitie_session"]." AND plaats = ".$_GET['plaats'];
        mysql_query($sql);    
        $lager = $_GET['plaats'] - 1;
        $sql = "UPDATE svn_comp_deelname SET plaats = ".$_GET['plaats']." WHERE comp_id = ".$_SESSION["competitie_session"]." AND plaats = ".$lager;
        mysql_query($sql);    
        $sql = "UPDATE svn_comp_deelname SET plaats = ".$lager." WHERE comp_id = ".$_SESSION["competitie_session"]." AND plaats = 0";
        mysql_query($sql);    
  }
  if(isset($_GET['actie']) && $_GET['actie'] == "omlaag")           //SPELER VERLAGEN
  {
        $sql = "UPDATE svn_comp_deelname SET plaats = 0 WHERE comp_id = ".$_SESSION["competitie_session"]." AND plaats = ".$_GET['plaats'];
        mysql_query($sql);    
        $lager = $_GET['plaats'] + 1;
        $sql = "UPDATE svn_comp_deelname SET plaats = ".$_GET['plaats']." WHERE comp_id = ".$_SESSION["competitie_session"]." AND plaats = ".$lager;
        mysql_query($sql);    
        $sql = "UPDATE svn_comp_deelname SET plaats = ".$lager." WHERE comp_id = ".$_SESSION["competitie_session"]." AND plaats = 0";
        mysql_query($sql);    
  }
  if(isset($_POST['speler_add']))       //SPELER TOEVOEGEN
  {
      //MAX NR BINNENHALEN VAN COMP
      $sql = "SELECT MAX(plaats) FROM svn_comp_deelname WHERE comp_id = ".$_SESSION["competitie_session"];
      $result = mysql_query($sql);
      $row = mysql_fetch_array($result);
      $plaats = $row[0]+ 1;

      if($plaats == "")
        $plaats = 1;
      
      $sql = "INSERT INTO svn_comp_deelname (comp_id, speler_id, plaats) VALUES (".$_SESSION["competitie_session"].",".$_POST['speler_add'].",".$plaats.")";
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
        <h1>Deelnemers competitie: <? echo $_SESSION['competitie_naam']; ?></h1>

        <form action="deelnemers.php" method="post">
            Speler toevoegen: 
            <select name="speler_add"><?  $competitie->spelers_select(); ?></select>
        <input type="submit" value="Toevoegen">
        </form>
        
        <?
          //HUIDIGE SPELERS PRINTEN
        $deelnemers = $competitie->spelers_deelname($_SESSION['competitie_session']);
        
            echo "<TABLE><TR><TD>Plaats<TD>Speler";
            foreach($deelnemers as $key => $deelnemer)
            {
                echo "<TR><TD>".$deelnemer["plaats"]."<TD>".$deelnemer["naam"]."<TD>";
                echo "<a href=\"deelnemers.php?actie=omhoog&plaats=".$deelnemer["plaats"]."\">Omhoog</a> ";
                echo "<a href=\"deelnemers.php?actie=omlaag&plaats=".$deelnemer["plaats"]."\">Omlaag</a> ";
                echo "<a href=\"deelnemers.php?actie=verwijderen&speler=".$key."\" onclick=\"return confirm('Weet je zeker dat deze speler moet worden verwijderd?')\">Verwijderen</a> ";
            }
        
        ?>
        
    </div>    
    

</body>
</html>

