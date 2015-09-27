<?php
/**
 * Created by PhpStorm.
 * User: Rob
 * Date: 27-9-2015
 * Time: 17:09
 */

namespace svn;


class season
{
    var $id;
    var $db;


    public function __construct($id = null)
    {
        $this->id = $id;

        require_once __DIR__ . '../../vendor/medoo.min.php';
        require_once 'settings.php';

        $this->db = new \medoo(array(
                'database_type' => \svn\settings::dbType,
                'database_name' => \svn\settings::dbName,
                'server' => \svn\settings::server,
                'username' => \svn\settings::dbUsername,
                'password' => \svn\settings::dbPassword)
        );
    }


    public function getSeasons()
    {
        return $this->db->select('svn_seizoen', '*');
    }


    /**
     * @return array|bool
     * Get all competitions in a season
     */
    public function getCompetitions()
    {
        if ($this->id)
            return $this->db->select('svn_competities', '*', array('seizoen_id' => $this->id));
        return false;
    }

}