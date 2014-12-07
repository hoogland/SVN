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
            $result;     
            switch($data->action)
            {
                case "data" : $result = $this->data($data); break;
                case "extern" : $result = $this->extern($data);break;
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
