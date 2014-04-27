<?php
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
  
  $competitie->rating_rapportage($_SESSION['competitie_session'], $_POST['van'], $_POST['tot']);
?>
