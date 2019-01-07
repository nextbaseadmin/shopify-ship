<div id="main-content">
	
	<div class="grid-container">
		<div class="home__header">
			<div class="grid-container full">
				<div class="grid-x align-middle">
					<div class="cell small-8">
						<div class="user__media">
							<div class="user__media--avatar">
								<img src="<?php echo base_url(); ?>assets/store_front/images/logo.png" alt="logo">
							</div>		
							<div class="user__media--text">
								<h4 class="t-black-color t-text-bold u-no-margin-bottom">
									<?php
									if (date("H") < 12) {
									    $welcome = 'Good morning';
									} else if (date('H') > 11 && date("H") < 18) {
									    $welcome = 'Good afternoon';
									} else if(date('H') > 17) {
									    $welcome = 'Good evening';
									}
									?>
									<?php echo $welcome; ?>, <?php echo $shop; ?>
								</h4>
								<p class="t-gray-color u-no-margin-bottom">Here's what's happening with your shipments.</p>
							</div>
						</div>
					</div>
					<div class="cell small-4 text-right">
						<h4 class="t-black-color t-text-bold u-no-margin-bottom">Today</h4>
						<p class="t-gray-color u-no-margin-bottom"><?php echo date('j F Y'); ?></p>
					</div>
				</div>
			</div>
		</div>

		<div class="grid-container full">
			<div class="grid-x grid-margin-x" data-equalizer>

				<?php if(!$orders) { ?>
					<div class="cell medium-12">
						<div class="card-box u-no-padding" data-equalizer-watch>
							<div class="card-box--header">
								<div class="grid-container">
									<div class="grid-x">
										<div class="cell auto text-center"> </div>
									</div>
								</div>
							</div>
							<div class="card-box--body">
								<div class="grid-container">
									<div class="cell auto text-center">No pending orders found.</div>
								</div>
							</div>
							<div class="card-box--footer">
								<div class="grid-container">
									<div class="grid-x align-middle">									
										<div class="cell auto text-center"> </div>
									</div>
								</div>							
							</div>
						</div>
					</div>
				<?php } ?>

				<?php foreach ($orders as $key => $order) { ?>
					
					<div class="cell medium-6">
						<div class="card-box u-no-padding" data-equalizer-watch>
							<form action="<?php echo base_url();?>to_receiver_information" method="POST">
								<div class="card-box--header">
									<div class="grid-container">
										<div class="grid-x">
											<div class="cell auto"></div>
											<div class="cell auto text-right">
												<!-- <a href="#" class="t-gray-color">Edit</a> -->
											</div>
										</div>
									</div>
								</div>
								<div class="card-box--body">
									<div class="grid-container">
										<div class="grid-x t-border-label t-spacing-bottom-small">									
											<div class="cell auto">
												<div class="t-text-bold t-black-color"><?php echo $order->quantity; ?> <?php echo $order->quantity > 1 ? "Items" : "Item" ;?></div>
											</div>
											<div class="cell auto text-right">
												<a href="#myPopup_<?php echo $order->order_id; ?>" class="t-gray-color open-popup-link">Details</a>
											</div>									
										</div>
										<div class="grid-x grid-margin-x t-spacing-bottom-small">
											<div class="cell medium-6">
												<div class="t-text-bold t-black-color">Order</div>
												<div class="t-border-label">
													<!-- <small class="t-medium-gray-color">Order Number <span class="t-primary-color">#<?php echo $order->order_id; ?></span></small> -->
													<small class="t-medium-gray-color">Order Number</small>
													<div class="t-text-bold t-black-color"><img class="t-icon-small" src="<?php echo base_url(); ?>assets/front/images/ic-shopify.png" alt="">#<?php echo $order->order_id; ?></div>
												</div>
											</div>
											<div class="cell medium-6">
												<div class="t-text-bold t-black-color">Receiver</div>
												<div class="t-border-label">
													<small class="t-medium-gray-color">Country <span class="t-primary-color"><?php echo $order->country; ?></span></small>
													<div class="t-text-bold t-black-color"><?php echo $order->name; ?></div>
												</div>
											</div>
										</div>
										<div class="grid-x t-spacing-bottom-small">									
											<div class="cell auto">
												<div class="t-text-bold t-black-color">Estimated Time</div>
											</div>
											<div class="cell auto text-right">
												<div class="t-primary-color" id="shipping-label-<?php echo $order->order_id; ?>">
													<?php
														if($order->order_id == $orderx) {
															echo $delivery_timex;
														} else {
															echo $delivery_time;
														}
													?>
												</div>
											</div>									
										</div>
										<div class="grid-x t-spacing-bottom-small">									
											<div class="cell auto">
												<?php $service = $this->session->userdata('service_'.$order->order_id); ?>
												<select name="" id="shipping-<?php echo $order->order_id; ?>" class="t-select-center u-no-margin-bottom">
													<option value="<?php echo $shipping_rate->standard_price; ?>" <?php if(isset($service) && ($service == $shipping_rate->standard_price)){ ?> selected <?php } ?> shipping-label="2-3 working day">Standard(SGD <?php echo $shipping_rate->standard_price; ?>)</option>
													<option value="<?php echo $shipping_rate->priority_price; ?>" <?php if(isset($service) && ($service ==  $shipping_rate->priority_price)){ ?> selected <?php } ?> shipping-label="1-2 working day">Priority(SGD <?php echo $shipping_rate->priority_price; ?>)</option>
													<option value="<?php echo $shipping_rate->express_price; ?>" <?php if(isset($service) && ($service == $shipping_rate->express_price)){ ?> selected <?php } ?> shipping-label="1 working day">Express(SGD <?php echo $shipping_rate->express_price; ?>)</option>
												</select>

												<script type="text/javascript">
													$(document).ready(function() {

														$('#shipping-<?php echo $order->order_id; ?>').change(function() {
															$('#shipping-label-<?php echo $order->order_id; ?>').html($(this).children("option:selected").attr("shipping-label"));
															// console.log($(this).children("option:selected").attr("shipping-label"));

															var service = $(this).children("option:selected").val();
															var order_id = <?php echo $order->order_id; ?>;

															var url = window.location.href;    
															if (url.indexOf('?') > -1) {
															   url += '&service=' + service + '&order_id=' + order_id;
															} else {
															   url += '?service=' + service + '&order_id=' + order_id;
															}

															window.location.href = url;
														});

													 });

												</script>

											</div>								
										</div>
										<!-- <div class="grid-x t-spacing-bottom-small">									
											<div class="cell auto">
												<div class="t-primary-color">No Tax</div>
											</div>								
										</div> -->
									</div>
								</div>
								<div class="card-box--footer t-border-top">
									<div class="grid-container">
										<div class="grid-x align-middle">									
											<div class="cell auto">
												<div class="t-text-bold t-black-color">Total Cost</div>
											</div>
											<div class="cell auto text-right">
												<h4 class="t-gray-color t-text-bold u-no-margin-bottom">SGD 509</h4>
											</div>									
										</div>
									</div>							
								</div>
								<div class="card-box--footer-button">
									<!-- <a href="#" class="button alert expanded">Ship</a> -->
									<input type="hidden" name="shop" value="<?php echo $shop; ?>">
									<input type="hidden" name="order_id" value="<?php echo $order->order_id; ?>">
									<button class="button alert expanded" type="submit">Ship</button>
								</div>
							</form>
						</div>
					</div>
					
					<!--item details popup starts-->
					<div class="popuptext white-popup mfp-hide" id="myPopup_<?php echo $order->order_id; ?>" style="display: ;">
						<div class="form_pop">
							<div class="items">
								<ul>
									<li>
										<div class="it-1 it-err-2">
										<div class="bg"></div>
										<span><?php echo $order->quantity; ?><br><?php echo $order->quantity > 1 ? "Items" : "Item" ;?></span></div>
									</li>
									<li>
										<div class="it-2"><span><img src="<?php echo base_url(); ?>assets/front/images/shopify.png">Order<br><p>#<?php echo $order->order_id; ?></p></span></div>
									</li>
									<li>
										<div class="it-2"><span><?php echo $order->name; ?><br><img src="<?php echo base_url(); ?>assets/front/images/flag.png"><b><?php echo $order->country; ?></b></span></div>
									</li>
									<li>
										<div class="err"><img src="<?php echo base_url(); ?>assets/front/images/errr.png"><span>Please update your item details in order to select courier</span></div>
									</li>
								</ul>
							</div>
							<?php foreach ($order->products as $key2 => $product) { ?>
								<div class="id_main id_<?php echo $product->id ?>">
									<div class="id_1">#<?php echo $product->product_id ?>
										<span><!--img src="<?php echo base_url(); ?>assets/front/images/trash.png"--></span>
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
											<input type="text" name="dimension[long]" value="<?php echo $product->long ?>">*
											<input type="text" name="dimension[wide]" value="<?php echo $product->wide ?>">*
											<input type="text" name="dimension[high]" value="<?php echo $product->high ?>">
										</div>
										<div class="dimen-2">
											Weight(Kg)<br>
											<input type="text" value="<?php echo $product->weight ?>">
										</div>
										<div class="dimen-custom">
											Customs value<br>
											<input type="text" value="">
										</div>
										<div class="dimen-3">
										<br>
											<select>
										 		<option>SGD</option>
										 		<option>SGD</option>
										 	</select>
										</div>
									</div>
								</div>
							<?php } ?>
						</div>
						<div style="clear: both;"></div>
					</div>
					<!--item details popup ends-->

				<?php } ?>

			</div>
		</div>

		<div class="pagination-simple bottom">
			<ul>
				<?php echo $paging;?>
			</ul>
		</div>
	</div>

</div>