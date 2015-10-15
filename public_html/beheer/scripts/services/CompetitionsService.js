/**
 * Created by Rob on 28-9-2015.
 */

'use strict';

angular
    .module('app')
    .factory('CompetitionService', function($resource) {

        var API_PATH = '/API/v1/index.php/seasons/:season/competitions';

        var Competition = $resource(API_PATH, null,            {
            query: {
                url: '/API/v1/index.php/seasons/:season/competitions',
                isArray: true
            },
            participants: {
                url: '/API/v1/index.php/competitions/:competition/participants',
                isArray: true
            },
            createParticipant: {
                url: '/API/v1/index.php/competitions/:competition/participants',
                method: 'POST'
            },
            updateParticipant: {
                url: '/API/v1/index.php/competitions/:competition/participants/:participantId',
                method: 'PUT'
            },
            deleteParticipant: {
                url: '/API/v1/index.php/competitions/:competition/participants/:participantId',
                method: "DELETE"
            },
            createOption: {
                url: '/API/v1/index.php/competitions/:competition/options',
                method: 'POST'
            },
            queryOptions: {
                url: '/API/v1/index.php/competitions/:competition/options',
                method: 'GET'
            },
            updateOption:{
                url: '/API/v1/index.php/competitions/:competition/options/:option',
                method: 'PUT'
            },
            getStanding:{
                url: '/API/v1/index.php/competitions/:competition/standings/:round',
                isArray: true
            },
            saveStanding:{
                url: '/API/v1/index.php/competitions/:competition/standings/:round',
                method: 'PUT',
            }
        });

        return {
            queryCompetitions: function(season) {
                return Competition.query({season : season.season}, function(successResult) {
                    return successResult;
                }, function(errorResult) {
                    console.log(errorResult);
                });
            },
            queryParticipants: function(competition) {
                return Competition.participants({competition : competition}, function(successResult) {
                    return successResult;
                }, function(errorResult) {
                    console.log(errorResult);
                });
            },
            createParticipant: function(competition, player){
                return Competition.createParticipant({competition : competition},{player: player}, function(successResult) {
                    return successResult;
                }, function(errorResult) {
                    console.log(errorResult);
                });
            },
            updateParticipant: function(competition, player){
                return Competition.updateParticipant({competition : competition, participantId: player.participantId},{player: player}, function(successResult) {
                    return successResult;
                }, function(errorResult) {
                    console.log(errorResult);
                });
            },
            deleteParticipant: function(competition, player){
                return Competition.deleteParticipant({competition : competition, participantId: player.participantId}, function(successResult) {
                    return successResult;
                }, function(errorResult) {
                    console.log(errorResult);
                });
            },
            queryOptions: function(competition) {
                return Competition.queryOptions({competition : competition}, function(successResult) {
                    return successResult;
                }, function(errorResult) {
                    console.log(errorResult);
                });
            },
            createOption: function(competition, option){
                return Competition.createOption({competition : competition},{option: option}, function(successResult) {
                    return successResult;
                }, function(errorResult) {
                    console.log(errorResult);
                });
            },
            updateOption: function(competition, option){
                return Competition.updateOption({competition: competition,option: option.id}, {option: option}, function (successResult) {
                    return successResult;
                }, function (errorResult) {
                    console.log(errorResult);
                });
            },
            getStanding: function(competition, round){
                return Competition.getStanding({competition: competition,round: round}, function (successResult) {
                    return successResult;
                }, function (errorResult) {
                    console.log(errorResult);
                });
            },
            saveStanding: function(competition, round){
                return Competition.saveStanding({competition: competition,round: round},null, function (successResult) {
                    return successResult;
                }, function (errorResult) {
                    console.log(errorResult);
                });
            },
        }
    });