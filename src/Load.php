<?php namespace GLab;
/**
*  Load API
*
*  The Load API enables your application to send prepaid load, postpaid credits or call, text and surfing promos to your subscribers.
* 
*  Note: The Load API is not readily available upon app creation. To avail, please email your app’s use case and company name to api@globe.com.ph
*
*  @author Juan Caser
*/

use GLab\Support\GLab;

class LoadException extends \Exception{}

class Load extends GLab{
    /**
     * Lists of load recipient
     */
    private $recipient = [];

    /**
     * Set Load recipient user
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
     * Send Load 
     * 
     * @param string $rewards_token
     * @param string $promo
     * @return array
     */
    public function send(string $rewards_token, string $promo){
        
        if(!is_array($this->recipient) || (is_array($this->recipient) && count($this->recipient) === 0)) 
            throw new LoadException(ApiConstants::RECIPIENT_NOTSET);
        
        $transaction = [];
        
        foreach($this->recipient as $recipient){
            $response = $this->post('/rewards/v1/transactions/send', [
                'outboundRewardRequest' => [
                    'app_id'        => APP_ID,
                    'app_secret'    => APP_SECRET,
                    'rewards_token' => $rewards_token,
                    'address'       => $recipient,
                    'promo'         => $promo
                ]
            ]);
            $transaction[$recipient] = $response;
        }
        return $transaction;
    }

    /**
     * Get load status by $transaction_id
     * 
     * @param string $transaction_id
     * @param string $rewards_token     
     * @return array
     */
    public function status(string $transaction_id, string $rewards_token){
        return $this->post('/rewards/v1/transactions/status', [
            'rewardStatusRequest' => [
                'app_id'        => APP_ID, 
                'app_secret'    => APP_SECRET, 
                'rewards_token' => $rewards_token, 
                'transaction_id'=> $transaction_id
            ] 
        ]);
    }
}