<?php
    //WEBSITE STARTUP
    include_once('../../includes/class.init.php');
    // include_once('../../includes/class.settings.php');
    include_once('../../includes/class.competition.php');
    include_once('../../includes/class.swiss.php');
    include_once('../../includes/class.player.php');

    $init = new init(1,0,0);

    include_once('../../includes/header.beheer.php');

    if(isset($_FILES['knsb']))
    {
        include_once('../../includes/class.knsb.php');
        $knsb = new knsb($settings, $init->errorClass, $init->notificationClass);
        $knsb->insertKNSB($_FILES['knsb'], $_POST['knsbDate']);
    }

    include_once('../../includes/class.knsb.php');
    $knsb = new knsb($settings, $init->errorClass, $init->notificationClass);
    $knsb->getRatings(92);
?>
<script>
    $(function() {
        $( "#knsbDate" ).datepicker({dateFormat: "dd/mm/yy" });
    })
</script>
<body class="container" ng-app="svnKNSB">

    <?php
        include("../../includes/menu.beheer.php");
    ?>       


    <div class="row hidden-print">
        <div class="col-md-12" ng-controller="knsbLists">
            <h1 class="hidden-print">KNSB Ratinglijst verwerken</h1>
            <h2>KNSB Rating</h2>
            <div class="form-group row">
                <label for="knsbList" class="col-lg-2 control-label">Ratinglijst:</label>
                <div class="col-lg-3">
                    <select name="knsbList" class="form-control" ng-model="knsbList" ng-options="ratingList.value for ratingList in ratingLists track by ratingList.id" ng-change="knsbListGet(knsbList)"></select>
                </div>
                <button ng-click="knsbProcessExternalRatings(knsbList)" class="btn btn-primary pull-right">Verwerken</button>
            </div>
            
            
            <div class="row">
                Clubs
                <div class="progress">
                    <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: {{knsbClubProgress / knsbClubs.length * 100}}%;">
                    </div>
                </div>
                Spelers
                <div class="progress">
                    <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: {{knsbClubsProgress / knsbPlayers * 100}}%;">
                    </div>
                </div>
            </div>           
             <h2>Mark Huizer site</h2>
            <div class="form-group">
                <label for="knsbList" class="col-lg-2 control-label">Ratinglijst:</label>
                <div class="col-lg-3">
                    <select name="knsbList" class="form-control" ng-model="knsbList" ng-options="ratingList.value for ratingList in ratingLists track by ratingList.id" ng-change="knsbListGet(knsbList)"></select>
                </div>
            </div>
            <button ng-click="knsbInsertRating()" class="btn btn-primary pull-right">Verwerken</button> 
            <table  class="table table-condensed table-striped">
                <thead><tr><th>KNSB</th><th>Naam</th><th>Rating</th><th>Verschil</th></tr></thead>
                <tr ng-repeat="userRating in userRatings" ng-class="{success: userRating.result.code == 200, danger : userRating.result != undefined && userRating.result.code != 200}"><td>{{userRating.knsb}}</td><td>{{userRating.name}}</td><td>{{userRating.rating}}</td><td>{{userRating.ratingDiff}}</td><td>{{userRating.result.message}}</td></tr>
            </table>

            
            <hr style="clear:both">
            <h2>KNSB lijst</h2>
            <form action="knsbRating.php" method="post" role="form" class="form-horizontal" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="username" class="col-lg-2 control-label">Ratingdatum:</label>
                    <div class="col-lg-3">
                        <input type="text" class="form-control" id="knsbDate" name="knsbDate">
                    </div>
                </div>            
                <div class="form-group">
                    <label for="password" class="col-lg-2 control-label">CSV-bestand</label>
                    <div class="col-lg-3">
                        <input type="file" id="knsbFile" name="knsb">
                    </div>
                </div>            

                <input type="submit" value="Verwerken" class="btn btn-primary pull-right">
            </form>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.21/angular.min.js"></script>
    <script src="../js/svnKNSB.js"></script>
    </body>
</html>

