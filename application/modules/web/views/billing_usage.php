<div id="main-content">	
	<div class="grid-container">

		<div class="main__title">
			<h4 class="t-text-bold">Shipping Success</h4>
		</div>

		<div class="card-box">
			<div class="grid-container fluid">
				<div class="grid-x grid-margin-x">
					<div class="cell medium-6">
						<div class="t-spacing-bottom-small">
							<div class="t-spacing-bottom-small">
								<img class="t-inline-block t-spacing-right-small" src="<?php echo base_url(); ?>assets/ship/images/check.png" alt="">
								<h4 class="t-inline-block t-text-bold t-valign-middle u-no-margin-bottom">Shipment created</h4>
							</div>
							<small class="t-medium-gray-color">Country <span class="t-primary-color"><?php echo $order->country; ?></span></small>
							<div class="t-text-bold t-black-color"><?php echo $order->name; ?></div>
							<div><?php echo $order->address1 . " " . $order->address2 . " " . $order->postcode . " " . $order->country; ?></div>
							<div><?php echo $$order->phone; ?></div>
							<div class="t-text-bold t-black-color">1 Shipment</div>
							<div class="t-text-bold t-black-color"><?php echo $shipping_service; ?></div>
						</div>
						<a href="#" class="button alert expanded u-no-margin-bottom">Download Label</a>
					</div>
					<div class="cell medium-6">
						<div class="card-box--background">
							<img src="<?php echo base_url(); ?>assets/ship/images/bg-1.png" alt="">
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>