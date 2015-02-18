# FoxyStripe

[![Build Status](https://travis-ci.org/dynamic/foxystripe.svg?branch=master)](https://travis-ci.org/dynamic/foxystripe)
[![Latest Stable Version](https://poser.pugx.org/dynamic/foxystripe/v/stable.svg)](https://packagist.org/packages/dynamic/foxystripe) [![Total Downloads](https://poser.pugx.org/dynamic/foxystripe/downloads.svg)](https://packagist.org/packages/dynamic/foxystripe) [![Latest Unstable Version](https://poser.pugx.org/dynamic/foxystripe/v/unstable.svg)](https://packagist.org/packages/dynamic/foxystripe) [![License](https://poser.pugx.org/dynamic/foxystripe/license.svg)](https://packagist.org/packages/dynamic/foxystripe)
[![Code Climate](https://codeclimate.com/github/dynamic/FoxyStripe/badges/gpa.svg)](https://codeclimate.com/github/dynamic/FoxyStripe)

FoxyStripe is a [SilverStripe](http://silverstripe.org) ecommerce module designed to integrate with [FoxyCart](http://www.foxycart.com/).

FoxyCart provides you with a flexible, powerful, customizable, and secure ecommerce platform. FoxyStripe provides a product catalog that integrates with the FoxyCart shopping cart and API.

Features include:

*	FoxyCart Add to Cart Form with Product Options and Modifiers
*	Customer Order History
*	Two-way Single Sign On with FoxyCart
*	HMAC Product Validation

Read more about [Using FoxyStripe](docs/en/Use.MD) and [Advanced Features](docs/en/Features.MD)


## Installation

### Requirements

*  SilverStripe 3.1.x
*  FoxyCart Store 2.x

### Composer Installation

`"require": { "dynamic/foxystripe": "dev-master" }`

### Git Installation

`git clone git@github.com:dynamic/FoxyStripe.git foxystripe`

### Manual Installation

Place this directory in the root of your SilverStripe installation, and rename the folder to 'foxystripe'.

### Recommended Add-Ons

The following add-ons are optional, but will enhance FoxyStripe when installed:

*	[GridField Better Buttons](http://addons.silverstripe.org/add-ons/unclecheese/betterbuttons)
*	[GridField Bulk Editing Tools](http://addons.silverstripe.org/add-ons/colymba/gridfield-bulk-editing-tools)
*	[Quick Add New](http://addons.silverstripe.org/add-ons/sheadawson/quickaddnew)
*	[Sortable GridField](http://addons.silverstripe.org/add-ons/undefinedoffset/sortablegridfield)

## Setup

Once FoxyStripe is installed, run a dev/build to setup the database.

### FoxyCart Setup

Login to your [FoxyCart Account](https://admin.foxycart.com/admin.php) and create a new store. See [Creating a FoxyCart Account](https://wiki.foxycart.com/v/2.0/getting_started/foxycart_setup) for detailed instructions.
	
### FoxyStripe Setup

Now that your store has been created, you'll need to grab some information from FoxyCart to configure your FoxyStripe site.

In CMS > Settings > FoxyStripe
	
1. Store Name - enter value from FoxyCart > Store > Settings > Store sub domain - ex: `myfoxystripestore`
2. Store Key - copy value to FoxyCart > Store > Advanced  > API key - ex: `xxxxxxxxxxxxx1234`

Your FoxyStripe store is now setup and ready for business. To get started, see [Using FoxyStripe](docs/en/Use.MD)

To set up additional features, such as Single Sign On and Order History, see [Advanced Features](docs/en/Features.MD)

### Security

While SSL is not required to use FoxyStripe, it is recommended that you run your production instance of FoxyStripe as SSL. This will prevent any 'insecure connection' warnings you may receive during the checkout process.

In `mysite/config_php`, add the following to force the site into SSL in live mode:

`if(Director::isLive()) {
	Director::forceSSL();
}`

## Additional Information

### FoxyCart Documentation

 * [FoxyCart 2.0 API Docs](https://wiki.foxycart.com/v/2.0/start)

### Maintainer Contact

 *  [Dynamic](http://www.dynamicdoes.com) (<info@dynamicdoes.com>)
   
### Credits

Inspired by previous work done by [cbryer](https://github.com/cbryer).

## License

	Copyright (c) 2014, Dynamic Inc
	All rights reserved.

	Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

	Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
	
	Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
	
	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
