<% require css('foxystripe/css/foxycart.css') %>
<div class="content-container unit size3of4 lastUnit">
	<article>
		<h1>$Title</h1>
		
		<% if $Content %><div class="typography">$Content</div><% end_if %>

        <% if $Orders %>
            <% loop $Orders %>
                <div class="historySummary unit">
                    <div class="unit size1of4 sidebar">
                        <h3>$TransactionDate.NiceUS</h3>
                        <p><a href="$ReceiptURL" target="_blank">View Invoice</a></p>
                        <p>Order #{$Order_ID}</p>
                        <p>Total $OrderTotal.Nice</p>
                    </div>
                    <div class="unit size3of4 full">
	                    <% loop $Details %>
                            <div class="unit size1of5">
                                <% with Product %>
                                    <img src="$PreviewImage.PaddedImage(150,150).URL" class="product-image">
                                <% end_with %>
                            </div>
                            <div class="unit size4of5">
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