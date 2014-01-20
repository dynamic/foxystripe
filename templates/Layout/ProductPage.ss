<% require css('https://cdn.foxycart.com/static/scripts/colorbox/1.3.19/style1_fc/colorbox.css?ver=1') %>

<% include ProductSideBar %>
<div class="unit size1of2">
	<article>
	    <h1>$Parent.Title</h1>
	    
	    <% cached 'ProductPage', LastEdited %>
	        <div class="productTextContainer floatLeft">
	            <h2>{$Title}</h2>
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
