<?php
define('SITE_URL',''); define('PUBLIC_URL','https://app.tapify.co.in'); define('SITE_NAME','Tapify');
require __DIR__.'/includes/functions.php';
$vcardId=99; $fullName='De Dirt Automotive';
$vcard=['email'=>'a@b.in','phone'=>'8668396209','name'=>$fullName,'occupation'=>'Car Service','profile_image'=>'images/templates/taxiservice2/tax-025.webp'];
$socialLinks=[];
$testimonials=[];
for($i=1;$i<=6;$i++){ $testimonials[]=['message'=>"Testimonial number $i - excellent car detailing service.",'author_name'=>"Customer $i",'image'=>'images/templates/taxiservice2/tax-025.webp']; }
$services=[];$products=[];$galleries=[];$businessHours=[];$serviceCategories=[];
include __DIR__.'/templates/vcard99.php';
