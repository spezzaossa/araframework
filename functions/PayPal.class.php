<?php

require_once("paypal/OAuth.php");

class PayPal
{
	private $api_username;
	private $api_password;
	private $api_signature;
	private $api_appid;

	public function __construct() {
		if (DEBUG_MODE)
		{
			$this->api_username		= PAYPAL_SANDBOX_USERNAME;
			$this->api_password		= PAYPAL_SANDBOX_PASSWORD;
			$this->api_signature	= PAYPAL_SANDBOX_SIGNATURE;
			$this->api_appid		= PAYPAL_SANDBOX_APPID;
		}
		else
		{
			$this->api_username		= PAYPAL_USERNAME;
			$this->api_password		= PAYPAL_PASSWORD;
			$this->api_signature	= PAYPAL_SIGNATURE;
			$this->api_appid		= PAYPAL_APPID;
		}
	}

    public function setValue($key, $value) {
      // Verify if array exists
     if (isset($this->allValues) && is_array($this->allValues)) {
        // Insert or update current key
        $this->allValues[$key] = $value;
      } else {
        // Array doesn't exists, create it
        $this->allValues = array($key => $value);
      }
    }

    public function getValue($key) {
      // Verify if array exists
     if (is_string($key) && isset($this->allValues) && is_array($this->allValues)) {
       // Insert or update current key
		if (array_key_exists($key, $this->allValues)) {
          return $this->allValues[$key];
        } else {
          return false;
        }
      } else {
        return false;
      }
    }

    public function cleanValues() {
      // Verify if array exists
     if (isset($this->allValues) && is_array($this->allValues)) {
        // Cleanup array
        unset($this->allValues);
      }
    }

    public function buildQuery() {
      // Verify if array exists
      if (is_array($this->allValues)) {
        // Array exists, return querystring
        return http_build_query($this->allValues, "", chr(38));
      } else {
        // Array doesn't exists, create it
        return false;
      }
    }

    private function getCURLResult($function, $opts, $user_data = null)
    {
        // Verify if array exists
        if (is_array($this->allValues)) {
            // Create a new cURL resource
            $request = curl_init();

            if (!$request) {
                return array(
                    "ERROR" => "Could not initialize a cURL session"
                );
            } else {
                // Print eppela request to screen for debugging purposes
                if (DEBUG_MODE === true) {
                    Utils::prePrint($this->allValues);
                }

                // Define PayPal API Credentials

                if(DEBUG_MODE){
                    // Use Sandbox API credentials:
                    $api_endpoint  = "https://svcs.sandbox.paypal.com/";
                } else {
                    $api_endpoint  = "https://svcs.paypal.com/";
                }

                if ($user_data && $user_data['token'] != "" && $user_data['secret'] != "")
                {
                    $auth = GenSignature($api_username, $api_password, $user_data['token'], $user_data['secret'], 'POST', $api_endpoint, null);
                    // https://api.paypal.com/nvp

                    $auth_cooked = "timestamp=".time().",token={$user_data['token']},signature=";
                    $auth_cooked .= $auth['oauth_signature'];

                    $opts[] = "X-PP-AUTHORIZATION: {$auth_cooked}";
                }

				$opts = array_merge($opts, array(
					"X-PAYPAL-SECURITY-USERID: {$this->api_username}",
					"X-PAYPAL-SECURITY-SIGNATURE: {$this->api_signature}",
					"X-PAYPAL-SECURITY-PASSWORD: {$this->api_password}",
					"X-PAYPAL-APPLICATION-ID: {$this->api_appid}"
				));

                // Setting options for a cURL transfer
                //curl_setopt($request, CURLOPT_URL, $api_endpoint . ($nvp) ? '' : $function);
                curl_setopt($request, CURLOPT_URL, $api_endpoint . $function);
                curl_setopt($request, CURLOPT_VERBOSE, true);         // Output verbose information
                curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false); // Turning off the peer verification(TrustManager Concept)
                curl_setopt($request, CURLOPT_SSL_VERIFYHOST, false); // Don't verify SSL
                curl_setopt($request, CURLOPT_RETURNTRANSFER, true);  // Return the transfer as a string instead of outputting it out directly
                curl_setopt($request, CURLOPT_CONNECTTIMEOUT, 15);    // Timeout on connect
                curl_setopt($request, CURLOPT_TIMEOUT, 15);           // Timeout on response
                curl_setopt($request, CURLOPT_FAILONERROR, true);     // Fail silently if the HTTP code returned is greater than or equal to 400
                curl_setopt($request, CURLOPT_HEADER, false);         // Don't return response headers
                curl_setopt($request, CURLOPT_POST, true);            // Do a regular HTTP POST
                curl_setopt($request, CURLINFO_HEADER_OUT, true);     // Track the handle's request string
            }

            curl_setopt($request, CURLOPT_HTTPHEADER, $opts);

            // Setting the nvpreq as POST FIELD to curl
            curl_setopt($request, CURLOPT_POSTFIELDS, $this->buildQuery());

            // Execute the curl POST
            $response = curl_exec($request);

            // Print eppela headers to screen for debugging purposes
            if (DEBUG_MODE === true) {
                Utils::prePrint(curl_getinfo($request, CURLINFO_HEADER_OUT));
            }

            if (!curl_errno($request) == 0) {
                // Close a cURL session
                curl_close($request);

                // Return a string containing the last error
                return array(
                    "ERROR" => "Could not open connection"
                );
            } else {
                // Close a cURL session
                curl_close($request);

                // Parses the response into variables
                // and assign values to $result array
                parse_str($response, $result);

                // Print PayPal response to screen for debugging purposes
                if (DEBUG_MODE === true) {
                    Utils::prePrint($result);
                }
            }
            return $result;
        }

        return array(
            "ERROR" => "No data to send"
        );
    }

    function Preapproval($user = null)
    {
        $opts = array(
              "X-PAYPAL-REQUEST-DATA-FORMAT: NV",
              "X-PAYPAL-RESPONSE-DATA-FORMAT: NV"
        );

        $result = $this->getCURLResult("AdaptivePayments/Preapproval", $opts, $user);

		return $result;
    }

    function PreapprovalDetails($user = null)
    {
        $opts = array(
              "X-PAYPAL-REQUEST-DATA-FORMAT: NV",
              "X-PAYPAL-RESPONSE-DATA-FORMAT: NV"
        );

        $result = $this->getCURLResult("AdaptivePayments/PreapprovalDetails", $opts, $user);

		return $result;
    }

    function CancelPreapproval()
    {
        $opts = array(
              "X-PAYPAL-REQUEST-DATA-FORMAT: NV",
              "X-PAYPAL-RESPONSE-DATA-FORMAT: NV"
        );

        $result = $this->getCURLResult("AdaptivePayments/CancelPreapproval", $opts, $user);

        return $result;
    }

    function getGMT($datetime)
	{
      // Convert datetime to PayPal GMT offset formats
      // as in the following example:
      // 2010-09-10T17:24:03.874-07:00

      if (!strtotime($datetime)) {
        return $datetime;
      } else {
        // Convert input to Datetime object with local timezone
        $gmtdatetime = new DateTime($datetime, new DateTimeZone('Europe/Rome'));

        // Return date into PayPal GMT offset format
        return $gmtdatetime->format('Y-m-d\TH:i:s.000Z');
      }
    }

    function setValue3D($key, $value)
	{
      // Verify if array exists
      if (is_array($this->allValues)) {
        // Verify if value alredy exists
        foreach ($this->allValues as &$array) {
          // to print the key name use: key($array)
          // to print the key value use: $array[key($array)]

          if (key($array) == $key) {
            // Value exists, update current key
            $array[key($array)] = $value;
            return;
          }
        }

        // Value doesn't exists, create it
        $this->allValues[] = array ($key => $value);
      } else {
        // Array doesn't exists, create it
        $this->allValues[] = array ($key => $value);
      }
    }

    function Pay()
    {
        $opts = array(
            "X-PAYPAL-REQUEST-DATA-FORMAT: NV",
            "X-PAYPAL-RESPONSE-DATA-FORMAT: NV",
        );

        $result = $this->getCURLResult("AdaptivePayments/Pay", $opts, null);

        return $result;
    }

    function PaymentDetails()
    {
        $opts = array(
            "X-PAYPAL-REQUEST-DATA-FORMAT: NV",
            "X-PAYPAL-RESPONSE-DATA-FORMAT: NV",
        );

        $result = $this->getCURLResult("AdaptivePayments/PaymentDetails", $opts);

        return $result;
    }

	function RequestPermissions()
    {
        $opts = array(
            "X-PAYPAL-REQUEST-DATA-FORMAT: NV",
            "X-PAYPAL-RESPONSE-DATA-FORMAT: NV"
        );

        $result = $this->getCURLResult("Permissions/RequestPermissions", $opts);

        return $result;
    }

	function GetAccessToken()
    {
        $opts = array(
            "X-PAYPAL-REQUEST-DATA-FORMAT: NV",
            "X-PAYPAL-RESPONSE-DATA-FORMAT: NV"
        );

        $result = $this->getCURLResult("Permissions/GetAccessToken", $opts);

        return $result;
    }

	function GetPermissions()
    {
        $opts = array(
            "X-PAYPAL-REQUEST-DATA-FORMAT: NV",
            "X-PAYPAL-RESPONSE-DATA-FORMAT: NV"
        );

        $result = $this->getCURLResult("Permissions/GetPermissions", $opts);

        return $result;
    }

	function CancelPermissions()
    {
        $opts = array(
            "X-PAYPAL-REQUEST-DATA-FORMAT: NV",
            "X-PAYPAL-RESPONSE-DATA-FORMAT: NV"
        );

        $result = $this->getCURLResult("Permissions/CancelPermissions", $opts);

        return $result;
    }

    public function SetExpressCheckout()
    {
        $this->setValue('METHOD', 'SetExpressCheckout');

        $result = $this->getCURLResult("AdaptivePayments/SetExpressCheckout", $opts);

        return $result;
    }

    public static function getAuthorizationList()
    {
        return array(
            'EXPRESS_CHECKOUT',
            'AUTH_CAPTURE',
            'BILLING_AGREEMENT',
            'TRANSACTION_DETAILS',
            'ENCRYPTED_WEBSITE_PAYMENTS',
            'REFUND',
            'NON_REFERENCED_CREDIT',
            'MANAGE_PENDING_TRANSACTION_STATUS',
        );
    }

    public static function checkAuthorizationList($authlist)
    {
        $auths = PayPal::getAuthorizationList();
        $auth = array_pop($auths);

        while($auth != NULL)
        {
            if (array_search($auth, $authlist) === false)
                return false;

            $auth = array_pop($auths);
        }

        return true;
    }
}

?>