/**
 * Created by Rob on 28-9-2015.
 */

'use strict';

angular
    .module('app')
    .factory('genericDataService', function($resource) {

        var API_PATH = '/API/v1/index.php/';

        var data = $resource(API_PATH, null,            {
            members: {
                url: '/API/v1/index.php/members',
                isArray: true
            },
            byeTypes:{
                url: '/API/v1/index.php/data/byeTypes',
                isArray: true
            },
            columns:{
                url: '/API/v1/index.php/data/columns/:compType',
                isArray: true
            }
        });

        return {
            queryMembers: function(season) {
                return data.members(null, function(successResult) {
                    return successResult;
                }, function(errorResult) {
                    console.log(errorResult);
                });
            },
            queryByeTypes: function(season) {
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