
 <section class="content">
 	<div class="col-xs-12">
 		<div class="box">
 			<?php echo $output; ?>
 		</div>
 	</div>	
</section>


<?php 
foreach($css_files as $file): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
<?php endforeach; ?>
<?php foreach($js_files as $file): ?>
    <script src="<?php echo $file; ?>"></script>
<?php endforeach; 
?>

