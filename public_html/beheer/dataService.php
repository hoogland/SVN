<?php
    include_once('../../includes/class.init.php');
    $init = new init(1,0,0);

    include_once('../../includes/class.knsb.php');
    $class = new knsb($settings, $init->errorClass, $init->notificationClass);
    include_once('../../includes/class.data.php');
    $dataClass = new data();


    $data = file_get_contents("php://input");

    $objData = json_decode($data);
    if($objData->action == "getLists")
        echo json_encode($class->getRatingLists());
    if($objData->action == "getList")
        echo json_encode($class->getRatings($objData->list));
    if($objData->action == "getKNSBList")
        echo json_encode($class->getRatings($objData->list, $objData->club, $objData->date));
    if($objData->action == "insertRating")
        echo json_encode($class->insertRating($objData->date,$objData->player,$objData->rating));
    if($objData->action == "insertKNSBRating")
        echo json_encode($class->insertKNSBRating($objData->date,$objData->player,$objData->club,$objData->name,$objData->rating));
    if($objData->action == "getClubs")
     echo json_encode($dataClass->getClubs()); 
        
?>
