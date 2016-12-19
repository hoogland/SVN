<?
    //WEBSITE STARTUP
    include_once('../class_login2.php');
    //include_once('../class_database.php');
    include_once('../class_menu.php');
    include_once('../../../includes/class.settings.php');
    include_once('../../../includes/competition.php');
    include_once('../../../includes/class.swiss.php');
    include_once('../../../includes/class.player.php');
    $swiss = new swiss();
    $settings = new settings();

    //$database = new database();
    $login = new login(1, 0);
    $login->main();

    $menu = new menu();
    $_GET["competitie"] = $_SESSION["competitie_session"];
    $competitie = new competition($settings, $_GET['competitie']);
    $competitie->getGeneralData();

    $competitie->getStanding();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
    <head>
        <title>SV Nieuwerkerk | Archief</title>

        <meta name="author" content="Rob Hoogland" />
        <meta name="copyright" content="&copy; 2010 jeugdschaken.nl" />
        <meta name="description" content="Welkom - mijn-2e-huis.nl" />
        <meta name="keywords" content="Share documents, School Project, information, file sharing" />
        <meta name="robots" content="index,nofollow" />

        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
        <script src="../../js/bootstrap.min.js"></script>
        <link rel="stylesheet" type="text/css" href="../style.css" />
        <link rel="stylesheet" type="text/css" href="../../css/style.css" />
        <link rel="stylesheet" type="text/css" href="../../css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="../../css/bootstrap-theme.min.css" />
        <link rel="stylesheet" type="text/css" href="../rrip.css" />



    </head>

    <body>

        <? $menu->menu_main($login->LOGGED_IN);
            // $menu->menu_competitie();
        ?>            

        <div id="text">
            <h1>Competitie</h1>
            <div id="competitie" class="container">
                <div id="competitieMenu">

                    <h2>Standen + Uitslagen</h2>
                    <div class="row">
                        <div id="competitieDetails" class="col-md-12">
                            <table class="table">
                                <tr><td>Competitie</td><td><?php echo $competitie->name;?></td></tr>
                                <tr><td>Tempo</td><td><?php echo $competitie->tempo;?></td></tr>
                                <tr><td>Paringsysteem</td><td></td></tr>
                                <tr><td>Plaats</td><td><?php echo $competitie->place;?></td></tr>
                                <tr><td>Land</td><td><?php echo $competitie->country;?></td></tr>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <h2>Rondes</h2>
                            <ul id="rondes">
                                <?
                                    foreach($competitie->getRounds() as $round)
                                    {
                                        echo "<li><a href=\"standen.php?competitie=".$competitie->id."&ronde=".$round["ronde"]."\">".$round["datum"]."</a></li>";
                                    }
                                ?>
                            </ul>
                        </div>
                        <div class="col-md-9">
                            <h2>Stand</h2>
                            <table class="table table-striped">
                                <?
                                    echo "<tr><thead>";
                                    foreach(explode(",",$competitie->options["DisplayData"]) as $field)
                                        echo "<th>".$field."</th>";
                                    echo "</thead></tr>";
                                    foreach($competitie->standings as $key => $data)
                                    {
                                        echo "<tr>";
                                        foreach(explode(",",$competitie->options["DisplayData"]) as $field)
                                        {
                                            if($field == "Ranking")
                                                echo "<td>".($key + 1)."</td>";
                                            elseif($field == "Name")
                                            {
                                                $player = new player($settings, $data["player"]);
                                                $player->getDetails();
                                                echo "<td>".$player->name."</td>";        
                                            }
                                            else
                                            {
                                                echo "<td>".$data[$field]."</td>";
                                            }
                                        }

                                        echo "</tr>";
                                        //echo "<tr><td>".$key."</td><td>".$data["score"]."</td><td>".$data["nrOpponents"]."</td><td>".$data["Percentage"]."</td><td>".$data["TPR"]."</td>";
                                    }

                                ?>

                            </table>

                        </div>
                    </div>
                </div>
            </div>
            <?

            ?>                                                       
        </div>    


    </body>
</html>

