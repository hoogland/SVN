<nav class="navbar navbar-default navbar-static-top" role="navigation">
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
            <li><a href="speler.php">Spelers</a></li>
            <li><a href="standen.php?seizoen=<? echo settings::standardCompetitionSeason;?>&competitie=<? echo settings::standardCompetition;?>">Competities</a></li>
            <li class="dropdown hidden"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Historie <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="geschiedenis.php">Competitie</a></li>
                    <li><a href="kampioenen.php">Kampioenen</a></li>
                    <li><a href="canon.php">Canon</a></li>
                </ul>
            </li>
            <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Extern <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="externUitslagen.php?seizoen=<? echo settings::standardCompetitionExternalSeason;?>&team=<? echo settings::standardCompetitionExternal;?>">Uitslagen</a></li>
                </ul>
            </li>
        </ul> 
        <form action="" role="form" class="navbar-form navbar-right" method="get">
            <div class="form-group">
                <select name="seizoen" class="form-control" onchange="this.form.submit()">
                    <option value="">Selecteer een seizoen</option>
                    <? 
                        $data = new data($settings);
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
