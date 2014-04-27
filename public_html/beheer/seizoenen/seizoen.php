<?
 //WEBSITE STARTUP
  include_once('../class_login2.php');
  include('../class_database.php');
  $database = new database();
  $login = new login(1, 0);
  $login->main();
  
  include('../class_menu.php');
  $menu = new menu();
  
  //WIJZIGEN + VERWIJDEREN SEIZOEN
  if(isset($_POST['actie']) && $_POST['actie'] != "" && $_POST['actie'] != "kampioenen")
  {
      if($_POST['actie'] == "verwijderen")
      {
          $sql = "DELETE FROM svn_seizoen WHERE id = ".$_POST['seizoen_id'];
          mysql_query($sql);
      }
      if($_POST['actie'] == "naam")
      {
          $sql = sprintf("UPDATE svn_seizoen SET naam = '%s' WHERE id = ".$_POST['seizoen_id'],$_POST['naam']);
          mysql_query($sql);
      }
      header('location: http://www.svnieuwerkerk.nl/beheer/seizoenen/main.php');exit();
  }
  
  //WIJZIGEN KAMPIOENEN
  if(isset($_POST['actie']) && $_POST['actie'] == "kampioenen")
  {
      $seizoen = $_POST["seizoen_id"];
      $_GET['id'] = $seizoen;
      unset($_POST["seizoen_id"]);
      unset($_POST["actie"]);
      
      foreach($_POST as $type => $kampioen)
      {
          //CONTROLEREN OF ER AL EEN KAMPIOEN IS
          $sql = "SELECT * FROM svn_kampioenen WHERE seizoen_id = ".$seizoen." AND type_comp = ".$type;
          $result = mysql_query($sql);
          if(mysql_num_rows($result) == 0)      //GEEN KAMPIOEN AANWEZIG => AANMAKEN
          {
            if($kampioen != "")
            {
                $sql = "INSERT INTO svn_kampioenen (seizoen_id, speler_id, type_comp) VALUES (".$seizoen.",".$kampioen.",".$type.");";
                $result = mysql_query($sql);
            }    
          }
          else                                  //KAMPIOEN AANWEZIG => WIJZIGEN
          {
            if($kampioen == "")                 //KAMPIOEN WISSEN
            {
                $sql = "DELETE FROM svn_kampioenen WHERE seizoen_id = ".$seizoen." AND type_comp = ".$type."";
                $result = mysql_query($sql);
            } 
            else                                //UPDATEN
            {
                $sql = "UPDATE svn_kampioenen SET speler_id = ".$kampioen." WHERE  seizoen_id = ".$seizoen." AND type_comp = ".$type."";
                $result = mysql_query($sql);
            }   
          }
      }
  }
  
  
      function spelers($id)
    {
        echo "<OPTION>";
        $sql = "SELECT * FROM svn_leden ORDER BY achternaam";
        $result = mysql_query($sql);
        for($a = 0; $a < mysql_num_rows($result); $a++)
        {
            $row = mysql_fetch_array($result);
            $selected = "";
            if($row['id'] == $id)
                $selected = "SELECTED";
            echo "<OPTION value=\"".$row['id']."\" ".$selected.">".$row['achternaam'].", ".$row['voorletters']." ".$row['tussenvoegsel']." ";
        }
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
           document.myform.actie.value = 'naam';
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
        $table      =    "svn_seizoen";
        $rows       =     "id,naam";
        $where      =    "id = ".$_GET['id'];
        $type       =    array('number','text');
        $array      =    $database-> extraction($table, $rows, $where,$type);
        ?>
        <h1>Seizoen <? echo $array[0][1];?></h1>
        
        <form action="seizoen.php" method="post" name="myform">
            <input type="hidden" name="seizoen_id" value="<? echo $_GET['id'];?>">
            <input type="hidden" name="actie" value="">
            <input type="text" name="naam">
            <input type="submit" value="Naam wijzigen" onclick="verzend(0); return confirm('Naam wijzigen?')">    
            <input type="submit" value="Verwijderen" onclick="verzend(1); return confirm('Seizoen verwijderen? Let op! dit kan niet ongedaan worden gemaakt!')">    
        </form>
        
        <?

        //KAMPIOENEN SELECTEREN
        $table      =    "svn_kampioenen";
        $rows       =     "id,speler_id, type_comp";
        $where      =    "seizoen_id = ".$_GET['id'];
        $type       =    array('number','number','number');
        $array      =    $database-> extraction($table, $rows, $where,$type);


        foreach($array as $kampioen)
            $kampioenen[$kampioen["2"]] = $kampioen["1"];

        ?>
        
        <h2>Kampioenen</h2>
        <form action="seizoen.php" method="post">
            <input type="hidden" name="seizoen_id" value="<? echo $_GET['id'];?>">
            <input type="hidden" name="actie" value="kampioenen">
            Interne competitie <select name="1"><? spelers($kampioenen[1]);?></select><br>
            Beker competitie  <select name="2"><? spelers($kampioenen[2]);?></select><br>
            Snelschaak competitie  <select name="3"><? spelers($kampioenen[3]);?></select><br>
            Rapid competitie  <select name="4"><? spelers($kampioenen[4]);?></select><br>  
            Interne competitie jeugd <select name="5"><? spelers($kampioenen[5]);?></select><br>
            Beker competitie jeugd  <select name="6"><? spelers($kampioenen[6]);?></select><br>
            Snelschaak competitie jeugd  <select name="7"><? spelers($kampioenen[7]);?></select><br>
            <input type="submit" value="Invoeren / Wijzigen">
        </form>
    </div>    
    

</body>
</html>

