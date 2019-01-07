<div id="main-content">	
	<div class="grid-container">

		<div class="main__title">
			<h4 class="t-text-bold">Shipping Failed</h4>
		</div>

		<div class="card-box">
			<div class="grid-container fluid">
				<div class="grid-x grid-margin-x">
					<div class="cell medium-6">
						<div class="t-spacing-bottom-small">
							<div class="t-spacing-bottom-small">
								<img class="t-inline-block t-spacing-right-small" src="<?php echo base_url(); ?>assets/ship/images/warning - material.png" alt="">
								<h4 class="t-inline-block t-text-bold t-valign-middle u-no-margin-bottom">Recurring Application Charges</h4>
							</div>

							<div class="t-text-bold t-black-color">ID : <?php echo $response->recurring_application_charge->id; ?></div>
							<div>Name : <?php echo $response->recurring_application_charge->name; ?></div>

						</div>
						<a href="<?php echo $response->recurring_application_charge->confirmation_url; ?>" class="button alert expanded u-no-margin-bottom" id="confirm">Confirm</a>
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