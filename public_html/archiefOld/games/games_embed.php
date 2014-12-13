<?
    //WEBSITE STARTUP
    include_once('../database.inc');
    require_once('../../../includes/class.settings.php');
    $settings = new settings();
    //include('../class_menu.php');
    //$menu = new menu();
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
        $pgn = str_replace("  "," ", $pgn);

    }

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
    <head>
        <title>SV Nieuwerkerk | Archief</title>

        <meta name="author" content="Rob Hoogland" />
        <meta name="copyright" content="&copy; 2010 svnieuwerkerk.nl" />
        <meta name="description" content="Welkom - archiefsite SV Nieuwerkerk" />
        <meta name="keywords" content="SV Nieuwerkerk, svnieuwerkerk, svn, schaken, archief" />
        <meta name="robots" content="index,nofollow" />

        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />

        <link rel="stylesheet" type="text/css" href="http://www.svnieuwerkerk.nl/archief/style.css" />
        <script type="text/javascript" src="http://chesstempo.com/js/pgnyui.js" /></script>   
        <script type="text/javascript" src="http://chesstempo.com/js/pgnviewer.js"></script>  
        <link type="text/css" rel="stylesheet" href="http://chesstempo.com/css/board-min.css" />  

        <script>  
            new PgnViewer(  
                { boardName: "demo",  
                    //pgnFile: 'afek_235.pgn',  
                    pgnString: '<? echo $pgn;?>',
                    boardImagePath: "http://www.chessvibes.com/boards",
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
        </script> 
        <style type="text/css"/>
        .ct-board-container{float:left;margin-right:5px;}.ct-board-container select{font-size:1.1em;width:auto;}.ct-board{display:block;overflow:hidden;text-align:center;}.ct-board-border{border:1px solid #363A3D;padding:2px;}.ct-board-border-green{background-color:#408038;color:#CCC;border:2px solid #363A3D;}.ct-board-border-greenwhite{background-color:#112724;color:#CCC;border:2px solid #363A3D;}.ct-board-border-stucco{background-color:#777;color:#333;border:2px solid #363A3D;background:url('/images/tiles/Worn Stucco.jpg') repeat;}.ct-board-border-wooddark{background-color:#777;color:#CCC;border:2px solid #363A3D;background:url('/images/tiles/WoodFine0011_preview.jpg') repeat;}.ct-black-square,.ct-white-square,.ct-black-square-grey,.ct-white-square-grey,.ct-black-square-lightgrey,.ct-white-square-lightgrey,.ct-black-square-brown,.ct-white-square-brown,.ct-black-square-greenwhite,.ct-white-square-greenwhite,.ct-black-square-green,.ct-white-square-green,.ct-black-square-woodlight,.ct-white-square-woodlight,.ct-black-square-marblebrown,.ct-white-square-marblebrown,.ct-black-square-metal,.ct-white-square-metal,.ct-black-square-stucco,.ct-white-square-stucco,.ct-black-square-goldsilver,.ct-white-square-goldsilver,.ct-black-square-wooddark,.ct-white-square-wooddark,.ct-black-square-sandsnow,.ct-white-square-sandsnow,.ct-black-square-crackedstone,.ct-white-square-crackedstone,.ct-black-square-granite,.ct-white-square-granite,.ct-black-square-marblegreen,.ct-white-square-marblegreen{display:block;float:left;}.ct-black-square,.ct-black-square-grey{background-color:#99ccff;}.ct-white-square,.ct-white-square-grey{background-color:#ffffff;}.ct-white-square-lightgrey{background-color:#CDCDCD;}.ct-black-square-lightgrey{background-color:#AAA;}.ct-white-square-brown{background-color:#FCCC9C;}.ct-black-square-brown{background-color:#CC9C6C;}.ct-white-square-woodlight{background-color:#FCCC9C;background:url('/images/tiles/WoodFine0009_preview.jpg') repeat;}.ct-black-square-woodlight{background-color:#CC9C6C;background:url('/images/tiles/WoodFine0015_preview.jpg') repeat;}.ct-white-square-green{background-color:#C8C060;color:red;}.ct-black-square-green{background-color:#70A068;color:red;}.ct-white-square-greenwhite{background-color:#FFFFF0;color:red;}.ct-black-square-greenwhite{background-color:#2C794F;color:red;}.ct-black-square-marblebrown{background-color:#CC9C6C;background:url('/images/tiles/Egyptian Marble.jpg') repeat;}.ct-white-square-metal{background-color:#FCCC9C;background:url('/images/tiles/Aluminum Light.jpg') repeat;}.ct-black-square-metal{background-color:#CC9C6C;background:url('/images/tiles/Aluminum Dark.jpg') repeat;}.ct-white-square-stucco{background-color:#FCCC9C;background:url('/images/tiles/Tan Stucco.jpg') repeat;}.ct-black-square-stucco{background-color:#CC9C6C;background:url('/images/tiles/Santa Fe Stucco.jpg') repeat;}.ct-white-square-goldsilver{background-color:#FCCC9C;background:url('/images/tiles/Mothership.jpg') repeat;}.ct-black-square-goldsilver{background-color:#CC9C6C;background:url('/images/tiles/Raw Gold.jpg') repeat;}.ct-white-square-wooddark{background-color:#FCCC9C;background:url('/images/tiles/WoodFine0010_preview.jpg') repeat;}.ct-black-square-wooddark{background-color:#CC9C6C;background:url('/images/tiles/WoodFine0003_preview.jpg') repeat;}.ct-white-square-sandsnow{background-color:#FCCC9C;background:url('/images/tiles/Snow.jpg') repeat;}.ct-black-square-sandsnow{background-color:#CC9C6C;background:url('/images/tiles/Slush.jpg') repeat;}.ct-white-square-crackedstone{background-color:#FCCC9C;background:url('/images/tiles/Cracked Pomegranate.jpg') repeat;}.ct-black-square-crackedstone{background-color:#CC9C6C;background:url('/images/tiles/Lightning Rock.jpg') repeat;}.ct-white-square-granite{background-color:#FCCC9C;background:url('/images/tiles/White Beach Granite.jpg') repeat;}.ct-black-square-granite{background-color:#CC9C6C;background:url('/images/tiles/Brown Pearl Granite.jpg') repeat;}.ct-black-square-marblegreen{background-color:#CC9C6C;background:url('/images/tiles/Sea Green Marble.jpg') repeat;}.ct-from-square{background:none;background-color:#9F9FFF;}.ct-to-square{background:none;background-color:#557FFF;}.ct-over-valid-square{background:none;background-color:green;}.ct-over-invalid-square{background:none;background-color:red;}.ct-nav-buttons{padding-bottom:7px;padding-top:7px;}.ct-back,.ct-forward,.ct-start,.ct-end,.ct-play,.ct-stop{vertical-align:middle;padding-right:20px;}.ct-mainline-commentary{padding:3px;}.ct-board-move-mainline{cursor:pointer;font-size :10pt;color:#000;}.ct-board-move-variation{cursor:pointer;color:#000;}.ct-board-move-mainline{font-weight:700;}.ct-board-move-comment{color:black;}.ct-board-move-current{color:red;}.ct-bad-move-score{color:#FF2020;}.ct-board-border-lightgrey,.ct-board-border-goldsilver{background-color:#777;border:2px solid #363A3D;color:#CCC;}.ct-board-border-grey,.ct-board-border-metal,.ct-board-border-sandsnow,.ct-board-border-crackedstone,.ct-board-border-granite{background-color:#555;border:2px solid #363A3D;color:#CCC;}.ct-board-border-brown,.ct-board-border-wood,.ct-board-border-marblebrown,.ct-board-border-marblegreen{background-color:#9C6C3C;border:2px solid #363A3D;color:#CCC;}.ct-white-square-marblebrown,.ct-white-square-marblegreen{background:url('/images/tiles/Light Swirl Marble.jpg') repeat;background-color:#FCCC9C;}.ct-subopt-move-score,.ct-opt-move-score{color:#8AAFEF;}
        </style>
    </head>

    <body>         

        <div id="text">
            <?
                if($title != "")
                {
                    echo "<h1>".$title."</h1>";
                }

            ?>
            <div id="demo-container"></div>  
            <div id="demo-moves" style="overflow: auto; height: 330px;"></div> 
            <div style="clear: both;">Game viewer by <a href="http://chesstempo.com/pgn-viewer.html" target="_blank">ChessTempo</a></div> 
        </div>    


    </body>
</html>

