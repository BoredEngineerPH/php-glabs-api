<?php namespace GLab;
/**
*  LBS Service Api
*
*  This API allows a web application to query the location of one or more mobile devices that are connected to a mobile operator network. The Globe Labs LBS is a RESTful interface.
* 
*  Note: All API calls must include the access_token as one of the Universal Resource Identifier (URI) parameters. This can be requested beforehand via the Subscriber Consent Workflow.
* 
*  Read more about the Subscriber Consent Workflow (http://goo.gl/EEEBO8)
*
*  @author Juan Caser
*/

use GLab\Support\Glab;

class LBS  extends GLab{
    /**
     * Send SMS message if you set multiple recipient they will all received the message
     * 
     * @param boolean $bypass
     */
    public function locate(string $address, int $accuracy = 100){
        $access_token = $this->getAccessToken();
        return $this->get('/location/v1/queries/location?access_token='.$access_token.'&address='.$address.'&requestedAccuracy='.$accuracy)
    }
}