<?php
    //WEBSITE STARTUP
    include_once('../../includes/class.init.php');
    include_once('../../includes/class.player.php');
    include_once('../../includes/class.external.php');
    $init = new init(1,0,0);
    $settings = new settings();

    $external = new externalCompetition($settings);

    //Saving match
    if($init->repository->get_data("save"))
    {
        //Creating new match
        if(!$init->repository->get_data("matchId"))
            $init->repository->set_data("matchId", $external->createMatch($init->repository->get_data("seizoen"),$init->repository->get_data("teamId")));
        //Updating the match
        if($init->repository->get_data("matchId"))
        {
            $external->updateMatch($init->repository->get_data("matchId"),$init->repository->get_data("date"),$init->repository->get_data("externalAway"),$init->repository->get_data("externalGroup"),$init->repository->get_data("ratingTeam"),$init->repository->get_data("scoreTeam"),$init->repository->get_data("opponentName"),$init->repository->get_data("opponentTeam"),$init->repository->get_data("ratingOpponent"),$init->repository->get_data("scoreOpponent"),$init->repository->get_data("report"),$init->repository->get_data("reportOpponent"));
            for($a = 1; $a <= 8; $a++)
            {
                if(!$init->repository->get_data("gameId".$a))
                    $init->repository->set_data("gameId".$a, $external->createGame($a, $init->repository->get_data("matchId"),$init->repository->get_data("externalAway")));

                if($init->repository->get_data("gameId".$a))
                    $external->updateMatchGame($init->repository->get_data("gameId".$a),$init->repository->get_data("memberId".$a),$init->repository->get_data("memberRating".$a),$init->repository->get_data("opponentName".$a),$init->repository->get_data("opponentKNSB".$a),$init->repository->get_data("opponentRating".$a),$init->repository->get_data("externalScore".$a)); 
            }    
        }
    }


    //Get match details if possible
    $matchDetails = $external->getMatchDetails($init->repository->get_data("matchId"));
    $matchGames = $external->getIndividualMatches($init->repository->get_data("matchId"));

    include_once('../../includes/header.beheer.php');
?>
<link href="../css/select2.css" rel="stylesheet"/>
<script src="../js/select2.min.js"></script>
<script>
    $(function() {
        $( "#date" ).datepicker({dateFormat: "dd/mm/yy" });
    })
</script>
<script type="text/javascript">
    $(function() { 
        $(".knsbSearch").select2({
            placeholder: "Zoeken",
            multiple: false,
            allowClear: true,
            minimumInputLength: 1,
            ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
                url: "data_asynch.php",
                dataType: 'json',
                data: function (term, page) {
                    return {
                        asynchAction: 2, // search term
                        searchFilter: term
                    };
                },
                results: function (data, page) { // parse the results into the format expected by Select2.
                    // since we are using custom formatting functions we do not need to alter remote JSON data
                    return {results: data};
                }
            },
            initSelection : function (element, callback) {
                var data = {id: element.val(), naam: element.val()};
                callback(data);
            },
            id: format,
            // createSearchChoice:function(term) { return {id:term, text:term}; },        
            formatSelection: format,
            formatResult: format

        }); 

        $(".knsbSearch").change(function(){
            var row = $(this).attr("name").substr(12);
            $('input[name=opponentKNSB'+ row +']').val($(this).select2('data').knsb);   
            $('input[name=opponentRating'+ row +']').val($(this).select2('data').rating);    
        }) 


        $(".memberSearch").select2({
            placeholder: "Zoeken",
            allowClear: true,
            minimumInputLength: 1,
            ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
                url: "data_asynch.php",
                dataType: 'json',
                data: function (term, page) {
                    return {
                        asynchAction: 1, 
                        searchFilter: term  // search term
                    };
                },
                results: function (data, page) { // parse the results into the format expected by Select2.
                    // since we are using custom formatting functions we do not need to alter remote JSON data
                    return {results: data};
                }
            },
            initSelection : function (element, callback) {
                var data = {id: element.val(), achternaam: element.val(), tussenvoegsel: "", voornaam: ""};
                callback(data);
            },
            formatSelection: formatMember,
            formatResult: formatMember

        }); 
        $(".memberSearch").change(function(){
            var row = $(this).attr("name").substr(10);
            $('input[name=memberRating'+ row +']').val($(this).select2('data').rating);    
            $('input[name=memberId'+ row +']').val($(this).select2('data').id);    
        }) 

        function format(item) { return item.naam; };
        function formatMember(item) { return item.voornaam+" " + item.tussenvoegsel + " " + item.achternaam; };

        //Other items
        $("#externalAway").change(function()
            {
                switch($("#externalAway option:selected").val())
                {
                    case "0": $("#matches").removeClass("reversed");break;
                    case "1": $("#matches").addClass("reversed");break;
                }
        })

    })

</script>
<style>
    .table-striped.reversed>tbody>tr:nth-child(even)>td, .table-striped.reversed>tbody>tr:nth-child(even)>th{
        background-color: #f9f9f9;    
    }
    .table-striped.reversed>tbody>tr:nth-child(odd)>td, .table-striped.reversed>tbody>tr:nth-child(odd)>th{
        background-color: transparent;    
    }

</style>
<body class="container">

    <?php
        include("../../includes/menu.beheer.php");
    ?>       

    <div class="row">
    <div class="col-md-5 pull-right">
        <form action"compExtenal.php" method="get">
        <input type="hidden" name="seizoen" value="<?php echo $init->repository->get_data("seizoen");?>">
        <span class="col-xs-4" style="padding-left: 0px;padding-right: 0px;"><select class="form-control pull-right" id="teamSelect" placeholder="Nieuw" onchange="this.form.submit()" name="teamId">
                <option value="">Selecteer team</option>
                <?php
                    foreach($data->getExternalTeams() as $team)
                    {
                        $selected = "";
                        if($date["ronde"] == $_GET["ronde"])
                            $selected = "SELECTED";
                        echo '<option value="'.$team["id"].'" '.($team["id"] == $init->repository->get_data("teamId") ? "SELECTED" : "").'>'.$team['naam'].'</option>';
                    }
                ?>
            </select></span>
        </form>
        <form action"compExtenal.php" method="get">
        <input type="hidden" name="seizoen" value="<?php echo $init->repository->get_data("seizoen");?>">
        <input type="hidden" name="teamId" value="<?php echo $init->repository->get_data("teamId");?>">
        <span class="col-xs-8 pull-right" style="padding-right: 0px;"><select class="form-control pull-right" id="matchSelect" placeholder="Nieuw" onchange="this.form.submit()" name="matchId">
                <option value="">Wedstrijd</option>
                <option value="">Nieuw</option>
                <?php
                    foreach($external->getMatches($init->repository->get_data("seizoen"),$init->repository->get_data("teamId")) as $match)
                    {
                        $selected = "";
                        if($date["ronde"] == $_GET["ronde"])
                            $selected = "SELECTED";
                        echo '<option value="'.$match["id"].'" '.($match["id"] == $init->repository->get_data("matchId") ? "SELECTED" : "").'>'.date("d-m-Y",strtotime($match['datum']))." - ".$match['tegenstander'].'</option>';
                    }
                ?>
            </select></span>
        </form>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h1 class="hidden-print">Externe competitie</h1>
            <form class="form-horizontal" method="post">
                <input type="hidden" name="save" value="1">
                <input type="hidden" name="matchId" value="<?php echo $init->repository->get_data("matchId");?>">
                <input type="submit" value="Opslaan" class="btn btn-success pull-right" style="margin-bottom: 5px;">
                <h3>Wedstrijd</h3>
                <table class="table">
                    <tr>
                        <td>Datum</td><td><input type="text" class="form-control input-sm" id="date" name="date" value="<?php echo date("d/m/Y",strtotime($matchDetails["datum"]));?>"></td>
                        <td>Groep</td><td><select class="form-control input-sm" name="externalGroup"><option selected disabled hidden value=''></option><optgroup label="RSB"><?php foreach($data->getExternalGroups() as $line){ echo '<option value="'.$line["id"].'" '.($matchDetails["groupId"] == $line["id"] ? ' SELECTED' : '').'>RSB '.$line["groep"].'</option>';};?></optgroup><optgroup label="KNSB"><?php foreach($data->getExternalGroups(1) as $line){ echo '<option value="'.$line["id"].'">KNSB '.$line["groep"].'</option>';};?></optgroup></select></td>
                    </tr>
                    <tr>
                        <?php $uitThuis = array("Thuis", "Uit");?>
                        <td>Thuis / Uit</td><td><select class="form-control input-sm" name="externalAway" id="externalAway"><?php foreach($uitThuis as $key => $line){ echo '<option value="'.$key.'"'.($matchDetails["uitwedstrijd"] == $key ? ' SELECTED' : '').'>'.$line.'</option>';};?></select></td>
                        <td>Tegenstander</td><td>
                            <span class="col-xs-10" style="padding-left: 0px;"><input type="text" name="opponentName" class="form-control input-sm" value="<?php echo $matchDetails["tegenstander"];?>"></span>
                            <span class="col-xs-2" style="padding-right: 0px;"><input type="text" name="opponentTeam" placeholder="Team" class="form-control input-sm" value="<?php echo $matchDetails["tegenstanderTeam"];?>"></span></td>
                    </tr>
                    <tr>
                        <td>Gemiddelde rating</td><td><input type="text" name="ratingTeam" class="form-control input-sm" value="<?php echo $matchDetails["teamElo"];?>"></td>
                        <td>Gemiddelde rating</td><td><input type="text" name="ratingOpponent" class="form-control input-sm" value="<?php echo $matchDetails["tegenstanderElo"];?>"></td>
                    </tr>
                    <tr>
                        <td>Score</td><td><input type="text" name="scoreTeam" class="form-control input-sm" value="<?php echo $matchDetails["score"];?>"></td>
                        <td>Score</td><td><input type="text" name="scoreOpponent" class="form-control input-sm" value="<?php echo $matchDetails["scoreTegenstander"];?>"></td>
                    </tr>
                    <tr>
                        <td>Verslag</td><td><input type="text" name="report" class="form-control input-sm" value="<?php echo $matchDetails["verslag"];?>"></td>
                        <td>Verslag tegenstander</td><td><input type="text" name="reportOpponent" class="form-control input-sm" value="<?php echo $matchDetails["verslagTegenstander"];?>"></td>
                    </tr>
                </table>
                <h3>Partijen</h3>
                <table class="table table-striped" id="matches"  ng-app="SVNpublic" ng-controller="externCompetitie">
                    <thead>
                        <tr>            
                            <th>Bord</th>
                            <th>Speler</th>
                            <th>Rating</th>
                            <th></th>
                            <th>Tegenstander</th>
                            <th>KNSB</th>
                            <th>Rating</th>
                            <th>Uitslag</th>
                        </tr> 
                    </thead>
                    <?php 
                        $results = array("1" => "1 - 0","0.5" => "&#189;-&#189;","0" => "0 - 1");
                        for($a = 1; $a < 11; $a++)
                        {
                            $game = $data->filter($matchGames,"bord",$a);
                            $game = $game[0];
                            $speler = new player($settings, $game["spelerId"]);
                            $speler->getDetails();
                        ?>
                        <tr>
                            <td><?php echo $a;?>
                                <input type="hidden" name="gameId<?php echo $a;?>" value="<?php echo $game["id"];?>">
                                <input type="hidden" name="memberId<?php echo $a;?>" value="{{playerSelect<?php echo $a;?>.id}}">
                            </td>
                            <td><select class="form-control" ng-model="playerSelect<?php echo $a;?>" ng-options="player as (player.achternaam  + ' ' + player.voornaam) for player in players.data track by player.id" ng-init="playerSelect<?php echo $a;?> = playerSelect<?php echo $a;?> || {id: '<?php echo $game["spelerId"];?>'}"></select></td>
                            <td><input  class="form-control input-sm" type="text" name="memberRating<?php echo $a;?>" ng-model="playerSelect<?php echo $a;?>.rating"  placeholder="Rating" style="width: 70px;" maxlength="4" ng-init="playerSelect<?php echo $a;?>.rating = <?php echo $game["spelerElo"];?>"></td>
                            <td>-</td>
                            <td><input class="form-control input-sm knsbSearch" name="opponentName<?php echo $a;?>" value="<?php echo $game["tegenstanderNaam"];?>"></td>
                            <td><input class="form-control input-sm" type="text" name="opponentKNSB<?php echo $a;?>" style="width: 100px;" maxlength="8" value="<?php echo $game["tegenstanderKNSB"];?>"></td>
                            <td><input class="form-control input-sm" type="text" name="opponentRating<?php echo $a;?>" placeholder="Rating" style="width: 70px;" maxlength="4" value="<?php echo $game["tegenstanderElo"];?>"></td>
                            <td><select class="form-control input-sm" name="externalScore<?php echo $a;?>" style="width: 80px;"><option value=""></option><?php foreach($results as $value => $line){ echo '<option value="'.$value.'" '.($game["score"] != "" && $game["score"] == $value ? ' SELECTED' : '').'>'.$line.'</option>';};?></select></td>
                        </tr>
                        <?php }?>
                </table>
            </form>

        </div>
    </div>
    </body>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.0-rc.3/angular.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.0-rc.3/angular-resource.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/angular-ui-bootstrap/0.12.0/ui-bootstrap.min.js"></script>
<script src="../js/svn.js"></script>

</html>

