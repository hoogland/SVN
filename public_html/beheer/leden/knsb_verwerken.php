<?
 //WEBSITE STARTUP
  include_once('../class_login2.php');
  include('../class_database.php');
  $database = new database();
  $login = new login(1, 0);
  $login->main();
  
  include('../class_menu.php');
  $menu = new menu();
  
/*  $f = @fopen($_FILES["uploadedfile"]["tmp_name"],'r');
  if($f)
  {
          $line = fgets($f, 4096);
      while(!feof($f))
      {
          $line = fgets($f, 4096);
          $line = str_replace("\"","",$line);
          $line = str_replace("'","",$line);
          $data = explode(";",$line);
          //CONTROLEREN OF SPELER AL BESTAAT
          $sql = "SELECT * FROM svn_knsb WHERE id = ".$data[0];
          $result = mysql_query($sql);
          if(mysql_num_rows($result) == 1)      //UPDATEN KNSB RATING
          {
              if($data[4] != "")
              {
                $sql = "UPDATE svn_knsb SET rating_knsb = ".$data[4].")";
                mysql_query($sql);
              }
          }
          else
          {
              $sql = "INSERT INTO svn_knsb VALUES (\"";
              $sql .= implode("\",\"",$data);
              $sql .= "\")";
              mysql_query($sql);
          }
          if($data[1] == 1428) //SPELER VAN NIEUWERKERK
          {
              //CONTROLEREN OF SPELER IN LEDEN DB STAAT
              $sql = "SELECT * FROM svn_leden WHERE knsb = ".$data[0];
              $result = mysql_query($sql);
              if(mysql_num_rows($result) != 1)
              {
                  $sql = "INSERT INTO svn_leden VALUES (\"\",\"".$data[6]."\",\"".$data[4]."\",\"".$data[3]."\",\"".$data[0]."\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\")";
                  mysql_query($sql);
              }
              //ID SPELER OPHALEN
              $sql = "SELECT * FROM svn_leden WHERE knsb = ".$data[0];
              $result = mysql_query($sql);
              $speler = mysql_fetch_array($result);
                            
              //TOEVOEGEN HUIDIGE KNSB RATING
              $datum = "\"".substr($_FILES["uploadedfile"]["name"],3,4)."-".substr($_FILES["uploadedfile"]["name"],7,2)."-01\"";
                //KNSB
                if($data[11] != "")
                {
                    $sql = "INSERT INTO svn_rating VALUES ('',".$datum.",".$speler[0].",1,".$data[11].")";
                    mysql_query($sql);
                }
                //JEUGD
                if($data[12] != "")
                {
                    $sql = "INSERT INTO svn_rating VALUES ('',".$datum.",".$speler[0].",2,".$data[12].")";
                    mysql_query($sql);
                }
                
                //FIDE
                if($data[15] != "")
                {
                    $sql = "INSERT INTO svn_rating VALUES ('',".$datum.",".$speler[0].",3,".$data[15].")";
                    mysql_query($sql);
                }
          }
      }
  }
  */
  
      $filename = "http://drl.tuxtown.net/club.php?club=1428";
    $file = file($filename);
    $true = 0;
    $start = 0;
    for($a = 0; $a < count($file); $a++)
    {
        if(strstr($file[$a],"KNSB clubratinglijst"))
        {
            $string = explode(")",$file[$a]);
            $datum = substr($string[0],count($string) - 12);
        }
        if(strstr($file[$a],"positie"))
        {
            $id = $a;
        }
        
    }

    $speler = 1;
    while(!strstr($file[$id],"</table></td>"))
    {
        $row1 = $file[$id];
        $row2 = $file[$id + 1];
        $row1 = explode("id=",$row1);
        $speler_geg["id"] = substr($row1[1],0,7);
        
        //GEGEVENS SPELER OPHALEN
        $row2 = substr($row2, 37 + strlen($speler));
        $speler_geg["naam"] = substr($row2,0,strpos($row2,"</td>"));
        $row2 = substr($row2,strpos($row2,"</td>"));

        $row2 = explode("green>",$row2);
        $speler_geg["rating"] = substr($row2[1],0,strpos($row2[1],"<"));
        
        $sql = "SELECT * FROM svn_leden WHERE knsb = ".$speler_geg["id"];
        $result = mysql_query($sql);
        $speler_dat = mysql_fetch_array($result);
        
        if($speler_geg["rating"] != "")
        {
            $sql = "INSERT INTO svn_rating VALUES ('',\"".$datum."\",".$speler_dat[0].",1,".$speler_geg["rating"].")";
            //echo $sql;
            mysql_query($sql);
        }

        $id = $id + 2;
        $speler++; 
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
        <h1>KNSB ledenbestand verwerkt!</h1>        
        
    </div>    
    

</body>
</html>

