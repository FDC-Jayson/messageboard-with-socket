<?php
/**
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 */

if (!Configure::read('debug')):
	throw new NotFoundException();
endif;

App::uses('Debugger', 'Utility');

$loguser = $this->Session->read('Auth.User');
?>
<!-- app/View/Users/dashboard.ctp -->

<h2>Dashboard</h2>
<?php if (!empty($loguser)): ?>
    <p>Welcome, <?= h($loguser['email']); ?>!</p>
    <!-- Add any content you want to display on the dashboard here -->
<?php else: ?>
    <p>You are not logged in.</p>
<?php endif; ?>
