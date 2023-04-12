<?php
include('vendor/autoload.php');

use GLab\Support\AccessToken;

define('APP_ID', '6ogpInMa8RfA7cx8XRTa6zfazooqI4o7');
define('APP_SECRET', 'b790e865e0149bc7654fb5ac9d7d1a5f706e49acd9594c70d82cb036e210e23b');
define('SHORT_CODE', '21663042');

$token = AccessToken::get(); // Retrieve access token, request if not yet set 