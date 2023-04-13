<?php
include('config.php');
include('vendor/autoload.php');

try {
    $lbs = new GLab\LBS();    
    $lbs->locate('0916xxxxxxx', 100);
} catch(GLab\HttpException $e) {
    echo 'Http: ' .$e->getMessage();
} catch(GLab\TokenException $e) {
    echo 'Http: ' .$e->getMessage();
}