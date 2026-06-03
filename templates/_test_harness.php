<?php
// Mock-data harness to render vcard43.php and catch PHP errors (no DB needed)
error_reporting(E_ALL); ini_set('display_errors','1');
function imgUrl($p,$d=''){ if(empty($p))return $d; if(strpos($p,'http')===0)return $p; return '/'.ltrim($p,'/'); }
$vcardId=1;
$fullName='Christopher Print World';
$vcard=[
 'url_alias'=>'christopher','occupation'=>'Designer','description'=>'printing services',
 'email'=>'printworldonline1@gmail.com','alternate_email'=>'abid.printworld@gmail.com',
 'phone'=>'+91 9800508990','alternate_phone'=>'+91 9800508990',
 'cover_type'=>'video','cover_image'=>'https://youtu.be/muX4Pp4c8fo',
 'profile_image'=>'','favicon_image'=>'','location'=>'Surat,India','dob'=>'1990-06-12',
 'remove_branding'=>0,'custom_css'=>'','custom_js'=>'','show_appointments'=>1,'display_inquiry_form'=>1,
];
$socialLinks=[['platform'=>'whatsapp','url'=>'#'],['platform'=>'instagram','url'=>'#'],['platform'=>'linkedin-in','url'=>'#'],['platform'=>'facebook','url'=>'#']];
$services=[['name'=>'Skin & Facial','description'=>'Glow facial','image'=>''],['name'=>'Hair Coloring','description'=>'Balayage','image'=>'']];
$products=[['name'=>'Facial Package','price'=>'2500','description'=>'','image'=>''],['name'=>'Body Package','price'=>'3500','description'=>'','image'=>'']];
$galleries=[['name'=>'Work','images'=>[['image'=>''],['image'=>'']]]];
$testimonials=[['message'=>'Great!','author_name'=>'Rahul','rating'=>5,'image'=>'']];
$businessHours=[['day_name'=>'MONDAY','is_open'=>1,'open_time'=>'12:00 AM','close_time'=>'12:00 AM']];
$customLinks=$blogs=$iframes=$insta_feed=[]; $storeUrl=null;
ob_start(); include 'vcard43.php'; $html=ob_get_clean();
file_put_contents('_test_output.html',$html);
echo "RENDER OK — output bytes: ".strlen($html)."\n";
echo "ends with </html>: ".(rtrim($html)[-1]==='>' && strpos($html,'</html>')!==false ? 'yes':'no')."\n";
