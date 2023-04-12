<?php namespace GLab;

use GLab\Support\AccessToken;

if(!function_exists('curl_init')) throw new BadFunctionCallException('cURL not enable.');
if(!defined('APP_ID')) throw new \Exception('APP ID not set.');
if(!defined('APP_SECRET')) throw new \Exception('APP SECRET not set.');
if(!defined('SHORT_CODE')) throw new \Exception('SHORT CODE not set.');


class LoadException extends \Exception{}

/**
*  Load API
*
* The Load API enables your application to send prepaid load, postpaid credits or call, text and surfing promos to your subscribers.
* 
* Note: The Load API is not readily available upon app creation. To avail, please email your app’s use case and company name to api@globe.com.ph
*
*  @author Juan Caser
*/
class Load {

    /**
     * Outbound URI
     */
    const OUTBOUND_ENDPOINT_URI = 'https://devapi.globelabs.com.ph/rewards/v1/transactions';

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
            throw new LoadException('Load recipient not set.');
        
        $transaction = [];
        
        foreach($this->recipient as $recipient){

            $curl_options = [
                CURLOPT_URL => self::ENDPOINT_OUTBOUND_URI.'/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "{ \"outboundRewardRequest\" : { \"app_id\" : \"".APP_ID."\", \"app_secret\" : \"".APP_SECRET."\", \"rewards_token\" : \"".$rewards_token."\", \"address\" : \"".$recipient."\", \"promo\" : \"".$promo."\" } }",
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                    "Host: devapi.globelabs.com.ph"
                ],                    
            ];
            $response = $this->call($curl_options);
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
        return $this->call([
            CURLOPT_URL => self::ENDPOINT_OUTBOUND_URI.'/status',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{ \"rewardStatusRequest\" : { \"app_id\" : \"".APP_ID."\", \"app_secret\" : \"".APP_SECRET."\", \"rewards_token\" : \"".$rewards_token."\", \"transaction_id\" : \"".$transaction_id."\" } }",
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json"
            ],                    
        ]);
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