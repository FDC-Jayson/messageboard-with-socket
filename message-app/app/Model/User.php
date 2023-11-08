<?php

class User extends AppModel {
    public $name = 'User';

    // Define the association with the UserProfile model
    public $hasOne = array(
        'UserProfile' => array(
            'className' => 'UserProfile',
            'foreignKey' => 'user_id',
        )
    );
    // Table columns validation
    public $validate = array(
        'name' => array(
            'required' => array(
                'rule' => 'notBlank',
                'message' => 'Name is required'
            )
        ),
        'email' => array(
            'required' => array(
                'rule' => 'notBlank',
                'message' => 'An email address is required'
            ),
            'unique' => array(
                'rule' => 'isUnique',
                'message' => 'This email address is already taken'
            ),
            'email' => array(
                'rule' => array('email'),
                'message' => 'Please enter a valid email address'
            )
        ),
        'password' => array(
            'required' => array(
                'rule' => 'notBlank',
                'message' => 'Password is required'
            ),
            'match' => array(
                'rule' => 'validatePassword',
                'message' => 'Passwords do not match'
            )
        ),
    );

    public function beforeSave($options = array()) {
        // Perform hashing of the password
        if (isset($this->data[$this->alias]['password'])) {
            $this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
        }

        return true;
    }
    
    // Custom validation method for password matching
    public function validatePassword($data) {
        if(isset($this->data[$this->alias]['confirm_password']) && isset($this->data[$this->alias]['password'])) {
            return $this->data[$this->alias]['password'] === $this->data[$this->alias]['confirm_password'];
        }
    }
    
    public function checkPassword($oldPassword, $storedPassword) {
        // Hash the old password and compare it with the stored password
        return AuthComponent::password($oldPassword) === $storedPassword;
    }    
}
