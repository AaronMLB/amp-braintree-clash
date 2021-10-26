<?php

if (!empty($_POST)) {
  header('Content-type: application/json');
  header('Access-Control-Allow-Credentials: true');
  header('Access-Control-Allow-Origin: ' . urldecode($_GET['__amp_source_origin']));
  header('AMP-Access-Control-Allow-Source-Origin: ' . urldecode($_GET['__amp_source_origin']));
  header('Access-Control-Expose-Headers: AMP-Access-Control-Allow-Source-Origin');
  echo json_encode($_POST);
  exit;
}

require_once('vendor/autoload.php');

$gateway = new \Braintree\Gateway([
  'environment' => 'sandbox',
  'merchantId' => '',
  'publicKey' => '',
  'privateKey' => '',
]);

?><!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <script async src="https://cdn.ampproject.org/v0.js"></script>
    <script async custom-element="amp-form" src="https://cdn.ampproject.org/v0/amp-form-0.1.js"></script>
    <title>Form event listener test</title>
    <link rel="canonical" href="amp-braintree.php">
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
    <style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>
    <style amp-custom>
      body {
        padding: 1rem
      }
    </style>
  </head>
  <body>
    <h1>Fom event listener test</h1>
    <form id="theForm" method="post" action-xhr="/amp-braintree.php" target="_top">
      <input type="hidden" name="nonce">
      <input type="hidden" name="device-data">
      <div id="cardTab" role="tab" aria-controls="cardContainer" option selected><h3>Credit/debit card</h3></div>
        <div id="cardContainer" role="tabpanel" aria-labelledby="cardTab">
          <div class="group">
            <label class="label" for="cardholderName">Name on card</label>
            <div class="input">
              <input type="text" name="cardholderName" id="cardholderName" placeholder="Cardholder's name">
            </div>
          </div>

          <div class="group">
            <label class="label" for="company">Company <small>if applicable</small></label>
            <div class="input">
              <input type="text" name="company" id="company" placeholder="Name of company (if applicable)">
            </div>
          </div>

          <div class="group">
            <label class="label" for="card-number">Card number</label>
            <div class="input">
              <div id="card-number"></div>
            </div>
            <span class="helper-text"></span>
          </div>

          <div class="group">
            <label class="label" for="expiration-date">Expiry date</label>
            <div class="input">
              <div class="card-input" id="expiration-month"></div>
              <div class="card-input" id="expiration-year"></div>
            </div>
          </div>

          <div class="group">
            <label class="label" for="cvv">CVV</label>
            <div class="input">
              <div id="cvv"></div>
            </div>
          </div>
        </div>

        <div id="payPalTab" role="tab" aria-controls="payPalContainer" option><h3>PayPal account</h3></div>
        <div id="payPalContainer" role="tabpanel" aria-labelledby="payPalTab">
          <div class="paypal-button"></div>
          <div class="paypal-account"></div>
        </div>
        <input type="submit" value="Submit">
    </form>
    <script src="https://js.braintreegateway.com/web/3.82.0/js/client.min.js"></script>
    <script src="https://js.braintreegateway.com/web/3.82.0/js/hosted-fields.min.js"></script>
    <script src="https://js.braintreegateway.com/web/3.82.0/js/data-collector.min.js"></script>
    <script src="https://js.braintreegateway.com/web/3.82.0/js/three-d-secure.min.js"></script>
    <script src="https://js.braintreegateway.com/web/3.82.0/js/paypal-checkout.min.js"></script>
    <script>
      var $token = "<?php echo $gateway->clientToken()->generate(); ?>";
      var methodForm = document.getElementById('theForm');
      var threeDSecure = null;

      braintree.client.create({
        authorization: $token
      }, function(err, client) {
        if (err) {
          console.error(err);
          return;
        }
      
        braintree.hostedFields.create({
          client: client,
          fields: {
            number: {
              selector: '#card-number',
              placeholder: '•••• •••• •••• ••••',
            },
            cvv: {
              selector: '#cvv',
              placeholder: '123',
            },
            expirationMonth: {
              selector: '#expiration-month',
              placeholder: 'Month',
              select: {
                options: [
                  '01 - January',
                  '02 - February',
                  '03 - March',
                  '04 - April',
                  '05 - May',
                  '06 - June',
                  '07 - July',
                  '08 - August',
                  '09 - September',
                  '10 - October',
                  '11 - November',
                  '12 - December'
                ]
              }
            },
            expirationYear: {
              selector: '#expiration-year',
              placeholder: 'Year',
              select: true
            }
          }
        }, function(err, hostedFields) {
          if (err) {
            console.log('There was an error. Please go back and try to proceed to checkout again.');
            return;
          }

          methodForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Listener fired.');

            if (document.querySelector('input[name="method"]').value != 'paypal') {
              hostedFields.tokenize(function (tokenizeErr, payload) {
                if (tokenizeErr) {
                  console.log('Please check your card details. We received this error message from our payment processor: ' + tokenizeErr.message);
                  return;
                }

                threeDSObj = {
                  nonce: payload.nonce,
                  bin: payload.details.bin,
                  onLookupComplete: function (data, next) {
                    // Use data here, then call next()
                    next();
                  }
                };

                threeDSecure.verifyCard(threeDSObj, function (err, response) {
                  if (err) {
                    console.log('There was an error authorising your payment. Please double-check your billing and payment details, then attempt to check out again.');
                    return false;
                  }

                  // If eligible for 3DS, check it didn't fail
                  if (response.liabilityShiftPossible && !response.liabilityShifted) {
                    console.log('Security check failed. Please try again, or choose a different payment method.');
                    return false;
                  }

                  // Success! Submit the form
                  document.querySelector('input[name="nonce"]').value = response.nonce;
                });

              });
            }
            methodForm.submit();
          });
        });
        
        // Set up the device data collector for security.
        braintree.dataCollector.create({
          client: client,
          kount: true
        }, function (err, dataCollectorInstance) {
          if (err) {
            // Handle error in creation of data collector
            console.log('Data collector could not be created.');
            return;
          }
          // At this point, you should access the dataCollectorInstance.deviceData value and provide it
          // to your server, e.g. by injecting it into your form as a hidden input.
          var deviceData = dataCollectorInstance.deviceData;
          document.querySelector('input[name="device-data"]').value = deviceData;
        });
            
        // Set up the 3DS support.
        braintree.threeDSecure.create({
          client: client,
          version: 2,
        }, function (err, threeDSecureInstance) {
          if (err) {
            // Handle error in 3D Secure component creation
            console.log('3DS could not be initialised.');
            return;
          }

          threeDSecure = threeDSecureInstance;
        });

        // Create a PayPal Checkout component.
        braintree.paypalCheckout.create({
          client: client
        }, function (paypalCheckoutErr, paypalCheckoutInstance) {
        
          // Stop if there was a problem creating PayPal Checkout.
          // This could happen if there was a network error or if it's incorrectly
          // configured.
          if (paypalCheckoutErr) {
            console.error('Error creating PayPal Checkout:', paypalCheckoutErr);
            return;
          }
        
          paypalCheckoutInstance.loadPayPalSDK({
            vault: true,
            commit: false
          }, function (loadSDKErr) {
            // The PayPal script is now loaded on the page and
            // window.paypal.Buttons is now available to use
            
            return paypal.Buttons({
              fundingSource: paypal.FUNDING.PAYPAL,
              style: {
                tagline: false,
                layout: 'horizontal',
              },
          
              createBillingAgreement: function () {
                return paypalCheckoutInstance.createPayment({
                  flow: 'vault',
                  currency: 'GBP',
                  locale: 'en_GB',
                  vault: true
                });
              },
          
              onApprove: function (data, actions) {
                return paypalCheckoutInstance.tokenizePayment(data, function (err, payload) {
                  document.querySelector('input[name="nonce"]').value = payload.nonce;
                });
              },
          
              onCancel: function (data) {
                //console.log('PayPal payment canceled', JSON.stringify(data, 0, 2));
              },
          
              onError: function (err) {
                console.error('PayPal error', err);
              }
            }).render('.paypal-button').then(function () {
              // The PayPal button will be rendered in an html element with the ID
              // `paypal-button`. This function will be called when the PayPal button
              // is set up and ready to be used
              //console.log('PayPal complete!');
            });
          });
        });
      });
    </script>
  </body>
</html>
