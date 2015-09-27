<?php
    //WEBSITE STARTUP
    include_once('../../includes/class.init.php');
    include_once('../../includes/competition.php');
    include_once('../../includes/class.swiss.php');
    include_once('../../includes/class.player.php');

    $init = new init(0,0,0);                        

    include_once('../../includes/header.beheer.php');     
?>

<body class="container">

    <?php
        include("../../includes/menu.beheer.php");        
    ?>       


    <div class="row hidden-print">
        <div class="col-md-12">
            <h1 class="hidden-print"><?php echo settings::vereniging;?> Beheersite</h1>
        </div>
    </div>
    </body>
</html>

