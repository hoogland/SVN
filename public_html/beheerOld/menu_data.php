<?php

    //LEDENADMINSTRATIE
    $menu[0] = array('Ledenadministratie','leden/main.php');            //OVERZICHT LEDEN
        $menu_sub[0][] = array('Overzicht', 'leden/main.php');                      //OVERZICHT LEDEN    
        $menu_sub[0][] = array('Nieuw lid', 'leden/nieuw.php');                     //NIEUW LID AANMAKEN 
        $menu_sub[0][] = array('Massamail','leden/mail.php');                       //MASSA MAIL -> leden 
        $menu_sub[0][] = array('KNSB lijst','leden/knsb_start.php');                //KNSB ledenlijst + rating verwerken 
      
    //SEIZOENEN BEHEER
    $menu[1] = array('Seizoenen','seizoenen/main.php');                 //OVERZICHT SEIZOENEN
        $menu_sub[1][] = array('Nieuw seizoen','seizoenen/nieuw.php');              //NIEUW SEIZOEN AANMAKEN
        $menu_sub[1][] = array('Teams','seizoenen/teams.php');                      //DE TEAMS + SPELERS VAN HET SEIZOEN AANMAKEN
        
    //TOERNOOIEN
    $menu[2] = array('Toernooien','toernooien/main.php');               //OVERZICHT TOERNOOIEN
        $menu_sub[2][] = array('Deelnemersoverzicht','toernooien/deelnemers.php');  //OVERZICHT DEELNEMERS TOERNOOI
        $menu_sub[2][] = array('Groepen','toernooien/groepen.php');                 //NIEUWE GROEPEN IN TOERNOOI AANMAKEN
        $menu_sub[2][] = array('Nieuwe deelnemer','toernooien/deelnemer_nieuw.php');//NIEUWE DEELNEMERS AANMELDEN 
        $menu_sub[2][] = array('Uitslagen','toernooien/uitslagen.php');             //UITSLAGEN / PARTIJEN VAN TOERNOOI INVOEREN
        
    //COMPETITIES
    $menu[3] = array('Competities','competitie/main.php');               //OVERZICHT COMPETITIES IN SEIZOEN
        $menu_sub[3][] = array('Overzicht','competitie/main.php');                  //OVERZICHT COMPETITIES IN SEIZOEN
        $menu_sub[3][] = array('Nieuw','competitie/nieuw.php');                     //NIEUWE COMPETITIE AANMAKEN
        $menu_sub[3][] = array('|','competitie/main.php');                          //SCHEIDING
        $menu_sub[3][] = array('Deelnemers','competitie/deelnemers.php');           //DEELNEMERS AAN EEN COMPETITIE        
        $menu_sub[3][] = array('Indeling','competitie/indeling.php');               //AANMAKEN VAN INDELINGEN + PRINTEN TOTAALOVERZICHT SPEELDAG
        $menu_sub[3][] = array('Uitslagen','competitie/uitslagen.php');             //INVOEREN VAN UITSLAGEN (HANDMATIG + RATING BESTAND)
        $menu_sub[3][] = array('Standen','competitie/index.php');                   //OVERZICHT VAN STANDEN BETREFFENDE COMPETITIE
        $menu_sub[3][] = array('Rating rapportage','competitie/rating_rapport.php');//AANMAKEN / VERZENDEN VAN RATING RAPPORTAGES COMPETITIE
        
    //TOOLS
    $menu[4] = array('Tools','tools/game_viewer.php');                  //TOOLS OVERZICHT
        $menu_sub[4][] = array('Game Viewer','tools/game_viewer.php');              //GAME VIEWER MAKER
        $menu_sub[4][] = array('FEN Viewer','tools/fen_viewer.php');                //FEN VIEWER MAKER
        
    //HELP
    $menu[5] = array('Help','help/main.php');                           //ALGEMENE HELP PAGINA
        $menu_sub[5][] = array('Algemeen','help/main.php');                         //OVERZICHT COMPETITIES IN SEIZOEN
        $menu_sub[5][] = array('Ledenadministratie','help/ledenadministratie.php'); //OVERZICHT COMPETITIES IN SEIZOEN
        $menu_sub[5][] = array('Competitie','help/competitie.php');                 //OVERZICHT COMPETITIES IN SEIZOEN
    
      
?>
