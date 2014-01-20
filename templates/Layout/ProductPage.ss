<% require css('https://cdn.foxycart.com/static/scripts/colorbox/1.3.19/style1_fc/colorbox.css?ver=1') %>
<p>$Breadcrumbs</p>

<% include ProductSideBar %>
<div class="unit size1of2">
	<article>
	    
	    <% cached 'ProductPage', LastEdited %>
	        <div class="productTextContainer floatLeft">
	            <h1>{$Title}</h1>
	            <div class="content"><p>{$Content}</p></div>
	        </div>
		<% end_cached %>
	    	
	    <% if Menu(2) %>
	        </div>
	    <% end_if %>
   </article>
	$Form
	$PageComments
</div>
<div class="unit size1of4 lastUnit">
	$PurchaseForm
</div>

<% require javascript('framework/thirdparty/jquery/jquery.js') %>
<% require javascript('https://cdn.foxycart.com/dynamic/foxycart.colorbox.js?ver=2') %>
