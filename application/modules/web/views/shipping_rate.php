<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/front/css/style.css">
	<!-- Magnific Popup core CSS file -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/front/css/magnific-popup.css">
</head>
<body>
<div id="pblock"></div>
<div class="main_ship">
<!-----------------------------------------------------------------heading------------------------------------------------------------------------------>
	<div class="ship">
		<h2>Create Shipment</h2>
	</div>
<!-----------------------------------------------------------------header------------------------------------------------------------------------------>
	<div class="ship_det">
		<div class="ship_det_con">
		<div class="ship-det-1"><span>1</span><br><p>Shipping Detail</p></div><hr>
		<div class="ship-det-2"><span>2</span><br><p>Pickup Information</p></div><hr>
		<div class="ship-det-3"><span>3</span><br><p>Confirmation</p></div>
		</div>
	</div>
<!-----------------------------------------------------------------pagination----------------------------------------------------------------------------->
	<div class="pagiation">
		<div class="page-1"><span><</span>1 to 10 of 16 <span>></span></div>
	</div>
<!-----------------------------------------------------------------navbar------------------------------------------------------------------------------>	
	<div class="list">
		<ul>
			<li>Items</li>
			<li>rate</li>
			<li>Receiver</li>
			<li>Service</li>
			<li>Delivery Time</li>			
			<li>Total Cost</li>
		</ul>
	</div>

<!-----------------------------------------------------------------row------------------------------------------------------------------------------>

<?php foreach ($rates as $key => $rate) { ?>
	<div class="items">
		<ul>
			<li>
				<div class="popup" >
					<a href="#myPopup_<?php echo $rate->rate_id; ?>" class="it-1 it-er open-popup-link">
						<div class="bg"></div>
						<span><?php echo $rate->quantity; ?><br>items</span>
					</a>
					<div class="popuptext white-popup mfp-hide" id="myPopup_<?php echo $rate->rate_id; ?>">
						<div class="form_pop">
							<!-------------------------------------------------------------item--------------------------------------------------------------------------------------------->
							<div class="items">
								<ul>
									<li>
										<div class="it-1 it-err-2">
										<div class="bg"></div>
										<span><?php echo $rate->quantity; ?><br>items</span></div>
									</li>
									<li>
										<div class="it-2"><span><img src="<?php echo base_url(); ?>assets/front/images/shopify.png">Nextbase..<br><p>#<?php echo $rate->rate_id; ?></p></span></div>
									</li>
									<li>
										<div class="it-2"><span><?php echo $rate->name; ?><br><img src="<?php echo base_url(); ?>assets/front/images/flag.png"><b><?php echo $rate->country; ?></b></span></div>
									</li>
									<li>
										<div class="err"><img src="<?php echo base_url(); ?>assets/front/images/errr.png"><span>Please update your item details in rate to select courier</span></div>
									</li>
								</ul>
							</div>
							<!-------------------------------------------------------------id--------------------------------------------------------------------------------------------->

							<?php foreach ($rate->products as $key2 => $product) { ?>
								<div class="id_main id_<?php echo $product->id ?>">
									<div class="id_1">#<?php echo $product->product_id ?>
										<span><img src="<?php echo base_url(); ?>assets/front/images/trash.png"></span>
									</div>
									<div class="desc">
										<div class="desc_1">Items Description<br>
											<input type="text" name="Items Description" value="<?php echo $product->title ?>">
										</div>
										<div class="desc_2">Company SKU(optional)<br>
											<input type="text" name="Company SKU(optional)" value="<?php echo $product->sku ?>">
										</div>
									</div>
									
									<div class="dimen">
										<div class="dimen-1">Dimension<img src="<?php echo base_url(); ?>assets/front/images/errr.png"><br>
											<input type="text" value="<?php echo $product->long ?>">*
											<input type="text" value="<?php echo $product->wide ?>">*
											<input type="text" value="<?php echo $product->high ?>">
										</div>
										<div class="dimen-2">
											Weight(Kg)<br>
											<input type="text" value="<?php echo $product->weight ?>">
										</div>
									</div>
								</div>
							<?php } ?>
							<!-------------------------------------------------------------end div--------------------------------------------------------------------------------------------->
						</div>
						<div style="clear: both;"></div>
					</div>
				
				</div>
			</li>
			<li>
				<div class="it-2">
					<span>
						<!--img src="<?php echo base_url(); ?>assets/front/images/shopify.png">Merchant<br-->
						<p>#<?php echo $rate->rate_id; ?></p>
					</span>
				</div>
			</li>
			<li>
				<div class="it-2"><span><?php echo $rate->name; ?><br><img src="<?php echo base_url(); ?>assets/front/images/flag.png"><b><?php echo $rate->country; ?></b></span></div>
			</li>			
			<li>
				<div class="popup1">
					<div class="it-4">
						<a href="#merchant1" class="open-popup-link">Standard</a>
						<div class="popuptext1 white-popup mfp-hide" id="merchant1">
							<div class="slect">
								<h2>Select Service</h2>
								<select class="slt_1">
								<option>Standard(SGD 5.90)</option>
								<option>Standard(SGD 5.90)</option>
								<option>Standard(SGD 5.90)</option>
								<option>Standard(SGD 5.90)</option>
								<select><br>
								<button class="btn_slt" type="button">OK</button>
							</div>
						</div>					
					</div>
				</div>
			</li>
			<li>
				<div class="it-3"><p>1-2 working day</p></div>
			</li>
			<li>
				<div class="it-5">
					<p><?php
						$number = $rate->total_price;
						setlocale(LC_MONETARY,"en_SG");
						echo money_format("%i ", $number);
						?>
					</p>
				</div>
			</li>
			<button class="it-btn" type="button">Ship</button>
		</ul>
	</div>
<?php } ?>

<!-----------------------------------------------------------------row-end----------------------------------------------------------------------------->
	<div class="pagiation">
		<div class="page-1">
			<span><</span>
			<?php echo $paging; ?>
			<span>></span>
		</div>
	</div>
<!-----------------------------------------------------------------pagination end------------------------------------------------------------------------------>


<!-- Like so: -->
<a href="#test-popup" class="open-popup-link">Show inline popup</a>

<div id="test-popup" class="white-popup mfp-hide">
  Popup contentsdsadasssssssssssssssss
</div>

	<!-- jQuery 1.7.2+ or Zepto.js 1.0+ -->
	<script src="<?php echo base_url(); ?>assets/front/js/jquery.min.js"></script>

	<!-- Magnific Popup core JS file -->
	<script src="<?php echo base_url(); ?>assets/front/js/jquery.magnific-popup.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
		$('.open-popup-link').magnificPopup({
			  type:'inline',
			  midClick: true // Allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source in href.
			});
		 });

	</script>

</div>
</body>
</html>
