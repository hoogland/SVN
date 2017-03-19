<?php
/**
 * Created by PhpStorm.
 * User: Rob
 * Date: 27-9-2015
 * Time: 17:09
 */

namespace svn;

class matches
{
    var $db;
    var $seasonId;
    var $teamId;
    var $matchId;


    public function __construct($seasonId, $teamId, $matchId = null)
    {
        $this->seasonId = $seasonId;
        $this->teamId = $teamId;
        $this->matchId = $matchId;

        require_once __DIR__ . '../../../vendor/medoo.min.php';
        require_once __DIR__ . '../../../src/settings.php';

        $this->db = new \medoo(array(
                'database_type' => \svn\settings::dbType,
                'database_name' => \svn\settings::dbName,
                'server' => \svn\settings::server,
                'username' => \svn\settings::dbUsername,
                'password' => \svn\settings::dbPassword,
                'charset' => 'utf8',
            )
        );
    }

    /**
     * @return array|bool
     * Get all members of club
     */
    public function getTeamMatches()
    {
        return $this->db->select('svn_extern_wedstrijden_team', '*', array("AND" => array("team" => $this->teamId, "seizoen" => $this->seasonId), "ORDER" => array("datum ASC")));
    }

    public function createTeamMatch($data)
    {
        $result = $this->db->insert('svn_extern_wedstrijden_team', $data);

        if(!$result)
            return $result;
        
        //Fetch competition information
        require_once __DIR__ . '../../../src/generic.php';
        $generic = new \svn\generic();
         $externalClasses = $generic->getExternalClasses();

        $groupInfo = array_values(array_filter($externalClasses, function($arrayValue) use($data) { return $arrayValue['id'] == $data['groupId']; } ));
        $groupInfo = $groupInfo[0];

       //Set the right amount of getTeamMatchGames
        $teamMatchGames = 8;
        if($groupInfo['knsb'] && $groupInfo['klasse'] <= 1)
        {
            $teamMatchGames = 10;
        }
 
        //Create the getTeamMatchGames
        for($i = 1; $i <= $teamMatchGames; $i++)
        {
            $color = 2;
            if(($i + $data['uitwedstrijd']) % 2)
                $color = 1;

            $teamMatchGame = array("teamwedstrijdId" => $result, "bord" => $i, "kleur" => $color,  "tegenstanderNaam" => "", "score" => "0.5", "spelerId" => 0);
            $this->db->insert('svn_extern_partijen', $teamMatchGame);
        }
        //Return teamMatchId
        return $result;

    }

    public function saveTeamMatch($data)
    {
        return $this->db->update('svn_extern_wedstrijden_team', $data, array("id" => $this->matchId));
    }

    public function deleteTeamMatch()
    {
        //Delete all corresponding getTeamMatchGames
        $this->db->delete('svn_extern_partijen', array("teamwedstrijdId" => $this->matchId));
        //Delete match
        return $this->db->delete('svn_extern_wedstrijden_team', array("id" => $this->matchId));
    }

    public function getTeamMatchGames()
    {
        return $this->db->select('svn_extern_partijen' , "*", array("teamwedstrijdId" => $this->matchId));
    }

    public function saveTeamMatchGames($data)
    {
        foreach($data as $game)
        {
            $this->db->update('svn_extern_partijen', $game, array("id" => $game["id"]));
        }
    }
}