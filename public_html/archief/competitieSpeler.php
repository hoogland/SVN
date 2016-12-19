<?php
    //WEBSITE STARTUP
    include_once('../../includes/class.settings.php');
    include_once('../../includes/competition.php');
    include_once('../../includes/class.swiss.php');
    include_once('../../includes/class.player.php');

    $swiss = new swiss();
    $settings = new settings();


    $competitie = new competition($settings, $_GET['competitie']);
    $competitie->getGeneralData();

    $roundSelect =  $competitie->rounds[count($competitie->rounds) - 1];

    if($_GET["ronde"])
    {
        foreach($competitie->rounds as $comp)
        {
            if($comp["ronde"] == $_GET["ronde"])
                $roundSelect = $comp; 
        }
    }

    $competitie->getStanding(array(1,$roundSelect["ronde"]));
    include_once('../../includes/header.archief.php');
?>

<body class="container">

    <?php 
        include("../../includes/menu.archief.php");
    ?>       

    <h1 class="hidden-print">Competitie</h1>

    <div id="competitie" >
        <div id="competitieMenu">
            <div class="row hidden-print">
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
                <div class="col-md-2 hidden-print">
                    <h2>Rondes</h2>
                    <ul id="rondes">
                        <?php
                            foreach($competitie->rounds as $round)
                            {
                                echo "<li><a href=\"standen.php?seizoen=".$competitie->season."&competitie=".$competitie->id."&ronde=".$round["ronde"]."\">".$round["ronde"]." - ".date("d-m-Y",strtotime($round["datum"]))."</a></li>";
                            }
                        ?>
                    </ul>
                </div>
                <div class="col-md-10">
                <?php
                    $player = new player($settings, $_GET["spelerId"]);
                    $player->getDetails();
                ?>
                    <h2>Partijen: <?php echo "<a href=\"competitieSpeler.php?seizoen=".$_GET["seizoen"]."&competitie=".$_GET["competitie"]."&spelerId=".$player->id."\">".$player->name."</a>"?></h2>
                    <table class="table table-striped table-hover">
                        <thead><tr><th>Datum</th><th>Witspeler</th><th></th><th>Zwartspeler</th><th>Uitslag</th></tr></thead>
                        <?php
                            foreach($competitie->getMatches(null, $_GET["spelerId"]) as $match)
                            {
                                // print_r($match);
                                $playerWhite = new player($settings, $match["speler_wit"]);
                                $playerWhite->getDetails();
                                $playerBlack = new player($settings, $match["speler_zwart"]);
                                $playerBlack->getDetails();
                                $uitslag;
                                switch($match["uitslag"])
                                {
                                    case "1" : $uitslag = "1 - 0";break;
                                    case "2" : $uitslag = "&#189;-&#189;";break;
                                    case "3" : $uitslag = "0 - 1";break;
                                }

                                echo "<tr><td>".date("d-m-Y",strtotime($match["datum"]))."</td><td><a href=\"competitieSpeler.php?seizoen=".$_GET["seizoen"]."&competitie=".$_GET["competitie"]."&spelerId=".$playerWhite->id."\">".$playerWhite->name."</a> (".$match["rating_wit"].")</td><td>-</td><td><a href=\"competitieSpeler.php?seizoen=".$_GET["seizoen"]."&competitie=".$_GET["competitie"]."&spelerId=".$playerBlack->id."\">".$playerBlack->name."</a> (".$match["rating_zwart"].")</td><td>".$uitslag."</td></tr>";

                            }
                        ?>

                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
    </body>
</html>

