/**
 * Created by Rob on 28-9-2015.
 */

'use strict';

angular
    .module('app')
    .factory('ByeService', function($resource) {

        var API_PATH = '/API/v1/index.php/byes/:byeId';

        var Bye = $resource(API_PATH, null,            {
                query: {
                    url: '/API/v1/index.php/rounds/:round/byes',
                    isArray: true
                },
                save:{
                    method: 'PUT',
                    isArray: true
                },
                create:{
                    method: 'POST',
                    isArray: true
                }
            });

        return {
            queryRoundByes: function(round) {
                return Bye.query({round : round}, function(successResult) {
                    return successResult;
                }, function(errorResult) {
                    console.log(errorResult);
                });
            },

            createBye: function(round, player, bye){
                return Bye.create({round: round, player: player, bye: bye}, function(successResult) {
                    return successResult;
                }, function(errorResult) {
                    console.log(errorResult);
                });
            },

            updateBye: function(bye){
                return Bye.save({byeId: bye.id},{bye:bye}, function(successResult) {
                    return successResult;
                }, function(errorResult) {
                    console.log(errorResult);
                });
            },

            deleteBye: function(bye){
                return Bye.delete({byeId: bye.id},null, function(successResult) {
                    return successResult;
                }, function(errorResult) {
                    console.log(errorResult);
                });
            }
        }
    });