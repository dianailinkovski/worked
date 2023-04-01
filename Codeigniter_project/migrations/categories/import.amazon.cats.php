<?php
$catStr = "appliances>Appliances
mobile-apps>Apps &amp; Games
arts-crafts>Arts, Crafts &amp; Sewing
automotive>Automotive
baby-products>Baby
beauty>Beauty
stripbooks>Books
popular>CDs &amp; Vinyl
mobile>Cell Phones &amp; Accessories
fashion>Clothing, Shoes &amp; Jewelry
fashion-womens>&nbsp;&nbsp;&nbsp;Women
fashion-mens>&nbsp;&nbsp;&nbsp;Men
fashion-girls>&nbsp;&nbsp;&nbsp;Girls
fashion-boys>&nbsp;&nbsp;&nbsp;Boys
fashion-baby>&nbsp;&nbsp;&nbsp;Baby
collectibles>Collectibles &amp; Fine Art
computers>Computers
financial>Credit and Payment Cards
digital-music>Digital Music
electronics>Electronics
gift-cards>Gift Cards
grocery>Grocery &amp; Gourmet Food
hpc>Health &amp; Personal Care
garden>Home &amp; Kitchen
industrial>Industrial &amp; Scientific
digital-text>Kindle Store
fashion-luggage>Luggage &amp; Travel Gear
magazines>Magazine Subscriptions
movies-tv>Movies &amp; TV
mi>Musical Instruments
office-products>Office Products
lawngarden>Patio, Lawn &amp; Garden
pets>Pet Supplies
pantry>Prime Pantry
software>Software
sporting>Sports &amp; Outdoors
tools>Tools &amp; Home Improvement
toys-and-games>Toys &amp; Games
videogames>Video Games
wine>Wine";

$cats = explode("\n", $catStr);
//print_r($cats); exit;

foreach($cats as $c){
    list($junk,$c) = explode('>',$c);
    $c = str_replace('&nbsp;','',$c);
    $c = str_replace('&amp;','&',$c);
    
    echo "INSERT INTO `categories` set `name`='$c';\n";
}














?>
