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

class SMSException extends \Exception{}

class SMS extends GLab{
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
            $this->message = str_split($message, parent::SMS_CHAR_LIMIT);
            return $this;
        }else{
            if(strlen($message) <= parent::SMS_CHAR_LIMIT){
                $this->message[] = $message;
                return $this;
            }else{
                throw new SMSException(parent::SMS_MAXCHARLIMIT);
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
            throw new SMSException(parent::RECIPIENT_NOTSET);
        
        $report = [];

        if($bypass){
            if(!defined('PASS_PHRASE')) throw new SMSException(parent::SMS_PASSPHRASE_NOTSET);
            $query_str = '?app_id='.APP_ID.'&app_secret='.APP_SECRET.'&passphrase='.PASS_PHRASE;
        }else{
            $access_token = $this->getAccessToken(); // Get access token
            $query_str = '?access_token='.$access_token;
        }
        
        foreach($this->recipient as $recipient){
            // Loop all message if its split it will take to sending
            foreach($this->message as $message){
                $clientCorrelator = date('hisjmy'); // ID is based from from time and date                    
                $report[$recipient][$clientCorrelator] = $this->post('/smsmessaging/v1/outbound/'.SHORT_CODE.'/requests'.$query_str, [
                    'outboundSMSMessageRequest' => [
                        'clientCorrelator'  => $clientCorrelator,
                        'senderAddress'     => SHORT_CODE
                        'outboundSMSTextMessage' => [
                            'message'       => $message
                        ],
                        'address'           => $recipient                     
                    ]
                ]);
            }
        }
        return $report;
    }
}