<?
 //WEBSITE STARTUP
  include_once('../class_login2.php');
  include_once('../class_database.php');
  $database = new database();
  $login = new login(1, 0);
  $login->main();
  
  if(!isset($_POST["naam"]) || $_POST["naam"] == "" || !isset($_SESSION['seizoen']))
  {
    header('location: http://www.svnieuwerkerk.nl/beheer/competitie/nieuw.php');
    exit();
  }
  else
  {
      $sql = sprintf("INSERT INTO svn_competities (naam, type_comp, stand_tempo, seizoen_id, naam_uitgebreid, plaats, land, wedstrijdleider, speeltempo) VALUES (\"%s\",".$_POST['type_comp'].",".$_POST['stand_tempo'].",".$_SESSION['seizoen'].",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\")",$_POST["naam"],$_POST["naam_uitgebreid"],$_POST["plaats"],$_POST["land"],$_POST["wedstrijdleider"],$_POST["speeltempo"]);
      $result = mysql_query($sql);
     // echo $sql;
      header('location: http://www.svnieuwerkerk.nl/beheer/competitie/main.php');
  }
  
?>