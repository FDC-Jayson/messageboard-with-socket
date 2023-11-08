
const socket = io('http://localhost:3000');
const app = angular.module("messageBoard", ["ngRoute"]);

/** Angular Configuration */
app.config(function($routeProvider) {
    $routeProvider
    .when("/", {
        templateUrl: "/messages/list",
        controller: "listCtrl"
    })
    .when("/send", {
        templateUrl: "/messages/send",
        controller: "sendCtrl"
    })
    .when("/details/:toUserId", {
        templateUrl: "/messages/details",
        controller: "detailsCtrl"
    })
    .otherwise({ redirectTo: "/" });
});

/** Main Controller */
app.controller("mainCtrl", function($scope) {
    // Function to toggle "Show more" state for a message
    $scope.toggleMessage = function(message) {
        message.showFullMessage = !message.showFullMessage;
    };
});
/** List Controller */
app.controller("listCtrl", function($scope, $http, $httpParamSerializer, $location, $timeout) {
    $scope.messages = [];
    $scope.totalMsg = 0;
    $scope.page = 1;
    $scope.hasMore = false;
    $scope.getMessages = function() {
        $http({
            method: "GET",
            url: "/api/messages/" + $scope.userId + "/" + $scope.page
        }).then(function mySuccess(response) {

            response.data?.messages.map(item => {
                $scope.messages.push(item)
            });
            $scope.totalMsg += response.data?.messages.length;
            $scope.hasMore = response.data.hasMore;

        }, function myError(response) { });
    }
    $scope.viewMessage = function(id) {
        $location.path('/details/'+id);
    }
    $scope.showMorePagination = function() {
        $scope.page++;
        $scope.getMessages();
    }
    $scope.deleteConversation = function(toUserId) {

        if(!confirm("Are you sure to delete this conversation?")) {
            return false;
        }

        $http.post("/api/messages-delete-conversation/" + toUserId, {
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        })
        .then(
            function(response){
                // Assuming response.data.success is true when the deletion is successful
                if (response.data) {
                    // Find the index of the conversation to remove
                    const index = $scope.messages.findIndex(x => x.Message.from_user_id == toUserId || x.Message.to_user_id == toUserId);
                    
                    if (index !== -1) {
                        const conversationId = `conversation-${toUserId}`;
                        const elementToRemove = document.getElementById(conversationId);
                        if(elementToRemove) {

                            $scope.messages.splice(index, 1);
                            $scope.$apply();

                            // Use jQuery to fade out and remove the conversation element
                            $(elementToRemove).fadeOut('fast', function() {
                                console.log("Message removed");
                            });
                        }
       
                    }
                }
            },
            function(response){ }
        );

    }
    $scope.socketListen = function() {
        $timeout(function() {
            socket.on('message', (data) => {
                console.log("messages-1", $scope.messages);
                // Verify Message if its for you or it's yours
                if (data.Message.from_user_id === $scope.userId || data.Message.to_user_id === $scope.userId) {
                    console.log("socket data", data)
                    // Check if the conversation exist
                    var checkConvExist = $scope.messages.find((row) => {
                        if(
                            (row.Message.from_user_id === data.Message.from_user_id || row.Message.from_user_id === data.Message.to_user_id) 
                            && 
                            (row.Message.to_user_id === data.Message.from_user_id || row.Message.to_user_id === data.Message.to_user_id)
                        ) {
                            return row;
                        } else {
                            return false;
                        }
                    });

                    if(!checkConvExist) {
                        // Push new conversation
                        $scope.messages.unshift(data);
                    } else {
                        // Update conversation with the latest message
                        checkConvExist.Message = data.Message;
                        checkConvExist.FromUser = data.FromUser;
                        checkConvExist.ToUser = data.ToUser;
                    }

                    $scope.$apply(); // Manually trigger 
                }
            });
        }, 2000);
    }
    $scope.$on('$destroy', function() {
        socket.removeListener('message');
    });
    $scope.getMessages();
    $scope.socketListen();
});
/** Send Controller */
app.controller("sendCtrl", function($scope, $http, $httpParamSerializer, $location) {
    $scope.sendMessage = function() {
        // Validation
        if(!$scope.selectedRecipient) {
            alert("Recipient is required.");
            return;
        }
        if(!$scope.textMessage) {
            alert("Message must not be empty.")
            return;
        }

        var postData = {
            userIds: $scope.selectedRecipient,
            message: $scope.textMessage
        };

        $http.post(
            "/api/messages/send", 
            $.param(postData), {
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        })
        .then(
            function(response){
                 // Perform Socket Emit Message
                socket.emit('message', response.data[0]);
                // Redirect to the root URL
                alert("The message has been sent successfully.")
                $location.path('/');
            },
            function(response){ }
        );

    }
    $scope.backToMessageList = function() {
        // Redirect to the root URL
        $location.path('/');
    }
});
/** Details Controller */
app.controller("detailsCtrl", function($scope, $http, $httpParamSerializer, $routeParams, $timeout, $location) {

    var toUserId = parseInt($routeParams.toUserId);

    $scope.messages = [];
    $scope.newMessagesData = [];
    $scope.totalMsg = 0;
    $scope.page = 1;
    $scope.hasMore = false;
    $scope.txtSearchMessage = "";
    $scope.totalCntSearchMessage = 0;
    $scope.totalCntSearchMessageDefault = 0;
    $scope.searchOperationExecuted = false;

    $scope.getMessages = function() {
        $http({
            method: "GET",
            url: "/api/messages/details/" + toUserId + "/" + $scope.page
        }).then(function mySuccess(response) {
            // Sort the messages based on the 'created' property (message timestamp)
            response.data?.messages.sort(function(a, b) {
                // Assuming the 'created' property is a valid date or timestamp
                response.data?.messages.sort((a, b) => ($scope.page > 1) ? (new Date(b.Message.created) - new Date(a.Message.created)) : (new Date(a.Message.created) - new Date(b.Message.created)));
            });
            // Remove the old message from newMessagesData properties
            $scope.newMessagesData = [];
            // Push the sorted messages into the $scope.messages array
            response.data?.messages.forEach(function(item) {
                $scope.messages.unshift(item);
                $scope.newMessagesData.push(item)
            });
            
            $scope.totalMsg += response.data?.messages.length;
            $scope.hasMore = response.data.hasMore;

            // By default auto scroll the conversation up to the latest message
            if($scope.page === 1) {
                $scope.scrollToBottom();
            }
            
        }, function myError(response) {
            console.log("response", response);
        });
    }
    $scope.sendMessage = function() {
        if(!$scope.textMessage) {
            alert("Message must not be empty.")
            return;
        }
        var postData = {
            userIds: [toUserId],
            message: $scope.textMessage
        };

        $http.post(
            "/api/messages/send", 
            $.param(postData), {
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        })
        .then(
            function(response){
                // Perform Socket Emit Message
                socket.emit('message', response.data[0]);
                // Clear input message text
                $scope.textMessage = '';
            },
            function(response){ }
        );
    }
    $scope.backToMessageList = function() {
        // Redirect to the root URL
        $location.path('/');
    }
    $scope.scrollToBottom = function() {
        $timeout(function() {
            var messageContainer = document.getElementById('messages-conversation-list');
            if (messageContainer) {
                messageContainer.scrollTop = messageContainer.scrollHeight;
            }
        });
    };
    $scope.showMorePagination = function() {
        $scope.page++;
        $scope.getMessages();
    }
    $scope.deleteMessage = function(event, deleteType, data) {
        event.preventDefault();

        var userIds = [];

        if(!confirm("Are you sure to delete this message?")) {
            return false;
        }
        if (deleteType == "for-you") {
            userIds.push($scope.userId);
        } else if (deleteType == "for-everyone") {
            userIds = [data.Message.from_user_id, data.Message.to_user_id];
        }

        userIds.forEach(userId => {
            
            $http.post("api/message-delete/" + data.Message.id + "/" + userId, {
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            })
            .then(
                function(response){
                    // Assuming response.data.success is true when the deletion is successful
                    if (response.data) {
                        // Find the conversation element to remove
                        var element = document.getElementById("message-" + data.Message.id);
                        if (element) {
                            // Use jQuery to fade out and remove the element
                            $(element).fadeOut('fast', function () {
                                // Remove the element from the array
                                const index = $scope.messages.findIndex(x => x.Message.id === data.Message.id);

                                if (index !== -1) {
                                    $scope.messages.splice(index, 1);
                                    $scope.$apply();
                                }
                                // Socket emit remove message for everyone
                                if(deleteType == "for-everyone") { 
                                    socket.emit('remove_message', data);
                                }
                                
                            });
                        }
                    }
                },
                function(response){ }
            );
        });
    }
    $scope.searchMessages = function(toUserId) {
        // Retrieve the search query from the input field
        var searchQuery = $scope.txtSearchMessage;
        if(searchQuery == '') {
            $scope.totalCntSearchMessage = 0;
            $scope.searchOperationExecuted = false;
            $scope.messages = $scope.messages.map(function(data) {
                // Modify the message properties as needed
                data.Message.is_filtered = false; // Update property1
                data.Message.is_filtered = false; // Update property2
                // ... update other properties here ...
                return data;
            });
            return false;
        }

        $http.post("api/messages-search-count/" + toUserId + "/" + searchQuery, {
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        })
        .then(
            function(response){
                $scope.totalCntSearchMessage = response.data?.messageCount;
                $scope.totalCntSearchMessageDefault = response.data?.messageCount;
    
                if(response.data?.messageCount != 0 ) {
                    // Create a regex pattern for the search query (case-insensitive)
                    var pattern = new RegExp(searchQuery, 'i');
    
                    // Use regex to filter messages based on the pattern
                    var filteredMessages = $scope.messages.filter(function(data) {
                        return pattern.test(data.Message.message);
                    });
    
                    if(filteredMessages.length === 0 && response.data?.messageCount != 0) {
                            // Auto paginate to find more message
                            $scope.showMorePagination();
                            $timeout(function() {
                                $scope.searchMessages(toUserId)
                            });
                    } else {
                        filteredMessages.map(data => {
                            data.Message.is_filtered = true;
                        });
                        // Auto scroll to the search found message
                        $scope.scrollToLastFilteredMessage();
                    }
                }
            },
            function(response){ }
        );

        $scope.searchOperationExecuted = true;

    };
    $scope.upSearchMsg = function() {
        // Trap if no more message found.
        if($scope.totalCntSearchMessage === 1) {
            return false;
        }
        // Count the current total message found with column is filtered
        var countFilteredMsg = $scope.messages.filter(data => data.Message.is_filtered === true).length;

        // Condition compare total current filtered data to the total count serach filter
        if(countFilteredMsg < $scope.totalCntSearchMessageDefault) {
            // Auto paginate previous conversation
            $scope.showMorePagination();
        }
        // Execute the following codes inside the timeout in order to get the updated messages data
        $timeout(function() {
            // Create a regex pattern for the search query (case-insensitive)
            var pattern = new RegExp($scope.txtSearchMessage, 'i');

            // Use regex to filter messages based on the pattern
            var filteredNewMsg = $scope.newMessagesData.filter(function(data) {
                return pattern.test(data.Message.message);
            });

            if(filteredNewMsg.length > 0) {
                $scope.messages.map(data => {
                    filteredNewMsg.map(newMsg => {
                        if(newMsg.Message.id === data.Message.id) {
                            data.Message.is_filtered = true;
                        }
                    });
                });
                // Auto scroll to the search found message
                $scope.scrollToNextFilteredMessage();
                $scope.totalCntSearchMessage--;
            } else {
                $scope.upSearchMsg();
            }
        }, countFilteredMsg < $scope.totalCntSearchMessageDefault ? 500 : 0);

    }
    $scope.downSearchMsg = function() {
        if($scope.totalCntSearchMessage >= $scope.totalCntSearchMessageDefault) {
            return false;
        }

        $scope.scrollToPreviousFilteredMessage();
        $scope.totalCntSearchMessage++;
    }
    $scope.currentFilteredMessageIndex = -1; // Initialize to -1 to ensure it starts from the first message
    $scope.scrollToNextFilteredMessage = function() {
        const filteredMessages = $scope.messages.filter(function(data) {
            return data.Message.is_filtered === true;
        });

        if (filteredMessages.length > 0) {
            $scope.currentFilteredMessageIndex++;
            if ($scope.currentFilteredMessageIndex >= filteredMessages.length) {
                $scope.currentFilteredMessageIndex = 0; // Wrap around to the first message
            }
            
            // Scroll to the next filtered message
            var currScrollActiveKey = 0;
            filteredMessages.map((msg, key) => {
                if(msg.is_scroll_active) {
                    currScrollActiveKey = key;
                }
            });
            
            filteredMessages.map(msg => msg.is_scroll_active = false);
            currScrollActiveKey = currScrollActiveKey != 0 ? currScrollActiveKey - 1 : currScrollActiveKey;
            filteredMessages[currScrollActiveKey].is_scroll_active = true;

            // console.log("filteredMessages", filteredMessages)
            $scope.scrollToFilteredMessage(filteredMessages && filteredMessages[currScrollActiveKey])
        }
    };
    $scope.scrollToPreviousFilteredMessage = function() {
        const filteredMessages = $scope.messages.filter(function(data) {
            return data.Message.is_filtered === true;
        });

        if (filteredMessages.length > 0) {
            $scope.currentFilteredMessageIndex--;
            if ($scope.currentFilteredMessageIndex < 0) {
                $scope.currentFilteredMessageIndex = filteredMessages.length - 1; // Wrap around to the last message
            }

            // Scroll to the previous filtered message
            var currScrollActiveKey = 0;
            filteredMessages.map((msg, key) => {
                if(msg.is_scroll_active) {
                    currScrollActiveKey = key;
                    $scope.scrollToFilteredMessage(msg)
                }
                msg.is_scroll_active = false;
            });
         
            currScrollActiveKey += 1;
            filteredMessages[currScrollActiveKey].is_scroll_active = true;

            if(filteredMessages[currScrollActiveKey]) {
                $scope.scrollToFilteredMessage(currScrollActiveKey && filteredMessages[currScrollActiveKey]);
            }

            // console.log("filteredMessges", filteredMessages)
        }
    };
    $scope.scrollToFilteredMessage = function(filteredMessages) {
        var container = document.getElementById('messages-conversation-list'); // Replace with your container's ID
        if (filteredMessages) {
            // Scroll to the last filtered message
            var lastMessageElement = document.getElementById('message-' + filteredMessages.Message.id);
            
            if (lastMessageElement) {
                var scrollPosition = lastMessageElement.offsetTop - container.offsetTop;
                container.scrollTop = scrollPosition;
            }
        }
    }
    $scope.scrollToLastFilteredMessage = function() {
        var container = document.getElementById('messages-conversation-list'); // Replace with your container's ID

        if (container) {
            // Find the last message element with is_filtered === true
            var lastFilteredMessage = null;
            for (var i = $scope.messages.length - 1; i >= 0; i--) {
                if ($scope.messages[i].Message.is_filtered) {
                    lastFilteredMessage = $scope.messages[i];
                    break;
                }
            }

            if (lastFilteredMessage) {
                // Scroll to the last filtered message
                var lastMessageElement = document.getElementById('message-' + lastFilteredMessage.Message.id);
                
                if (lastMessageElement) {
                    var scrollPosition = lastMessageElement.offsetTop - container.offsetTop;
                    container.scrollTop = scrollPosition;
                }
            }
        }
    };
    $scope.socketListen = function() {
        // Socket listen new messages
        socket.on('message', (data) => {
            // Verify Message if its for you or it's yours
            if (data.Message.from_user_id === $scope.userId || data.Message.to_user_id === $scope.userId) {
                // Check if the message is associated to this conversation
                console.log("new message associated for you")
                if(data.Message.from_user_id === toUserId || data.Message.to_user_id === toUserId) {
                    console.log("it's associated to this conversation")
                    $scope.messages.push(data);
                    // Manually trigger a digest cycle
                    $scope.$apply();

                
                    var messageContainer = document.getElementById('messages-conversation-list');
                    var currentScrollPosition = messageContainer.scrollTop;
                    var maxScrollHeight = messageContainer.scrollHeight - messageContainer.clientHeight;

                    if (maxScrollHeight - currentScrollPosition < 300) {
                        $scope.scrollToBottom();
                    }
        
                }
            }
        });
        // Socket listen deleted messages
        socket.on('remove_message', (data) => {
            // Verify Message if its for you or it's yours
            if (data.Message.from_user_id === $scope.userId || data.Message.to_user_id === $scope.userId) {
                // Check if the message is associated to this conversation
                if(data.Message.from_user_id === toUserId || data.Message.to_user_id === toUserId) {
                    // check if the deleted message is exist in the current conversation
                    $checkMessage = $scope.messages.find(x => x.Message.id === data.Message.id);

                    if($checkMessage) {
                        $checkMessage.is_unsent = true;
                    }
                    // Manually trigger a digest cycle
                    $scope.$apply();

                }
            }
        });
    }
    $scope.getMessages();
    $scope.socketListen();
});
/** Custom Directives */
app.directive('select2', function($http, $httpParamSerializer) {
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {
            element.select2({
                width: 'resolve', // need to override the changed default
                templateResult: formatState,
                placeholder: "Search for a recipient"
            });

            element.on('select2:select', function(e) {
                element.val([e.params.data.id]).trigger('change');
            });

            element.on('select2:open', function() {
                element.siblings('.select2-container').find('.select2-search__field').on('input', function() {
                    var searchValue = $(this).val();

                    $http.post("/api/search-contacts", $httpParamSerializer({ search: searchValue }), {
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                    })
                    .then(
                        function(response){
                            scope.contacts = response.data.contacts;

                            // Clear existing options
                            element.empty();

                            // Append new options from the fetched data
                            response.data.contacts.forEach(function (contact) {
                                var option = new Option(contact.UserProfile.name, contact.User.id, false, false);
                                option.dataset.imageUrl = contact.UserProfile.image;
                                element.append(option);
                            });
                        },
                        function(response){ }
                    );
                });
            });
        }
    };
});
/** Other Scripts */
function formatState(state) {
    if (!state.id) {
        return state.text;
    }

    var imageUrl = state.element.dataset.imageUrl || 'no-image.png';
    imageUrl = '/img/profiles/' + imageUrl;

    var $state = $('<span class="mr-2"><img src="' + imageUrl + '" class="image" width="40" onerror="replaceWithError(this)" /> ' + state.text + '</span>');

    return $state;
}
window.replaceWithError = function(imageElement) {
    imageElement.src = 'img/profiles/no-image.png';
};