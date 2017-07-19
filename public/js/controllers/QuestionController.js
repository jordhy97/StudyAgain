(function() {

    'use strict';

    angular
        .module("studyAgain")
        .controller('QuestionController', QuestionController);

    function QuestionController($http, $state, $stateParams) {
        var vm = this;

        if ($state.is("questionsEdit")) {
            $http.get('api/questions/' + $stateParams.questionId).then(function (response) {
                vm.title = response.data.title;
                vm.content = response.data.body;
            }).catch(function(response) {

            });
        } else if ($state.is("questionsDetail")) {
            $http.get('api/questions/' + $stateParams.questionId).then(function (response) {
                vm.id = response.data.id;
                vm.title = response.data.title;
                vm.content = response.data.body;
                vm.tags = response.data.tags;
            }).catch(function(response) {

            });
        }

        vm.edit = function() {
            var question = {
                title: vm.title,
                body: vm.content
            };
            $http.put('api/questions/' + $stateParams.questionId, question).then(function (response) {
                $state.go('home', {src : 'ask'});
            }).catch(function (response) {

            })
        };

        vm.delete = function(questionId) {
            $http.delete('api/questions/' + questionId).then(function(response) {
                $state.go('home', {src : 'delete-question'});
            })
        };

        vm.ask = function() {

            vm.loading = true;

            var question = {
                title: vm.title,
                body: vm.content
            };

            $http.post('/api/questions', question).then(function (response) {
                vm.loading = false;
                $state.go('home', {src : 'ask'});
            }).catch(function(response) {

            });
        }
    }
})();