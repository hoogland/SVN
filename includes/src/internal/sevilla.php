<?php
/**
 * Created by PhpStorm.
 * User: Rob
 * Date: 24-8-2016
 * Time: 20:22
 */

namespace svn\competition;
error_reporting(E_ALL | E_STRICT);

class sevilla{

    var $db;
    var $competitionId;

    public function __construct($competitionId = null)
    {
        $this->competitionId;


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



}