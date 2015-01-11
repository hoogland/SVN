<?php
    //WEBSITE STARTUP
    include_once('../../includes/class.init.php');
    include_once('../beheerOld/class_competitie.php');

    $init = new init(1,0,0);

    $competitie = new competitie();
    $competitie->rating_rapportage($_POST['competitie'], $_POST['van'], $_POST['tot']);
?>