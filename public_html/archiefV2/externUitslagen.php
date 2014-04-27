<?
    //WEBSITE STARTUP
    include_once('../../includes/class.init.php');
    include_once('../../includes/class.player.php');
    include_once('../../includes/class.external.php');

    $init = new init();
    $settings = new settings();
    $external = new externalCompetition($settings);

    $roundSelect =  $competitie->rounds[count($competitie->rounds) - 1];

    include_once('../../includes/header.archief.php');
?>

<body class="container">

    <? 
        include("../../includes/menu.archief.php");
    ?>   
    <div class="row">
        <div class="col-md-12">
            <form action="externUitslagen.php" method="get" class="form-inline" role="form">
                <input type="hidden" name="seizoen" value="<? echo $init->repository->get_data("seizoen");?>">

                <div class="form-group">
                    Team: <select name="team" class="form-control" onchange="this.form.submit()">
                        <option value="">Selecteer een team</option>
                        <? 
                            foreach($external->getTeams() as $team)
                            {
                                $selected = "";
                                if($team["team"] == $_GET["team"])
                                    $selected = "SELECTED";
                                echo "<option ".$selected.">".$team["team"]."</option>";
                            }
                        ?>
                    </select>
                </div>
            </form>
        </div>
    </div>    

    <h1 class="hidden-print">Extern - Uitslagen</h1>

    <div class="row" >
        <div class="col-md-8">
            <h2>Uitslagen</h2>
            <div class="panel-group" id="teamResults">
                <?php
                    foreach($external->getMatches($_GET["seizoen"], $_GET["team"]) as $match)
                    {
                        $thuis = "thuis";
                        $teams = settings::teamName." ".$match["team"]." - ".$match["tegenstander"];
                        $score = number_format($match["score"],1).' - '.number_format(8 - $match["score"],1);
                        if($match["uitwedstrijd"])
                        {
                            $thuis = "uit";
                            $teams = $match["tegenstander"]." - ".settings::teamName." ".$match["team"];
                            $score = number_format(8 - $match["score"],1).' - '.number_format($match["score"],1);
                        }

                        echo '<div class="panel panel-default">';
                        echo '<div class="panel-heading">';
                        echo '<div class="panel-title" style="overflow:auto"><a class="accordion-toggle" data-toggle="collapse" data-parent="#teamResults" href="#collapse'.$match["id"].'"><span class="col-md-2">'.date("d-m-Y",strtotime($match["datum"])).'</span><span class="col-md-5">'.$teams.'</span><span class="col-md-2">'.$score.'</span><span class="col-md-1">'.$thuis.'</span></a></div>';
                        echo '</div>';
                        echo '<div id="collapse'.$match["id"].'" class="panel-collapse collapse">';
                        echo '<div class="panel-body">';
                        echo '<table class="table table-striped">';
                        echo '<thead><tr><th>Witspeler</th><th>Rating</th><th></th><th>Zwartspeler</th><th>Rating</th><th style="text-align:center">Uitslag</th></tr>';
                        foreach($external->getIndividualMatches($match["id"]) as $individualMatch)
                        {
                            $player = new player($settings, $individualMatch["spelerId"]);
                            $player->getDetails();
                            if(!$match["uitwedstrijd"])
                                echo '<tr><td>'.$player->name.'</td><td>'.$individualMatch["spelerElo"].'</td><td>-</td><td>'.$individualMatch["tegenstanderNaam"].'</td><td>'.$individualMatch["tegenstanderElo"].'</td><td style="text-align:center">'.round($individualMatch["score"],1).' - '.round(1-$individualMatch["score"],1).'</td></tr>';
                            else
                                echo '<tr><td>'.$individualMatch["tegenstanderNaam"].'</td><td>'.$individualMatch["tegenstanderElo"].'</td><td>-</td><td>'.$player->name.'</td><td>'.$individualMatch["spelerElo"].'</td><td style="text-align:center">'.round(1-$individualMatch["score"],1).' - '.round($individualMatch["score"],1).'</td></tr>';
                        }

                        echo '</table>';


                        echo '</div>';  
                        echo '</div>';
                        echo '</div>';


                    }

                ?>
            </div>
        </div>
        <div class="col-md-4">
            <h2>Topscorers</h2>
            <table class="table table-striped">
                <thead><tr><th>Speler</th><th>Score</th><th>Partijen</th></tr></thead>
                <?php
                    foreach($external->getTopscorers($_GET["seizoen"], $_GET["team"]) as $speler)
                    {
                        $player = new player($settings, $speler["spelerId"]);
                        $player->getDetails();
                        
                        echo "<tr><td>".$player->name."</td><td>".$speler["score"]."</td><td>".$speler["partijen"]."</td></tr>";
                    }
                ?>

            </table>
        </div>
    </div>
    </body>
</html>

