<?php
    //WEBSITE STARTUP
    include_once('../../includes/class.settings.php');
    include_once('../../includes/class.competition.php');
    include_once('../../includes/class.swiss.php');
    include_once('../../includes/class.player.php');

    $swiss = new swiss();
    $settings = new settings();

    include_once('../../includes/header.archief.php');
?>
<link href="../../css/select2.css" rel="stylesheet"/>
<script src="../../js/select2.min.js"></script>
<script>
    $(function() {
    });
</script>
<script type="text/javascript">
    // Function to get the Min value in Array
    Array.prototype.min = function() {
        return Math.min.apply(null, this);
    };
    $(function () { 

        $("#player").select2({
            placeholder: "Selecteer een speler",
            allowClear: true}); 
        $("#player").change(function()
        {
            loadScores();
            loadRating();
            loadOpponentScores();
        })
        $("#tempo").change(function()
        {
            loadScores();
            loadOpponentScores();
        })

        loadScores();
        loadRating();
        loadOpponentScores();



    });

    function loadOpponentScores()
    {
        $('#opponentScores tbody').empty();
        $.ajax({
            type: "POST",
            url: "asynch.php",
            data: { player: $("#player").val(), asynchAction: "4", tempo: $("#tempo").val() },
            dataType: "json",
        }).done(function( data ) {
            $.each(data, function(index, opponent){
                var partijen = (parseInt(opponent.Winst) + parseInt(opponent.Remise) + parseInt(opponent.Verlies));
                var percentage = (parseInt(opponent.Winst) + parseInt(opponent.Remise) / 2) / partijen * 100;
                $('#opponentScores tbody').append('<tr id="'+ opponent.TegenstanderId +'"><td><span class="glyphicon glyphicon-th-list"></span> ' + opponent.name + '</td><td>' + partijen + '</td><td>' + opponent.Winst + '</td><td>' + opponent.Remise + '<td>' + opponent.Verlies + '</td><td>' + Math.round(percentage) + '%</td></td></tr>')   
            });
            $("#opponentScores tr").on("click", function(){
                opponentGames($(this).attr("id"));

            });
        });        

    }

    function opponentGames(opponentId)
    {
        $('#tableGames tbody').empty();
        $.ajax({
            type: "POST",
            url: "asynch.php",
            data: { player: $("#player").val(), asynchAction: "3", opponent: opponentId },
            dataType: "json",
        }).done(function( data ) {
            $.each(data.games, function(index, game){
                var uitslag = "";
                switch(game.uitslag)
                {
                    case "1": 
                        uitslag = "1-0";
                        break;
                    case "2": 
                        uitslag = "1/2-1/2";
                        break;
                    case "3": 
                        uitslag = "0-1";
                        break;
                }
                var tempo = "";
                switch(game.tempo)
                {
                    case "1": tempo = "glyphicon glyphicon-flash";break;
                    case "2": tempo = "glyphicon glyphicon-plane";break;
                }
                var rowColor = "";
                if((game.uitslag == "1" && game.speler_wit == $("#player").val()) || (game.uitslag == "3" && game.speler_zwart == $("#player").val()))
                    rowColor = "success"
                if((game.uitslag == "3" && game.speler_wit == $("#player").val()) || (game.uitslag == "1" && game.speler_zwart == $("#player").val()))
                    rowColor = "danger"

                $('#tableGames tbody').append('<tr class="'+rowColor+'"><td><a href="standen.php?competitie=' + game.comp_id + '" title="Open competitie"><span class="glyphicon glyphicon-th-list" style="color: #000000"></span></a></td><td>' + game.datum + '</td><td>' + data.players[game.speler_wit] + '</td><td>-</td><td>' + data.players[game.speler_zwart] + '</td><td>' + uitslag + '</td><td><span class="'+tempo+'"></td></tr>')  

            });
            $('#OpponentName').text(data.players[opponentId]); 
            $('#modalGames').modal();
        });        

    }

    function loadScores()
    {
        var scoreChartOptions = {
            chart: {
                type: 'pie'
            },
            title: {
            },
            plotOptions: {
                pie:
                {
                    dataLabels:{enabled: false},
                    colors: ['#336600','#C0C0C0','#C13100']
                }
            },
            tooltip:
            {
                pointFormat: '<b>{point.y}</b> ({point.percentage:.1f}%)'
            }                                                                        ,
            series: [{
                type: 'pie',
                name: '%',
                data:
                [
                ['Winst', 0],
                ['Remise', 0],
                ['Verlies', 0],           
                ]
            }]
        };        

        var charts = [["Totaal","scoreTotaal", ""],["Wit","scoreWhite",1],["Zwart","scoreBlack",2]]
        $.each(charts, function(index, chart)
        {
            $.ajax({
                type: "POST",
                url: "asynch.php",
                data: { player: $("#player").val(), asynchAction: "2", color: chart[2], tempo: $("#tempo").val() },
                dataType: "json",
            }).done(function( data ) { 
                scoreChartOptions.title.text = chart[0] + " ("+ (parseInt(data.Winst) + parseInt(data.Remise) + parseInt(data.Verlies))+")";
                scoreChartOptions.series[0].data = [['Winst', parseInt(data.Winst)],['Remise', parseInt(data.Remise)],['Verlies', parseInt(data.Verlies)]] ;
                $('#'+chart[1]).highcharts(scoreChartOptions);
            });        
        })       
    }

    function loadRating()
    {
        var ratingChartOptions = {
            chart: {
                type: 'area'
            },
            title: {
                text: ''
            },
            xAxis:{
            },
            yAxis:{
                title: {text: ''},
                startOnTick: true,
                endOnTick: true,
                tickInterval: 100 ,
            }                         ,
            tooltip:
            {
                pointFormat: '<b>{point.y}</b>'
            },
            series: [{
            }],
            legend: {enabled : false}
        }; 

        $.ajax({
            type: "POST",
            url: "asynch.php",
            data: { player: $("#player").val(), asynchAction: "1"},
            dataType: "json",
        }).done(function( data ) {
            var ratings = new Array();
            var dates = new Array();
            $.each(data, function(index, object){
                data[index].y = parseInt(object.rating); 
                ratings.push(object.rating);
                dates.push(object.datum);
            })

            ratingChartOptions.series[0].data = data;
            ratingChartOptions.yAxis.min = ratings.min() * 0.95;
            ratingChartOptions.xAxis.categories = dates;  

            $('#ratingChart').highcharts(ratingChartOptions);
        });        
    }
</script>
<style>
    #opponentScores tr{
        cursor: pointer;
    }

    #opponentScores td,#opponentScores th {
        text-align: center;
    }

    #opponentScores td:first-child,#opponentScores th:first-child {
        text-align: left;
    }
    #tableGames td:last-child,#tableGames th:last-child{
        text-align: center;
    }
</style>

<body>
    <?php 
        include("../../includes/menu.archief.php");
    ?>  
    <div class="container-fluid">     
        <div class="row">
            <div class="col-md-12">
                <h1 class="hidden-print">Speler statistieken</h1>
                <select class="form-control" id="player" name="addPlayer" placeholder="Selecteer een clublid" style="padding:0px;height:27px;margin-bottom: 4px;border:none">
                    <?php
                        foreach($data->getPlayers() as $playerData)
                        {
                            $player = new player($settings, $playerData["id"]);
                            $player->getDetails();
                            echo '<option value="'.$player->id.'">'.$player->name.'</option>';
                        }
                    ?>
                </select>
                <select class="form-control" id="tempo"><option value="">Alle partijen</option>
                    <?php
                        foreach($data->tempi as $id => $text)
                            echo "<option value=".$id.">".$text."</option>";
                    ?>

                </select>
                <div class="row">
                    <div class="col-md-12">
                        <h2>Scores</h2>
                        <div class="row">
                            <div class="col-md-4">
                                <div id="scoreTotaal"></div>
                            </div>
                            <div class="col-md-4">
                                <div id="scoreWhite"></div>
                            </div>
                            <div class="col-md-4">
                                <div id="scoreBlack"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h2>Rating</h2>
                        <div class="row">
                            <div class="col-md-12">
                                <div id="ratingChart"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h2>Tegenstanders (intern)</h2>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-striped table-hover" id="opponentScores">
                                    <thead>
                                        <tr><th>Tegenstander</th><th>Partijen</th><th>Winst</th><th>Remise</th><th>Verlies</th><th>Score</th></tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>    
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="modalGames">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="OpponentName">Edo Pouwelse</h4>
                            </div>
                            <div class="modal-body">
                                <table class="table" id="tableGames">
                                    <thead><tr><th></th><th>Datum</th><th>Wit</th><th></th><th>Zwart</th><th>Uitslag</th><th></th></tr></thead>
                                    <tbody>
                                    </tbody>
                                </table>
                                <span class="glyphicon glyphicon-flash"></span> = Snelschaken; 
                                <span class="glyphicon glyphicon-plane"></span> = Rapid
                            </div>
                            <div class="modal-footer">

                                <button type="button" class="btn btn-default" data-dismiss="modal">Sluiten</button>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->    
            </div>
        </div>
    </div>
    </body>
</html>

