<?php
    class competition
    {
        var $settings;
        var $prefix;
        var $errorClass;
        var $notificationClass;

        var $indeling;

        var $id;
        var $season;
        var $name;
        var $nameExtended;
        var $typeCompetion;
        var $tempo;
        var $tempoExtended;
        var $place;
        var $country;
        var $land;
        var $arbiter;
        var $sorting;
        var $options;


        var $players;
        var $matches;
        var $rounds;
        var $standings;

        /**
        * Construction of the competition class
        * 
        * @param mixed $settings
        * @param mixed $id
        * @return competition
        */
        public function __construct($settings = null, $id, $errorClass = null, $notificationClass = null)
        {
            // Additional check to set the settings when is called via api service.
            // Else is used in current /beheer
            if(is_null($settings))
            {
                include_once("class.settings.php");
                $this->settings = new settings();
            }
            else
                $this->settings = $settings;

            $this->prefix = $settings->prefix;
            $this->prefix = "svn_";
            $this->id = $id;   
            $this->errorClass = $errorclass;
            $this->notificationClass = $notificationClass;
        }

        /**
        * Function to collect the general data of the competition
        * 
        */
        public function getGeneralData()
        {
            if($this->id)
            {
                $sql = "SELECT * FROM ".$this->prefix."competities WHERE id = ".$this->id;
                $result = mysql_query($sql);
                $data = mysql_fetch_assoc($result);
                $this->season = $data["seizoen_id"];
                $this->name = $data["naam"];
                $this->nameExtended = $data["naam_uitgebreid"];
                $this->typeCompetion = $data["type_comp"];
                $this->tempo = $data["stand_tempo"];
                $this->tempoExtended = $data["speeltempo"];
                $this->place = $data["plaats"];
                $this->country = $data["land"];
                $this->arbiter = $data["wedstrijdleider"];
                $this->arbiterMail = $data["wedstrijdleider_email"];


                $this->getPlayers();

                //GET THE OPTIONS
                $sql = "SELECT `option`, `value` FROM ".$this->prefix."competitie_opties WHERE comp_id = ".$this->id;
                $result = mysql_query($sql);
                while($data = mysql_fetch_assoc($result))
                    $this->options[$data["option"]] = $data["value"];
                $this->sorting = explode(",",$this->options["Sorting"]);
                if($this->sorting[0] == "")
                    $this->sorting = array("Score", "SB", "TPR");
                if($this->options["DisplayData"] == "")
                    $this->options["DisplayData"] = "Ranking,Name,Score,SB,WP,Matches,Percentage,TPR";
                $this->getRounds();

                if($this->options["compSystem"] == "Keizer")
                {
                    require_once("class.keizer.php");
                    $this->indeling = new keizer();
                    $this->indeling->iteraties = $this->options["keizerIteraties"];
                    $this->indeling->initialSorting = $this->options["keizerInitialSorting"];
                    $this->indeling->maxValue = $this->options["keizerMaxValue"];
                }
                else
                {
                    $this->options ["compSystem"] = "Zwitsers / Round Robin";
                    require_once("class.swiss.php");
                    $this->indeling = new swiss();
                }

                //Return general data
                return array(
                    "seizoen_id" => $this->season,
                    "naam" => $this->name,
                    "naam_uitgebreid" => $this->nameExtended,
                    "type_comp" => $this->typeCompetion,
                    "stand_tempo" => $this->tempo,
                    "speeltempo" => $this->tempoExtended,
                    "plaats" => $this->place,
                    "land" => $this->country,
                    "wedstrijdleider" => $this->arbiter,
                    "wedstrijdleider_email" => $this->arbiterMail,
                    "displayData" => explode(",",$this->options["DisplayData"])

            );
            }
        }

        public function setData($name, $nameExtended, $typeCompetition, $tempo, $tempoExtended, $place, $country, $arbiter, $arbiterMail, $displayData, $sorting)
        {
            $sql = "UPDATE ".settings::prefix."competities SET";
            $sql .= " naam = '".$name."',";
            $sql .= " naam_uitgebreid = '".$nameExtended."',";
            $sql .= " type_comp = '".$typeCompetition."',";
            $sql .= " stand_tempo = '".$tempo."',";
            $sql .= " speeltempo = '".$tempoExtended."',";
            $sql .= " plaats = '".$place."',";
            $sql .= " land = '".$country."',";
            $sql .= " wedstrijdleider = '".$arbiter."',";
            $sql .= " wedstrijdleider_email = '".$arbiterMail."'";   
            $sql .= " WHERE id = ".$this->id;
            $query = mysql_query($sql);

            $sql = "REPLACE  INTO ".settings::prefix."competitie_opties (`comp_id`, `option`, `value`) VALUES ('".$this->id."','Sorting','".$sorting."')";
            $query = mysql_query($sql);

            $sql = "REPLACE  INTO ".settings::prefix."competitie_opties (`comp_id`, `option`, `value`) VALUES ('".$this->id."','DisplayData','".$displayData."')";
            $query = mysql_query($sql);

            $this->notificationClass->add_note("De competitiegegevens zijn gewijzigd.");

        }

        public function setOptions($option, $value)
        {
            $sql = "REPLACE  INTO ".settings::prefix."competitie_opties (`comp_id`, `option`, `value`) VALUES ('".$this->id."','".$option."','".$value."')";
            $query = mysql_query($sql);

        }

        /**
        * Function to collect the players
        * 
        */
        public function getPlayers()
        {
            $sql = "SELECT speler_id FROM ".settings::prefix."comp_deelname WHERE comp_id = ".$this->id." ORDER BY plaats";
            $result = mysql_query($sql);
            $players;

            while($data = mysql_fetch_assoc($result))
                $players[$data["speler_id"]] = new player($this->settings, $data["speler_id"]);
            return $players;
        }

        /**
        * Add a player to the competition
        * 
        * @param mixed $id
        */
        public function addPlayer($id)
        {
            //Get maximum placement current players
            $sql = "SELECT MAX(plaats) as plaats FROM ".settings::prefix."comp_deelname WHERE comp_id = ".$this->id."";
            $result = mysql_fetch_assoc(mysql_query($sql));

            $sql = "INSERT INTO svn_comp_deelname (comp_id, speler_id, plaats) VALUES (".$this->id.",".$id.",".($result["plaats"] == "" ? 1 : $result["plaats"] + 1).")";
            mysql_query($sql);
            $this->notificationClass->add_note("Speler toegevoegd aan de competitie.");
        }

        /**
        * Remove a player from the competition
        * 
        * @param mixed $id
        */
        public function removePlayer($id)
        {
            //Remove player
            $sql = "DELETE FROM ".settings::prefix."comp_deelname WHERE comp_id = ".$this->id." AND speler_id = ".$id;
            mysql_query($sql);    

            //Update order
            $sql = "SET @place = 1; UPDATE ".settings::prefix."comp_deelname SET plaats = @place := @place+1  WHERE comp_id = ".$this->id; 
            mysql_query($sql);
            $this->notificationClass->add_note("Speler verwijderd uit de competitie. Let op dat de partijen nog wel bestaan!");
        }

        /**
        * Update the sorting of the players
        * 
        * @param mixed $sorting
        */
        public function setPlayerSorting($sorting)
        {
            $a = 1;
            foreach(explode(",",$sorting) as $player)
            {
                $sql = "UPDATE ".settings::prefix."comp_deelname SET plaats = ".$a." WHERE comp_id = ".$this->id." AND speler_id = ".$player;
                mysql_query($sql);
                $a++;
            } 
            $this->notificationClass->add_note("De volgorde van de spelers is aangepast.");
        }

        /**
        * Function to collect all the rounds of a competition
        * 
        */
        public function getRounds()
        {
            if($this->id)
            {
                $sql = "SELECT DISTINCT ronde, datum FROM svn_partijen WHERE comp_id = ".$this->id." ORDER BY datum ASC, ronde ASC";
                $result = mysql_query($sql);

                $rounds;
                while($data = mysql_fetch_assoc($result)) {
                    $data["ronde"] = (int) $data["ronde"];
                    $rounds[] = $data;
                }
                $this->rounds = $rounds;
                return $rounds;
            }  
        }

        /**
        * Function to retreive the matches played in the competition
        * 
        * @param mixed $round (optional round selection)
        * @param mixed $player (optional player selection)
        */
        public function getMatches($round = null, $player = null)
        {
            $sql = "SELECT * FROM ".$this->prefix."partijen WHERE comp_id = ".$this->id;
            if(is_array($round))
                $sql .= " AND ronde >= ".$round[0]." AND ronde <= ".$round[1];
            elseif($round)
                $sql .= " AND ronde = ".$round;
            if($player)
                $sql .= " AND (speler_wit = ".$player." OR speler_zwart = ".$player.")";
            $sql .= " ORDER BY ronde ASC, datum ASC, id ASC";

            $result = mysql_query($sql);
            $matches;
            while($data = mysql_fetch_assoc($result))
                $matches[] = $data;
            $this->matches = $matches;
            return $matches;  
        } 

        /**
        * Generic function to retrieve the current standings of the games played
        * Makes use of the extended classes (Swiss etc)
        * 
        */
        public function getStanding($round = null)
        {
            $this->standings = $this->indeling->getScores($this->getPlayers(), $this->getMatches($round));
            $this->calculateTPR();
            $players = $this->getPlayers();

            foreach($this->standings as $id => $player)
            {     
                //Calculate Percentage   
                if(count($player["RatedOpponents"]) > 0)
                    $this->standings[$id]["Percentage"] = round($player["Score"] / count($player["RatedOpponents"]) * 100,0);  
                //Average rating opponents    
                if(count($player["RatedOpponents"]) > 0)
                    $this->standings[$id]["RtO"] = round(array_sum($player["RatedOpponents"])/ count($player["RatedOpponents"]));
                //Rating
                $this->standings[$id]["Rating"] = $players[$id]->rating;  
            }

            //Getting subgroups if applicable
            $this->getSubgroup();    

            //Sorting the competition
            foreach($this->standings as $id => $player)
                $this->standings[$id]["player"] = $id;         

            $this->standings = array_reverse($this->multisort($this->standings,$this->sorting));

//            Set position
            for($a = 1; $a < count($this->standings) + 1; $a++)
                $this->standings[$a - 1]['Ranking'] = $a;
            return $this->standings;
        }

        public function getSubgroup()
        {
            if(strpos($this->options["DisplayData"],"Subgroup"))
            {

                $sql = "SELECT speler_id, sub FROM ".$this->prefix."comp_deelname WHERE comp_id = ".$this->id;
                $result = mysql_query($sql);
                $players;
                for($a = 0; $a < mysql_num_rows($result); $a++)
                {
                    $data = mysql_fetch_assoc($result);
                    $players[$data["speler_id"]] = $data["sub"];
                }
                foreach($this->standings as $key => $data)
                {
                    $this->standings[$key]["Subgroup"] = $players[$key];
                }
            }
        }

        /**
        * Section to add stuff
        */

        public function addMatch($playerWhite, $playerBlack, $round, $date)
        {
            $sql = 'INSERT INTO '.$this->prefix.'partijen (speler_wit, rating_wit, speler_zwart, rating_zwart, comp_id, tempo, datum, ronde) VALUES ('.$playerWhite->id.','.$playerWhite->rating.','.$playerBlack->id.','.$playerBlack->rating.','.$this->id.','.$this->tempo.',"'.$date.'","'.$round.'" )';
            mysql_query($sql);

            $_GET["ronde"] = $round;
        }

        /**
        * Section to update stuff
        */

        public function setMatch($matchId, $score, $reglementair = 0, $ratingReport = 0)
        {
            if($reglementair == "")
                $reglementair = 0;
            if($ratingReport == "")
                $ratingReport = 0;
            $sql = 'UPDATE '.$this->prefix.'partijen SET uitslag = '.$score.', reglementair = '.$reglementair.', excludeRatingReport = '.$ratingReport.' WHERE id = '.$matchId;                                                                                
            mysql_query($sql);
        }


        /**
        * Section to delete stuff
        */

        public function deleteMatch($matchId)
        {
            $sql = 'DELETE FROM '.$this->prefix.'partijen WHERE id = '.$matchId;
            mysql_query($sql);
        }



        /**
        * Calculation
        */

        /**
        * Generic TPR calculation
        * Can make use of dufferent functions to calculate the TPR
        * 
        */
        public function calculateTPR()
        {
            switch($this->options["TPRmethod"])
            {
                case "offsetTPR": $this->offsetTPR();break;
                case "adjustmentTPR": $this->adjustmentTPR();break;
                case "hooglandTPR": $this->hooglandTPR();break;
                case "svnTPR": $this->svnTPR();break;
                default: $this->offsetTPR();
            }
        } 

        private function offsetTPR()
        {
            
            if($this->options["TPRdamped"])
            {
                $this->dampedTPR();
                return;
            }
            foreach($this->standings as $id => $player)
            {
                $score = $this->multiArraySum($this->filter($player["PlayerMatches"],"ratingOpponent",0,true),"score");
                if(count($player["RatedOpponents"]) > 0)
                    $percentage = $score / count($this->filter($player["PlayerMatches"],"ratingOpponent",0,true));
                else
                    $percentage = 0;
                if($percentage == 1)
                    $percentage = 0.999;
                if($percentage == 0)
                    $percentage = 0.001;
                if(count($player["RatedOpponents"]) > 0)
                    $this->standings[$id]["TPR"] = round(array_sum($player["RatedOpponents"])/ count($this->filter($player["PlayerMatches"],"ratingOpponent",0,true)) - 400 * log10(1 / $percentage - 1));
            }  
        } 

        private function dampedTPR()
        {
            foreach($this->standings as $id => $player)
            {
                $opponentAVT = array_sum($player["RatedOpponents"])/ count($player["RatedOpponents"]);
                $opponentsDamped = array_merge($player["RatedOpponents"], array(array_sum($player["RatedOpponents"])/ count($player["RatedOpponents"])));
                $this->standings[$id]["TPR"] =  round((array_sum($opponentsDamped) / count($opponentsDamped) - 400 * log10(1 / (($player["Score"] + 0.5) / count($opponentsDamped)) - 1)));
            }
        }

        private function adjustmentTPR()
        {
            foreach($this->standings as $id => $player)
            {
                $ratingPlayerTotal = 0;
                foreach($player["PlayerMatches"] as $match)
                {
                    $this->standings[$id]["ScoreExpected"] += $this->expectancyFormula($match["ratingPlayer"], $match["ratingOpponent"]);
                    $ratingPlayerTotal += $match["ratingPlayer"];
                }
                $this->standings[$id]["TPR"] = round($ratingPlayerTotal / $player["Matches"] + 800 * ($player["Score"] - $this->standings[$id]["ScoreExpected"]) / $player["Matches"]); 

                //DAMPED
                if($this->options["TPRdamped"])
                {
                    $this->standings[$id]["TPR"] = round($ratingPlayerTotal / $player["Matches"] + 800 * ($player["Score"] - $this->standings[$id]["ScoreExpected"] + 0.5 - $this->expectancyFormula($ratingPlayerTotal / $player["Matches"], (array_sum($player["RatedOpponents"])/ count($player["RatedOpponents"])))) / ($player["Matches"] + 1));
                }
            }
        }

        private function hooglandTPR()
        {
            foreach($this->standings as $id => $player)
            {
                $ratingPlayerTotal = 0;
                foreach($player["PlayerMatches"] as $match)
                {
                    $expectedScore = $this->expectancyFormula($match["ratingPlayer"], $match["ratingOpponent"]);

                    //Limitation to ensure a win always results in a positive performance
                    if($player["Matches"] > 1 && $match["score"] = 1)
                        $expectedScore = min (1 - (1/$player["Matches"] - 1), $expectedScore);

                    $this->standings[$id]["ScoreExpected"] += $expectedScore;
                    $ratingPlayerTotal += $match["ratingPlayer"];
                }
                $this->standings[$id]["TPR"] = round($ratingPlayerTotal / $player["Matches"] + 800 * ($player["Score"] - $this->standings[$id]["ScoreExpected"]) / $player["Matches"]); 
                //DAMPED
                if($this->options["TPRdamped"])
                {
                    $this->standings[$id]["TPR"] = round($ratingPlayerTotal / $player["Matches"] + 800 * ($player["Score"] - $this->standings[$id]["ScoreExpected"] + 0.5 - $this->expectancyFormula($ratingPlayerTotal / $player["Matches"], (array_sum($player["RatedOpponents"])/ count($player["RatedOpponents"])))) / ($player["Matches"] + 1));
                }
            }
        }
        private function svnTPR()
        {
            foreach($this->standings as $id => $player)
            {
                $matches = $player["Matches"];
                $score = $player["Score"];
                $RtO = array_sum($player["RatedOpponents"])/ count($this->filter($player["PlayerMatches"],"ratingOpponent",0,true));
                $this->standings[$id]["TPR"] = round($RtO + 250 * (log($score * (6 + $matches) + 6) - log (($matches - $score) * ( 6 + $matches) + 6))); 
            }            
        }        


        /**
        * Function to calculate the expected score
        * 
        * @param mixed $rating
        * @param mixed $ratingOpponent
        */
        public function expectancyFormula ($rating, $ratingOpponent)
        {
            return 1 / (pow(10, -(($rating - $ratingOpponent) / 400)) + 1);
            //return 1 / (exp(-($rating - $ratingOpponent) / 200) + 1);
        } 



        function multi2sort($array)
        {
            /* foreach($this->sorting as $sortKey)
            {
            $sortKey = explode(",", $sortKey);
            if($sortKey[1] == "ASC")
            {
            if($a[$sortKey[0]] )
            }

            }   */


            foreach ($array as $key => $value) {
                $evalstring = '';
                foreach ($sorting as $sort_field) {
                    $tmp[$sort_field][$key] = $value[$sort_field];
                    $evalstring .= '$tmp[\'' . $sort_field . '\'], ';
                }
            }
            $evalstring .= '$array';
            $evalstring = 'array_multisort(' . $evalstring . ');';
            eval($evalstring);

            return $array;
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

         /**
        * Filter arrays
        * 
        * @param mixed $array
        * @param mixed $key
        * @param mixed $value
        * @param mixed $reverse --> filter out a key
        */
        public function filter($array, $key, $value, $reverse = false) {
            $temp = false;
            foreach($array as $i => $element){
                if((!$reverse && $element[$key] == $value) || ($reverse && $element[$key] != $value)){
                    $temp[] = $element;
                    unset($array[$i]);
                }      
            }
            return $temp;
        } 
        
        /**
        * put your comment there...
        * 
        * @param mixed $array
        * @param mixed $key
        */
        public function multiArraySum($array, $key)
        {
            $sum = 0;
            foreach($array as $num => $values) {
                $sum += $values[ $key ];
            }  
            return $sum;          
        }


    }
?>
