# Yet Another GLabs API PHP Wrapper

Globe labs API library

## Available Globe Labs API 
| API                             | Model           | Release | Status  |
|---------------------------------|----------------|---------|---------|
| [Short Messaging Service (SMS)](#short-messaging-service-sms) | `SMS.php`      | `1.0.1` | Added   |
| [Location Based Service (LBS)](#location-based-services-lbs)  | `LBS.php`      | `1.0.1` | Done |
| [Charging](#charging)                        | `Charging.php` | `1.0.1` | Ongoing |
| [Load](#load)                            | `Load.php`     | `1.0.1` | Added   |
| [USSD](#ussd)                            | `USDD.php`     | `1.0.1` | Ongoing |
| [Realtime Wallet](#realtime-wallet)               | `Wallet.php`   | `1.0.1` | Ongoing |
## Short Messaging Service (SMS)

Short Message Service (SMS) enables your application or service to send and receive secure, targeted text messages and alerts to your Globe / TM and other telco subscribers.

Note: All API calls must include the access_token as one of the Universal Resource Identifier (URI) parameters.

See [https://www.globelabs.com.ph/docs/#sms](https://www.globelabs.com.ph/docs/#sms)

### Sending SMS (MT)

> (Mobile Terminating - Application to Subscriber)

#### Resource Parameters 

| Parameter     | Description                                                                                                                                                        | Type   | Usage    |
|---------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------|--------|----------|
| `senderAddress` | Refers to the application short code suffix (last 4 digits) a.k.a `SHORT CODE`	                                                                                                        | `STRING` | Required |
| `access_token`  | Contains security information for transacting with a subscriber.   Subscriber needs to grant an app first via SMS or Web Form Subscriber Consent   Workflow.	 | `STRING` | Required |


#### Request Body or Payload Parameters
| Parameter          | Description                                                                                                                                                                                                                                  | Type     | Usage    |
|--------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|----------|----------|
|        `address`   |        Subscriber MSISDN (mobile number), including the ‘tel:’ identifier.   Parameter format can include the ‘+’ followed by country code +639xxxxxxxxx   or 09xxxxxxxxx	                                                             | `STRING` | Required |
| `message`          | Must be provided within the outboundSMSTextMessage element. Currently,   the API implementation is limited a maximum of 160 characters. Also make sure   that your language or framework’s editor is encoding the HTTP parameters as   UTF-8	 | `STRING` | Required |
| `cloentCorrelator` | Unique identifier when doing SMS request. If there is a communication   failure during the request, using the same clientCorrelator when retrying the   request allows the operator to avoid sending the same SMS twice.	                  | `STRING` | Optional |

```php
$payload = [
	'outboundSMSMessageRequest' => [
		'clientCorrelator' => '123456',
		'senderAddress' => '1234',
		'outboundSMSTextMessage' => [
			'message' => 'Hello World',
		],
		'address' => '0916xxxxxxx'
	]
];
```
| Method | URI                                                                                                                                     |
|--------|-----------------------------------------------------------------------------------------------------------------------------------------|
| `POST` | `https://devapi.globelabs.com.ph/location/v1/queries/location?access_token={access_token}&address={address}&requestedAccuracy={metres}` |

#### Usage
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
} catch(GLab\HttpException $e) {
    echo 'Http: ' .$e->getMessage();
} catch(GLab\TokenException $e) {
    echo 'Access Token: ' .$e->getMessage();
}
```

#### API Response
```json
{
 "outboundSMSMessageRequest": {
   "address": "tel:+639175595283",
   "deliveryInfoList": {
     "deliveryInfo": [],
     "resourceURL": "https://devapi.globelabs.com.ph/smsmessaging/v1/outbound/8011/requests?access_token=3YM8xurK_IPdhvX4OUWXQljcHTIPgQDdTESLXDIes4g"
   },
   "senderAddress": "8011",
   "outboundSMSTextMessage": {
     "message": "Hello World"
   },
   "receiptRequest": {
     "notifyURL": "http://test-sms1.herokuapp.com/callback",
     "callbackData": null,
     "senderName": null,
     "resourceURL": "https://devapi.globelabs.com.ph/smsmessaging/v1/outbound/8011/requests?access_token=3YM8xurK_IPdhvX4OUWXQljcHTIPgQDdTESLXDIes4g"
   }
 }
}
```
|        Parameter             | Description                                                                                      |
|------------------------------|--------------------------------------------------------------------------------------------------|
|        `outboundSMSResponse` |             Specifies the body of the response.                                                  |
|        `address`             |             Subscriber MSISDN (mobile number) whom the SMS was sent to.                          |
| `senderAddress`              | Refers to the application short code suffix (last 4 digits).                                     |
| `outboundSMSTextMessage`     | The string message sent to the subscriber’s number (address).                                    |
| `resourceURL`                | Specifies the URI that provides network delivery status of the sent   message. The API Endpoint. |
| `notifyURL`                  | App call back URL defined at the App Info.                                                       |

**Note:** Response parameters `deliveryInfo`, `callbackData`, `senderName` are optional parameters that are not currently supported by the Globe Labs SMS API. Error response with 400 series will deduct 0.50 from your wallet balance.



#### HTTP Code

| Code          | Description                                                                                          |
|---------------|------------------------------------------------------------------------------------------------------|
| `201`         | Request has been   successful                                                                        |
| `400` `401` | Request failed. Wrong   or missing parameters, invalid subscriber_number format, wrong access_token. |
| `502` `503` | Platform Error. API   Service is busy or down                                                        |

**Note:** API requests with a response code of `201`, `400` or `401` will be chargeable against your developer wallet. Standard SMS API rates apply, unless otherwise stated.


See [https://www.globelabs.com.ph/docs/#sms-sending-sms-sms-mt](https://www.globelabs.com.ph/docs/#sms-sending-sms-sms-mt)

## Location Based Services (LBS)

This API allows a web application to query the location of one or more mobile devices that are connected to a mobile operator network. The Globe Labs LBS is a RESTful interface.

Note: All API calls must include the access_token as one of the Universal Resource Identifier (URI) parameters. This can be requested beforehand via the Subscriber Consent Workflow.

Read more about the Subscriber Consent Workflow (http://goo.gl/EEEBO8)

See [https://www.globelabs.com.ph/docs/#location-based-services](https://www.globelabs.com.ph/docs/#location-based-services)

#### Resource Parameters 
| Parameter                            | Description                                                                                                                                                                                                                           | Type     | Usage    |
|--------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|----------|----------|
|        `access_token`                |        Contains security information for transacting with a subscriber.This can be   requested beforehand via the [Subscriber Consent Workflow](http://goo.gl/EEEBO8).              | `STRING` | Required |
|                  `address`           | Subscriber MSISDN (mobile number), including the ‘tel:’ identifier.   Parameter format can include the ‘+’ followed by country code +639xxxxxxxxx   or 09xxxxxxxxx                                                                    | `INT`    | Required |
|                  `requestedAccuracy` | The preferred accuracy of the result, in metres. Typically, when you   request an accurate location it will take longer to retrieve than a coarse   location. So requestedAccuracy=10 will take longer than requestedAccuracy=100   .	 | `INT`    | Required |

| Method | URI                                                                                                                                       |
|--------|-------------------------------------------------------------------------------------------------------------------------------------------|
| `GET`  | `https://devapi.globelabs.com.ph/location/v1/queries/location?access_token={access_token}&address={address}&requestedAccuracy={accuracy}` |
#### Usage

```php
try {
    $lbs = new GLab\LBS();    
    $lbs->locate('0916xxxxxxx', 100);
} catch(GLab\HttpException $e) {
    echo 'Http: ' .$e->getMessage();
} catch(GLab\TokenException $e) {
    echo 'Http: ' .$e->getMessage();
}
```
#### API Response
```json
{
  "terminalLocationList": {
    "terminalLocation": {
      "address": "tel:9171234567",
      "currentLocation": {
        "accuracy": 100,
        "latitude": "14.5609722",
        "longitude": "121.0193394",
        "map_url": "http://maps.google.com/maps?z=17&t=m&q=loc:14.5609722+121.0193394",
        "timestamp": "Fri Jun 06 2014 09:25:15 GMT+0000 (UTC)"
      },
      "locationRetrievalStatus": "Retrieved"
    }
  }
}
```
|        Parameter              | Description                                                             |
|-------------------------------|-------------------------------------------------------------------------|
|        `terminalLocationList` |             Specifies the body of the response.                         |
|        `address`              |             Subscriber MSISDN (mobile number) whom the SMS was sent to. |
| `accuracy`                    | The preferred accuracy of the result, in metres.                        |
| `outboundSMSTextMessage`      | The string message sent to the subscriber’s number (address).           |
| `latitude`                    | geographic coordinate of the subscriber that specifies the north-south. |
| `longitude`                   | geographic coordinate of the subscriber that specifies the north-south. |
| `timestamp`                   | time of event response.                                                 |
| `locationRetrievalStatus`     | status of location request.                                             |

#### HTTP Code
| Code          | Description                                    |
|---------------|------------------------------------------------|
| `201`         | Request has been   successful                  |
| `400` `401` | Request failed. Wrong   or missing parameters. |
| `502` `503` | Platform Error. API   Service is busy or down  |

See [https://www.globelabs.com.ph/docs/#location-based-services-lbs-query](https://www.globelabs.com.ph/docs/#location-based-services-lbs-query)

## Charging
The Charging API allows developers to directly charge for digital services to the prepaid balance of a Globe or TM subscriber.

Note: The Charging API is not readily available upon app creation. To avail, please email your app’s use case and company name to api@globe.com.ph

See [https://www.globelabs.com.ph/docs/#charging](https://www.globelabs.com.ph/docs/#charging)

## Load
The Load API enables your application to send prepaid load, postpaid credits or call, text and surfing promos to your subscribers.

Note: The Load API is not readily available upon app creation. To avail, please email your app’s use case and company name to api@globe.com.ph

See [https://www.globelabs.com.ph/docs/#load](https://www.globelabs.com.ph/docs/#load)

#### Request Body or Payload Parameters
| Parameter           | Description                                                                                                                                                     | Type     | Usage    |
|---------------------|-----------------------------------------------------------------------------------------------------------------------------------------------------------------|----------|----------|
|        `app_id`     |             This is the unique identifier of your app                                                                                                           | `STRING` | Required |
|        `app_secret` |             This is the security code of your app                                                                                                               | `STRING` | Required |
| `rewards_token`     | This is used as a key to allow your app to send rewards                                                                                                         | `STRING` | Required |
| `address`           | Subscriber MSISDN (mobile number), including the ‘tel:’ identifier.   Parameter format can include the ‘+’ followed by country code 09xxxxxxxxx or   9xxxxxxxxx | `STRING` | Required |
| `promo`             | This is the promo to be sent                                                                                                                                    | `STRING` | Required |

### Usage
```php
$rewards = 'AbCkxKYid_F_p-JSgTejow';
$promo = 'Load 1';
try {
    $sms = new GLab\Load();
    $sms->to(['0916xxxxxxx'])
        ->send($rewards, $promo)
} catch(GLab\LoadException $e) {
    echo 'Load: ' .$e->getMessage();
}
```

## USSD

The USSD API allows users to access your products or services free of charge by accessing the dial menu through a dedicated number.

Note: The USSD API is not readily available upon app creation. To avail, please email your app’s use case and company name to api@globe.com.ph

See [https://www.globelabs.com.ph/docs/#ussd](https://www.globelabs.com.ph/docs/#ussd)

## Realtime Wallet
Wallet Balance API allows developers to retrieve their real-time wallet balance via API so they can monitor their balance.

See [](https://www.globelabs.com.ph/docs/#realtime-wallet-api)
