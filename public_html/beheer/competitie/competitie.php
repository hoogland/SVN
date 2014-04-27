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
  
  if(isset($_GET['competitie']))
    $_GET['id'] = $_GET['competitie'];
  
  if(isset($_POST['actie']) && $_POST['actie'] != "")
  {
      if($_POST['actie'] == "verwijderen")
      {
          $sql = "DELETE FROM svn_competities WHERE id = ".$_POST['competitie_id'];
          mysql_query($sql);
      }
      if($_POST['actie'] == "wijzigen")
      {
          $sql = sprintf("UPDATE svn_competities SET naam = '%s', type_comp = ".$_POST['type_comp'].", stand_tempo = ".$_POST['stand_tempo'].", naam_uitgebreid = '%s', plaats = '%s', land = '%s', wedstrijdleider = '%s', speeltempo = '%s' WHERE id = ".$_POST['competitie_id'],$_POST['naam'],$_POST['naam_uitgebreid'],$_POST['plaats'],$_POST['land'],$_POST['wedstrijdleider'],$_POST['speeltempo']);
          mysql_query($sql);
      }
      header('location: http://www.svnieuwerkerk.nl/beheer/competitie/main.php');exit();
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
   <SCRIPT LANGUAGE = "JavaScript">
   <!--
   
   function verzend(type)
   {
       if(type == 0)
           document.myform.actie.value = 'wijzigen';
       if(type == 1)
           document.myform.actie.value = 'verwijderen';
   }
   
   //-->
   </SCRIPT>
</head>

<body>

    <? $menu->menu_main($login->LOGGED_IN);?>        
    
        
    <div id="text">
        <?
        $competitie_gegevens = $competitie->competitie_gegevens($_GET['id']);
        ?>
        <h1>Competitie <? echo $array[0][1];?></h1>
        
        <form action="competitie.php" method="post" name="myform">
            <input type="hidden" name="competitie_id" value="<? echo $_GET['id'];?>">
            <input type="hidden" name="actie" value="">
            Naam competitie: <input type="text" name="naam" value="<? echo $competitie_gegevens["naam"];?>"><br>
            Naam uitgebreid (Rating rapportage): <input type="text" name="naam_uitgebreid" value="<? echo $competitie_gegevens["naam_uitgebreid"];?>"><br>
            Type competitie: <select name="type_comp"><option value="1" <? if($competitie_gegevens["type_comp"] == 1) echo "SELECTED";?>>Interne competitie<option value="2" <? if($competitie_gegevens["type_comp"] == 2) echo "SELECTED";?>>Externe competitie</select><br>
            Tempo competitie: <select name="stand_tempo"><option value="1" <? if($competitie_gegevens["stand_tempo"] == 1) echo "SELECTED";?>>5 min. p.p.p.p.<option value="2" <? if($competitie_gegevens["stand_tempo"] == 2) echo "SELECTED";?>>45 min p.p.p.p. (Rapid)<option value="3" <? if($competitie_gegevens["stand_tempo"] == 3) echo "SELECTED";?>>1:30u + 15 (Lang)</select><br>
            Tempo getypt:  <input type="text" name="speeltempo" value="<? echo $competitie_gegevens["speeltempo"];?>"><br>
            Wedstrijdleider + email:  <input type="text" name="wedstrijdleider" value="<? echo $competitie_gegevens["wedstrijdleider"];?>"><br>
            Plaats  <input type="text" name="plaats" value="<? echo $competitie_gegevens["plaats"];?>"><br>
            Land  <input type="text" name="land" value="<? echo $competitie_gegevens["land"];?>"><br>
            <input type="submit" value="Wijzigen" onclick="verzend(0); return confirm('Wijzigen?')">    
            <input type="submit" value="Verwijderen" onclick="verzend(1); return confirm('Seizoen verwijderen? Let op! dit kan niet ongedaan worden gemaakt!')">    
        </form>
    </div>    
    

</body>
</html>

