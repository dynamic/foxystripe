<h4><%if Level(2) %>$Parent.Title<% else %>$Title <% end_if %></h4>
<% if Menu(2) %>
	<nav class="secondary">
		<ul>
			<% loop Menu(2) %>
				<li class="$LinkingMode">
					<a href="$Link" title="Go to the $Title.XML page">
						$MenuTitle.XML
					</a>
					<% if MenuChildren %>
						<ul>
							<% loop MenuChildren %>
								<li class="$LinkingMode"><a href="$Link" title="Go to the $Title.XML page">$MenuTitle.XML</a></li>
							<% end_loop %>
						</ul>
					<% end_if %>
				</li>
			<% end_loop %>
		</ul>
	</nav>
<% end_if %> 

<% if Links %>
	<hr>
	<h4>Links</h4>
	    <ul>
	    	<% loop Links.Sort(SortOrder) %>
	    		<li>
	    			<% if PageLink %>
	    				<a href="$PageLink.Link" title="Go to the $PageLink page">$Name</a>
	    			<% else_if External %>
	    				<a href="$External" target="_blank"  title="Go to the $Exteranl page">$Name</a>
	    			<% end_if %>
	    		</li>
	        <% end_loop %>
	    </ul>
<% end_if %>