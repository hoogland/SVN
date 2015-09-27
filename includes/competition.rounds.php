<?php
/**
 * Created by PhpStorm.
 * User: Rob
 * Date: 13-9-2015
 * Time: 14:41
 */

namespace svn\competition;


class rounds
{
    private $conn;

    function __construct(){
        require_once 'database.php';
        $db = new \database();
        $this->conn = $db->connect();
    }

    /**
     * @param $competitionId
     * @param null $roundId
     *
     * Query the existing round + providing details
     * @return array
     */
    public function getRounds($competitionId, $roundId = null){
        $query = $this->conn->prepare("SELECT * FROM svn_rounds WHERE comp_id = ?".($roundId ? " AND round_id = ?" : ""));
        if(!$roundId)
            $query->bind_param("i", $competitionId);
        else
            $query->bind_param("ii", $competitionId, $roundId);

        $query->execute();

        $rounds = $query->fetch_assoc();

        return $rounds;
    }

    /**
     * @param $competitionId
     * @param $roundData
     *
     * Create a new round
     */
    public function createRound($competitionId, $roundData){



    }

    /**
     * @param $roundId
     * @param $data
     *
     * Update existing round
     */
    public function updateRound($roundId, $data){

    }

    public function deleteRound($roundId){
        // Delete games

        // Delete standing

        // Delete round

    }
}