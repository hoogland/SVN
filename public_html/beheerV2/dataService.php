<?php
    include_once('../../includes/class.init.php');
    $init = new init(1,0,0);

    include_once('../../includes/class.knsb.php');

    $class = new knsb($settings, $init->errorClass, $init->notificationClass);


    $data = file_get_contents("php://input");

    $objData = json_decode($data);
    if($objData->action == "getLists")
        echo json_encode($class->getRatingLists());
    if($objData->action == "getList")
        echo json_encode($class->getRatings($objData->list));
    if($objData->action == "insertRating")
        echo json_encode($class->insertRating($objData->date,$objData->player,$objData->rating));
?>
