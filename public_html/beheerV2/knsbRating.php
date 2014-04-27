<?
    //WEBSITE STARTUP
    include_once('../../includes/class.init.php');
   // include_once('../../includes/class.settings.php');
    include_once('../../includes/class.competition.php');
    include_once('../../includes/class.swiss.php');
    include_once('../../includes/class.player.php');

    $init = new init(1,0,0);

    include_once('../../includes/header.beheer.php');
    
    
    if(isset($_FILES['knsb']))
    {
        include('../../includes/class.knsb.php');
        $knsb = new knsb($settings, $init->errorClass, $init->notificationClass);
        $knsb->insertKNSB($_FILES['knsb'], $_POST['knsbDate']);
    }
?>
<script>
    $(function() {
        $( "#knsbDate" ).datepicker({dateFormat: "dd/mm/yy" });
    })
    </script>
<body class="container">

    <? 
        include("../../includes/menu.beheer.php");
    ?>       


    <div class="row hidden-print">
        <div class="col-md-12">
            <h1 class="hidden-print">KNSB Ratinglijst verwerken</h1>
            <form action="knsbRating.php" method="post" role="form" class="form-horizontal" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="username" class="col-lg-2 control-label">Ratingdatum:</label>
                    <div class="col-lg-3">
                        <input type="text" class="form-control" id="knsbDate" name="knsbDate">
                    </div>
                </div>            
                <div class="form-group">
                    <label for="password" class="col-lg-2 control-label">CSV-bestand</label>
                    <div class="col-lg-3">
                        <input type="file" id="knsbFile" name="knsb">
                    </div>
                </div>            

                <input type="submit" value="Verwerken" class="btn btn-primary pull-right">
            </form>
        </div>
    </div>

    </body>
</html>

