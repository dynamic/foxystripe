<% require css('https://cdn.foxycart.com/static/scripts/colorbox/1.3.19/style1_fc/colorbox.css?ver=1') %>
<% require css('foxystripe/css/foxycart.css') %>
<p>$Breadcrumbs</p>

<aside class="unit size1of4">
	<% if MainImage %>
		$MainImage.SetWidth(245)
	<% end_if %>
	<% if ProductImages %>
        <% loop ProductImages %>
        	<% with Image %>
                <% with SetWidth(245) %>
                    <img src="{$URL}" width="$getWidth" height="$getHeight" />
                <% end_with %>
            <% end_with %>
        <% end_loop %>
    <% else_if PreviewImage %>
    	<% with PreviewImage %>
            <% with SetWidth(220) %>
                <img src="{$URL}" width="$getWidth" height="$getHeight" />
            <% end_with %>
        <% end_with %>
    <% end_if %>
</aside>

<div class="unit size1of2">
	<article>
	    
        <div class="productTextContainer floatLeft">
            <h1>{$Title}</h1>
            <p>
	            <b>Price:</b> $Price.Nice<br>
	            <b>Weight:</b> $Weight lbs<br>
	            <b>Code:</b> $Code
            </p>
            <div class="content"><p>{$Content}</p></div>
        </div>
	    	
	    
   </article>
	$Form
	$PageComments
</div>
<div class="unit size1of4 lastUnit">
	$PurchaseForm
</div>

<% require javascript('framework/thirdparty/jquery/jquery.js') %>
<% require javascript('https://cdn.foxycart.com/dynamic/foxycart.colorbox.js?ver=2') %>
