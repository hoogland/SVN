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
    $competitie_geg = $competitie->competitie_gegevens($_GET['competitie']);


    //VERWERKEN NIEUWE PARTIJ
    if((isset($_GET['partij']) OR (isset($_GET["player_white"]) && $_GET["player_white"] != "" && isset($_GET["player_black"]) && $_GET["player_black"] != "")) && $_GET['action'] == "")
    {
        $datum =  $database->mysql_datum($_GET['datum']);
        $spelers = explode("_",$_GET['partij']);
        if(isset($_GET["player_white"]) && $_GET["player_white"] != "" && isset($_GET["player_black"]) && $_GET["player_black"] != "")
            $spelers = array($_GET["player_white"], $_GET["player_black"]);
        $speler_wit = $competitie->speler_gegevens($spelers[0]);
        $speler_zwart = $competitie->speler_gegevens($spelers[1]);
        $competitie_geg = $competitie->competitie_gegevens($_SESSION['competitie_session']);
        if($_GET['ronde_nr'] == "Nieuw" || $_GET['ronde'] == "" )
            $ronde = $competitie_geg["laatste_ronde"] + 1;
        else
        {
            $ronde = explode("_",$_GET['ronde']);
            $ronde = $ronde[0];
        }
        $sql = "INSERT INTO svn_partijen (speler_wit, rating_wit, speler_zwart, rating_zwart, tempo, comp_id, datum, ronde) VALUES (".$spelers[0].",".$speler_wit["rating"].",".$spelers[1].",".$speler_zwart["rating"].", ".$competitie_geg['stand_tempo'].",".$_SESSION['competitie_session'].",'".$datum."',".$_GET['ronde_nr'].")";
        mysql_query($sql); 
        unset($_GET['partij']);
        unset($_GET['player_white']);
        unset($_GET['player_black']);
    }
    if($_GET['action'] == "delete")
    {
        $sql = "DELETE FROM svn_partijen WHERE id = ".$_GET['partij'];
        mysql_query($sql);
        unset($_GET['action']);
        unset($_GET['partij']);
        unset($_GET['datum']);
        unset($_GET['ronde_nr']);
    }

    //DATUM BIJ GELECTEERDE RONDE NEERZETTEN
    if(isset($_GET['ronde']))
    {
        $ronde = explode("_",$_GET['ronde']);
        $datum_ronde = $database->datum($ronde[1]);
        $datum_ronde = str_replace("-","/",$datum_ronde);
        $ronde = $ronde[0];
        if($ronde == "Nieuw")
        {
            $competitie_geg = $competitie->competitie_gegevens($_SESSION['competitie_session']);
            $ronde = count($competitie_geg["speeldata"]) + 1;
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

        <script type="text/javascript" language="javascript" src="../prototype.js" ></script> 
    <script type="text/javascript" language="javascript" src="../html-form-input-mask.js"></script> </HEAD>
    <link rel="stylesheet" type="text/css" href="../style.css" />
    <script type="text/javascript" src="../bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.css" type="text/css" />
    </head>

    <body  onload="Xaprb.InputMask.setupElementMasks()">

        <? $menu->menu_main($login->LOGGED_IN);?>        


        <div id="text">
            <h1>Indeling - <? echo $_SESSION['competitie_naam']; ?></h1>
            <?
                echo "<form method=\"get\" action=\"".$_SERVER['PHP_SELF']."\">";
                foreach($_GET as $key => $value)
                {
                    if($key != "ronde");
                    echo "<input type=\"hidden\" name=\"".$key."\" value=\"".$value."\">";
                }

                echo "Ronde: <SELECT name=\"ronde\" onChange='this.form.submit();'><OPTION>Nieuw";        

                $sql = "SELECT DISTINCT datum, ronde FROM svn_partijen WHERE comp_id = ".$_SESSION['competitie_session']." ORDER BY ronde DESC";
                $result = mysql_query($sql);
                for($a = 0; $a < mysql_num_rows($result); $a++)
                {
                    $row = mysql_fetch_array($result);
                    $selected = "";
                    if($row['ronde'] == $_SESSION['ronde'])
                        $selected = "SELECTED";
                    echo "<OPTION value=\"".$row['ronde']."_".$row['datum']."\" ".$selected.">".$row['ronde']." - ".$row['datum'];
                }
                echo "</SELECT></form>";

                echo "<form method=\"get\" action=\"".$_SERVER['PHP_SELF']."\">";
                foreach($_GET as $key => $value)
                {
                    if($key != "partij");
                    echo "<input type=\"hidden\" name=\"".$key."\" value=\"".$value."\">";
                }

                echo "Partij: <SELECT name=\"partij\">";
                $deelnemers = $competitie->spelers_deelname($_SESSION['competitie_session']);
                $partijen = $competitie->partijen($_SESSION['competitie_session']);

                foreach($deelnemers as $sleutel => $deelnemer)
                {
                    $plaats = $deelnemers[$sleutel]["plaats"];
                    $aantal_spelers = count($deelnemers);
                    $speler_gegevens = $deelnemers[$sleutel];
                    if(count($deelnemers) > 0)
                    {

                        foreach($deelnemers as $key => $tegenstander)
                        {
                            $kleuren = $competitie->kleur(array(array($sleutel,$deelnemers[$sleutel]["plaats"]), array($key,$deelnemers[$key]["plaats"])),$aantal_spelers);
                            $print = true;          
                            foreach($partijen['partijen'] as $partij)
                            {
                                if(($partij["speler_wit"] == $key && $partij["speler_zwart"] == $sleutel) || ($partij["speler_zwart"] == $key && $partij["speler_wit"] == $sleutel))
                                    $print = false;
                            } 
                            if($print && $key != $sleutel && $sleutel == $kleuren["wit"])
                            {echo "<option value=".$kleuren["wit"]."_".$kleuren["zwart"].">".$deelnemers[$kleuren["wit"]]["naam"]." - ".$deelnemers[$kleuren["zwart"]]["naam"]."";}
                        }
                    }    
                }

                echo "</SELECT><br>Partij Handmatig:";
                echo "<SELECT name=\"player_white\"><OPTION></OPTION>";
                foreach($deelnemers as $key => $deelnemer)
                    echo "<option value=\"".$key."\">".$deelnemer["naam"];
                echo "</SELECT> - <SELECT name=\"player_black\"><OPTION></OPTION>";
                foreach($deelnemers as $key => $deelnemer)
                    echo "<option value=\"".$key."\">".$deelnemer["naam"];
            ?>        
            </select><br>Datum: <input type="text" name="datum" class='text input_mask mask_date_us' value="<? echo $datum_ronde;?>">(dd/mm/jjjj)
            <br>Ronde: <input type="text" name="ronde_nr" value="<? echo $ronde;?>"><input type="submit" value="Invoeren"></form>
            <?
                $partijen = $competitie->partijen($_SESSION['competitie_session'], $_SESSION['ronde'], $_SESSION['ronde']);
            ?>
            <table><TR><TD>Witspeler<TD>Zwartspeler<TD>Uitslag
            <?
                foreach($partijen["partijen"] as $partij)
                {
                    echo "<TR><TD>".$partijen["spelers"][$partij["speler_wit"]]["naam"]."<TD>- ".$partijen["spelers"][$partij["speler_zwart"]]["naam"]."<TD>";
                    switch($partij["uitslag"])
                    {
                        case 1: echo "1-0";             break;
                        case 2: echo "&#189;-&#189;";   break;
                        case 3: echo "0-1";             break;
                    }
                    echo "<a href=\"indeling.php?action=delete&partij=".$partij["id"]."\">Verwijderen</a>";

                }
            ?>


        </div>    


    </body>
</html>

