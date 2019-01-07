<div id="main-content">
	
	<div class="grid-container header-top">
		<div class="main__title">
			<h4 class="t-text-bold">Edit Address</h4>
		</div>
	</div>

	<div class="grid-container">
		<form action="<?php echo site_url('manage_address_in');?>" method="post">
			<input type="hidden" name="id" value="<?php echo $get_address->id;?>">
			<input type="hidden" name="shop" value="<?php echo $shop;?>">
			<div class="card-box">
				<div class="grid-container fluid">
					<div class="grid-x grid-margin-x">
						<div class="cell small-12 medium-6">
							<label for="">Email
								<input type="email" name="email" value="<?php echo $get_address->email;?>" required>
							</label>
						</div>
						<div class="cell small-12 medium-6">
							<label for="">Phone
								<input type="number" name="phone" value="<?php echo $get_address->phone;?>" required>
							</label>
						</div>
						<div class="cell small-12 medium-6">
							<label for="">Address
								<input type="text" name="address-1" value="<?php echo $get_address->address1;?>" required>
							</label>
						</div>
						<div class="cell small-12 medium-6">
							<label for="">Province
								<input type="text" name="province" value="<?php echo $get_address->province;?>" required>
							</label>
						</div>

						<div class="cell small-12 medium-6">
							<label for="">City
								<input type="text" name="city" value="<?php echo $get_address->city;?>" required>
							</label>
						</div>

						<div class="cell small-12 medium-6">
							<label for="">Country
								<input type="text" name="country-name" value="<?php echo $get_address->country_name;?>" required>
							</label>
						</div>

						<div class="cell small-12 medium-6">
							<label for="">Post Code
								<input type="number" name="post-code" value="<?php echo $get_address->postcode;?>" required>
							</label>
						</div>
					</div>
				</div>
			</div>

			<div class="footer__nav">
				<div class="footer__nav--left">
					<a href="<?php echo base_url(); ?>manage_address?shop=<?php echo $shop; ?>" class="button hollow gray button-left">Back</a>
				</div>
				<div class="footer__nav--right">
					<input type="submit" name="submit" value="Save" class="button alert">
				</div>
			</div>

		</form>

	</div>
</div>
