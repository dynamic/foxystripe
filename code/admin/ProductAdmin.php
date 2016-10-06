<?php

/**
 * Class ProductAdmin
 */
class ProductAdmin extends ModelAdmin
{

    /**
     * @var string
     */
    private static $url_segment = 'products';

    /**
     * @var string
     */
    private static $menu_title = 'Products';

    /**
     * @var array
     */
    private static $managed_models = [
        'FoxyStripeProduct' => [
            'title' => 'Product',
        ],
    ];

}