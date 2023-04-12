<?php
include('config.php');
include('vendor/autoload.php');

try {
    $sms = new GLab\SMS();
    $sms->to(['0916xxxxxxx'])
        ->to(['0908xxxxxxx'])
        ->message('Lorem ipsum dolor sit amet consectetur adipisicing elit. Ex tempora inventore illo cum eaque atque eos, earum eum iste autem rerum excepturi ducimus, veniam dolorum, voluptates nesciunt aperiam harum nam?', true)
        ->send(true)
} catch(GLab\SMSException $e) {
    echo 'SMS: ' .$e->getMessage();
}