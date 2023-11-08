<div>
    <br /> <br />
    <button class="btn btn-info mb-4" ng-click="backToMessageList()">Message List</button>
    <h5>Message Details</h5>
    <div class="border p-3 mt-3">
        <div class="d-flex justify-content-between border-bottom pb-3">
            <div class="d-flex align-items-center">
                <img class="mr-3" 
                        src="/img/profiles/{{ messages[0].Message.from_user_id != <?php echo $userData['id'] ?> ? messages[0].FromUser.UserProfile.image : messages[0].ToUser.UserProfile.image }}" 
                        onerror="this.src='/img/profiles/no-image.png';"
                        alt="Generic placeholder image" 
                        width="50"
                >
                <h5 class="ml-3">
                    {{ messages[0].Message.from_user_id == <?php echo $userData['id'] ?> ? messages[0].ToUser.UserProfile.name : messages[0].FromUser.UserProfile.name }}
                </h5>
            </div>
            <div class="d-flex">
                <span ng-show="searchOperationExecuted"><button class="btn btn-primary" ng-click="upSearchMsg()">Up</button></span>
                <span ng-show="searchOperationExecuted" class="mx-2 mt-2">{{ totalCntSearchMessage }}</span>
                <span ng-show="searchOperationExecuted"><button class="btn btn-primary" ng-click="downSearchMsg()">Down</button></span>
                <div class="ml-3">
                    <input 
                        type="text" 
                        placeholder="Search Message" 
                        class="form-control" 
                        ng-model="txtSearchMessage" 
                    />
                </div>
                <span><button class="btn btn-success ml-2" ng-click="searchMessages(messages[0].Message.from_user_id != <?php echo $userData['id'] ?> ? messages[0].Message.from_user_id : messages[0].Message.to_user_id)">Search</button></span>
            </div>
        </div>
        <div style="height: 500px;" class="d-flex align-items-end">
            <div class="d-flex flex-column w-100">
                <div class="messages-conversation-list" id="messages-conversation-list" style="overflow-y:auto;height:380px">
                    <ul class="list-group">
                        <li class="list-group-item mx-0 border-0" ng-show="hasMore">
                            <button class="btn btn-block btn-outline-secondary" ng-click="showMorePagination()">Show previous messages</button>
                        </li>
                        <li id="message-{{ x.Message.id }}" class="list-group-item mx-0 border-0 {{ x.Message.from_user_id != <?php echo $userData['id'] ?> ? 'd-flex justify-content-start' : 'd-flex justify-content-end' }}" 
                            ng-repeat="x in messages track by $index"
                        >
                            <div class="dropdown" ng-show="x.Message.from_user_id != <?php echo $userData['id'] ?>">
                                <a style="cursor:pointer" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <img src="/img/3dots.png" width="20" />
                                </a>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" ng-click="deleteMessage($event, 'for-you', x)" href="#">Delete for you</a>
                                    <a class="dropdown-item" ng-click="deleteMessage($event, 'for-everyone', x)" ng-show="x.Message.from_user_id == <?php echo $userData['id'] ?>" href="#">Delete for everyone</a>
                                </div>
                            </div>
                            <div class="bg-light rounded p-2 {{ x.Message.is_filtered && 'border border-success' }}">
                                <p>
                                    <span ng-show="!x.showFullMessage && !x.is_unsent">{{ x.Message.message.substring(0, 100) }} {{ x.is_unsent }}</span>
                                    <span ng-show="x.showFullMessage && !x.is_unsent">{{ x.Message.message }} {{ x.is_unsent }}</span>
                                    <span ng-show="x.is_unsent === true" class="p-2" style="font-weight:200;border: 0.5px solid gray;border-radius:10px"><i>Message Unsent</i></span>
                                    <a ng-show="x.Message.message.length > 100" ng-click="toggleMessage(x)" style="font-size:10px;color:blue;font-weight:400;cursor:pointer"> {{ x.showFullMessage ? 'Hide' : '...Show more' }}</a>
                                </p>
                                <span class="text-secondary mt-2" style="font-size:10px">{{ x.Message.created }}</span>
                            </div>
                            <div class="dropdown" ng-show="x.Message.from_user_id == <?php echo $userData['id'] ?>">
                                <a style="cursor:pointer" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <img src="/img/3dots.png" width="20" />
                                </a>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" ng-click="deleteMessage($event, 'for-you', x)" href="#">Delete for you</a>
                                    <a class="dropdown-item" ng-click="deleteMessage($event, 'for-everyone', x)" ng-show="x.Message.from_user_id == <?php echo $userData['id'] ?>" href="#">Delete for everyone</a>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="send-message-div d-flex w-100">
                    <div class="form-group mt-4 w-100 d-flex">
                        <textarea ng-model="textMessage" class="form-control" id="exampleFormControlTextarea1" rows="2"></textarea>
                        <button class="btn btn-success ml-2 px-4" ng-click="sendMessage()">Send</button>
                    </div>
                <div>
            </div>
        <div>
    </div>
</div>