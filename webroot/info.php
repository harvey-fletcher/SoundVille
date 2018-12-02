<?php
    session_start();

    include '../config/database.php';
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
            <div align="center" class="fullWidth">
                <h1 class="noMargin"><u>Information</u></h1>
            </div>
            <h2 class="noMargin">What is Linkenfest?</h3>Linkenfest is a small music festival presenting local bands and individual artists.<br /><br />
            <h2 class="noMargin">When is it?</h2>Linkenfest will be taking place from the 19th to 21st of July 2019.<br /><br />
            <h2 class="noMargin">Where is it?</h2>Linkenfest is at the Linkenholt Adventure Centre, Linkenholt, SP11 0EA.<br /><br />
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2096.585375854682!2d-1.477779317007439!3d51.324475400045465!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x487400307f1cf205%3A0xe5696ab08fabfae7!2sLinkenholt+Adventure+Centre+%2F+Campsite!5e0!3m2!1sen!2suk!4v1543786671440" width="600" height="450" frameborder="0" style="border:0" allowfullscreen></iframe><br /><br />
            <h2 class="noMargin">Age Restrictions</h2>The festival is open to everyone over the age of 18.<br /><br />
            <h2 class="noMargin">Lost Property</h2>Should you lose an item of personal belonging, please contact the lost and found at information@linkenfest.co.uk<br />If you are bringing a mobile phone with you, please make note of the device IMEI number. For any other portable device, please note the serial number. The organisers cannot be held responsible for any item of personal property which gets lost or damaged.<br /><br />
            <h2 class="noMargin">What you can and can't bring</h2>
                <h3 class="noMargin">Confiscated / surrendered items cannot be returned to you after the event</h3>
                Please read through this list to ensure that you don't bring any non-permitted items with you.<br /><br />
                <table class="noBorder">
                <tr>
                    <td class="itemsList tableHeading">
                        Item
                    </td>
                    <td class="itemsList tableHeading">
                        Permitted?
                    </td>
                    <td class="itemsList tableHeading">
                        Conditions
                    </td>
                </tr>
                <?php
                $itemsQuery = $db->prepare("SELECT * FROM permittedItems");
                $itemsQuery->execute();

                $itemsList = $itemsQuery->fetchAll( PDO::FETCH_ASSOC );

                foreach( $itemsList as $key=>$item){
                ?>
                    <tr>
                        <td class="itemsList">
                            <?= $item['item_name']; ?>
                        </td>
                        <td class="itemsList" align="center">
                            <?= $item['permitted']; ?>
                        </td>
                        <td class="itemsList">
                            <?= $item['notes']; ?>
                        </td>
                    </tr>
                <?php } ?>
                </table>
                <br /><br />
                <h2 class="noMargin">Terms and Conditions</h3>
                <ul>
                    <li>
                        <h3 class="noMargin">Terms and Conditions</h3>
                        <ul>
                            <li>Artists may be subject to change or cancellation.</li>
                            <li>No trading or promotional activities allowed within the venue without express consent.</li>
                            <li>Photographic equipment is permitted provided that it is for personal non-commercial use only.</li>
                            <li>Only tickets purchased from <u><a href="https://linkenfest.co.uk">linkenfest.co.uk</a></u> will be valid.</li>
                            <li>By attending the event, express consent is given for use of your image, or true likeness, in any media or promotional activity by Linkenfest or associates (e.g. bands or artists)</li>
                        </ul>
                    <li>
                        <h3 class="noMargin">Refunds and Cancellations</h3>
                        <ul>
                            <li>Refunds are only considered on major cancellation. (Where less than 60% of the acts are shown, or the event is cancelled by reasons not due to adverse weather)</li>
                            <li>Refunds should be obtained by contacting information@linkenfest.co.uk no later than 1 month after the event</li>
                            <li>A minimum of the face value of the ticket (minus any processing fees) will be refunded.</li>
                            <li>The site processing fee will not be refunded.</li>
                        </ul>
                    </li>
                    <li>
                        <h3 class="noMargin">Age Policy</h3>
                        <ul>
                            <li>This is an 18+ event. Please bring ID</li>
                            </li>If you cannot present a valid ticket and a valid form of photo ID (drivers license / passport / citizien card), you will be refused entry to the event.</li>
                        </ul>
                    </li>
                    <li>
                        <h3 class="noMargin">Tickets and wristbands</h3>
                        <ul>
                            <li>Tickets are non-transferable and only valid when purchased from <u><a href="https://linkenfest.co.uk">linkenfest.co.uk</a></u>.</li>
                            <li>Weekend ticket holders MUST exchange their ticket for a wristband on entry.</li>
                            <li>Day ticket holders MUST be in posession of a physical copy of their ticket at all times.</li>
                            <li>Tickets MUST be printed or they will be considered invalid and you will be refused entry to the event.</li>
                            <li>The ticket holder is responsible for their tickets until it is exchanged for a wristband and then responsible for their wristband for the duration of the event.</li>
                        </ul>
                    </li>
                    <li>
                        <h3 class="noMargin">Security</h3>
                        <ul>
                            <li>The organiser reserves the right to evict a customer without refund, and/or refuse admission.</li>
                            <li>Your bags or other possessions may be searched. This is a condition of entry. Refusal will result in refusal of entry</li>
                            <li>Any items which could reasonably be considered as a weapon must be surrendered prior to entry.</li>
                            <li>Should you be found in posession of any item which could reasonably be considered as a weapon after you have entered the event, you will be evicted, and where necessary, the police will be contacted.</li>
                            <li>Any person carrying illegal items or carrying out illegal activity will be given to the Police and refused entry.</li>
                            <li>Anti-social behaviour may lead to eviction. Please act responsibly.</li>
                            <li>Throwing gas, aerosol or similar canisters/containers on to fires is extremely dangerous and will lead to eviction.</li>
                        </ul>
                    </li>
                    <li>
                        <h3 class="noMargin">Banned Items</h3>
                        <ul>
                            <li>Banned from event – Gas canisters, aerosols over 250ml, airhorns, fireworks, flares, glass, illegal substances, drugs, ‘legal highs’ – this includes Nitrous Oxide and associated equipment including balloons, unidentified substances, new psychoactive substances (NPS), illegal items, laser equipment/pens, megaphones, blowtorches, sky or ‘chinese’ lanterns, petrol burners, spray cans, tabards/high viz jackets.</li>
                            <li>Generators with the exception of fixed in campervans.</li>
                            <li>Anyone resisting the surrender of disallowed items or disregarding these conditions will face eviction.</li>
                            <li>Items that are surrendered or confiscated will not be returned</li>
                        </ul>
                    </li>
                    <li>
                        <h3 class="noMargin">Fire and Safety</h3>
                        <ul>
                            <li>Campfires are not permitted unless they are in the designated fire pit. The organiser is NOT responsible for any injury or personal loss by campfire.</li>
                            <li>The burning of plastics, bedding, tents, furniture etc is not permitted anywhere on site.</li>
                            <li>Smoking is not permitted in enclosed public spaces or buildings or archways.</li>
                            <li>Excessive exposure to loud music may cause damage to your hearing.</li>
                            <li>Lasers, smoke machines, strobe lighting/special effects may take place during some performances.</li>
                            <li>The use of Drones or similar equipment for any reason is prohibited on or near the event without written permission</li>
                        </ul>
                    </li>
                    <li>
                        <h3 class="noMargin">Housekeeping</h3>
                        <ul>
                            <li>Please use the bins and sanitary facilities provided.</li>
                        </ul>
                    </li>
                </ul>
                <br /><br />
                <h2 class="noMargin">
                    If any of these terms are unclear please contact information@linkenfest.co.uk
                </h2>
        </div>
    </body>
</html>
