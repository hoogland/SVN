<?php
    include("../database.inc");
    include("../../archief/class_competitie.php");

    $competitie = new competitie();
    $competitie->eindscore(19);



    function nog_te_spelen($result)
    {
        $deelnemers = $this->competitie->spelers_deelname(19);
        $plaats = $deelnemers[$speler]["plaats"];
        $aantal_spelers = count($deelnemers);
        $speler_gegevens = $deelnemers[$speler];
        unset($deelnemers[$speler]); 
        if(count($deelnemers) > 0)
        {
            echo "<h3>Nog te spelen partijen</h3>";
            echo "<table>";
            foreach($deelnemers as $key => $tegenstander)
            {
                $opzet = array(array(array($speler,$plaats), array($key,$deelnemers[$key]["plaats"])),$competitie_gegevens["aantal_deelnemers"]);

                $kleuren = $competitie->kleur(array(array($speler,$plaats), array($key,$deelnemers[$key]["plaats"])),$competitie_gegevens["aantal_deelnemers"]);
                if($kleuren["wit"] == $speler)               //WIT PRINTEN
                    echo "<TR><TD>".$partij["datum"]."<TD>".$speler_gegevens["naam"]."<TD><a href=\"index.php?action=speler&competitie=".$_GET['competitie']."&id=".$key."\">".$deelnemers[$key]["naam"]."</a>";
                else                            //ZWART PRINTEN 
                    echo "<TR><TD>".$partij["datum"]."<TD><a href=\"index.php?action=speler&competitie=".$_GET['competitie']."&id=".$key."\">".$deelnemers[$key]["naam"]."</a><TD>".$speler_gegevens["naam"];    


            }
            echo "</TABLE>";
        }

    }
?>
