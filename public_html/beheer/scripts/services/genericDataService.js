/**
 * Created by Rob on 28-9-2015.
 */

'use strict';

angular
    .module('app')
    .factory('genericDataService', function($resource, SETTINGS) {

        var API_PATH = SETTINGS.API_BASEURL;

        var data = $resource(API_PATH, null,            {
            config: {
                url: SETTINGS.API_BASEURL + 'data/config',
                isArray: false
            },
            members: {
                url: SETTINGS.API_BASEURL + 'members',
                isArray: true
            },
            byeTypes:{
                url: SETTINGS.API_BASEURL + 'data/byeTypes',
                isArray: true
            },
            columns:{
                url: SETTINGS.API_BASEURL + 'data/columns/:compType',
                isArray: true
            }
        });

        return {
            queryConfig: function() {
                return data.config(null, function(successResult) {
                    console.log(SETTINGS);
                    return successResult;
                }, function(errorResult) {
                    console.log(errorResult);
                });
            },
            queryMembers: function() {
                return data.members(null, function(successResult) {
                    return successResult;
                }, function(errorResult) {
                    console.log(errorResult);
                });
            },
            queryByeTypes: function() {
                return data.byeTypes(null, function(successResult) {
                    return successResult;
                }, function(errorResult) {
                    console.log(errorResult);
                });
            },
            queryColumns: function(compType) {
                return data.columns({compType: compType}, function(successResult) {
                    return successResult;
                }, function(errorResult) {
                    console.log(errorResult);
                });
            }
        }
    });