<% require css('foxystripe/css/foxycart.css') %>
<div class="content-container unit size3of4 lastUnit">
	<article>
		<h1>$Title</h1>
		
		<% if Content %><div class="typography">$Content</div><% end_if %>
		
		<div class="historySummary unit">
			<div class="unit size1of4 sidebar">
				<h3>April 10, 2014</h3>
				<p><a href="#">Order Details</a> | <a href="#">Invoice</a></p>
				<p>Order #1234</p>
				<p>Total $25.99</p>
			</div>
			<div class="unit size3of4 full">
				<div class="unit size1of5">
					<img src="/assets/Uploads/_resampled/PaddedImage150150FFFFFF-3SheepsShirt.jpg" width="150" height="150" class="product-image">
				</div>
				<div class="unit size3of5">
					<h3><a href="{$Link}" title="{$Title}">Antique Irish Green 3 Sheeps T-Shirt</a></h3>
					<div class="content"><p>Description of item would go here</p></div>
					<p><a class="productLearnMore" href="$Link" alt="Learn More">Click here for more information</a></p>
				</div>
				<div class="unit size1of5">
					<p><em>Placeholder if we want a third column, otherwise the previous gets stretched to 4of5</em></p>
				</div>
			</div>
		</div>
		
		
		<div class="historySummary unit">
			<div class="unit size1of4 sidebar">
				<h3>April 15, 2014</h3>
				<p><a href="#">Order Details</a> | <a href="#">Invoice</a></p>
				<p>Order #1235</p>
				<p>Total $52.99</p>
			</div>
			<div class="unit size3of4 full">
				<div class="unit size1of5">
					<img src="/assets/Uploads/_resampled/PaddedImage150150FFFFFF-3SheepsShirt.jpg" width="150" height="150" class="product-image">
				</div>
				<div class="unit size3of5">
					<h3><a href="{$Link}" title="{$Title}">Antique Irish Green 3 Sheeps T-Shirt</a></h3>
					<div class="content"><p>Description of item would go here</p></div>
					<p><a class="productLearnMore" href="$Link" alt="Learn More">Click here for more information</a></p>
				</div>
				<div class="unit size1of5">
					<p><em>Placeholder if we want a third column, otherwise the previous gets stretched to 4of5</em></p>
				</div>
			</div>
			<div class="unit size3of4 full">

				<div class="unit size1of5">
					<img src="/assets/Uploads/_resampled/PaddedImage150150FFFFFF-3SheepsShirt.jpg" width="150" height="150" class="product-image">
				</div>
				<div class="unit size3of5">
					<h3><a href="{$Link}" title="{$Title}">Antique Irish Green 3 Sheeps T-Shirt</a></h3>
					<div class="content"><p>Description of item would go here</p></div>
					<p><a class="productLearnMore" href="$Link" alt="Learn More">Click here for more information</a></p>
				</div>
				<div class="unit size1of5">
					<p><em>Placeholder if we want a third column, otherwise the previous gets stretched to 4of5</em></p>
				</div>
			</div>
		</div>
		<%--
		<% if {History} %>
			<% loop {History} %>
				<div class="historySummary unit">
					<div class="unit size1of4 sidebar">
						<h3>{Date}</h3>
						<p><a href="{DetailLink}">Order Details</a> | <a href="{InvoiceLink}">Invoice</a></p>
						<p>Order {OrderNumber}</p>
						<p>Total {PriceTotal}</p>
					</div>
					<% loop {HistoryItems} %>
						<div class="unit size3of4 full">
							<div class="unit size1of5">
								<img src="/assets/Uploads/_resampled/PaddedImage150150FFFFFF-3SheepsShirt.jpg" width="150" height="150" class="product-image">
							</div>
							<div class="unit size3of5">
								<h3><a href="{$Link}" title="{$Title}">{Title}</a></h3>
								<div class="content"><p>$Description</p></div>
								<p><a class="productLearnMore" href="$Link" alt="Learn More">Click here for more information</a></p>
							</div>
							<div class="unit size1of5">
								<p><em>Placeholder if we want a third column, otherwise the previous gets stretched to 4of5</em></p>
							</div>
							
							<% if != Last %><br style="clear: both;"><% end_if %>
						</div>
					<% end_loop %>
				</div>
			<% end_loop %>
		<% end_if %>
		--%>
	</article>
</div>