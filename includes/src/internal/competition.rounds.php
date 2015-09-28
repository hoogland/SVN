<?php
/**
 * Created by PhpStorm.
 * User: Rob
 * Date: 13-9-2015
 * Time: 14:41
 */

namespace svn\competition;


class round
{
    var $id;
    var $db;

    public function __construct($id = null){
        $this->id = $id;
        //echo __DIR__;

        require_once __DIR__ . '../../../vendor/medoo.min.php';
        require_once __DIR__ . '../../settings.php';

        $this->db = new \medoo(array(
                'database_type' => \svn\settings::dbType,
                'database_name' => \svn\settings::dbName,
                'server' => \svn\settings::server,
                'username' => \svn\settings::dbUsername,
                'password' => \svn\settings::dbPassword)
        );
    }

    /**
     * @param $competitionId
     * @param null $roundId
     *
     * Query the existing round + providing details
     * @return array
     */
    public function getRounds($competitionId){
        $data = $this->db->select('svn_rounds', '*', array('comp_id' => $competitionId));
        return $data;
    }

    /**
     * @param $competitionId
     * @param $roundData Array(compId, round, date)
     *
     * Create a new round
     */
    public function createRound($roundData){
        $data = $this->db->insert('svn_rounds', $roundData);
        return $data;
    }

    /**
     * @param $roundId
     * @param $roundData Array(roundId, round, date)
     *
     * Update existing round
     */
    public function updateRound($roundData){
        if($this->id) {
            $data = $this->db->update('svn_rounds', $roundData, array("id" => $this->id));
            return $data;
        }
    }

    /**
     * @return bool|int
     */
    public function deleteRound(){
        if($this->id) {
            // Delete games

            // Delete standing

            // Delete round
            $data = $this->db->delete('svn_rounds', array("id" => $this->id));
            return $data;
        }
    }
}