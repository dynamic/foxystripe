<div class="typography contentLayout">
    <% if Menu(2) %>
        <% include SideBar %>
        <div id="Content" class="floatLeft">
    <% end_if %>

    <h2>$Title</h2>
    <% if Level(2) %>
		<% include BreadCrumbs %>
    <% end_if %>
    
    <% cached 'ProductPage', LastEdited %>
    <div class="floatLeft">
        <div class="clear productContainer floatLeft">
            <div class="productContainerSide floatLeft">
            <div class="productContainerBottom floatLeft">
                <div class="floatRight productImageContainer">
                    <% if ProductImages %>
                        <% control ProductImages %>
                        	<% control Image %>
                                <% control PaddedImage(214, 214) %>
                                    <img src="{$URL}" width="$getWidth" height="$getHeight" />
                                <% end_control %>
                            <% end_control %>
                        <% end_control %>
                    <% else_if PreviewImage %>
                    	<% control PreviewImage %>
                            <% control PaddedImage(214, 214) %>
                                <img src="{$URL}" width="$getWidth" height="$getHeight" />
                            <% end_control %>
                        <% end_control %>
                    <% end_if %>
                </div>
                <div class="productTextContainer floatLeft">
                    <div class="header"><p>{$Title}</p></div>
                    <div class="content"><p>{$Content}</p></div>
                </div>
                <div class="purchaseSection clear floatLeft">
					$PurchaseForm
				</div>
            </div>
            </div>
        </div>
    </div>
	<% end_cached %>
    	
    <% if Menu(2) %>
        </div>
    <% end_if %>
</div>