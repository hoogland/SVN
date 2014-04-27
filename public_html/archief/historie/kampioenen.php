<?
 //WEBSITE STARTUP
  include_once('../database.inc');
  include('../class_menu.php');
  $menu = new menu();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
<head>
   <title>SV Nieuwerkerk | Archief</title>
   
   <meta name="author" content="Rob Hoogland" />
   <meta name="copyright" content="&copy; 2010 svnieuwerkerk.nl" />
   <meta name="description" content="Welkom - archiefsite SV Nieuwerkerk" />
   <meta name="keywords" content="SV Nieuwerkerk, svnieuwerkerk, svn, schaken, archief" />
   <meta name="robots" content="index,nofollow" />
    
   <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />

   <link rel="stylesheet" type="text/css" href="../style.css" />
 <link href="../rrip.css" rel="stylesheet" type="text/css"> 



</head>

<body>

    <? $menu->menu_main($login->LOGGED_IN);
       $menu->menu_historie();
    ?>            
        
    <div id="text">
        <h1>Kampioenen</h1>
        <table>
            <TR><TD>Seizoen<TD>Intern <TD>Beker <TD>Snelschaken <TD>Rapid <TD>Intern jeugd <TD>Beker jeugd <TD> Snelschaken jeugd
    <?
    //COMPETITIES SELECTEREN
    $sql = "SELECT * FROM svn_seizoen WHERE id IN (SELECT seizoen_id FROM svn_kampioenen) ORDER BY naam ASC";
    $result = mysql_query($sql);
    
    for($a = 0; $a < mysql_num_rows($result); $a++)
    {
        $row = mysql_fetch_array($result);
        echo "<TR><TD>".substr($row["naam"],0,5)."".substr($row["naam"],7,2);
        for($b = 1; $b < 8; $b++)
        {
            echo "<TD>";
            
            $sql2 = "SELECT * FROM svn_leden WHERE id IN (SELECT speler_id FROM svn_kampioenen WHERE seizoen_id = ".$row["id"]." AND type_comp = ".$b.")";
            $result2 = mysql_query($sql2);
            $row2 = mysql_fetch_array($result2);
            echo $row2["achternaam"].", ".$row2["voorletters"]." ".$row2["tussenvoegsel"];
        }
        
    }
    
    ?>     
        </table>                                                  
    </div>    
    

</body>
</html>

