<?php

/**
 * Created by PhpStorm.
 * User: Rob
 * Date: 13-9-2015
 * Time: 14:32
 */

namespace svn\competition;

class competition
{
    var $id;

    /**
     * @param $id
     */
    public function __construct($id){
        $this->id = $id;
    }

    /**
     * @param array $data
     *
     * Updates the competition settings/information
     */
    public function update($data){
        if(array_key_exists('options'))
            $this->updateOptions($data['options']);
    }

    /**
     * @param string $options comma separated options
     *
     * Updates the options of a competition
     */
    private function updateOptions($options){

    }

    /**
     * Games section
     */

    public function getGames($round = null){

    }

    public function createGame($round, $data){

    }

    public function updateGame($gameId, $data){

    }

    public function deleteGame($gameId){

    }


    /**
     * Standings section
     */

    public function getStanding($round = null){

    }

    public function createStanding($round){

    }

    public function updateStanding($round){
        //Delete standing
        $this->deleteStanding($round);
    }

    public function deleteStanding($round){

    }





    /**
     * Players section
     */

    /**
     * Retrieve the id's of all the players in the competition
     */
    public function getPlayers(){

    }

    /**
     * @param $playerId
     */
    public function addPlayer($playerId){

    }

    /**
     * @param $playerId
     *
     * Removes player from the competition
     */
    public function deletePlayer($playerId){
        // Remove games

        // Remove byes

        // Remove participant

    }

    /**
     * @param $sorting
     *
     * Function to save the initial sorting of the players
     */
    public function updateSorting($sorting){

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
    public function calculateTPR($method, $damped = null, $opponents){
        $tpr = null;
        switch($method){
            case "offsetTPR":        $tpr = $this->getOffsetTPR($damped, $opponents);break;
            case "adjustmentTPR":    $tpr = $this->getAdjustmentTPR($damped, $opponents);break;
            case "hooglandTPR":      $tpr = $this->getHooglandTPR($damped, $opponents);break;
            case "svnTPR":           $tpr = $this->getSvnTPR($damped, $opponents);break;
            default:                 $tpr = $this->getOffsetTPR($damped, $opponents);
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
    private function getOffsetTPR($damped, $opponents){
        $tpr = null;


        return $tpr;
    }

    /**
     * @param $damped
     * @param $opponents
     * @return null
     */
    private function getAdjustmentTPR($damped, $opponents){
        $tpr = null;

        return $tpr;
    }

}