<?php
    //WEBSITE STARTUP
    include_once('../../includes/class.init.php');
   // include_once('../../includes/class.settings.php');
   include_once('../../includes/class.competition.php');
     include_once('../../includes/class.swiss.php');
    include_once('../../includes/class.player.php');
  
    $init = new init(1,0,0);
    include_once('../../includes/header.beheer.php');    
?>

<body class="container">

    <?php
        include("../../includes/menu.beheer.php");
    ?>       


    <div class="row hidden-print">
        <div class="col-md-12">
            <h1 class="hidden-print">Inloggen</h1>
            <form action="index.php" method="post" role="form" class="form-horizontal">
                <div class="form-group">
                    <label for="email" class="col-lg-2 control-label">Gebruikersnaam</label>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="email" name="email">
                    </div>
                </div>            
                <div class="form-group">
                    <label for="password" class="col-lg-2 control-label">Wachtwoord</label>
                    <div class="col-lg-10">
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                </div>            

                <input type="submit" value="Inloggen" class="btn btn-primary pull-right">
            </form>
        </div>
    </div>

    </body>
</html>

