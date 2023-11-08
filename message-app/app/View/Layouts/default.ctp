<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

?>
<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo "Message Board" ?> :
		<?php echo $this->fetch('title'); ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('cake.generic');
		echo $this->Html->css('bootstrap.min');
		echo $this->Html->css('jquery-ui');
		echo $this->Html->css('select2.min.css');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	
		echo $this->Html->script('/js/jquery.min');
		echo $this->Html->script('/js/jquery.ui');
		echo $this->Html->script('/js/proper.min');
		echo $this->Html->script('/js/bootstrap.min');
		echo $this->Html->script('/js/select2.min');
		
	?>
</head>
<body>
	<div id="container">
		<div id="header" class="navbar navbar-expand-lg navbar-dark bg-dark">
			<div class="container">
				<?php
					// Wrap the content in an anchor element with the URL to the default page
					echo $this->Html->link(
						'<h1 class="navbar-brand">Message Board App</h1>',
						'/',
						array('escape' => false)
					);

					// Display the user session header element if the user is authenticated
					echo $this->element('user_session_header', array('loguser' => $this->Session->read('Auth.User')));
				?>
			</div>
		</div>
		<div id="content" class="container">

			<?php echo $this->Flash->render(); ?>

			<?php echo $this->fetch('content'); ?>
			
		</div>
		
		<div id="footer" style="display:none">
				<!-- footer element -->
		</div>
	</div>

</body>
</html>
