<aside class="unit size1of4">
	<% if ProductImages %>
        <% loop ProductImages %>
        	<% with Image %>
                <% with SetWidth(220) %>
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
