<?php
    //WEBSITE STARTUP
    include_once('../../includes/class.init.php');
    include_once('../../includes/class.competition.php');
    include_once('../../includes/class.swiss.php');
    include_once('../../includes/class.player.php');

    $init = new init(1,0,0);
    $competitie = new competition($settings, $init->repository->get_data("competitie"), $init->errorClass, $init->notificationClass);

    $rounds = "";
    foreach($competitie->getRounds() as $round) {
        $rounds .= "<OPTION value=\"" . $round['ronde'] . "\">" . $round['ronde'] . " - " . $round["datum"];
    }

    include_once('../../includes/header.beheer.php');
?>

<body class="container">

    <?php
        include("../../includes/menu.beheer.php");
    ?>       

    <div class="row">
        <div class="col-md-12">
            <form action="compReportCreate.php" method="post" class="form">
                <input type="hidden" name="seizoen" value="<?php echo $init->repository->get_data("seizoen");?>">
                <input type="hidden" name="competitie" value="<?php echo $init->repository->get_data("competitie");?>">
                <input type="hidden" name="participantsSorting">
                <h2>Rating Rapportage</h2>
                <div class="form-group">
                    <label for="exampleInputPassword1">Vanaf Ronde:</label>
                    <SELECT name="van" class="form-control"><?php echo $rounds;?></SELECT>
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1">T/m Ronde:</label>
                    <SELECT name="tot" class="form-control"><?php echo $rounds;?></SELECT>
                </div>
                <button type="submit" class="btn btn-default ">Rapportage</button>
            </form>
        </div>
    </div>
    <div class="row" style="margin-top:20px;">
        <div class="col-sm-12">
<pre>
  Speeldata                 Weergegeven als              Inzenden voor       Verwerkt
  1 januari t/m 31 maart    201X.01.01 t/m 201x.31.03 => 31 maart         => mei
  1 april t/m 30 juni       201X.04.01 t/m 201x.30.06 => 30 juni          => augustus
  1 juli t/m 30 september   201X.07.01 t/m 201x.30.09 => 30 september     => november
  1 oktober t/m 31 december 201X.10.01 t/m 201x.31.12 => 31 december      => februari</pre>
        </div>
    </div>

    </body>
</html>

