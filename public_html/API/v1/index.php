<?php
/**
 * Created by PhpStorm.
 * User: Rob
 * Date: 27-9-2015
 * Time: 14:58
 */

require '../../../includes/vendor/Slim/Slim.php';

\Slim\Slim::registerAutoloader();
$api = new \Slim\Slim();


//SEASONS
$api->group('/seasons', function() use ($api){
    require_once '../../../includes/src/season.php';
    $api->get('', function(){
       //get all seasons
        $season = new \svn\season();
        echo  json_encode($season->getSeasons());

    });

    $api->get('/:seasonId', function($seasonId){

    });
});

//COMPETITIONS
$api->group('/seasons/:seasonId/competitions', function($seasonId) use ($api) {
    $api->get('', function ($seasonId) {
        require_once '../../../includes/src/season.php';
        //get all competitions of a season
        $season = new \svn\season($seasonId);
         echo  json_encode($season->getCompetitions());
    });
    $api->get('/:competitionId', function ($seasonId, $competitionId) {
        //get all rounds
        echo $seasonId." ".$competitionId;

    });

    //Participants
    $api->group('/competitions/:competitionId', function ($seasonId, $competitionId) use ($api) {
        require_once '../../../includes/src/internal/competition.php';

        $api->get('/participants', function ($seasonId, $competitionId) {
            //get all participants
            $competition = new \svn\competition\competition($competitionId);
            echo  json_encode($competition->getPlayers());
        });

    });

});



//ROUNDS
$api->group('/seasons/:seasonId/competitions/:competitionId/rounds', function($seasonId, $competitionId) use ($api) {
    require_once '../../../includes/src/internal/competition.rounds.php';
    $api->get('', function ($seasonId, $competitionId) {
        //get all rounds
        $rounds = new \svn\competition\round();
        echo  json_encode($rounds->getRounds($competitionId));
    });
    $api->get('/:roundId', function ($seasonId, $competitionId, $roundId) {
        //get round details
        echo $seasonId." ".$competitionId." ".$roundId;

    });
    $api->delete('/:roundId', function ($seasonId, $competitionId, $roundId) {
        $round = new \svn\competition\round($roundId);
        echo $round->deleteRound();
    });
});

//MATCHES


$api->run();