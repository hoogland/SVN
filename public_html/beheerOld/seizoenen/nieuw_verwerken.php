<?
 //WEBSITE STARTUP
  include_once('../class_login2.php');
  include('../class_database.php');
  $database = new database();
  $login = new login(1, 0);
  $login->main();
  
  if(!isset($_POST["naam"]) || $_POST["naam"] == "")
  {
    header('location: http://www.svnieuwerkerk.nl/beheerOld/competitie/nieuw.php');
    exit();
  }
  else
  {
      $sql = sprintf("INSERT INTO svn_seizoen (naam) VALUES (\"%s\")",$_POST["naam"]);
      $result = mysql_query($sql);
  //    echo $sql;
      header('location: http://www.svnieuwerkerk.nl/beheerOld/seizoenen/main.php?menu=1');
  }
  
?>