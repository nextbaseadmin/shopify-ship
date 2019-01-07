<div id="main-content">
	
	<div class="grid-container header-top">
		<div class="main__title">
			<h4 class="t-text-bold">Manage Address</h4>
		</div>
	</div>

	<div class="grid-container">
		<form action="<?php echo site_url('manage_address_in');?>" method="post">
			<input type="hidden" name="shop" value="<?php echo $shop;?>">
			<input type="hidden" value="SG" name="country-code" required>
						
			<table class="table-default t-spacing-top-small">
				<thead>
					<tr>
						<th>ADDRESS</th>
						<th>PROVINCE</th>
						<th>CITY</th>
						<th>COUNTRY</th>
						<th>POSTCODE</th>
						<th>ACTION</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($get_address->result() as $key => $ship) { ?>
					<tr>
						<td><?php echo $ship->address1;?></td>
						<td><?php echo $ship->province;?></td>
						<td><?php echo $ship->city;?></td>
						<td><?php echo $ship->country_name;?></td>
						<td><?php echo $ship->postcode;?></td>
						<td>
							<a href="<?php echo base_url(); ?>manage_address_edit?shop=<?php echo $shop; ?>&id=<?php echo $ship->id;?>" class="js-label-url button alert">Edit</a>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>

			<div class="footer__nav">
				<div class="footer__nav--left">
					<a href="<?php echo base_url(); ?>pickup_information?shop=<?php echo $shop; ?>" class="button hollow gray button-left">Back</a>
				</div>
				<div class="footer__nav--right">
					<a href="<?php echo base_url(); ?>manage_address_add?shop=<?php echo $shop; ?>" class="button alert button-right">Add Address</a>
				</div>
			</div>
		</form>
	</div>
</div>