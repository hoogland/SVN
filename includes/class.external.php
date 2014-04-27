<?php
    class externalCompetition
    {
        var $settings;
        var $prefix;
        
        
        /**
        * put your comment there...
        * 
        * @param mixed $settings
        * @return externalCompetition
        */
        public function __construct($settings)
        {
            $this->settings = $settings;
            $this->prefix = $settings->prefix;   
        }
        
        
        public function getMatches($season = null, $team = null)
        {
            $sql = "SELECT * FROM ".$this->prefix."extern_wedstrijden_team WHERE 1 = 1";
            if($season)
                $sql .= " AND seizoen = ".$season;
            if($team)
                $sql .= " AND team = ".$team;
            $sql .= " ORDER BY datum ASC, id ASC";

            $result = mysql_query($sql);
            $data;
            while($row = mysql_fetch_assoc($result))
                $data[] = $row;
            return $data;
        }
        
        public function getSeasons()
        {
            $sql = "SELECT DISTINCT seizoen FROM ".$this->prefix."extern_wedstrijden_team ORDER BY seizoen ASC";
            echo $sql;
            $result = mysql_query($sql);
            $data;
            while($row = mysql_fetch_assoc($result))
                $data[] = $row;
            return $data;
        }
        
        public function getMatchDetails($matchId)
        {
            $sql = "SELECT * FROM ".$this->prefix."extern_wedstrijden_team WHERE id = ".$matchId;
            $result = mysql_query($sql);
            $data;
            while($row = mysql_fetch_assoc($result))
                $data = $row;   
            return $data;
        }

        public function getTeams()
        {
            $sql = "SELECT DISTINCT team FROM ".$this->prefix."extern_wedstrijden_team ORDER BY team ASC";
            $result = mysql_query($sql);
            $data;
            while($row = mysql_fetch_assoc($result))
                $data[] = $row;
            return $data;
        }
        
        public function getIndividualMatches($teamMatch)
        {
            $sql = "SELECT * FROM ".$this->prefix."extern_partijen WHERE teamwedstrijdId = ".$teamMatch;
            $result = mysql_query($sql);
            $data;
            while($row = mysql_fetch_assoc($result))
                $data[] = $row;
            return $data;
        }
        
        public function getTopscorers($season = null, $team = null)
        {
            $sql = "SELECT spelerId, sum(score) AS score, count(*) AS partijen FROM svn_extern_partijen WHERE teamwedstrijdId IN (SELECT id FROM svn_extern_wedstrijden_team WHERE seizoen = ".$season." AND team = ".$team.") GROUP BY spelerId ORDER BY score DESC, partijen ASC";
            $result = mysql_query($sql);
            $data;
            while($row = mysql_fetch_assoc($result))
                $data[] = $row;
            return $data;
        }
        
        /**
        * Create a new team match
        * 
        * @param mixed $season
        * @param mixed $team
        */
        public function createMatch($season, $team)
        {
            $sql = "INSERT INTO ".$this->prefix."extern_wedstrijden_team (team, seizoen) VALUES (".$team.", ".$season.")";
            $result = mysql_query($sql);
            return mysql_insert_id();
        }

        /**
        * Create Match Game
        *         
        * @param mixed $matchId
        * @param mixed $board
        * @param mixed $away
        */
        public function createGame($board, $matchId, $away)
        {
            $sql = "INSERT INTO ".$this->prefix."extern_partijen (teamwedstrijdId, bord, kleur) VALUES (".$matchId.", ".$board.", ".(($board + $away + 1) % 2 == 1 ? 1 : 2).")";
            $result = mysql_query($sql);
            return mysql_insert_id();
        }
        
        /**
        * Update team match
        * 
        * @param mixed $teamMatch
        * @param mixed $date
        * @param mixed $away
        * @param mixed $group
        * @param mixed $teamRating
        * @param mixed $teamScore
        * @param mixed $opponentName
        * @param mixed $opponentTeam
        * @param mixed $opponentRating
        * @param mixed $opponentScore
        */
        public function updateMatch($teamMatch, $date, $away, $groupId, $teamRating, $teamScore, $opponentName, $opponentTeam, $opponentRating, $opponentScore)
        {
            $sql = "UPDATE ".$this->prefix."extern_wedstrijden_team SET datum = '".date("Y-m-d", strtotime(str_replace("/","-",$date)))."', uitwedstrijd = '".$away."', groupId = '".$groupId."', teamElo = '".$teamRating."', score = '".$teamScore."', tegenstander = '".$opponentName."', tegenstanderTeam = '".$opponentTeam."', tegenstanderElo = '".$opponentRating."', scoreTegenstander = '".$opponentScore."' WHERE id = '".$teamMatch."' ";
            $result = mysql_query($sql);
        }
        
        /**
        * Update Match games
        * 
        * @param mixed $gameId
        * @param mixed $memberId
        * @param mixed $memberRating
        * @param mixed $opponentName
        * @param mixed $opponentKNSB
        * @param mixed $opponentRating
        * @param mixed $score
        */
        public function updateMatchGame($gameId, $memberId, $memberRating, $opponentName, $opponentKNSB, $opponentRating, $score)
        {
            $sql = "UPDATE ".$this->prefix."extern_partijen SET spelerId = ".$memberId.", spelerElo = ".$memberRating.", tegenstanderNaam = '".$opponentName."', tegenstanderKNSB = '".$opponentKNSB."', tegenstanderElo = '".$opponentRating."', score = '".$score."' WHERE id = ".$gameId;
            $result = mysql_query($sql);
        }


    }
?>
