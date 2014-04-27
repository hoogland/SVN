<?
 //WEBSITE STARTUP
  include_once('../class_login2.php');
  include('../class_database.php');
  $database = new database();
  $login = new login(1, 0);
  $login->main();
  
  include('../class_menu.php');
  $menu = new menu();
  
  if(isset($_GET['competitie']))
    $_GET['id'] = $_GET['competitie'];
  
  if(isset($_POST['actie']) && $_POST['actie'] != "")
  {
      if($_POST['actie'] == "verwijderen")
      {
          $sql = "DELETE FROM svn_leden WHERE id = ".$_POST['speler_id'];
          mysql_query($sql);
      }
      if($_POST['actie'] == "wijzigen")
      {
          $sql = sprintf("UPDATE svn_leden SET voorletters = '%s', tussenvoegsel = '%s', achternaam = '%s', knsb = '%s', adres = '%s', postcode = '%s', plaats = '%s', telefoon = '%s', geslacht = '%s', type_lid = '%s', geb_dat = '%s', email = '%s' WHERE id = ".$_POST['speler_id'],$_POST['voorletters'],$_POST['tussenvoegsel'],$_POST['achternaam'],$_POST['knsb'],$_POST['adres'],$_POST['postcode'],$_POST['plaats'],$_POST['telefoon'],$_POST['geslacht'],$_POST['type_lid'],$_POST['geb_dat'],$_POST['email']);
          mysql_query($sql);
      }
      header('location: http://www.svnieuwerkerk.nl/beheer/leden/main.php');exit();
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
        $table      =    "svn_leden";
        $rows       =     "id, voorletters, tussenvoegsel, achternaam, knsb, adres, postcode, plaats, telefoon, geslacht, type_lid, geb_dat, email";
        $where      =    "id = ".$_GET['id'];
        $type       =    array('number','text','number','number');
        $array      =    $database-> extraction($table, $rows, $where,$type);
        ?>
        <h1>Competitie <? echo $array[0][1];?></h1>
        
        <form action="lid.php" method="post" name="myform">
            <input type="hidden" name="speler_id" value="<? echo $_GET['id'];?>">
            <input type="hidden" name="actie" value="">
            Voorletters: <input type="text" name="voorletters" value="<? echo $array[0][1];?>"><br>
            Tussenvoegsel: <input type="text" name="tussenvoegsel" value="<? echo $array[0][2];?>"><br>
            Achternaam: <input type="text" name="achternaam" value="<? echo $array[0][3];?>"><br>
            KNSB: <input type="text" name="knsb" value="<? echo $array[0][4];?>"><br>
            adres: <input type="text" name="adres" value="<? echo $array[0][5];?>"><br>
            postcode: <input type="text" name="postcode" value="<? echo $array[0][6];?>"><br>
            plaats: <input type="text" name="plaats" value="<? echo $array[0][7];?>"><br>
            telefoonnr: <input type="text" name="telefoon" value="<? echo $array[0][8];?>"><br>
            geslacht: <select name="geslacht"><option value="1" <? if($array[0][9] == 1) echo "SELECTED";?>>man<option value="2" <? if($array[0][9] == 2) echo "SELECTED";?>>Vrouw</select><br>
            type lid: <select name="type_lid"><option value="1" <? if($array[0][10] == 1) echo "SELECTED";?>>Senior lid<option value="2" <? if($array[0][10] == 2) echo "SELECTED";?>>Aspirant lid<option value="2" <? if($array[0][10] == 3) echo "SELECTED";?>>Senior dubbellid<option value="2" <? if($array[0][10] == 4) echo "SELECTED";?>>Erelid</select><br>
            geboortedatum: <input type="text" name="geb_dat" value="<? echo $array[0][11];?>"> <i>(jjjj-mm-dd)</i><br>
            emailadres: <input type="text" name="email" value="<? echo $array[0][12];?>"><br>
            <input type="submit" value="Wijzigen" onclick="verzend(0); return confirm('Wijzigen?')">    
            <input type="submit" value="Verwijderen" onclick="verzend(1); return confirm('Lid verwijderen? Let op! dit kan niet ongedaan worden gemaakt!')">    
        </form>
    </div>    
    

</body>
</html>

