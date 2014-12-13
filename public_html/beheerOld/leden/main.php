<?
 //WEBSITE STARTUP
  include_once('../class_login2.php');
  include('../class_database.php');
  $database = new database();
  $login = new login(1, 0);
  $login->main();
  
  include('../class_menu.php');
  $menu = new menu();
  
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
        <h1>Ledenbestand</h1>
        <?
        $table      =    "svn_leden";
        $rows       =     "id,knsb,voorletters, tussenvoegsel,achternaam,email, type_lid";
        $where      =    "";
        $type       =    array('number','text','text','text','text');
        $array      =    $database-> extraction($table, $rows, $where,$type);
        
        for($a = 0; $a < count($array); $a++)
        {
            switch($array[$a][6])
            {
                case 1: $array[$a][6] = "Senior lid";break;
                case 2: $array[$a][6] = "Aspirant lid";break;
                case 3: $array[$a][6] = "Senior dubbellid";break;
                case 4: $array[$a][6] = "Erelid";break;
            }
        }

        $headers        = array('id','KNSB','Voorletter','Tussenvoegsel','Achternaam','Email','Type lid');  //Dient evenveel te zijn als $array
        $print_header   = 1;                                                                //Bij niet printen 1 bij wel printen
        $sort           = array(1,2,3,4,5,6,7);                                                    //Nummer rij vd extractie
        $ext_css        = 0;    //Bij wel wisselende opmaak 0 bij niet
        $visible        = array(0,1,1,1,1,1,1);    //bij 1 zichtbaar bij 0 onzichtbaar
        $link           = array('lid.php?id=!1#' , '2');    //0 bij link over volledige rij nr bij slechts 1    
        $database -> table($array, $headers, $print_header, $sort, $ext_css, $visible,$link);

        
        ?>
        
        
        
    </div>    
    

</body>
</html>

