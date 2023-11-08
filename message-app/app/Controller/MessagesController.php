<?php 

class MessagesController extends AppController {

    public function index() {
        // Auth user data
        $userData = $this->Auth->user();

        // Pass auth user data to rendered view
        $this->set(compact('userData'));
    }

    public function list() {
        $this->layout = false;
        // Auth user data
        $userData = $this->Auth->user();

        // Pass auth user data to rendered view
        $this->set(compact('userData'));
    }
    
    public function details() {
        $this->layout = false;
        // Auth user data
        $userData = $this->Auth->user();

        // Pass auth user data to rendered view
        $this->set(compact('userData'));
    }

    public function send() {
        $this->layout = false;
        // Auth user data
        $userData = $this->Auth->user();

        // Pass auth user data to rendered view
        $this->set(compact('userData'));
    }
}

