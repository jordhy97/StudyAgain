(function() {

    'use strict';

    angular
        .module("studyAgain")
        .controller('AnswerController', AnswerController);

    function AnswerController($http, $state, $stateParams, $rootScope) {
        var vm = this;

        if ($state.is("answerEdit")) {
            $http.get('api/answers/' + $stateParams.answerId).then(function (response) {
                vm.content = response.data.body;
            }).catch(function (response) {
                $state.go('404Error', {});
            });
        }

        vm.delete = function(answerId) {
            $http.delete('api/answers/' + answerId).then(function(response) {
                $rootScope.$broadcast('answerChanged');
            })
        };

        vm.upVote = function(answerId, voteStatus) {
            if (voteStatus !== 'up') {
                $http.post('/api/answers/' + answerId + '/upVote').then(function (response) {
                    $rootScope.$broadcast('answerChanged');
                }).catch(function(response) {

                });
            }
        };

        vm.downVote = function(answerId, voteStatus) {
            if (voteStatus !== 'down') {
                $http.post('/api/answers/' + answerId + '/downVote').then(function (response) {
                    $rootScope.$broadcast('answerChanged');
                }).catch(function(response) {

                });
            }
        };

        vm.edit = function() {
            var answer = {
                body: vm.content
            };
            $http.put('api/answers/' + $stateParams.answerId, answer).then(function (response) {
                $state.go('questionsDetail', {questionId : $stateParams.questionId});
            }).catch(function (response) {

            })
        };
    }
})();