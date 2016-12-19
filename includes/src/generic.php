<?php
/**
 * Created by PhpStorm.
 * User: Rob
 * Date: 27-9-2015
 * Time: 17:09
 */

namespace svn;


class generic
{
    var $seasonId;
    var $db;


    public function __construct($seasonId = null)
    {
        $this->seasonId = $seasonId;

        require_once __DIR__ . '../../vendor/medoo.min.php';
        require_once 'settings.php';

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
     * @return array
     *
     * Gets the public configuration for the website
     */
    public function getConfig()
    {
        $data = array(
            "vereniging" => \svn\settings::vereniging ,
            "analyticsUA" => \svn\settings::AnalyticsUA ,
            "teamName" => \svn\settings::teamName ,
            "externalTeam" => \svn\settings::standardCompetitionExternal ,
            "externalSeason" => \svn\settings::standardCompetitionExternalSeason ,
        );
        return $data;
    }

    /**
     * @return array|bool
     * Get all members of club
     */
    public function getMembers()
    {
        return $this->db->select('svn_leden', array("id", "voorletters", "voornaam", "tussenvoegsel", "achternaam", "knsb"), array("ORDER" => array("achternaam ASC")));
    }

    /**
     * @return array|bool
     * Get all available seasons
     */
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
        if ($this->seasonId)
            return $this->db->select('svn_competities', '*', array('seizoen_id' => $this->seasonId));
        return false;
    }

    /**
     * @return array|bool
     *
     * Retrieve the different bye types that exist
     */
    public function getByeTypes()
    {
        return $this->db->select('svn_bye_types', '*');
    }

    /**
     * @param $competitionType
     *
     * Retrieves the fields that are applicable for a specific competition type
     */
    public function getCompFields($competitionType)
    {
        return $this->db->select("svn_columns", "*", array(strtolower($competitionType) => 1));

    }
}