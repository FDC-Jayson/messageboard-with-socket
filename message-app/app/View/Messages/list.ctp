    <div class="d-flex justify-content-end mt-4">
        <a class="btn btn-success" href="#!send">New Message</a>
    </div>
    <br />
    <ul class="list-group mx-0">
        <li 
            id="conversation-{{ x.Message.from_user_id != <?php echo $userData['id'] ?> ? x.Message.from_user_id : x.Message.to_user_id }}"
            class="list-group-item mx-0" 
            style="cursor:pointer" 
            ng-repeat="x in messages track by $index">
            <div class="d-flex justify-content-end position-absolute" style="right:2%;cursor:pointer">
                <button 
                    style="font-size:12px" 
                    class="btn btn-danger" 
                    ng-click="deleteConversation(x.Message.from_user_id != <?php echo $userData['id'] ?> ? x.Message.from_user_id : x.Message.to_user_id)"
                >Delete Message Conversation</button>
            </div>
            <div class="media d-flex align-items-center" ng-click="viewMessage(x.Message.from_user_id != <?php echo $userData['id'] ?> ? x.Message.from_user_id : x.Message.to_user_id)">
                <img class="mr-3" 
                     src="/img/profiles/{{ x.Message.from_user_id != <?php echo $userData['id'] ?> ? x.FromUser.UserProfile.image : x.ToUser.UserProfile.image }}" 
                     onerror="this.src='/img/profiles/no-image.png';"
                     alt="Generic placeholder image" 
                     width="100"
                >
                <div class="media-body">
                    <label style="cursor:pointer" class="mt-0 {{ x.Message.from_user_id != <?php echo $userData['id'] ?> && x.Message.is_read == 0 ? 'font-weight-bold' : '' }}">
                        {{ x.Message.from_user_id == '<?php echo $userData['id'] ?>' ? x.ToUser.UserProfile.name : x.FromUser.UserProfile.name }}
                    </label>
                    <!-- <p class="{{ x.Message.from_user_id != <?php echo $userData['id'] ?> && x.Message.is_read == 0 ? 'font-weight-bold' : 'text-secondary' }}">
                        {{ x.Message.from_user_id == <?php echo $userData['id'] ?> ? 'You: ' : '' }}
                        {{ x.Message.message }}
                    </p> -->
                    <p class="{{ x.Message.from_user_id != <?php echo $userData['id'] ?> && x.Message.is_read == 0 ? 'font-weight-bold' : 'text-secondary' }}">
                        {{ x.Message.from_user_id == <?php echo $userData['id'] ?> ? 'You: ' : '' }}
                        <span ng-show="!x.showFullMessage">{{ x.Message.message.substring(0, 100) }} </span>
                        <span ng-show="x.showFullMessage">{{ x.Message.message }}</span>
                        <a ng-show="x.Message.message.length > 100" ng-click="toggleMessage(x)" style="font-size:10px;color:blue;font-weight:400;cursor:pointer"> {{ x.showFullMessage ? 'Hide' : '...Show more' }}</a>
                    </p>
                    <div class="d-flex justify-content-end mt-3 pt-2">
                        <span class="text-secondary">{{ x.Message.created }}</span>
                    </div>
                </div>
            </div>
        </li>
    </ul>
    <div class="mt-5 d-flex justify-content-center" ng-show="totalMsg === 0">
        <h5>EMPTY MESSAGES</h5>
    </div>
    <div class="mt-4" ng-show="hasMore">
        <button class="btn btn-block btn-outline-secondary" ng-click="showMorePagination()">Show more</button>
    </div>