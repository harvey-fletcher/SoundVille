<?php
    session_start();

    include '../config/database.php';

    //Automatically open any sections?
    $auto    = false;
    $section = "";

    if( isset( $_GET['section'] ) ){
        $auto = true;
        $section = "show-" . $_GET['section'];
    }
?>
<html>
    <head>
        <link rel="stylesheet" href="main.css" type="text/css"/>
        <title>SoundVille 2019</title>
    </head>
    <body <?php if( $auto ){ ?>onload=" displaySect( '<?= $section; ?>' ) "<?php }?>>
        <img src="https://files.soundville.co.uk/logo_png.png" class="main-logo"/>
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
            <h2 class="noMargin" onclick=" displaySect( this.id ) " id="show-whatIs">
                What is SoundVille? [+]
            </h3>
            <div class="hidden" id="whatIs">
                SoundVille is a small music festival presenting local bands and individual artists.<br /><br />
            </div>
            <br />
            <h2 class="noMargin" onclick=" displaySect( this.id ) " id="show-whenIs">
                When is it? [+]
            </h2>
            <div id="whenIs" class="hidden">
                SoundVille will be taking place from the 19th to 21st of July 2019.<br />
            </div>
            <br />
            <h2 class="noMargin" onclick=" displaySect( this.id ) " id="show-whereIs">
                Where is it? [+]
            </h2>
            <div class="hidden" id="whereIs">
                SoundVille is at the Dummer Cricker Centre, Dummer, RG25 2AR.
                <br />
                <br />
                <iframe src="https://maps.google.com/maps?q=Dummer%20Cricket%20Center&t=&z=13&ie=UTF8&iwloc=&output=embed" width="600" height="450" frameborder="0" style="border:0" allowfullscreen></iframe><br /><br />
            </div>
            <br />
            <h2 class="noMargin" onclick="displaySect( this.id )" id="show-ageRestrictions">
                Age Restrictions [+]
            </h2>
            <div class="hidden" id="ageRestrictions">
                The festival is open to everyone over the age of 18.<br />
            </div>
            <br />
            <h2 class="noMargin" onclick="displaySect( this.id )" id="show-lostProperty">
                Lost Property [+]
            </h2>
            <div class="hidden" id="lostProperty">
                Should you lose an item of personal belonging, please contact the lost and found at <span id='eml_1_pt_1'></span>@<span id='eml_1_pt_2'></span>.co.uk<br />If you are bringing a mobile phone with you, please make note of the device IMEI number. For any other portable device, please note the serial number. The organisers cannot be held responsible for any item of personal property which gets lost or damaged.<br /><br />
            </div>
            <br />
            <h2 class="noMargin" onclick=" displaySect( this.id ) " id="show-restrictedItems">
                What you can and can't bring [+]
            </h2>
                <div class="hidden" id="restrictedItems">
                    <br />
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
                    <br />
                </div>
                <br />
                <h2 class="noMargin" onclick="displaySect( this.id )" id="show-terms">
                    Terms and Conditions [+]
                </h3>
                <div class="hidden" id="terms">
                    <ul>
                        <li>
                            <h3 class="noMargin">Terms and Conditions</h3>
                            <ul>
                                <li>Artists may be subject to change or cancellation.</li>
                                <li>No trading or promotional activities allowed within the venue without express consent.</li>
                                <li>Photographic equipment is permitted provided that it is for personal non-commercial use only.</li>
                                <li>Only tickets purchased from <u><a href="https://soundville.co.uk">soundville.co.uk</a></u> will be valid.</li>
                                <li>By attending the event, express consent is given for use of your image, or true likeness, in any media or promotional activity by SoundVille or associates (e.g. bands or artists)</li>
                            </ul>
                        <li>
                            <h3 class="noMargin">Refunds and Cancellations</h3>
                            <ul>
                                <li>Refunds are only considered on major cancellation. (Where less than 60% of the acts are shown, or the event is cancelled by reasons not due to adverse weather)</li>
                                <li>Refunds should be obtained by contacting <span id="eml_2_pt_1"></span>@<span id="eml_2_pt_2"></span>.co.uk no later than 1 month after the event</li>
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
                                <li>Tickets are non-transferable and only valid when purchased from <u><a href="https://soundville.co.uk">soundville.co.uk</a></u>.</li>
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
                </div>
                <br />
                <h2 class="noMargin" onclick=" displaySect( this.id ) " id="show-fees">
                    What is the "Processing Fee"? [+]
                </h2>
                <div id="fees" class="hidden">
                    <p class="fullWidth">Our payment provider, <u><a href="https://stripe.com/gb">stripe payments</a></u>, charges a fee to process payment cards. Additionally, this fee helps to keep our website services up and running. Because this fee relates to an external service provider, unfortunately it cannot be refunded.</p>
                </div>
                <br />
                <h2 class="noMargin" onclick=" displaySect( this.id ) " id="show-privacyStatement">
                    Privacy Statement [+]
                </h2>
               <p class="fullWidth">
                <div id="privacyStatement" class="hidden">
                    <ul>
                        <li>
                            <h3 class="noMargin">
                                Data Controller
                            </h3>
                            The data controller for the personal information collected on this site is Harvey Fletcher. To contact the data controller, please use one of the methods in <u><a href="contact.php">the contact page</a></u> of this site.
                        </li>
                        <li>
                            <h3 class="noMargin">
                                The data we collect
                            </h3>
                            On this site, we collect the following information from you:<br />
                            <ul>
                                <li>Your Name</li>
                                <li>Your email address</li>
                                <li>Your telephone number</li>
                            </ul>
                        </li>
                        <li>
                            <h3 class="noMargin">
                                <li>How we collect this information</li>
                            </h3>
                            We collect the information through the following methods:
                            <ul>
                                <li>The information we keep on you is provided when you complete one of the forms on this site. We have no other data sources.</li>
                            </ul>
                        </li>
                        <li>
                            <h3 class="noMargin">
                                <li>How we use this information</li>
                            </h3>
                            The information collected on this site is used for:
                            <ul>
                                <li>Your Name - If you are a performer at the festival, we collect your name so that we can compare it with your ID.</li>
                                <li>Your email address - Anyone using this site will be required to provide us with an email address. We use this email address to keep you updated when new acts are added to the line up, and also when you make a purchase, your confirmation is sent to your email. If you are a performer, we also use your email address to contact you regarding your contract with us.</li>
                                <li>Your telephone number - If you are a volunteer or performer, we use your phone number to stay in contact with you regarding your duties at the festival. Users who are not a volunteer or performer do not need to provide their phone number.</li>
                            </ul>
                        </li>
                        <li>
                            <h3 class="noMargin">
                                <li>Your data rights</li>
                            </h3>
                            As part of the GDPR, you have the following rights with regard to the data we hold on you.
                            <ul>
                                <li>The right to request confirmation if we process your personal data, and if so, a right to request a copy of that data.</li>
                                <li>The right to request that SoundVille rectifies or updates your personal data that is inaccurate, incomplete, or outdated.</li>
                                <li>The right to request that SoundVille erase your personal data in certain circumstances provided by law;</li>
                                <li>The right to request that SoundVille restrict the use of your personal data in certain circumstances, such as while SoundVille considers another request that you have submitted (including a request that SoundVille makes an update to your Personal Data; and</li>
                                <li>The right to request that SoundVille export to another company, where technically feasible, your Personal Data that we hold in order to provide Services to you.</li>
                            </ul>
                            Where the processing of your Personal Data is based on your previously given consent, you have the right to withdraw your consent at any time. You may also have the right to object to the processing of your Personal Data on grounds relating to your particular situation.<br />
                            If you wish to exercise any of your data protection rights listed above, you can contact us using the <u><a href="contact.php">contact us page</a></u>. We take each request seriously. We will comply with your request to the extent required by the applicable law. We will not be able to respond to a request if we no longer hold your Personal Data.<br />
                            If you feel that you have not received a satisfactory response from us, you may consult with the data protection authority.<br /?
                            For your protection, we may need you to verify your identity before responding to your request, such as verifying the email address the request was sent from matches your email address that we have on file.<br />
                            When we no longer require your data, it will be purged from our systems.
                        </li>
                        <li>
                            <h3 class="noMargin">
                                <li>Do we share your data?</li>
                            </h3>
                            <ul>
                                <li>SoundVille does not share your data with third parties. We do not have any advertisements on this site that collect data.</li>
                                <li>Our payment processor, stripe payments, has a separate privacy policy. This can be viewed <u><a href="https://stripe.com/gb/privacy">here.</a></u></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </p>
            <br /><br />
            <h2 class="noMargin">
                If any of these terms are unclear please contact <span id='eml_3_pt_1'></span>@<span id='eml_3_pt_2'></span>.co.uk
            </h2>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script type="text/javascript">
          window.addEventListener("load", function(){
              //eml_pt_1
              //eml_pt_2
              //Assemble the email address, we do this here to prevent bots from scraping the web page.
              $("#eml_1_pt_1")
                  .text( "information" );

               $("#eml_1_pt_2")
                  .text( "soundville" );

               $("#eml_2_pt_1")
                   .text( "information" );

               $( "#eml_2_pt_2" )
                   .text("soundville");

               $("#eml_3_pt_1")
                   .text( "information" );

               $( "#eml_3_pt_2" )
                   .text("soundville");
          })

          function displaySect( id ){
              //The ID of that sections div
              var sect = id.split('-')[1];

              //Show that section
              $('#' + sect).show();

              //Set the onclick event
              $('#show-' + sect).attr("onclick", "hideSect( this.id )");

              //Change the symbol
              changeSymbol( id );
          }

          function hideSect( id ){
              //The ID of that sections div
              var sect = id.split('-')[1];

              //Hide that section
              $('#' + sect).hide();

              //Set the onclick event
              $('#show-' + sect).attr("onclick", "displaySect( this.id )");

              //Change the symbol
              changeSymbol( id );
          }

          function changeSymbol( id ){
            var currentText   = $('#' + id).text();
            var currentSymbol = currentText.split('[')[1].split(']')[0];
            var newSymbol = "+";

            if( currentSymbol == "+" ){
                newSymbol = "-";
            }

            //Set the text using the new symbol
            $('#' + id).text(  currentText.split('[')[0] + "[" + newSymbol + "]" + currentText.split(']')[1] );
          }
        </script>
    </body>
</html>
