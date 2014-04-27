<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#"><? echo settings::vereniging;?></a>
    </div>


    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav">
            <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-cog"></span></a>
                <ul class="dropdown-menu">
                    <li class="disabled"><a href="geschiedenis.php"><span class="glyphicon glyphicon-list"></span> Gebruikers</a></li>
                    <li class="divider"></li>
                    <li><a href="usersCreate.php"><span class="glyphicon glyphicon-user"></span> Nieuwe gebruiker</a></li>
                </ul>
            </li>
            <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Ledenadministratie <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="members.php"><span class="glyphicon glyphicon-list"></span> Overzicht</a></li>
                    <li><a href="knsbRating.php"><span class="glyphicon glyphicon-stats"></span> KNSB Rating verwerken</a></li>
                    <li class="divider"></li>
                    <li class="disabled"><a href="kampioenen.php"><span class="glyphicon glyphicon-plus"></span> Nieuw</a></li>
                </ul>
            </li>
            <li class="dropdown disabled"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Seizoenen<span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="externUitslagen.php?seizoen=2010&team=1"><span class="glyphicon glyphicon-list"></span> Overzicht</a></li>
                    <li class="divider"></li>
                    <li><a href="externTopscorers.php"><span class="glyphicon glyphicon-plus"></span> Nieuw</a></li>
                </ul>
            </li>
            <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Competities<span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li class="disabled"><a href=""><span class="glyphicon glyphicon-list"></span> Overzicht</a></li>
                    <li class="disabled"><a href=""><span class="glyphicon glyphicon-plus"></span> Nieuw</a></li>
                    <li class="divider"></li>
                    <li><a href="compSettings.php?seizoen=<? echo $init->repository->get_data("seizoen");?>&competitie=<? echo $init->repository->get_data("competitie");?>"><span class="glyphicon glyphicon-cog"></span> Instellingen</a></li>
                    <li><a href="compPlayers.php?seizoen=<? echo $init->repository->get_data("seizoen");?>&competitie=<? echo $init->repository->get_data("competitie");?>"><span class="glyphicon glyphicon-user"></span> Deelnemers</a></li>
                    <li><a href="compPairing.php?seizoen=<? echo $init->repository->get_data("seizoen");?>&competitie=<? echo $init->repository->get_data("competitie");?>"><span class="glyphicon glyphicon-resize-small"></span> Indeling</a></li>
                    <li><a href="<? echo settings::archive;?>/standen.php?seizoen=<? echo $init->repository->get_data("seizoen");?>&competitie=<? echo $init->repository->get_data("competitie");?>" target="_blank"><span class="glyphicon glyphicon-list-alt"></span> Standen</a></li>
                    <li class="disabled"><a href=""><span class="glyphicon glyphicon-tower"></span> Rating rapportage</a></li>
                    <li class="divider"></li>
                    <li><a href="compExternal.php?seizoen=<? echo $init->repository->get_data("seizoen");?>"><span class="glyphicon glyphicon-road"></span> Extern</a></li>
                </ul>
            </li>
            <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Tools<span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li class=" disabled"><a href="toolFen.php">Gameviewer</a></li>
                    <li><a href="toolFen.php">FEN Viewer</a></li>
                </ul>
            </li>
            <li><a href="help.php"><span class="glyphicon glyphicon-question-sign" title="Help"></span></a>
            </li>
            <?php
                if($init->login->LOGGED_IN != 1)
                    echo '<li class="dropdown"><a href="login.php">Inloggen</span></a></li>';
                else
                    echo '<li class="dropdown"><a href="login.php?logout=true"><span class="glyphicon glyphicon-log-out" title="Uitloggen"></span></span></a></li>';
                ?>           
            
        </ul> 
        <form action="" role="form" class="navbar-form navbar-right" method="get">
            <div class="form-group">
                <select name="seizoen" class="form-control" onchange="this.form.submit()">
                    <option value="">Selecteer een seizoen</option>
                    <? 
                        $data = new data();
                        foreach($data->getSeasons(true) as $season)
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
                    <? 
                        
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