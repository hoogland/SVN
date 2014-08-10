<?php
    //WEBSITE STARTUP
    include_once('../../includes/class.init.php');
    include_once('../../includes/class.player.php');
    $init = new init(1,0,0);

    $settings = new settings();
    $member = new player($settings, $init->repository->get_data("memberId"));
    
    if($init->repository->get_data("memberAction") == 1)
        $member->setDetails($init->repository->get_data("memberKNSB"), $init->repository->get_data("memberInitials"), $init->repository->get_data("memberFirstname"), $init->repository->get_data("memberMiddlename"), $init->repository->get_data("memberSurname"));
    $member->getDetails();
    include_once('../../includes/header.beheer.php');
?>
<body class="container">

    <?php
        include("../../includes/menu.beheer.php");
    ?>       


    <div class="row hidden-print">
        <div class="col-md-12">
            <h1 class="hidden-print">Leden bewerken</h1>
            <form class="form-horizontal" method="post">
                <input type="hidden" name="memberId" value=<?php echo $init->repository->get_data("memberId");?>>
                <input type="hidden" name="memberAction" value=1>
                <fieldset>

                    <!-- Form Name -->
                    <legend><?php echo $member->name;?></legend>
                    <!-- Text input-->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="memberKNSB">KNSB</label>  
                        <div class="col-md-4">
                            <input id="memberKNSB" name="memberKNSB" type="text" placeholder="KNSB" class="form-control input-md" value="<?php echo $member->knsb;?>">

                        </div>
                    </div>

                    <!-- Text input-->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="memberInitials">Voorletter(s)</label>  
                        <div class="col-md-4">
                            <input id="memberInitials" name="memberInitials" type="text" placeholder="Voorletter(s)" class="form-control input-md" value="<?php echo $member->initials;?>">

                        </div>
                    </div>

                    <!-- Text input-->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="memberFirstname">Voornaam</label>  
                        <div class="col-md-4">
                            <input id="memberFirstname" name="memberFirstname" type="text" placeholder="Voornaam" class="form-control input-md" value="<?php echo $member->firstname;?>">

                        </div>
                    </div>

                    <!-- Text input-->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="memberMiddlename">Tussenvoegsel</label>  
                        <div class="col-md-4">
                            <input id="memberMiddlename" name="memberMiddlename" type="text" placeholder="Tussenvoegsel" class="form-control input-md" value="<?php echo $member->middlename;?>">

                        </div>
                    </div>

                    <!-- Text input-->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="memberSurname">Achternaam</label>  
                        <div class="col-md-4">
                            <input id="memberSurname" name="memberSurname" type="text" placeholder="Achternaam" class="form-control input-md" value="<?php echo $member->surnameClean;?>">

                        </div>
                    </div>

                    <!-- Button -->
                    <div class="form-group">
                        <label class="col-md-4 control-label" for="send"></label>
                        <div class="col-md-4">
                            <button id="send" name="send" class="btn btn-success">Opslaan</button>
                        </div>
                    </div>

                </fieldset>
            </form>

        </div>
    </div>

    </body>
</html>

