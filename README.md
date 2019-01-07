# shopify shipping
Shopify Shipping is a complete Shopify solution to manage shipments &amp; shipping methods and shipping cost calculation for modern PHP Applications

INSTALLATION

- Minimum requirement: Apache2 + php5.6 MySQL
- PHP Library: GD-Lib, ImageMagick
- Framework: PHP Codeigniter
- Upload all files to document root directory
- Chmod 777 these folder: logs, upload, cache, data
- Restore MySQL database
- Change connection database in ~application/config/databases.php
- Open data/label.sh & data/label.php change to your path
- Open api/GenerateTicket.php and update as Shopify Shipping credential 


USAGE

Setting steps
1. Change the shopify api and secret key in application/config/shopify.php
2. Fill your database details in application/config/database.php
3. Import database backup from sql/shopifyshipping.sql to your database
4. Setup Shopify Shipping rate on admin panel

 	https://your-domain/admin/login
  
 	user : admin@shipping.com
  
 	password : admin123
    

This source integrate Shipping with Shopify platform.
If you want to integrate this source with another platform you can adjust api setting on application/config/shopify.php and application/helpers/shopify_helper.php.

