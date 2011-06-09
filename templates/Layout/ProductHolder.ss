<div class="typography contentLayout">
    <% if Menu(2) %>
        <% include SideBar %>
        <div id="Content" class="floatLeft">
    <% end_if %>
    
        <h2>$Title</h2>
        <% if Level(2) %>
			<% include BreadCrumbs %>
        <% end_if %>
    
        $Content
        <% cached 'ProductHolderList', LastEdited %>
	        <% control Children %>
	        	<div class="floatLeft">
	            <div class="clear productContainer">
	                <div class="productContainerSide">
	            	<div class="productContainerBottom">
	                	<div class="floatRight productImageContainer">
							<% if PreviewImage %>
	                        	<a href="{$Link}" title="{$Title}">
	                            <% control PreviewImage %>
	                            	<% control PaddedImage(214, 214) %>
	                                	<img src="{$URL}" width="$getWidth" height="$getHeight" />
	                                <% end_control %>
	                            <% end_control %>
	                            </a>
	                        <% end_if %>
	                    </div>
	            		<div class="productTextContainer">
	                    	<div class="header"><p><a href="{$Link}" title="{$Title}">{$Title}</a></p></div>
	                        <div class="content"><p>{$Content.firstParagraph}</p></div>
	                        <p><a class="productLearnMore" href="$Link" alt="Learn More">Learn more about {$Title} &raquo;</a></p>
	                    </div>
	            	</div>
	                </div>
	            </div>
	            </div>
	            
	        <% end_control %>
		<% end_cached %>
    <% if Menu(2) %>
        </div>
    <% end_if %>
</div>