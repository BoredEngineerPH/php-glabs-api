<?php
include('config.php');
include('vendor/autoload.php');

try {
    $sms = new GLab\SMS();
    $sms->set_app_id(APP_ID)
        ->set_app_secret(APP_SECRET)    
        ->set_short_code(SHORT_CODE);
    
    $sms->getAccessToken(); // Pre-request access token

    // Sending to multiple recipient
    $sms->address('0916xxxxxxx')
        ->address('0908xxxxxxx');
    
    // This message will be split since it exceed 160 max character limit
    $sms->message('Lorem ipsum dolor sit amet consectetur adipisicing elit. Ex tempora inventore illo cum eaque atque eos, earum eum iste autem rerum excepturi ducimus, veniam dolorum, voluptates nesciunt aperiam harum nam?') 
    $sms->send();
} catch(GLab\SMSException $e) {
    echo 'SMS: ' .$e->getMessage();
} catch(GLab\HttpException $e) {
    echo 'Http: ' .$e->getMessage();
} catch(GLab\TokenException $e) {
    echo 'Http: ' .$e->getMessage();
}