# Yet Another GLabs API PHP Wrapper

Globe labs API library

## Available Globe Labs API 
| API                           | Release | Status  |
|-------------------------------|---------|---------|
| Short Messaging Service (SMS) | 1.0.1   | Added   |
| Location Based Service (LBS)  | 1.0.1   | Ongoing |
| Charging                      | 1.0.1   | Ongoing |
| Load                          | 1.0.1   | Added   |
| USSD                          | 1.0.1   | Ongoing |
| Realtime Wallet               | 1.0.1   | Ongoing |

## Short Messaging Service (SMS)

Short Message Service (SMS) enables your application or service to send and receive secure, targeted text messages and alerts to your Globe / TM and other telco subscribers.

Note: All API calls must include the access_token as one of the Universal Resource Identifier (URI) parameters.

See [](https://www.globelabs.com.ph/docs/#sms)

### Sending SMS (MT) `POST`

> (Mobile Terminating - Application to Subscriber)

#### Resource Parameters 

> https://devapi.globelabs.com.ph/smsmessaging/v1/outbound/{senderAddress}/requests?access_token={access_token}


| Parameter     | Description                                                                                                                                                        | Type   | Usage    |
|---------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------|--------|----------|
| `senderAddress` | refers to the application short code suffix (last 4 digits) a.k.a `SHORT CODE`	                                                                                                        | `STRING` | Required |
| `access_token`  | which contains security information for transacting with a subscriber.   Subscriber needs to grant an app first via SMS or Web Form Subscriber Consent   Workflow.	 | `STRING` | Required |


#### Request Body or Payload Parameters
| Parameter          | Description                                                                                                                                                                                                                                  | Type     | Usage    |
|--------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|----------|----------|
|        `address`   |        is the subscriber MSISDN (mobile number), including the ‘tel:’ identifier.   Parameter format can include the ‘+’ followed by country code +639xxxxxxxxx   or 09xxxxxxxxx	                                                             | `STRING` | Required |
| `message`          | must be provided within the outboundSMSTextMessage element. Currently,   the API implementation is limited a maximum of 160 characters. Also make sure   that your language or framework’s editor is encoding the HTTP parameters as   UTF-8	 | `STRING` | Required |
| `cloentCorrelator` | uniquely identifies this create SMS request. If there is a communication   failure during the request, using the same clientCorrelator when retrying the   request allows the operator to avoid sending the same SMS twice.	                  | `STRING` | Optional |


#### How to use
```php
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
}
```

## Location Based Services (LBS)

This API allows a web application to query the location of one or more mobile devices that are connected to a mobile operator network. The Globe Labs LBS is a RESTful interface.

Note: All API calls must include the access_token as one of the Universal Resource Identifier (URI) parameters. This can be requested beforehand via the Subscriber Consent Workflow.

Read more about the Subscriber Consent Workflow (http://goo.gl/EEEBO8)

See [](https://www.globelabs.com.ph/docs/#location-based-services)

## Charging
The Charging API allows developers to directly charge for digital services to the prepaid balance of a Globe or TM subscriber.

Note: The Charging API is not readily available upon app creation. To avail, please email your app’s use case and company name to api@globe.com.ph

See [](https://www.globelabs.com.ph/docs/#charging)

## Load
The Load API enables your application to send prepaid load, postpaid credits or call, text and surfing promos to your subscribers.

Note: The Load API is not readily available upon app creation. To avail, please email your app’s use case and company name to api@globe.com.ph

See [](https://www.globelabs.com.ph/docs/#load)

## USSD

The USSD API allows users to access your products or services free of charge by accessing the dial menu through a dedicated number.

Note: The USSD API is not readily available upon app creation. To avail, please email your app’s use case and company name to api@globe.com.ph

See [](https://www.globelabs.com.ph/docs/#ussd)

## Realtime Wallet
Wallet Balance API allows developers to retrieve their real-time wallet balance via API so they can monitor their balance.

See [](https://www.globelabs.com.ph/docs/#realtime-wallet-api)
