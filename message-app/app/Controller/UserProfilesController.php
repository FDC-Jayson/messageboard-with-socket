<?php 

class UserProfilesController extends AppController {
    public $name = 'UserProfiles';

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function details() {
        // Retrieve the logged-in user's data
        $userData = $this->Auth->user();

        // Set the user data for the view
        $this->set('userData', $userData);
    }
    

    public function edit() {
        // Load user profile details for the form
        $userProfile = $this->UserProfile->findByUserId($this->Auth->user('id'));
        $userProfile['UserProfile']['email'] = $this->Auth->user('email');
        $this->request->data = $userProfile;
    }
    
    
    
    
    
    
    

    

}
