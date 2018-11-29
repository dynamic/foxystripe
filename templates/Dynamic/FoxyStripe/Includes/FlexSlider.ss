<div class="flexslider" id="slider">
	<ul class="slides">
        <% if $SortedImages %>
            <% loop $SortedImages %>
                <li>
                    <a href="{$URL}" rel="shadowbox">
                        <img src="{$URL}"  alt="$Name.XML">
                    </a>
                </li>
            <% end_loop %>
        <% end_if %>
	</ul>
</div>
<div class="flexslider" id="carousel">
	<ul class="slides">
        <% if $SortedImages %>
            <% loop $SortedImages %>
                <li>
                    <img src="{$Pad(75,75).URL}"  alt="$Name.XML">
                </li>
            <% end_loop %>
        <% end_if %>
	</ul>
</div>
