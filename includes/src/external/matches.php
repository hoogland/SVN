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
        return $this->db->insert('svn_extern_wedstrijden_team', $data);
    }

    public function saveTeamMatch($data)
    {
        return $this->db->update('svn_extern_wedstrijden_team', $data, array("id" => $this->matchId));
    }
}