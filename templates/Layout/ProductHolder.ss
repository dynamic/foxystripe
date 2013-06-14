<% include SideBar %>
<div class="content-container unit size3of4 lastUnit">
	<article>
	    <h1>$Title</h1>
        
        <% if Content %><div class="typography">$Content</div><% end_if %>
        
    	<% loop Children %>
    		<div class="productSummary">
            	<div class="productSummaryImage">
            		<% if PreviewImage %>
		               	
	                	<a href="{$Link}" title="{$Title}">
                        <% with PreviewImage %>
                        	<% control PaddedImage(150, 150) %>
                            	<img src="{$URL}" width="$getWidth" height="$getHeight" />
                            <% end_control %>
                        <% end_with %>
                        </a>
					
					<% end_if %>
				</div>
					
        		<div class="productSummaryText">
                	<h3><a href="{$Link}" title="{$Title}">{$Title}</a></h3>
                    <div class="content"><p>{$Content.firstParagraph}</p></div>
                    <p><a class="productLearnMore" href="$Link" alt="Learn More">Learn more about {$Title} &raquo;</a></p>
                </div>
			</div>
        
		<% end_loop %>

    <% if Menu(2) %>
        </div>
    <% end_if %>
</div>