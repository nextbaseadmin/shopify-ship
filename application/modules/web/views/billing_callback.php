<script type="text/javascript">
	
	$(document).ready(function() {
		$('#test-popup').magnificPopup({type:'inline'});
	});

</script>
<style type="text/css">
	.white-popup {
		height: 385px;	
		width: 546px;	
		border-radius: 5px;	
		background-color: #FFFFFF;	
		box-shadow: 0 0 10px 0 rgba(0,0,0,0.2);
		text-align: center;
	}

	.rectangle-4 {	height: 72px;	width: 496px;	border-radius: 3px;	background-color: #24559D; }
</style>

<div id="main-content">
	<div class="grid-container">
		
		<div class="card-box">
			<div class="card-box__row">
				
				<div class="card-box__column">
					<p class="t-black-color">ID : <?php echo $response->recurring_application_charge->id; ?></p>
					<p class="t-black-color">Name : <?php echo $response->recurring_application_charge->name; ?></p>
					<p class="t-black-color">Price : <?php echo $response->recurring_application_charge->price; ?></p>
					<p class="t-black-color">Balance Used : <?php echo $response->recurring_application_charge->balance_used; ?></p>
					<p class="t-black-color">Balance Remaining : <?php echo $response->recurring_application_charge->balance_remaining; ?></p>
					<p class="t-black-color">Status : <?php echo $response->recurring_application_charge->status; ?></p>
				</div>

			</div>
		</div>

		<div class="footer__nav">
			<div class="footer__nav--left">
				<a href="<?php echo base_url(); ?>pickup_information?shop=<?php echo $shop; ?>" class="button hollow gray button-left">Back</a>
			</div>
			<div class="footer__nav--right">
				<a href="<?php echo base_url(); ?>confirm?shop=<?php echo $shop; ?>" class="button primary button-right">Next Step</a>
			</div>
		</div>

	</div>
</div>