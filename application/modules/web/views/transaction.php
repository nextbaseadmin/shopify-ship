<div id="main-content" class="t-gray-bg">

	<div class="grid-container">
		
		<div class="main__title">
			<h4>Transactions</h4>
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
							if (url.indexOf('?') > -1) {
							   url += '&start_date=' + start_date + '&end_date=' + end_date;
							} else {
							   url += '?start_date=' + start_date + '&end_date=' + end_date;
							}

							window.location.href = url;
						});

						$('#reset').click(function() {

							window.location.href = '<?php echo base_url();?>transactions?shop=<?php echo $shop; ?>';
						});

					 });

				</script>

			</div>

			<table class="table-default t-spacing-top-small">
				<thead>
					<tr>
						<th>PRICE (SGD)</th>
						<th>DESCRIPTION</th>
						<th>CREATED</th>
						<th>TIME</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($transactions as $key => $trans) { ?>
					<tr>
						<td><?php
							$number = $trans->price;
							setlocale(LC_MONETARY,"en_SG");
							echo $number;
							?></td>
						<td><?php echo $trans->description;?></td>
						<td><?php echo date("Y/m/d", strtotime($trans->created_at));?></td>
						<td><?php echo date("H:i:s", strtotime($trans->created_at));?></td>
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
