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

    Fatal error: Call to undefined function: curl_init() in "your script here"

  It is because your installation of PHP was not configured to support curl.
  Contact your system administrator to have curl setup.

  Version 1.01

  ChangeLog:

  v1.00
    Develop Chicken Pox
    Get bored.
    Learn PHP.
    Write the module.
    Total Time 15 minutes.

  v1.00 - 10/08/2003
    Updated code for generating pnp_transaction_array so it
    doesn't include junk from parsing the results.  Thanks art.
    This will not affect old code and should work the same.

  v1.01 - 10/05/2004
    Add ability to use curl, even when curl is not compiled into PHP
    Added code to select if curl is compiled or not into PHP & set the curl file path
    Added curl parameter which can be uncommented for windows 2003 compatibility
    Updated some of the comments to help users understand what us happening & what to do

  v1.01 - 01/14/2005
    Fixed non-compile curl usage error with returned array & decoding

  v1.01 - 06/02/2005
    Updated format of the response HTML pages
    Updated response message in error.html page.

  v1.01 - 01/05/2012
    Applied cipher tweak to curl call, to fix issue with CentOS 6 servers

  v1.01 - 10/09/2015
    Rolled back cipher tweak, to let curl use new stronger ciphers

*******************************************************************************/
    /*
      Set script parameters & answer questions down here...
    */

    // Is curl complied into PHP? 
    $is_curl_compiled_into_php = "yes"; 
    // Possible answers are: 
    //  'yes' -> means that curl is compiled into PHP [DEFAULT]
    //  'no'  -> means that curl is not-compiled into PHP & must be called externally

    // If you selected 'no' to the above question, then set the absolute path to curl
    $curl_path = "/usr/bin/curl";
    // [e.g.: '/usr/bin/curl' on Unix/Linux or 'c:/curl/curl.exe' on Windows servers] 
    // If you are unsure of this, check with your hosting company.

    // Set URL that you will post the transaction to
    $pnp_post_url = "https://pay1.plugnpay.com/payment/pnpremote.cgi";
    // This should never need to be changed...


    /*
      This is where you build the query string to be posted to plugnpay.  You
      can replace this code with your own or you need to follow the instructions
      in the README file for calling this script.
    */

    if ($pnp_post_values == "") {
        $pnp_post_values .= "publisher-name=" . $publisher_name . "&";
        $pnp_post_values .= "card-number=" . $card_number . "&";
        $pnp_post_values .= "card-cvv=" . $card_cvv . "&";
        $pnp_post_values .= "card-exp=" . $card_exp . "&";
        $pnp_post_values .= "card-amount=" . $card_amount . "&";
        $pnp_post_values .= "card-name=" . $card_name . "&";
        $pnp_post_values .= "email=" . $email . "&";
        $pnp_post_values .= "ipaddress=" . $email . "&";
        // billing address info
        $pnp_post_values .= "card-address1=" . $card_address1 . "&";
        $pnp_post_values .= "card-address2=" . $card_address2 . "&";
        $pnp_post_values .= "card-zip=" . $card_zip . "&";
        $pnp_post_values .= "card-city=" . $card_city . "&";
        $pnp_post_values .= "card-state=" . $card_state . "&";
        $pnp_post_values .= "card-country=" . $card_country . "&";
        // shipping address info
        $pnp_post_values .= "shipname=" . $shipname . "&";
        $pnp_post_values .= "address1=" . $card_address1 . "&";
        $pnp_post_values .= "address2=" . $card_address2 . "&";
        $pnp_post_values .= "zip=" . $card_zip . "&";
        $pnp_post_values .= "state=" . $card_state . "&";
        $pnp_post_values .= "country=" . $card_country . "&";
    }


    /**************************************************************************
      UNLESS YOU KNOW WHAT YOU ARE DOING YOU SHOULD NOT CHANGE THE BELOW CODE
    **************************************************************************/

    if ($is_curl_compiled_into_php == "no") {
      // do external PHP curl connection 
      exec("$curl_path -d \"$pnp_post_values\" https://pay1.plugnpay.com/payment/pnpremote.cgi", $pnp_result_page);
      // NOTES:
      // -- The '-k' attribute can be added before the '-d' attribute to turn off curl's SSL certificate validation feature.
      // -- Only use the '-k' attribute if you know your curl path is correct & are getting back a blank response in $pnp_result_page.

      $pnp_result_decoded = urldecode($pnp_result_page[1]);
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
    $pnp_temp_array = split('&',$pnp_result_decoded);
    foreach ($pnp_temp_array as $entry) {
        list($name,$value) = split('=',$entry);
        $pnp_transaction_array[$name] = $value;
    }

    /**************************************************************************
        UNLESS YOU KNOW WHAT YOU ARE DOING DO NOT CHANGE THE ABOVE CODE
    **************************************************************************/


    /*
       These statements handle the results for the transaction and where
       the customer is sent next.  If you don't want this script to handle
       the final transaction process set $pnp_handle_post_process="no" and
       it will be skipped.  You can edit the sepearate HTML files to look
       the way you want them to or each can be replaced with a php script.
       Your php scripts should use $pnp_transaction_array[] to check the
       transaction status.  All the documented plugnpay fields should be
       valid in pnp_transation_array.
    */

    if ($pnp_handle_post_process != "no") {
      if ($pnp_transaction_array['FinalStatus'] == "success") {
        include("success.html");
      }
      elseif ($pnp_transaction_array['FinalStatus'] == "badcard") {
        include("badcard.html");
      }
      elseif ($pnp_transaction_array['FinalStatus'] == "fraud") {
        include("fraud.html");
      }
      elseif ($pnp_transaction_array['FinalStatus'] == "problem") {
        include("problem.html");
      }
      else {
        // this should not happen
        include("error.html");
      }
    }
?>
