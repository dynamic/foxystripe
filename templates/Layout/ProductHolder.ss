<% require css('foxystripe/css/foxycart.css') %>
<% include SideBar %>
<div class="content-container unit size3of4 lastUnit">
	<article>
	    <h1>$Title</h1>
        
        <% if Content %><div class="typography">$Content</div><% end_if %>
        
    	<% loop Products %>
    		<div class="productSummary unit">
				<div class="unit size1of4">
					<% if PreviewImage %>
						<a href="{$Link}" title="{$Title}" class="anchor-fix">
		                    <% with PreviewImage %>
		                    	<% loop PaddedImage(150, 150) %>
		                        	<img src="{$URL}" width="$getWidth" height="$getHeight" class="product-image"/>
		                        <% end_loop %>
		                    <% end_with %>
	                    </a>
	                <% else %>
	                	<%-- placeholder image --%>
						&nbsp;
					<% end_if %>
				</div>
				<div class="unit size3of4">
	            	<h3><a href="{$Link}" title="{$Title}">{$Title.LimitCharacters(48)}</a></h3>
	            	<b>$Price.Nice</b>
	                <div class="content"><p>{$Content.Summary}</p></div>
	                <p><a class="productLearnMore" href="$Link" alt="Learn More">Click here for more information</a></p>
				</div>
            </div>
		<% end_loop %>
	</article>
</div>