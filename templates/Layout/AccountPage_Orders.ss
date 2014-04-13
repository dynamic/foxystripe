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
                    <% loop $Products %>
                        <div class="unit size3of4 full">
                            <div class="unit size1of5">
                                <img src="$PreviewImage.PaddedImage(150,150).URL" class="product-image">
                            </div>
                            <div class="unit size4of5">
                                <h3><a href="{$Link}" title="{$Title.XML}">$Title</a></h3>
                                <div class="content"><p>{$Content.Summary}</p></div>
                                <p><a class="productLearnMore" href="$Link" alt="Learn More">Click here for more information</a></p>
                            </div>
                        </div>
                    <% end_loop %>
                </div>
            <% end_loop %>
        <% else %>
            <p>No past orders.</p>
        <% end_if %>

	</article>
</div>