<?php
    class ratinglijst
    {
        var $location = "http://xaa.dohd.org/rating/clubs.php?club=1428";
        var $ratings;
        var $ratingList;

        /**
        * Functie om alle gegevens in te lezen inde variable Ratings
        * Van de site http://xaa.dohd.org/rating/clubs.php
        */
        public function read_data_xaa()
        {
            $data = $this->getHTML($this->location);
            $spelersTabel;
            $process = false;
            for($a = 0; $a < count($data); $a++)
            {
                //Opslaan welke lijst het is
                if(strstr($data[$a],"Lijst:") && preg_match("/[0-9]{4}-[0-9]{2}/", $data[$a]))
                {
                    preg_match("/[0-9]{4}-[0-9]{2}/", $data[$a], $this->ratingList);
                    $this->ratingList = $this->ratingList[0]."-01";
                    print_r($this->ratingList); 
                }

                //Controleren of de spelersgegevens aanwezig zijn
                if($data[$a] == "<table class=\"clubspelerinfo\">")
                    $process = true;

                //Begin met het verwerken van de gegevens
                if($process)
                {
                    //Controleren of de spelersinfo in de rij zit
                    if(strstr($data[$a], "spelers.php"))
                        $spelersTabel .= $data[$a];

                    //Controleren einde van de tabel
                    if($data[$a] == "</table>")
                        $process = false;      
                }
            }
            $spelersTabel = str_replace("<tr>", "", $spelersTabel);
            $spelersTabel = explode("</tr>", $spelersTabel);

            foreach($spelersTabel as $speler)
            {
                $speler = explode("</>",str_replace("<>","",preg_replace("<td[a-z=\" ]*>","",$speler)));

                $player["rating"] = $speler[3];
                $player["knsb"] = $speler[2];
                $player["naam"] = preg_replace('/<[^>]*>/', '', $speler[1]);
                $this->ratings[] = $player;
                //print_r($player);
            }
        }


        public function saveRatinglist()
        {
            foreach($this->ratings as $player)
            {
                //Controleren of speler al bestaat + ophalen id
                $sql = "SELECT * FROM svn_leden WHERE knsb = ".$player["knsb"];
                $query = mysql_query($sql);
                if(mysql_num_rows($query) == 1)
                {
                    $data = mysql_fetch_assoc($query);
                    $player["id"] = $data["id"];
                }
                elseif(mysql_num_rows($query) == 0)
                {
                    //Toevoegen van de speler
                    print_r($player);
                }
                
                //Controleren of de rating al bestaat
                $sql = "SELECT * FROM svn_rating WHERE id = ".$player["id"]." AND datum = '".$this->ratingList."'";
                $query = mysql_query($sql);
                if(mysql_num_rows($query) == 0)
                {
                    //Toevoegen rating
                    $sql = "INSERT INTO svn_rating VALUES ('',\"".$this->ratingList."\",".$player["id"].",1,".$player["rating"].")";
                    mysql_query($sql);
                }
            }
            
            $sql = "SELECT * FROM svn_leden WHERE knsb = ".$speler_geg["id"];
            $result = mysql_query($sql);
            $speler_dat = mysql_fetch_array($result);

            if($speler_geg["rating"] != "")
            {
                $sql = "INSERT INTO svn_rating VALUES ('',\"".$datum."\",".$speler_dat[0].",1,".$speler_geg["rating"].")";
                //echo $sql;
                mysql_query($sql);
            }

        }



        /**
        * Functie om de html te downloaden
        * 
        * @param mixed $url
        */
        function getHTML($url)
        {
            $c = curl_init($url);
            curl_setopt($c,CURLOPT_RETURNTRANSFER, true);

            $html = curl_exec($c);

            curl_close($c);
            $delimiter = "\r\n";
            if(!strstr($html, $delimiter));
            $delimiter = "\n";
            return preg_split ('/$\R?^/m', $html);//explode("\r\n",$html);
        }                          

    }
?>
