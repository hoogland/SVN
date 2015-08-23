<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#"><?php echo settings::vereniging;?></a>
    </div>


    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav">
            <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>
                <ul class="dropdown-menu">
                    <li class="disabled"><a href="geschiedenis.php"><span class="fa fa-list"></span> Gebruikers</a></li>
                    <li class="divider"></li>
                    <li><a href="usersCreate.php"><span class="fa fa-user"></span> Nieuwe gebruiker</a></li>
                </ul>
            </li>
            <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Ledenadministratie <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="members.php"><span class="fa fa-list"></span> Overzicht</a></li>
                    <li><a href="knsbRating.php"><span class="fa fa-area-chart"></span> KNSB Rating verwerken</a></li>
                    <li class="divider"></li>
                    <li class="disabled"><a href="kampioenen.php"><span class="fa fa-plus"></span> Nieuw</a></li>
                </ul>
            </li>
            <li class="dropdown disabled"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Seizoenen<span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="externUitslagen.php?seizoen=2010&team=1"><span class="fa fa-list"></span> Overzicht</a></li>
                    <li class="divider"></li>
                    <li><a href="externTopscorers.php"><span class="fa fa-plus"></span> Nieuw</a></li>
                </ul>
            </li>
            <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Competities<span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li class="disabled"><a href=""><span class="fa fa-list"></span> Overzicht</a></li>
                    <li class="disabled"><a href=""><span class="fa fa-plus"></span> Nieuw</a></li>
                    <li class="divider"></li>
                    <li><a href="compSettings.php?seizoen=<?php echo $init->repository->get_data("seizoen");?>&competitie=<?php echo $init->repository->get_data("competitie");?>"><span class="fa fa-cog"></span> Instellingen</a></li>
                    <li><a href="compPlayers.php?seizoen=<?php echo $init->repository->get_data("seizoen");?>&competitie=<?php echo $init->repository->get_data("competitie");?>"><span class="fa fa-user"></span> Deelnemers</a></li>
                    <li><a href="compPairing.php?seizoen=<?php echo $init->repository->get_data("seizoen");?>&competitie=<?php echo $init->repository->get_data("competitie");?>"><span class="fa fa-compress"></span> Indeling</a></li>
                    <li><a href="<?php echo settings::archive;?>/standen.php?seizoen=<?php echo $init->repository->get_data("seizoen");?>&competitie=<?php echo $init->repository->get_data("competitie");?>" target="_blank"><span class="fa fa-list-ol"></span> Standen</a></li>
                    <li><a href="compReport.php?seizoen=<?php echo $init->repository->get_data("seizoen");?>&competitie=<?php echo $init->repository->get_data("competitie");?>"><span class="fa fa-file-text-o"></span> Rating rapportage</a></li>
                    <li class="divider"></li>
                    <li><a href="compExternal.php?seizoen=<?php echo $init->repository->get_data("seizoen");?>"><span class="fa fa-road"></span> Extern</a></li>
                </ul>
            </li>
            <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Tools<span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li class=" disabled"><a href="toolFen.php"><span class="fa fa-puzzle-piece"></span> Gameviewer</a></li>
                    <li><a href="toolFen.php"><span class="fa fa-qrcode"></span> FEN Viewer</a></li>
                </ul>
            </li>
            <li><a href="help.php"><span class="fa fa-question-circle" title="Help"></span></a>
            </li>
            <?php
                if($init->login->LOGGED_IN != 1)
                    echo '<li class="dropdown"><a href="login.php">Inloggen</span></a></li>';
                else
                    echo '<li class="dropdown"><a href="login.php?logout=true"><span class="fa fa-power-off" title="Uitloggen"></span></span></a></li>';
                ?>           
            
        </ul> 
        <form action="" role="form" class="navbar-form navbar-right" method="get">
            <div class="form-group">
                <select name="seizoen" class="form-control" onchange="this.form.submit()">
                    <option value="">Selecteer een seizoen</option>
                    <?php 
                        $data = new data();
                        foreach($data->getSeasons(true, true) as $season)
                        {
                            $selected = "";
                            if($season["id"] == $_GET["seizoen"])
                                $selected = "SELECTED";
                            echo "<option value=\"".$season["id"]."\" ".$selected.">".$season["naam"]."</option>";
                        }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <select name="competitie" class="form-control" onchange="this.form.submit()">
                <option value="">Selecteer een competitie</option>
                    <?php 
                        
                        foreach($data->getCompetitions($_GET["seizoen"]) as $competition)
                        {
                            $selected = "";
                            if($competition["id"] == $_GET["competitie"])
                                $selected = "SELECTED";
                            echo "<option value=\"".$competition["id"]."\" ".$selected.">".$competition["naam"]."</option>";
                        }
                    ?>
               
                </select>
            </div>
        </form>
    </div>
</nav>
<?php
    $init->errorClass->display_errors();
    $init->notificationClass->display_notes();
?>