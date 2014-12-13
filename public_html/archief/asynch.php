<?php
    include_once('../../includes/class.init.php');
    include_once('../../includes/class.player.php');
    include('../../includes/class.data.php');
    $init = new init(0,0,0);
    $data = new data();

    switch($init->repository->get_data("asynchAction"))
    {
        case 1: getRatingData($init, $data); break;
        case 2: getScore($init, $data); break;
        case 3: getOpponentMatches($init, $data); break;
        case 4: getOpponentScores($init, $data); break;
    }
    
    function getRatingData($init, $data)
    {
        $player = new player($init->settings, $init->repository->get_data("player"));
        echo json_encode($player->getRatingData());
    }
    
    function getScore($init, $data)
    {
        $player = new player($init->settings, $init->repository->get_data("player"));
        echo json_encode($player->getScores($init->repository->get_data("color"),$init->repository->get_data("tempo")));
    }
    
    function getOpponentMatches($init, $data)
    {
        $player = new player($init->settings, $init->repository->get_data("player"));
        $result["games"] = $player->getOpponentMatches($init->repository->get_data("opponent"));
        $result["players"][$player->id] = $player->name;
        $opponent = new player($init->settings, $init->repository->get_data("opponent"));
        $result["players"][$opponent->id] = $opponent->name;
        echo json_encode($result);
    }
    
    function getOpponentScores($init, $data)
    {
        $player = new player($init->settings, $init->repository->get_data("player"));
        $scores = $player->getOpponentScores($init->repository->get_data("tempo"));
        
        foreach($scores as $key => $opponentData)
        {
            $opponent = new player($settings, $opponentData["TegenstanderId"]);
            $opponent->getDetails();
            $scores[$key]["name"] = $opponent->name;
            
        }
        
        echo json_encode($scores);
    }
    

?>
