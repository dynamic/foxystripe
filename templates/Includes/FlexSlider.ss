<div class="flexslider" id="slider">
	<ul class="slides">
		<% if Top.PreviewImage %>
	    	<li>
				<% with Top.PreviewImage %>
					<a href="{$URL}" rel="shadowbox">
						<img src="{$URL}" width="$getWidth" height="$getHeight" />
					</a>
				<% end_with %>
			</li>
	    <% end_if %>
		<% loop ProductImages %>
			<li class="remove-bottom">
				<a href="{$Image.URL}" rel="shadowbox">
					<img src="{$Image.URL}"  alt="$Name.XML">
				</a>
			</li>
		<% end_loop %>
	</ul>
</div>
<div class="flexslider" id="carousel">
	<ul class="slides">
		<% if Top.PreviewImage %>
	    	<li>
				<% with Top.PreviewImage %>
					<img src="{$PaddedImage(75,75).URL}" width="$getWidth" height="$getHeight" />
				<% end_with %>
			</li>
	    <% end_if %>
		<% loop ProductImages %>
			<li class="remove-bottom">
				<img src="{$Image.PaddedImage(75,75).URL}"  alt="$Name.XML">
			</li>
		<% end_loop %>
	</ul>
</div>
