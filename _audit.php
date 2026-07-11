<?php
/* TEMP visual-audit harness — renders a vcard template with realistic sample data.
   DELETE before deploy. Usage: /_audit.php?t=vcard72 */
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);
if (!function_exists('imgUrl')) { function imgUrl($p){ if(empty($p)) return ''; if(strpos($p,'http')===0) return $p; return '/'.ltrim($p,'/'); } }

// Use a real locally-served image (relative path -> imgUrl prepends '/') so it loads reliably.
$img = fn($seed,$w=600,$h=600) => 'images/templates/programmerx/pro-005.webp';
$vcardId = 1;
$vcard = [
  'id'=>1,'url_alias'=>'rahul-sharma','vcard_name'=>'Rahul Sharma','first_name'=>'Rahul','last_name'=>'Sharma',
  'occupation'=>'Interior Designer','company'=>'Sharma Interiors','job_title'=>'Founder & Lead Designer',
  'email'=>'rahul@sharmainteriors.com','alternate_email'=>'hello@sharmainteriors.com',
  'phone'=>'9876543210','alternate_phone'=>'9812345678','phone_country_code'=>'+91',
  'description'=>'<p>Award-winning interior designer with 12+ years transforming homes and offices into functional works of art. Specialising in modern, minimal and luxury spaces.</p>',
  'location'=>'Bandra West, Mumbai, Maharashtra','location_url'=>'https://maps.google.com/?q=Mumbai',
  'cover_type'=>'image','cover_image'=>$img('cover',1200,600),'profile_image'=>$img('rahulface',400,400),'favicon_image'=>'',
  'primary_color'=>'#6228d7','secondary_color'=>'#a855f7','custom_css'=>'','custom_js'=>'',
  'dob'=>'1988-05-14','made_by'=>'Tapify','made_by_url'=>'https://tapify.co.in',
];
$fullName = 'Rahul Sharma';
$coverImg = imgUrl($vcard['cover_image']);
$qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=https://tapify.co.in/rahul-sharma';

$products = [];
foreach ([['Modular Kitchen Design',45000,55000],['3D Space Planning',15000,0],['Living Room Makeover',80000,95000],['Office Interior Package',150000,0]] as $i=>$d)
  $products[] = ['id'=>$i+1,'name'=>$d[0],'description'=>'Premium design service tailored to your space.','currency'=>'INR','price'=>$d[1],'discount_price'=>$d[2]?:null,'image'=>$img('prod'.$i,600,600),'product_url'=>''];

$services = [];
foreach ([['Residential Design','fa-home'],['Commercial Spaces','fa-building'],['Consultation','fa-comments'],['Turnkey Projects','fa-key']] as $i=>$d)
  $services[] = ['id'=>$i+1,'name'=>$d[0],'image'=>$img('svc'.$i,600,600),'icon'=>'','service_url'=>''];

$galleries = [['id'=>1,'name'=>'Recent Work','images'=>array_map(fn($i)=>['id'=>$i,'image'=>$img('gal'.$i,600,600)], range(1,6))]];
$testimonials = [];
foreach ([['Priya Mehta','Absolutely transformed our apartment. Highly recommend!'],['Arjun Kapoor','Professional, punctual and creative. 5 stars.']] as $i=>$d)
  $testimonials[] = ['id'=>$i+1,'name'=>$d[0],'message'=>$d[1],'image'=>$img('testi'.$i,200,200),'rating'=>5,'designation'=>'Home Owner'];
$businessHours = [];
foreach (['MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY'] as $d)
  $businessHours[] = ['day_name'=>$d,'is_open'=>($d!=='SUNDAY')?1:0,'open_time'=>'10:00 AM','close_time'=>'07:00 PM'];
$socialLinks = [];
foreach (['facebook','instagram','linkedin','youtube','whatsapp'] as $i=>$p)
  $socialLinks[] = ['id'=>$i+1,'platform'=>$p,'url'=>'https://'.$p.'.com/rahulsharma'];
$customLinks = $blogs = $iframes = $insta_feed = $serviceCategories = [];

$t = preg_replace('/[^a-z0-9]/','', strtolower($_GET['t'] ?? 'vcard72'));
$path = __DIR__ . '/templates/' . $t . '.php';
if (!is_file($path)) { die('no such template: '.htmlspecialchars($t)); }
include $path;
