<?php
   //WEBSITE STARTUP
  include_once('class_login2.php');
  $login = new login(0, 0);
  $login->main();
  
  include('class_competitie.php');
  $competitie = new competitie();
  
  
  //MENU AANMAKEN
  ?>
<HTML> 
<HEAD> 
<TITLE></TITLE> 
<link href="http://www.svnieuwerkerk.nl/beheer/rrip.css" rel="stylesheet" type="text/css"> 
</HEAD> 

  <div id="menu">
    <ul>
        <li><a href="site.php?competitie=<? echo $_GET['competitie'];?>&action=spelers">Spelerslijst</a> 
        <li><a href="site.php?competitie=<? echo $_GET['competitie'];?>">Rangschikking</a> 
        <li><a href="site.php?competitie=<? echo $_GET['competitie'];?>&action=xref">Xref</a> 
    </ul>  
    Standen + Uitslagen
    <ul>
  
    <?
        $sql = "SELECT MAX(ronde) FROM svn_partijen WHERE comp_id = ".$_GET['competitie'];
        $result = mysql_query($sql);
        $row = mysql_fetch_array($result);
        
        for($a = $row[0]; $a > 0; $a--)
        {
            echo "<li><a href=\"site.php?competitie=".$_GET['competitie']."&ronde=".$a."\">Ronde ".$a."</a>";
        }
    
    ?>  
  
    </ul>
  </div>
  <div id="ranglijst">
  <?
  if($_GET['action'] == "xref")
    $competitie->xref($_GET['competitie']);
  elseif($_GET['action'] == "spelers")
  {
      $spelers = $competitie->spelers($_GET['competitie']);
      echo "<TABLE>";
      echo "<TR class=\"eerste_rij\"><TD>KNSB<TD>Naam";
      foreach($spelers as $speler)
        echo "<TR><TD>".$speler["knsb"]."<TD>".$speler["naam"];
      echo "</TABLE>";
  }
  elseif($_GET['action'] == "speler")
  {
      
  }
  else
  {
      //STAND PRINTEN
      $result = $competitie->partijen((int)$_GET['competitie'],(int)$_GET['ronde']);
      echo "<TABLE>";
      $competitie->stand_print($result["spelers"],$result["stand"],array('naam','PUNTEN','WP','SB','We','W-We','TPR'));
      echo "</TABLE>";   
      if(isset($_GET['ronde']))
        $competitie->partijen_print($result["spelers"],$result["partijen"],$_GET['ronde']);
  }
?>
</div>
