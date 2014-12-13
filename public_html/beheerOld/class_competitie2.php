<?php
  class competitie{
      
      var $uitslagen = array();
      function stand($spelers, $partijen)                  //GENEREREN STAND
      {
          //PUNTEN VERWERKEN
          foreach($spelers as $key => $speler)
          {
              $stand[$key]['speler'] = $key;

              foreach($partijen as $partij)
              {
                  
                  //WIT PARTIJ
                  if($partij[0] == $key)
                      $stand[$key]["PUNTEN"] = $stand[$key]["PUNTEN"] + $partij[2];
                  
                  //ZWART PARTIJ
                  if($partij[1] == $key)
                      $stand[$key]["PUNTEN"] = $stand[$key]["PUNTEN"] + (1 - $partij[2]);
              }
          }

          //WP SB TPR VERWERKEN 
          foreach($spelers as $key => $speler)
          {
              foreach($partijen as $partij)
              {
                  //WIT PARTIJ
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
              $stand[$key]["TPR"] = round($stand[$key]["Avg_opp"] - 400 * log10(1 / $percentage - 1));
          }
        
          //SORTEREN
          $stand = array_reverse($this->multisort($stand,array(0,2)));

          //BIJ GELIJKE STAND (PUNTEN & SB) ONDERLING RESULTAAT
          $onderling;
          for($a = 0; $a < count($stand); $a++)
          {
              if($stand[$a][0] == $stand[$a + 1][0] && $stand[$a][2] == $stand[$a + 1][2])
                $onderling[] = $a;
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
                                $stand[$a]["ONDERLING"] = $stand[$a][3] + $partij[2];
                              
                              //ZWART
                              if($partij[0] == $speler_id && in_array($partij[1],$onderling))
                                $stand[$a]["ONDERLING"] = $stand[$a][3] + (1 - $partij[2]);
                          }
                      }
                      unset($onderling);
                  }
              }
          }
          
          $stand = array_reverse($this->multisort($stand,array("PUNTEN","SB")));
          
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
                  case "PUNTEN": echo "punten";break;
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
                    if($print[$b] == "naam" || $print[$b] == "knsb")
                        echo $spelers[$stand[$a]['speler']][$print[$b]];
                    elseif($print[$b] == "PUNTEN" || $print[$b] == "WP")
                        echo number_format($stand[$a][$print[$b]],1);
                    elseif($print[$b] == "SB" || $print[$b] == "W-We" || $print[$b] == "We")
                        echo number_format($stand[$a][$print[$b]],2);
                    else
                        echo $stand[$a][$print[$b]];
                }
            }    
      }
      
      function partijen_print($spelers, $partijen, $ronde)
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
          $sql = "SELECT speler_wit, speler_zwart, uitslag, rating_wit, rating_zwart, ronde, id FROM svn_partijen WHERE comp_id = ".$competitie."";
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
          
          return array("stand"=>$this->stand($spelers,$partijen),"spelers"=>$spelers,"partijen"=>$partijen);  
      }
      
      function xref($competitie)
      {
          //BINNENHALEN GEGEVENS VAN COMPETITIE
          $result = $this->partijen($competitie);
          //PRINTEN XREF
          echo "<TABLE>";
          echo "<TR><TD><TD>Naam";
          for($a = 1; $a < count($result["stand"]) + 1; $a++)
            echo "<TD width=\"20px\">".$a;  
          echo "<TD>Punten<TD width=\"40px\">%<TD width=\"40px\">WP<TD width=\"40px\">SB";
          
          for($a = 1; $a <  count($result["stand"]) + 1; $a++)
          {
              //RIJ PER SPELER
              echo "<TR><TD>".$a.".<TD>".$result["spelers"][$result["stand"][$a - 1]["speler"]]["naam"];
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
                          if($partij[2] != "NULL")
                          {
                          $uitslag = "";
                          if($partij["speler_wit"] == $result["stand"][$a - 1]["speler"] && $partij["speler_zwart"] == $result["stand"][$b - 1]["speler"])
                            $uitslag =  $partij[2];
                          if($partij["speler_wit"] == $result["stand"][$b - 1]["speler"] && $partij["speler_zwart"] == $result["stand"][$a - 1]["speler"])
                            $uitslag = 1 - $partij[2];
                          if($uitslag == 0.5)
                            $uitslag = "&#189;";
                          echo $uitslag;
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
          $sql = "SELECT DISTINCT svn_leden.* FROM svn_leden, svn_partijen WHERE (svn_leden.id = speler_wit OR svn_leden.id = speler_zwart) AND comp_id = ".$competitie."";
          $result = mysql_query($sql);
          for($a = 0; $a < mysql_num_rows($result); $a++)
          {
              $row = mysql_fetch_array($result);
              $spelers[$row['id']] = array('naam'=> $row['achternaam'].", ".$row['voorletters']." ".$row['tussenvoegsel'],'knsb'=> $row['knsb']);
          }
          return $spelers;
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
          $speler = array('naam'=> $row['achternaam'].", ".$row['voorletters']." ".$row['tussenvoegsel'],'knsb'=> $row['knsb'], 'plaats' => $row['plaats']);
          
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
       
       function rating_rapportage($competitie_id, $van, $tot)
       {
           //GEGEVENS BINNENHALEN
           $deelnemers = $this->spelers_deelname($competitie_id);
           $competitie = $this->competitie_gegevens($competitie_id);
           $competitie_stand = $this->partijen($competitie_id,$competitie['speeldata'][count($competitie['speeldata'])-1]['ronde'],$competitie['speeldata'][0]['ronde']);
          
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
           foreach($competitie["speeldata"] as $datum)
            echo substr($datum["datum"],2)."  ";
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
               foreach($competitie["speeldata"] as $datum)
               {
                   $resultaat = "";
                   
                   //PARTIJ WIT
                   $sql = "SELECT * FROM svn_partijen WHERE datum = '".str_replace(".","-",$datum['datum'])."' AND speler_wit = ".$key;
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
                   $sql = "SELECT * FROM svn_partijen WHERE datum = '".str_replace(".","-",$datum['datum'])."' AND speler_zwart = ".$key;
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
     
  }
?>
