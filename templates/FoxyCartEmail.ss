<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1"/>
	<title>Order from ^^store_name^^</title>
    <% require themedCSS('reset') %>
    <% require themedCSS('typography') %>
    <% require themedCSS('form') %>
    <% require themedCSS('layout') %>
	</head>
	<body>
		<style>
			table, tr, td {
				border: 0px gray solid;
				padding: 0px;
			}
	
			img {
				max-width: 100%;
				height: auto;
			}
			body {background: #fff;}
			
			.typography table td, .typography table th, .typography table {border: 0px; padding: 0px;}
			 #emailbody {border: 0px solid #000;}
			 
			 #header {background: #000; color: #fff;}
			 h2.title a, h2.title a:visited {color: #fff; text-decoration: none;}
			 h2.title a:hover {color: #fff; text-decoration: none; border: none;}
	
			@media only screen and (max-width: 480px) { 
	
				table[id=emailbody] { 
					width: 100% !important;
				}
				a {
					display: inline-block;
					padding:3px 10px;
					text-align: center;
					border:1px gray solid;
					border-radius: 4px;
					background-color: #b00;
					text-decoration: none;
					color: #fff !important;
				}
				td {
					font-size: 16px;
					line-height: 16px;
	
				}
				img {
					max-width: 100%;
					height: auto;
				}
				table[class=pxcolumn] {
					width: 300px !important}
				}
				h2.title a {background: none; color: #B80000; border: none;}
		</style>
		
		
		<center>

			<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodytable">
				<tr>
					<td align="center" valign="top" id="bodycell">
						<table border="0" cellpadding="0" cellspacing="0" width="600" id="emailbody" class="typography">
							<tr>
								<td>
									<p style="text-align: center; margin-top: 10px; margin-bottom: 10px;">
										<a href="^^receipt_url^^">View this message in a browser</a>
									</p>
								</td>
							</tr>

							<tr>
								<td>
									<table border="0" cellpadding="0" cellspacing="20" width="100%" id="header">
										<tr>
											<td width="5%">&nbsp;</td>
											<td width="90%" valign="middle" class="textContent">
												<h2 class="title" style="margin-top: 15px; margin-bottom: 15px;"><a href="/">$SiteConfig.Title</a></h2>
											</td>
											<td width="5%">&nbsp;</td>
										</tr>
									</table>
									
									$Layout

									<table border="0" cellpadding="0" cellspacing="0" width="100%" id="footer">
										<tr>
											<td width="5%">&nbsp;</td>
											<td valign="top" class="textContent">
												<h4>Thank you for your purchase!</h4>
												<p>&copy; $Now.Year $SiteConfig.Title</p>
											</td>
											<td width="5%">&nbsp;</td>
										</tr>
									</table>
								</td>
							</tr>
						</table><!-- end of emailbody -->
					</td><!-- end of bodycell -->
				</tr>
			</table><!-- end of bodytable -->
		</center>
	</body>
	</html>