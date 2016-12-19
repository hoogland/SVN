<?php
/**
 * Created by PhpStorm.
 * User: Rob
 * Date: 24-8-2016
 * Time: 20:37
 */

namespace svn\competition;
error_reporting(E_ALL | E_STRICT);

class season{

    var $id;
    var $db;

    /**
     * @param $id
     */
    public function __construct($id = null)
    {
        $this->id = $id;
        //echo __DIR__;

        require_once __DIR__ . '../../../vendor/medoo.min.php';
        require_once __DIR__ . '../../settings.php';

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
     * @param $season
     * @return array
     *
     * Create a new season
     */
    public function createSeason($season){
        $data = $this->db->insert("svn_seizoen", array("naam" => $season->name));
        return $data;
    }



}

