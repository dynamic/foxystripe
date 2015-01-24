# FoxyStripe

## Using FoxyStripe

### Product Groups

Product Groups are Pages in the CMS that display a list of related products. By default, Product Groups will show its child Product Pages.

A Product can also be added to multiple Product Groups via the Products tab on a Product Group Page. To enable this feature, go to Settings > FoxyStripe and check `Multi-Group Support`.

### Products

Products are managed as Pages in the CMS. To add a Product to a Product Group, create a new Product Page under a Product Group page in the Site Tree.

Once your Product Page is saved, it will include an Add to Cart form, allowing customers to add it to the FoxyCart shopping cart for purchase.

Note - all products are required to have a unique Product Code. FoxyCart will use this as a unique identifier in transactions.

#### Product Options

Product Options allow you to set modifiers to your products. Examples include:

*	Size - Small, Medium, Large
*	Color - Red, Blue, Green

By using the fields in the 'Modifiers' tab, you can change certain Product values if that option is selected. These include:

*	Weight - +/- shipping costs
*	Price - a hardcover book costs more than a paperback
*	Product Code - add a style number to your product code

These values and modifications will be passed to FoxyCart, and will be displayed on the order receipt.


#### FoxyCart Product Categories

Each Product asks you to assign a FoxyCart Product Category.

FoxyCart categories offer a way to give products additional behaviors that cannot be accomplished by product options alone. You can assign properties for groups of products, such as Taxes, Shipping and Coupon Codes.

Product Categories are created in your FoxyCart account under Products > Categories. You must also manually create this category in your FoxyStripe store.

In CMS > Products > FoxyCart Category:

1. Click `Add FoxyCart Category`
2. FoxyCart Category Description - enter value from FoxyCart > Category > Description
3. FoxyCart Category Code - enter value from FoxyCart > Category > Category code
4. Click `Create`

See [Product Categories](https://wiki.foxycart.com/v/2.0/categories) for more information on setting up categories in FoxyCart.

