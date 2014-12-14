<?php
    include_once('../../includes/class.init.php');
    include_once('../../includes/class.webservice.php');
    $init = new init(0,0,0);
    //echo json_encode($_GET);
    //exit();
    $data = file_get_contents("php://input");
    $objData = json_decode($data);
    
    $data = json_decode(json_encode($_GET), FALSE);
     $data = $objData;
    //Extra security due to public webservice without login

    if($data->method != "GET")
        echo json_encode(array("status" => array("code" => "403", "text" => "Alleen GET-acties zijn toegestaan")));
    else
    {
        $webservice = new webservice($init);
        echo json_encode($webservice->execute($data));
    };
        
?>
