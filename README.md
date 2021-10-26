# amp-braintree-clash

Demonstration of amp-form component killing the submit event listener required by Braintree payment processing.

I have set this up as a very basic demonstration with PHP and Composer for inclusion of the Braintree SDK. The use of PHP in this example is minimal, so changing to your preferred language should not be a problem: official SDKs are also supplied for Python, Ruby, Node.js, Java and .NET.

Register for a sandbox account with [Braintree](https://www.braintreepayments.com/sandbox) then update lines 17 - 19 of the amp-braintree.php script with your own merchant ID, public key and private key.

See: https://developer.paypal.com/braintree/docs/start/overview

Run amp-braintree.php and try credit card number 2223000048400011 with any future expiry date and CVV. With the amp-form component included on the page, the specified event listener for Braintree's tokenisation of the payment data does not run at all.

As per line 153, the console should log a clear message if the event listener fires, but at present lines 151 to 190 are not being invoked at all.

The page utilises Braintree's [Hosted Fields solution](https://developer.paypal.com/braintree/docs/start/hosted-fields), also including PayPal option and a 3DS component for card security.
