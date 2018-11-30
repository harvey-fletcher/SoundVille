<?php

    //This is the total cost
    $orderTotal = 0;

    //Start the order summary table
    $orderSummary = "<table border='0' style='display: inline-block'>"
                  . "<tr><td>Quantity</td><td>Item</td><td>Sub-total</td>";

    //Build the order summary
    foreach( $basketItems as $key=>$item ){
        $itemTotal = number_format( ( (int)$item['quantity'] * (float)$item['product_price'] ), 2, '.', '' );
        $orderTotal += $itemTotal;

        $row = "<tr>"
             .     "<td>". $item['quantity'] ." x</td>"
             .     "<td>". $item['product_name'] ."&nbsp; &nbsp;</td>"
             .     "<td>&pound;". $itemTotal . "</td>"
             . "</tr>";

        //Add this product row to the order summary
        $orderSummary .= $row;
    }

    //There is a blank row, followed by a complete order total
    $orderSummary .= "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp</td>"
                   . "<tr><td>&nbsp;</td><td>Total:</td><td>&pound;" . number_format( $orderTotal, 2, '.', '') . "</td>";

    //Close off the order summary
    $orderSummary .= "</table>";

    //Assemble the full email
    $emailBody = "<div style='width: 750'>"
               .     "<div style='float: left; width: 150px; height: 150px;'>"
               .         "<img src='https://files.linkenfest.co.uk/logo_png.png' style='width: 150px; height: 150px;' />"
               .     "</div>"
               .     "<div style='float: left; height: 150;' align='right'>"
               .         "<h1 style='margin: 0; font-size: 130px;'>Linkenfest</h1>"
               .     "</div>"
               . "</div>"
               . "<div style='width: 750; margin-top: 25px; display: inline-block;'>"
               .     "<h4 style='margin: 0;'>"
               .         "Hello,<br />Thank you for booking your tickets to Linkenfest 2019! We're happy to have you with us.<br />"
               .         "It is extremely important that you print this email off and keep it safe. You <b>MUST</b> bring it with you<br />"
               .         "to the festival, or you will not be granted entry.<br />"
               .         "<br /><br />"
               .         "Your order number is <b><u>" . $orderNumber . "</u></b>.<br />"
               .         "Your reference is: <b><u>" . $orderReference . "</u></b>.<br />"
               .         "<br /><br />"
               .         "Here is a summary of your order:<br /><br />"
               .         $orderSummary
               .         "<br /><br />"
               .         "Gates open 2pm on Friday 19th July 2019. If you are a day-ticket holder, you will not be permitted to re-enter<br />"
               .         "the site once you have left. All guests must have vacated the premises by 19:00 on Sunday 22nd July 2019<br /><br />"
               .         "<br /><br />"
               .         "Event address:<br />"
               .         "&nbsp;&nbsp;&nbsp;&nbsp;Linkenholt Adventure Centre<br />"
               .         "&nbsp;&nbsp;&nbsp;&nbsp;Linkenholt<br />"
               .         "&nbsp;&nbsp;&nbsp;&nbsp;Wiltshire<br />"
               .         "&nbsp;&nbsp;&nbsp;&nbsp;SP11 0EA"
               .         "<br /><br />"
               .         "Questions? Contact us!<br />0751 174 9870<br />https://www.linkenfest.co.uk"
               .     "</h4>"
               . "</div>";

//$emailBody = "<p>test</p>";
