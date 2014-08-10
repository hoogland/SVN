<?php
    //WEBSITE STARTUP
    include_once('../../includes/class.init.php');
    $init = new init(1,0,0);

    include_once('../../includes/header.beheer.php');
?>
<body class="container">

    <?php
        include("../../includes/menu.beheer.php");
    ?>       


    <div class="row hidden-print">
        <div class="col-md-12">
            <h1 class="hidden-print">Ledenlijst</h1>
            <table class="table table-condensed table-striped">
            <tr><thead><th>KNSB</th><th>Voorletter</th><th>Voornaam</th><th>tussenvoegsel</th><th>Achternaam</th><th></th></th></thead></tr>
            <?php
                foreach($data->getPlayers() as $player)
                    echo "<tr><td>".$player["knsb"]."</td><td>".$player["voorletters"]."</td><td>".$player["voornaam"]."</td><td>".$player["tussenvoegsel"]."</td><td>".$player["achternaam"]."</td><td><a href='member.php?memberId=".$player["id"]."'><span class='glyphicon glyphicon-edit'></span></a></td></tr>";
            ?>
            </table>
        </div>
    </div>

    </body>
</html>

