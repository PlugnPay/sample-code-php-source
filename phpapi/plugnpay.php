<?php

/******************************************************************************
  This code is an example only and is not officially supported by Plug N Pay
  Technologies inc.  If you are having problems with PHP please refer to
  http://www.php.net for help.  If you are having problems using the API please
  check the documentation in the admin area first.  If you don't find an answer
  submit a helpdesk item.

  If you are using this script please READ all documentation included with it and
  the API specification.

  This script requires a working implementation of curl (internal or external to PHP).
  If you receive any error messages like this:

    Fatal error: Call to undefined function: curl_init() in 'your script here'

  It is because your installation of PHP was not configured to support curl.
  Contact your system administrator to have curl setup.

*******************************************************************************/
    /*
      Set script parameters & answer questions down here...
    */

    // Is curl complied into PHP? 
    $is_curl_compiled_into_php = 'no'; 
    // Possible answers are: 
    //  'yes' -> means that curl is compiled into PHP [DEFAULT]
    //  'no'  -> means that curl is not-compiled into PHP & must be called externally

    // If you selected 'no' to the above question, then set the absolute path to curl
    $curl_path = '/usr/bin/curl';
    // [e.g.: '/usr/bin/curl' on Unix/Linux or 'c:/curl/curl.exe' on Windows servers] 
    // If you are unsure of this, check with your hosting company.

    // Set URL that you will post the transaction to
    $pnp_post_url = 'https://pay1.plugnpay.com/payment/pnpremote.cgi';
    // This should never need to be changed...


    // Set PnP account cridentials here:
    $publisher_name = '{gateway_account}';  // replace with your PlugnPay account username
    $publisher_password = '{api_password}'; // replace with your Remote Client Password

    /*
      This is where you build the query string to be posted to plugnpay.  You
      can replace this code with your own or you need to follow the instructions
      in the README file for calling this script.

      ** Since this is a just a example, we'll use the raw POST data. But for proper 
      usage of this payment method, you should have all the required data, have
      filtered it, validated its format, and performed your own fraud security
      before passing any of the data to the gateway for processing.
    */

    $pnp_post_values = '';
    $pnp_post_values .= 'publisher-name=' . $publisher_name . '&';
    $pnp_post_values .= 'publisher-password=' . $publisher_password . '&';
    $pnp_post_values .= 'card-number=' . isset($_POST['card_number']) . '&';
    $pnp_post_values .= 'card-cvv=' . isset($_POST['card_cvv']) . '&';
    $pnp_post_values .= 'card-exp=' . isset($_POST['card_exp']) . '&';
    $pnp_post_values .= 'card-amount=' . isset($_POST['card_amount']) . '&';
    $pnp_post_values .= 'card-name=' . isset($_POST['card_name']) . '&';
    $pnp_post_values .= 'email=' . isset($_POST['email']) . '&';
    $pnp_post_values .= 'ipaddress=' . isset($_SERVER['REMOTE_ADDR']) . '&';
    // billing address info
    $pnp_post_values .= 'card-address1=' . isset($_POST['card_address1']) . '&';
    $pnp_post_values .= 'card-address2=' . isset($_POST['card_address2']) . '&';
    $pnp_post_values .= 'card-zip=' . isset($_POST['card_zip']) . '&';
    $pnp_post_values .= 'card-city=' . isset($_POST['card_city']) . '&';
    $pnp_post_values .= 'card-state=' . isset($_POST['card_state']) . '&';
    $pnp_post_values .= 'card-country=' . isset($_POST['card_country']) . '&';
    // shipping address info
    $pnp_post_values .= 'shipinfo=' . '1' . '&';
    $pnp_post_values .= 'shipname=' . isset($_POST['shipname']) . '&';
    $pnp_post_values .= 'address1=' . isset($_POST['card_address1']) . '&';
    $pnp_post_values .= 'address2=' . isset($_POST['card_address2']) . '&';
    $pnp_post_values .= 'zip=' . isset($_POST['card_zip']) . '&';
    $pnp_post_values .= 'state=' . isset($_POST['card_state']) . '&';
    $pnp_post_values .= 'country=' . isset($_POST['card_country']) . '&';

    /**************************************************************************
      UNLESS YOU KNOW WHAT YOU ARE DOING YOU SHOULD NOT CHANGE THE BELOW CODE
    **************************************************************************/

    $pnp_result_decoded = '';
    if ($is_curl_compiled_into_php == 'no') {
      // do external PHP curl connection 
      exec("$curl_path -H 'Content-Type: application/x-www-form-urlencoded' -d \"$pnp_post_values\" https://pay1.plugnpay.com/payment/pnpremote.cgi", $pnp_result_page, $pnp_result_status);
      // NOTES:
      // -- The '-k' attribute can be added before the '-d' attribute to turn off curl's SSL certificate validation feature.
      // -- Only use the '-k' attribute if you know your curl path is correct & are getting back a blank response in $pnp_result_page.

      $pnp_result_decoded = urldecode($pnp_result_page[0]);
    }
    else {
      // do internal PHP curl connection
      // init curl handle
      $pnp_ch = curl_init($pnp_post_url);
      curl_setopt($pnp_ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($pnp_ch, CURLOPT_POSTFIELDS, $pnp_post_values);
      #curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  // Upon problem, uncomment for additional Windows 2003 compatibility

      // perform ssl post
      $pnp_result_page = curl_exec($pnp_ch);

      $pnp_result_decoded = urldecode($pnp_result_page);
    }

    // decode the result page and put it into transaction_array
    $pnp_temp_array = explode('&',$pnp_result_decoded);
    foreach ($pnp_temp_array as $entry) {
        list($name,$value) = explode('=',$entry);
        $pnp_transaction_array[$name] = $value;
    }

    /**************************************************************************
        UNLESS YOU KNOW WHAT YOU ARE DOING DO NOT CHANGE THE ABOVE CODE
    **************************************************************************/

    /*
       These statements handle the results for the transaction and where
       the customer is sent next.  If you don't want this script to handle
       the final transaction process set $pnp_handle_post_process='no' and
       it will be skipped.  You can edit the sepearate HTML files to look
       the way you want them to or each can be replaced with a php script.
       Your php scripts should use $pnp_transaction_array[] to check the
       transaction status.  All the documented plugnpay fields should be
       valid in pnp_transation_array.
    */

    $pnp_handle_post_process = 'yes';

    if ($pnp_handle_post_process != 'no') {
      if ($pnp_transaction_array['FinalStatus'] == 'success') {
        include('success.html');
      }
      elseif ($pnp_transaction_array['FinalStatus'] == 'badcard') {
        include('badcard.html');
      }
      elseif ($pnp_transaction_array['FinalStatus'] == 'fraud') {
        include('fraud.html');
      }
      elseif ($pnp_transaction_array['FinalStatus'] == 'problem') {
        include('problem.html');
      }
      else {
        // this should not happen
        include('error.html');
      }
    }
?>

