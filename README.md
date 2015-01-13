## FoxyStripe

[![Code Climate](https://codeclimate.com/github/dynamic/FoxyStripe/badges/gpa.svg)](https://codeclimate.com/github/dynamic/FoxyStripe)
[![Test Coverage](https://codeclimate.com/github/dynamic/FoxyStripe/badges/coverage.svg)](https://codeclimate.com/github/dynamic/FoxyStripe)
[![Build Status](https://travis-ci.org/dynamic/FoxyStripe.svg?branch=master)](https://travis-ci.org/dynamic/FoxyStripe)

FoxyStripe is a SilverStripe ecommerce module designed to integrate with [FoxyCart](http://www.foxycart.com/).

FoxyCart provides you with a flexible, powerful, customizable, and secure ecommerce platform. FoxyStripe provides a product catalog that integrates with the FoxyCart shopping cart and API.

Features include:

*	Add to Cart Form
*	Product Options
*	Order History
*	Single Sign On
*	HMAC Product Verification

### Installation

#### Requirements

*  SilverStripe 3.1.x

#### Composer Installation

`"require": { "dynamic/foxystripe": "dev-master }`

Add `foxystripe` to your `.gitignore` file.

#### Git Installation

`git clone git@github.com:dynamic/FoxyStripe.git foxystripe`

#### Recommended Add-Ons

The following add-ons are optional, but will enhance FoxyStripe when installed:

*	[GridField Better Buttons](http://addons.silverstripe.org/add-ons/unclecheese/betterbuttons)
*	[GridField Bulk Editing Tools](http://addons.silverstripe.org/add-ons/colymba/gridfield-bulk-editing-tools)
*	[Quick Add New](http://addons.silverstripe.org/add-ons/sheadawson/quickaddnew)
*	[Sortable GridField](http://addons.silverstripe.org/add-ons/undefinedoffset/sortablegridfield)

### Setup

Once FoxyStripe is installed, run a dev/build to setup the database.

#### FoxyCart Setup

Login to your [FoxyCart Account](https://admin.foxycart.com/admin.php) and create a new store. See [Creating a FoxyCart Account](https://wiki.foxycart.com/v/1.1/getting_started/foxycart_setup) for detailed instructions.
	
Once you've completed the initial FoxyCart setup, your store is ready to work with FoxyStripe. You'll want to revisit the FoxyCart admin to setup Shipping, Taxes, and additional Product Categories.

#### FoxyStripe Setup

Now that your store has been created, you'll need to grab some information from FoxyCart to configure your FoxyStripe site.

In CMS > Settings > FoxyStripe
	
1. Store Name - enter value from FoxyCart > Store > Settings > Store sub domain - ex: `myfoxystripestore`
2. Store Key - enter value from FoxyCart > Store > Advanced  > API key - ex: `xxxxxxxxxxxxx1234`

Your FoxyStripe store is now setup and ready for business. 


### Use



#### Product Groups

Product Groups are Pages in the CMS that display a list of related products. By default, Product Groups will show its child Product Pages.

A Product can also be added to multiple Product Groups via the Products tab on a Product Group Page. To enable this feature, go to Settings > FoxyStripe and check `Multi-Group Support`.

#### Products

Products are managed as Pages in the CMS. To add a Product to a Product Group, create a new Product Page under a Product Group page in the Site Tree.

Once your Product Page is saved, it will include an Add to Cart form, allowing customers to add it to the FoxyCart shopping cart for purchase.

Note - all products are required to have a unique Product Code. FoxyCart will use this as a unique identifier in transactions.

##### Product Options

Product Options allow you to set modifiers to your products. Examples include:

*	Size - Small, Medium, Large
*	Color - Red, Blue, Green

By using the fields in the 'Modifiers' tab, you can change certain Product values if that option is selected. These include:

*	Weight - +/- shipping costs
*	Price - a hardcover book costs more than a paperback
*	Product Code - add a style number to your product code

These values and modifications will be passed to FoxyCart, and will be displayed on the order receipt.


##### FoxyCart Product Categories

Each Product asks you to assign a FoxyCart Product Category.

FoxyCart categories offer a way to give products additional behaviors that cannot be accomplished by product options alone. You can assign properties for groups of products, such as Taxes, Shipping and Coupon Codes.

Product Categories are created in your FoxyCart account under Products > Categories. You must also manually create this category in your FoxyStripe store.

In CMS > Products > FoxyCart Category:

1. Click `Add FoxyCart Category`
2. FoxyCart Category Description - enter value from FoxyCart > Category > Description
3. FoxyCart Category Code - enter value from FoxyCart > Category > Category code
4. Click `Create`

See [Product Categories](https://wiki.foxycart.com/v/1.1/categories) for more information on setting up categories in FoxyCart.

#### Security

While SSL is not required to use FoxyStripe, it is recommended that you run your production instance of FoxyStripe as SSL. This will prevent any 'insecure connection' warnings you may receive during the checkout process.

In `mysite/config_php`, add the following to force the site into SSL in live mode:

`if(Director::isLive()) {
	Director::forceSSL();
}`

### Additional Features

#### FoxyCart DataFeed

Next we will setup your store's DataFeed, which allows FoxyCart to push transaction and user information to your FoxyStripe site. This is required for the more advanced features listed below, such as Single Sign On and Order History

In CMS > Settings > FoxyStripe

1. Copy `FoxyCart DataFeed URL` to your clipboard - `https://myfoxystripestore.com/foxycart`

In FoxyCart > Store > Advanced:

1. click 'would you like to enable your store's datafeed?' to enable the DataFeed
2. Datafeed URL -  paste value from FoxyStripe

Your FoxyCart store will now be able to communicate with your FoxyStripe site.

#### Single Sign On

Enabling single sign on will sync user account information between your SilverStripe site and FoxyCart. If the user is logged into your SilverStripe site prior to checkout, they will be auto logged in to FoxyCart.

You can enable this feature by doing the following:

1. In CMS > Settings > FoxyStripe:
	*	Check 'Enable Single Sign On'
	*	Copy `Single Sign On URL` to your clipboard - `https://myfoxystripestore.com/foxycart/sso`
2. In FoxyCart > Store > Advanced:
	* 	Check `enable single sign on`
	*	Single sign on url - paste the value from FoxyStripe
	*	Customer password hash type - select `SHA-1, salted (suffix)`
	*	Customer password has config - enter `40`
	
This will setup a two way sync of user information between FoxyCart and FoxyStripe. If a user is created or modified in FoxyStripe, it will push that info via FoxyCart's API. If the user is created or modified during a FoxyCart transaction, FoxyStripe will receive the info via FoxyCart's DataFeed.

[Member Profiles](http://addons.silverstripe.org/add-ons/ajshort/silverstripe-memberprofiles) is recommended for user login/registration, but any member management system should work with FoxyStripe.

#### Order History

If you have Single Sign On enabled, and have setup a user login/registration system, you can allow your customers to view their order history in your FoxyStripe site.

Simply create an Order History page in the appropriate area of your Site Tree. Once logged in, Customers will be able to review their order history, and access receipt from FoxyCart.

#### HMAC Product Validation

HMAC Product Validation will encrypt the add to cart forms on Product Pages. This prevents a user from manipulating the form prior to adding to cart. 

To enable HMAC Product Validation in FoxyStripe:

1. In FoxyCart > Store > Advanced:
	*	Check `would you like to enable cart validation?'
2. In CMS > Settings > FoxyStripe:
	*	Check 'enable cart validation'

### Maintainer Contact

 *  [Dynamic](http://www.dynamicdoes.com) (<info@dynamicdoes.com>)
 
### FoxyCart Documentation

[FoxyCart 2.0 API Docs](https://wiki.foxycart.com/v/2.0/start)
 
[FoxyCart 1.1 API Docs](https://wiki.foxycart.com/v/1.1/start)
 
### Credits

Inspired by previous work done by [cbryer](https://github.com/cbryer).

### License

	Copyright (c) 2014, Dynamic Inc
	All rights reserved.

	Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

	Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
	
	Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
	
	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
