<?php
    class match
    {
        var $errorClass;
        var $notificationClass;
        var $settings;
        var $prefix;

        var $id;
        var $external;
        var $tempo;
        var $date;

        var $playerWhite;
        var $playerWhiteElo;
        var $playerBlack;
        var $playerBlackElo;
        var $result;


        var $pgnArray;

        public function __construct($settings, $errorClass = 0, $notificationClass = 0, $id = null, $external = 0)
        {
            $this->notificationClass = $notificationClass;
            $this->errorClass = $errorClass;
            $this->settings = $settings;
            $this->prefix = $settings->prefix;
            $this->id = $id;
            $this->external = $external;
        }

        public function getData()
        {
            if($this->id)
            {
                //GET data from database
                $sql = "SELECT * FROM ".settings::prefix."partijen WHERE id = ".$this->id."";
                $result = mysql_query($sql);
                if(mysql_num_rows($result) == 1)
                {
                    $data = mysql_fetch_assoc($result);

                    $this->result = $data["result"];
                    $this->playerWhiteElo = $data["rating_wit"];
                    $this->playerBlackElo = $data["rating_zwart"];
                    include_once('class.player.php');
                    $this->playerWhite = new player($this->settings, $data["speler_wit"]);
                    $this->playerWhite->getDetails();
                    $this->playerBlack = new player($this->settings, $data["speler_zwart"]);
                    $this->playerBlack->getDetails();

                    //GET PGN's
                    $sql = "SELECT * FROM ".settings::prefix."partijen_pgn WHERE idPartijIntern = ".$this->id."";                 
                    $result2 = mysql_query($sql);
                    for($a = 0; $a < mysql_num_rows($result2); $a++)
                        $this->pgnArray[] = mysql_fetch_assoc($result2);
                }
            }
            else
                return false;
        }

        /**
        * Create or update a PGN of a match
        * 
        * @param mixed $pgnText
        * @param mixed $matchId
        * @param mixed $pgnId
        */
        public function setPGN($pgnText, $matchId, $pgnId = null)
        {
            $this->getData();
            if($pgnId == "")
            {
                $sql = "INSERT INTO ".settings::prefix."partijen_pgn (pgn, idPartijIntern) VALUES ('".$pgnText."',".$matchId.")";
                mysql_query($sql);
                $this->notificationClass->add_note("Partij toegevoegd aan de partij: ".$this->playerWhite->name." - ".$this->playerBlack->name);   
            }
            else
            {
                $sql = "UPDATE ".settings::prefix."partijen_pgn SET pgn = '".$pgnText."' WHERE id = ".$pgnId;
                mysql_query($sql);
                $this->notificationClass->add_note("De partij: ".$this->playerWhite->name." - ".$this->playerBlack->name." is geupdate.");   
            }

        }

        public function removePGN($pgnId)
        {
            $sql = "DELETE FROM ".settings::prefix."partijen_pgn WHERE id = ".$pgnId;
            mysql_query($sql);
            $this->notificationClass->add_note("De partij is verwijderd.");   
        }


    }
?>
