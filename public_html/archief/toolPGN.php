<?php
//WEBSITE STARTUP
include_once('../../includes/class.settings.php');
include_once('../../includes/competition.php');
include_once('../../includes/class.swiss.php');
include_once('../../includes/class.player.php');

$swiss = new swiss();
$settings = new settings();

include_once('../../includes/header.archief.php');


$pgn;$title;

if(isset($_GET["pgn"]))
{
    $pgn = $_GET['pgn'];
    $title = $_GET['title'];
}
if(isset($_GET["gameId"]))
{
    $sql = "SELECT * FROM ".$settings->prefix."partijen_pgn WHERE id = ".$_GET["gameId"];
    $sql = mysql_query($sql);
    $result = mysql_fetch_assoc($sql);
    $pgn =  $result["pgn"];
    $pgn = str_replace("\r\n", " ", $pgn);
    $pgn = str_replace("\n", " ", $pgn);
    $pgn = str_replace("  "," ", $pgn);

}

//Set match details
$items = array("Event","Site","Date","Round","White","Black","WhiteElo","BlackElo","Result","ECO","Annotator");
$pgnInfo;
foreach($items as $item)
{
    preg_match_all("/\[".$item." \"(.*?)\"\]/", $pgn, $itemInfo);
    $pgnInfo[$item] = $itemInfo[1][0];

    if($item == "Date" && $pgnInfo[$item] <> "")
    {
        $date = explode(".",$pgnInfo[$item]);
        $pgnInfo[$item] = $date[2]."/".$date[1]."/".$date[0];
    }

}

?>
<script type="text/javascript" src="http://chesstempo.com/js/pgnyui.js" /></script>
<script type="text/javascript" src="http://chesstempo.com/js/pgnviewer.js"></script>
<link type="text/css" rel="stylesheet" href="http://chesstempo.com/css/board-min.css" />
<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css" rel="stylesheet">

<script>
    new PgnViewer(
        { boardName: "demo",
            pgnString: '<?php echo $pgn;?>',
            boardImagePath: "http://www.svnieuwerkerk.nl/archief/pgn",
            pieceSet: 'merida',
            pieceSize: 35,
            showCoordinates: true,
            autoScrollMoves: true,
            addVersion: false,
            hideBracketsOnTopLevelVariation: true,
            movesFormat: 'main_on_own_line',
            moveAnimationLength: 0.1,
            pauseBetweenMoves: 800,
            newlineForEachMainMove: false,
        }
    );
    $(function () {
        setText();
        $(window).resize(function(){setText()});
        $("#hidePGNinfo").click(function(){
            $("#gameInfo table").toggle();
            $("#hidePGNinfo").toggleClass("glyphicon-resize-small");
            $("#hidePGNinfo").toggleClass("glyphicon-resize-full");
            setHeight();
        })
    })

    function setText()
    {
        if($(window).height() < 500)
        {
            $("#gameInfo table").hide();
            $("#hidePGNinfo").hide();
            $("#fullScreen").show();
        }
        else
        {
            $("#gameInfo table").show();
            $("#hidePGNinfo").show();
            $("#fullScreen").hide();
        }
        setHeight();
    }

    function setHeight()
    {
        $("#demo-moves").height($(window).height() - $("#gameInfo").height()-20);
    }
</script>
<style type="text/css"/>
.ct-board-container{float:left;margin-right:5px;}.ct-board-container select{font-size:1.1em;width:auto;}.ct-board{display:block;overflow:hidden;text-align:center;}.ct-board-border{border:1px solid #363A3D;padding:2px;}.ct-board-border-green{background-color:#408038;color:#CCC;border:2px solid #363A3D;}.ct-board-border-greenwhite{background-color:#112724;color:#CCC;border:2px solid #363A3D;}.ct-board-border-stucco{background-color:#777;color:#333;border:2px solid #363A3D;background:url('/images/tiles/Worn Stucco.jpg') repeat;}.ct-board-border-wooddark{background-color:#777;color:#CCC;border:2px solid #363A3D;background:url('/images/tiles/WoodFine0011_preview.jpg') repeat;}.ct-black-square,.ct-white-square,.ct-black-square-grey,.ct-white-square-grey,.ct-black-square-lightgrey,.ct-white-square-lightgrey,.ct-black-square-brown,.ct-white-square-brown,.ct-black-square-greenwhite,.ct-white-square-greenwhite,.ct-black-square-green,.ct-white-square-green,.ct-black-square-woodlight,.ct-white-square-woodlight,.ct-black-square-marblebrown,.ct-white-square-marblebrown,.ct-black-square-metal,.ct-white-square-metal,.ct-black-square-stucco,.ct-white-square-stucco,.ct-black-square-goldsilver,.ct-white-square-goldsilver,.ct-black-square-wooddark,.ct-white-square-wooddark,.ct-black-square-sandsnow,.ct-white-square-sandsnow,.ct-black-square-crackedstone,.ct-white-square-crackedstone,.ct-black-square-granite,.ct-white-square-granite,.ct-black-square-marblegreen,.ct-white-square-marblegreen{display:block;float:left;}.ct-black-square,.ct-black-square-grey{background-color:#99ccff;}.ct-white-square,.ct-white-square-grey{background-color:#ffffff;}.ct-white-square-lightgrey{background-color:#CDCDCD;}.ct-black-square-lightgrey{background-color:#AAA;}.ct-white-square-brown{background-color:#FCCC9C;}.ct-black-square-brown{background-color:#CC9C6C;}.ct-white-square-woodlight{background-color:#FCCC9C;background:url('/images/tiles/WoodFine0009_preview.jpg') repeat;}.ct-black-square-woodlight{background-color:#CC9C6C;background:url('/images/tiles/WoodFine0015_preview.jpg') repeat;}.ct-white-square-green{background-color:#C8C060;color:red;}.ct-black-square-green{background-color:#70A068;color:red;}.ct-white-square-greenwhite{background-color:#FFFFF0;color:red;}.ct-black-square-greenwhite{background-color:#2C794F;color:red;}.ct-black-square-marblebrown{background-color:#CC9C6C;background:url('/images/tiles/Egyptian Marble.jpg') repeat;}.ct-white-square-metal{background-color:#FCCC9C;background:url('/images/tiles/Aluminum Light.jpg') repeat;}.ct-black-square-metal{background-color:#CC9C6C;background:url('/images/tiles/Aluminum Dark.jpg') repeat;}.ct-white-square-stucco{background-color:#FCCC9C;background:url('/images/tiles/Tan Stucco.jpg') repeat;}.ct-black-square-stucco{background-color:#CC9C6C;background:url('/images/tiles/Santa Fe Stucco.jpg') repeat;}.ct-white-square-goldsilver{background-color:#FCCC9C;background:url('/images/tiles/Mothership.jpg') repeat;}.ct-black-square-goldsilver{background-color:#CC9C6C;background:url('/images/tiles/Raw Gold.jpg') repeat;}.ct-white-square-wooddark{background-color:#FCCC9C;background:url('/images/tiles/WoodFine0010_preview.jpg') repeat;}.ct-black-square-wooddark{background-color:#CC9C6C;background:url('/images/tiles/WoodFine0003_preview.jpg') repeat;}.ct-white-square-sandsnow{background-color:#FCCC9C;background:url('/images/tiles/Snow.jpg') repeat;}.ct-black-square-sandsnow{background-color:#CC9C6C;background:url('/images/tiles/Slush.jpg') repeat;}.ct-white-square-crackedstone{background-color:#FCCC9C;background:url('/images/tiles/Cracked Pomegranate.jpg') repeat;}.ct-black-square-crackedstone{background-color:#CC9C6C;background:url('/images/tiles/Lightning Rock.jpg') repeat;}.ct-white-square-granite{background-color:#FCCC9C;background:url('/images/tiles/White Beach Granite.jpg') repeat;}.ct-black-square-granite{background-color:#CC9C6C;background:url('/images/tiles/Brown Pearl Granite.jpg') repeat;}.ct-black-square-marblegreen{background-color:#CC9C6C;background:url('/images/tiles/Sea Green Marble.jpg') repeat;}.ct-from-square{background:none;background-color:#9F9FFF;}.ct-to-square{background:none;background-color:#557FFF;}.ct-over-valid-square{background:none;background-color:green;}.ct-over-invalid-square{background:none;background-color:red;}.ct-nav-buttons{padding-bottom:7px;padding-top:7px;}.ct-back,.ct-forward,.ct-start,.ct-end,.ct-play,.ct-stop{vertical-align:middle;padding-right:20px;}.ct-mainline-commentary{padding:3px;}.ct-board-move-mainline{cursor:pointer;font-size :10pt;color:#000;}.ct-board-move-variation{cursor:pointer;color:#000;}.ct-board-move-mainline{font-weight:700;}.ct-board-move-comment{color:black;}.ct-board-move-current{color:red;}.ct-bad-move-score{color:#FF2020;}.ct-board-border-lightgrey,.ct-board-border-goldsilver{background-color:#777;border:2px solid #363A3D;color:#CCC;}.ct-board-border-grey,.ct-board-border-metal,.ct-board-border-sandsnow,.ct-board-border-crackedstone,.ct-board-border-granite{background-color:#555;border:2px solid #363A3D;color:#CCC;}.ct-board-border-brown,.ct-board-border-wood,.ct-board-border-marblebrown,.ct-board-border-marblegreen{background-color:#9C6C3C;border:2px solid #363A3D;color:#CCC;}.ct-white-square-marblebrown,.ct-white-square-marblegreen{background:url('/images/tiles/Light Swirl Marble.jpg') repeat;background-color:#FCCC9C;}.ct-subopt-move-score,.ct-opt-move-score{color:#8AAFEF;}
#demo-boardBorder{border-box: initial; -webkit-box-sizing:initial; box-sizing: content-box}
#containerBoard{position: fixed; top: 10px;}
#gameText{margin-left: 315px}
.players{font-size: 18px; font-weight: bold;padding-left: 10px;}
</style>

<body class="container">
<div class="row">
<div id="containerBoard">
<div id="demo-container" style="box-sizing: none"></div>
                                                    <div style="clear: both;">Game viewer by <a href="http://chesstempo.com/pgn-viewer.html" target="_blank">ChessTempo</a></div>
                                                                                                                                                                             </div>
                                                                                                                                                                               <div id="gameText">
<div id="gameInfo">
<a href="toolPGN.php?gameId=<?php echo $_GET["gameId"];?>" target="_blank" id="fullScreen" class="glyphicon glyphicon-fullscreen pull-right" style="cursor: pointer;margin: 5px" title="Volledig scherm"></a>
                                                                                                                                                                                                           <span id="hidePGNinfo" class="glyphicon glyphicon-resize-full pull-right" style="cursor: pointer;margin: 5px" title="Commentaar groot weergeven"></span>
                                                                                                                                                                                                                                                                                                                                                              <div class="players"><?php echo $pgnInfo["White"].($pgnInfo["WhiteElo"] ? " (".$pgnInfo["WhiteElo"].")" : "")." - ".$pgnInfo["Black"].($pgnInfo["BlackElo"] ? " (".$pgnInfo["BlackElo"].")" : "");?></div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <table class="table-striped table">
<?php
    $displayInfo = array("Event","Site","Date","Round","Result","ECO","Annotator");
    foreach($displayInfo as $item)
    {
        if($pgnInfo[$item] <> "" && $pgnInfo[$item] <> "?")
            echo "<tr><td>".$item."</td><td>".$pgnInfo[$item]."</td></tr>";
    }
?>
</table>
  </div>
    <div id="demo-moves" style="overflow: auto;">


</div>
  </div>
    </div>

      <div id="text">
<?
    if($title != "")
    {
        echo "<h1>".$title."</h1>";
    }

?>



</div>
  </body>
    </html>

