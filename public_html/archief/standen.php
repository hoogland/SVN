<?php
    //WEBSITE STARTUP
    include_once('../../includes/class.init.php');
    include_once('../../includes/competition.php');
    include_once('../../includes/class.swiss.php');
    include_once('../../includes/class.player.php');

    $init = new init(0,0,1);
    $swiss = new swiss();
    $settings = new settings();

    $competitie = new competition($settings, $init->repository->get_data("competitie"));

    if($init->repository->get_data("competitie"))
    {
        $competitie->getGeneralData();

        $roundSelect =  $competitie->rounds[count($competitie->rounds) - 1];

        if($init->repository->get_data("competitie"))
        {
            foreach($competitie->rounds as $comp)
            {
                if($comp["ronde"] == $init->repository->get_data("round"))
                    $roundSelect = $comp; 
            }
        }

        $competitie->getStanding(array(1,$roundSelect["ronde"]));
    }
    include_once('../../includes/header.archief.php');
?>
<script type="text/javascript">
    $(document).ready(function(){
        $('#stand > table').floatThead({
        });
    });                                   
</script>

<body class="container">

    <?php 
        include("../../includes/menu.archief.php");
    ?>       

    <h1 class="hidden-print">Competitie</h1>

    <div id="competitie" >
        <div id="competitieMenu">
            <div class="row hidden-print <?php echo (!$init->repository->get_data("competitie") ? "hidden" : "");?>">
                <div id="competitieDetails" class="col-md-12">
                    <table class="table">
                        <tr><td>Competitie</td><td><?php echo $competitie->name;?></td></tr>
                        <tr><td>Tempo</td><td><?php echo $competitie->tempoExtended;?></td></tr>
                        <tr><td>Paringsysteem</td><td><?php echo $competitie->options["compSystem"];?></td></tr>
                        <tr><td>Plaats</td><td><?php echo $competitie->place;?></td></tr>
                    </table>
                </div>
            </div>
            <div class="row <?php echo (!$init->repository->get_data("competitie") ? "hidden" : "");?>">
                <div class="col-md-2 hidden-print visible-md visible-lg">
                    <h2>Rondes</h2>
                    <ul id="rondes">
                        <?php
                            foreach($competitie->rounds as $round)
                            {
                                echo "<li class='".($round["ronde"] == $init->repository->get_data("round") ? "selectedRound" : "")."'><a href=\"standen.php?seizoen=".$competitie->season."&competitie=".$competitie->id."&ronde=".$round["ronde"]."\">".$round["ronde"]." - ".date("d-m-Y",strtotime($round["datum"]))."</a></li>";
                            }
                        ?>
                    </ul>
                </div>
                <div class="col-md-10">
                    <ul class="nav nav-tabs hidden-print">
                        <li class="active"><a href="#stand" data-toggle="tab">Stand</a></li>
                        <li><a href="#partijen" data-toggle="tab">Partijen</a></li>
                        <li><a href="#kruistabel" data-toggle="tab">Kruistabel</a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="stand" class="tab-pane active table-responsive">
                            <table class="table table-striped table-condensed">
                                <?php
                                    $compColumns = $data->getCompetitionColums($competitie->options["compSystem"]);
                                    echo "<thead><tr>";
                                    foreach(explode(",",$competitie->options["DisplayData"]) as $field)
                                    {
                                        $sort = $data->filter($compColumns, "name", $field);
                                        echo "<th style='background-color: #FFF'><span class=\"visible-md visible-lg\">".$sort[0]["name_long"]."</span><span class=\"visible-xs visible-sm\">".$sort[0]["name_short"]."</span></th>";
                                    }

                                    echo "</tr></thead><tbody>";
                                    foreach($competitie->standings as $key => $dataPlayer)
                                    {
                                        foreach(explode(",",$competitie->options["DisplayData"]) as $field)
                                        {
                                            if($field == "Ranking")
                                                echo "<td>".($key + 1)."</td>";
                                            elseif($field == "Name")
                                            {
                                                $player = new player($settings, $dataPlayer["player"]);
                                                $player->getDetails();
                                                echo "<td><a href=\"competitieSpeler.php?seizoen=".$_GET["seizoen"]."&competitie=".$_GET["competitie"]."&spelerId=".$dataPlayer["player"]."\">".$player->name."</a></td>";        
                                            }
                                            else
                                                echo "<td>".$dataPlayer[$field]."</td>";
                                        }
                                        echo "</tr>";
                                    }

                                    
                                ?>                  
                            </tbody></table>
                        </div>

                        <div id="partijen" class="tab-pane">
                            <h3>Uitslagen ronde <?php echo $roundSelect["ronde"]." <i>(".date("d-m-Y",strtotime($roundSelect["datum"]));?>)</i></h3> 
                            <table class="table table-striped table-hover">
                                <thead><tr><th>Witspeler</th><th></th><th>Zwartspeler</th><th>Uitslag</th></tr></thead>
                                <?php
                                    foreach($competitie->getMatches($roundSelect["ronde"]) as $match)
                                    {
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

                                        echo "<tr><td><a href=\"competitieSpeler.php?seizoen=".$_GET["seizoen"]."&competitie=".$_GET["competitie"]."&spelerId=".$playerWhite->id."\">".$playerWhite->name."</a> (".$match["rating_wit"].")</td><td>-</td><td><a href=\"competitieSpeler.php?seizoen=".$_GET["seizoen"]."&competitie=".$_GET["competitie"]."&spelerId=".$playerBlack->id."\">".$playerBlack->name."</a> (".$match["rating_zwart"].")</td><td>".$uitslag."".($match["reglementair"] ? ' <i class="fa fa-exclamation-circle text-danger pointer" title="Reglementaire uitslag"></i>' : ''). "</td></tr>";

                                    }
                                ?>

                            </table> 
                        </div>
                        <div id="kruistabel" class="tab-pane table-responsive"> 
                            <table class="table table-striped table-condensed" id="kruistabel">
                                <?php
                                    $competitie->getMatches(array(1,$roundSelect["ronde"]));
                                    echo "<tr><thead><th></th><th>Speler</th>";
                                    foreach($competitie->standings as $key => $data)
                                        echo "<th>".($key + 1)."</th>";
                                    echo "</thead></tr>";
                                    foreach($competitie->standings as $key => $data)
                                    {
                                        echo "<tr><td>".($key + 1)."</td>";
                                        $player = new player($settings, $data["player"]);
                                        $player->getDetails();
                                        echo "<td><a href=\"competitieSpeler.php?seizoen=".$_GET["seizoen"]."&competitie=".$_GET["competitie"]."&spelerId=".$data["player"]."\">".$player->name."</a></td>";        
                                        foreach($competitie->standings as $nr => $speler)
                                        {

                                            if($key == $nr)
                                                echo "<td style='background-color: black'></td>";
                                            else
                                            {
                                                echo "<td>";
                                                foreach($competitie->matches as $partij)
                                                {
                                                    if($partij["uitslag"] <> "" && ($partij["speler_wit"] == $data["player"] && $competitie->standings[$nr]["player"] == $partij["speler_zwart"]) || ($partij["speler_wit"] == $competitie->standings[$nr]["player"] && $data["player"] == $partij["speler_zwart"]))
                                                    {
                                                        $uitslag = $partij["uitslag"];
                                                        if($partij["speler_zwart"] == $data["player"])
                                                        $uitslag = 4 - $uitslag;
                                                        switch($uitslag)
                                                        {
                                                            case 1:   echo "1";break;
                                                            case 2:   echo "&#189;";break;
                                                            case 3:   echo "0";break;
                                                        }
                                                    }

                                                }
                                                echo "</td>";
                                            }
                                        }
                                        echo "</tr>";
                                    }

                                ?>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </body>
</html>

