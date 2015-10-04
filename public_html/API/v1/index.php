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
        echo  json_encode($season->getSeasons(), JSON_NUMERIC_CHECK);

    });

    $api->get('/:seasonId', function($seasonId){
        echo "SEASON DETAILS";
    });

    $api->get('/:seasonId/competitions', function ($seasonId) {
        require_once '../../../includes/src/season.php';
        //get all competitions of a season
        $season = new \svn\season($seasonId);
        echo  json_encode($season->getCompetitions(), JSON_NUMERIC_CHECK);
    });
});

//COMPETITION
$api->group('/competitions/:competitionId', function($competitionId) use ($api) {
    $api->get('', function ($competitionId) {
        //get comp details
        echo $competitionId;
    });

    //Participants
    $api->group('', function ($competitionId) use ($api) {
        require_once '../../../includes/src/internal/competition.php';

        $api->get('/participants', function ($competitionId) {
            //get all participants
            $competition = new \svn\competition\competition($competitionId);
            echo json_encode($competition->getPlayers(), JSON_NUMERIC_CHECK);
        });
    });


    //Rounds
    $api->get('/rounds', function ($competitionId) {
        //get all rounds
        require_once '../../../includes/src/internal/competition.rounds.php';
        $rounds = new \svn\competition\round();
        echo  json_encode($rounds->getRounds($competitionId), JSON_NUMERIC_CHECK);
    });

});



//ROUND
$api->group('/rounds/:roundId', function($roundId) use ($api) {
    require_once '../../../includes/src/internal/competition.rounds.php';
    $api->get('', function ($roundId) {
        //get round details
        echo $roundId;

    });
    $api->delete('', function ($roundId) {
        $round = new \svn\competition\round($roundId);
        echo $round->deleteRound();
    });

});

//MATCHES
$api->get('/rounds/:roundId/matches', function ($roundId) {
    require_once '../../../includes/src/internal/competition.php';
    //get all matches from a round
    $competition = new svn\competition\competition();
    echo json_encode($competition->getGames($roundId), JSON_NUMERIC_CHECK);
});

//Create a match
$api->post('/matches', function() use ($api){
    require_once '../../../includes/src/internal/competition.php';
    $competition = new \svn\competition\competition();
    $data = json_decode($api->request->getBody());
    echo json_encode($competition->createGame($data->competition, $data->round, $data->speler_wit, $data->speler_zwart), JSON_NUMERIC_CHECK);
});

$api->group('/matches/:matchId', function ($matchId) use ($api){
    require_once '../../../includes/src/internal/competition.php';

    //Update a game
    $api->put('', function ($matchId) use ($api) {
        $competition = new \svn\competition\competition();
        $data = json_decode($api->request->getBody());
        echo json_encode($competition->updateGame($matchId, $data->match), JSON_NUMERIC_CHECK);
    });

    //Deleting a game
    $api->delete('', function($matchId){
        $competition = new \svn\competition\competition();
        echo json_encode($competition->deleteGame($matchId), JSON_NUMERIC_CHECK);
    });
});

$api->run();
