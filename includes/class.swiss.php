<?php
    class swiss //extends competition
    {

        /**
        * Function to retreive the players with their scores (Scores / WP / SB)
        * 
        * @param mixed $players
        * @param mixed $matches
        */
        public function getScores($players, $matches)
        {
            $stand;
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
    }
?>
