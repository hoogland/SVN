/**
 * Created by Rob on 28-9-2015.
 */

'use strict';

angular
    .module('app')
    .factory('RoundService', function($resource, SETTINGS) {

        var API_PATH = SETTINGS.API_BASEURL + 'competitions/:competition/rounds';

        var Round = $resource(API_PATH, null,            {
            save:{
                method: 'PUT'
            },
            create:{
                method: 'POST',
                isArray: false
            },
            delete:{
                method: 'DELETE',
                url: SETTINGS.API_BASEURL + 'rounds/:round'
            }
        });

        return {
            queryRounds: function( competition) {
                return Round.query({competition : competition}, function(successResult) {
                    return successResult;
                }, function(errorResult) {
                    console.log(errorResult);
                });
            },
            createRound: function(round){
                return Round.create({competition : round.comp_id}, {round: round}, function(successResult) {
                    return successResult;
                }, function(errorResult) {
                    console.log(errorResult);
                });
            },
            deleteRound: function(round){
                return Round.delete({round:round.id}, function(successResult){
                    return successResult;
                }, function (errorResult){
                    console.log(errorResult);
                })
            }
        }
    });