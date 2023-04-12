<?php namespace GLab\Support;
/**
*  Access Token using OAuth 2.0
*
*  This is used to get and create access token
*
*  @author Juan Caser
*/

if(!function_exists('curl_init')) throw new BadFunctionCallException('cURL not enable.');
if(!defined('APP_ID')) throw new \Exception('APP ID not set.');
if(!defined('APP_SECRET')) throw new \Exception('APP SECRET not set.');
if(!defined('SHORT_CODE')) throw new \Exception('SHORT CODE not set.');

class AccessTokenException extends \Exception {}

class AccessToken {
    /**
     * URI used to request access token
     */
    const ENDPOINT_URI = 'https://developer.globelabs.com.ph/oauth/access_token?app_id={app_id}&app_secret={app_secret}&code={code}';

    /**
     * Access Token exception messages
     */

    const INVALID_ACCESSTOKEN_REQUEST = 'Invalid access token request.';
    const INVALID_ACCESSTOKEN_RESPONSE = 'We have encountered an error while requesting your access token.';
    

    public static function get(){        
        if(defined('ACCESS_TOKEN')) $_SESSION['__GLAB_ACT'] = ACCESS_TOKEN;
        if(isset($_SESSION['__GLAB_ACT'])) return $_SESSION['__GLAB_ACT'];

        $uri = self::ENDPOINT_URI;
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
            throw new AccessTokenException($err);
        } else {
            $response = json_decode($response);
            if(json_last_error() == JSON_ERROR_NONE){
                if(isset($response->access_token)){
                    $_SESSION['__GLAB_ACT'] = $response->access_token;
                }else{
                    if(isset($response->error)){
                        throw new AccessTokenException($response->error);    
                    }else{
                        throw new AccessTokenException(self::INVALID_ACCESSTOKEN_REQUEST);
                    }
                }
            }else{
                throw new AccessTokenException(self::INVALID_ACCESSTOKEN_RESPONSE);
            }
            
        }
    }
}