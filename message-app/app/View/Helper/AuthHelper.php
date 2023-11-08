<?php 

// app/View/Helper/AuthHelper.php

App::uses('AppHelper', 'View/Helper');

class AuthHelper extends AppHelper {
    
    public $helpers = array('Html');
    
    public function user() {
        $user = $this->Html->tag('strong', $this->Session->read('Auth.User.email'));
        return $user;
    }
}
