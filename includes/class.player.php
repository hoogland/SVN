<?php
    class player
    {
        var $settings;      
        var $prefix;

        var $id;
        var $knsb;

        var $initials;
        var $firstname;
        var $middlename;
        var $surname;
        var $surnameClean;
        var $name;
        var $gender;
        var $birthdate;

        var $postalCode;
        var $city;
        var $phone;
        var $email;

        // Competition related
        var $rating;
        var $tpr;
        var $avgOpponent;
        var $nrOpponents;
        var $score;
        var $wp;
        var $sb;


        /**
        * Construct player
        * 
        * @param mixed $settings
        * @param mixed $id
        * @return player
        */
        public function __construct($settings, $id = null)
        {
            $this->settings = $settings;
            $this->prefix = $settings->prefix;
            $this->id = $id;  
            
            $this->getDetails();        
        }

        /**
        * Retreive the player details
        * 
        */
        public function getDetails()
        {
            if(!$this->id)
                return false;
            $sql = "SELECT * FROM ".settings::prefix."leden WHERE id = ".$this->id;
            $result = mysql_query($sql);

            $data = mysql_fetch_assoc($result);
            $this->knsb = $data["knsb"];
            $this->surname = implode(" ", array($data["tussenvoegsel"], $data["achternaam"]));
            $this->surnameClean = $data["achternaam"];
            $this->firstname = $data["voornaam"];
            $this->middlename = $data["tussenvoegsel"];
            $this->initials = $data["voorletters"];
            $this->name = implode(" ", array($this->firstname, $this->surname));

            //Get latest rating
            $sql = "SELECT rating FROM ".settings::prefix."rating WHERE speler_id = ".$this->id." ORDER BY datum DESC, type ASC LIMIT 0,1";
            $row = mysql_fetch_array(mysql_query($sql));
            if($row["rating"])
                $this->rating = $row["rating"];
            else
                $this->rating = 0;
        }
        
        /**
        * Update the basic details of a member
        * 
        * @param mixed $knsb
        * @param mixed $initials
        * @param mixed $firstname
        * @param mixed $middlename
        * @param mixed $surname
        */
        public function setDetails($knsb, $initials, $firstname, $middlename = "", $surname)
        {
            $sql = "UPDATE  ".settings::prefix."leden SET knsb = '".$knsb."', voorletters = '".$initials."', voornaam = '".$firstname."', tussenvoegsel = '".$middlename."', achternaam = '".$surname."' WHERE id = ".$this->id;
            $query = mysql_query($sql);
        }
        
        /**
        * Function to retrieve all rating data of a player
        * 
        */
        public function getRatingData()
        {
            $data;
            $sql = "SELECT * FROM  ".settings::prefix."rating WHERE speler_id = ".$this->id." ORDER BY datum ASC";
            $result = mysql_query($sql);
            while($row = mysql_fetch_assoc($result))
                $data[] = $row;
            return $data;
        }
        
        /**
        * get the scores of a player
        * 
        * @param mixed $color
        */
        public function getScores($color = "all", $tempo = false)
        {
            $sql = "SELECT SUM(CASE WHEN (uitslag = 1 AND speler_wit = ".$this->id.") OR (uitslag = 3 AND speler_zwart = ".$this->id.") THEN 1 ELSE 0 END) as Winst, SUM(CASE WHEN (uitslag = 2) THEN 1 ELSE 0 END) as Remise, SUM(CASE WHEN (uitslag = 3 AND speler_wit = ".$this->id.") OR (uitslag = 1 AND speler_zwart = ".$this->id.") THEN 1 ELSE 0 END) as Verlies FROM ".settings::prefix."partijen WHERE ";
            switch($color)
            {
                case false : $sql .= "(speler_wit = ".$this->id." OR speler_zwart = ".$this->id.")";break;
                case 1 : $sql .= "speler_wit = ".$this->id;break;
                case 2 : $sql .= "speler_zwart = ".$this->id."";break;
            }
            if($tempo)
                $sql .= " AND tempo = ".$tempo;

            $result = mysql_query($sql);
            return mysql_fetch_assoc($result);

        }
        
        public function getOpponentScores($tempo = false)
        {
            $sql = "SELECT
                    CASE WHEN speler_wit = ".$this->id." THEN speler_zwart ELSE speler_wit END as TegenstanderId,
                    SUM(CASE WHEN (uitslag = 1 AND speler_wit = ".$this->id.") OR (uitslag = 3 AND speler_zwart = ".$this->id.") THEN 1 ELSE 0 END) as Winst,
                    SUM(CASE WHEN (uitslag = 2) THEN 1 ELSE 0 END) as Remise,
                    SUM(CASE WHEN (uitslag = 3 AND speler_wit = ".$this->id.") OR (uitslag = 1 AND speler_zwart = ".$this->id.") THEN 1 ELSE 0 END) as Verlies
                    FROM
                        ".settings::prefix."partijen
                    WHERE
                        (speler_wit = ".$this->id." OR speler_zwart = ".$this->id.") ".($tempo ? " AND tempo = ".$tempo : "")." GROUP BY TegenstanderId;
                    ";
            $data;
            $result = mysql_query($sql);
            while($row = mysql_fetch_assoc($result))
                $data[] = $row;
            return $data;
        }
        
        public function getOpponentMatches($opponent)
        {
            $sql = "SELECT * FROM ".settings::prefix."partijen WHERE (speler_wit = ".$this->id." AND speler_zwart = ".$opponent.") OR (speler_wit = ".$opponent." AND speler_zwart = ".$this->id.") ORDER BY datum ASC";
            $data;
            $result = mysql_query($sql);
            while($row = mysql_fetch_assoc($result))
                $data[] = $row;
            return $data;
        }



    }
?>
