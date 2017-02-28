#FoxyStripe

## Advanced Features

### FoxyCart DataFeed

Next we will setup your store's DataFeed, which allows FoxyCart to push transaction and user information to your FoxyStripe site. This is required for the more advanced features listed below, such as Single Sign On and Order History

In CMS > Settings > FoxyStripe

1. Copy `FoxyCart DataFeed URL` to your clipboard - `https://myfoxystripestore.com/foxystripe/`

In FoxyCart > Store > Advanced:

1. click 'would you like to enable your store's datafeed?' to enable the DataFeed
2. Datafeed URL -  paste value from FoxyStripe

Your FoxyCart store will now be able to communicate with your FoxyStripe site.

### Single Sign On

Enabling single sign on will sync user account information between your SilverStripe site and FoxyCart. If the user is logged into your SilverStripe site prior to checkout, they will be auto logged in to FoxyCart.

You can enable this feature by doing the following:

1. In CMS > Settings > FoxyStripe:
	*	Check 'Enable Single Sign On'
	*	Copy `Single Sign On URL` to your clipboard - `https://myfoxystripestore.com/foxystripe/sso/`
2. In FoxyCart > Store > Advanced:
	* 	Check `enable single sign on`
	*	Single sign on url - paste the value from FoxyStripe
	*	Customer password hash type - select `SHA-1, salted (suffix)`
	*	Customer password has config - enter `40`
	
This will setup a two way sync of user information between FoxyCart and FoxyStripe. If a user is created or modified in FoxyStripe, it will push that info via FoxyCart's API. If the user is created or modified during a FoxyCart transaction, FoxyStripe will receive the info via FoxyCart's DataFeed.

[Member Profiles](http://addons.silverstripe.org/add-ons/silverstripe-australia/memberprofiles) is recommended for user login/registration, but any member management system should work with FoxyStripe.

### Order History

If you have Single Sign On enabled, you can allow your customers to view their order history in your FoxyStripe site.

Simply create an Order History page in the appropriate area of your Site Tree. Once logged in, Customers will be able to review their order history, and access receipts from FoxyCart.

### HMAC Product Validation

HMAC Product Validation will encrypt the add to cart forms on Product Pages. This prevents a user from manipulating the form prior to adding to cart. 

To enable HMAC Product Validation in FoxyStripe:

1. In FoxyCart > Store > Advanced:
	*	Check `would you like to enable cart validation?'
2. In CMS > Settings > FoxyStripe:
	*	Check 'enable cart validation'
	
### Security

While SSL is not required to use FoxyStripe, it is recommended that you run your production instance of FoxyStripe as SSL. This will prevent any 'insecure connection' warnings you may receive during the checkout process.

In `mysite/config_php`, add the following to force the site into SSL in live mode:

`if(Director::isLive()) {
	Director::forceSSL();
}`

	