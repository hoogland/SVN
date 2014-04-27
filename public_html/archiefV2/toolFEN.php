<?php
    error_reporting(E_ALL);
    ini_set("display_errors", 1); 
    include_once('../../includes/class.settings.php');

    $fen = $_GET['fen'];
    $dest = @imagecreatetruecolor(332, 332)
    or die("Cannot Initialize new GD image stream");

    //LEEG BORD MAKEN    
    for($d = 0; $d < 8; $d++){
        for($e = 0; $e < 8; $e++){
            if (($d + $e) % 2 == 0) 
                imagecopy($dest,imagecreatefromgif(settings::archive."/fen/big/w.gif"),$d * 40 + 6,$e * 40 + 6,0,0,40,40);
            else
                imagecopy($dest,imagecreatefromgif(settings::archive."/fen/big/b.gif"),$d * 40 + 6,$e * 40 + 6,0,0,40,40);
        }
    }      

    $fen        = explode("/",$fen);     
    for($a = 0; $a < 8; $a++)
    {
        $c = 0;
        for ($b = 0; $b < strlen($fen[$a]); $b++){
            if (!is_numeric($fen[$a][$b]) && $fen[$a][$b] != "")
            {
                //FEN IS NIET numeriek
                if(ctype_upper($fen[$a][$b]))
                {
                    //FEN IS WIT
                    if (($a + $c) % 2 == 0) 
                        imagecopy($dest,imagecreatefromgif(settings::archive."/fen/big/w".strtolower($fen[$a][$b])."w.gif"),$c * 40 + 6,$a * 40 + 6,0,0,40,40);
                    else 
                        imagecopy($dest,imagecreatefromgif(settings::archive."/fen/big/w".strtolower($fen[$a][$b])."b.gif"),$c * 40 + 6,$a * 40 + 6,0,0,40,40);          
                }
                ELSE
                {
                    if (($a + $c) % 2 == 0) 
                        imagecopy($dest,imagecreatefromgif(settings::archive."/fen/big/b".$fen[$a][$b]."w.gif"),$c * 40 + 6,$a * 40 + 6,0,0,40,40);
                    else 
                        imagecopy($dest,imagecreatefromgif(settings::archive."/fen/big/b".$fen[$a][$b]."b.gif"),$c * 40 + 6,$a * 40 + 6,0,0,40,40);               
                }
                $c++;
            }
            if (is_numeric($fen[$a][$b]) && $fen[$a][$b] != ""){
                $c = $c + $fen[$a][$b];
            }
        }
    }


    header("Content-type: image/png");   
    imagepng($dest);
    imagedestroy($dest);
?>
