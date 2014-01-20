<% include SideBar %>
<div class="content-container unit size3of4 lastUnit">
	<article>
	    <h1>$Title</h1>
        
        <% if Content %><div class="typography">$Content</div><% end_if %>
        
    	<% loop Products %>
    		<div class="productSummary unit size3of4">
        		<% if PreviewImage %>
					<div class="unit size1of4">
	                	<a href="{$Link}" title="{$Title}">
	                    <% with PreviewImage %>
	                    	<% loop PaddedImage(150, 150) %>
	                        	<img src="{$URL}" width="$getWidth" height="$getHeight" />
	                        <% end_loop %>
	                    <% end_with %>
	                    </a>
	                </div>
				<% end_if %>
				<div class="unit size3of4">
	            	<h3><a href="{$Link}" title="{$Title}">{$Title.LimitCharacters(40)}</a></h3>
	            	<b>$Price.Nice</b>
	                <div class="content"><p>{$Content.Summary}</p></div>
	                <p><a class="productLearnMore" href="$Link" alt="Learn More">Learn more about {$Title} &raquo;</a></p>
				</div>
            </div>
		<% end_loop %>
		</article>
    <% if Menu(2) %>
        </div>
    <% end_if %>
</div>