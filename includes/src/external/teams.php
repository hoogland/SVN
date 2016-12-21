<?php
/**
 * Created by PhpStorm.
 * User: Rob
 * Date: 27-9-2015
 * Time: 17:09
 */

namespace svn;

class teams
{
    var $db;


    public function __construct()
    {
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
    public function getTeams()
    {
        return $this->db->select('svn_teams', '*', array("ORDER" => array("naam ASC")));
    }
}