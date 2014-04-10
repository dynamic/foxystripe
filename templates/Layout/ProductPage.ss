<p>$Breadcrumbs</p>

<div class="content-container unit size3of4">
	<aside class="unit size1of4 product-image-stack">
		<% if MainImage %>
			$MainImage.SetWidth(245)
		<% end_if %>
		<% if ProductImages %>
	        <% loop ProductImages %>
	        	<% with Image %>
	                <% with SetWidth(245) %>
	                    <img src="{$URL}" width="$getWidth" height="$getHeight" class="product-image" />
	                <% end_with %>
	            <% end_with %>
	        <% end_loop %>
	    <% else_if PreviewImage %>
	    	<% with PreviewImage %>
	            <% with SetWidth(220) %>
	                <img src="{$URL}" width="$getWidth" height="$getHeight" class="product-image" />
	            <% end_with %>
	        <% end_with %>
	    <% end_if %>
	</aside>
	
	<div class="unit size3of4 product-info-stack">
		<article>
		    
	        <div class="productTextContainer floatLeft">
	            <h1>{$Title}</h1>
	            <p>
		            <strong>Price:</strong> $Price.Nice<br>
		            <strong>Weight:</strong> $Weight lbs<br>
		            <strong>Code:</strong> $Code
	            </p>
	            <div class="content"><p>{$Content}</p></div>
	        </div>
		    	
		    
	   </article>
		$Form
		$PageComments
	</div>
</div>
<div class="sidebar unit size1of4 lastUnit ">
	$PurchaseForm
</div>