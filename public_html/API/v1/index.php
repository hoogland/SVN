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

//UP & RUNNING
$api->get('/status', function(){
    echo "Up & Running";
});


//Configuration
$api->group('/data/config', function() use ($api){
    $api->get('', function(){
        require_once '../../../includes/src/generic.php';
        $data = new \svn\generic();
        echo  json_encode($data->getConfig(), JSON_NUMERIC_CHECK);
    });
});

//Bye Types
$api->group('/data/byeTypes', function() use ($api){
    $api->get('', function(){
        require_once '../../../includes/src/generic.php';
        $data = new \svn\generic();
        echo  json_encode($data->getByeTypes(), JSON_NUMERIC_CHECK);
    });
});

//Bye Types
$api->group('/data/columns', function() use ($api){
    $api->get('/:columnType', function($columnType){
        require_once '../../../includes/src/generic.php';
        $data = new \svn\generic();
        echo  json_encode($data->getCompFields($columnType), JSON_NUMERIC_CHECK);
    });
});

//MEMBERS
$api->group('/members', function() use ($api){
    require_once '../../../includes/src/generic.php';
    $api->get('', function(){
        //get all seasons
        $data = new \svn\generic();
        echo  json_encode($data->getMembers(), JSON_NUMERIC_CHECK);
    });
    //Update a member
    $api->put('/:memberId', function ($memberId) use ($api) {
        $generic = new \svn\generic();
        $data = json_decode($api->request->getBody());
        if($memberId && $data)
            echo json_encode($generic->updateMember($memberId, $data), JSON_NUMERIC_CHECK);
    });
    //Create option
    $api->post('', function() use ($api){
        $generic = new \svn\generic();
        $data = json_decode($api->request->getBody(), true);
        echo json_encode($generic->createMember($data), JSON_NUMERIC_CHECK);
    });
});


//SEASONS
$api->group('/seasons', function() use ($api){
    $api->get('', function(){
        require_once '../../../includes/src/generic.php';
        //get all seasons
        $season = new \svn\generic();
        echo  json_encode($season->getSeasons(), JSON_NUMERIC_CHECK);

    });

    $api->put('', function() use ($api){
        $season = new \svn\competition\season();
        $data = json_decode($api->request->getBody());
        echo json_encode($season->createSeason($data->season));
    });

    $api->get('/:seasonId', function($seasonId){
        echo "SEASON DETAILS";
    });

    $api->get('/:seasonId/competitions', function ($seasonId) {
        require_once '../../../includes/src/generic.php';
        //get all competitions of a season
        $season = new \svn\generic($seasonId);
        echo  json_encode($season->getCompetitions(), JSON_NUMERIC_CHECK);
    });
});

//COMPETITION
$api->group('/competitions/:competitionId', function($competitionId) use ($api) {
    $api->group('/options', function ($competitionId) use ($api) {
        require_once '../../../includes/src/internal/competition.php';
        $api->get('', function ($competitionId) {
            //get comp options
            $competition = new \svn\competition\competition($competitionId);
            echo json_encode($competition->getOptions(), JSON_NUMERIC_CHECK);
        });
        //Update an option
        $api->put('/:optionId', function ($competitionId, $optionId) use ($api) {
            $competition = new \svn\competition\competition($competitionId);
            $data = json_decode($api->request->getBody());
            echo json_encode($competition->updateOption($optionId, $data->option), JSON_NUMERIC_CHECK);
        });
        //Create option
        $api->post('', function($competitionId) use ($api){
            $competition = new \svn\competition\competition($competitionId);
            $data = json_decode($api->request->getBody());
            echo json_encode($competition->createOption($data->option), JSON_NUMERIC_CHECK);
        });
    });

    //Standings
    $api->group('/standings/:roundId', function ($competitionId, $roundId) use ($api) {
        $api->get('', function ($competitionId, $roundId) {
            require_once '../../../includes/src/internal/competition.standing.php';
            //Get the standings
            $standing = new \svn\competition\standing($competitionId);
            echo json_encode($standing->getStanding($roundId), JSON_NUMERIC_CHECK);
        });
        $api->put('', function ($competitionId, $roundId) {
            require_once '../../../includes/src/internal/competition.standing.php';
            //Save the standings
            $standing = new \svn\competition\standing($competitionId);
            $standing->saveStanding($roundId);
            echo json_encode(true, JSON_NUMERIC_CHECK);
        });
    });

    //Get all matches based on roundnr and comeptitionId
    $api->get('/rounds/:round/matches', function ($competitionId, $round) {
        require_once '../../../includes/src/internal/competition.php';
        //get all matches from a round
        $competition = new svn\competition\competition($competitionId);
        echo json_encode($competition->getGames(null, $round), JSON_NUMERIC_CHECK);
    });
    //Get all byes based on roundnr and comeptitionId
    $api->get('/rounds/:round/byes', function ($competitionId, $round) {
        require_once '../../../includes/src/internal/competition.php';
        //get all matches from a round
        $competition = new svn\competition\competition($competitionId);
        echo json_encode($competition->getByes(null, null, $round), JSON_NUMERIC_CHECK);
    });

    //Get all matches of a player based on playerId and comeptitionId
    $api->get('/players/:round/matches', function ($competitionId, $player) {
        require_once '../../../includes/src/internal/competition.php';
        //get all matches from a round
        $competition = new svn\competition\competition($competitionId);
        echo json_encode($competition->getGames(null, null, $player), JSON_NUMERIC_CHECK);
    });
    //Get all matches of a player based on playerId and comeptitionId
    $api->get('/players/:round/byes', function ($competitionId, $player) {
        require_once '../../../includes/src/internal/competition.php';
        //get all matches from a round
        $competition = new svn\competition\competition($competitionId);
        echo json_encode($competition->getByes(null, $player), JSON_NUMERIC_CHECK);
    });


        //Participants
    $api->group('/participants', function ($competitionId) use ($api) {
        require_once '../../../includes/src/internal/competition.php';

        $api->get('', function ($competitionId) use ($api) {
            //Retrieve all participants
            $competition = new \svn\competition\competition($competitionId);
            echo json_encode($competition->getPlayers(), JSON_NUMERIC_CHECK);
        });

        //Create participant
        $api->post('', function($competitionId) use ($api){
            $competition = new \svn\competition\competition($competitionId);
            $data = json_decode($api->request->getBody());
            echo json_encode($competition->addPlayer($data->player), JSON_NUMERIC_CHECK);
        });

        //Update a participant
        $api->put('/:participantId', function ($competitionId, $participantId) use ($api) {
            $competition = new \svn\competition\competition($competitionId);
            $data = json_decode($api->request->getBody());
            echo json_encode($competition->updatePlayer($participantId, $data->player), JSON_NUMERIC_CHECK);
        });

        //Deleting a participant
        $api->delete('/:participantId', function($competitionId, $participantId){
            $competition = new \svn\competition\competition($competitionId);
            echo json_encode($competition->deletePlayer($participantId), JSON_NUMERIC_CHECK);
        });
    });


    //Rounds
    $api->group('/rounds', function ($competitionId) use ($api) {
        require_once '../../../includes/src/internal/competition.rounds.php';
        $api->get('', function ($competitionId) {
            //get all rounds
            $rounds = new \svn\competition\round();
            echo json_encode($rounds->getRounds($competitionId), JSON_NUMERIC_CHECK);
        });

        //Create round
        $api->post('', function($competitionId) use ($api){
            $rounds = new \svn\competition\round();
            $data = json_decode($api->request->getBody());
            echo json_encode($rounds->createRound($data->round), JSON_NUMERIC_CHECK);
        });
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

/**
 * MATCHES
 */

//get all matches of a round
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

/**
 * Byes
 */

//get all byes of a round
$api->get('/rounds/:roundId/byes', function ($roundId) {
    require_once '../../../includes/src/internal/competition.php';
    //get all byes from a round
    $competition = new svn\competition\competition();
    echo json_encode($competition->getByes($roundId), JSON_NUMERIC_CHECK);
});

//Create a bye
$api->post('/byes', function() use ($api){
    require_once '../../../includes/src/internal/competition.php';
    $competition = new \svn\competition\competition();
    $data = json_decode($api->request->getBody());
    echo json_encode($competition->createBye($data->round, $data->player, $data->bye), JSON_NUMERIC_CHECK);
});

$api->group('/byes/:byeId', function ($byeId) use ($api){
    require_once '../../../includes/src/internal/competition.php';

    //Update a game
    $api->put('', function ($byeId) use ($api) {
        $competition = new \svn\competition\competition();
        $data = json_decode($api->request->getBody());
        echo json_encode($competition->updateBye($byeId, $data->bye), JSON_NUMERIC_CHECK);
    });

    //Deleting a game
    $api->delete('', function($byeId){
        $competition = new \svn\competition\competition();
        echo json_encode($competition->deleteBye($byeId), JSON_NUMERIC_CHECK);
    });
});


//External
$api->group('/external', function() use ($api){
    //Teams
    $api->get('/teams', function(){
        require_once '../../../includes/src/external/teams.php';
        //get all teams
        $teams = new \svn\teams();
        echo  json_encode($teams->getTeams(), JSON_NUMERIC_CHECK);
    });

    $api->group('/seasons/:seasonId/teams/:teamId', function($seasonId, $teamId) use ($api) {
        $api->get('/matches', function ($seasonId, $teamId) {
            require_once '../../../includes/src/external/matches.php';
            $teams = new \svn\matches();
            echo json_encode($teams->getTeamMatches($seasonId, $teamId), JSON_NUMERIC_CHECK);
        });
    });
});

$api->run();
