<!-- app/View/Users/dashboard.ctp -->

<h2>Dashboard</h2>
<?php if (!empty($userData)): ?>
    <p>Welcome, <?= h($userData['email']); ?>!</p>
    <!-- Add any content you want to display on the dashboard here -->
<?php else: ?>
    <p>You are not logged in.</p>
<?php endif; ?>

<!-- Add this to your CakePHP view where you want to display messages -->
<div id="message-list"></div>


<script src="https://cdn.socket.io/4.6.0/socket.io.min.js" integrity="sha384-c79GN5VsunZvi+Q/WObgk2in0CbZsHnjEqvFxC5DxHn9lTfNce2WW6h2pH6u/kF+" crossorigin="anonymous"></script>
<script>
    var socket = io('http://localhost:3000');

    socket.on('message', (data) => {
        // Append the new message to the message list
        $('#message-list').append('<div>' + data + '</div>');
    });
</script>



