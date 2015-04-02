<%-- redeclare Simple theme includes to keep correct inclusion order --%>
<% require themedCSS('reset') %>
<% require themedCSS('typography') %>
<% require themedCSS('layout') %>
<%-- FoxyStripe requirements --%>
<% require css('foxystripe/thirdparty/flexslider/flexslider.css') %>
<% require css('foxystripe/thirdparty/shadowbox/shadowbox.css') %>
<% require css('foxystripe/css/foxycart.css') %>

<div class="ProductPage line">
    <p>$Breadcrumbs</p>

    <div class="content-container unit size3of4">
        <aside class="unit size2of5 productSummaryImage">
            <% include FlexSlider %>
        </aside>

        <div class="unit size3of5 productSummaryText">
            <article>

                <h1><% if $ReceiptTitle %>$ReceiptTitle<% else %>$Title<% end_if %></h1>
                    <p>
                        <strong>Base Price:</strong> $Price.Nice<br>
                        <strong>Weight:</strong> $Weight lbs<br>
                        <strong>Code:</strong> $Code
                        <% if $ProductRating %><br>$ProductRating<% end_if %>
                    </p>
                    <div class="content">$Content</div>

            </article>
        </div>
    </div>

    <div class="sidebar unit size1of4">
        $PurchaseForm
    </div>

</div>
<% if $ProductReviews %>
<div class="line">
    <div class="unit size3of4">
        $ProductReviews
    </div>
</div>
<% end_if %>

<% require javascript('foxystripe/thirdparty/flexslider/jquery.flexslider.js') %>
<% require javascript('foxystripe/thirdparty/shadowbox/shadowbox.js') %>
<% require javascript('foxystripe/javascript/product_init.js') %>
$CartScript
