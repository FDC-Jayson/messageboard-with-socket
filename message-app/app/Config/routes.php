<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
 
/** ROUTES */

	Router::connect('/', array('controller' => 'dashboard', 'action' => 'index'));
	Router::connect('/register', array('controller' => 'users', 'action' => 'register'));
	Router::connect('/login', array('controller' => 'users', 'action' => 'login'));
	Router::connect('/logout', array('controller' => 'users', 'action' => 'logout'));
	Router::connect('/registration-success-page', array('controller' => 'users', 'action' => 'registrationSuccessPage'));

	/** API Routes */
	Router::mapResources(['api']);
	Router::parseExtensions('json');

	Router::connect('/api/update-profile', array('controller' => 'api', 'action' => 'updateProfile'));
	Router::connect('/api/change-password', array('controller' => 'api', 'action' => 'changePassword'));
	Router::connect('/api/messages/:userId/:page', array('controller' => 'api', 'action' => 'messages'), array('pass' => array('userId', 'page')));
	Router::connect('/api/messages/details/:toUserId/:page', array('controller' => 'api', 'action' => 'messageDetails'), array('pass' => array('toUserId', 'page')));
	Router::connect('/api/messages-search-count/:toUserId/:search', array('controller' => 'api', 'action' => 'searchMessageCount'), array('pass' => array('toUserId', 'search')));
	Router::connect('/api/messages/send', array('controller' => 'api', 'action' => 'sendMessage'));
	Router::connect('/api/message-delete/:id/:userId', array('controller' => 'api', 'action' => 'deleteMessage'), array('pass' => array('id', 'userId'), 'id' => '[0-9]+'));
	Router::connect('/api/messages-delete-conversation/:toUserId', array('controller' => 'api', 'action' => 'deleteMessageConv'), array('pass' => array('toUserId'), 'toUserId' => '[0-9]+'));
	Router::connect('/api/search-contacts', array('controller' => 'api', 'action' => 'searchContacts'));

/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';
