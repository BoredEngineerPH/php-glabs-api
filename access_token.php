<?php
include('config.php');
include('vendor/autoload.php');

use GLab\Support\AccessToken;


$token = AccessToken::get(); // Retrieve access token, request if not yet set 