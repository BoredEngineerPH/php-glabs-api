<?php
include('config.php');
include('vendor/autoload.php');
$rewards = 'AbCkxKYid_F_p-JSgTejow';
$promo = 'Load 1';
try {
    $sms = new GLab\Load();
    $sms->to(['0916xxxxxxx'])
        ->send($rewards, $promo);
} catch(GLab\LoadException $e) {
    echo 'Load: ' .$e->getMessage();
}