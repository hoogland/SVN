<?php
    class data
    {
        var $settings;
        var $prefix;
        var $competitionColumns;
        var $tempi;
        var $tprMethods;

        /**
        * Construction of the competition class
        * 
        * @param mixed $settings
        * @return data
        */
        public function __construct()
        {
            $this->prefix = settings::prefix;

            $this->competitionColumns = array("Ranking","Subgroup","Name","Rating","TPR","Score","Matches","Percentage","RtO");
            $this->tempi = array(1 => "Snelschaken", 2 => "Rapid", 3 => "Regulier");
            $this->tprMethods = array('offsetTPR', 'adjustmentTPR', 'hooglandTPR', 'svnTPR');

        }
        
        function getCompetitionColums($compType)
        {
            $sql = "SELECT * FROM ".$this->prefix."columns WHERE ".($compType == "Keizer" ? "keizer = 1" : "zwitsers = 1");
            $result = mysql_query($sql);
            $data;
            for($a = 0; $a < mysql_num_rows($result); $a++)
                $data[] = mysql_fetch_assoc($result);
            return $data;
        }

        function getSeasons($desc = false)
        {
            $sql = "SELECT * FROM ".$this->prefix."seizoen WHERE id IN (SELECT seizoen_id FROM svn_competities) ORDER BY naam ".(!$desc ? "ASC" : "DESC");
            $result = mysql_query($sql);
            $data;
            for($a = 0; $a < mysql_num_rows($result); $a++)
                $data[] = mysql_fetch_assoc($result);
            return $data;
        }

        function getCompetitions($season)
        {
            $sql = "SELECT * FROM ".settings::prefix."competities WHERE seizoen_id = ".$season." ORDER BY naam ASC";
            $result = mysql_query($sql);
            $data;
            for($a = 0; $a < mysql_num_rows($result); $a++)
                $data[] = mysql_fetch_assoc($result);
            return $data;
        }    

        /**
        * Get all players within the club
        * 
        */
        function getPlayers($filter = null)
        {
            $sql = "SELECT * FROM ".settings::prefix."leden ".($filter ? "WHERE achternaam LIKE '%".implode("%' AND achternaam LIKE '%", explode(" ", $filter))."%' " : "")." ORDER BY achternaam ASC";
            $result = mysql_query($sql);
            $data;
            for($a = 0; $a < mysql_num_rows($result); $a++)
                $data[] = mysql_fetch_assoc($result);
            return $data;
        } 

        function getMembersExternal($filter = null)
        {
            $sql = "SELECT ".settings::prefix."leden.*, rating FROM ".settings::prefix."leden,".settings::prefix."rating WHERE ".settings::prefix."leden.id = speler_id AND ".settings::prefix."rating.id IN (SELECT MAX(id) FROM ".settings::prefix."rating GROUP BY speler_id) ".($filter ? "AND achternaam LIKE '%".implode("%' AND achternaam LIKE '%", explode(" ", $filter))."%' " : "")." ORDER BY achternaam ASC";
            $result = mysql_query($sql);
            $data;
            for($a = 0; $a < mysql_num_rows($result); $a++)
                $data[] = mysql_fetch_assoc($result);
            return $data;
        }

        function getKNSBPlayers($filter = null)
        {
            $sql = "SELECT A.* FROM ".settings::prefix."knsb_rating A INNER JOIN( SELECT MAX(id) id, knsb FROM ".settings::prefix."knsb_rating GROUP BY knsb) B ON A.id = B.id ".($filter ? "WHERE naam LIKE '%".implode("%' AND naam LIKE '%", explode(" ", $filter))."%'" : "")." ORDER BY naam ASC";       
            $result = mysql_query($sql);
            $data;
            for($a = 0; $a < mysql_num_rows($result); $a++)
                $data[] = mysql_fetch_assoc($result);
            return $data;
        }

        function getExternalGroups($knsb = 0)
        {
            $sql = "SELECT * FROM  ".settings::prefix."extern_groepen WHERE knsb = ".$knsb." ORDER BY klasse ASC, groep ASC";
            $result = mysql_query($sql);
            $data;
            for($a = 0; $a < mysql_num_rows($result); $a++)
                $data[] = mysql_fetch_assoc($result);
            return $data;
        }

        function getExternalTeams()
        {
            $sql = "SELECT * FROM  ".settings::prefix."teams ORDER BY naam ASC";
            $result = mysql_query($sql);
            $data;
            for($a = 0; $a < mysql_num_rows($result); $a++)
                $data[] = mysql_fetch_assoc($result);
            return $data;
        }
        
        function getClubs()
        {
            $sql = "SELECT * FROM  ".settings::prefix."verenigingen ORDER BY id ASC";
            $result = mysql_query($sql);
            $data;
            for($a = 0; $a < mysql_num_rows($result); $a++)
                $data[] = mysql_fetch_assoc($result);
            return $data;
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



    }
?>
