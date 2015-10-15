/**
 * Created by Rob on 28-9-2015.
 */

'use strict';

angular
    .module('app')
    .factory('MatchService', function($resource) {

        var API_PATH = '/API/v1/index.php/matches/:matchId';

        var Match = $resource(API_PATH, null,            {
                query: {
                    url: '/API/v1/index.php/rounds/:round/matches',
                    isArray: true
                },
                queryCompRound: {
                    url: '/API/v1/index.php/competitions/:competition/rounds/:round/matches',
                    isArray: true
                },
                save:{
                    method: 'PUT'
                },
                create:{
                    method: 'POST',
                    isArray: true
                },
                getPlayerGames:{
                    url: '/API/v1/index.php/competitions/:competition/players/:player/matches',
                    isArray: true
                },
                getPlayerByes:{
                    url: '/API/v1/index.php/competitions/:competition/players/:player/byes',
                    isArray: true
                }
            });

        return {
            queryRoundMatches: function(round) {
                return Match.query({round : round}, function(successResult) {
                    return successResult;
                }, function(errorResult) {
                    console.log(errorResult);
                });
            },
            queryCompRoundMatches: function(competition, round) {
                return Match.queryCompRound({competition : competition,round : round}, function(successResult) {
                    return successResult;
                }, function(errorResult) {
                    console.log(errorResult);
                });
            },

            createMatch: function(competition, round, playerWhite, playerBlack){
                return Match.create({competition: competition, round: round, speler_wit: playerWhite, speler_zwart: playerBlack}, function(successResult) {
                    return successResult;
                }, function(errorResult) {
                    console.log(errorResult);
                });
            },

            updateMatch: function(match){
                return Match.save({matchId: match.id},{match:match}, function(successResult) {
                    return successResult;
                }, function(errorResult) {
                    console.log(errorResult);
                });
            },

            deleteMatch: function(match){
                return Match.delete({matchId: match.id},null, function(successResult) {
                    return successResult;
                }, function(errorResult) {
                    console.log(errorResult);
                });
            },
            getPlayerMatches: function(competition, player){
                return Match.getPlayerGames({competition: competition,player: player}, function (successResult) {
                    return successResult;
                }, function (errorResult) {
                    console.log(errorResult);
                });
            },
            getPlayerByes: function(competition, player){
                return Match.getPlayerByes({competition: competition,player: player}, function (successResult) {
                    return successResult;
                }, function (errorResult) {
                    console.log(errorResult);
                });
            },
        }
    });