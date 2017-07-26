(function() {

    'use strict';

    angular
        .module("studyAgain")
        .controller('QuestionController', QuestionController);

    function QuestionController($http, $state, $stateParams, $rootScope) {
        var vm = this;

        vm.loadDetails = function() {
            vm.loading = true;

            $http.get('api/questions/' + $stateParams.questionId + "?page=" + $stateParams.page).then(function (response) {
                vm.id = response.data.id;
                vm.title = response.data.title;
                vm.content = response.data.body;
                vm.tags = response.data.tags;
                vm.author = response.data.author;

                vm.updated_at = response.data.updated_at;
                vm.editable = response.data.editable;
                vm.status = response.data.status;
                vm.voteCounts = response.data.voteCounts;
                vm.answerCounts = response.data.answerCounts;
                vm.voteStatus = response.data.voteStatus;

                vm.answers = response.data.answers.data;
                vm.lastPage = response.data.answers.last_page;
                vm.currentPage = response.data.answers.current_page;

                if (vm.currentPage > vm.lastPage && vm.lastPage !== 0) {
                    $state.go('404Error', {});
                }
                else {

                    var start = vm.currentPage - (vm.lastPage - vm.currentPage < 2 ? (4 - (vm.lastPage - vm.currentPage)) : 2);
                    if (start < 0) {
                        start = 1;
                    }
                    vm.pages = [];
                    for (var i = start; i < start + 5; i++) {
                        if (i > vm.lastPage) {
                            break;
                        }
                        vm.pages.push(i);
                    }

                    vm.loading = false;
                }
            }).catch(function(response) {
            });

        };

        vm.edit = function() {
            var question = {
                title: vm.title,
                body: vm.content,
                tags: vm.tags
            };
            $http.put('api/questions/' + $stateParams.questionId, question).then(function (response) {
                $state.go('questionsDetail', {questionId : $stateParams.questionId});
            }).catch(function (response) {
                $state.go('404Error', {});
            })
        };

        if ($state.is("questionsEdit")) {
            $http.get('api/questions/' + $stateParams.questionId).then(function (response) {
                vm.title = response.data.title;
                vm.content = response.data.body;
                var temp = response.data.tags;
                vm.tags = "";
                for (var i = 0; i < temp.length; i++) {
                    if (i !== 0) {
                        vm.tags += ", ";
                    }
                    vm.tags += temp[i]['name'];
                }
                vm.pattern = '^(?:[^,]+(?:,[^,]+){0,4})?$';
            }).catch(function(response) {
                $state.go('404Error', {});
            });

        } else if ($state.is("questionsDetail")) {
            vm.loadDetails();
        } else if($state.is("ask")) {
            vm.pattern = '^(?:[^,]+(?:,[^,]+){0,4})?$';
        }

        vm.delete = function(questionId) {
            $http.delete('api/questions/' + questionId).then(function(response) {
                $state.go('home', {src : 'delete-question'});
                $rootScope.$broadcast('questionDeleted');
            }).catch(function(response) {

            });
        };

        vm.ask = function() {

            vm.loading = true;

            var question = {
                title: vm.title,
                body: vm.content,
                tags: vm.tags
            };

            $http.post('/api/questions', question).then(function (response) {
                vm.loading = false;
                $state.go('home', {src : 'ask'});
            }).catch(function(response) {

            });
        };

        vm.answer = function() {
            var answer = {
                body: vm.answerContent
            };

            $http.post('/api/questions/' + vm.id + '/answers', answer).then(function (response) {
                vm.answerContent = "";
                vm.loadDetails();
            }).catch(function(response) {

            });
        };

        vm.upVote = function() {
            if (vm.voteStatus !== 'up') {
                $http.post('/api/questions/' + vm.id + '/upVote').then(function (response) {
                    vm.loadDetails();
                }).catch(function(response) {

                });
            }
        };

        vm.downVote = function() {
            if (vm.voteStatus !== 'down') {
                $http.post('/api/questions/' + vm.id + '/downVote').then(function (response) {
                    vm.loadDetails();
                }).catch(function(response) {

                });
            }
        };

        $rootScope.$on('answerChanged', function () {
            vm.loadDetails();
        });
    }
})();