<div id="main-content">	

	<div class="grid-container">

		<div class="main__title">
			<h4 class="t-text-bold">Shipping Information</h4>
		</div>

		<div class="card-box">
			<div class="grid-container fluid">
				<div class="grid-x grid-margin-x">
					<div class="cell small-12">
						<p class="t-text-bold t-black-color u-no-margin-bottom">Pickup Address</p>
						<p>this address and contact information will be used for the courier pickup.</p>
					</div>
				</div>
				<div class="grid-x grid-margin-x">				
					<div class="cell small-12">

						<div class="radio-wrapper t-border-label t-spacing-bottom-small">

						<?php foreach ($stores_address as $s_key => $s_address) { ?>

								<div class="radio-item radio-item--middle">
									<input type="radio" name="address" value="<?php echo $s_address->id; ?>" id="b-opt-<?php echo $s_key; ?>" <?php echo $address == $s_address->id ? 'checked="true"' : ''; ?>>
									<label for="b-opt-<?php echo $s_key; ?>" class="radio-label"></label>
								</div>
								<div class="radio-content">
									<div><small class="t-medium-gray-color">Country <span class="t-primary-color"><?php echo $s_address->country_name; ?></span></small></div>
									<div class="t-text-bold t-black-color"><?php echo $s_address->shop_name; ?></div>
									<div><?php echo $s_address->address1 . ", " . $s_address->province . ", " . $s_address->city . ", " . $store->postcode;?></div>
									<div><?php echo $s_address->phone; ?></div>
								</div>

								<script type="text/javascript">
									$(document).ready(function() {
										$('#b-opt-<?php echo $s_key; ?>').click(function() {
											var address 	= $(this).val();
											var shop 		= '<?php echo $shop; ?>';

											var url = window.location.href;    
											if (url.indexOf('?') > -1) {
											   url += '&address='+address;
											} else {
											   url += '?shop='+shop+'&address='+address;
											}
											window.location.href = url;
										});
									 });
								</script>

						<?php } ?>
						</div>

					</div>
				</div>
				<div class="grid-x grid-margin-x">				
					<div class="cell small-12">						
						<a href="<?php echo base_url(); ?>manage_address_add?shop=<?php echo $shop; ?>" class="button hollow primary u-no-margin-bottom">Add New Address</a>		
					</div>
				</div>
			</div>
		</div>

		<div class="card-box separated">
			<div class="card-box__row grid-x">
				
				<div class="card-box__column cell auto">
					<p class="t-primary-color t-text-bold">1 Shipment</p>
					<div class="t-border-label t-text-bold text-center">
						<?php echo $service_selected; ?>
					</div>
				</div>

				<div class="card-box__column cell medium-4">
					<p class="t-black-color t-text-bold">Service Option</p>
					<div class="radio-wrapper">
						<div class="radio-item">
							<input type="radio" name="" value="" id="a-opt" checked="true">
							<label for="a-opt" class="radio-label"></label>
						</div>
						<div class="radio-content">
							<label for="a-opt">Pickup by Ship (Standard Pickup)</label>
							<h5 class="t-primary-color">Free</h5>
							<div class="grid-container full">
								<div class="grid-x grid-margin-x">
									<div class="cell small-12 medium-6">
										<input type="text" class="datepicker" maxlength="20" name="" value="<?php echo $collectionDate; ?>" id="collectionDate" >
									</div>
									<div class="cell small-12 medium-6">
										<select name="" id="collectionTime">
											<?php foreach ($collections as $key => $collect) { ?>
												<option value="<?php echo $collect->CollectionTimeFrom.';'.$collect->CollectionTimeTo ?>" 
													<?php if(!empty($collectionTime) && ($collectionTime == $collect->CollectionTimeFrom.';'.$collect->CollectionTimeTo)){ ?> selected="true" <?php } ?>
													><?php echo $collect->CollectionTimeFrom . ' - ' . $collect->CollectionTimeTo; ?></option>
											<?php }	?>
										</select>

										<input type="hidden" name="shop" id="shop" value="<?php echo $shop; ?>">

										<script type="text/javascript">
											$(document).ready(function() {

												$('#collectionTime').change(function() {

													var Collection 	= $(this).val();
													var shop 		= $('#shop').val();
													var mydate = new Date($('#collectionDate').val());
													var dateString = mydate.getFullYear() + "-" + (mydate.getMonth() + 1) + "-" + mydate.getDate();
													Collection = dateString + ";" + Collection;

													var url = window.location.href;    
													if (url.indexOf('?') > -1){
													   url += '&Collection='+Collection;
													}else{
													   url += '?shop='+shop+'&Collection='+Collection;
													}
													//alert(url);
													window.location.href = url;
												});

											 });

										</script>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="card-box__column cell auto">
					<p class="t-black-color t-text-bold">Total Cost</p>
					<h5 class="t-gray-color">SGD <?php echo $cost_service; ?> <i class="fas fa-exclamation-circle t-primary-color"></i></h5>
					<div><small>Learn more about</small></div>
					<a href="#"><small>Pickup and Drop-off options.</small></a>
				</div>

			</div>
		</div>

		<div class="footer__nav">
			<div class="footer__nav--left">
				<a href="<?php echo base_url(); ?>receiver_information?shop=<?php echo $shop; ?>" class="button hollow gray button-left">Back</a>
			</div>
			<div class="footer__nav--right">
				<a href="<?php echo base_url(); ?>confirm?shop=<?php echo $shop; ?>" class="button alert button-right">Next Step</a>
			</div>
		</div>
	</div>
</div>