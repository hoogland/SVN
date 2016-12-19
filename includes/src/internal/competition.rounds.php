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
     * @param $competitionId
     * @param null $round
     * @return array
     * @internal param null $roundId Query the existing round + providing details*
     * Query the existing round + providing details
     */
    public function getRounds($competitionId, $round = false)
    {
        if ($competitionId && $round === false)
            $data = $this->db->select('svn_rounds', '*', array('comp_id' => $competitionId));
        else
            $data = $this->db->select('svn_rounds', '*', array('id' => $round));
        foreach ($data as $key => $row) {
            $data[$key]["matrix_score"] = unserialize($data[$key]["matrix_score"]);
            $data[$key]["matrix_games"] = unserialize($data[$key]["matrix_games"]);
            $data[$key]["matrix_win"] = unserialize($data[$key]["matrix_win"]);
            $data[$key]["matrix_draw"] = unserialize($data[$key]["matrix_draw"]);
            $data[$key]["matrix_loss"] = unserialize($data[$key]["matrix_loss"]);
            $data[$key]["matrix_byes"] = unserialize($data[$key]["matrix_byes"]);
        }
        return $data;
    }

    /**
     * @param $competitionId
     * @param $round
     * @return bool
     *
     * Returns the id of a round based upon competitionId and round number
     */
    public function getRoundId($competitionId, $round)
    {
        if ($round && $competitionId) {
            $data = $this->db->select("svn_rounds", "id", array("AND" => array("comp_id" => $competitionId, "round" => $round)));
            $this->id = $data[0];
            return $this->id;
        }
        return false;
    }

    /**
     * @param $competitionId
     * @param $round Array(compId, round, date)
     *
     * Create a new round
     */
    public function createRound($round)
    {
        //Get highest round
        $maxRound = $this->db->max('svn_rounds', 'round', array('comp_id' => $round->comp_id));
        $round->round = $maxRound + 1;
        //Create the round
        $data = $this->db->insert('svn_rounds', array("comp_id" => $round->comp_id, "round" => $round->round, "date" => $round->date));
        $data = $this->getRounds(null, $data);
        return $data[0];
    }

    /**
     * @param $roundId
     * @param $roundData Array(roundId, round, date)
     *
     * Update existing round
     */
    public function updateRound($roundData)
    {
        if ($this->id) {
            $data = $this->db->update('svn_rounds', $roundData, array("id" => $this->id));
            return $data;
        }
    }

    /**
     * @return bool|int
     */
    public function deleteRound()
    {
        if ($this->id) {
            // Delete games
            $data = $this->db->delete('svn_partijen', array("round_id" => $this->id));
            if ($data !== false) {
                // Delete Byes

                // Delete standing

                // Delete round
                $data = $this->db->delete('svn_rounds', array("id" => $this->id));
                return $data;
            }
        }
    }

    /**
     * @param $competitionId
     * @return bool|int|string
     *
     * Gets the round nr of the previous round
     */
    public function getPreviousRound($competitionId, $round)
    {
        if ($round > 1)
            return $this->db->max('svn_rounds', 'round', array("AND" => array("comp_id" => $competitionId, "round[<]" => $round)));
        return 0;
    }
}