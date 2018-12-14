<?php
    //Uses session
    session_start();

    //This page should only be accessible if the user is signed in
    include 'sessionAccess.php';

    //Need to see the products in the basket
    include '../controllers/basketController.php';
    $basketController = new basketController();
    $basket = $basketController->getContents();
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
        <div class="mainBodyContainer" align="center">
            <h2 class="noMargin"><u>Your Basket</u></h2>
            <br />
            <?php foreach( $basket['basket_items'] as $key=>$product ){ ?>
                <div class="productParent" align="left">
                    <div class="productImageContainer">
                        <img src="<?= $product['image_url']; ?>" width="100%" height="100%" />
                    </div>
                    <div class="productDetails">
                        <h2 class="noMargin">
                            <?= $product['product_name'];?><br  />
                            £<?= $product['product_price'];?> each<br />
                            <br />
                            Quantity: <?= $product['quantity'];?><br />
                            Total Price ( <?= $product['quantity'];?> X £<?= $product['product_price'];?> ): £<?= $product['item_total'];?>
                        </h2>
                        <div class="productOptions">
                             <select id="quantity-item-<?=$product['product_id'];?>" class="productQuantitySelect">
                                 <option value="1" <?php if( $product['quantity'] == 1 ){ ?>selected<?php } ?>>1</option>
                                 <option value="2" <?php if( $product['quantity'] == 2 ){ ?>selected<?php } ?>>2</option>
                                 <option value="3" <?php if( $product['quantity'] == 3 ){ ?>selected<?php } ?>>3</option>
                                 <option value="4" <?php if( $product['quantity'] == 4 ){ ?>selected<?php } ?>>4</option>
                                 <option value="5" <?php if( $product['quantity'] == 5 ){ ?>selected<?php } ?>>5</option>
                             </select>
                             <button id="update-product-<?=$product['product_id'];?>" onclick="amendOrderQuantity( this.id );" class="signOutButton">Amend</button>
                             <button id="remove-product-<?=$product['product_id'];?>" onclick="removeOrderItems( this.id );" class="signOutButton">Remove</button>
                         </div>
                        <br />
                        <?php if( $product['quantity'] > $product['product_max_per_purchase'] ){ ?>
                            <h3 class="warning noMargin">
                                You have selected too many of this item and you will not be able to check out.<br />
                                You must first amend your order in the basket.
                            </h3>
                        <?php } ?>
                        <?php if(!$product['in_stock']){ ?>
                            <h3 class="warning noMargin">
                                There is not enough of this product remaining.<br />
                                You will need to decrease your selection before you can check out.<br />
                            </h3>
                        <?php } ?>
                    </div>
                </div>
            <?php
            }
            ?>
            <?php if( sizeof($basket['basket_items']) > 0 ){ ?>
                <div class="productParent">
                    <div class="orderOptions" align="right">
                        <h3 class="noMargin">
                            Order SubTotal: £<?= $basket['sub_total']; ?><br />
                            Processing Charge: £<?= $basket['processing_fee'] ?> <a href="info.php?section=fees">(?)</a><br />
                            <br />
                            Order Total: £<?= $basket['order_total']['decimal'] ?><br />
                        </h3>
                    </div>
                </div>
            <?php } else { ?>
                <h4 class="noMargin">Your basket is empty</h4>
            <?php } ?>
        </div>
    </body>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript">
        function amendOrderQuantity( id ){
            var product_id  = id.split("-")[2];
            var newQuantity = $('#quantity-item-' + product_id).val();

            $.post(
                "https://api.linkenfest.co.uk/basket/amend",
                { session: '<?= session_id(); ?>', amend_id: product_id, new_quantity: newQuantity }
            ).done(function( data ){
                alert( data.data.message );
                window.location.reload();
            });
        }

        function removeOrderItems( id ){
            var product_id  = id.split("-")[2];

            $.post(
                "https://api.linkenfest.co.uk/basket/remove",
                { session: '<?= session_id(); ?>', amend_id: product_id }
            ).done(function( data ){
                alert( data.data.message );
                window.location.reload();
            });
        }
    </script>
</html>
