<?php

$this->create('proton_oauth', 'init')->get()->action('OCA\Proton\OAuth', 'OAuth');
$this->create('proton_logout', 'logout')->get()->action('OCA\Proton\User', 'logoutController');
?>