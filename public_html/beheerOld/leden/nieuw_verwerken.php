<?
 //WEBSITE STARTUP
  include_once('../class_login2.php');
  include('../class_database.php');
  $database = new database();
  $login = new login(1, 0);
  $login->main();
  
  if(!isset($_POST["knsb"]) || $_POST["knsb"] == "")
  {
    header('location: http://www.svnieuwerkerk.nl/beheerOld/leden/nieuw.php');
    exit();
  }
  else
  {                         //INSERT WIJZIGEN
    $sql = sprintf("INSERT INTO svn_leden (voorletters, tussenvoegsel, achternaam, knsb, adres, postcode, plaats, telefoon, geslacht, type_lid, geb_dat, email) VALUES (\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\")",$_POST['voorletters'], $_POST['tussenvoegsel'], $_POST['achternaam'], $_POST['knsb'], $_POST['adres'], $_POST['postcode'], $_POST['plaats'], $_POST['telefoon'], $_POST['geslacht'], $_POST['type_lid'], $_POST['geb_dat'], $_POST['email']);
    //  $sql = sprintf("INSERT INTO svn_leden (naam, type_comp, stand_tempo, seizoen_id) VALUES (\"%s\",".$_POST['type_comp'].",".$_POST['stand_tempo'].",".$_SESSION['seizoen'].")",$_POST["naam"]);
      $result = mysql_query($sql);
    //  echo $sql;
      header('location: http://www.svnieuwerkerk.nl/beheerOld/leden/main.php');
  }
  
?>