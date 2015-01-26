<%-- redeclare Simple theme includes to keep correct inclusion order --%>
<% require themedCSS('reset') %>
<% require themedCSS('typography') %>
<% require themedCSS('layout') %>
<%-- FoxyStripe requirements --%>
<% require css('foxystripe/css/foxycart.css') %>

<% include SideBar %>
<div class="content-container unit size3of4 lastUnit">
	<section>
	    <h1>$Title</h1>

        <% if $Content %><div class="content">$Content</div><% end_if %>

        <% if $ProductList %>
            <% loop $ProductList %>
                <div class="unit size1of1 productSummary">
                    <div class="unit size1of4 productSummaryImage">
                        <% if $PreviewImage %>
                            <a href="{$Link}" title="<% if $ReceiptTitle %>{$ReceiptTitle}<% else %>{$Title}<% end_if %>" class="anchor-fix product-image">
                                $PreviewImage.PaddedImage(250, 250)
                            </a>
                        <% else %>
                            <%-- placeholder image --%>
                            &nbsp;
                        <% end_if %>
                    </div>
                    <div class="unit size3of4 productSummaryText">
                        <h3><a href="{$Link}" title="<% if $ReceiptTitle %>{$ReceiptTitle}<% else %>{$Title}<% end_if %>"><% if $ReceiptTitle %>{$ReceiptTitle.LimitCharacters(48)}<% else %>{$Title.LimitCharacters(48)}<% end_if %></a></h3>
                        <b>$Price.Nice</b>
                        <div class="content"><p>{$Content.Summary}</p></div>
                        <p><a class="productLearnMore" href="$Link" alt="Learn More">Click here for more information</a></p>
                    </div>
                </div>


            <% end_loop %>
			<% with $ProductList %>
				<% include Pagination %>
			<% end_with %>
        <% else %>
            <p>No results</p>
        <% end_if %>
	</section>
</div>
