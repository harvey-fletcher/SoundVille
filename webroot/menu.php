<a href="index.php" class="pageLink">Home</a>
<a href="info.php" class="pageLink">Info</a>
<?php if( isset( $_SESSION['email'] ) ){ ?>
    <a href="myAccount.php" class="pageLink">My Account</a>
<?php } else { ?>
    <a href="createAccount.php" class="pageLink">Sign Up</a>
<?php } ?>
<a href="lineup.php" class="pageLink">Lineup</a>
<a href="tickets.php" class="pageLink">Buy Tickets</a>
<a href="bands.php" class="pageLink">Perform</a>
<a href="volunteer.php" class="pageLink">Volunteer</a>
<a href="gallery.php" class="pageLink">Gallery</a>
<a href="contact.php" class="pageLink">Contact</a>
