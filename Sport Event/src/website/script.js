angular.module('sportEvent', ['ui.bootstrap']);
angular.module('sportEvent').controller('SportEventListController', function ($scope,$http, $timeout) {

    $scope.eventsCollection = [];
    $scope.errorMessages = [];
    var retrieveItems = function () {
        $scope.errorMessages = [];
        $http.get('http://localhost:8931/api/point')
            .success(function (items) {
                $scope.eventsCollection = items;
            })
            .error(function () {
                $scope.errorMessages = [
                    { type: 'danger', msg: 'Could not get events' }];
            })
        ;
        $timeout(retrieveItems, 1000);
    };
    retrieveItems();

    $scope.scoreClass = function(point_id) {
        return point_id >= 1 ? 'finished': '';
    }
});

angular.module('sportEventTest', ['ui.bootstrap']);
angular.module('sportEventTest').controller('AthletesListController', function ($scope, $http, $timeout) {

    $scope.athletesCollection = [];
    $scope.errorMessages = [];
    var errorLogCleaner = function() {
        $scope.errorMessages = [];
        $timeout(errorLogCleaner, 3000);
    };
    errorLogCleaner();

    var retrieveItems = function () {
        $http.get('http://localhost:8931/api/athletes')
            .success(function (items) {
                $scope.athletesCollection = items;
            })
            .error(function () {
                $scope.errorMessages = [
                    { type: 'danger', msg: 'Could not get athletes data' }];
            })
        ;
        $timeout(retrieveItems, 10000);
    };
    retrieveItems();

    $scope.enterFinalCorridor = function(chipId) {
        console.log(chipId);
        var req = {
            method: 'POST',
            url: 'http://localhost:8931/api/point',
            headers: {
                'Content-Type': 'application/json'
            },
            data: { athleteChipId:chipId, pointId:0}
        };
        $http(req)
            .success(function () {
                $scope.errorMessages = [
                    { type: 'success', msg: 'Sent' }];
            })
            .error(function () {
                $scope.errorMessages = [
                    { type: 'danger', msg: 'Could not send request' }];
            })
        ;
    };
    $scope.crossFinishLine = function(chipId) {
        var req = {
            method: 'POST',
            url: 'http://localhost:8931/api/point',
            headers: {
                'Content-Type': 'application/json'
            },
            data: JSON.stringify({ athleteChipId:chipId, pointId:1})
        };

        $http(req)
            .success(function () {
                $scope.errorMessages = [
                    { type: 'success', msg: 'Sent' }];
            })
            .error(function () {
                $scope.errorMessages = [
                    { type: 'danger', msg: 'Could not send request' }];
            })
        ;
    };
});