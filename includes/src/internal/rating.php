<?php
/**
 * Created by PhpStorm.
 * User: Rob
 * Date: 10-10-2015
 * Time: 15:46
 */

namespace svn;


class rating
{
    /**
     * @param $rating
     * @param $ratingOpponent
     * @return float
     *
     * Function to calculate the expected score
     */
    public function expectancyFormula($rating, $ratingOpponent)
    {
        return 1 / (pow(10, -(($rating - $ratingOpponent) / 400)) + 1);
    }

    public function calculateTPR($method, $damped = false, $rating, $RtOTotal, $score, $games)
    {
        if($games == 0)
            return 0;
        $RtO = $RtOTotal / $games;
        switch ($method) {
            case "offsetTPR":
                return $this->offsetTPR($damped, $RtO, $score, $games);
                break;
            case "adjustmentTPR":
                return $this->adjustmentTPR();
                break;
            case "hooglandTPR":
                return $this->hooglandTPR();
                break;
            case "svnTPR":
                return $this->svnTPR($RtO, $score, $games);
                break;
            default:
                return $this->offsetTPR($damped, $RtO, $score, $games);
        }
    }

    private function offsetTPR($damped, $RtO, $score, $games)
    {
        if ($damped)
            return $this->dampedOffsetTPR($RtO, $score, $games);
        else {
            $percentage = $score / $games;
            if ($percentage == 1)
                $percentage = 0.999;
            if ($percentage == 0)
                $percentage = 0.001;
            return round($RtO - 400 * log10(1 / $percentage - 1));
        }
    }

    private function dampedOffsetTPR($RtO, $score, $games)
    {
        return round($RtO - 400 * log10(1 / (($score + 0.5) / ($games + 1)) - 1));
    }

    //ToDo rewrite
    private function adjustmentTPR()
    {
        $ratingPlayerTotal = 0;
        foreach ($player["PlayerMatches"] as $match) {
            $this->standings[$id]["ScoreExpected"] += $this->expectancyFormula($match["ratingPlayer"], $match["ratingOpponent"]);
            $ratingPlayerTotal += $match["ratingPlayer"];
        }
        $this->standings[$id]["TPR"] = round($ratingPlayerTotal / $player["Matches"] + 800 * ($player["Score"] - $this->standings[$id]["ScoreExpected"]) / $player["Matches"]);
        //DAMPED
        if ($this->options["TPRdamped"]) {
            $this->standings[$id]["TPR"] = round($ratingPlayerTotal / $player["Matches"] + 800 * ($player["Score"] - $this->standings[$id]["ScoreExpected"] + 0.5 - $this->expectancyFormula($ratingPlayerTotal / $player["Matches"], (array_sum($player["RatedOpponents"]) / count($player["RatedOpponents"])))) / ($player["Matches"] + 1));
        }
    }

    //ToDo rewrite (first the adjustment TPR then this)
    private function hooglandTPR()
    {
        foreach ($this->standings as $id => $player) {
            $ratingPlayerTotal = 0;
            foreach ($player["PlayerMatches"] as $match) {
                $expectedScore = $this->expectancyFormula($match["ratingPlayer"], $match["ratingOpponent"]);
                //Limitation to ensure a win always results in a positive performance
                if ($player["Matches"] > 1 && $match["score"] = 1)
                    $expectedScore = min(1 - (1 / $player["Matches"] - 1), $expectedScore);
                $this->standings[$id]["ScoreExpected"] += $expectedScore;
                $ratingPlayerTotal += $match["ratingPlayer"];
            }
            $this->standings[$id]["TPR"] = round($ratingPlayerTotal / $player["Matches"] + 800 * ($player["Score"] - $this->standings[$id]["ScoreExpected"]) / $player["Matches"]);
            //DAMPED
            if ($this->options["TPRdamped"]) {
                $this->standings[$id]["TPR"] = round($ratingPlayerTotal / $player["Matches"] + 800 * ($player["Score"] - $this->standings[$id]["ScoreExpected"] + 0.5 - $this->expectancyFormula($ratingPlayerTotal / $player["Matches"], (array_sum($player["RatedOpponents"]) / count($player["RatedOpponents"])))) / ($player["Matches"] + 1));
            }
        }
    }

    private function svnTPR($RtO, $score, $games)
    {
        return round($RtO + 250 * (log($score * (6 + $games) + 6) - log(($games - $score) * (6 + $games) + 6)));
    }
}