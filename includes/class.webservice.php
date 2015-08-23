<?php
    class webservice
    {
        var $init;

        function __autoload($class_name) {
            if(file_exists('class.'.$class_name . '.php')) {
                require_once('class.'.$class_name . '.php');    
            } else {
                throw new Exception("Unable to load $class_name.");
            }
        }        

        function __construct($init)
        {
            $this->init = $init;
        }

        function execute($data)
        {
            $result = array();
            switch($data->action)
            {
                case "data" : $result = $this->data($data); break;
                case "extern" : $result = $this->extern($data);break;
                case "intern" : $result = $this->intern($data);break;
                default: return;
            }
            if($result)
                return array("status" => array("code" => 200), "data" => $result);
        }

        function extern($data)
        {
            include('class.external.php');
            $class = new externalCompetition();
            if($data->method == "GET")
            {
                switch($data->subaction)
                {      
                    case "matches" : return $class->getMatches($data->data->season, $data->data->team, $data->data->details); break;
                    case "topScorers" : return $class->getTopscorers($data->data->season, $data->data->team); break;
                    default: return;
                }
            }
        }

        private function intern($data)
        {
            include('class.competition.php');
            include_once('class.player.php');
            $class = new competition(null, $data->data->competition);
            $compInfo = $class->getGeneralData();
            if($data->method == "GET")
            {
                switch($data->subaction)
                {
                    case "generalData" : return $compInfo;break;
                    case "rounds" : return $class->getRounds();break;
                    case "standing" : return $class->getStanding($data->data->round);break;
                    case "matches" : return $class->getMatches($data->data->round);break;
                    default: return;
                }
            }
        }

        function data($data)
        {
            include('class.data.php');
            $class = new data();
            if($data->method == "GET")
            {
                switch($data->subaction)
                {
                    case "defaultData" : return $class->getDefaultData(); break;
                    case "competitionColumns" : return $class->getCompetitionColums($data->data->compType); break;
                    case "seasons" : return $class->getSeasons(); break;
                    case "teams" : return $class->getExternalTeams(); break;
                    case "competitions" : return $class->getCompetitions($data->data->season);break;
                    case "players" : return $class->getPlayers($data->data->filter);break; 
                    default: return;
                }           
            }   
        }
    }
?>
