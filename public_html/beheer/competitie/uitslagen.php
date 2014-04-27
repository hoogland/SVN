<?
 //WEBSITE STARTUP
  include_once('../class_login2.php');
  include_once('../class_database.php');
  include_once('../class_menu.php');
  include_once('../class_competitie.php');
  
  $login = new login(1, 0);
  $login->main();
  
  $menu = new menu();
  $competitie = new competitie();
  $database = new database();

  if(isset($_POST['partij_id']) && isset($_POST['uitslag']))
  {
      $sql = "UPDATE svn_partijen SET uitslag = ".$_POST['uitslag']." WHERE id = ".$_POST['partij_id'];
      mysql_query($sql);
  }
  if(isset($_POST['partij_id']) && isset($_POST['reglementair']))
  {
      $sql = "UPDATE svn_partijen SET reglementair = ".$_POST['reglementair']." WHERE id = ".$_POST['partij_id'];
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
        <h1>Uitslagen - <? echo $_SESSION['competitie_naam']; ?></h1>
        <a href="uitslagen_fide.php">Rating rapportage invoeren</a>
        <?
        echo "<form method=\"get\" action=\"".$_SERVER['PHP_SELF']."\">";
        foreach($_GET as $key => $value)
        {
            if($key != "ronde");
            echo "<input type=\"hidden\" name=\"".$key."\" value=\"".$value."\">";
        }

        echo "Ronde: <SELECT name=\"ronde\" onChange='this.form.submit();'>";        
        
        $sql = "SELECT DISTINCT datum, ronde FROM svn_partijen WHERE comp_id = ".$_SESSION['competitie_session']." ORDER BY ronde DESC";
        $result = mysql_query($sql);
        for($a = 0; $a < mysql_num_rows($result); $a++)
        {
            $row = mysql_fetch_array($result);
            //print_r($_SESSION);
            //Laatste ronde als standaard laden indien $_SESSION['ronde'] nog niet bestaat
            if((!isset($_SESSION['ronde']) || $_SESSION["ronde"] == "") && $a == 0)
                $_SESSION["ronde"] = $row["ronde"];
            
            $selected = "";
            if($row['ronde'] == $_SESSION['ronde'])
                $selected = "SELECTED";
            echo "<OPTION value=\"".$row['ronde']."_".$row['datum']."\" ".$selected.">".$row['ronde']." - ".$row['datum'];
        }
        echo "</SELECT></form>";        
        
        $partijen = $competitie->partijen($_SESSION['competitie_session'], $_SESSION['ronde'], $_SESSION['ronde']);
        ?>
        <table><TR><TD>Witspeler<TD>Zwartspeler<TD>Uitslag
        <?
            $uitslagen[] = array(1,"1-0");
            $uitslagen[] = array(2,"&#189;-&#189;");
            $uitslagen[] = array(3,"0-1");   
                  
        foreach($partijen["partijen"] as $partij)
        {
            echo "<TR><TD>".$partijen["spelers"][$partij["speler_wit"]]["naam"]."<TD>- ".$partijen["spelers"][$partij["speler_zwart"]]["naam"]."<TD>";
            echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\"><input type=\"hidden\" name=\"partij_id\" value =\"".$partij['id']."\"><SELECT name=\"uitslag\" onChange='this.form.submit();'><OPTION value=\"NULL\">Geen";
           
            foreach($uitslagen as $uitslag_option)
            {
                if($partij["uitslag"] == $uitslag_option[0])
                    echo "<OPTION value=".$uitslag_option[0]." SELECTED>".$uitslag_option[1];
                else
                    echo "<OPTION value=".$uitslag_option[0].">".$uitslag_option[1];
            }
            echo "</SELECT></FORM>";
            $checked = "";
            if($partij["reglementair"] == 1)
                $checked = " checked";
            echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\"><input type=\"hidden\" name=\"partij_id\" value =\"".$partij['id']."\"><input type=\"hidden\" name=\"reglementair\" value=\"0\" /><input type=\"checkbox\" name=\"reglementair\" onChange='this.form.submit();' ".$checked." value=\"1\"> Reglementair</form>";

        }
        
        ?>
        
        
    </div>    
    

</body>
</html>

