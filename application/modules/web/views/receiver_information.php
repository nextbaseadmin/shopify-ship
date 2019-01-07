<div id="main-content">	

	<div class="grid-container">

		<div class="main__title">
			<h4 class="t-text-bold">Receiver Information</h4>
		</div>

		<div class="card-box">
			<div class="grid-container fluid">
				<div class="grid-x grid-margin-x">
					<div class="cell small-12 medium-6">
						<label for="">Full Name
							<input type="text" value="<?php echo $shipping->name; ?>">
						</label>
					</div>
					<div class="cell small-12 medium-3">
						<label for="">Contact Number
							<input type="text" value="<?php echo $shipping->phone; ?>">
						</label>
					</div>
					<div class="cell small-12 medium-3">
						<label for="">Email (optional)
							<input type="text" value="<?php echo $order->email; ?>">
						</label>
					</div>
				</div>				
				<div class="grid-x grid-margin-x">
					<div class="cell small-12 medium-6">
						<label for="">Address Line 1
							<input type="text" value="<?php echo $shipping->address1; ?>">
						</label>
					</div>
					<div class="cell small-12 medium-6">
						<label for="">Address Line 2 (optional)
							<input type="text" value="<?php echo $shipping->address2; ?>">
						</label>
					</div>
				</div>
				<div class="grid-x grid-margin-x">
					<div class="cell small-12 medium-3">
						<label for="">Country
							<input type="text" value="<?php echo $shipping->country; ?>">
						</label>
					</div>
					<div class="cell small-12 medium-3">
						<label for="">Postal Code
							<input type="text" value="<?php echo $shipping->postcode; ?>">
						</label>
					</div>
					<div class="cell small-12 medium-3">
						<label for="">City
							<input type="text" value="<?php echo $shipping->city; ?>">
						</label>
					</div>
					<div class="cell small-12 medium-3">
						<label for="">State / Province (optional)
							<input type="text" value="<?php echo $shipping->province; ?>">
						</label>
					</div>
				</div>		
			</div>
		</div>

		<div class="footer__nav">
			<div class="footer__nav--right">
				<a href="<?php echo base_url(); ?>pickup_information?shop=<?php echo $shop; ?>" class="button alert button-right">Next Step</a>
			</div>
		</div>
	</div>
</div>