<?php
    //WEBSITE STARTUP
    include_once('../../includes/class.init.php'); 
    $init = new init(1,0,0);
    include_once('../../includes/competition.php');
    include_once('../../includes/class.swiss.php');
    include_once('../../includes/class.player.php');
    include_once('../../includes/header.beheer.php');

    $swiss = new swiss();
    $settings = new settings();
    $competitie = new competition($settings, $_GET['competitie']);
    $competitie->getGeneralData();

    //Processing changes
    //Adding match
    if($_POST["date"] && $_POST["ronde"] && ($_POST["match"] || ($_POST["playerWhite"] && $_POST["playerBlack"])))
    {
        $playerWhite = new player($settings, $_POST["playerWhite"]);
        $playerWhite->getDetails();
        $playerBlack = new player($settings, $_POST["playerBlack"]);
        $playerBlack->getDetails();
        $date = explode("/",$_POST["date"]);
        $date = $date[2]."/".$date[1]."/".$date[0];
        $competitie->addMatch($playerWhite, $playerBlack,$init->repository->get_data("round"), $date);
    }

    //Deleting match
    if($_GET["matchDelete"])
        $competitie->deleteMatch($_GET["matchDelete"]);

    //Updating scores
    if($_POST["updateScores"])
    {
        $matches;
        foreach($_POST as $key => $data)
        {   
            if(preg_match("/^R[0-9]+/", $key))
                $matches[substr($key, 1)]["Score"] = $data;
            if(preg_match("/^Regl[0-9]+/", $key))
                $matches[substr($key, 4)]["Reglementair"] = $data;
            if(preg_match("/^Rapp[0-9]+/", $key))
                $matches[substr($key, 4)]["ExcludeRating"] = $data;
        }
        foreach($matches as $matchId => $match)
        {
            $competitie->setMatch($matchId, $match["Score"], $match["Reglementair"], $match["ExcludeRating"]);
        }
    }

    $compRounds = $competitie->getRounds();
    $compDate;
    if(is_array($compRounds))
        foreach($compRounds as $round)
        {
            if($round["ronde"] == $_GET["ronde"])
                $compData = date("d/m/Y",strtotime($round["datum"]));
    }
    
    //Processing PGN
    if($init->repository->get_data("pgnText") && $init->repository->get_data("matchId"))
    {
        include_once('../../includes/class.match.php');
        $pgnMatch = new match($settings, $init->errorClass, $init->notificationClass, $init->repository->get_data("matchId"));
        $pgnMatch->setPGN($init->repository->get_data("pgnText"),$init->repository->get_data("matchId"),$init->repository->get_data("pgnId"));
    }
    if($init->repository->get_data("pgnRemove"))
    {
        include_once('../../includes/class.match.php');
        $pgnMatch = new match($settings, $init->errorClass, $init->notificationClass, $init->repository->get_data("matchId"));
        $pgnMatch->removePGN($init->repository->get_data("pgnRemove"));
    }



?>
<link href="../css/select2.css" rel="stylesheet"/>
<script src="../js/select2.min.js"></script>
<script src="../js/jquery.maskedinput.js"></script>
<script>
    $(function() {
        $( "#date" ).datepicker({dateFormat: "dd/mm/yy" });
        $( "#date" ).mask("99/99/9999");
        $("#playerWhite,#playerBlack").select2({
            placeholder: "Selecteer een speler",
            allowClear: true});


        $('.glyphicon-tower').click(function(){
            $('#myModal').load('modalMatch.php?<?php echo "seizoen=".$init->repository->get_data("seizoen")."&competitie=".$init->repository->get_data("competitie")."&ronde=".$init->repository->get_data("round");?>&matchId='+ $(this).attr("value"), function(){ 
                $('#myModal').modal();
                $("#pgnSelect").change(
                    function()
                    {
                        $("#pgnText").text($("#pgnSelect option:selected").attr("pgn"));
                        $("#pgnId").val($("#pgnSelect option:selected").attr("value"));
                        $("#pgnLink").val("<?php echo setting::baseUrl;?>/archief/games/games_embed.php?gameId="+$("#pgnSelect  option:selected").attr("value"));
                        $("#pgnIframe").val("<iframe src=\"<?php echo setting::baseUrl;?>/archief/games/games_embed.php?gameId="+$("#pgnSelect  option:selected").attr("value")+"\" frameborder=\"0\" scrolling=\"no\" width=\"100%\" height=\"385px\"></iframe>");
                        $("#pgnRemove").attr("href", "compPairing.php?<?php echo "seizoen=".$init->repository->get_data("seizoen")."&competitie=".$init->repository->get_data("competitie")."&ronde=".$init->repository->get_data("round");?>&pgnRemove="+$("#pgnSelect option:selected").attr("value"));
                    });
            });

        })  
        
         
    });
</script>

<body class="container">
    <?php
        include("../../includes/menu.beheer.php");
    ?>       

    <div class="row">
    <div class="col-md-4 pull-right">
        <form action"comPairing.php" method="get">
        <input type="hidden" name="seizoen" value="<?php echo $_GET["seizoen"];?>">
        <input type="hidden" name="competitie" value="<?php echo $_GET["competitie"];?>">
        <select class="form-control pull-right" id="roundSelect" placeholder="Nieuw" onchange="this.form.submit()" name="ronde">
            <option value="">Selecteer een ronde</option>
            <?php
                foreach($compRounds as $date)
                {
                    $selected = "";
                    if($date["ronde"] == $_GET["ronde"])
                        $selected = "SELECTED";
                    echo '<option value="'.$date["ronde"].'" '.$selected.'>'.$date['ronde'].' - '.$date['datum'].'</option>';
                }
            ?>
        </select>
        </form>
    </div>

    <h1 class="hidden-print">Competitie - indeling</h1>
    <form action="compPairing.php" method="post" role="form">
        <input type="hidden" name="seizoen" value="<?php echo $_GET["seizoen"];?>">
        <input type="hidden" name="competitie" value="<?php echo $_GET["competitie"];?>">
        <div class="row">
            <div class="col-md-4 form-horizontal">
                <div class="form-group">
                    <label for="date" class="col-lg-2 control-label">Datum</label>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="date" name="date" placeholder="Datum" value="<?php echo $compData;?>">
                    </div>
                </div>            
                <div class="form-group">
                    <label for="round" class="col-lg-2 control-label">Ronde</label>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="round" name="ronde" placeholder="Ronde" value="<?php echo $_GET["ronde"];?>">
                    </div>
                </div>            
            </div>
            <div class="col-md-8 form-horizontal">
                <div class="form-group">
                    <label for="match" class="col-lg-2 control-label">Partij</label>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="match" placeholder="Email">
                    </div>
                </div>
                <div class="form-group">
                    <label for="playerWhite" class="col-lg-2 control-label">Handmatig</label>
                    <div class="col-lg-5">
                        <select type="text" class="form-control" id="playerWhite" name="playerWhite" data-placeholder="Witspeler"><option></option>
                            <?php
                                foreach($competitie->getPlayers() as $player)
                                {
                                    $player->getDetails();
                                    echo '<option value="'.$player->id.'">'.$player->name.'</option>';
                                }
                            ?>
                        </select>
                    </div>
                    <div class="col-lg-5">
                        <select type="text" class="form-control" id="playerBlack" name="playerBlack" data-placeholder="Zwartspeler"><option></option>
                            <?php
                                foreach($competitie->getPlayers() as $player)
                                {
                                    $player->getDetails();
                                    echo '<option value="'.$player->id.'">'.$player->name.'</option>';
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <input type="submit" value="Toevoegen" class="btn btn-default pull-right">
            </div>
        </div>

    </form>
    </div>
    <hr>
    <form action="compPairing.php" method="post" role="form">
        <input type="hidden" name="seizoen" value="<?php echo $_GET["seizoen"];?>">
        <input type="hidden" name="competitie" value="<?php echo $_GET["competitie"];?>">
        <input type="hidden" name="ronde" value="<?php echo $_GET["ronde"];?>">
        <input type="hidden" name="updateScores" value="1">
        <div class="row">
            <div class="col-md-12 hidden-print">
                <div class="pull-right btn-group">
                    <input type="submit" value="Opslaan" class="btn btn-success">
                    <input type="reset" class="btn btn-warning">
                </div>
                <h2>Partijen</h2>
                <table class="table tdMiddle">
                    <thead><tr><th>Witspeler</th><th><th>Zwartspeler</th><th>Uitslag</th><th style='text-align:center'>Reglementair</th><th style='text-align:center'>Geen Rapportage</th><th></th></tr></thead>
                    <?php
                        if($_GET['ronde'])
                        {
                            foreach($competitie->getMatches($_GET["ronde"], null) as $match)
                            {
                                // print_r($match);
                                $playerWhite = new player($settings, $match["speler_wit"]);
                                $playerWhite->getDetails();
                                $playerBlack = new player($settings, $match["speler_zwart"]);
                                $playerBlack->getDetails();
                                $uitslag;
                                $results = array(1 => "1 - 0",2 => "&#189;-&#189;",3 => "0 - 1");

                                echo "<tr><td>".$playerWhite->name." (".$match["rating_wit"].")</td><td>-</td><td>".$playerBlack->name." (".$match["rating_zwart"].")</td>";
                                echo "<td>";
                                echo '<select class="form-control" name="R'.$match["id"].'">';
                                foreach($results as $result => $line)
                                {
                                    $selected = "";
                                    if($result == $match["uitslag"])
                                        $selected = "SELECTED";
                                    echo '<option value="'.$result.'" '.$selected.'>'.$line.'</option>';
                                }


                                echo "</select></td>";
                                echo "<td style='text-align:center'>";
                                if(!$match["reglementair"])
                                    echo "<input type='checkbox' NAME='Regl".$match["id"]."' value=1>";
                                else
                                    echo "<input type='checkbox' NAME='Regl".$match["id"]."' CHECKED value=1>";
                                echo "</td>";
                                echo "<td style='text-align:center'>";
                                if(!$match["excludeRatingReport"])
                                    echo "<input type='checkbox' NAME='Rapp".$match["id"]."' value=1>";
                                else
                                    echo "<input type='checkbox' NAME='Rapp".$match["id"]."' CHECKED value=1>";
                                echo "</td>";
                                echo "<td><a href='compPairing.php?seizoen=".$_GET["seizoen"]."&competitie=".$_GET["competitie"]."&ronde=".$_GET["ronde"]."&matchDelete=".$match["id"]."' class='btn btn-danger glyphicon glyphicon-trash' title='Partij verwijderen'> </a> <span class='btn btn-primary glyphicon glyphicon-tower' title='PGN toevoegen/bewerken' value='".$match["id"]."'> </span></td>";
                                echo "</tr>";

                            }
                        }
                    ?>
                </table>
            </div>
        </div>
    </form>

    <div class="modal fade" id="myModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Wit - Zwart</h4>
                </div>
                <div class="modal-body">
                    <p>One fine body&hellip;</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->    
    </body>
</html>

