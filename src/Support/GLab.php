<?php namespace GLab\Support;
/**
*  Main
*
*  This is used to get and create access token
*
*  @author Juan Caser
*/

if(!function_exists('curl_init')) throw new BadFunctionCallException('cURL not enable.');
if(!defined('APP_ID')) throw new \Exception('APP ID not set.');
if(!defined('APP_SECRET')) throw new \Exception('APP SECRET not set.');
if(!defined('SHORT_CODE')) throw new \Exception('SHORT CODE not set.');

class TokenException extends \Exception {}

abstract class GLab {
    
    /**
     * URIs
     */
    const API_HOST = 'devapi.globelabs.com.ph';

    /**
     * Exception messages
     */
    const SMS_PASSPHRASE_NOTSET = 'Pass phrase not set.';
    const RECIPIENT_NOTSET = 'Recipient not set.';
    const SMS_MAXCHARLIMIT = 'Message exceeded maximum 160 character limit.';
    const TOKEN_INVALID_REQUEST = 'Invalid access token request.';
    const TOKEN_INVALID_RESPONSE = 'We have encountered an error while requesting your access token.';
    
    const SMS_CHAR_LIMIT = 160;

    /**
     * Get access token via OAuth 2.0
     */
    public function getAccessToken(){        
        if(defined('ACCESS_TOKEN')) $_SESSION['__GLAB_ACT'] = ACCESS_TOKEN;
        if(isset($_SESSION['__GLAB_ACT'])) return $_SESSION['__GLAB_ACT'];

        $uri = 'https://developer.globelabs.com.ph/oauth/access_token?app_id={app_id}&app_secret={app_secret}&code={code}';
        $uri = str_replace('{app_id}', APP_ID, $uri);
        $uri = str_replace('{app_secret}', APP_SECRET, $uri);
        $uri = str_replace('{code}', SHORT_CODE, $uri);
            
        $ch = curl_init();    
        curl_setopt_array($ch, [
            CURLOPT_URL => $uri,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true
        ]);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            throw new TokenException($err);
        } else {
            $response = json_decode($response);
            if(json_last_error() == JSON_ERROR_NONE){
                if(isset($response->access_token)){
                    $_SESSION['__GLAB_ACT'] = $response->access_token;
                }else{
                    if(isset($response->error)){
                        throw new TokenException($response->error);    
                    }else{
                        throw new TokenException(self::TOKEN_INVALID_REQUEST);
                    }
                }
            }else{
                throw new TokenException(self::TOKEN_INVALID_RESPONSE);
            }
            
        }
    }

    /**
     * Call POST via CURL
     * 
     * @param string $uri
     * @param array $post_fields
     */
    public function post(string $uri, array $post_fields = null){
        return $this->call('POST', $uri, $post_fields);
    }
    /**
     * Call GET via CURL
     * 
     * @param string $uri
     */
    public function get(string $uri){
        return $this->call('GET', $uri);
    }

    /**
     * CURL call
     * 
     * @param string $method
     * @param string $uri
     * @param array $fields
     */
    private function call(string $method, string $uri, array $fields = null){
        $ch = curl_init();
        $curl_options = [
            CURLOPT_URL             => 'https://'.API_HOST.'/'.ltrim($uri, '/'),
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => "",
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 30,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => $method,
            CURLOPT_HTTPHEADER      => ['Content-Type: application/json', 'Host: '.API_HOST])            
        ];

        if(!is_null($fields)){
            $curl_options[CURLOPT_POSTFIELDS] = json_encode($post_fields);
        }
        curl_setopt_array($ch, $curl_options);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($curl);
        if ($err) {
            return $err;
        } else {
            return $response;
        }
    }
}