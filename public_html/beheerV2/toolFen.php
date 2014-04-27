<?
    //WEBSITE STARTUP
    include_once('../../includes/class.init.php');
    // include_once('../../includes/class.settings.php');
    include_once('../../includes/class.competition.php');
    include_once('../../includes/class.swiss.php');
    include_once('../../includes/class.player.php');

    $init = new init(1,0,0);

    include_once('../../includes/header.beheer.php');
?>
<script type="text/javascript">
    $(function() {
        $("#FENcode").change(
            function()
            {
                $("#FENlink").val("<?php echo settings::archive;?>/FEN/"+$(this).val().substring(0,+$(this).val().indexOf(" ")));
                $("#FENimage").html('<img src="<?php echo settings::archive;?>/FEN/'+$(this).val().substring(0,+$(this).val().indexOf(" "))+'">');           
            }
        )
    })

</script>
<body class="container">

    <? 
        include("../../includes/menu.beheer.php");
    ?>       


    <div class="row hidden-print">
        <div class="col-md-12">
            <h1 class="hidden-print">FEN Viewer</h1>
            <div class="form-horizontal">
                <div class="form-group">
                    <label for="FENcode" class="col-sm-2 control-label">FEN code:</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="FENcode" placeholder="FEN code">
                    </div>
                </div>
                <div class="form-group">
                    <label for="FENlink" class="col-sm-2 control-label">Link:</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="FENlink">
                    </div>
                </div>
                <div class="form-group">
                    <label for="FENimage" class="col-sm-2 control-label">Voorbeeld:</label>
                    <div class="col-sm-10" id="FENimage">
                    </div>
                </div>
            </div>

        </div>
    </div>

    </body>
</html>

