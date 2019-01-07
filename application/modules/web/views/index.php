
<script src="https://cdn.shopify.com/s/assets/external/app.js"></script>

<script type='text/javascript'>
<?php 
$api_key        = $this->config->item('shopify_api_key');
$scopes         = "read_shipping,write_shipping,read_orders,write_orders,read_customers,write_customers,read_themes,read_products,read_fulfillments,write_fulfillments";
$redirect_uri   = base_url()."ship_connect/";
$state          = uniqid();
?>
var url = window.location.ancestorOrigins[0];
console.log(url);
url = url.replace("https://","");
console.log(url);

window.location = "<?php echo base_url() ?>home/?shop=" + url;
</script>