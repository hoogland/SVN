<?
 //WEBSITE STARTUP
  include_once('../class_login2.php');
  include('../class_menu.php');

  $login = new login(0, 2);
  $login->main();
  
  $menu = new menu();
  
  $pgn = $_GET['pgn'];
  $title = $_GET['title'];
  
  //OPSCHONEN PGN
  $pgn = str_replace("\r\n", " ", $pgn);
  $pgn = str_replace("'", "\'", $pgn);
  $pgn = str_replace("\"", "%22", $pgn);
  $pgn = str_replace("  "," ", $pgn);
  
  $link = "http://www.svnieuwerkerk.nl/archief/games/games_embed.php?pgn=".$pgn;
  if($title != "")
  {
    $link .= "&title=".$title;
    $iframe = "<p><iframe src=\"".$link."\" frameborder=\"0\" scrolling=\"no\" width=\"100%\" height=\"425px\"></iframe></p>";
  }
  else
    $iframe = "<p><iframe src=\"".$link."\" frameborder=\"0\" scrolling=\"no\" width=\"100%\" height=\"385px\"></iframe></p>";
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
        <h1>Game Viewer</h1>
        Gemaakt om eenvoudig de gameviewer voor de site aan te maken.<BR><BR>
        <form action="game_viewer.php">Titel (optioneel)<input type="text" name="title"><BR>PGN van wedstrijd: <TEXTAREA name="pgn"></TEXTAREA><BR><input type="submit" value="Aanmaken">
        
        <?
            if($pgn != "")
            {
                echo "<BR>Link: <Textarea>".$link."</Textarea>";
                echo "<BR>Iframe: <Textarea>".$iframe."</Textarea>";
                echo "<BR>".$iframe;
            }
    
?>
    </div>    
    

</body>
</html>

