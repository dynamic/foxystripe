<?php

/** enter your store name which will become the subdomain for your foxycart store
 * example:
 * to register a store with a subdomain of foxycart-store-name.foxycart.com, enter
 * 
 * FoxyCart::setFoxyCartStoreName('foxycart-store-name');
 * 
 *
 */

Config::inst()->update('Security', 'password_encryption_algorithm', 'sha1_v2.4');