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
                'password' => \svn\settings::dbPassword)
        );
    }

    /**
     * @param array $data
     *
     * Updates the competition settings/information
     */
    public function update($data)
    {
        if (array_key_exists('options'))
            $this->updateOptions($data['options']);
    }

    /**
     * @param string $options comma separated options
     *
     * Updates the options of a competition
     */
    private function updateOptions($options)
    {

    }

    /**
     * Games section
     */

    /**
     * @param null $round
     * @return array|bool
     *
     * Get the games of a competition / round
     */
    public function getGames($round = null)
    {
        if ($round) {
            $data = $this->db->select('svn_partijen', '*', array('round_id' => (int)$round));
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
        $rating = $this->db->select("svn_rating", "rating", ["AND" => ["speler_id" => $player_white->id, "datum[<=]" => $round->date], "ORDER" => ["datum DESC"], "LIMIT" => [0, 1]]);
        $player_white->rating = $rating[0];
        $rating = $this->db->select("svn_rating", "rating", ["AND" => ["speler_id" => $player_black->id, "datum[<=]" => $round->date], "ORDER" => ["datum DESC"], "LIMIT" => [0, 1]]);
        $player_black->rating = $rating[0];

        //Insert game
        $this->db->insert("svn_partijen", ['speler_wit' => $player_white->id, 'rating_wit' => $player_white->rating, 'speler_zwart' => $player_black->id, 'rating_zwart' => $player_black->rating, 'tempo' => $competition->stand_tempo, 'comp_id' => $competition->id, 'datum' => $round->date, 'ronde' => $round->round, 'round_id' => $round->id]);
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
     * Standings section
     */

    public function getStanding($round = null)
    {

    }

    public function createStanding($round)
    {

    }

    public function updateStanding($round)
    {
        //Delete standing
        $this->deleteStanding($round);
    }

    public function deleteStanding($round)
    {

    }





    /**
     * Players section
     */

    /**
     * Retrieve the id's of all the players in the competition
     */
    public function getPlayers()
    {
        $data = $this->db->select('svn_comp_deelname', array('[>]svn_leden' => ['speler_id' => 'id']), '*', array('comp_id' => $this->id));
        return $data;
    }

    /**
     * @param $playerId
     */
    public function addPlayer($playerId)
    {

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
     * @return null
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

}