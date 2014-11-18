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
                    <div class="sidebar size1of4 unit">
                        <h3>$TransactionDate.NiceUS</h3>
                        <p>
	                        <a href="$ReceiptURL" target="_blank">View Invoice</a><br>
	                        Order #{$Order_ID}<br>
	                        Total $OrderTotal.Nice
	                    </p>
                    </div>
                    <div class="size3of4 lastUnit">
	                    <% loop $Details %>
                            <div class="unit size2of5 productSummaryImage">
                                <% with Product %>
                                	<a href="{$Link}" title="{$Title}" class="anchor-fix product-image">
										$PreviewImage.PaddedImage(250, 250)
                                	</a>
                                <% end_with %>
                            </div>
                            <div class="unit size3of5 productSummaryText">
                                <% with Product %>
                                    <h3><a href="{$Link}" title="{$Title.XML}">$Title</a></h3>
                                <% end_with %>
                                <p>
                                    <b>Quantity</b>: $Quantity
                                    <% if $Options %>
                                        <br>
                                        <% loop $Options %>
                                            <b>{$ProductOptionGroup.Title}</b>: $Title<% if Last %><% else %><br><% end_if %>
                                        <% end_loop %>
                                    <% end_if %>
                                    <br>
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