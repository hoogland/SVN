/**
 * Created by Rob on 28-9-2015.
 */

'use strict';

angular
    .module('app')
    .factory('RoundService', function($resource) {

        var API_PATH = '/API/v1/index.php/competitions/:competition/rounds';

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
                url: '/API/v1/index.php/rounds/:round'
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