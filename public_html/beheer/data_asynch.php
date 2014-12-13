<?php
    include_once('../../includes/class.init.php');
    include_once('../../includes/class.player.php');
    include('../../includes/class.data.php');
    $init = new init(1,0,0);
    $data = new data();

    switch($init->repository->get_data("asynchAction"))
    {
        case 1: memberSearch($init, $data); break;
        case 2: knsbSearch($init, $data); break;
    }


    function memberSearch($init, $data)
    {
        $result = $data->getMembersExternal($init->repository->get_data("searchFilter"));
        echo json_encode($result);
    }
    
    function knsbSearch($init, $data)
    {
        $result = $data->getKNSBPlayers($init->repository->get_data("searchFilter"));
        if(count($result) > 0)
            echo json_encode($result);
        else
            echo json_encode(array(array("id" => 1,"naam" => $init->repository->get_data("searchFilter"))));
    }

?>
