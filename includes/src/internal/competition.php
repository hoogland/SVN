<?php

/**
 * Created by PhpStorm.
 * User: Rob
 * Date: 13-9-2015
 * Time: 14:32
 */

namespace svn\competition;
error_reporting(E_ALL | E_STRICT);

class competition
{
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
     * @param array $competition
     *
     * Updates the competition settings/information
     * @return bool|int
     */
    public function update($competition)
    {
        $data = $this->db->update("svn_competities", $competition, array("id" => $competition->id));
        return $data;
    }

    /**
     * @param $option
     * @return mixed
     *
     * Creates an option if it doesn't exist yet
     */
    public function createOption($option){
        $data = $this->db->insert("svn_competitie_opties", array("comp_id" => $option->comp_id, "option" => $option->option, "value" => $option->value));
        return $data;
    }

    public function getOptions(){
        $tmp = $this->db->select("svn_competitie_opties", "*", array("comp_id" => $this->id));
        $data = array();
        foreach($tmp as $row)
            $data[$row["option"]] = $row;
        return $data;
    }

    /**
     * @param string $options comma separated options
     *
     * Updates the options of a competition
     */
    public function updateOption($id, $option)
    {
        $data = $this->db->update("svn_competitie_opties", $option, array("id" => $id));
        return $data;
    }

    /**
     * Games section
     */

    /**
     * @param null $roundId
     * @return array|bool
     *
     * Get the games of a competition / round
     */
    public function getGames($roundId = null, $round = null, $playerId = null)
    {
        if ($roundId) {
            $data = $this->db->select('svn_partijen', '*', array('round_id' => (int)$roundId));
            return $data;
        }
        if ($round) {
            $data = $this->db->select('svn_partijen', '*', array("AND" => array("ronde" => (int)$round, "comp_id" => $this->id)));
            return $data;
        }
        if ($playerId) {
            $data = $this->db->select('svn_partijen', '*', array("AND" => array("comp_id" => $this->id, "OR" => array("speler_wit" => (int)$playerId,"speler_zwart" => (int)$playerId))));
            return $data;
        }
    }

    /**
     * @param $round
     * @param $data  array{competition, round, speler_wit, speler_zwart}
     *
     */
    public function createGame($competition, $round, $player_white, $player_black)
    {
        // Get ratings for players
        $rating = $this->db->select("svn_rating", "rating", array("AND" => array("speler_id" => $player_white->id, "datum[<=]" => $round->date), "ORDER" => array("datum DESC"), "LIMIT" => array(0, 1)));
        $player_white->rating = $rating[0];
        $rating = $this->db->select("svn_rating", "rating", array("AND" => array("speler_id" => $player_black->id, "datum[<=]" => $round->date), "ORDER" => array("datum DESC"), "LIMIT" => array(0, 1)));
        $player_black->rating = $rating[0];

        //Insert game
        $this->db->insert("svn_partijen", array('speler_wit' => $player_white->id, 'rating_wit' => $player_white->rating, 'speler_zwart' => $player_black->id, 'rating_zwart' => $player_black->rating, 'tempo' => $competition->stand_tempo, 'comp_id' => $competition->id, 'datum' => $round->date, 'ronde' => $round->round, 'round_id' => $round->id));
        //echo $this->db->last_query();
        //Return games
        return $this->getGames($round->id);
    }

    /**
     * @param $gameId
     * @param $data array(player_white, player
     * @return bool|int
     *
     * Updates an existing game. All data should be given
     */
    public function updateGame($gameId, $data)
    {
        if ($gameId) {
            $data = $this->db->update('svn_partijen', $data, array('id' => $gameId));
            return $data;
        }
    }

    /**
     * @param $gameId
     * @return bool|int
     *
     * Deleting a game
     */
    public function deleteGame($gameId)
    {
        $data = $this->db->delete('svn_partijen', array('id' => $gameId));
        return $data;
    }

    /**
     * Byes section
     */

    /**
     * @param null $round
     * @param null $playerId
     * @param null $roundnr
     * @return array|bool Get the byes of a competition / round
     *
     * Get the byes of a competition / round
     */
    public function getByes($round = null, $playerId = null, $roundnr = null)
    {
        if ($round) {
            $data = $this->db->select('svn_bye', '*', array('round_id' => (int)$round));
            return $data;
        }
        if ($roundnr) {
            $data = $this->db->select("svn_rounds", "id", array("AND" => array("comp_id" => $this->id, "round" => $roundnr)));
            $data = $this->db->select('svn_bye', '*', array('round_id' => $data[0]));
            return $data;
        }
        if ($playerId) {
            $data = $this->db->select('svn_bye', '*', array('user_id' => (int)$playerId));
            return $data;
        }
    }

    /**
     * @param $round
     * @param $player
     * @param $bye
     * @return array|bool
     *
     * Create a new bye
     */
    public function createBye($round, $player, $bye)
    {
        //Insert bye
        $this->db->insert("svn_bye", array('round_id' => $round->id, 'user_id' => $player->id, 'bye_id' => $bye->id));
        //Return byes
        return $this->getByes($round->id);
    }

    /**
     * @param $byeId
     * @param $data array(player_white, player
     * @return bool|int
     *
     * Updates an existing bye. All data should be given
     */
    public function updateBye($byeId, $data)
    {
        if ($byeId) {
            $data = $this->db->update('svn_bye', $data, array('id' => $byeId));
            return $data;
        }
    }

    /**
     * @param $byeId
     * @return bool|int
     *
     * Deleting a bye
     */
    public function deleteBye($byeId)
    {
        $data = $this->db->delete('svn_bye', array('id' => $byeId));
        return $data;
    }


    /**
     * Players section within the competition
     */

    /**
     * @param $playerId
     */
    public function addPlayer($player)
    {
        $data = $this->db->insert('svn_comp_deelname', array('comp_id' => $this->id, 'speler_id' => $player->id, 'plaats' => $player->plaats));
        if($data) {
            $data = $this->getPlayers($player->id);
            return $data[0];
        }
        return false;
    }

    /**
     * Retrieve the id's of all the players in the competition
     */
    public function getPlayers($playerId = null)
    {
        $where = array('comp_id' => $this->id);
        if($playerId)
            $where = array("AND" => array("comp_id" => $this->id, "speler_id" => $playerId));
        $data = $this->db->select('svn_comp_deelname', array('[>]svn_leden' => array('speler_id' => 'id')), array('svn_leden.id(id)','svn_comp_deelname.id(participantId)', 'comp_id','speler_id','svn_comp_deelname.plaats(plaats)','sub','jeugdtempo','voorletters','voornaam','tussenvoegsel','achternaam','knsb','adres','postcode','telefoon','geslacht','type_lid','geb_dat','email'), $where);
        return $data;
    }

    public function updatePlayer($participantId, $participant)
    {
        //Strip unnecessary information
        $allowed = array('plaats', 'sub', 'jeugdtempo');
        foreach($participant as $key => $value)
        {
            if(!in_array($key, $allowed)){
                unset($participant->$key);
            };
        }
        $data = $this->db->update('svn_comp_deelname', $participant, array('id' => $participantId));
        return $data;
    }


    /**
     * @param $playerId
     *
     * Removes player from the competition
     */
    public function deletePlayer($playerId)
    {
        // Remove games

        // Remove byes

        // Remove participant
        $data = $this->db->delete('svn_comp_deelname', array('id' => $playerId));
        return $data;
    }

    /**
     * @param $sorting
     *
     * Function to save the initial sorting of the players
     */
    public function updateSorting($sorting)
    {

    }


    /**
     * TPR Section
     */

    /**
     * @param string $method
     * @param int $damped
     * @param array $opponents Array of ratings of opponents
     * @return int $tpr
     *
     * Calculate the TPR of a player based on the provided ratings
     */
    public function calculateTPR($method, $damped = null, $opponents)
    {
        $tpr = null;
        switch ($method) {
            case 'offsetTPR':
                $tpr = $this->getOffsetTPR($damped, $opponents);
                break;
            case 'adjustmentTPR':
                $tpr = $this->getAdjustmentTPR($damped, $opponents);
                break;
            case 'hooglandTPR':
                $tpr = $this->getHooglandTPR($damped, $opponents);
                break;
            case 'svnTPR':
                $tpr = $this->getSvnTPR($damped, $opponents);
                break;
            default:
                $tpr = $this->getOffsetTPR($damped, $opponents);
        }
        return $tpr;
    }

    /**
     * @param $damped
     * @param $opponents
     * @return int null
     *
     * Returns the
     */
    private function getOffsetTPR($damped, $opponents)
    {
        $tpr = null;


        return $tpr;
    }

    /**
     * @param $damped
     * @param $opponents
     * @return null
     */
    private function getAdjustmentTPR($damped, $opponents)
    {
        $tpr = null;

        return $tpr;
    }


    /**
     * Helper functions
     */


    /**
     * @param $array
     * @param $sort_by
     * @return mixed
     *
     * Sorts a multi dimensional array
     */
    function multisort($array, $sort_by)
    {
        foreach ($array as $key => $value) {
            $evalstring = '';
            foreach ($sort_by as $sort_field) {
                $tmp[$sort_field][$key] = $value[$sort_field];
                $evalstring .= '$tmp[\'' . $sort_field . '\'], ';
            }
        }
        $evalstring .= '$array';
        $evalstring = 'array_multisort(' . $evalstring . ');';
        eval($evalstring);
        return $array;
    }

}