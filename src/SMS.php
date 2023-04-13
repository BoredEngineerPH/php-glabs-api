<?php namespace GLab;
/**
*  SMS Service Api
*
*  Short Message Service (SMS) enables your application or service to send and receive secure, 
*  targeted text messages and alerts to your Globe / TM and other telco subscribers.
* 
*  Note: All API calls must include the access_token as one of the Universal Resource Identifier (URI) parameters.
*
*  @author Juan Caser
*/
use GLab\Support\GLab;

/**
 * SMS model exception
 */
class SMSException extends \Exception{}

class SMS extends GLab{
    
    private $address = []; // Number of the recipient
    private $message = ''; // Message to the recipient

    /**
     * Address or mobile number of the recipient
     * 
     * @param string/array $address Mobile number of the recipient, you can either pass multiple or single number on this function.
     * @return object $this self
     */
    public function address($address){
        if(is_array($address)){
            $this->address = array_merge($this->address, $address);
        }elseif(is_string($address)){
            $this->address[] = $address;
        }
        $this->address = array_unique($this->address);
        return $this
    }

    /**
     * Message to the recipoent
     * 
     * @param string $message Should be UTF-8 encoded, the API implementation limits a maximum of 160 characters anything beyond it will split the message into mutiple parts.
     * @return object $this self
     */
    public function message(string $message){
        $this->message = $message;
        return $this;
    }

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
     * Send SMS message if you set multiple recipient they will all received the message
     * 
     * @param string $pass_phrase Passing this will trigger bypass sending of SMS
     * @return array Report
     */
    public function send(string $pass_phrase = null){
        
        if(is_array($this->address) && count($this->address) === 0) throw new SMSException('Recipient address not set.');
        if(!empty($message))  throw new SMSException('Message not set.');

        if(!is_null($pass_phrase)){
            $query_str = '?app_id='.$this->APP_ID.'&app_secret='.$this->APP_SECRET.'&passphrase='.$pass_phrase;
        }else{
            $query_str = '?access_token='.$this->getAccessToken();
        }
        
        $reporting = [];
        foreach($this->address as $address){
            $clientCorrelator = md5($address);
            $post_fields = [
                'outboundSMSMessageRequest' => [
                    'clientCorrelator' => $clientCorrelator,
                    'senderAddress' => $this->SHORTCODE,
                    'outboundSMSTextMessage' => [
                        'message' => $message,
                    ],
                    'address' => $address
                ]
            ];
            
            $this->post('/smsmessaging/v1/outbound/'. $this->SHORTCODE.'/requests'.$query_str, $post_fields, function($http_code, $http_response, $http_error) use($reporting){
                if($http_code == 201){
                    $reporting[$address][$clientCorrelator] = $http_response;
                }elseif(in_array($http_code, [400, 401])){
                    throw new SMSException('Request failed. Wrong or missing parameters, invalid subscriber_number format, wrong access_token.');
                }
                
            });
        }
        return $reporting;
    }
}