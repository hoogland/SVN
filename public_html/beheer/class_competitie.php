<?php
    class competitie{

        var $uitslagen = array();
        function site($competitie)
        {  ?>
        <div id="menu_comp">
            <ul>
                <li><a href="index.php?competitie=<? echo $_GET['competitie'];?>&action=spelers">Spelerslijst</a> 
                <li><a href="index.php?competitie=<? echo $_GET['competitie'];?>">Rangschikking</a> 
                <li><a href="index.php?competitie=<? echo $_GET['competitie'];?>&action=xref">Xref</a> 
                <li><a href="index.php?competitie=<? echo $_GET['competitie'];?>&action=statistieken">Statistieken</a> 
            </ul>  
            Standen + Uitslagen
            <ul>

                <?
                    $sql = "SELECT MAX(ronde) FROM svn_partijen WHERE comp_id = ".$_GET['competitie'];
                    $result = mysql_query($sql);
                    $row = mysql_fetch_array($result);

                    for($a = $row[0]; $a > 0; $a--)
                    {
                        echo "<li><a href=\"index.php?competitie=".$_GET['competitie']."&ronde=".$a."\">Ronde ".$a."</a>";
                    }

                ?>  

            </ul>
        </div>
        <div id="ranglijst">
            <?
                if($_GET['action'] == "xref")
                    $this->xref($_GET['competitie']);
                elseif($_GET['action'] == "statistieken")
                    $this->statistieken($_GET['competitie']);
                elseif($_GET['action'] == "spelers")
                {
                    $spelers = $this->spelers($_GET['competitie']);
                    echo "<TABLE>";
                    echo "<TR class=\"eerste_rij\"><TD>KNSB<TD>Naam";
                    foreach($spelers as $key => $speler)
                        echo "<TR><TD><a href=\"index.php?action=speler&competitie=".$_GET['competitie']."&id=".$key."\">".$speler["knsb"]."</a><TD>".$speler["naam"];
                    echo "</TABLE>";
                }
                elseif($_GET['action'] == "speler")
                {
                    $result = $this->partijen((int)$_GET['competitie'],(int)$_GET['ronde']);
                    $this->partijen_speler_print($result["spelers"],$result["partijen"],$_GET['id'], $competitie);  
                }
                else
                {
                    //STAND PRINTEN
                    if($_GET['ronde'] == "max")
                    {
                        $competitie = $this->competitie_gegevens((int)$_GET['competitie']);
                        $_GET['ronde'] = $competitie["laatste_ronde"];
                    }

                    $result = $this->partijen((int)$_GET['competitie'],(int)$_GET['ronde']);
                    echo "<TABLE>";
                    $this->stand_print($result["spelers"],$result["stand"],array('naam','PUNTEN','partijen','winst','remise','verlies','SB','W-We','TPR'));
                    echo "</TABLE>";   
                    if(isset($_GET['ronde']))
                        $this->partijen_print($result["spelers"],$result["partijen"],$_GET['ronde']);
                }
            ?>
        </div>

        <?    }

        function stand($spelers, $partijen, $competitie = null)                  //GENEREREN STAND
        {
            //PUNTEN VERWERKEN
            foreach($spelers as $key => $speler)
            {
                $stand[$key]['speler'] = $key;

                foreach($partijen as $partij)
                {

                    if($partij["uitslag"] != "")
                    {
                        //WIT PARTIJ
                        if($partij[0] == $key)
                        {
                            $stand[$key]["PUNTEN"] = $stand[$key]["PUNTEN"] + $partij[2];
                            switch($partij[2])
                            {
                                case 1:   $stand[$key]["winst"]++;break;
                                case 0.5:   $stand[$key]["remise"]++;break;
                                case 0:   $stand[$key]["verlies"]++;break;
                            }
                        }

                        //ZWART PARTIJ
                        if($partij[1] == $key)
                        {
                            $stand[$key]["PUNTEN"] = $stand[$key]["PUNTEN"] + (1 - $partij[2]);
                            switch($partij[2])
                            {
                                case 1:   $stand[$key]["verlies"]++;break;
                                case 0.5:   $stand[$key]["remise"]++;break;
                                case 0:   $stand[$key]["winst"]++;break;
                            }
                        }
                    }
                }
            }

            //WP SB TPR VERWERKEN 
            foreach($spelers as $key => $speler)
            {
                foreach($partijen as $partij)
                {
                    //WIT PARTIJ
                    if($partij["uitslag"] != "")
                    {
                        if($partij[0] == $key)
                        {
                            $stand[$key]["WP"] = $stand[$key]["WP"] + $stand[$partij[1]]["PUNTEN"];
                            $stand[$key]["SB"] = $stand[$key]["SB"] + $stand[$partij[1]]["PUNTEN"] * $partij[2];
                            $We = 1 / (1+ pow(10,-(($partij["rating_wit"] - $partij["rating_zwart"]) / 400)));
                            $stand[$key]["We"] = $stand[$key]["We"] + $We;

                            //Rating tegenstanders
                            if(abs($partij["rating_zwart"] - $partij["rating_wit"]) > 350)
                            {
                                if($partij["rating_zwart"] < $partij["rating_wit"])
                                    $stand[$key]["Rat_opp"][] = $partij["rating_wit"] - 350;
                                else
                                    $stand[$key]["Rat_opp"][] = $partij["rating_wit"] + 350;
                            }
                            else       
                                $stand[$key]["Rat_opp"][] = $partij["rating_zwart"];
                            $stand[$key]["partijen"]++;
                            if(!isset($speler["rating"]))
                                $speler["rating"] = $partij["rating_wit"];

                        }

                        //ZWART PARTIJ
                        if($partij[1] == $key)
                        {
                            $stand[$key]["WP"] = $stand[$key]["WP"] + $stand[$partij[0]]["PUNTEN"];
                            $stand[$key]["SB"] = $stand[$key]["SB"] + $stand[$partij[0]]["PUNTEN"] * (1 - $partij[2]);
                            $We = 1 / (1+ pow(10,-(($partij["rating_zwart"] - $partij["rating_wit"]) / 400)));
                            $stand[$key]["We"] = $stand[$key]["We"] + $We;

                            //Rating tegenstanders
                            if(abs($partij["rating_wit"] - $partij["rating_zwart"]) > 350)
                            {
                                if($partij["rating_wit"] < $partij["rating_zwart"])
                                    $stand[$key]["Rat_opp"][] = $partij["rating_zwart"] - 350;
                                else
                                    $stand[$key]["Rat_opp"][] = $partij["rating_zwart"] + 350;
                            }
                            else       
                                $stand[$key]["Rat_opp"][] = $partij["rating_wit"];

                            $stand[$key]["partijen"]++;
                            if(!isset($speler["rating"]))
                                $speler["rating"] = $partij["rating_zwart"];
                        }  
                    }   
                }
                $totaal_opp = 0;
                foreach($stand[$key]["Rat_opp"] as $rat_opp)
                    $totaal_opp = $totaal_opp + $rat_opp;

                $stand[$key]["Avg_opp"] = round($totaal_opp / $stand[$key]["partijen"]);;
                $stand[$key]["We"]  =  round($stand[$key]["We"],2);
                $stand[$key]["W-We"]  =  $stand[$key]["PUNTEN"] - $stand[$key]["We"];

                //TPR BEREKENEN
                $percentage = $stand[$key]["PUNTEN"] / $stand[$key]["partijen"];
                if($percentage == 1)
                    $percentage = 0.9999;
                if($percentage == 0)
                    $percentage = 0.0001;
                if($stand[$key]["PUNTEN"] == 0 || $stand[$key]["PUNTEN"] == $stand[$key]["partijen"])
                {
                    $speler = $this->speler_gegevens($key);
                    $stand[$key]["Avg_opp"] = ($stand[$key]["Avg_opp"] * $stand[$key]["partijen"] + $speler["rating"]) / ($stand[$key]["partijen"] + 1);
                    //  echo print_r($spelers[$key]); 
                    // $stand[$key]["partijen"]++;
                    //  $stand[$key]["PUNTEN"] = $stand[$key]["PUNTEN"] + 0.5;
                    $percentage = ($stand[$key]["PUNTEN"] + 0.5) / ($stand[$key]["partijen"] + 1);
                }
                $stand[$key]["TPR"] = round($stand[$key]["Avg_opp"] - 400 * log10(1 / $percentage - 1));
            }

            //SORTEREN
            //$stand = array_reverse($this->multisort($stand,array("PUNTEN","SB")));
            $competitie = $this->competitie_gegevens($competitie);
            //print_r($competitie);
            

            //BIJ GELIJKE STAND (PUNTEN & SB) ONDERLING RESULTAAT
            $onderling;  
            for($a = 0; $a < count($stand); $a++)
            {
                if($stand[$a]["PUNTEN"] == $stand[$a + 1]["PUNTEN"] && $stand[$a]["WP"] == $stand[$a + 1]["WP"] && $stand[$a]["SB"] == $stand[$a + 1]["SB"])
                {
                    $onderling[] = $stand[$a]["speler"];
                    $onderling[] = $stand[$a + 1]["speler"];                 
                }
                else
                {
                    if(count($onderling) > 1)
                    {
                        foreach($onderling as $speler_id)
                        {
                            foreach($partijen as $partij)
                            {
                                //WIT
                                if($partij[0] == $speler_id && in_array($partij[1],$onderling))
                                {
                                    $stand[$a]["ONDERLING"] = $stand[$a]["ONDERLING"] + $partij[2];
                                }

                                //ZWART
                                if($partij[1] == $speler_id && in_array($partij[0],$onderling))
                                    $stand[$a]["ONDERLING"] = $stand[$a]["ONDERLING"] + (1 - $partij[2]);
                            }
                        }
                        unset($onderling);
                    }
                }
            }
            $stand = array_reverse($this->multisort($stand,array("PUNTEN","SB", "ONDERLING")));
            $stand = array_reverse($this->multisort($stand,explode(",",$competitie[10])));
            return $stand;    
        }

        function stand_print($spelers,$stand,$print)         //PRINTEN STAND
        {
            echo "<TR class=\"eerste_rij\"><TD>";
            for($a = 0; $a < count($print); $a++)
            {
                echo "<TD>";
                switch($print[$a])
                {
                    case "knsb": echo "KNSB";break;
                    case "naam": echo "Naam";break;
                    case "winst": echo "W";break;
                    case "remise": echo "R";break;
                    case "verlies": echo "V";break;
                    case "partijen": echo "Prt";break;
                    case "PUNTEN": echo "Score";break;
                    case "SB": echo "SB";break;
                    case "WP": echo "WP";break;
                    case "We": echo "We";break;
                    case "W-We": echo "W-We";break;
                    case "TPR": echo "TPR";break;
                }
            }
            for($a = 0; $a < count($stand); $a++)
            {
                if($a % 2 == 1)
                    $class = "oneven";
                else
                    $class = "even";
                echo "\r\n<TR class=\"".$class."\"><TD align=\"right\">"; echo $a +1;
                for($b = 0; $b < count($print); $b++)
                {
                    $align = "right";
                    if($print[$b] == "naam")
                        $align = "left";
                    elseif($print[$b] == "PUNTEN" || $print[$b] == "WP")
                        $align = "center";
                    echo "<TD align=".$align.">";
                    if($print[$b] == "knsb")
                        echo $spelers[$stand[$a]['speler']][$print[$b]];
                    elseif($print[$b] == "naam")
                        echo "<a href=\"index.php?action=speler&competitie=".$_GET['competitie']."&id=".$stand[$a]['speler']."\">".$spelers[$stand[$a]['speler']][$print[$b]]."</a>";
                    elseif($print[$b] == "PUNTEN" || $print[$b] == "WP")
                        echo number_format($stand[$a][$print[$b]],1);
                    elseif($print[$b] == "SB" || $print[$b] == "W-We" || $print[$b] == "We")
                        echo number_format($stand[$a][$print[$b]],2);
                    else
                        echo $stand[$a][$print[$b]];
                }
            }    
        }

        function partijen_speler_print($spelers, $partijen, $speler, $competitie)
        {
            $deelnemers = $this->spelers_deelname($competitie);
            $speler_gegevens = $this->speler_gegevens($speler);
            echo "<h2>".$spelers[$speler]["naam"]." (".$speler_gegevens["rating"].")</h2>";
            echo "<TABLE>";
            foreach($partijen as $partij)
            {
                if($partij["speler_wit"] == $speler || $partij["speler_zwart"] == $speler)
                {
                    switch($partij[2])
                    {
                        case "0":   $result = "0-1";break;
                        case "0.5": $result = "&#189;-&#189;";break;
                        case "1":   $result = "1-0";break;
                        case "":  $result = "...";break;
                    }
                    if($partij["speler_zwart"] == $speler)
                    {
                        echo "<TR><TD>".$partij["datum"]."<TD><a href=\"index.php?action=speler&competitie=".$_GET['competitie']."&id=".$partij["speler_wit"]."\">".$spelers[$partij["speler_wit"]]["naam"]."</a><TD>".$spelers[$partij["speler_zwart"]]["naam"]."<TD align=\"center\">".$result;
                        unset($deelnemers[$partij["speler_wit"]]);
                    }
                    if($partij["speler_wit"] == $speler)
                    {
                        echo "<TR><TD>".$partij["datum"]."<TD>".$spelers[$partij["speler_wit"]]["naam"]."<TD><a href=\"index.php?action=speler&competitie=".$_GET['competitie']."&id=".$partij["speler_zwart"]."\">".$spelers[$partij["speler_zwart"]]["naam"]."</a><TD align=\"center\">".$result;
                        unset($deelnemers[$partij["speler_zwart"]]);
                    }
                    if($partij["reglementair"] == 1)
                        echo "<td>Reglementair";
                }
            }
            echo "</TABLE>";

            //NOG TE SPELEN PARTIJEN

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
                    $som = $plaats + $tegenstander['plaats'];
                    if($tegenstander['plaats'] < $plaats)
                        $som++;
                    if($plaats == $aantal_spelers && $aantal_spelers % 2 == 0)                                //LAATSTE SPELER SPECIAAL BIJ EVEN AANTAL
                    {
                        if($plaats == $aantal_spelers && $tegenstander['plaats'] > ($aantal_spelers / 2))
                            $som = 1;
                        else
                            $som = 2;
                    }
                    if($tegenstander['plaats'] == $aantal_spelers)                          //LAATSTE TEGENSTANDER AANPASSEN BIJ EVEN AANTAL = OMGEKERDE ALS HIERBOVEN
                    {
                        if($plaats > ($aantal_spelers / 2))
                            $som = 2;
                        else
                            $som = 1;
                    }

                    if($som % 2 == 1)               //WIT PRINTEN BIJ ONEVEN
                        echo "<TR><TD>".$partij["datum"]."<TD>".$speler_gegevens["naam"]."<TD><a href=\"index.php?action=speler&competitie=".$_GET['competitie']."&id=".$key."\">".$deelnemers[$key]["naam"]."</a>";
                    else                            //ZWART PRINTEN BIJ EVEN
                        echo "<TR><TD>".$partij["datum"]."<TD><a href=\"index.php?action=speler&competitie=".$_GET['competitie']."&id=".$key."\">".$deelnemers[$key]["naam"]."</a><TD>".$speler_gegevens["naam"];                                            
                }
            }
        }

        function partijen_print($spelers, $partijen, $ronde) //MEEGEGEVEN PARTIJEN WEERGEVEN
        {
            echo "<BR>Partijen ronde ".$ronde."<TABLE><TR class=\"eerste_rij\"><TD>Witspeler<TD>Zwartspeler<TD>Uitslag";
            $a = 0;
            foreach($partijen as $partij)
            {
                if($partij["ronde"] == $ronde)
                {
                    echo "<TR><TD>".$spelers[$partij["speler_wit"]]["naam"]."<TD>- ".$spelers[$partij["speler_zwart"]]["naam"]."<TD>";
                    switch($partij["uitslag"])
                    {
                        case 1: echo "1-0";             break;
                        case 2: echo "&#189;-&#189;";   break;
                        case 3: echo "0-1";             break;
                        case "": echo "...";            break;
                    }
                }
            }  
            echo "</table>";  
        }

        function fide_naar_partijen($input)                  //FIDE RAPPORTAGE NAAR PARIJEN OMZETTEN
        {
            $input = explode("\r\n",$input);
            //SPELERS INLEZEN
            for($a = 14; $a < count($input) - 3;$a++)
            {
                $spelers[trim(substr($input[$a],4,4))]['naam'] = substr($input[$a],14,34);  
                $spelers[trim(substr($input[$a],4,4))]['knsb'] = substr($input[$a],61,7);  
                $spelers[trim(substr($input[$a],4,4))]['rating'] = substr($input[$a],48,4);  
            }     

            //RONDES INLEZEN
            for($b = 91; $b < strlen($input[12]) - 7; $b = $b + 10)
                $rondes[] = trim(substr($input[12],$b,8));

            //PARTIJEN INLEZEN
            $partijen;
            for($a = 14; $a < count($input) -3;$a++)
            {
                $speler = trim(substr($input[$a],4,4));
                for($b = 91; $b < strlen($input[$a]) - 7; $b = $b + 10)
                {
                    $ronde = ($b - 91) / 10;
                    $tegenstander = trim(substr($input[$a],$b,4));
                    $kleur = substr($input[$a],$b + 5,1);
                    $uitslag = trim(substr($input[$a],$b + 7,1));
                    if($tegenstander != "" && $speler < $tegenstander)
                    {
                        if($uitslag == "=")
                            $uitslag = 0.5;
                        if($kleur == "w")
                            $partijen[] = array($speler,$tegenstander,$uitslag,'datum'=>$rondes[$ronde],'ronde'=>$ronde,'rating_wit'=>$spelers[$speler]['rating'],'rating_zwart'=>$spelers[$tegenstander]['rating']);
                        if($kleur == "b")
                            $partijen[] = array($tegenstander,$speler,(1 - $uitslag),'datum'=>$rondes[$ronde],'ronde'=>$ronde,'rating_wit'=>$spelers[$tegenstander]['rating'],'rating_zwart'=>$spelers[$speler]['rating']);
                    }
                }    
            }
            return array($spelers,$partijen);          
        }

        function multisort($array, $sort_by)                 //SORTEREN ARRAY
        {
            foreach ($array as $key => $value) {
                $evalstring = '';
                foreach ($sort_by as $sort_field) {
                    $tmp[$sort_field][$key] = $value[$sort_field];
                    $evalstring .= '$tmp[\'' . $sort_field . '\'], ';
                }
            }
            $evalstring .= '$array';
            $evalstring = 'array_multisort(' . $evalstring . ');';
            eval($evalstring);

            return $array;
        }     

        function partijen($competitie, $ronde, $minronde)               //PARTIJEN VAN EEN COMPETITIE BINNENHALEN
        {
            //PARTIJEN BINNENHALEN
            $sql = "SELECT speler_wit, speler_zwart, uitslag, rating_wit, rating_zwart, ronde, id, reglementair, datum FROM svn_partijen WHERE comp_id = ".$competitie."";
            if($ronde != "")
                $sql .= " AND ronde <= ".$ronde;
            if($minronde != "")
                $sql .= " AND ronde >= ".$minronde;

            $result = mysql_query($sql);
            for($a = 0; $a < mysql_num_rows($result); $a++)
            {
                $partijen[] = mysql_fetch_array($result);
                //RESULTATEN BEWERKEN
                switch($partijen[$a][2])
                {
                    case 2: $partijen[$a][2] = 0.5;break;
                    case 3: $partijen[$a][2] = 0;break;
                    case 4: $partijen[$a][2] = 1;break;
                    case 5: $partijen[$a][2] = 0;break;
                }
            }

            //SPELERS BINNENHALEN
            $sql = "SELECT DISTINCT svn_leden.* FROM svn_leden, svn_partijen WHERE (svn_leden.id = speler_wit OR svn_leden.id = speler_zwart) AND comp_id = ".$competitie."";
            $result = mysql_query($sql);
            for($a = 0; $a < mysql_num_rows($result); $a++)
            {
                $row = mysql_fetch_array($result);
                $spelers[$row['id']] = array('naam'=> $row['achternaam'].", ".$row['voorletters']." ".$row['tussenvoegsel'],'knsb'=> $row['knsb']);
            }

            return array("stand"=>$this->stand($spelers,$partijen, $competitie),"spelers"=>$spelers,"partijen"=>$partijen);  
        }

        function xref($competitie)                           //KRUISTABEL MAKEN
        {
            //BINNENHALEN GEGEVENS VAN COMPETITIE
            $result = $this->partijen($competitie,"");
            //PRINTEN XREF
            echo "<TABLE>";
            echo "<TR><TD><TD>Naam";
            for($a = 1; $a < count($result["stand"]) + 1; $a++)
                echo "<TD width=\"20px\">".$a;  
            echo "<TD>Punten<TD width=\"40px\">%<TD width=\"40px\">WP<TD width=\"40px\">SB";

            for($a = 1; $a <  count($result["stand"]) + 1; $a++)
            {
                //RIJ PER SPELER
                echo "<TR><TD>".$a.".<TD><a href=\"index.php?action=speler&competitie=".$_GET['competitie']."&id=".$result["stand"][$a - 1]["speler"]."\">".$result["spelers"][$result["stand"][$a - 1]["speler"]]["naam"]."</a>";
                //TEGENSTANDERS OP VOLGORDE VAN STAND
                for($b = 1 ;$b < count($result["stand"]) + 1;$b++)
                {
                    if($b == $a)
                        echo "<TD>x";
                    else
                    {
                        echo "<TD>";
                        foreach($result["partijen"] as $partij)
                        {
                            $uitslag = "";
                            if($partij["uitslag"] != "")
                            {
                                if($partij["speler_wit"] == $result["stand"][$a - 1]["speler"] && $partij["speler_zwart"] == $result["stand"][$b - 1]["speler"])          //SCORE BIJ WIT
                                    $uitslag = $partij["uitslag"];
                                if($partij["speler_wit"] == $result["stand"][$b - 1]["speler"] && $partij["speler_zwart"] == $result["stand"][$a - 1]["speler"])          //SCORE BIJ ZWART
                                    $uitslag = 4 - $partij["uitslag"];
                                if($uitslag == 0.5)
                                    $uitslag = "&#189;";
                                switch($uitslag)
                                {
                                    case 1:   echo "1";break;
                                    case 2:   echo "&#189;";break;
                                    case 3:   echo "0";break;
                                }
                                //echo $uitslag;
                            }
                        }
                    }
                }
                $percentage = round($result["stand"][$a-1]["PUNTEN"] / $result["stand"][$a-1]["partijen"] * 100);
                echo "<TD>".$result["stand"][$a-1]["PUNTEN"]."<TD>".$percentage."%<TD>".$result["stand"][$a-1]["WP"]."<TD>".$result["stand"][$a-1]["SB"]; 
            }
        }

        function spelers($competitie)
        {
            //SPELERS BINNENHALEN
            $sql = "SELECT DISTINCT svn_leden.* FROM svn_leden WHERE (id IN (SELECT speler_wit FROM svn_partijen WHERE comp_id = ".$competitie.")) OR (id IN (SELECT speler_zwart FROM svn_partijen WHERE comp_id = ".$competitie.")) OR (id IN (SELECT speler_id FROM svn_comp_deelname WHERE comp_id = ".$competitie."))";
            $result = mysql_query($sql);
            for($a = 0; $a < mysql_num_rows($result); $a++)
            {
                $row = mysql_fetch_array($result);
                $spelers[$row['id']] = array('naam'=> $row['achternaam'].", ".$row['voorletters']." ".$row['tussenvoegsel'],'knsb'=> $row['knsb']);
            }
            return $spelers;
        }

        function uitslagen($partijen)                         //GENEREREN MOGELIJKE UITKOMSTEN COMPETITIE
        {
            $array;
            for($b = 0; $b < count($partijen); $b++)
            {
                $array[$b] = 1 - $partijen[$b] / 2;

            }
            $this->uitslagen[] = $array;

            $ready = false;
            for($a = count($partijen) - 1; $a > -1 && $ready == false; $a--)
            {
                if($partijen[$a] != 2)
                {
                    $partijen[$a]++;
                    $ready = true;
                }
                else
                {
                    $partijen[$a] = 0;
                }
            }
            if(implode($partijen,'') != "22")
            {
                $this->uitslagen($partijen);

            }
            else
            {
                $array;
                for($b = 0; $b < count($partijen); $b++)
                {
                    $array[$b] = 1 - $partijen[$b] / 2;

                }
                $this->uitslagen[] = $array;

            }
            /*            for($a = count($partijen) - 1;$a > -1;$a--)
            {
            if($partijen[$a] != 3)
            {
            //verhogen score
            $partijen[$a]++;
            print_r($partijen);
            if($partijen[$a] == 3 && $a != 0)
            {
            $partijen[$a - 1]++;
            for($c = $a; $c < count($partijen); $c++)
            $partijen[$c] = 1;
            }
            $this->uitslagen($partijen);
            }
            }*/     
        }

        function statistieken($competitie)
        {
            $result = $this->partijen($competitie,"");

            foreach($result["partijen"] as $partij)
            {
                switch($partij["uitslag"])
                {
                    case 1: $wit++;break;
                    case 2: $remise++;break;
                    case 3: $zwart++;break;
                }
            }
            $wit = round($wit / count($result["partijen"]) * 100,1);
            $zwart = round($zwart / count($result["partijen"]) * 100,1);
            $remise = round($remise / count($result["partijen"]) * 100,1);

            $stand_we = array_reverse($this->multisort($result["stand"],array("W-We")));
            $stand_remise = array_reverse($this->multisort($result["stand"],array("remise")));
            $stand_winst = array_reverse($this->multisort($result["stand"],array("winst")));

            //SPELERS MET MEESTER REMISES & WINSTEN
            foreach($result["stand"] as $resultaat)
            {
                if($resultaat["remise"] == $stand_remise[0]["remise"])
                    $spelers_remise[] = $result["spelers"][$resultaat["speler"]]["naam"];
                if($resultaat["winst"] == $stand_winst[0]["winst"])
                    $spelers_winst[] = $result["spelers"][$resultaat["speler"]]["naam"];
            }
            $spelers_remise = implode("; ",$spelers_remise);
            $spelers_winst = implode("; ",$spelers_winst);


            echo "<table>";
            echo "<TR><TD>Wit overwinningen<TD>".$wit."%";    
            echo "<TR><TD>Zwart overwinningen<TD>".$zwart."%";    
            echo "<TR><TD>Remises<TD>".$remise."%";
            echo "<TR><TD>Beste prestatie<TD>".$result["spelers"][$stand_we[0]["speler"]]["naam"]." (W-We: ".$stand_we[0]["W-We"].")";
            echo "<TR><TD>Meeste winstpartijen<TD>".$spelers_winst." (".$stand_winst[0]["winst"].")";
            echo "<TR><TD>Meeste remises<TD>".$spelers_remise." (".$stand_remise[0]["remise"].")";
            echo "</table>";

        }

        function speler($speler)
        {
            $speler_gegevens = $this->speler_gegevens($speler);
            //SPELERSLIJST MAKEN
            $sql = "SELECT * FROM svn_leden";
            $result = mysql_query($sql);
            for($a = 0; $a < mysql_num_rows($result); $a++)
            {
                $row = mysql_fetch_array($result);
                $spelers[$row['id']] = array('naam'=> $row['achternaam'].", ".$row['voorletters']." ".$row['tussenvoegsel'],'knsb'=> $row['knsb']);
            }

            //RATINGVOORTGANG
            $sql = "SELECT * FROM svn_rating WHERE speler_id = ".$speler." ORDER BY datum DESC";
            $result = mysql_query($sql);
            echo "<h2>Rating voortgang</h2>";
            echo "<img src=\"http://drl.tuxtown.net/knsbratinggrafiekgroot.php?id=".$speler_gegevens['knsb']."\"><BR>";
            echo "<TABLE>";
            for($a = 0; $a < mysql_num_rows($result); $a++)
            {
                $row = mysql_fetch_array($result);
                echo "<TR><TD>".$row['datum']."<TD>".$row["rating"];
            }
            echo "</TABLE>";



            //PARTIJEN BINNENHALEN
            $sql = "SELECT speler_wit, speler_zwart, uitslag, rating_wit, rating_zwart, ronde, datum FROM svn_partijen WHERE (speler_wit = ".$speler." OR speler_zwart = ".$speler.")";

            if(isset($_SESSION["competitie"]) && $_SESSION["competitie"] != "")
                $sql .= " AND comp_id = ".$_SESSION["competitie"];
            elseif(isset($_SESSION["seizoen"]) && $_SESSION["seizoen"] != "")
                $sql .= " AND comp_id IN (SELECT id FROM svn_competities WHERE seizoen_id = ".$_SESSION["seizoen"].")";
            if(isset($_SESSION['type']) && $_SESSION['type'] != "")
                $sql .= " AND comp_id IN (SELECT ID FROM svn_competities WHERE type_comp = ".$_SESSION["type"].")";
            if(isset($_SESSION['tempo']) && $_SESSION['tempo'] != "")
                $sql .= " AND comp_id IN (SELECT ID FROM svn_competities WHERE stand_tempo = ".$_SESSION["tempo"].")";
            $sql .= " ORDER BY datum ASC";
            $result = mysql_query($sql);
            for($a = 0; $a < mysql_num_rows($result); $a++)
            {
                $partijen[] = mysql_fetch_array($result);
                //RESULTATEN BEWERKEN
                switch($partijen[$a][2])
                {
                    case 2: $partijen[$a][2] = 0.5;break;
                    case 3: $partijen[$a][2] = 0;break;
                    case 4: $partijen[$a][2] = 1;break;
                    case 5: $partijen[$a][2] = 0;break;
                }
            }

            if(count($partijen) == 0)
            {echo "Geen partijen gevonden";exit();  }


            //WINST REMISE VERLIES BEREKENEN
            foreach($partijen as $partij)
            {
                //WIT GEGEVENS
                if($partij["speler_wit"] == $speler)
                {
                    $spelers[$partij["speler_zwart"]]["partijen"]++;
                    $partijen_wit++;
                    switch($partij[2])
                    {
                        case 0:   $verlies++;$verlies_wit++; $spelers[$partij["speler_zwart"]]["verlies"]++;break;
                        case 0.5: $remise++;$remise_wit++;$spelers[$partij["speler_zwart"]]["remise"]++;break;
                        case 1:   $winst++;$winst_wit++;$spelers[$partij["speler_zwart"]]["winst"]++;break;
                    }

                }
                //ZWART GEGEVENS
                else
                {
                    $spelers[$partij["speler_wit"]]["partijen"]++;
                    $partijen_zwart++;
                    switch($partij[2])
                    {
                        case 0:   $winst++;$winst_zwart++; $spelers[$partij["speler_wit"]]["winst"]++;break;
                        case 0.5: $remise++;$remise_zwart++;$spelers[$partij["speler_wit"]]["remise"]++;break;
                        case 1:   $verlies++;$verlies_zwart++;$spelers[$partij["speler_wit"]]["verlies"]++;break;
                    }

                }
            }
            echo "<h2>Scores</h2><TABLE><TR><TD><TD>Totaal<TD>Wit<TD>Zwart";
            echo "<TR><TD>Winst<TD>".$winst." (".round($winst / count($partijen) * 100)." %)<TD>".$winst_wit." (".round($winst_wit / $partijen_wit * 100)." %)<TD>".$winst_zwart." (".round($winst_zwart / $partijen_zwart * 100)." %)";
            echo "<TR><TD>Remise<TD>".$remise." (".round($remise / count($partijen) * 100)." %)<TD>".$remise_wit." (".round($remise_wit / $partijen_wit * 100)." %)<TD>".$remise_zwart." (".round($remise_zwart / $partijen_zwart * 100)." %)";
            echo "<TR><TD>Verlies<TD>".$verlies." (".round($verlies / count($partijen) * 100)." %)<TD>".$verlies_wit." (".round($verlies_wit / $partijen_wit * 100)." %)<TD>".$verlies_zwart." (".round($verlies_zwart / $partijen_zwart * 100)." %)";
            echo "</TABLE>";
            echo "<h2>Tegenstanders</h2><TABLE><TR><TD><TD>Partijen<TD>Winst<TD>Remise<TD>Verlies<TD>Score";
            foreach($spelers as $speler2)
            {
                if(isset($speler2["partijen"]))
                    echo "<TR><TD>".$speler2["naam"]."<TD>".$speler2["partijen"]."<TD>".$speler2["winst"]."<TD>".$speler2["remise"]."<TD>".$speler2["verlies"]."<TD>".round(($speler2["remise"] / 2 + $speler2["winst"]) / $speler2["partijen"] * 100)." %";
            }
        }

        function spelers_deelname($competitie)
        {
            //SPELERS BINNENHALEN
            $sql = "SELECT DISTINCT svn_leden.*, svn_comp_deelname.plaats FROM svn_leden, svn_comp_deelname WHERE comp_id = ".$competitie." AND speler_id = svn_leden.id ORDER BY svn_comp_deelname.plaats ASC";
            $result = mysql_query($sql);
            for($a = 0; $a < mysql_num_rows($result); $a++)
            {
                $row = mysql_fetch_array($result);
                $spelers[$row['id']] = $this->speler_gegevens($row['id']);
                $spelers[$row['id']]["plaatsnaam"] =  $spelers[$row['id']]["plaats"];
                $spelers[$row['id']]['plaats'] = $row['plaats'];
                // $spelers[$row['id']] = array('naam'=> $row['achternaam'].", ".$row['voorletters']." ".$row['tussenvoegsel'],'knsb'=> $row['knsb'], 'plaats' => $row['plaats']);
            }
            return $spelers;
        }

        function spelers_select()
        {
            $sql = "SELECT * FROM svn_leden ORDER BY achternaam ASC";
            $result = mysql_query($sql);
            for($a = 0; $a < mysql_num_rows($result); $a++)
            {
                $row = mysql_fetch_array($result);
                $selected = "";
                if($row['id'] == $_SESSION['speler'])
                    $selected = "SELECTED";
                echo "<OPTION value=\"".$row['id']."\" ".$selected.">".$row['achternaam'].", ".$row['voorletters']." ".$row['tussenvoegsels'];
            }   
        }

        function speler_gegevens($speler_id)
        {
            //SPELER GEGEVENS + KAMPIOENSCHAPPEN + LAATSTE RATING + TOTAAL RATING
            $sql = "SELECT * FROM svn_leden WHERE id = ".$speler_id;
            $row = mysql_fetch_array(mysql_query($sql));
            $speler = array('naam'=> $row['achternaam'].", ".$row['voornaam']." ".$row['tussenvoegsel'],'knsb'=> $row['knsb'], 'plaats' => $row['plaats']);

            //HUIDIGE RATING
            $sql = "SELECT rating FROM svn_rating WHERE speler_id = ".$speler_id." ORDER BY datum DESC, type ASC";
            $row = mysql_fetch_array(mysql_query($sql));
            $speler['rating'] = $row[0];
            if($speler['rating'] == "")
                $speler['rating'] = "0000";
            return $speler;
        }

        function competitie_gegevens($comp_id)
        {
            $sql = "SELECT * FROM svn_competities WHERE id = ".$comp_id;
            $row = mysql_fetch_array(mysql_query($sql));           $competitie = $row;
            //LAATSTE RONDE
            $sql = "SELECT MAX(ronde) FROM svn_partijen WHERE comp_id = ".$comp_id;

            $row = mysql_fetch_array(mysql_query($sql));
            $competitie['laatste_ronde'] = $row[0];
            $competitie['Sortering'] = $row[10];
            if($competitie['laatste_ronde'] == "")
                $competitie['laatste_ronde'] = 0;

            //SPEELDATA
            $sql = "SELECT DISTINCT datum, ronde FROM svn_partijen WHERE comp_id = ".$comp_id." ORDER BY datum ASC";
            $result = mysql_query($sql);
            for($a = 0; $a < mysql_num_rows($result); $a++)
            {
                $row = mysql_fetch_array($result);
                $competitie['speeldata'][] = array('datum' => str_replace("-",".",$row[0]), 'ronde' => $row[1]);
            }

            //EERSTE RONDE
            $datum = strtotime(str_replace(".","-",$competitie['speeldata'][0]["datum"]));
            $datum = date("d-m-Y", $datum);
            $competitie['eerste_ronde'] = $datum;
            //LAATSTE RONDE
            $count = count($competitie['speeldata']) - 1;
            $datum = strtotime(str_replace(".","-",$competitie['speeldata'][$count]["datum"]));
            $datum = date("d-m-Y", $datum);
            $competitie['laatste_ronde'] = $datum;


            return($competitie);
        }

        function rating_rapportage($competitie_id, $van, $tot)
        {
            //GEGEVENS BINNENHALEN
            $deelnemers = $this->spelers_deelname($competitie_id);
            $competitie = $this->competitie_gegevens($competitie_id);
            $competitie_stand = $this->partijen($competitie_id,$van, $tot);
           // print_r($competitie_stand);
            header("Pragma: public"); // required
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private",false); // required for certain browsers
            header("Content-Transfer-Encoding: binary");
            header("Content-Type: txt");
            header("Content-Disposition: attachment; filename=\"" . $competitie["naam_uitgebreid"] . ".txt\";" );

            //GEGEVENS PRINTEN
            echo "012 ".$competitie["naam_uitgebreid"]."\r\n";       // OMSCHRIJVING COMPETITIE
            echo "022 ".$competitie["plaats"]."\r\n";                // PLAATS
            echo "032 ".$competitie["land"]."\r\n";                  // LAND
            echo "042 ".$competitie["eerste_ronde"]."\r\n";          // DATUM EERSTE RONDE
            echo "052 ".$competitie["laatste_ronde"]."\r\n";         // DATUM LAATSTE RONDE
            echo "062 ".count($deelnemers)."\r\n";                   // AANTAL SPELERS
            echo "072 \r\n";                                     
            echo "082 \r\n";
            echo "092 \r\n";
            echo "102 ".$competitie["wedstrijdleider"]."\r\n";       // Naam en E-mail wedstrijdleider
            echo "112 \r\n";
            echo "122 ".$competitie["speeltempo"]."\r\n";            // SPEELTEMPO
            echo "132 ";                                     // DATA WANNEER GESPEELD

            //DATA PRINTEN
            for($a = 5; $a < 92; $a++)
                echo " ";
            for($a = $van; $a <= $tot; $a++)
                echo substr($competitie["speeldata"][$a - 1]["datum"],2)."  ";
            echo "\r\n\r\n";

            //SPELERSGEGEVENS
            foreach($deelnemers as $key => $speler)
            {
                $zonder_score = 0;
                echo "001";
                for($a = strlen($speler["plaats"]); $a < 5; $a++)
                    echo " ";                                                     //SPATIES VOOR SPEELNUMMER
                echo $speler["plaats"]."      ";                               //SPEELNUMMER PRINTEN
                echo substr($speler["naam"],0,32);                             //NAAM PRINTEN

                //SPATIES VOOR RATING PRINTEN
                for($a = strlen($speler["naam"]); $a < 34; $a++)
                    echo " ";
                printf("%4s",$speler["rating"]);
                echo "         ";

                printf("%7s",$speler["knsb"]);
                echo "           ";                             //KNSB RELATIENUMMER

                //GESCOORDE PUNTEN
                $score_geprint = false;
                foreach($competitie_stand['stand'] as $plaats => $score)
                {
                    if($score["speler"] == $key)
                    {
                        $score_geprint = true;
                        printf("%5s",sprintf("%01.1f", $score["PUNTEN"]));
                        printf("%5s",$plaats + 1);
                    }
                }
                if(!$score_geprint)
                {
                    $zonder_score++;
                    printf("%5s",sprintf("%01.1f",0));
                    printf("%5s",count($competitie_stand['stand']) + $zonder_score);                   
                }

                //PARTIJEN PRINTEN
                for($a = $van; $a <= $tot; $a++)
                {
                    $datum = $competitie["speeldata"][$a - 1];
                    $resultaat = "";

                    //PARTIJ WIT
                    $sql = "SELECT * FROM svn_partijen WHERE id NOT IN (SELECT id FROM svn_partijen WHERE reglementair = 1 OR excludeRatingReport = 1) AND datum = '".str_replace(".","-",$datum['datum'])."' AND speler_wit = ".$key." AND comp_id = ".$competitie_id;
                    $result = mysql_query($sql);  
  
                    if(mysql_num_rows($result) > 0)
                    {
                        $result = mysql_fetch_array($result);

                        $uitslag = " ";
                        switch($result["uitslag"])
                        {
                            case 1: $uitslag = 1;break;
                            case 2: $uitslag = "=";break;
                            case 3: $uitslag = 0;break;
                        }
                        $resultaat = $deelnemers[$result["speler_zwart"]]["plaats"]." w ".$uitslag;
                    }


                    //PARTIJ ZWART
                    $sql = "SELECT * FROM svn_partijen WHERE  id NOT IN (SELECT id FROM svn_partijen WHERE reglementair = 1 OR excludeRatingReport = 1) AND datum = '".str_replace(".","-",$datum['datum'])."' AND speler_zwart = ".$key." AND comp_id = ".$competitie_id;
                    $result = mysql_query($sql);   
 
                    if(mysql_num_rows($result) > 0)
                    {
                        $result = mysql_fetch_array($result);
                        $uitslag = " ";
                        switch($result["uitslag"])
                        {
                            case 3: $uitslag = 1;break;
                            case 2: $uitslag = "=";break;
                            case 1: $uitslag = 0;break;
                        }
                        $resultaat = $deelnemers[$result["speler_wit"]]["plaats"]." z ".$uitslag;
                    }
                    printf("%10s",$resultaat);
                }

                echo "\r\n";
            }

        }

        function kleur($spelers, $totaal_spelers)           //Spelers => (id,plaats)(id,plaats)
        {
            $speler_plaatsen = array($spelers[0][1] => $spelers[0][0],$spelers[1][1] => $spelers[1][0]);
            $spelers = array($spelers[0][1],$spelers[1][1]); 
            sort($spelers);
            $speler_wit = $spelers[0];
            $speler_zwart = $spelers[1];

            if(($spelers[0] + $spelers[1]) % 2 == 0)
            {
                $speler_wit = $spelers[1];
                $speler_zwart = $spelers[0];
            }    
            if(($totaal_spelers % 2 == 0 &&  $spelers[1] == $totaal_spelers))
            {
                $speler_wit = $spelers[0];
                $speler_zwart = $spelers[1];

                if($spelers[0] > $totaal_spelers / 2)
                {
                    $speler_wit = $spelers[1];
                    $speler_zwart = $spelers[0];
                }   
            }
            return(array("wit" => $speler_plaatsen[$speler_wit], "zwart" => $speler_plaatsen[$speler_zwart]));        
        }

    }
?>
