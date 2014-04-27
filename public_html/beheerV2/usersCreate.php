<?
    //WEBSITE STARTUP
    include_once('../../includes/class.init.php');
   // include_once('../../includes/class.settings.php');
    include_once('../../includes/class.competition.php');
    include_once('../../includes/class.swiss.php');
    include_once('../../includes/class.player.php');

    $init = new init(1,0,0);

    include_once('../../includes/header.beheer.php');
    
   
    if($_POST["username"])
    {
        $init->repository->set_data("userEmail", $_POST["username"]);
        $init->repository->set_data("userPassword", $_POST["userPassword"]);
        $init->repository->set_data("userPassword2", $_POST["userPassword"]);
        $init->repository->set_data("createAccount", 2);
        $init->login->register();
    }
?>

<body class="container">

    <? 
        include("../../includes/menu.beheer.php");
    ?>       


    <div class="row hidden-print">
        <div class="col-md-12">
            <h1 class="hidden-print">Gebruikers - Aanmaken</h1>
            <form action="usersCreate.php" method="post" role="form" class="form-horizontal">
                <div class="form-group">
                    <label for="username" class="col-lg-2 control-label">Gebruikersnaam</label>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="username" name="username">
                    </div>
                </div>            
                <div class="form-group">
                    <label for="password" class="col-lg-2 control-label">Wachtwoord</label>
                    <div class="col-lg-10">
                        <input type="password" class="form-control" id="userPassword" name="userPassword">
                    </div>
                </div>            

                <input type="submit" value="Aanmaken" class="btn btn-primary pull-right">
            </form>
        </div>
    </div>

    </body>
</html>

