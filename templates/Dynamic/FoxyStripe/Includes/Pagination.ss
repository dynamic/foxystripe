<% if MoreThanOnePage %>
	<div class="clearfix">
		<% if NotFirstPage %>
			<a class="previous_page" href="$PrevLink" rel="previous">&lt; Previous</a></span>
		<% else %>	
			<span class="disabled previous_page">&lt; Previous</span>
		<% end_if %>
		
		<% loop PaginationSummary(4) %>
			<% if CurrentBool %>
				<em class="current">$PageNum</em>
			<% else %>
				<% if Link %>
					<a href="$Link">$PageNum</a>
				<% else %>
					<em>...</em>						
				<% end_if %>
			<% end_if %>
		<% end_loop %>
		<% if NotLastPage %>
			<a class="next_page" href="$NextLink" rel="next">Next &gt;</a>
		<% else %>
			<span class="disabled next_page">Next &gt;</span>
		<% end_if %>
	</div>
<% end_if %>