<?php
 require 'lib/shopify.php';

define('SHOPIFY_API_KEY','c9e11482c68962f39b380dfbe849e678');
define('SHOPIFY_SECRET','65219b9a1a07d634edd79de3f86fae0e');


define('SHOPIFY_SCOPE','read_orders,write_orders,read_products,write_products'); 

if (isset($_GET['code'])) { 
    $shopifyClient = new ShopifyClient($_GET['shop'], "", SHOPIFY_API_KEY, SHOPIFY_SECRET);
    session_unset();
    $_SESSION['token'] = $shopifyClient->getAccessToken($_GET['code']);
    if ($_SESSION['token'] != '')
        $_SESSION['shop'] = $_GET['shop'];
    header("Location: index.php");
    exit;       
}
else if (isset($_POST['shop'])) {

    $shop = isset($_POST['shop']) ? $_POST['shop'] : $_GET['shop'];
    $shopifyClient = new ShopifyClient($shop, "", SHOPIFY_API_KEY, SHOPIFY_SECRET);

	$pageURL = "https://duspay.myshopify.com/admin/apps";
  
    header("Location: " . $shopifyClient->getAuthorizeUrl(SHOPIFY_SCOPE, $pageURL));
    exit;
}

?>
<p>Install this app in a shop to get access to its private admin data.</p> 

<p style="padding-bottom: 1em;">
    <span class="hint">Don&rsquo;t have a shop to install your app in handy? <a href="https://app.shopify.com/services/partners/api_clients/">Create a test shop.</a></span>
</p> 

<form action="" method="post">
  <label for='shop'><strong>The URL of the Shop</strong> 
    <span class="hint">(enter it exactly like this: myshop.myshopify.com)</span> 
  </label> 
  <p> 
    <input id="shop" name="shop" type="text" value="" /> 
    <input name="commit" type="submit" value="Install" /> 
  </p> 
</form>