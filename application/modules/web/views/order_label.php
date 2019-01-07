<html>
	<body>
		<div>
			<h3>Ship Orders</h3>
			<form action=<?php echo base_url() ?>"orders" method="GET">
				<input type="text" name="order_id">
				<input type="submit" name="Search">
			</form>
			<table>
				<tr>
					<td style="padding: 10px;">Order Id</td>
					<td style="padding: 10px;">Shipment Number</td>
					<td style="padding: 10px;">Label</td>
				</tr>
				
				<?php if($tipe == 'one') { ?>
					<tr>
						<td style="padding: 10px;">
							<?php echo $orders->order_id;  ?>
						</td>
						<td style="padding: 10px;">
							<?php echo $orders->shipment_number;  ?>
						</td>
						<td style="padding: 10px;">
							<a href="<?php echo $orders->label;  ?>" target="_blank">
								<?php echo $orders->label;  ?>
							</a>
						</td>
					</tr>
					<br>
					<a href=<?php echo base_url() ?>"orders">Back</a>
				<?php } else { ?>
					<?php foreach($orders as $key => $order) {?>
						<tr>
							<td style="padding: 10px;">
								<?php echo $order->order_id;  ?>
							</td>
							<td style="padding: 10px;">
								<?php echo $order->shipment_number;  ?>
							</td>
							<td style="padding: 10px;">
								<a href="<?php echo $order->label;  ?>" target="_blank">
									<?php echo $order->label;  ?>
								</a>
							</td>
						</tr>
					<?php } ?>
				<?php } ?>
			</table>
		</div>
		
	</body>
</html>