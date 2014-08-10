<?php
    class knsb
    {
        var $settings;
        var $prefix;
        var $errorClass;
        var $notificationClass;

        public function __construct($settings = null, $errorclass = null, $notificationClass = null)
        {
            $this->settings = $settings;
            $this->prefix = $settings->prefix;
            $this->errorClass = $errorclass;
            $this->notificationClass = $notificationClass;
        }


        public function insertKNSB($file, $date)
        {
            $date = date("Y-m-d",DateTime::createFromFormat('!d/m/Y', $date)->getTimestamp());
            if($file["error"] > 0)
                $this->notificationClass->add_note("Return Code: " . $file["error"]);
            else
            {
                if(($handle = fopen($file["tmp_name"], 'r')) !== FALSE) {
                    //check if rating is not already added
                    $sql = "SELECT * FROM ".settings::prefix."knsb_rating WHERE periode = '".$date."'";
                    $result = mysql_query($sql);
                    if(mysql_num_rows($result) > 0)
                    {
                        $this->notificationClass->add_note('Deze ratinglijst is al toegevoegd');
                        return;
                    }
                    // necessary if a large csv file
                    set_time_limit(0);
                    while(($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
                        //Insert the data into the database
                        if($data[4] != "")
                        {
                            $sql = "INSERT INTO ".settings::prefix."knsb_rating (periode, knsb, naam, rating) VALUES ('".$date."','".$data[0]."','".$data[1]."','".$data[4]."')";
                            echo $sql;
                            $result = mysql_query($sql);
                        }
                    }
                    fclose($handle);

                    $sql = "INSERT INTO ".settings::prefix."rating (datum, speler_id, type, rating) SELECT '".$date."', ".settings::prefix."leden.id, 1, rating FROM ".settings::prefix."leden, ".settings::prefix."knsb_rating WHERE periode = '".$date."' AND ".settings::prefix."knsb_rating.knsb = ".settings::prefix."leden.knsb";
                    mysql_query($sql);
                    $this->notificationClass->add_note('De ratinglijst is verwerkt.');

                }
            }
        }

        /**
        * Get all the ratinglists of the http://xaa.dohd.org website
        * 
        */
        public function getRatingLists()
        {
            $doc = new DOMDocument();
            $doc->loadHTMLFile('http://xaa.dohd.org/rating/ranglijst.php');
            $xpath = new DOMXPath($doc);
            $options = $xpath->query('//select[@name="rl_listid"]/option');
            $values = array();
            foreach ($options as $option) {
                $val = array();
                $val["value"] = $option->nodeValue;
                $val["id"] = $option->getAttribute('value');
                $values[] = $val;
            }
            return $values;
        }

        public function getRatings($list)
        {
            $doc = new DOMDocument();
            $doc->loadHTMLFile('http://xaa.dohd.org/rating/tabel_club.php?club='.SETTINGS::verenigingsNummer.'&listid='.$list);
            $xpath = new DOMXPath($doc);
            $rows = $xpath->query('//table[@class="ri_table_rating"]/tr');
            $values = array();
            foreach ($rows as $option) {
                $val = array();
                $html = $option->ownerDocument->saveXML( $option );
                $val["knsb"] = substr($html,strpos($html,'lidnr') + strlen("lidnr="),strpos($html,'&',strpos($html, 'lidnr')) - strpos($html,'lidnr') - strlen("lidnr=")); 
                $val["name"] = substr($html,strpos($html,'listid',strpos($html, 'ri_td_naam2')) + strlen("listid=".$list."\">"),strpos($html,'</a>',strpos($html, 'ri_td_naam2')) - strpos($html,'listid',strpos($html, 'ri_td_naam2')) - strlen("listid=".$list."\">")); 
                $val["rating"] = substr($html,strpos($html, 'ri_td_rating') + 14,strpos($html,'</td>',strpos($html, 'ri_td_rating')) - strpos($html,'ri_td_rating') - strlen("ri_td_rating\">")); 
                $val["ratingDiff"] = substr($html,strpos($html,'ri_td_ratdiff') + strlen("ri_td_ratdiff\">"),strpos($html,'</td>',strpos($html, 'ri_td_ratdiff')) - strpos($html,'ri_td_ratdiff') - strlen("ri_td_ratdiff\">"));  
                $values[] = $val;
            }
            return $values;
        }

        public function insertRating($date, $player, $rating, $type = 1, $force = false)
        {
            $data["code"] = false;
            //Select player
            $sql = "SELECT id FROM ".settings::prefix."leden WHERE knsb = '".$player."'";
            $result = mysql_query($sql);
            if(mysql_num_rows($result) != 1)
                $data = array("code" => 100, "message" => "De speler kan niet worden gevonden.");
            else
            {
                $row = mysql_fetch_assoc($result);
                $player = $row["id"];
            }

            //Check if not alreay set
            if(!$data["code"])
            {
                $sql = "SELECT * FROM ".settings::prefix."rating WHERE datum = '".$date."' AND speler_id = ".$player." AND type = ".$type."";
                $result = mysql_query($sql);
                if(mysql_num_rows($result) > 0)
                    $data = array("code" => 100, "message" => "De rating is al ingevoerd.");
            }

            //Insert rating
            if(!$data["code"])
            {
                $sql =  "INSERT INTO ".settings::prefix."rating (datum, speler_id, type, rating) VALUES ('".$date."',".$player.", ".$type.", ".$rating.");";
                $result = mysql_query($sql);
                $data["code"] = 200;  
            }
            return $data;

        }





    }
?>
