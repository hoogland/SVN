<?php
    //WEBSITE STARTUP
    include_once('../../includes/class.init.php');
    include_once('../../includes/competition.php');
    include_once('../../includes/class.swiss.php');
    include_once('../../includes/class.player.php');

    $init = new init(1,0,0);
    $competitie = new competition($settings, $init->repository->get_data("competitie"), $init->errorClass, $init->notificationClass);
    
    if($init->repository->get_data("addPlayer"))
        $competitie->addPlayer($init->repository->get_data("addPlayer"));
    if($init->repository->get_data("removePlayer"))
        $competitie->removePlayer($init->repository->get_data("removePlayer"));
    if($init->repository->get_data("participantsSorting"))
        $competitie->setPlayerSorting($init->repository->get_data("participantsSorting"));
    
    
    include_once('../../includes/header.beheer.php');
?>
<link href="../css/select2.css" rel="stylesheet"/>
<script src="../js/select2.min.js"></script>
<script>
    $(function() {
        $("#player").select2({
            placeholder: "Selecteer een speler",
            allowClear: true}); 
        $( "#participants" ).sortable({
        }).disableSelection();
        $('input[name=participantsSorting]').val($("#participants li").map(function() { return $(this).attr("value") }).get().join(","));
        $("#participants").on("sortupdate", function( event, ui ) {
            $('input[name=participantsSorting]').val($("#participants li").map(function() { return $(this).attr("value") }).get().join(","));
        })
    });
</script>

<body class="container">

    <?php
        include("../../includes/menu.beheer.php");
    ?>       


    <div class="row hidden-print">
        <div class="col-md-12">
            <h1 class="hidden-print">Competitie - Deelnemers</h1>
            <form action="compPlayers.php" class="form-horizontal" method="post" role="form">
                <input type="hidden" name="seizoen" value="<?php echo $init->repository->get_data("seizoen");?>">
                <input type="hidden" name="competitie" value="<?php echo $init->repository->get_data("competitie");?>">
                <div class="form-group">
                    <label for="player" class="col-sm-2 control-label">Speler toevoegen:</label>
                    <div class="col-sm-10">
                        <select class="form-control" id="player" name="addPlayer" placeholder="Selecteer een clublid">
                            <?php
                                foreach($data->getPlayers() as $playerData)
                                {
                                    $player = new player($settings, $playerData["id"]);
                                    $player->getDetails();
                                    echo '<option value="'.$player->id.'">'.$player->name.'</option>';
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-default">Toevoegen</button>
                    </div>
                </div>
            </form>
        </div>   
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12 sortingContainer">
            <form action="compPlayers.php" method="post">
                <input type="hidden" name="seizoen" value="<?php echo $init->repository->get_data("seizoen");?>">
                <input type="hidden" name="competitie" value="<?php echo $init->repository->get_data("competitie");?>">
                <input type="hidden" name="participantsSorting">
                <button type="submit" class="btn btn-default pull-right">Volgorde opslaan</button>
                <h2>Huidige deelnemers</h2>
                <ul id="participants">
                    <?php
                        foreach($competitie->getPlayers() as $player)
                        {
                            echo "<li value='".$player->id."'>".$player->name."<a class='pull-right btn btn-danger btn-xs' href='compPlayers.php?seizoen=".$init->repository->get_data("seizoen")."&competitie=".$init->repository->get_data("competitie")."&removePlayer=".$player->id."'>Verwijder</a></li>";  
                        }
                    ?>
                </ul>
            </form>
        </div>
    </div>

    </body>
</html>

