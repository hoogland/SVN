<?php
    include('../../includes/header2.archief.php');
print_r($init);
?>

<body ng-app="SVNpublic" ng-controller="externCompetitie">
<navigation></navigation>

<div class="container-fluid" id="externCompetitie">
        <div class="row">
            <div class="col-md-12">
                <table class="table">
                    <tr>
                        <td>Team</td><td><select class="form-control" ng-model="selectedTeam" ng-options="team as team.naam for team in teams.data track by team.id" ng-init="selectedTeam = {id: '<?php echo $_GET['team'];?>'}" ng-change="getMatches()"></select></td>
                        <td>Seizoen</td><td><select class="form-control" ng-model="selectedSeason" ng-options="season as season.naam for season in seasons.data  | orderBy:'naam':true track by season.id"  ng-init="selectedSeason = {id: '<?php echo $_GET['seizoen'];?>'}" ng-change="getMatches()"></select></td>
                    </tr>
                    <tr class="hide"><td>Captain</td><td>{{competitie.Captain}}</td><td colspan="2"></td></td></tr>
                    <tr class="hide"><td>Klasse</td><td>{{competitie.Class}}</td><td>Link</td><td>{{competitie.link}}</td></tr>
                    <tr class="hide"><td colspan=4>{{competitie.Omschrijving}}</td></tr>
                </table>
            </div>
        </div>
        {{defaultData.data.teamName}}
        <div class="row">
            <div class="col-md-9" ng-show="matches.data">
                <h2>Wedstrijden</h2>
                <table class="table table-condensed table-striped">
                    <tr ng-repeat="match in matches2.data"><td>{{defaultData.teamName}} {{match.team}} - </td><td>{{match.datum}}</td><td>{{match.teamElo}}</td></tr>
                </table>
                <div class="externalMatch" ng-repeat="match in matches.data" ng-init="team = (teams.data | filter:{id : match.team} : true)[0]">
                    <div class="matchTitle" ng-click="showDetails = ! showDetails" ng-class="{active:showDetails}"  ng-show="match.uitwedstrijd != 1">
                        <div class="col-sm-2 col-xs-3">{{match.datum | date:'dd-MM-yyyy '}}</div>
                        <div class="col-sm-8 col-xs-7"><div class="col-sm-6">{{defaultData.teamName}} {{team.naamKort}} ({{match.teamElo}})</div><div class="col-sm-6"><span class="hidden-xs inline-block">-</span> {{match.tegenstander}} {{match.tegenstanderTeam}} ({{match.tegenstanderElo}})</div></div>
                        <div class="col-xs-2">{{match.score  | number:1}} - {{match.games.length - match.score | number:1}}</div>
                    </div>
                    <div class="matchTitle" ng-click="showDetails = ! showDetails" ng-class="{active:showDetails}"  ng-show="match.uitwedstrijd == 1">
                        <div class="col-sm-2 col-xs-3">{{match.datum | date:'dd-MM-yyyy '}}</div>
                        <div class="col-sm-8 col-xs-7"><div class="col-sm-6">{{match.tegenstander}} {{match.tegenstanderTeam}} ({{match.tegenstanderElo}})</div><div class="col-sm-6"><span class="hidden-xs inline-block">-</span> {{defaultData.teamName}} {{team.naamKort}} ({{match.teamElo}})</div></div>
                        <div class="col-xs-2">{{match.games.length - match.score | number:1}} - {{match.score  | number:1}}</div>
                    </div>
                    <div class="row matchDetails" ng-show="showDetails">
                        <div class="col-md-12">
                            <h4 ng-show="{{match.verslag || match.verslagTegenstander}}">Verslagen</h4>
                            <div ng-show="{{match.verslag}}"><a ng-href="{{match.verslag}}" target="_blank">{{defaultData.teamName}} {{team.naamKort}}</a></div>
                            <div ng-show="{{match.verslagTegenstander}}"> <a ng-href="{{match.verslagTegenstander}}" target="_blank">{{match.tegenstander}}</a></div>
                            <h4>Partijen</h4>
                            <table class="table table-striped" ng-show="match.uitwedstrijd != 1">
                                <thead><tr><th>Bord</th><th>{{defaultData.teamName}} {{team.naamKort}}</th><th>Rating</th><th></th><th>{{match.tegenstander}} {{match.tegenstanderTeam}}</th><th>Rating</th><th style="text-align:center">Uitslag</th></tr></thead>
                                <tr ng-repeat="game in match.games" ng-init="player = (players.data | filter:{id : game.spelerId} : true)[0]"><td>{{game.bord}}</td><td>{{player.voornaam}} {{player.tussenvoegsel}} {{player.achternaam}}</td><td>{{game.spelerElo}}</td><td>-</td><td>{{game.tegenstanderNaam}}</td><td>{{game.tegenstanderElo}}</td><td style="text-align:center">{{game.score | number}} - {{1 - game.score | number}}</td></tr>
                            </table>
                            <table class="table table-striped" ng-show="match.uitwedstrijd == 1">
                                <thead><tr><th>Bord</th><th>{{match.tegenstander}} {{match.tegenstanderTeam}}</th><th>Rating</th><th></th><th>{{defaultData.teamName}} {{team.naamKort}}</th><th>Rating</th><th style="text-align:center">Uitslag</th></tr></thead>
                                <tr ng-repeat="game in match.games" ng-init="player = (players.data | filter:{id : game.spelerId} : true)[0]"><td>{{game.bord}}</td><td>{{game.tegenstanderNaam}}</td><td>{{game.tegenstanderElo}}</td><td>-</td><td>{{player.voornaam}} {{player.tussenvoegsel}} {{player.achternaam}}</td><td>{{game.spelerElo}}</td><td style="text-align:center">{{1 - game.score | number}} - {{game.score | number}}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3" ng-show="topscorers.data">
                <h2>Topscoorders</h2>
                <table class="table table-condensed table-striped">
                    <thead><tr><th>Speler</th><th>Score</th></tr></thead>
                    <tbody>
                        <tr ng-repeat="topscorer in topscorers.data" ng-animate="'animate'" ng-init="player = (players.data | filter:{id : topscorer.spelerId} : true)[0]"><td>{{player.voornaam}} {{player.tussenvoegsel}} {{player.achternaam}}</td><td>{{topscorer.score}} uit {{topscorer.partijen}}</td></tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.5/angular.min.js""></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.5/angular-resource.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.5/angular-animate.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.3.3/angular-route.js"></script>
    <script src="../js/svn.js"></script>
</body>
</html>
