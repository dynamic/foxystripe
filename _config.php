<?php

/** 
 * FoxyStripe config - Change password encryption to something compatible with FoxyCart
 */

Config::inst()->update('Security', 'password_encryption_algorithm', 'sha1_v2.4');