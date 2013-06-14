<% include SideBar %>
<div class="content-container unit size3of4 lastUnit">
	<article>
	    <h1>$Parent.Title</h1>
	    
	    <% cached 'ProductPage', LastEdited %>
		    <div class="floatRight productImageContainer">
	            <% if ProductImages %>
	                <% control ProductImages %>
	                	<% control Image %>
	                        <% control SetWidth(220) %>
	                            <img src="{$URL}" width="$getWidth" height="$getHeight" />
	                        <% end_control %>
	                    <% end_control %>
	                <% end_control %>
	            <% else_if PreviewImage %>
	            	<% control PreviewImage %>
	                    <% control SetWidth(220) %>
	                        <img src="{$URL}" width="$getWidth" height="$getHeight" />
	                    <% end_control %>
	                <% end_control %>
	            <% end_if %>
	        </div>
	        <div class="productTextContainer floatLeft">
	            <h2>{$Title}</h2>
	            <div class="content"><p>{$Content}</p></div>
	        </div>
	        <div class="purchaseSection clear floatLeft">
				$PurchaseForm
			</div>
		<% end_cached %>
	    	
	    <% if Menu(2) %>
	        </div>
	    <% end_if %>
   </article>
	$Form
	$PageComments
</div>