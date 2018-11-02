<div class="flexslider" id="slider">
	<ul class="slides">
		<% if $PreviewImage %>
	    	<li>
				<% with $PreviewImage %>
					<a href="{$URL}" rel="shadowbox">
						<img src="{$URL}">
					</a>
				<% end_with %>
			</li>
	    <% end_if %>
        <% if $ProductImages %>
            <% loop $ProductImages %>
                <li>
                    <a href="{$Image.URL}" rel="shadowbox">
                        <img src="{$Image.URL}"  alt="$Name.XML">
                    </a>
                </li>
            <% end_loop %>
        <% end_if %>
	</ul>
</div>
<div class="flexslider" id="carousel">
	<ul class="slides">
        <% if $ProductImages %>
            <% if $PreviewImage %>
                <li>
                    <% with $PreviewImage %>
                        <img src="{$PaddedImage(75,75).URL}">
                    <% end_with %>
                </li>
            <% end_if %>

            <% loop $ProductImages %>
                <li>
                    <img src="{$Image.PaddedImage(75,75).URL}"  alt="$Name.XML">
                </li>
            <% end_loop %>
        <% end_if %>
	</ul>
</div>