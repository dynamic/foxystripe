# FoxyStripe

[![Latest Stable Version](https://poser.pugx.org/dynamic/foxystripe/version)](https://packagist.org/packages/dynamic/foxystripe)
[![Latest Unstable Version](https://poser.pugx.org/dynamic/foxystripe/v/unstable)](//packagist.org/packages/dynamic/foxystripe)
[![Total Downloads](https://poser.pugx.org/dynamic/foxystripe/downloads)](https://packagist.org/packages/dynamic/foxystripe)
[![License](https://poser.pugx.org/dynamic/foxystripe/license)](https://packagist.org/packages/dynamic/foxystripe)

FoxyStripe is a [SilverStripe](http://silverstripe.org) ecommerce module designed to integrate with [FoxyCart](http://www.foxycart.com/).

FoxyCart provides you with a flexible, powerful, customizable, and secure ecommerce platform. FoxyStripe provides a product catalog that integrates with the FoxyCart shopping cart and API.

Features include:

*	FoxyCart Add to Cart Form with Product Options and Modifiers
*	Customer Order History
*	Two-way Single Sign On with FoxyCart
*	HMAC Product Validation

Read more about [Using FoxyStripe](docs/en/userguide/index.md), [Advanced Features](docs/en/userguide/features.md) and [Migrating from SilverStripe 3.x to 4.x](docs/en/userguide/migration.md)


## Installation

`composer require dynamic/foxystripe`

## Requirements

*  SilverStripe 4.x
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

 *  [Dynamic](http://www.dynamicagency.com) (<dev@dynamicagency.com>)
   
### Credits

Inspired by previous work done by [cbryer](https://github.com/cbryer).

## Documentation

See the [docs/en](docs/en/index.md) folder.