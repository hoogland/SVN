<?
    //WEBSITE STARTUP
    include_once('../../includes/class.init.php');
    $init = new init(1,0,0);
    include_once('../../includes/class.competition.php');
    include_once('../../includes/class.swiss.php');
    include_once('../../includes/class.player.php');
    //require_once('../../includes/class.data.php');

    $swiss = new swiss();
    $settings = new settings();

    $competitie = new competition($settings, $init->repository->get_data("competitie"), $init->errorClass, $init->notificationClass);
    $competitie->getGeneralData();


    if($init->repository->get_data("compName"))
    {
        $competitie->setData($init->repository->get_data("compName"), $init->repository->get_data("compNameExtended"), $init->repository->get_data("typeCompetition"), $init->repository->get_data("tempo"), $init->repository->get_data("tempoExtended"), $init->repository->get_data("place"), $init->repository->get_data("country"), $init->repository->get_data("arbiter"), $init->repository->get_data("arbiterMail"), $init->repository->get_data("compDisplay"), $init->repository->get_data("compSorting"));
        $competitie->getGeneralData();

        $competitie->setOptions("TPRmethod", $init->repository->get_data("TPRmethod"));
        $competitie->setOptions("TPRdamped", $init->repository->get_data("TPRdamped"));
        $competitie->setOptions("compSystem", $init->repository->get_data("compSystem"));
        $competitie->getGeneralData();
    }
    include_once('../../includes/header.beheer.php');
?>
<script>
    $(function() {
        $( "#rangschikking" ).sortable();
        $( ".compDisplay" ).sortable({
            connectWith: ".compDisplay"
        }).disableSelection();
        $( ".compSorting" ).sortable({
            connectWith: ".compSorting"
        }).disableSelection();

        $("#compSorting").on("sortupdate", function( event, ui ) {
            $('input[name=compSorting]').val($("#compSorting li").map(function() { return $(this).attr("value") }).get().join(","));
        })
        $("#compDisplay").on("sortupdate", function( event, ui ) {
            $('input[name=compDisplay]').val($("#compDisplay li").map(function() { return $(this).attr("value") }).get().join(","));
        })

    });
</script>

<body class="container">

    <? 
        include("../../includes/menu.beheer.php");
    ?>       
    <form action="compSettings.php" method="post" role="form">
        <input type="hidden" name="seizoen" value="<?php echo $init->repository->get_data("seizoen");?>">
        <input type="hidden" name="competitie" value="<?php echo $init->repository->get_data("competitie");?>">
        <input type="hidden" name="typeCompetition" value="1">
        <input type="submit" value="Opslaan" class="btn btn-primary pull-right">
        <h1 class="hidden-print">Competitie - instellingen</h1>

        <div id="competitie" >
            <div id="competitieMenu">
                <div class="row hidden-print">
                    <div id="competitieDetails" class="col-md-12">
                        <table class="table">
                            <tr><td>Competitie</td><td><input class="form-control" type="text" name="compName" value="<?php echo $competitie->name;?>"></td><td>Wedstrijdleider</td><td><input class="form-control" type="text" name="arbiter" value="<?php echo $competitie->arbiter;?>"></td></tr>
                            <tr><td>Naam uitgebreid</td><td><input class="form-control" type="text" name="compNameExtended" value="<?php echo $competitie->nameExtended;?>"></td><td>E-mail wedstrijdleider</td><td><input class="form-control" type="text" name="arbiterMail" value="<?php echo $competitie->arbiterMail;?>"></td></tr>
                            <tr><td>Intern / Extern</td><td></td><td>Plaats</td><td><input class="form-control" type="text" name="place" value="<?php echo $competitie->place;?>"></td></tr>
                            <tr><td>Type competitie</td><td><select class="form-control" name="compSystem"><option value="Zwitsers">Zwitsers/Round Robin</option><option value="Keizer" <?php echo ($competitie->options["compSystem"] == "Keizer" ? "selected" : "")?>>Keizer</option></select></td><td>Land</td><td><input class="form-control" type="text" name="country" value="<?php echo $competitie->country;?>"></td></tr>
                            <tr><td>Tempo</td><td><select class="form-control" name="tempo"><?php
                                        foreach($data->tempi as $key => $tempo)
                                        {
                                            echo "<option value=".$key." ".($key == $competitie->tempo ? "selected" : "").">".$tempo."</tempo>";
                                        }                                                                                                                                                                                                                                        
                                    ?></select></td><td>TPR Methode</td><td><select class="form-control" name="TPRmethod"><?php
                                        foreach($data->tprMethods as $method)
                                        {
                                            echo "<option value=".$method." ".($method == $competitie->options["TPRmethod"] ? "selected" : "").">".$method."</tempo>";
                                        }                                                                                                                                                                                                                                        
                                    ?></select></td></tr>
                            <tr><td>Tempo getypt</td><td><input class="form-control" type="text" name="tempoExtended" value="<?php echo $competitie->tempoExtended;?>"></td><td>TPR demping</td><td><input type="radio" name="TPRdamped" value="1" <?php echo ($competitie->options["TPRdamped"] == 1 ? "checked" : "") ?>>Aan <input type="radio" name="TPRdamped" value="0" <?php echo ($competitie->options["TPRdamped"] == 0 ? "checked" : "") ?>>Uit</td></tr>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 hidden-print">
                        <input type="hidden" name="compSorting" value="<?php echo implode(",", $competitie->sorting);?>">
                        <h2>Rangschikking</h2>
                        <div class="col-md-6 sortingContainer">
                            <b>Beschikbare criteria</b>
                            <ul class="compSorting">
                                <?php
                                    $compColumns = $data->getCompetitionColums($competitie->options["compSystem"]);

                                    foreach($compColumns as $sort)
                                    {
                                        if(!in_array($sort["name"],$competitie->sorting)  && $sort["name"] != "Ranking")
                                            echo "<li value = \"".$sort["name"]."\">".$sort["name_long"]."</li>";
                                    }   
                                ?>
                            </ul>
                        </div>
                        <div class="col-md-6 sortingContainer">
                            <b>Gebruikte criteria</b>
                            <ul id="compSorting" class="compSorting">
                                <?php
                                        foreach($competitie->sorting as $item)
                                        {
                                            $sort = $data->filter($compColumns, "name", $item);
                                            echo "<li value = \"".$sort[0]["name"]."\">".$sort[0]["name_long"]."</li>";
                                        }   
                                ?>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6 hidden-print">
                        <input type="hidden" name="compDisplay" value="<?php echo $competitie->options["DisplayData"];?>">
                        <h2>Weergave</h2>
                        <div class="row">
                            <div class="col-md-6 sortingContainer">
                                <b>Beschikbare kolommen</b>
                                <ul class="compDisplay">
                                    <?php
                                        foreach($compColumns as $sort)
                                        {
                                            if(!in_array($sort["name"],explode(",",$competitie->options["DisplayData"])))
                                                echo "<li value = \"".$sort["name"]."\">".$sort["name_long"]."</li>";
                                        }   
                                    ?>
                                </ul>
                            </div>
                            <div class="col-md-6 sortingContainer">
                                <b>Gebruikte kolommen</b>
                                <ul id="compDisplay" class="compDisplay">
                                    <?php
                                        foreach(explode(",",$competitie->options["DisplayData"]) as $item)
                                        {
                                            $sort = $data->filter($compColumns, "name", $item);
                                             echo "<li value = \"".$sort[0]["name"]."\">".$sort[0]["name_long"]."</li>";
                                        }   
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    </body>
</html>

