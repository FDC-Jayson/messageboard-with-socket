<?php 

class ApiController extends AppController {
    public $uses = [
        'User',
        'UserProfile',
        'Message',
        'DeletedMessage'
    ];

    public function beforeFilter() {
        parent::beforeFilter();
        
        $this->autoRender = false;
        $this->response->type('json');
        $this->Auth->allow(
            'index', 
            'register'
        );
    }

    public function index() {
        $this->response->statusCode(200);
        return $this->response->body(json_encode(array(
            'code' => 200,
            'message' => 'Message Board Api'
        )));
    }
    
    public function register() {
        if(!$this->request->is('post')) {
            $this->response->statusCode(405);
            return $this->response->body(json_encode(array(
                'code' => 405,
                'message' => 'Method Not Allowed: The requested method is not allowed for this resource.'
            )));
        }

        $errors = [];

        $this->User->validate['name'] = array(
            'required' => array(
                'rule' => 'notBlank',
                'message' => 'Name is required'
            ),
            'length' => array(
                'rule' => array('lengthBetween', 5, 20),
                'message' => 'Name should be between 5 and 20 characters.',
            )
        );

        // Set the data for the User model
        $this->User->set($this->request->data);

        if ($this->User->validates()) {

            $this->User->create();

            if (!$this->User->save($this->request->data)) {
                $errors[] = 'Error creating user.';
            }
            
            $userId = $this->User->id;
            
            $this->UserProfile->create();
            
            if (!$this->UserProfile->save([
                'user_id' => $userId,
                'name' => $this->request->data['User']['name'],
            ])) {
                $errors[] = 'Error creating user profile.';
            }
            
        } else {
            $errors = $this->User->validationErrors;
        }

        if(!empty($errors)) {
            $this->response->statusCode(400);
            return $this->response->body(json_encode(array(
                'code' => 400,
                'errors' => $errors
            )));
        }
        
        /** Automatically Login and update the session for the created user  */
        if($this->Auth->login()) {}

        $this->response->statusCode(200);
        return $this->response->body(json_encode(array(
            'code' => 200,
            'message' => 'Successfully Registered.'
        )));
    }

    public function updateProfile() {
        
        if(!$this->request->is('post')) {
            $this->response->statusCode(405);
            return $this->response->body(json_encode(array(
                'code' => 405,
                'message' => 'Method Not Allowed: The requested method is not allowed for this resource.'
            )));
        }

        $data = $this->request->data;
       
        // Converting the birthdate string data to PHP date
        if ($data['UserProfile']['birthdate']) {
            $data['UserProfile']['birthdate'] = date("Y-m-d", strtotime($data['UserProfile']['birthdate']));
        }

        // Set the profile id to the currently logged-in user profile ID
        $userProfileId = $this->Auth->user('UserProfile.id');
        $data['UserProfile']['id'] = $userProfileId;

        // Append User data
        $data['User']['email'] = $data['UserProfile']['email'];
        $data['User']['id'] = $this->Auth->user('id');

        $errors = [];

        // Validate the new email address in the User model
        $this->User->set($data);

        // Check if the email is unique
        $newEmail = $data['User']['email'];
        $currentUserEmail = $this->Auth->user('email');
        
        // User email validation
        if ($newEmail !== $currentUserEmail && $this->User->hasAny(array('User.email' => $newEmail))) {
            $errors[] = 'This email address is already taken';
        }
        
        // Validate UserProfile data
        $this->UserProfile->set($data['UserProfile']);

        if (!$this->UserProfile->validates()) {
            $errors = array_merge($errors, $this->UserProfile->validationErrors);
        }
      
        // Continue with the rest of the profile update logic
        if (empty($errors)) { 
            
            // Upload image if a file was selected
            if (!empty($data['UserProfile']['imageFile']['name'])) {
                $file = $data['UserProfile']['imageFile'];
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);

                // Customize filename using user email address
                list($name, $domain) = explode('@', $this->Auth->user('email'));

                $customFileName = $name . "." . $extension;
                $path = 'img' . DS . 'profiles' . DS . $customFileName;

                // Move the uploaded file to the target directory
                if (move_uploaded_file($file['tmp_name'], WWW_ROOT . $path)) {
                    $data['UserProfile']['image'] = $customFileName;
                } else {
                    $errors[] = 'Error uploading the image.';
                }
            }

            // Update User profile and User email address
            if ($this->UserProfile->save($data)) {
                $newAuthData = array_merge(
                    $this->Auth->user(), 
                    $data
                );
                if ($newEmail !== $currentUserEmail) {
                    if ($this->User->save($data)) {
                        // Update email address
                        $newAuthData['email'] = $data['User']['email'];
                    }
                }
      
             
                // Manually Update the session logged in data
                unset($newAuthData['User']);
                $this->Auth->login($newAuthData);

            } else {
                $errors = 'Error saving profile data.';
            }
        }

        if (!empty($errors)) {
            $this->response->statusCode(400);
            return $this->response->body(json_encode(array(
                'code' => 400,
                'errors' => $errors
            )));
        }

        $this->response->statusCode(200);
        return $this->response->body(json_encode(array(
            'code' => 200,
            'message' => 'Profile Updated Successfully.'
        )));
    }

    public function changePassword() {

        if(!$this->request->is('post')) {
            $this->response->statusCode(405);
            return $this->response->body(json_encode(array(
                'code' => 405,
                'message' => 'Method Not Allowed: The requested method is not allowed for this resource.'
            )));
        }

        $errors = [];

        $data = $this->request->data;

        // Validate old password
        $user = $this->User->findById($this->Auth->user('id'));

        if (!$this->User->checkPassword($data['User']['old_password'], $user['User']['password'])) {
            $errors[] = 'Old password is incorrect.';
        } else {
            // Perform validation for new password and confirm password
            if ($data['User']['password'] !== $data['User']['confirm_password']) {
                $errors[] = 'Passwords do not match.';
            } else {

                $user['User']['password'] = $data['User']['password'];
                $user['User']['confirm_password'] = $data['User']['confirm_password'];
                
                if ($this->User->save($user)) {
                    // Password Successfully Updated
                } else {
                    $errors[] = 'Error updating credentials.';
                }
            }

        }

        if (!empty($errors)) {
            $this->response->statusCode(400);
            return $this->response->body(json_encode(array(
                'code' => 400,
                'errors' => $errors
            )));
        }

        $this->response->statusCode(200);
        return $this->response->body(json_encode(array(
            'code' => 200,
            'message' => 'User Password Updated Successfully'
        )));
        
    }

    /** Message API Action Below */
    public function messages($userId, $page = 1) {
        $this->autoRender = false;
    
        $limit = 10;
        $offset = ($page - 1) * $limit;
    
        // Define the custom query to get message IDs
        $messageIdsQuery = "SELECT m.id
            FROM messages AS m
            WHERE
                (m.from_user_id = $userId AND m.to_user_id = $userId)
                OR (
                    (m.from_user_id = $userId OR m.to_user_id = $userId)
                    AND m.id = (
                        SELECT MAX(id)
                        FROM messages AS mss
                        WHERE (
                            (mss.from_user_id = m.from_user_id AND mss.to_user_id = m.to_user_id)
                            OR (mss.from_user_id = m.to_user_id AND mss.to_user_id = m.from_user_id)
                        )
                        AND (
                            mss.id NOT IN (
                                SELECT dm.message_id
                                FROM deleted_messages AS dm
                                WHERE dm.user_id = $userId AND dm.message_id = mss.id
                            )
                            OR (
                                mss.id NOT IN (
                                    SELECT dm.message_id
                                    FROM deleted_messages AS dm
                                    WHERE dm.user_id = $userId
                                )
                                AND mss.id = (
                                    SELECT MAX(id)
                                    FROM messages AS mss2
                                    WHERE (
                                        (mss2.from_user_id = m.from_user_id AND mss2.to_user_id = m.to_user_id)
                                        OR (mss2.from_user_id = m.to_user_id AND mss2.to_user_id = m.from_user_id)
                                    )
                                    AND mss2.id NOT IN (
                                        SELECT dm2.message_id
                                        FROM deleted_messages AS dm2
                                        WHERE dm2.user_id = $userId AND dm2.message_id = mss2.id
                                    )
                                )
                            )
                        )
                    )
                )
            ORDER BY m.created DESC
            LIMIT $limit OFFSET $offset;";
    
        $messageIds = $this->Message->query($messageIdsQuery);
  
        // Extract message IDs from the result
        $messageIds = array_map(function ($result) {
            return $result['m']['id'];
        }, $messageIds);

        $options = array(
            'conditions' => array('Message.id' => $messageIds),
            'order' => 'Message.created DESC',
            'contain' => array(
                'FromUser' => array(
                    'UserProfile'
                ),
                'ToUser' => array(
                    'UserProfile'
                )
            )
        );
     
        // Find the associated messages with user profiles
        $messages = $this->Message->find('all', $options);
    
        $this->response->type('json');
    
        if (empty($messages)) {
            $this->response->statusCode(404); // No messages found
            $this->response->body(json_encode(array(
                'error' => 'No messages found',
            )));
        } else {
            $hasMore = count($messages) >= $limit;
            $this->response->statusCode(200);
            $this->response->body(json_encode(array(
                'hasMore' => $hasMore,
                'messages' => $messages,
            )));
        }
    }

    public function messageDetails($toUserId, $page = 1) {
        $this->autoRender = false;
        $limit = 10;
    
        // Get the currently logged-in user's ID
        $loggedInUserId = $this->Auth->user('id');
    
        // Add a condition to filter messages for the selected conversation user
        $conditions = array(
            'OR' => array(
                array('from_user_id' => $loggedInUserId, 'to_user_id' => $toUserId),
                array('from_user_id' => $toUserId, 'to_user_id' => $loggedInUserId)
            )
        );
    
        // Exclude messages found in deleted messages for the logged-in user
        $deletedMessages = $this->DeletedMessage->find('list', array(
            'conditions' => array('user_id' => $loggedInUserId),
            'fields' => array('message_id')
        ));
    
        if (!empty($deletedMessages)) {
            $conditions[] = array('NOT' => array('Message.id' => $deletedMessages));
        }
    
        $options = array(
            'conditions' => $conditions,
            'order' => 'Message.created DESC',
            'limit' => $limit,
            'page' => $page,
            'contain' => array(
                'FromUser' => array(
                    'UserProfile'
                ),
                'ToUser' => array(
                    'UserProfile'
                )
            )
        );
  
        // Find messages that meet the specified conditions
        $messages = $this->Message->find('all', $options);
    
        $this->response->type('json');
    
        if (empty($messages)) {
            $this->response->statusCode(404); // No messages found
            $this->response->body(json_encode(array(
                'error' => 'No messages found',
            )));
        } else {
            $hasMore = count($messages) >= $limit;
            $this->response->statusCode(200);
            $this->response->body(json_encode(array(
                'hasMore' => $hasMore,
                'messages' => $messages,
            )));
        }
    }

    public function searchMessageCount($toUserId, $search = null) {
        $this->autoRender = false;
    
        // Get the currently logged-in user's ID
        $loggedInUserId = $this->Auth->user('id');
    
        // Add a condition to filter messages for the selected conversation user
        $conditions = array(
            'OR' => array(
                array('from_user_id' => $loggedInUserId, 'to_user_id' => $toUserId),
                array('from_user_id' => $toUserId, 'to_user_id' => $loggedInUserId)
            )
        );
    
        // Exclude messages found in deleted messages for the logged-in user
        $deletedMessages = $this->DeletedMessage->find('list', array(
            'conditions' => array('user_id' => $loggedInUserId),
            'fields' => array('message_id')
        ));
    
        if (!empty($deletedMessages)) {
            $conditions[] = array('NOT' => array('Message.id' => $deletedMessages));
        }
    
        // Add a search condition if a search term is provided
        if (!empty($search)) {
            $searchCondition = array(
                'OR' => array(
                    'Message.message LIKE' => "%$search%",
                )
            );
            $conditions[] = $searchCondition;
        }
    
        $options = array(
            'conditions' => $conditions,
        );
    
        // Get the total count of messages that match the conditions
        $messageCount = $this->Message->find('count', $options);
    
        $this->response->type('json');
        $this->response->statusCode(200);
        $this->response->body(json_encode(array(
            'messageCount' => $messageCount, // Total message count
        )));
    }
    
    public function sendMessage() {
        $this->autoRender = false;
        
        if ($this->request->is('post')) {
            $data = $this->request->data;
    
            $messageData = array(
                'from_user_id' => $this->Auth->user('id'),
                'message' => $data['message']
            );
            
            $toUserIds = $data['userIds']; // Assuming you pass the target user IDs as an array
            
            $messages = array(); // Store the created messages

            foreach ($toUserIds as $toUserId) {
                $messageData['to_user_id'] = $toUserId;
                
                if ($this->Message->save($messageData)) {
                    // $message = $this->Message->findById($this->Message->id);
                    $message = $this->Message->find('first', array(
                        'conditions' => array(
                            'Message.id' => $this->Message->id
                        ),
                        'contain' => array(
                            'FromUser' => array(
                                'UserProfile'
                            ),
                            'ToUser' => array(
                                'UserProfile'
                            )
                        )
                    ));
                    $messages[] = $message;
                }
            }
            
            if (!empty($messages)) {
                $this->response->statusCode(200);
                $this->response->body(json_encode($messages));
            } else {
                $this->response->statusCode(400);
                $this->response->body(json_encode(array(
                    'error' => 'Messages could not be sent to any recipients',
                )));
            }
        } else {
            $this->response->statusCode(400);
            $this->response->body(json_encode(array(
                'error' => 'Invalid request method',
            )));
        }
    }
    
    public function deleteMessage($messageId, $userId) {
        $this->autoRender = false;
    
        $this->response->type('json');
    
        // Find the message by ID
        $message = $this->Message->findById($messageId);
    
        if (!empty($message)) {
            // Check if the message belongs to the logged-in user
            if ($message['Message']['from_user_id'] == $userId || $message['Message']['to_user_id'] == $userId) {
                // Create a record in the `deleted_messages` table to mark the message as deleted
                $deletedMessageData = array(
                    'user_id' => $userId, // Marked as deleted by the currently logged-in user
                    'message_id' => $messageId
                );
    
                // Assuming you have a DeletedMessage model
                $this->DeletedMessage->create();
                if ($this->DeletedMessage->save($deletedMessageData)) {
                    // Successfully marked the message as deleted
                    $this->response->statusCode(200);
                    $this->response->body(json_encode('Message marked as deleted'));
                } else {
                    $this->response->statusCode(400);
                    $this->response->body(json_encode(array(
                        'error' => 'Error marking the message as deleted',
                    )));
                }
            } else {
                $this->response->statusCode(403);
                $this->response->body(json_encode(array(
                    'error' => 'You do not have permission to delete this message',
                )));
            }
        } else {
            $this->response->statusCode(404);
            $this->response->body(json_encode(array(
                'error' => 'Message not found',
            )));
        }
    }
    
    public function deleteMessageConv($toUserId) { // Delete messages conversation
        $this->autoRender = false;

        // Get the ID of the currently logged-in user
        $loggedInUserId = $this->Auth->user('id');
    
        $this->response->type('json');
    
        // Check if the request is a POST request
        if ($this->request->is('post')) {
            // Define conditions to mark the conversation as deleted for the logged-in user
            $conditions = array(
                'OR' => array(
                    array(
                        'from_user_id' => $loggedInUserId,
                        'to_user_id' => $toUserId
                    ),
                    array(
                        'from_user_id' => $toUserId,
                        'to_user_id' => $loggedInUserId
                    )
                )
            );
    
            // Find messages that belong to the conversation but are not deleted
            $messages = $this->Message->find('all', array('conditions' => $conditions));
    
            if (!empty($messages)) {
                // Create records in the `deleted_messages` table to mark the conversation as deleted
                foreach ($messages as $message) {
                    $deletedMessageData = array(
                        'user_id' => $loggedInUserId, // Marked as deleted by the currently logged-in user
                        'message_id' => $message['Message']['id']
                    );
    
                    // Assuming you have a DeletedMessage model
                    $this->DeletedMessage->create();
                    if ($this->DeletedMessage->save($deletedMessageData)) {
                        // Successfully marked the message as deleted
                    }
                }
    
                $this->response->statusCode(200);
                $this->response->body(json_encode('Conversation marked as deleted'));
            } else {
                $this->response->statusCode(404);
                $this->response->body(json_encode(array(
                    'error' => 'No conversation found to mark as deleted',
                )));
            }
        } else {
            $this->response->statusCode(400);
            $this->response->body(json_encode(array(
                'error' => 'Invalid request method',
            )));
        }
    }

    public function searchContacts() {

        if ($this->request->is('post')) {
            $searchValue = $this->request->data['search']; // Assuming you are posting the search value as 'search'
    
            $currentUserId = $this->Auth->user('id'); // Get the ID of the currently logged-in user

            // Configure the conditions to search in both User and UserProfile
            $conditions = array(
                'OR' => array(
                    'User.email LIKE' => "%$searchValue%",
                    'UserProfile.name LIKE' => "%$searchValue%"
                ),
                'NOT' => array(
                    'User.id' => $currentUserId
                )
            );
    
            // Use the Containable behavior to fetch related UserProfile data
            $this->User->Behaviors->load('Containable');
    
            $contacts = $this->User->find('all', array(
                'conditions' => $conditions,
                'contain' => 'UserProfile'
            ));
    
            // Return the search results in JSON format
            $this->response->statusCode(200);
            return $this->response->body(json_encode(array(
                'contacts' => $contacts,
                '_serialize' => 'contacts'
            )));
        } else {
             $this->response->statusCode(400);
             return $this->response->body(json_encode(array(
                'error' => 'Invalid request',
                '_serialize' => 'error'
            )));
        }
    }
}
