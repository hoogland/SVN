/**
 * Created by Rob on 28-9-2015.
 */

angular
    .module('app')
    .controller('CompetitionsCtrl', function ($scope, $state, $modal, CompetitionService, RoundService, genericDataService, DataFactory) {
        $scope.roundSelect = {};
        console.log(DataFactory);
        $scope.getRounds = function (competitionId) {
            $scope.rounds = RoundService.queryRounds(competitionId);
        };

        $scope.createRound = function (competitionSelect, date) {
            var newRound = {
                comp_id: competitionSelect.id,
                date: date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate()
            };

            var roundAdded = RoundService.createRound(newRound);
            $scope.rounds.push(roundAdded);
            $scope.roundSelect = $scope.rounds[$scope.rounds.length - 2];
            $modalInstance.close();
        };

        $scope.deleteRound = function (round) {
            console.log(RoundService.deleteRound(round));
            $scope.rounds.splice($scope.rounds.indexOf(round), 1);
        };

        $scope.queryOptions = function (competitionId) {
            if (competitionId) {
                CompetitionService.queryOptions(competitionId)
                    .$promise.then(function (value) {
                        $scope.competitionOption = value;
                        genericDataService.queryColumns(value.System.value).$promise.then(function (value) {
                            $scope.competitionOption.Ranking = [[], []];
                            $scope.competitionOption.Display = [[], []];

                            //Copy the used items in the Ranking[1] column
                            var columns = $scope.competitionOption.RankOrder.value.split(',');
                            for (var i = 0; i < columns.length; ++i) {
                                for (var j = 0; j < value.length; ++j) {
                                    if (columns[i] === value[j].name) {
                                        $scope.competitionOption.Ranking[1].push(value[j]);
                                    }
                                }
                                ;
                            }
                            ;
                            for (var i = 0; i < value.length; i++) {
                                if (columns.indexOf(value[i].name) < 0) {
                                    $scope.competitionOption.Ranking[0].push(value[i]);
                                }
                            }
                            ;
                            //Copy the used items in the Ranking[1] column
                            var columns = $scope.competitionOption.DisplayFields.value.split(',');
                            for (var i = 0; i < columns.length; ++i) {
                                for (var j = 0; j < value.length; ++j) {
                                    if (columns[i] === value[j].name) {
                                        $scope.competitionOption.Display[1].push(value[j]);
                                    }
                                }
                                ;
                            }
                            ;
                            for (var i = 0; i < value.length; i++) {
                                if (columns.indexOf(value[i].name) < 0) {
                                    $scope.competitionOption.Display[0].push(value[i]);
                                }
                            }
                            ;
                        });
                    });
            }
            ;
        };

        $scope.updateOption = function (competitionId, option, name) {
            if (option.id)
                CompetitionService.updateOption(competitionId, option);
            else {
                option.option = name;
                option.comp_id = competitionId;
                CompetitionService.createOption(competitionId, option);
            }
            if (name == "System")
                $scope.getColumns(option.value);
        };

        $scope.queryParticipants = function (competitionId) {
            $scope.participants = CompetitionService.queryParticipants(competitionId);
            console.log('participants');
        };

        $scope.createParticipant = function (competitionId, player, plaats) {
            player.plaats = plaats;
            $scope.participants.push(CompetitionService.createParticipant(competitionId, player));
        };

        $scope.updateParticipant = function (competitionId, player) {
            CompetitionService.updateParticipant(competitionId, player);
        };

        $scope.deleteParticipant = function (competitionId, player) {
            alertify
                .confirm()
                .set('title', 'Verwijderen')
                .set('message', 'Weet je zeker dat je de speler wilt verwijderen?')
                .set('labels', {ok: 'ja', cancel: 'nee'})
                .set({transition: 'fade'})
                .set('onok', function (closeEvent) {
                    CompetitionService.deleteParticipant(competitionId, player);
                    var index = $scope.participants.indexOf(player);
                    $scope.participants.splice(index, 1);
                })
                .show();
        };

        $scope.saveStanding = function (competitionId, round) {
            CompetitionService.saveStanding(competitionId, round);
        };


        $scope.openRoundsModal = function () {
            var modalInstance = $modal.open({
                backdrop: true,
                backdropClick: true,
                scope: $scope,
                templateUrl: '/beheer/partials/internal/roundsModal.tpl.html',
                controller: 'modalRoundsCtrl'
            })
        };


        //Open the Edit Round view to add/edit Matches & Byes
        $scope.editRound = function (roundId) {
            $state.go('internal.grouping.round', {'roundId': roundId, 'competitionId': $scope.competitionSelect.id});
        };

        $scope.$watch('competitionSelect', function (newValue) {
            console.log('nieuwe competitie');
            console.log(newValue);
            if (newValue) {
                $scope.getRounds(newValue.id);
                $scope.queryParticipants(newValue.id);
                $scope.queryOptions(newValue.id);
            }
        });

        //The options for the Ranking & Display Fields lists
        $scope.sortableOptions = {
            placeholder: "app",
            connectWith: ".sortList",
            stop: function (e, ui) {
                saveLists();
            }
        };

        //Save the Ranking & DisplayField lists onChange
        function saveLists() {
            //Save ranking
            var ranking = [];
            $scope.competitionOption.Ranking[1].forEach(function (option) {
                ranking.push(option.name);
            });
            $scope.competitionOption.RankOrder.value = ranking.join();
            var RankOrder = $scope.competitionOption.RankOrder;
            $scope.updateOption(RankOrder.comp_id, RankOrder, RankOrder.option);

            //Save display
            var display = [];
            $scope.competitionOption.Display[1].forEach(function (option) {
                display.push(option.name);
            });
            $scope.competitionOption.DisplayFields.value = display.join();
            var DisplayFields = $scope.competitionOption.DisplayFields;
            $scope.updateOption(DisplayFields.comp_id, DisplayFields, DisplayFields.option);
        }

        //Recalculate all the standings
        $scope.recalculateStandings = function (competitionId, rounds) {
            console.log("Competitie opslaan");
            console.log(rounds);
            recalculateStandings(competitionId, rounds, 1);
        };

        //Actual function to recursively calculate all the scores
        function recalculateStandings(competitionId, rounds, round) {
            console.log(round);
            CompetitionService.saveStanding(competitionId, rounds[round - 1].round).$promise.then(function () {
                if (round < rounds.length) {
                    round++;
                    recalculateStandings(competitionId, rounds, round);
                }
                else {
                    console.log("competitie opgeslagen");
                    return true;
                };
            });

        }
    })
;