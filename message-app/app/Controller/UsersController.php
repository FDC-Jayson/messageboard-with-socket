<?php 

class UsersController extends AppController {
    public $name = 'Users';

    public function beforeFilter() {
        parent::beforeFilter();

        $restrictedActions = [
            'login',
            'register',
        ];        

        if ($this->Auth->user() && in_array($this->action, $restrictedActions)) {
            $this->redirect('/');
        }
        
        $this->Auth->allow('register');
    }
    public function register() {
        // Render the View
    }
    public function changePassword() {
        // Render View
    }
    public function registrationSuccessPage() {
        // Render View
    }
    public function login() {
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                // Log in the user
                $this->User->id = $this->Auth->user('id');
                $this->User->saveField('last_login_at', date('Y-m-d H:i:s'));

                $this->Session->setFlash('Login successful.', 'success');
                return $this->redirect($this->Auth->redirect());
            } else {
                $this->Session->setFlash('Invalid email or password. Please try again.', 'flash');
            }
        }
    }
    public function logout() {
        $this->redirect($this->Auth->logout());
    }


}
