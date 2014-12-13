<?php
    //WEBSITE STARTUP
    include_once('../../includes/class.init.php');
    // include_once('../../includes/class.settings.php');
    include_once('../../includes/class.competition.php');
    include_once('../../includes/class.swiss.php');
    include_once('../../includes/class.player.php');

    $init = new init(0,0,0);

    include_once('../../includes/header.beheer.php');
?>

<body class="container">

    <?php
        include("../../includes/menu.beheer.php");
    ?>       


    <div class="row">
        <div class="col-md-3">
            <div class="bs-sidebar hidden-print affix" role="complementary">
                <ul class="nav bs-sidebar">
                    <li><a href="#Algemeen">Algemeen</a></li>
                    <li><a href="#Leden">Ledenadministratie</a></li>
                    <li><a href="#Competitie">Competitie</a>
                        <ul class="nav">
                            <li><a href="#CompetitieInstellingen">Instellingen</a></li>
                            <li><a href="#CompetitieDeelnemers">Deelnemers</a></li>
                            <li><a href="#CompetitieIndeling">Indeling</a></li>
                            <li><a href="#CompetitieStand">Stand</a></li>
                            <li><a href="#CompetitieRatingRapportage">Rating rapportage</a></li>
                        </ul>
                    </li>
                    <li><a href="#Algemeen">Tools</a></li>
                </ul>
            </div>
        </div>
        <div class="col-md-9" >
            <div class="bs-docs-section">
                <div class="page-header">
                    <h1 id="Algemeen">Algemeen</h1>
                </div>
                <h3 id=""></h3>
                <p>

                </p>
            </div>
            <div class="bs-docs-section">
                <div class="page-header">
                    <h1 id="Leden">Ledenadministratie</h1>
                </div>
                <h3 id=""></h3>
                <p>

                </p>
            </div>
            <div class="bs-docs-section">
                <div class="page-header">
                    <h1 id="Competitie">Competitie</h1>
                </div>
                <h3 id="CompetitieInstellingen">Instellingen</h3>
                <p>

                </p>
                <h3 id="CompetitieDeelnemers">Deelnemers</h3>
                <p>

                </p>
                <h3 id="CompetitieIndeling">Indeling</h3>
                <p>
                    <iframe width="800" height="400" src="//www.youtube.com/embed/Y62YGLVL-2Q" frameborder="0" allowfullscreen></iframe>
                </p>
                <h3 id="CompetitieStand">Stand</h3>
                <p>

                </p>
                <h3 id="CompetitieRatingRapportage">Rating rapportage</h3>
                <p>

                </p>
            </div>
            <div class="bs-docs-section">
                <div class="page-header">
                    <h1 id=""></h1>
                </div>
                <h3 id=""></h3>
                <p>

                </p>
            </div>
        </div>
    </div>

    </body>
</html>

