<?php
/**
 * Created by PhpStorm.
 * User: Rob
 * Date: 9-10-2015
 * Time: 12:56
 */

namespace svn\competition;


use svn\rating;

class standing extends competition
{
    var $matrixScore;
    var $matrixGames;
    var $matrixWin;
    var $matrixDraw;
    var $matrixLoss;

    var $standing;
    var $playerLocation;
    var $roundClass;
    var $roundId;
    var $competitionOptions;

    /**
     * @param $round
     * @return array|bool $standing
     *
     * returns the standings of a specific round
     * @internal param $roundId
     */
    public function getStanding($round)
    {
        $standing = $this->db->select("svn_standing", "*", array("AND" => array("round" => $round, "comp_id" => $this->id)));
        return $standing;
    }

    public function saveStanding($round)
    {
        include_once("competition.rounds.php");
        include("rating.php");
        $this->roundClass = new round();
        $this->roundId = $this->roundClass->getRoundId($this->id, $round);
        $this->competitionOptions = $this->getOptions();
        $this->calculateStanding($round, $this->roundClass->getPreviousRound($this->id, $round));

        //Delete current items
        $this->db->delete("svn_standing", array("AND" => array("round" => $round, "comp_id" => $this->id)));
        foreach ($this->standing as $row) {
            $this->db->insert("svn_standing", $row);
        }

        //Update round (matrices)
        $this->roundClass->updateRound(array("matrix_score" => serialize($this->matrixScore),
            "matrix_games" => serialize($this->matrixGames),
            "matrix_win" => serialize($this->matrixWin),
            "matrix_draw" => serialize($this->matrixDraw),
            "matrix_loss" => serialize($this->matrixLoss)));
    }

    /**
     * @param $round
     * @param $previousRound
     */
    public function calculateStanding($round, $previousRound)
    {
        //Get current standing if exists
        $participants = $this->getPlayers();
        if ($round > 1) {
            $this->standing = $this->getStanding($previousRound);
            //Update roundnr
            foreach ($this->standing as $key => $row) {
                $this->standing[$key]["round"] = $round;
                $this->playerLocation[$row["player_id"]] = $key;
            }

            //Get round details + set matrices
            $prevRound = new round();
            $prevRound->getRoundId($this->id, $previousRound);
            $roundData = $prevRound->getRounds($this->id, $prevRound->id);

            //Set the matrices
            $this->matrixScore = $roundData[0]["matrix_score"];
            $this->matrixGames = $roundData[0]["matrix_games"];
            $this->matrixWin = $roundData[0]["matrix_win"];
            $this->matrixDraw = $roundData[0]["matrix_draw"];
            $this->matrixLoss = $roundData[0]["matrix_loss"];
        } else
            $this->createInitialData($participants, $round);

        if ($this->competitionOptions["System"]["value"] == "Keizer" || $this->competitionOptions["System"]["value"] == "Keizerling") {
            /* if($this->competitionOptions["KeizerInitialSorting"]["value"] == 2)
                 $this->*/
            $this->standing = $this->setKeizerValues($this->standing);
        }

        //Get the games of the round
        $games = $this->getGames(null, $round);
        //Update matrices with games
        foreach ($games as $game) {
            $this->matrixGames[$game["speler_wit"]][$game["speler_zwart"]]++;
            $this->matrixGames[$game["speler_zwart"]][$game["speler_wit"]]++;
            switch ($game["uitslag"]) {
                case 1: {
                    $this->matrixWin[$game["speler_wit"]][$game["speler_zwart"]]++;
                    $this->matrixLoss[$game["speler_zwart"]][$game["speler_wit"]]++;
                    break;
                }
                case 2: {
                    $this->matrixDraw[$game["speler_wit"]][$game["speler_zwart"]]++;
                    $this->matrixDraw[$game["speler_zwart"]][$game["speler_wit"]]++;
                    break;
                }
                case 3: {
                    $this->matrixWin[$game["speler_zwart"]][$game["speler_wit"]]++;
                    $this->matrixLoss[$game["speler_wit"]][$game["speler_zwart"]]++;
                    break;
                }
            }
            //Keizer score
            if ($this->competitionOptions["System"]["value"] == "Keizer") {
                $this->standing[$this->playerLocation[$game["speler_wit"]]]["KeizerTotaal"] += $this->standing[$this->playerLocation[$game["speler_zwart"]]]["Value"] * (3 - $game["uitslag"]) / 2;
                $this->standing[$this->playerLocation[$game["speler_zwart"]]]["KeizerTotaal"] += $this->standing[$this->playerLocation[$game["speler_wit"]]]["Value"] * ($game["uitslag"] - 1) / 2;
            }

            //Add rating information
            $this->standing[$this->playerLocation[$game["speler_wit"]]]["RtOTotal"] += $game["rating_zwart"];
            $this->standing[$this->playerLocation[$game["speler_zwart"]]]["RtOTotal"] += $game["rating_wit"];
        }

        $this->processByes($this->roundId);

        //Update score matrix + create Score table
        $score = array();
        foreach ($this->standing as $player) {
            foreach ($this->standing as $opponent) {
                $this->matrixScore[$player["player_id"]][$opponent["player_id"]] = $this->matrixWin[$player["player_id"]][$opponent["player_id"]] + 0.5 * $this->matrixDraw[$player["player_id"]][$opponent["player_id"]];
            }
            $score[$player["player_id"]] = array_sum($this->matrixScore[$player["player_id"]]);
        }

        //Update simple scores
        foreach ($this->standing as $key => $player) {
            $this->standing[$key]["Score"] = array_sum($this->matrixScore[$player["player_id"]]);
            $this->standing[$key]["Games"] = array_sum($this->matrixGames[$player["player_id"]]);
            $this->standing[$key]["Win"] = array_sum($this->matrixWin[$player["player_id"]]);
            $this->standing[$key]["Draw"] = array_sum($this->matrixDraw[$player["player_id"]]);
            $this->standing[$key]["Loss"] = array_sum($this->matrixLoss[$player["player_id"]]);
            if($this->standing[$key]["Games"] > 0)
                $this->standing[$key]["RtO"] = $this->standing[$key]["RtOTotal"] / $this->standing[$key]["Games"];
            if ($this->standing[$key]["Score"] > 0)
                $this->standing[$key]["Percentage"] = $this->standing[$key]["Score"] / ($this->standing[$key]["Win"] + $this->standing[$key]["Draw"] + $this->standing[$key]["Loss"]);
        }

        //Update Opponent dependant scores
        $score = array(0 => $score);
        $WPtmp = $this->matrixmult($this->matrixGames, $score);
        $SBtmp["win"] = $this->matrixmult($this->matrixWin, $score);
        $SBtmp["draw"] = $this->matrixmult($this->matrixDraw, $score);
        $rating = new rating();
        foreach ($this->standing as $key => $player) {
            $this->standing[$key]["WP"] = array_sum($WPtmp[$player["player_id"]]);
            $this->standing[$key]["SB"] = array_sum($SBtmp["win"][$player["player_id"]]) + 0.5 * array_sum($SBtmp["draw"][$player["player_id"]]);
            $this->standing[$key]["TPR"] = $rating->calculateTPR("offsetTPR", $this->competitionOptions["tprDemping"]["value"], null, $this->standing[$key]["RtOTotal"], $this->standing[$key]["Score"], $this->standing[$key]["Games"]);
        }

        //Update keizerling score
        if ($this->competitionOptions["System"]["value"] == "Keizerling")
            $this->standing = $this->setKeizerlingScore($this->standing, $this->matrixScore, $this->competitionOptions["KeizerIterations"]["value"]);


        //Set the final ranking
        $this->standing = $this->setRanking($this->standing);

        //Set Keizer values
        if ($this->competitionOptions["System"]["value"] == "Keizer") {
            $this->standing = $this->setKeizerValues($this->standing);
        }

    }

    /**
     * @param $roundId
     * Process the byes of a round
     */
    private function processByes($roundId){
        $byes = $this->getByes($roundId);
        $byeTypes = array("", "awayClub","awayWithMessage", "awayNoMessage", "awayArbiter", "awayBye");
        if($this->competitionOptions["System"]["value"] == "Keizer"){
            foreach($byes as $bye){
                $this->standing[$this->playerLocation[$bye["user_id"]]]["KeizerTotaal"] += $this->standing[$this->playerLocation[$bye["user_id"]]]["Value"] * $this->competitionOptions[$byeTypes[$bye["bye_id"]]]["value"];
            }
        }
    }

    /**
     * Set the ranking of a competition
     * @param $standing
     * @return array
     */
    private function setRanking($standing)
    {
        $standing = array_reverse($this->multisort($standing, explode(",", $this->competitionOptions["RankOrder"]["value"])));
        for ($a = 1; $a < count($standing) + 1; $a++)
            $standing[$a - 1]["Ranking"] = $a;

        return $standing;
    }

    /**
     * Set the values of the players according to the current ranking
     * The players should therefore already have a ranking
     * @param $standing
     * @return TYPE_NAME
     */
    private function setKeizerValues($standing)
    {
        foreach ($standing as $key => $row)
            $standing[$key]["Value"] = $this->competitionOptions["KeizerMaxValue"]["value"] - ($row["Ranking"] - 1) * $this->competitionOptions["KeizerSteps"]["value"];
        return $standing;
    }

    /**
     * @param $standing
     * @param $scoreMatrix
     * @param $iterations
     * @return array
     *
     * Function to set the Keizerling scores with multiple iterations when necessary
     */
    private function setKeizerlingScore($standing, $scoreMatrix, $iterations){
        //Get current values of player
        $value = array();
        foreach ($standing as $player)
            $value[0][$player["player_id"]] = $player["Value"];

        //Calculate total values
        $score = $this->matrixmult($scoreMatrix, $value);

        //Set Keizer scores
        foreach ($standing as $key => $player) {
            $standing[$key]["KeizerTotaal"] = $score[$player["player_id"]][0];
            if($player["Games"] > 0)
            $standing[$key]["KeizerGemiddelde"] = $score[$player["player_id"]][0] / $player["Games"];
        }

        //Calculate new standing
        $standing = $this->setRanking($standing);
        $standing = $this->setKeizerValues($standing);

        //Execute multiple iterations if necessary
        if($iterations > 1)
            $standing = $this->setKeizerlingScore($standing, $scoreMatrix, $iterations - 1);

        return $standing;
    }

    /**
     * @param $participants
     * @param $round
     *
     * Create the initial standing in case of the first round
     */
    private function createInitialData($participants, $round)
    {
        foreach ($participants as $key => $player) {
            $this->standing[] = array(
                "round" => $round
                , "comp_id" => $this->id
                , "player_id" => $player["speler_id"]
                , "Ranking" => $player["plaats"]
                , "Subgroup" => $player["sub"]
                , "Score" => 0
                , "Win" => 0
                , "Draw" => 0
                , "Loss" => 0
                , "Games" => 0
                , "Percentage" => 0
                , "WP" => 0
                , "SB" => 0
                , "Rating" => null
                , "TPR" => 0
                , "RtO" => 0
                , "RtOTotal" => 0
                , "StartWaarde" => null
                , "Value" => null
                , "KeizerTotaal" => null
                , "KeizerGemiddelde" => null
            );
            $this->playerLocation[$player["speler_id"]] = $key;
        }

        foreach ($participants as $playerA) {
            foreach ($participants as $playerB) {
                $this->matrixGames[$playerA["speler_id"]][$playerB["speler_id"]] = 0;
                $this->matrixWin[$playerA["speler_id"]][$playerB["speler_id"]] = 0;
                $this->matrixDraw[$playerA["speler_id"]][$playerB["speler_id"]] = 0;
                $this->matrixLoss[$playerA["speler_id"]][$playerB["speler_id"]] = 0;
                $this->matrixScore[$playerA["speler_id"]][$playerB["speler_id"]] = 0;
            }
        }
    }

    /**
     * @param $m1
     * @param $m2
     * @return array
     * @throws Exception
     *
     * Calculates a matrix multiplication (m1 = the big one, m2 the small one)
     */
    private function matrixmult($m1, $m2)
    {
        $m3 = array();
        foreach ($m1 as $key => $val) {
            foreach ($m2 as $key2 => $val2) {
                $m3[$key][$key2] = 0;
                foreach ($val2 as $key3 => $val3)
                    $m3[$key][$key2] += $m1[$key][$key3] * $val3;
            }
        }
        return ($m3);
    }
}