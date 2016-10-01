# FoxyStripe
[![Build Status](https://travis-ci.org/dynamic/foxystripe.svg?branch=master)](https://travis-ci.org/dynamic/foxystripe)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/dynamic/foxystripe/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/dynamic/foxystripe/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/dynamic/foxystripe/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/dynamic/foxystripe/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/dynamic/foxystripe/badges/build.png?b=master)](https://scrutinizer-ci.com/g/dynamic/foxystripe/build-status/master)
[![codecov](https://codecov.io/gh/dynamic/foxystripe/branch/master/graph/badge.svg)](https://codecov.io/gh/dynamic/foxystripe)

[![Latest Stable Version](https://poser.pugx.org/dynamic/foxystripe/version)](https://packagist.org/packages/dynamic/foxystripe)
[![Latest Unstable Version](https://poser.pugx.org/dynamic/foxystripe/v/unstable)](//packagist.org/packages/dynamic/foxystripe)
[![Total Downloads](https://poser.pugx.org/dynamic/foxystripe/downloads)](https://packagist.org/packages/dynamic/foxystripe)
[![License](https://poser.pugx.org/dynamic/foxystripe/license)](https://packagist.org/packages/dynamic/foxystripe)
[![Monthly Downloads](https://poser.pugx.org/dynamic/foxystripe/d/monthly)](https://packagist.org/packages/dynamic/foxystripe)
[![Daily Downloads](https://poser.pugx.org/dynamic/foxystripe/d/daily)](https://packagist.org/packages/dynamic/foxystripe)

[![Dependency Status](https://www.versioneye.com/php/dynamic:foxystripe/badge.svg)](https://www.versioneye.com/php/dynamic:foxystripe)
[![Reference Status](https://www.versioneye.com/php/dynamic:foxystripe/reference_badge.svg?style=flat)](https://www.versioneye.com/php/dynamic:foxystripe/references)

FoxyStripe is a [SilverStripe](http://silverstripe.org) ecommerce module designed to integrate with [FoxyCart](http://www.foxycart.com/).

FoxyCart provides you with a flexible, powerful, customizable, and secure ecommerce platform. FoxyStripe provides a product catalog that integrates with the FoxyCart shopping cart and API.

Features include:

*	FoxyCart Add to Cart Form with Product Options and Modifiers
*	Customer Order History
*	Two-way Single Sign On with FoxyCart
*	HMAC Product Validation

Read more about [Using FoxyStripe](docs/en/Use.MD) and [Advanced Features](docs/en/Features.MD)


## Installation

`composer require dynamic/foxystripe`

## Requirements

*  SilverStripe ^3.1
*  FoxyCart Store 2.x

## Setup

Once FoxyStripe is installed, run a dev/build to setup the database.

### FoxyCart Setup

Login to your [FoxyCart Account](https://admin.foxycart.com/admin.php) and create a new store. See [Creating a FoxyCart Account](https://wiki.foxycart.com/v/2.0/getting_started/foxycart_setup) for detailed instructions.
	
### FoxyStripe Setup

Now that your store has been created, you'll need to grab some information from FoxyCart to configure your FoxyStripe site.

In CMS > Settings > FoxyStripe
	
1. Store Name - enter value from FoxyCart > Store > Settings > Store sub domain - ex: `myfoxystripestore`
2. Store Key - copy value to FoxyCart > Store > Advanced  > API key - ex: `xxxxxxxxxxxxx1234`

Your FoxyStripe store is now setup and ready for business. To get started, see [Using FoxyStripe](docs/en/userguide/index.md)

To set up additional features, such as Single Sign On and Order History, see [Advanced Features](docs/en/userguide/features.md)

## Additional Information

### FoxyCart Documentation

 * [FoxyCart 2.0 API Docs](https://wiki.foxycart.com/v/2.0/start)

### Maintainer Contact

 *  [Dynamic, Inc](http://www.dynamicagency.com) (<dev@dy.ag>)
   
### Credits

Inspired by previous work done by [cbryer](https://github.com/cbryer).

## Documentation

See the [docs/en](docs/en/index.md) folder.