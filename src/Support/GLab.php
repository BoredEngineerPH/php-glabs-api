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
class HttpException extends \Exception {}

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

    const API_SERVICE_DOWN = 'Platform Error. API Service is busy or down.';
    
    
    
    const SMS_CHAR_LIMIT = 160; // API implemented maximum char limit to SMS message

    /**
     * API Credentials
     */
    public $APP_ID; // Application ID
    public $APP_SECRET; // Application Secret
    public $SHORTCODE; // Sender Address

    // +----------------------------------------------------------------------------------------------------+
    // Configuration setter 
    // +----------------------------------------------------------------------------------------------------+
    /**
     * Set application id
     * 
     * @uses $app_id
     * @param string $app_id 32 character hash string generated when you create you app via Globe Labs developer dashboard.
     * @return object $this
     */
    public function set_app_id(string $app_id){
        $this->APP_ID = trim($app_id);
        return $this;
    }

    /**
     * Set application id
     * 
     * @uses $app_secret
     * @param string $app_secret 64 character hash string generated when you create you app via Globe Labs developer dashboard.
     * @return object $this
     */    
    public function set_app_secret(string $app_secret){
        $this->APP_SECRET = trim($app_secret);
        return $this;
    }
    
    /**
     * Set sender address a/k/a shortcode
     * 
     * @uses $shortcode
     * @param string $shortcode 8 digit number generated when you create you app via Globe Labs developer dashboard.
     * @return object $this
     */    
    public function set_shortcode(string $shortcode){
        $this->SHORTCODE = trim($shortcode);
        return $this;
    }

    /**
     * Get access token via OAuth 2.0
     * @param string $access_token
     * @param string Return access token
     */
    public function getAccessToken(string $access_token = null){        
        if(isset($_SESSION['__GLAB_ACT'])) return $_SESSION['__GLAB_ACT'];
        if(!is_null($access_token)) $_SESSION['__GLAB_ACT'] = $access_token;

        $uri = 'https://developer.globelabs.com.ph/oauth/access_token?app_id={app_id}&app_secret={app_secret}&code={code}';
        $uri = str_replace('{app_id}', $this->APP_ID, $uri);
        $uri = str_replace('{app_secret}', $this->APP_SECRET, $uri);
        $uri = str_replace('{code}', $this->SHORTCODE, $uri);
        
        $this0
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL             => $uri,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_POST            => true
        ]);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if(in_array($http_code, [200, 201])){
            $response = json_decode($response);
            if(json_last_error() == JSON_ERROR_NONE){
                if(isset($response->access_token)){
                    $_SESSION['__GLAB_ACT'] = $response->access_token;
                    return $_SESSION['__GLAB_ACT'];
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
        }elseif(in_array($http_code, [502, 503])){
            throw new HttpException(self::API_SERVICE_DOWN);
        }else{
            if ($err) throw new HttpException($err);    
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
     * cURL call
     * 
     * @param string $method
     * @param string $uri
     * @param array $fields
     */
    private function call(string $method = 'POST', string $uri, array $fields = null){
        $method = strtoupper($method);
        $ch = curl_init();
        $curl_options = [
            CURLOPT_URL             => 'https://'.API_HOST.'/'.ltrim($uri, '/'),
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => "",
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 30,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER      => ['Content-Type: application/json', 'Host: '.API_HOST]),
            CURLOPT_CUSTOMREQUEST   => $method
        ];
        if(!is_null($fields)) curl_setopt(CURLOPT_POSTFIELDS, json_encode($post_fields));
        curl_setopt_array($ch, $curl_options);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($curl);
        
        if($http_code == 500) throw new HttpException('Internal Server Error');
        if($http_code == 502) throw new HttpException('Bad Gateway');
        if($http_code == 503) throw new HttpException('Service Unavailable');
        if($http_code == 504) throw new HttpException('Gateway Timeout');
        if($err) throw new HttpException($err);

        return [
            'http_code' => $http_code,
            'message'   => $response
        ];        
    }
}