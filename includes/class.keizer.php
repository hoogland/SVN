<?php
    class keizer //extends competition
    {
        var $iteraties = 5;
        var $maxValue = 70;
        var $initialSorting = false;

        /**
        * Function to retreive the players with their scores (Scores / WP / SB)
        * 
        * @param mixed $players
        * @param mixed $matches
        */
        public function getScores($players, $matches)
        {
            $stand;
            $value = $this->maxValue;

            //Retreiving scores / AVG opponent / nrOpponents
            foreach($matches as $match)
            {
                if($match["uitslag"] != "")
                {
                    //Scores
                    $stand[$match["speler_wit"]]["Score"] += 1 - ($match["uitslag"] - 1) / 2;
                    $stand[$match["speler_zwart"]]["Score"] += ($match["uitslag"] - 1) / 2; 

                    //Win draw loss
                    switch($match["uitslag"])
                    {
                        case 1: $stand[$match["speler_wit"]]["Win"]++;$stand[$match["speler_zwart"]]["Loss"]++;break;
                        case 2: $stand[$match["speler_wit"]]["Draw"]++;$stand[$match["speler_zwart"]]["Draw"]++;break;
                        case 3: $stand[$match["speler_wit"]]["Loss"]++;$stand[$match["speler_zwart"]]["Win"]++;break;
                    }
                    
                    //Opponents
                    $stand[$match["speler_wit"]]["RatedOpponents"][] = $match["rating_zwart"];
                    $stand[$match["speler_zwart"]]["RatedOpponents"][] = $match["rating_wit"];
                    $stand[$match["speler_wit"]]["PlayerMatches"][] = array("score" => 1 - ($match["uitslag"] - 1) / 2, "ratingPlayer" => $match["rating_wit"], "ratingOpponent" => $match["rating_zwart"]);
                    $stand[$match["speler_zwart"]]["PlayerMatches"][] = array("score" => ($match["uitslag"] - 1) / 2, "ratingPlayer" => $match["rating_zwart"], "ratingOpponent" => $match["rating_wit"]);

                    //Nr Opponents
                    $stand[$match["speler_wit"]]["Matches"]++;
                    $stand[$match["speler_zwart"]]["Matches"]++; 
                    
                    //Percentage
                    $stand[$match["speler_wit"]]["Percentage"] = $stand[$match["speler_wit"]]["Score"] / $stand[$match["speler_wit"]]["Matches"];
                    $stand[$match["speler_zwart"]]["Percentage"] = $stand[$match["speler_zwart"]]["Score"] / $stand[$match["speler_zwart"]]["Matches"];
                }
            }

            //Initiele sortering
            if($this->initialSorting)   
                $standTmp = array_reverse($this->multisort($stand,array($this->initialSorting)));      

            foreach($players as $player)
            {
                $stand[$player->id]["value"] = $value;
                $value--;
            }


            //Calculate Keizer scores
            for($a = 0; $a < $this->iteraties; $a++)
            {
                foreach($stand as $id => $player)
                    $stand[$id]["KeizerTotaal"] = 0;

                foreach($matches as $match)
                {
                    if($match["uitslag"] != "")
                    {   //Keizer Score
                        $stand[$match["speler_wit"]]["KeizerTotaal"] += (1 - ($match["uitslag"] - 1) / 2) * $stand[$match["speler_zwart"]]["value"];
                        $stand[$match["speler_zwart"]]["KeizerTotaal"] += (($match["uitslag"] - 1) / 2) * $stand[$match["speler_wit"]]["value"];   
                    }                       
                }

                foreach($stand as $id => $player)
                {
                    if($stand[$id]["Matches"] > 0)
                        $stand[$id]["KeizerGemiddelde"] = $stand[$id]["KeizerTotaal"] / $stand[$id]["Matches"];
                    else
                        $stand[$id]["KeizerGemiddelde"] = 0;
                }


                //Set new player scores
                //Sorting the competition
                foreach($stand as $id => $player)
                    $stand[$id]["player"] = $id;         

                $standTmp = array_reverse($this->multisort($stand,array("KeizerGemiddelde")));
                $value = $this->maxValue;
                foreach($standTmp as $id => $player)
                {
                    $stand[$player["player"]]["value"] = $value;
                    $stand[$player["player"]]["KeizerGemiddelde"] = round($stand[$player["player"]]["KeizerGemiddelde"],2);
                    $value--;   
                }        
            }


            //Retreiving WP / SB   
            foreach($matches as $match)
            {
                if($match["uitslag"] != "")
                {
                    //WP
                    $stand[$match["speler_wit"]]["WP"] += $stand[$match["speler_zwart"]]["Score"];
                    $stand[$match["speler_zwart"]]["WP"] += $stand[$match["speler_wit"]]["Score"]; 

                    //SB
                    $stand[$match["speler_wit"]]["SB"] += $stand[$match["speler_zwart"]]["Score"] * (1 - ($match["uitslag"] - 1) / 2);
                    $stand[$match["speler_zwart"]]["SB"] += $stand[$match["speler_wit"]]["Score"] * (($match["uitslag"] - 1) / 2); 
                }
            }         
            return $stand;
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

    }
?>
