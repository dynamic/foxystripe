<%-- redeclare Simple theme includes to keep correct inclusion order --%>
<% require themedCSS('reset') %>
<% require themedCSS('typography') %>
<% require themedCSS('layout') %>
<%-- FoxyStripe requirements --%>
<% require css('foxystripe/css/foxycart.css') %>


<div class="content-container unit ProductPage">
	<article>
		<h1>$Title</h1>
		
		<% if $Content %><div class="typography">$Content</div><% end_if %>

        <% if $Orders %>
            <% loop $Orders %>
                <div class="historySummary line">
                    <div class="size1of4 unit">
                        <h3>Order #{$Order_ID}</h3>
                        <p>
                            $TransactionDate.NiceUS | <a href="$ReceiptURL" target="_blank">View Invoice</a>
                        </p>

                        <table width="100%">
                            <tr>
                                <td><b>Sub Total</b></td>
                                <td align="right" width="50%">$ProductTotal.Nice</td>
                            </tr>
                            <tr>
                                <td><b>Shipping</b></td>
                                <td align="right" width="50%">$ShippingTotal.Nice</td>
                            </tr>
                            <tr>
                                <td><b>Tax</b></td>
                                <td align="right" width="50%">$TaxTotal.Nice</td>
                            </tr>
                            <tr>
                                <td><b>Total</b></td>
                                <td align="right" width="50%"><b>$OrderTotal.Nice</b></td>
                            </tr>
                        </table>
                    </div>
                    <div class="size3of4 lastUnit">
                        <h3>Your Items</h3>
	                    <% loop $Details %>
                            <div class="unit size1of5 productSummaryImage">
                                <% if $Product %>
                                	<a href="{$Product.Link}" title="{$Product.Title}" class="anchor-fix product-image">
                                <% end_if %>
                                <img src="$ProductImage">
                                <% if $Product %></a><% end_if %>
                            </div>
                            <div class="unit size4of5 productSummaryText">
                                <h3>
                                    <% if $Product %><a href="{$Product.Link}" title="{$ProductName.XML}"><% end_if %>
                                        $ProductName
                                    <% if $Product %></a><% end_if %>
                                </h3>
                                <p>
                                    <% if $OrderOptions %>
                                        <% loop $OrderOptions %>
                                            <b>$Name</b>: $Value<br>
                                        <% end_loop %>
                                    <% end_if %>
                                    <b>Quantity</b>: $Quantity<br>
                                    <b>Price:</b> $Price.Nice
                                </p>
                            </div>
                            <br style="clear: both;">
	                    <% end_loop %>
                    </div>
                </div>
            <% end_loop %>

            <% with $Orders %>
                <% include Pagination %>
            <% end_with %>

        <% else %>
            <p>No past orders.</p>
        <% end_if %>

	</article>
</div>