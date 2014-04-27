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





    }
?>
