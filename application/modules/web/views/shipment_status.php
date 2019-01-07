<div id="main-content" class="t-gray-bg">

	<div class="grid-container">
		
		<div class="main__title">
			<h4 class="t-text-bold">Shipment Status</h4>
		</div>

		<div class="table-list">
			
			<div class="table-list__filter">
				<form>
				  <div class="grid-container full">
				    <div class="grid-x grid-margin-x">
				      <div class="auto xmedium-4 cell">
				        <div class="datepicker__icon">
				          <input type="text" name="start_date" class="datepicker" placeholder="Start Date" id="start_date" <?php if(!empty($start_date)){ ?> value="<?php echo $start_date ?>" <?php } ?>>
				        </div>
				      </div>
				      <div class="auto xmedium-4 cell">
				        <div class="datepicker__icon">
				          <input type="text" name="end_date" class="datepicker" placeholder="End Date" id="end_date" <?php if(!empty($end_date)){ ?> value="<?php echo $end_date ?>" <?php } ?> >
				        </div>
				      </div>
				      <div class="auto xmedium-3 cell">
				        <button class="button hollow gray expanded u-no-margin-bottom" id="filter">Filter</button>
				      </div>
				    </div>
				  </div>
				</form>

				<script type="text/javascript">

					$(document).ready(function() {

						$('#filter').click(function() {

							var start_date = $('#start_date').val();
							var end_date = $('#end_date').val();

							var url = window.location.href;    
							if (url.indexOf('?') > -1){
							   url += '&start_date='+start_date+'&end_date='+end_date;
							}else{
							   url += '?shop=<?php echo $shop; ?>&start_date='+start_date+'&end_date='+end_date;
							}

							window.location.href = url;
						});

						$('#reset').click(function() {

							window.location.href = '<?php echo base_url();?>shipment_status?shop=<?php echo $shop; ?>';
						});

					 });

				</script>

			</div>

			<table class="table-default t-spacing-top-small">
				<thead>
					<tr>
						<!-- <th>ORDER NO</th>
						<th>RECEIVER</th>
						<th>TRACKING NUMBER</th>
						<th>SERVICE</th>
						<th>CREATED</th>
						<th>STATUS</th>
						<th>LABEL</th> -->

						<th>ORDER NO</th>
						<th>SHIPMENT NUMBER</th>
						<th>CREATED AT</th>
						<th>STATUS</th>
						<th>LABEL</th>

					</tr>
				</thead>
				<tbody>

					<?php foreach ($shipments as $key => $ship) { ?>
						<tr>
							<td><?php echo $ship->order_id;?></td>
							<td><?php echo $ship->shipment_number;?></td>
							<td><?php echo $ship->created_date;?></td>
							<td><?php echo $ship->status;?></td>
							<td>
								
								<?php		
								$image = base_url().'data/'.$ship->shipment_number.'.pdf.jpg';
								$imageData = base64_encode(file_get_contents($image));  
								$src = 'data: '.mime_content_type($image).';base64,'.$imageData;  
								?>
								
								<a href="#label_<?php echo $ship->shipment_number;?>" class="open-popup-link button xhollow expanded alert u-no-margin-bottom">View</a>

								<div class="white-popup mfp-hide" id="label_<?php echo $ship->shipment_number;?>">
									<div class="label__image">
										<?php if($ship->img) { ?>
											<img src="<?php echo $src;?>">
										<?php } else { ?>
											<span style="color: white">Image Rendering Process</span>
										<?php } ?>
									</div>
									<div class="label__button">
										<a href="<?php echo base_url() ?>data/<?php echo $ship->shipment_number;?>.pdf" target="_blank" class="js-label-url button blue">View PDF</a>
									</div>
								</div>
								
							</td>

						</tr>
					<?php } ?>
				</tbody>
			</table>

			<div class="pagination-simple bottom">
				<ul>
					<?php echo $paging;?>
				</ul>
			</div>

		</div>

	</div>

</div>