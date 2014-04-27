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
  $result1 = $competitie->fide_naar_partijen($_POST['rapportage']);
  $stand = $competitie->stand($result[0],$result[1]);
  print_r($stand);
  $spelers;
  
  //GEGEVENS SPELERS BINNENHALEN
  foreach($result1[0] as $key=> $speler)
  {
      if($key != "")
      {
          if($speler['knsb'] != "")
          {
              $sql = "SELECT * FROM svn_leden WHERE knsb = ".$speler['knsb'];
              $result = mysql_query($sql);
              if(mysql_num_rows($result) == 1)
              {
                  $row = mysql_fetch_array($result);
                  $speler['id'] = $row['id'];
              }
              elseif(mysql_num_rows($result) == 0)         //SPELER BESTAAT NOG NIET
              {
                    echo "<BR>Invoeren speler: ".implode(" | ",$speler);    
              }
          }
          $spelers[$key] = $speler;
      }
  }
  
  //LAATSTE RONDE COMPETITIE BINNENHALEN
  $sql = "SELECT MAX(ronde) FROM svn_partijen WHERE comp_id = ".$_SESSION['competitie_session'];
  $result = mysql_query($sql);
  $ronde = 0;
  if(mysql_num_rows($result) == 1)
  {
      
      $row = mysql_fetch_array($result);
      $ronde = $row[0];
  }
  $ronde++;
  
  //STANDAARD TEMPO BINNENHALEN
  $sql = "SELECT stand_tempo FROM svn_competities WHERE id = ".$_SESSION['competitie_session'];
  $result = mysql_query($sql);
  $row = mysql_fetch_array($result);
  $stand_tempo = $row['stand_tempo'];
 
  
  foreach($result1[1] as $partij)
  {
      //RONDE BEREKENEN
      $ronde_insert = $ronde + $partij['ronde'];
      
      //PARTIJ RESULTATEN GOED NEERZETTEN
      $resultaat;
      switch($partij[2])
      {
          case 0: $resultaat = 3;break;
          case 0.5: $resultaat = 2;break;
          case 1: $resultaat = 1;break;
      }
      //DATUM CORRECT NEERZETTEN VOOR DB
      $datum = explode('.',$partij['datum']);
      $datum = "20".$datum[0]."-".$datum[1]."-".$datum[2];
      
      
      //CONTROLEREN OF PARTIJ AL BESTAAT
      $sql = "SELECT * FROM svn_partijen WHERE speler_wit = '".$spelers[$partij['0']]['id']."' AND rating_wit = '".$partij['rating_wit']."' and speler_zwart = '".$spelers[$partij['1']]['id']."' AND rating_zwart = '".$partij['rating_zwart']."' AND uitslag = ".$resultaat." AND tempo = ".$stand_tempo." AND comp_id = ".$_SESSION['competitie_session']." AND datum = '".$datum."' AND ronde = ".$ronde_insert."";
      $result = mysql_query($sql);
      if(mysql_num_rows($result) == 0)
      {
        //INSERT STATEMENT
        $sql = "INSERT INTO svn_partijen (speler_wit, rating_wit, speler_zwart, rating_zwart, uitslag, tempo, comp_id, datum, ronde) VALUES ('".$spelers[$partij['0']]['id']."','".$partij['rating_wit']."','".$spelers[$partij['1']]['id']."','".$partij['rating_zwart']."',".$resultaat.",".$stand_tempo.",".$_SESSION['competitie_session'].",'".$datum."',".$ronde_insert.")";
        $result = mysql_query($sql); 
      }
      header("Location: http://www.svnieuwerkerk.nl/beheer/competitie/uitslagen_fide.php");
  }
?>

