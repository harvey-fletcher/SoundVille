<?php
    //Use the session
    session_start();

    //Page requires DB connection
    include '../config/database.php';

    //We will need to get a list of all the products
    $productsQuery = $db->prepare( "SELECT p.*, i.image_url FROM products p JOIN product_images i ON p.product_image_id=i.id" );
    $productsQuery->execute();

    //Put all the products in an array
    $products = $productsQuery->fetchAll( PDO::FETCH_ASSOC );
?>
<html>
    <head>
        <link rel="stylesheet" href="main.css" type="text/css"/>
        <title>Linkenfest 2019</title>
    </head>
    <body>
        <img src="https://files.linkenfest.co.uk/logo_png.png" class="main-logo"/>
        <div class="signInWidget">
            <?php include 'signInWidget.php'; ?>
        </div>
        <div class="links" align="right">
            <?php include 'menu.php'; ?>
        </div>
        <div class="mainBodyContainer">
            <?php foreach( $products as $key=>$product ){ ?>
                <div class="productParent">
                    <div class="productImageContainer">
                        <img src="<?= $product['image_url']; ?>" width="100%" height="100%" />
                    </div>
                    <div class="productDetails">
                         <h2 class="noMargin"><?= $product['product_name'];?></h2>
                         <h2 class="noMargin">£<?= $product['product_price'];?></h2>
                         <br />
                         <p><?= $product['product_description']; ?></p>
                         <div class="productOptions">
                             <?php if( isset( $_SESSION['email'] ) ){ ?>
                                 <select id="quantity-item-<?=$product['id'];?>" class="productQuantitySelect">
                                     <option value="1">1</option>
                                     <option value="2">2</option>
                                     <option value="3">3</option>
                                     <option value="4">4</option>
                                     <option value="5">5</option>
                                 </select>
                                 <button id="add-product-<?=$product['id'];?>" onclick="addItemsToBasket( this.id );" class="signOutButton">Add to basket</button>
                             <?php } else { ?>
                                 You must be signed in to add this product to cart. <a href="createAccount.php"><u>Create Account</u></a>
                             <?php } ?>
                         </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </body>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript">
        function addItemsToBasket( id ){
            var productId = id.split('-')[2];
            var quantity  = $('#quantity-item-' + productId).val();

            $.post(
                "https://api.linkenfest.co.uk/basket/add",
                { session: '<?= session_id(); ?>', id: productId, productQuantity: quantity }
            ).done(function( data ){
                alert( data.data.message );

                if( data.status == 200 ){
                    var oldQuantity = $('#cartButton').text().split('(')[1].split(')')[0];
                    var newQuantity = parseInt(oldQuantity) + parseInt(quantity);

                    //Update button text
                    $('#cartButton').text('Basket ('+ newQuantity +')');
                }
            });
        }
    </script>
</html>
