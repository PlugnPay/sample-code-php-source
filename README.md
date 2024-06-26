# sample-code-php-source
PHP API - cURL

**************************************************************************
** This code is NOT officially support by Plug 'n Pay Technologies Inc. **
**************************************************************************

You will need to understand the 'Remote Client Specification' documentation
in order to use this module.  It is located in the 'Documentation/FAQ' section
of the admin area.

Requirements:

  -- PHP

  -- Curl (having curl compiled into PHP is optional, but recommended)

  For those wishing to compile curl into PHP, get curl from 'http://curl.haxx.se/'
  Then configure PHP with the '--with-curl' option, plus all the options
  you used previously and rebuild; then install it.  Curl has a few
  other requirements like openssl for one.  Be sure you have everything you need.


Please test your PHP & curl setup prior to using this module.


There are two methods for using this script.

  1. Post directly to it from an HTML form.

  2. Can be evaluated by another PHP script including it.


Troubleshooting:

Check the uploaded file's permissions:
-- .php files should be chmod 755
   (read/write/execute by owner, read/execute by all others)
-- .html files should be chmod 644
   (read/write by owner, read only by all others)

When processing a transaction and it fails, there should be a PnP generated error
message in the response string.  This would tell the you why PnP could not process
the order.  If the response string is blank, then you should check your curl connection.


To check the curl connection, you can check it by accessing your server via a shell account
(SSH/Telnet) & manually running a curl connection.
Run the following command from the command line of the server's shell and see if you
get a response string.


   /usr/bin/curl -d "publisher-name=pnpdemo&amp;publisher-email=trash%40plugnpay.com&amp;mode=auth
   &amp;card-name=cardtest&amp;card-number=4111111111111111&amp;card-exp=0105&amp;card-cvv=123
   &amp;card-amount=1.23" https://pay1.plugnpay.com/payment/pnpremote.cgi


 * NOTES:
 -- THE ABOVE COMMAND SHOULD ALL BE ON ONE LINE
 -- You should adjust the path to curl ('/usr/bin/curl') to whatever your server uses.
 -- You should get a response string similar to the one below.

 You should get a response string should look something like this, if it was successful.


   FinalStatus=success&amp;IPaddress=192%2e168%2e1%2e2&amp;MStatus=success&amp;User%2dAgent=
   url%2f7%2e11%2e1%20%28i386%2dredhat%2dlinux%2dgnu%29%20libcurl%2f7%2e11%2e1%20OpenSSL
   %2f0%2e9%2e7a%20ipv6%20zlib%2f1%2e2%2e1%2e1&amp;auth%2dcode=TSTAUT&amp;auth%2dmsg=%2000%3a&amp;
   auth_date=20040922&amp;avs%2dcode=U&amp;card%2damount=1%2e23&amp;card%2dname=cardtest&amp;currency=usd
   &amp;cvvresp=M&amp;easycart=0&amp;merchant=pnpdemo&amp;mode=auth&amp;orderID=2004092215542323043&amp;
   publisher%2demail=trash%40plugnpay%2ecom&amp;publisher%2dname=pnpdemo&amp;resp%2dcode=00&amp;
   shipinfo=0&amp;sresp=A&amp;success=yes&amp;transflags=retail&amp;MErrMsg=00%3a&amp;a=b

 * NOTES:
 -- THE ABOVE RESPONSE SHOULD ALL BE ON ONE LINE

If you get a certificate validation error, you can enter '-k' attribute between the path
to curl and the '-d' parameter.  The '-k' parameter will turn off the SSL certificate
validation.

If you get a blank response there is most likely a firewall which is internal to
your server/network which is preventing curl from contacting our servers.

If curl complied into your PHP is not working, but the command line curl connection
worked, select that curl is not compiled into PHP and set the same path to curl you used
in your command line test.

If this still hasn't fixed the issue, here are a few other suggestions.  You may want
to check the following:

  * If selected curl not compiled into PHP, your path to curl is correctly inputted.

  * If you selected curl compiled into PHP, check if curl is in fact compiled and working.

  * Check to see if your SSL is working and updated with the latest certificates.

  * Your PlugnPay account's username inputted correctly into the publisher-name field.

  * Your PlugnPay account is actually 'Live', so you can process real credit card transactions.

  * You have properly set up your PlugnPay account via your admin area, including setting
    all your Fraud settings and make sure your account is 'Live'.

  * If you are trying to do use a mode other then 'auth', you must registered your server's IP
    address in your PnP account's Security Administration area.

  * You must passing your account's Remote Client Password in the 'publisher-password' field.

  * If trying to process electronic checks, make sure you have an electronic checking
    account setup with PlugnPay before starting to asking for/accept electronic check
    payments.

Also, realize that this code itself is not very complex, and practically every person
that has contacted us about errors found that some other code not associated with this
contribution was responsible, or because their curl implementation was not properly set
up or working.  Do your own debugging before contacting anyone for assistance.

