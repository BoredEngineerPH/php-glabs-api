<?php namespace GLab;

use GLab\Support\AccessToken;

if(!function_exists('curl_init')) throw new BadFunctionCallException('cURL not enable.');
if(!defined('APP_ID')) throw new \Exception('APP ID not set.');
if(!defined('APP_SECRET')) throw new \Exception('APP SECRET not set.');
if(!defined('SHORT_CODE')) throw new \Exception('SHORT CODE not set.');


class SMSException extends \Exception{}

/**
*  SMS Service Api
*
* Short Message Service (SMS) enables your application or service to send and receive secure, 
* targeted text messages and alerts to your Globe / TM and other telco subscribers.
* 
* Note: All API calls must include the access_token as one of the Universal Resource Identifier (URI) parameters.
*
*  @author Juan Caser
*/
class SMS {

    /**
     * URI for outbound messaging
     */
    const OUTBOUND_ENDPOINT_URI = 'https://devapi.globelabs.com.ph/smsmessaging/v1/outbound/{code}/requests';

    //"https://devapi.globelabs.com.ph/smsmessaging/v1/outbound/".$shortcode."/requests?app_id=".$app_id."&app_secret=".$app_secret."&passphrase=".$passphrase

    /**
     * Max character limit in sending SMS message
     */
    const SMS_CHAR_LIMIT = 160;

    /**
     * Lists of SMS recipient
     */
    private $recipient = [];

    /**
     * SMS message we need to send
     */
    private $message = '';


    /**
     * Set SMS recipient user
     * 
     * @param array $recipient Number of the recipient in array format.
     */
    public function to(array $recipient){
        if(!is_array($this->recipient)) $this->recipient = [];
        $recipient = array_merge($recipient, $this->recipient);
        $numbers = [];
        $this->recipient = [];
        foreach($recipient as $number){
            if(!in_array($number, $this->recipient)) $this->recipient[] = $number;
        }
        return $this;
    }

    /**
     * Set SMS message
     * 
     * @param string $message You plain text sms message.
     * @param boolean $split Set to TRUE to forced send on multiple batch, if FALSE it will return FALSE if char limit had exceeded.
     * @return boolean If $truncate is set to TRUE it will always return TRUE, otherwise it will check and return FALSE if char limit had exceeded.
     */
    public function message(string $message, bool $split = false){
        if($split){
            $this->message = str_split($message, self::SMS_CHAR_LIMIT);
            return $this;
        }else{
            if(strlen($message) <= self::SMS_CHAR_LIMIT){
                $this->message[] = $message;
                return $this;
            }else{
                throw new SMSException('Message exceeded maximum character limit of '.self::SMS_CHAR_LIMIT);
            }
        }
    }

    /**
     * Send SMS message if you set multiple recipient they will all received the message
     * 
     * @param boolean $bypass
     */
    public function send(bool $bypass = false){
        
        if(!is_array($this->recipient) || (is_array($this->recipient) && count($this->recipient) === 0)) 
            throw new SMSException('Message recipient not set.');
        
        $report = [];

        $uri = str_replace('{code}', SHORT_CODE, self::ENDPOINT_OUTBOUND_URI);
        if($bypass){
            if(!defined('PASS_PHRASE')) throw new SMSException('Pass Phrase not set.');
            $uri = '?app_id='.APP_ID.'&app_secret='.APP_SECRET.'&passphrase='.PASS_PHRASE;
        }else{
            $access_token = AccessToken::get(); // Get access token
            $uri = '?access_token='.$access_token;
        }
        
        foreach($this->recipient as $recipient){
            // Loop all message if its split it will take to sending
            foreach($this->message as $message){
                $clientCorrelator = date('hisjmy'); // ID is based from from time and date                    

                $curl_options = [
                    CURLOPT_URL => $uri,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => "{\"outboundSMSMessageRequest\": { \"clientCorrelator\": \"".$clientCorrelator."\", \"senderAddress\": \"".SHORT_CODE."\", \"outboundSMSTextMessage\": {\"message\": \"".$message."\"}, \"address\": \"".$recipient."\" } }",
                    CURLOPT_HTTPHEADER => [
                        "Content-Type: application/json"                        
                    ],                    
                ];
                $report[$recipient][$clientCorrelator] = $this->call($curl_options);
            }
        }

        return $report;
    }

    /**
     * Call API via cURL
     * 
     * @param array $curl_options
     * @return array
     */
    private function call(array $curl_options){
        $ch = curl_init();
        curl_setopt_array($ch, $curl_options);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return ['status' => 'error', 'message' => $err];
        } else {
            return ['status' => 'ok', 'message' => $response];
        }
    }
}