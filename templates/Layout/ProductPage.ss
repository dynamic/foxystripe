<%-- redeclare Simple theme includes to keep correct inclusion order --%>
<% require themedCSS('reset') %>
<% require themedCSS('typography') %>
<% require themedCSS('layout') %>
<%-- FoxyStripe requirements --%>
<% require css('foxystripe/thirdparty/flexslider/flexslider.css') %>
<% require css('foxystripe/thirdparty/shadowbox/shadowbox.css') %>
<% require css('foxystripe/css/foxycart.css') %>

<div class="ProductPage">
    <p>$Breadcrumbs</p>

    <div class="content-container unit size3of4">
        <aside class="unit size2of5 productSummaryImage">
            <% include FlexSlider %>
        </aside>

        <div class="unit size3of5 productSummaryText">
            <article>

                    <h1>$Title</h1>
                    <p>
                        <strong>Base Price:</strong> $Price.Nice<br>
                        <strong>Weight:</strong> $Weight lbs<br>
                        <strong>Code:</strong> $Code
                    </p>
                    <div class="content">$Content</div>


            </article>
            $PageComments
        </div>
    </div>

    <div class="sidebar unit size1of4">
        $PurchaseForm
    </div>
</div>

<% require javascript('foxystripe/thirdparty/flexslider/jquery.flexslider.js') %>
<% require javascript('foxystripe/thirdparty/shadowbox/shadowbox.js') %>
<% require javascript('foxystripe/javascript/product_init.js') %>
$ColorBoxScript