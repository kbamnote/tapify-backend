<?php
/** Tapify vCard Template: vcard78 — auto-generated from tempsNewAdded/Event Management (hosted assets). */
$cardUrl='https://app.tapify.co.in/'.($vcard['url_alias'] ?? $vcardId);
$waPhone=preg_replace('/\D/','',$vcard['phone'] ?? '');
$locationUrl=!empty($vcard['location_url'])?$vcard['location_url']:'https://maps.google.com/?q='.urlencode($vcard['location'] ?? '');
$profileImg=!empty($vcard['profile_image'])?imgUrl($vcard['profile_image']):'https://ui-avatars.com/api/?name='.urlencode($fullName).'&size=200&background=2563eb&color=ffffff';
$coverImg=!empty($vcard['cover_image'])?imgUrl($vcard['cover_image']):'/images/templates/eventmanagementx/eve-038.png';
$qrUrl='https://api.qrserver.com/v1/create-qr-code/?size=200x200&data='.urlencode($cardUrl);
$platformIcons=['linkedin-in'=>'fa-linkedin-in','linkedin'=>'fa-linkedin-in','instagram'=>'fa-instagram','x-twitter'=>'fa-x-twitter','twitter'=>'fa-x-twitter','facebook'=>'fa-facebook-f','facebook-f'=>'fa-facebook-f','whatsapp'=>'fa-whatsapp','youtube'=>'fa-youtube','spotify'=>'fa-spotify','github'=>'fa-github','tiktok'=>'fa-tiktok','pinterest'=>'fa-pinterest-p','behance'=>'fa-behance','dribbble'=>'fa-dribbble','telegram'=>'fa-telegram','globe'=>'fa-globe'];
$socialSvgs=['facebook'=>'<svg viewBox="0 0 320 512" fill="currentColor" width="22" height="22" style="display:inline-block;vertical-align:middle"><path d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"/></svg>','facebook-f'=>'<svg viewBox="0 0 320 512" fill="currentColor" width="22" height="22" style="display:inline-block;vertical-align:middle"><path d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"/></svg>','instagram'=>'<svg viewBox="0 0 448 512" fill="currentColor" width="22" height="22" style="display:inline-block;vertical-align:middle"><path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/></svg>','whatsapp'=>'<svg viewBox="0 0 448 512" fill="currentColor" width="22" height="22" style="display:inline-block;vertical-align:middle"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.2-157zM223.9 438.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.5-186.6 184.5zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>','linkedin'=>'<svg viewBox="0 0 448 512" fill="currentColor" width="22" height="22" style="display:inline-block;vertical-align:middle"><path d="M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z"/></svg>','linkedin-in'=>'<svg viewBox="0 0 448 512" fill="currentColor" width="22" height="22" style="display:inline-block;vertical-align:middle"><path d="M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z"/></svg>','youtube'=>'<svg viewBox="0 0 576 512" fill="currentColor" width="22" height="22" style="display:inline-block;vertical-align:middle"><path d="M549.655 124.083c-6.281-23.65-24.787-42.276-48.284-48.597C458.781 64 288 64 288 64S117.22 64 74.629 75.486c-23.497 6.322-42.003 24.947-48.284 48.597-11.412 42.867-11.412 132.305-11.412 132.305s0 89.438 11.412 132.305c6.281 23.65 24.787 41.5 48.284 47.821C117.22 448 288 448 288 448s170.78 0 213.371-11.486c23.497-6.321 42.003-24.171 48.284-47.821 11.412-42.867 11.412-132.305 11.412-132.305s0-89.438-11.412-132.305zm-317.51 213.508V175.185l142.739 81.205-142.739 81.201z"/></svg>','x-twitter'=>'<svg viewBox="0 0 512 512" fill="currentColor" width="22" height="22" style="display:inline-block;vertical-align:middle"><path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/></svg>','twitter'=>'<svg viewBox="0 0 512 512" fill="currentColor" width="22" height="22" style="display:inline-block;vertical-align:middle"><path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/></svg>','globe'=>'<svg viewBox="0 0 512 512" fill="currentColor" width="22" height="22" style="display:inline-block;vertical-align:middle"><path d="M352 256c0 22.2-1.2 43.6-3.3 64H163.3c-2.2-20.4-3.3-41.8-3.3-64s1.2-43.6 3.3-64H348.7c2.2 20.4 3.3 41.8 3.3 64zm28.8-64H503.9c5.3 20.5 8.1 41.9 8.1 64s-2.8 43.5-8.1 64H380.8c2.1-20.6 3.2-42 3.2-64s-1.1-43.4-3.2-64zm112.6-32H376.7c-10-63.9-29.8-117.4-55.3-151.6 78.3 20.7 142 77.5 171.9 151.6zm-149.1 0H167.7c6.1-36.4 15.5-68.6 27-94.7 10.5-23.6 22.2-40.7 33.5-51.5C260.5 3.2 269.8 0 288 0s27.5 3.2 44.3 13.8c11.3 10.8 23 27.9 33.5 51.5 11.6 26 20.9 58.2 27 94.7zm-209 0H18.6C48.6 85.9 112.2 29.1 190.6 8.4 165.1 42.6 145.3 96.1 135.3 160zM8.1 192H131.2c-2.1 20.6-3.2 42-3.2 64s1.1 43.4 3.2 64H8.1C2.8 363.5 0 342.1 0 320s2.8-43.5 8.1-64zM194.7 446.6c-11.6-26-20.9-58.2-27-94.6H344.3c-6.1 36.4-15.5 68.6-27 94.6-10.5 23.6-22.2 40.7-33.5 51.5C267.5 508.8 258.2 512 240 512l-16 0c-18.2 0-27.5-3.2-44.3-13.8-11.3-10.8-23-27.9-33.5-51.5zM135.3 352c10 63.9 29.8 117.4 55.3 151.6C112.2 482.9 48.6 426.1 18.6 352H135.3zm358.1 0c-30 74.1-93.6 130.9-171.9 151.6 25.5-34.2 45.3-87.7 55.3-151.6H493.4z"/></svg>'];
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title><?= htmlspecialchars($fullName) ?></title><link rel="icon" href="<?= !empty($vcard['favicon_image'])?imgUrl($vcard['favicon_image']):'/images/tapify-logo-green.png' ?>"><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"><style>:root{--sf-img-31: url("/images/templates/eventmanagementx/eve-000.webp");--sf-img-32: url("/images/templates/eventmanagementx/eve-001.webp");--sf-img-33: url("/images/templates/eventmanagementx/eve-002.webp");--sf-img-34: url("/images/templates/eventmanagementx/eve-003.webp");--sf-img-42: url("/images/templates/eventmanagementx/eve-004.webp");--sf-img-43: url("/images/templates/eventmanagementx/eve-005.webp");--sf-img-44: url("/images/templates/eventmanagementx/eve-006.webp");--sf-img-45: url("/images/templates/eventmanagementx/eve-007.webp");--sf-img-47: url("/images/templates/eventmanagementx/eve-008.webp");--sf-img-48: url("/images/templates/eventmanagementx/eve-009.webp");--sf-img-49: url("/images/templates/eventmanagementx/eve-010.webp");--sf-img-50: url("/images/templates/eventmanagementx/eve-011.webp");--sf-img-51: url("/images/templates/eventmanagementx/eve-012.webp");--sf-img-52: url("/images/templates/eventmanagementx/eve-013.webp");--sf-img-55: url("/images/templates/eventmanagementx/eve-014.webp");--sf-img-56: url("/images/templates/eventmanagementx/eve-015.webp");--sf-img-57: url("/images/templates/eventmanagementx/eve-016.webp");--sf-img-58: url("/images/templates/eventmanagementx/eve-017.webp")}
:host,:root{--fa-font-solid:normal 900 1em/1"Font Awesome 6 Solid";--fa-font-regular:normal 400 1em/1"Font Awesome 6 Regular";--fa-font-light:normal 300 1em/1"Font Awesome 6 Light";--fa-font-thin:normal 100 1em/1"Font Awesome 6 Thin";--fa-font-duotone:normal 900 1em/1"Font Awesome 6 Duotone";--fa-font-brands:normal 400 1em/1"Font Awesome 6 Brands"}svg:not(:host).svg-inline--fa,svg:not(:root).svg-inline--fa{overflow:visible;box-sizing:content-box}.svg-inline--fa{display:var(--fa-display,inline-block);height:1em;vertical-align:-.125em}@-webkit-keyframes fa-beat{0%,90%{-webkit-transform:scale(1);transform:scale(1)}45%{-webkit-transform:scale(var(--fa-beat-scale,1.25));transform:scale(var(--fa-beat-scale,1.25))}}@keyframes fa-beat{0%,90%{-webkit-transform:scale(1);transform:scale(1)}45%{-webkit-transform:scale(var(--fa-beat-scale,1.25));transform:scale(var(--fa-beat-scale,1.25))}}@-webkit-keyframes fa-bounce{0%{-webkit-transform:scale(1,1) translateY(0);transform:scale(1,1) translateY(0)}10%{-webkit-transform:scale(var(--fa-bounce-start-scale-x,1.1),var(--fa-bounce-start-scale-y,.9)) translateY(0);transform:scale(var(--fa-bounce-start-scale-x,1.1),var(--fa-bounce-start-scale-y,.9)) translateY(0)}30%{-webkit-transform:scale(var(--fa-bounce-jump-scale-x,.9),var(--fa-bounce-jump-scale-y,1.1)) translateY(var(--fa-bounce-height,-.5em));transform:scale(var(--fa-bounce-jump-scale-x,.9),var(--fa-bounce-jump-scale-y,1.1)) translateY(var(--fa-bounce-height,-.5em))}50%{-webkit-transform:scale(var(--fa-bounce-land-scale-x,1.05),var(--fa-bounce-land-scale-y,.95)) translateY(0);transform:scale(var(--fa-bounce-land-scale-x,1.05),var(--fa-bounce-land-scale-y,.95)) translateY(0)}57%{-webkit-transform:scale(1,1) translateY(var(--fa-bounce-rebound,-.125em));transform:scale(1,1) translateY(var(--fa-bounce-rebound,-.125em))}64%{-webkit-transform:scale(1,1) translateY(0);transform:scale(1,1) translateY(0)}100%{-webkit-transform:scale(1,1) translateY(0);transform:scale(1,1) translateY(0)}}@keyframes fa-bounce{0%{-webkit-transform:scale(1,1) translateY(0);transform:scale(1,1) translateY(0)}10%{-webkit-transform:scale(var(--fa-bounce-start-scale-x,1.1),var(--fa-bounce-start-scale-y,.9)) translateY(0);transform:scale(var(--fa-bounce-start-scale-x,1.1),var(--fa-bounce-start-scale-y,.9)) translateY(0)}30%{-webkit-transform:scale(var(--fa-bounce-jump-scale-x,.9),var(--fa-bounce-jump-scale-y,1.1)) translateY(var(--fa-bounce-height,-.5em));transform:scale(var(--fa-bounce-jump-scale-x,.9),var(--fa-bounce-jump-scale-y,1.1)) translateY(var(--fa-bounce-height,-.5em))}50%{-webkit-transform:scale(var(--fa-bounce-land-scale-x,1.05),var(--fa-bounce-land-scale-y,.95)) translateY(0);transform:scale(var(--fa-bounce-land-scale-x,1.05),var(--fa-bounce-land-scale-y,.95)) translateY(0)}57%{-webkit-transform:scale(1,1) translateY(var(--fa-bounce-rebound,-.125em));transform:scale(1,1) translateY(var(--fa-bounce-rebound,-.125em))}64%{-webkit-transform:scale(1,1) translateY(0);transform:scale(1,1) translateY(0)}100%{-webkit-transform:scale(1,1) translateY(0);transform:scale(1,1) translateY(0)}}@-webkit-keyframes fa-fade{50%{opacity:var(--fa-fade-opacity,.4)}}@keyframes fa-fade{50%{opacity:var(--fa-fade-opacity,.4)}}@-webkit-keyframes fa-beat-fade{0%,100%{opacity:var(--fa-beat-fade-opacity,.4);-webkit-transform:scale(1);transform:scale(1)}50%{opacity:1;-webkit-transform:scale(var(--fa-beat-fade-scale,1.125));transform:scale(var(--fa-beat-fade-scale,1.125))}}@keyframes fa-beat-fade{0%,100%{opacity:var(--fa-beat-fade-opacity,.4);-webkit-transform:scale(1);transform:scale(1)}50%{opacity:1;-webkit-transform:scale(var(--fa-beat-fade-scale,1.125));transform:scale(var(--fa-beat-fade-scale,1.125))}}@-webkit-keyframes fa-flip{50%{-webkit-transform:rotate3d(var(--fa-flip-x,0),var(--fa-flip-y,1),var(--fa-flip-z,0),var(--fa-flip-angle,-180deg));transform:rotate3d(var(--fa-flip-x,0),var(--fa-flip-y,1),var(--fa-flip-z,0),var(--fa-flip-angle,-180deg))}}@keyframes fa-flip{50%{-webkit-transform:rotate3d(var(--fa-flip-x,0),var(--fa-flip-y,1),var(--fa-flip-z,0),var(--fa-flip-angle,-180deg));transform:rotate3d(var(--fa-flip-x,0),var(--fa-flip-y,1),var(--fa-flip-z,0),var(--fa-flip-angle,-180deg))}}@-webkit-keyframes fa-shake{0%{-webkit-transform:rotate(-15deg);transform:rotate(-15deg)}4%{-webkit-transform:rotate(15deg);transform:rotate(15deg)}24%,8%{-webkit-transform:rotate(-18deg);transform:rotate(-18deg)}12%,28%{-webkit-transform:rotate(18deg);transform:rotate(18deg)}16%{-webkit-transform:rotate(-22deg);transform:rotate(-22deg)}20%{-webkit-transform:rotate(22deg);transform:rotate(22deg)}32%{-webkit-transform:rotate(-12deg);transform:rotate(-12deg)}36%{-webkit-transform:rotate(12deg);transform:rotate(12deg)}100%,40%{-webkit-transform:rotate(0);transform:rotate(0)}}@keyframes fa-shake{0%{-webkit-transform:rotate(-15deg);transform:rotate(-15deg)}4%{-webkit-transform:rotate(15deg);transform:rotate(15deg)}24%,8%{-webkit-transform:rotate(-18deg);transform:rotate(-18deg)}12%,28%{-webkit-transform:rotate(18deg);transform:rotate(18deg)}16%{-webkit-transform:rotate(-22deg);transform:rotate(-22deg)}20%{-webkit-transform:rotate(22deg);transform:rotate(22deg)}32%{-webkit-transform:rotate(-12deg);transform:rotate(-12deg)}36%{-webkit-transform:rotate(12deg);transform:rotate(12deg)}100%,40%{-webkit-transform:rotate(0);transform:rotate(0)}}@-webkit-keyframes fa-spin{0%{-webkit-transform:rotate(0);transform:rotate(0)}100%{-webkit-transform:rotate(360deg);transform:rotate(360deg)}}@keyframes fa-spin{0%{-webkit-transform:rotate(0);transform:rotate(0)}100%{-webkit-transform:rotate(360deg);transform:rotate(360deg)}}.fa-sr-only-focusable:not(:focus),.sr-only-focusable:not(:focus){position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border-width:0}
@-webkit-keyframes animateErrorIcon{0%{-webkit-transform:rotateX(100deg);transform:rotateX(100deg);opacity:0}to{-webkit-transform:rotateX(0deg);transform:rotateX(0deg);opacity:1}}@keyframes animateErrorIcon{0%{-webkit-transform:rotateX(100deg);transform:rotateX(100deg);opacity:0}to{-webkit-transform:rotateX(0deg);transform:rotateX(0deg);opacity:1}}@-webkit-keyframes animateXMark{0%{-webkit-transform:scale(.4);transform:scale(.4);margin-top:26px;opacity:0}50%{-webkit-transform:scale(.4);transform:scale(.4);margin-top:26px;opacity:0}80%{-webkit-transform:scale(1.15);transform:scale(1.15);margin-top:-6px}to{-webkit-transform:scale(1);transform:scale(1);margin-top:0;opacity:1}}@keyframes animateXMark{0%{-webkit-transform:scale(.4);transform:scale(.4);margin-top:26px;opacity:0}50%{-webkit-transform:scale(.4);transform:scale(.4);margin-top:26px;opacity:0}80%{-webkit-transform:scale(1.15);transform:scale(1.15);margin-top:-6px}to{-webkit-transform:scale(1);transform:scale(1);margin-top:0;opacity:1}}@-webkit-keyframes pulseWarning{0%{border-color:#f8d486}to{border-color:#f8bb86}}@keyframes pulseWarning{0%{border-color:#f8d486}to{border-color:#f8bb86}}.swal-icon--success:after,.swal-icon--success:before{content:"";border-radius:50%;position:absolute;width:60px;height:120px;background:#fff;-webkit-transform:rotate(45deg);transform:rotate(45deg)}.swal-icon--success:before{border-radius:120px 0 0 120px;top:-7px;left:-33px;-webkit-transform:rotate(-45deg);transform:rotate(-45deg);-webkit-transform-origin:60px 60px;transform-origin:60px 60px}.swal-icon--success:after{border-radius:0 120px 120px 0;top:-11px;left:30px;-webkit-transform:rotate(-45deg);transform:rotate(-45deg);-webkit-transform-origin:0 60px;transform-origin:0 60px;-webkit-animation:rotatePlaceholder 4.25s ease-in;animation:rotatePlaceholder 4.25s ease-in}@-webkit-keyframes rotatePlaceholder{0%{-webkit-transform:rotate(-45deg);transform:rotate(-45deg)}5%{-webkit-transform:rotate(-45deg);transform:rotate(-45deg)}12%{-webkit-transform:rotate(-405deg);transform:rotate(-405deg)}to{-webkit-transform:rotate(-405deg);transform:rotate(-405deg)}}@keyframes rotatePlaceholder{0%{-webkit-transform:rotate(-45deg);transform:rotate(-45deg)}5%{-webkit-transform:rotate(-45deg);transform:rotate(-45deg)}12%{-webkit-transform:rotate(-405deg);transform:rotate(-405deg)}to{-webkit-transform:rotate(-405deg);transform:rotate(-405deg)}}@-webkit-keyframes animateSuccessTip{0%{width:0;left:1px;top:19px}54%{width:0;left:1px;top:19px}70%{width:50px;left:-8px;top:37px}84%{width:17px;left:21px;top:48px}to{width:25px;left:14px;top:45px}}@keyframes animateSuccessTip{0%{width:0;left:1px;top:19px}54%{width:0;left:1px;top:19px}70%{width:50px;left:-8px;top:37px}84%{width:17px;left:21px;top:48px}to{width:25px;left:14px;top:45px}}@-webkit-keyframes animateSuccessLong{0%{width:0;right:46px;top:54px}65%{width:0;right:46px;top:54px}84%{width:55px;right:0;top:35px}to{width:47px;right:8px;top:38px}}@keyframes animateSuccessLong{0%{width:0;right:46px;top:54px}65%{width:0;right:46px;top:54px}84%{width:55px;right:0;top:35px}to{width:47px;right:8px;top:38px}}.swal-icon--info:before{width:5px;height:29px;bottom:17px;border-radius:2px;margin-left:-2px}.swal-icon--info:after,.swal-icon--info:before{content:"";position:absolute;left:50%;background-color:#c9dae1}.swal-icon--info:after{width:7px;height:7px;border-radius:50%;margin-left:-3px;top:19px}.swal-button:not([disabled]):hover{background-color:#78cbf2}.swal-button:active{background-color:#70bce0}.swal-button:focus{outline:none;box-shadow:0 0 0 1px #fff,0 0 0 3px rgba(43,114,165,.29)}.swal-button::-moz-focus-inner{border:0}.swal-button--cancel:not([disabled]):hover{background-color:#e8e8e8}.swal-button--cancel:active{background-color:#d7d7d7}.swal-button--cancel:focus{box-shadow:0 0 0 1px #fff,0 0 0 3px rgba(116,136,150,.29)}.swal-button--danger:not([disabled]):hover{background-color:#df4740}.swal-button--danger:active{background-color:#cf423b}.swal-button--danger:focus{box-shadow:0 0 0 1px #fff,0 0 0 3px rgba(165,43,43,.29)}.swal-content__input:focus,.swal-content__textarea:focus{outline:none;border-color:#6db8ff}@-webkit-keyframes swal-loading-anim{0%{opacity:.4}20%{opacity:.4}50%{opacity:1}to{opacity:.4}}@keyframes swal-loading-anim{0%{opacity:.4}20%{opacity:.4}50%{opacity:1}to{opacity:.4}}.swal-overlay:before{content:" ";display:inline-block;vertical-align:middle;height:100%}@-webkit-keyframes showSweetAlert{0%{-webkit-transform:scale(1);transform:scale(1)}1%{-webkit-transform:scale(.5);transform:scale(.5)}45%{-webkit-transform:scale(1.05);transform:scale(1.05)}80%{-webkit-transform:scale(.95);transform:scale(.95)}to{-webkit-transform:scale(1);transform:scale(1)}}@keyframes showSweetAlert{0%{-webkit-transform:scale(1);transform:scale(1)}1%{-webkit-transform:scale(.5);transform:scale(.5)}45%{-webkit-transform:scale(1.05);transform:scale(1.05)}80%{-webkit-transform:scale(.95);transform:scale(.95)}to{-webkit-transform:scale(1);transform:scale(1)}}
/*!
 * Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com
 * License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License)
 * Copyright 2023 Fonticons, Inc.
 */@-webkit-keyframes fa-beat{0%,90%{-webkit-transform:scale(1);transform:scale(1)}45%{-webkit-transform:scale(var(--fa-beat-scale,1.25));transform:scale(var(--fa-beat-scale,1.25))}}@keyframes fa-beat{0%,90%{-webkit-transform:scale(1);transform:scale(1)}45%{-webkit-transform:scale(var(--fa-beat-scale,1.25));transform:scale(var(--fa-beat-scale,1.25))}}@-webkit-keyframes fa-bounce{0%{-webkit-transform:scale(1) translateY(0);transform:scale(1) translateY(0)}10%{-webkit-transform:scale(var(--fa-bounce-start-scale-x,1.1),var(--fa-bounce-start-scale-y,.9)) translateY(0);transform:scale(var(--fa-bounce-start-scale-x,1.1),var(--fa-bounce-start-scale-y,.9)) translateY(0)}30%{-webkit-transform:scale(var(--fa-bounce-jump-scale-x,.9),var(--fa-bounce-jump-scale-y,1.1)) translateY(var(--fa-bounce-height,-.5em));transform:scale(var(--fa-bounce-jump-scale-x,.9),var(--fa-bounce-jump-scale-y,1.1)) translateY(var(--fa-bounce-height,-.5em))}50%{-webkit-transform:scale(var(--fa-bounce-land-scale-x,1.05),var(--fa-bounce-land-scale-y,.95)) translateY(0);transform:scale(var(--fa-bounce-land-scale-x,1.05),var(--fa-bounce-land-scale-y,.95)) translateY(0)}57%{-webkit-transform:scale(1) translateY(var(--fa-bounce-rebound,-.125em));transform:scale(1) translateY(var(--fa-bounce-rebound,-.125em))}64%{-webkit-transform:scale(1) translateY(0);transform:scale(1) translateY(0)}to{-webkit-transform:scale(1) translateY(0);transform:scale(1) translateY(0)}}@keyframes fa-bounce{0%{-webkit-transform:scale(1) translateY(0);transform:scale(1) translateY(0)}10%{-webkit-transform:scale(var(--fa-bounce-start-scale-x,1.1),var(--fa-bounce-start-scale-y,.9)) translateY(0);transform:scale(var(--fa-bounce-start-scale-x,1.1),var(--fa-bounce-start-scale-y,.9)) translateY(0)}30%{-webkit-transform:scale(var(--fa-bounce-jump-scale-x,.9),var(--fa-bounce-jump-scale-y,1.1)) translateY(var(--fa-bounce-height,-.5em));transform:scale(var(--fa-bounce-jump-scale-x,.9),var(--fa-bounce-jump-scale-y,1.1)) translateY(var(--fa-bounce-height,-.5em))}50%{-webkit-transform:scale(var(--fa-bounce-land-scale-x,1.05),var(--fa-bounce-land-scale-y,.95)) translateY(0);transform:scale(var(--fa-bounce-land-scale-x,1.05),var(--fa-bounce-land-scale-y,.95)) translateY(0)}57%{-webkit-transform:scale(1) translateY(var(--fa-bounce-rebound,-.125em));transform:scale(1) translateY(var(--fa-bounce-rebound,-.125em))}64%{-webkit-transform:scale(1) translateY(0);transform:scale(1) translateY(0)}to{-webkit-transform:scale(1) translateY(0);transform:scale(1) translateY(0)}}@-webkit-keyframes fa-fade{50%{opacity:var(--fa-fade-opacity,.4)}}@keyframes fa-fade{50%{opacity:var(--fa-fade-opacity,.4)}}@-webkit-keyframes fa-beat-fade{0%,to{opacity:var(--fa-beat-fade-opacity,.4);-webkit-transform:scale(1);transform:scale(1)}50%{opacity:1;-webkit-transform:scale(var(--fa-beat-fade-scale,1.125));transform:scale(var(--fa-beat-fade-scale,1.125))}}@keyframes fa-beat-fade{0%,to{opacity:var(--fa-beat-fade-opacity,.4);-webkit-transform:scale(1);transform:scale(1)}50%{opacity:1;-webkit-transform:scale(var(--fa-beat-fade-scale,1.125));transform:scale(var(--fa-beat-fade-scale,1.125))}}@-webkit-keyframes fa-flip{50%{-webkit-transform:rotate3d(var(--fa-flip-x,0),var(--fa-flip-y,1),var(--fa-flip-z,0),var(--fa-flip-angle,-180deg));transform:rotate3d(var(--fa-flip-x,0),var(--fa-flip-y,1),var(--fa-flip-z,0),var(--fa-flip-angle,-180deg))}}@keyframes fa-flip{50%{-webkit-transform:rotate3d(var(--fa-flip-x,0),var(--fa-flip-y,1),var(--fa-flip-z,0),var(--fa-flip-angle,-180deg));transform:rotate3d(var(--fa-flip-x,0),var(--fa-flip-y,1),var(--fa-flip-z,0),var(--fa-flip-angle,-180deg))}}@-webkit-keyframes fa-shake{0%{-webkit-transform:rotate(-15deg);transform:rotate(-15deg)}4%{-webkit-transform:rotate(15deg);transform:rotate(15deg)}8%,24%{-webkit-transform:rotate(-18deg);transform:rotate(-18deg)}12%,28%{-webkit-transform:rotate(18deg);transform:rotate(18deg)}16%{-webkit-transform:rotate(-22deg);transform:rotate(-22deg)}20%{-webkit-transform:rotate(22deg);transform:rotate(22deg)}32%{-webkit-transform:rotate(-12deg);transform:rotate(-12deg)}36%{-webkit-transform:rotate(12deg);transform:rotate(12deg)}40%,to{-webkit-transform:rotate(0deg);transform:rotate(0deg)}}@keyframes fa-shake{0%{-webkit-transform:rotate(-15deg);transform:rotate(-15deg)}4%{-webkit-transform:rotate(15deg);transform:rotate(15deg)}8%,24%{-webkit-transform:rotate(-18deg);transform:rotate(-18deg)}12%,28%{-webkit-transform:rotate(18deg);transform:rotate(18deg)}16%{-webkit-transform:rotate(-22deg);transform:rotate(-22deg)}20%{-webkit-transform:rotate(22deg);transform:rotate(22deg)}32%{-webkit-transform:rotate(-12deg);transform:rotate(-12deg)}36%{-webkit-transform:rotate(12deg);transform:rotate(12deg)}40%,to{-webkit-transform:rotate(0deg);transform:rotate(0deg)}}@-webkit-keyframes fa-spin{0%{-webkit-transform:rotate(0deg);transform:rotate(0deg)}to{-webkit-transform:rotate(1turn);transform:rotate(1turn)}}@keyframes fa-spin{0%{-webkit-transform:rotate(0deg);transform:rotate(0deg)}to{-webkit-transform:rotate(1turn);transform:rotate(1turn)}}.fa-chevron-circle-right:before,.fa-trash-alt:before,.fa-user-times:before,.fa-comment-alt:before,.fa-compress-alt:before,.fa-file-alt:before,.fa-file-lines:before,.fa-calendar-alt:before,.fa-volleyball-ball:before,.fa-sort-desc:before,.fa-circle-minus:before,.fa-right-from-bracket:before,.fa-heart-music-camera-bolt:before,.fa-microphone-alt-slash:before,.fa-magnifying-glass-location:before,.fa-forward-step:before,.fa-face-smile-beam:before,.fa-football-ball:before,.fa-angle-double-down:before,.fa-beer-mug-empty:before,.fa-arrow-up-long:before,.fa-burn:before,.fa-male:before,.fa-face-grin-stars:before,.fa-pastafarianism:before,.fa-spoon:before,.fa-envelopes-bulk:before,.fa-circle-h:before,.fa-address-book:before,.fa-pencil-alt:before,.fa-file-clipboard:before,.fa-truck-loading:before,.fa-scroll-torah:before,.fa-broom-ball:before,.fa-quidditch-broom-ball:before,.fa-archive:before,.fa-arrow-down-9-1:before,.fa-sort-numeric-desc:before,.fa-face-grin-tongue-squint:before,.fa-earth-africa:before,.fa-tablet-alt:before,.fa-face-flushed:before,.fa-gavel:before,.fa-bell-concierge:before,.fa-pen-ruler:before,.fa-people-arrows-left-right:before,.fa-caret-square-right:before,.fa-cut:before,.fa-digital-tachograph:before,.fa-mail-reply:before,.fa-minus-square:before,.fa-caret-square-down:before,.fa-bars:before,.fa-hourglass-3:before,.fa-heart-broken:before,.fa-external-link-square-alt:before,.fa-face-kiss-beam:before,.fa-circle-exclamation:before,.fa-arrow-right-from-bracket:before,.fa-chevron-circle-down:before,.fa-unlock-alt:before,.fa-headphones-alt:before,.fa-circle-dollar-to-slot:before,.fa-volume-down:before,.fa-wheat-alt:before,.fa-check-square:before,.fa-header:before,.fa-list-squares:before,.fa-phone-square-alt:before,.fa-circle-dot:before,.fa-dizzy:before,.fa-futbol-ball:before,.fa-futbol:before,.fa-paint-brush:before,.fa-hot-tub-person:before,.fa-map-location:before,.fa-edit:before,.fa-share-alt:before,.fa-hourglass-2:before,.fa-bag-shopping:before,.fa-arrow-down-z-a:before,.fa-sort-alpha-desc:before,.fa-hand-paper:before,.fa-face-kiss:before,.fa-face-grin-tongue:before,.fa-face-grin-wink:before,.fa-deaf:before,.fa-deafness:before,.fa-ear-deaf:before,.fa-rss-square:before,.fa-hryvnia-sign:before,.fa-face-grin-wide:before,.fa-rod-asclepius:before,.fa-rod-snake:before,.fa-staff-aesculapius:before,.fa-ambulance:before,.fa-temperature-2:before,.fa-temperature-half:before,.fa-thermometer-2:before,.fa-poo-bolt:before,.fa-face-frown-open:before,.fa-folder-blank:before,.fa-file-medical-alt:before,.fa-dashboard:before,.fa-gauge-med:before,.fa-gauge:before,.fa-magic-wand-sparkles:before,.fa-pen-alt:before,.fa-shuttle-van:before,.fa-caret-square-left:before,.fa-area-chart:before,.fa-ban:before,.fa-air-freshener:before,.fa-arrow-pointer:before,.fa-expand-arrows-alt:before,.fa-shapes:before,.fa-random:before,.fa-person-running:before,.fa-computer-mouse:before,.fa-arrow-right-to-bracket:before,.fa-shop-slash:before,.fa-hourglass-1:before,.fa-right-to-bracket:before,.fa-heart-pulse:before,.fa-people-carry-box:before,.fa-weight-scale:before,.fa-user-friends:before,.fa-arrow-up-a-z:before,.fa-face-laugh-squint:before,.fa-arrow-circle-up:before,.fa-person-walking:before,.fa-bed-pulse:before,.fa-shuttle-space:before,.fa-face-laugh:before,.fa-microphone-alt:before,.fa-mars-stroke-up:before,.fa-champagne-glasses:before,.fa-file-arrow-up:before,.fa-wifi-3:before,.fa-wifi-strong:before,.fa-bath:before,.fa-user-edit:before,.fa-border-style:before,.fa-map-location-dot:before,.fa-poll:before,.fa-battery-car:before,.fa-mars-stroke-h:before,.fa-hand-back-fist:before,.fa-caret-square-up:before,.fa-bar-chart:before,.fa-hands-bubbles:before,.fa-eye-low-vision:before,.fa-plus-square:before,.fa-glass-martini-alt:before,.fa-rotate-back:before,.fa-rotate-backward:before,.fa-rotate-left:before,.fa-columns:before,.fa-dolly-box:before,.fa-compress-arrows-alt:before,.fa-angle-double-right:before,.fa-circle-play:before,.fa-eur:before,.fa-euro-sign:before,.fa-check-circle:before,.fa-circle-stop:before,.fa-compass-drafting:before,.fa-face-laugh-beam:before,.fa-chevron-circle-up:before,.fa-gbp:before,.fa-pound-sign:before,.fa-arrow-down-long:before,.fa-mail-reply-all:before,.fa-person-skating:before,.fa-filter-circle-dollar:before,.fa-arrow-circle-down:before,.fa-arrow-right-to-file:before,.fa-external-link-square:before,.fa-temperature-0:before,.fa-temperature-empty:before,.fa-thermometer-0:before,.fa-address-card:before,.fa-contact-card:before,.fa-balance-scale-right:before,.fa-diamond-turn-right:before,.fa-house-laptop:before,.fa-face-tired:before,.fa-cloud-arrow-up:before,.fa-cloud-upload-alt:before,.fa-seedling:before,.fa-arrows-alt-h:before,.fa-arrow-circle-left:before,.fa-arrow-down-wide-short:before,.fa-sort-amount-asc:before,.fa-cloud-bolt:before,.fa-remove-format:before,.fa-face-smile-wink:before,.fa-arrows-h:before,.fa-cloud-arrow-down:before,.fa-cloud-download-alt:before,.fa-blackboard:before,.fa-user-alt-slash:before,.fa-handshake-alt-slash:before,.fa-arrows-rotate:before,.fa-refresh:before,.fa-shield-alt:before,.fa-atlas:before,.fa-house-chimney-crack:before,.fa-file-archive:before,.fa-glass-martini:before,.fa-person-skiing:before,.fa-temperature-arrow-down:before,.fa-feather-alt:before,.fa-ad:before,.fa-arrow-circle-right:before,.fa-sort:before,.fa-list-1-2:before,.fa-list-numeric:before,.fa-money-check-alt:before,.fa-face-kiss-wink-heart:before,.fa-arrows-alt:before,.fa-star-half-alt:before,.fa-glass-whiskey:before,.fa-arrow-up-right-from-square:before,.fa-krw:before,.fa-won-sign:before,.fa-cab:before,.fa-chart-pie:before,.fa-face-grin-beam:before,.fa-location-pin:before,.fa-hard-hat:before,.fa-hat-hard:before,.fa-arrow-alt-circle-right:before,.fa-face-rolling-eyes:before,.fa-chart-line:before,.fa-map-signs:before,.fa-screwdriver-wrench:before,.fa-home-user:before,.fa-cocktail:before,.fa-face-surprise:before,.fa-circle-pause:before,.fa-apple-alt:before,.fa-temperature-1:before,.fa-temperature-quarter:before,.fa-thermometer-1:before,.fa-poll-h:before,.fa-backward-fast:before,.fa-basketball-ball:before,.fa-arrow-alt-circle-up:before,.fa-mobile-alt:before,.fa-volume-high:before,.fa-burger:before,.fa-rupee-sign:before,.fa-circle-question:before,.fa-phone-alt:before,.fa-fast-forward:before,.fa-face-meh-blank:before,.fa-parking:before,.fa-bars-progress:before,.fa-cart-flatbed:before,.fa-ban-smoking:before,.fa-basket-shopping:before,.fa-bus-alt:before,.fa-face-sad-cry:before,.fa-signal-5:before,.fa-signal-perfect:before,.fa-home-lg:before,.fa-face-frown:before,.fa-shop:before,.fa-floppy-disk:before,.fa-balance-scale-left:before,.fa-sort-asc:before,.fa-comment-dots:before,.fa-face-grin-squint:before,.fa-hand-holding-dollar:before,.fa-hands-praying:before,.fa-arrow-right-rotate:before,.fa-arrow-rotate-forward:before,.fa-arrow-rotate-right:before,.fa-location-crosshairs:before,.fa-face-grin-tears:before,.fa-calendar-times:before,.fa-user-cog:before,.fa-arrow-up-1-9:before,.fa-digging:before,.fa-gauge-simple-med:before,.fa-gauge-simple:before,.fa-quote-right-alt:before,.fa-shirt:before,.fa-t-shirt:before,.fa-tenge-sign:before,.fa-external-link-alt:before,.fa-table-cells:before,.fa-bible:before,.fa-medkit:before,.fa-female:before,.fa-briefcase-clock:before,.fa-table-cells-large:before,.fa-book-tanakh:before,.fa-phone-volume:before,.fa-birthday-cake:before,.fa-cake-candles:before,.fa-angle-double-up:before,.fa-arrow-up-9-1:before,.fa-hourglass-empty:before,.fa-user-doctor:before,.fa-circle-info:before,.fa-camera-alt:before,.fa-arrow-down-1-9:before,.fa-sort-numeric-asc:before,.fa-hand-holding-droplet:before,.fa-prescription-bottle-alt:before,.fa-arrow-down-a-z:before,.fa-sort-alpha-asc:before,.fa-arrow-left-rotate:before,.fa-arrow-rotate-back:before,.fa-arrow-rotate-backward:before,.fa-arrow-rotate-left:before,.fa-hard-drive:before,.fa-face-grin-squint-tears:before,.fa-list-alt:before,.fa-person-skiing-nordic:before,.fa-arrow-alt-circle-left:before,.fa-subway:before,.fa-indian-rupee-sign:before,.fa-indian-rupee:before,.fa-crop-alt:before,.fa-money-bill-1:before,.fa-left-long:before,.fa-minus:before,.fa-arrow-left-long:before,.fa-american-sign-language-interpreting:before,.fa-asl-interpreting:before,.fa-hands-american-sign-language-interpreting:before,.fa-cog:before,.fa-droplet-slash:before,.fa-cart-shopping:before,.fa-arrow-turn-up:before,.fa-square-root-alt:before,.fa-clock-four:before,.fa-backward-step:before,.fa-clinic-medical:before,.fa-temperature-3:before,.fa-temperature-three-quarters:before,.fa-thermometer-3:before,.fa-mobile-android-alt:before,.fa-battery-3:before,.fa-sliders-h:before,.fa-ellipsis-v:before,.fa-long-arrow-alt-right:before,.fa-teletype:before,.fa-hiking:before,.fa-cable-car:before,.fa-face-grin:before,.fa-backspace:before,.fa-eye-dropper-empty:before,.fa-eye-dropper:before,.fa-mobile-android:before,.fa-mobile-phone:before,.fa-face-meh:before,.fa-book-dead:before,.fa-drivers-license:before,.fa-dedent:before,.fa-home-alt:before,.fa-home-lg-alt:before,.fa-home:before,.fa-arrow-right-arrow-left:before,.fa-redo-alt:before,.fa-rotate-forward:before,.fa-cutlery:before,.fa-arrow-up-wide-short:before,.fa-broadcast-tower:before,.fa-long-arrow-alt-up:before,.fa-file-arrow-down:before,.fa-bolt:before,.fa-cny:before,.fa-jpy:before,.fa-rmb:before,.fa-yen-sign:before,.fa-rouble:before,.fa-rub:before,.fa-ruble-sign:before,.fa-face-laugh-wink:before,.fa-arrow-alt-circle-down:before,.fa-arrow-down-short-wide:before,.fa-sort-amount-desc:before,.fa-arrow-right-long:before,.fa-ellipsis-h:before,.fa-first-aid:before,.fa-credit-card-alt:before,.fa-automobile:before,.fa-book-open-reader:before,.fa-temperature-arrow-up:before,.fa-h-square:before,.fa-temperature-4:before,.fa-temperature-full:before,.fa-thermometer-4:before,.fa-hands-helping:before,.fa-location-dot:before,.fa-person-swimming:before,.fa-droplet:before,.fa-earth-america:before,.fa-earth-americas:before,.fa-earth:before,.fa-battery-0:before,.fa-gauge-high:before,.fa-tachometer-alt-fast:before,.fa-hospital-alt:before,.fa-hospital-wide:before,.fa-bars-staggered:before,.fa-reorder:before,.fa-blind:before,.fa-check-to-slot:before,.fa-boxes-alt:before,.fa-boxes-stacked:before,.fa-chain:before,.fa-assistive-listening-systems:before,.fa-magnifying-glass:before,.fa-ping-pong-paddle-ball:before,.fa-table-tennis-paddle-ball:before,.fa-diagnoses:before,.fa-trash-can-arrow-up:before,.fa-file-edit:before,.fa-pen-square:before,.fa-pencil-square:before,.fa-battery-5:before,.fa-battery-full:before,.fa-list-dots:before,.fa-down-long:before,.fa-landmark-alt:before,.fa-television:before,.fa-tv-alt:before,.fa-list-check:before,.fa-circle-user:before,.fa-car-burst:before,.fa-person-snowboarding:before,.fa-shipping-fast:before,.fa-adjust:before,.fa-circle-radiation:before,.fa-baseball-ball:before,.fa-diagram-project:before,.fa-volume-mute:before,.fa-volume-times:before,.fa-grip-horizontal:before,.fa-share-from-square:before,.fa-child-combatant:before,.fa-phone-square:before,.fa-add:before,.fa-close:before,.fa-multiply:before,.fa-remove:before,.fa-times:before,.fa-arrows-up-down-left-right:before,.fa-chalkboard-teacher:before,.fa-quote-left-alt:before,.fa-trash-arrow-up:before,.fa-ils:before,.fa-shekel-sign:before,.fa-shekel:before,.fa-sheqel-sign:before,.fa-photo-film:before,.fa-sign-hanging:before,.fa-tablet-android:before,.fa-car-alt:before,.fa-battery-2:before,.fa-baby-carriage:before,.fa-percent:before,.fa-face-smile:before,.fa-thumb-tack:before,.fa-person-praying:before,.fa-rotate:before,.fa-cogs:before,.fa-face-grin-hearts:before,.fa-transgender-alt:before,.fa-arrow-turn-down:before,.fa-ticket-alt:before,.fa-angle-double-left:before,.fa-clock-rotate-left:before,.fa-face-grin-beam-sweat:before,.fa-arrow-right-from-file:before,.fa-shield-blank:before,.fa-arrow-up-short-wide:before,.fa-golf-ball-tee:before,.fa-chevron-circle-left:before,.fa-magic:before,.fa-wine-glass-alt:before,.fa-biking:before,.fa-earth-oceania:before,.fa-square-xmark:before,.fa-times-square:before,.fa-expand-alt:before,.fa-arrows-alt-v:before,.fa-bahai:before,.fa-circle-plus:before,.fa-face-grin-tongue-wink:before,.fa-chain-broken:before,.fa-chain-slash:before,.fa-link-slash:before,.fa-arrow-up-z-a:before,.fa-fire-alt:before,.fa-book-quran:before,.fa-angry:before,.fa-feed:before,.fa-balance-scale:before,.fa-gauge-simple-high:before,.fa-tachometer-fast:before,.fa-desktop-alt:before,.fa-table-list:before,.fa-comment-sms:before,.fa-battery-4:before,.fa-fist-raised:before,.fa-image-portrait:before,.fa-earth-europe:before,.fa-cart-flatbed-suitcase:before,.fa-rectangle-times:before,.fa-rectangle-xmark:before,.fa-times-rectangle:before,.fa-book-journal-whills:before,.fa-exclamation-triangle:before,.fa-triangle-exclamation:before,.fa-arrow-turn-right:before,.fa-mail-forward:before,.fa-exchange-alt:before,.fa-money-bill-1-wave:before,.fa-hands:before,.fa-sign-language:before,.fa-ladder-water:before,.fa-swimming-pool:before,.fa-arrows-up-down:before,.fa-face-grimace:before,.fa-wheelchair-alt:before,.fa-level-down-alt:before,.fa-envelope-square:before,.fa-band-aid:before,.fa-circle-xmark:before,.fa-times-circle:before,.fa-earth-asia:before,.fa-id-card-alt:before,.fa-magnifying-glass-plus:before,.fa-allergies:before,.fa-coffee:before,.fa-magnifying-glass-minus:before,.fa-user-alt:before,.fa-note-sticky:before,.fa-face-sad-tear:before,.fa-try:before,.fa-turkish-lira-sign:before,.fa-dollar-sign:before,.fa-dollar:before,.fa-magnifying-glass-dollar:before,.fa-users-cog:before,.fa-bank:before,.fa-building-columns:before,.fa-institution:before,.fa-museum:before,.fa-masks-theater:before,.fa-handshake-alt:before,.fa-fighter-jet:before,.fa-share-alt-square:before,.fa-video-camera:before,.fa-graduation-cap:before,.fa-level-up-alt:before,.fa-sr-only-focusable:not(:focus),.sr-only-focusable:not(:focus){position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border-width:0}:host,:root{--fa-style-family-brands:"Font Awesome 6 Brands";--fa-font-brands:normal 400 1em/1"Font Awesome 6 Brands"}.fa-js-square:before,.fa-reddit-square:before,.fa-instagram-square:before,.fa-hacker-news-square:before,.fa-snapchat-square:before,.fa-font-awesome-alt:before,.fa-square-viadeo:before,.fa-dribbble-square:before,.fa-square-twitter:before,.fa-square-youtube:before,.fa-rendact:before,.fa-square-steam:before,.fa-square-vimeo:before,.fa-font-awesome-flag:before,.fa-font-awesome-logo-full:before,.fa-github-square:before,.fa-gitlab-square:before,.fa-odnoklassniki-square:before,.fa-pinterest-square:before,.fa-google-plus-square:before,.fa-square-xing:before,.fa-42-group:before,.fa-pied-piper-square:before,.fa-facebook-square:before,.fa-lastfm-square:before,.fa-wirsindhandwerk:before,.fa-snapchat-ghost:before,.fa-behance-square:before,.fa-git-square:before,.fa-square-tumblr:before,.fa-telegram-plane:before,.fa-square-whatsapp:before,.fa-slack-hash:before,.fa-medium-m:before,:host,:root{--fa-font-regular:normal 400 1em/1"Font Awesome 6 Free"}:host,:root{--fa-style-family-classic:"Font Awesome 6 Free";--fa-font-solid:normal 900 1em/1"Font Awesome 6 Free"}
/*!
 * Bootstrap v5.1.3 (https://getbootstrap.com/)
 * Copyright 2011-2021 The Bootstrap Authors
 * Copyright 2011-2021 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
 */:root{--bs-blue:#0d6efd;--bs-indigo:#6610f2;--bs-purple:#6f42c1;--bs-pink:#d63384;--bs-red:#dc3545;--bs-orange:#fd7e14;--bs-yellow:#ffc107;--bs-green:#198754;--bs-teal:#20c997;--bs-cyan:#0dcaf0;--bs-white:#fff;--bs-gray:#6c757d;--bs-gray-dark:#343a40;--bs-gray-100:#f8f9fa;--bs-gray-200:#e9ecef;--bs-gray-300:#dee2e6;--bs-gray-400:#ced4da;--bs-gray-500:#adb5bd;--bs-gray-600:#6c757d;--bs-gray-700:#495057;--bs-gray-800:#343a40;--bs-gray-900:#212529;--bs-primary:#0d6efd;--bs-secondary:#6c757d;--bs-success:#198754;--bs-info:#0dcaf0;--bs-warning:#ffc107;--bs-danger:#dc3545;--bs-light:#f8f9fa;--bs-dark:#212529;--bs-primary-rgb:13,110,253;--bs-secondary-rgb:108,117,125;--bs-success-rgb:25,135,84;--bs-info-rgb:13,202,240;--bs-warning-rgb:255,193,7;--bs-danger-rgb:220,53,69;--bs-light-rgb:248,249,250;--bs-dark-rgb:33,37,41;--bs-white-rgb:255,255,255;--bs-black-rgb:0,0,0;--bs-body-color-rgb:33,37,41;--bs-body-bg-rgb:255,255,255;--bs-font-sans-serif:system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans","Liberation Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";--bs-font-monospace:SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;--bs-gradient:linear-gradient(180deg,rgba(255,255,255,0.15),rgba(255,255,255,0));--bs-body-font-family:var(--bs-font-sans-serif);--bs-body-font-size:1rem;--bs-body-font-weight:400;--bs-body-line-height:1.5;--bs-body-color:#212529;--bs-body-bg:#fff}*,::after,::before{box-sizing:border-box}@media (prefers-reduced-motion:no-preference){:root{scroll-behavior:smooth}}body{margin:0;font-size:var(--bs-body-font-size);line-height:var(--bs-body-line-height);text-align:var(--bs-body-text-align);-webkit-text-size-adjust:100%;-webkit-tap-highlight-color:transparent}h1,h2,h3,h4,h5,h6{margin-top:0;margin-bottom:.5rem;font-weight:500;line-height:1.2}@media (min-width:1200px){h1{font-size:2.5rem}}@media (min-width:1200px){h2{font-size:2rem}}@media (min-width:1200px){h3{font-size:1.75rem}}@media (min-width:1200px){h4{font-size:1.5rem}}h5{font-size:1.25rem}p{margin-top:0;margin-bottom:1rem}ul{padding-left:2rem}ul{margin-top:0;margin-bottom:1rem}ul ul{margin-bottom:0}strong{font-weight:bolder}a{color:#0d6efd}a:hover{color:#0a58ca}a:not([href]):not([class]):hover{color:inherit;text-decoration:none}img,svg{vertical-align:middle}button{border-radius:0}button:focus:not(:focus-visible){outline:0}button,input,select,textarea{margin:0;font-family:inherit;font-size:inherit;line-height:inherit}button,select{text-transform:none}[role=button]{cursor:pointer}select{word-wrap:normal}select:disabled{opacity:1}[list]::-webkit-calendar-picker-indicator{display:none}[type=button],[type=submit],button{-webkit-appearance:button}[type=button]:not(:disabled),[type=reset]:not(:disabled),[type=submit]:not(:disabled),button:not(:disabled){cursor:pointer}::-moz-focus-inner{padding:0;border-style:none}textarea{resize:vertical}::-webkit-datetime-edit-day-field,::-webkit-datetime-edit-fields-wrapper,::-webkit-datetime-edit-hour-field,::-webkit-datetime-edit-minute,::-webkit-datetime-edit-month-field,::-webkit-datetime-edit-text,::-webkit-datetime-edit-year-field{padding:0}::-webkit-inner-spin-button{height:auto}::-webkit-search-decoration{-webkit-appearance:none}::-webkit-color-swatch-wrapper{padding:0}::-webkit-file-upload-button{font:inherit}::file-selector-button{font:inherit}::-webkit-file-upload-button{font:inherit;-webkit-appearance:button}iframe{border:0}.blockquote-footer::before{content:"— "}.img-fluid{max-width:100%}.container{width:100%;padding-right:var(--bs-gutter-x,.75rem);padding-left:var(--bs-gutter-x,.75rem);margin-right:auto;margin-left:auto}@media (min-width:576px){.container{max-width:540px}}@media (min-width:768px){.container{max-width:720px}}@media (min-width:992px){.container{max-width:960px}}@media (min-width:1200px){.container{max-width:1140px}}@media (min-width:1400px){.container{max-width:1320px}}.row{--bs-gutter-x:1.5rem;--bs-gutter-y:0;display:flex;flex-wrap:wrap;margin-top:calc(-1*var(--bs-gutter-y));margin-right:calc(-.5*var(--bs-gutter-x));margin-left:calc(-.5*var(--bs-gutter-x))}.row>*{flex-shrink:0;width:100%;max-width:100%;padding-right:calc(var(--bs-gutter-x)*.5);padding-left:calc(var(--bs-gutter-x)*.5);margin-top:var(--bs-gutter-y)}.col-12{flex:0 0 auto;width:100%}@media (min-width:576px){.col-sm-6{flex:0 0 auto;width:50%}}@media (min-width:768px){.col-md-6{flex:0 0 auto;width:50%}}.table-hover>tbody>tr:hover>*{--bs-table-accent-bg:var(--bs-table-hover-bg);color:var(--bs-table-hover-color)}.form-control{display:block;width:100%;font-size:1rem;font-weight:400;line-height:1.5;background-clip:padding-box;-webkit-appearance:none;-moz-appearance:none;appearance:none;transition:border-color .15s ease-in-out,box-shadow .15s ease-in-out}@media (prefers-reduced-motion:reduce){.form-control{transition:none}}.form-control[type=file]:not(:disabled):not([readonly]){cursor:pointer}.form-control:focus{color:#212529;background-color:#fff;border-color:#86b7fe;outline:0;box-shadow:0 0 0 .25rem rgba(13,110,253,.25)}.form-control::-webkit-date-and-time-value{height:1.5em}.form-control::-moz-placeholder{color:#6c757d;opacity:1}.form-control::placeholder{color:#6c757d;opacity:1}.form-control:disabled{background-color:#e9ecef;opacity:1}.form-control::-webkit-file-upload-button{padding:.375rem .75rem;margin:-.375rem -.75rem;-webkit-margin-end:.75rem;margin-inline-end:.75rem;color:#212529;background-color:#e9ecef;pointer-events:none;border-color:inherit;border-style:solid;border-width:0;border-inline-end-width:1px;border-radius:0;-webkit-transition:color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;transition:color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out}.form-control::file-selector-button{padding:.375rem .75rem;margin:-.375rem -.75rem;-webkit-margin-end:.75rem;margin-inline-end:.75rem;color:#212529;background-color:#e9ecef;pointer-events:none;border-color:inherit;border-style:solid;border-width:0;border-inline-end-width:1px;border-radius:0;transition:color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out}@media (prefers-reduced-motion:reduce){.form-control::-webkit-file-upload-button{-webkit-transition:none;transition:none}.form-control::file-selector-button{transition:none}}.form-control:hover:not(:disabled):not([readonly])::-webkit-file-upload-button{background-color:#dde0e3}.form-control:hover:not(:disabled):not([readonly])::file-selector-button{background-color:#dde0e3}.form-control::-webkit-file-upload-button{padding:.375rem .75rem;margin:-.375rem -.75rem;-webkit-margin-end:.75rem;margin-inline-end:.75rem;color:#212529;background-color:#e9ecef;pointer-events:none;border-color:inherit;border-style:solid;border-width:0;border-inline-end-width:1px;border-radius:0;-webkit-transition:color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;transition:color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out}@media (prefers-reduced-motion:reduce){.form-control::-webkit-file-upload-button{-webkit-transition:none;transition:none}}.form-control:hover:not(:disabled):not([readonly])::-webkit-file-upload-button{background-color:#dde0e3}.form-control-sm::-webkit-file-upload-button{padding:.25rem .5rem;margin:-.25rem -.5rem;-webkit-margin-end:.5rem;margin-inline-end:.5rem}.form-control-sm::file-selector-button{padding:.25rem .5rem;margin:-.25rem -.5rem;-webkit-margin-end:.5rem;margin-inline-end:.5rem}.form-control-sm::-webkit-file-upload-button{padding:.25rem .5rem;margin:-.25rem -.5rem;-webkit-margin-end:.5rem;margin-inline-end:.5rem}.form-control-lg::-webkit-file-upload-button{padding:.5rem 1rem;margin:-.5rem -1rem;-webkit-margin-end:1rem;margin-inline-end:1rem}.form-control-lg::file-selector-button{padding:.5rem 1rem;margin:-.5rem -1rem;-webkit-margin-end:1rem;margin-inline-end:1rem}.form-control-lg::-webkit-file-upload-button{padding:.5rem 1rem;margin:-.5rem -1rem;-webkit-margin-end:1rem;margin-inline-end:1rem}textarea.form-control{min-height:calc(1.5em + .75rem + 2px)}.form-control-color:not(:disabled):not([readonly]){cursor:pointer}.form-control-color::-moz-color-swatch{height:1.5em;border-radius:.25rem}.form-control-color::-webkit-color-swatch{height:1.5em;border-radius:.25rem}.form-select:focus{border-color:#86b7fe;outline:0;box-shadow:0 0 0 .25rem rgba(13,110,253,.25)}.form-select:disabled{background-color:#e9ecef}.form-select:-moz-focusring{color:transparent;text-shadow:0 0 0#212529}.form-check-input:active{filter:brightness(90%)}.form-check-input:focus{border-color:#86b7fe;outline:0;box-shadow:0 0 0 .25rem rgba(13,110,253,.25)}.form-check-input:checked{background-color:#0d6efd;border-color:#0d6efd}.form-check-input:checked[type=checkbox]{background-image:url(data:image/svg+xml,%3csvg\ xmlns=\'http://www.w3.org/2000/svg\'\ viewBox=\'0\ 0\ 20\ 20\'%3e%3cpath\ fill=\'none\'\ stroke=\'%23fff\'\ stroke-linecap=\'round\'\ stroke-linejoin=\'round\'\ stroke-width=\'3\'\ d=\'M6\ 10l3\ 3l6-6\'/%3e%3c/svg%3e)}.form-check-input:checked[type=radio]{background-image:url(data:image/svg+xml,%3csvg\ xmlns=\'http://www.w3.org/2000/svg\'\ viewBox=\'-4\ -4\ 8\ 8\'%3e%3ccircle\ r=\'2\'\ fill=\'%23fff\'/%3e%3c/svg%3e)}.form-check-input[type=checkbox]:indeterminate{background-color:#0d6efd;border-color:#0d6efd;background-image:url(data:image/svg+xml,%3csvg\ xmlns=\'http://www.w3.org/2000/svg\'\ viewBox=\'0\ 0\ 20\ 20\'%3e%3cpath\ fill=\'none\'\ stroke=\'%23fff\'\ stroke-linecap=\'round\'\ stroke-linejoin=\'round\'\ stroke-width=\'3\'\ d=\'M6\ 10h8\'/%3e%3c/svg%3e)}.form-check-input:disabled{pointer-events:none;filter:none;opacity:.5}.form-check-input:disabled~.form-check-label{opacity:.5}.form-switch .form-check-input:focus{background-image:url(data:image/svg+xml,%3csvg\ xmlns=\'http://www.w3.org/2000/svg\'\ viewBox=\'-4\ -4\ 8\ 8\'%3e%3ccircle\ r=\'3\'\ fill=\'%2386b7fe\'/%3e%3c/svg%3e)}.form-switch .form-check-input:checked{background-position:right center;background-image:url(data:image/svg+xml,%3csvg\ xmlns=\'http://www.w3.org/2000/svg\'\ viewBox=\'-4\ -4\ 8\ 8\'%3e%3ccircle\ r=\'3\'\ fill=\'%23fff\'/%3e%3c/svg%3e)}.btn-check:disabled+.btn{pointer-events:none;filter:none;opacity:.65}.form-range:focus{outline:0}.form-range:focus::-webkit-slider-thumb{box-shadow:0 0 0 1px #fff,0 0 0 .25rem rgba(13,110,253,.25)}.form-range:focus::-moz-range-thumb{box-shadow:0 0 0 1px #fff,0 0 0 .25rem rgba(13,110,253,.25)}.form-range::-moz-focus-outer{border:0}.form-range::-webkit-slider-thumb{width:1rem;height:1rem;margin-top:-.25rem;background-color:#0d6efd;border:0;border-radius:1rem;-webkit-transition:background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;transition:background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;-webkit-appearance:none;appearance:none}@media (prefers-reduced-motion:reduce){.form-range::-webkit-slider-thumb{-webkit-transition:none;transition:none}}.form-range::-webkit-slider-thumb:active{background-color:#b6d4fe}.form-range::-webkit-slider-runnable-track{width:100%;height:.5rem;color:transparent;cursor:pointer;background-color:#dee2e6;border-color:transparent;border-radius:1rem}.form-range::-moz-range-thumb{width:1rem;height:1rem;background-color:#0d6efd;border:0;border-radius:1rem;-moz-transition:background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;transition:background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;-moz-appearance:none;appearance:none}@media (prefers-reduced-motion:reduce){.form-range::-moz-range-thumb{-moz-transition:none;transition:none}}.form-range::-moz-range-thumb:active{background-color:#b6d4fe}.form-range::-moz-range-track{width:100%;height:.5rem;color:transparent;cursor:pointer;background-color:#dee2e6;border-color:transparent;border-radius:1rem}.form-range:disabled{pointer-events:none}.form-range:disabled::-webkit-slider-thumb{background-color:#adb5bd}.form-range:disabled::-moz-range-thumb{background-color:#adb5bd}.form-floating>.form-control::-moz-placeholder{color:transparent}.form-floating>.form-control::placeholder{color:transparent}.form-floating>.form-control:not(:-moz-placeholder-shown){padding-top:1.625rem;padding-bottom:.625rem}.form-floating>.form-control:focus,.form-floating>.form-control:not(:placeholder-shown){padding-top:1.625rem;padding-bottom:.625rem}.form-floating>.form-control:-webkit-autofill{padding-top:1.625rem;padding-bottom:.625rem}.form-floating>.form-control:not(:-moz-placeholder-shown)~label{opacity:.65;transform:scale(.85) translateY(-.5rem) translateX(.15rem)}.form-floating>.form-control:focus~label,.form-floating>.form-control:not(:placeholder-shown)~label{opacity:.65;transform:scale(.85) translateY(-.5rem) translateX(.15rem)}.form-floating>.form-control:-webkit-autofill~label{opacity:.65;transform:scale(.85) translateY(-.5rem) translateX(.15rem)}.input-group>.form-control:focus,.input-group>.form-select:focus{z-index:3}.input-group .btn:focus{z-index:3}.was-validated :valid~.valid-feedback,.was-validated :valid~.valid-tooltip{display:block}.was-validated .form-control:valid{border-color:#198754;padding-right:calc(1.5em + .75rem);background-image:url(data:image/svg+xml,%3csvg\ xmlns=\'http://www.w3.org/2000/svg\'\ viewBox=\'0\ 0\ 8\ 8\'%3e%3cpath\ fill=\'%23198754\'\ d=\'M2.3\ 6.73L.6\ 4.53c-.4-1.04.46-1.4\ 1.1-.8l1.1\ 1.4\ 3.4-3.8c.6-.63\ 1.6-.27\ 1.2.7l-4\ 4.6c-.43.5-.8.4-1.1.1z\'/%3e%3c/svg%3e);background-repeat:no-repeat;background-position:right calc(.375em + .1875rem) center;background-size:calc(.75em + .375rem) calc(.75em + .375rem)}.form-control.is-valid:focus,.was-validated .form-control:valid:focus{border-color:#198754;box-shadow:0 0 0 .25rem rgba(25,135,84,.25)}.was-validated textarea.form-control:valid{padding-right:calc(1.5em + .75rem);background-position:top calc(.375em + .1875rem) right calc(.375em + .1875rem)}.was-validated .form-select:valid{border-color:#198754}.was-validated .form-select:valid:not([multiple]):not([size]),.was-validated .form-select:valid:not([multiple])[size="1"]{padding-right:4.125rem;background-image:url(data:image/svg+xml,%3csvg\ xmlns=\'http://www.w3.org/2000/svg\'\ viewBox=\'0\ 0\ 16\ 16\'%3e%3cpath\ fill=\'none\'\ stroke=\'%23343a40\'\ stroke-linecap=\'round\'\ stroke-linejoin=\'round\'\ stroke-width=\'2\'\ d=\'M2\ 5l6\ 6\ 6-6\'/%3e%3c/svg%3e),url(data:image/svg+xml,%3csvg\ xmlns=\'http://www.w3.org/2000/svg\'\ viewBox=\'0\ 0\ 8\ 8\'%3e%3cpath\ fill=\'%23198754\'\ d=\'M2.3\ 6.73L.6\ 4.53c-.4-1.04.46-1.4\ 1.1-.8l1.1\ 1.4\ 3.4-3.8c.6-.63\ 1.6-.27\ 1.2.7l-4\ 4.6c-.43.5-.8.4-1.1.1z\'/%3e%3c/svg%3e);background-position:right .75rem center,center right 2.25rem;background-size:16px 12px,calc(.75em + .375rem) calc(.75em + .375rem)}.form-select.is-valid:focus,.was-validated .form-select:valid:focus{border-color:#198754;box-shadow:0 0 0 .25rem rgba(25,135,84,.25)}.was-validated .form-check-input:valid{border-color:#198754}.form-check-input.is-valid:checked,.was-validated .form-check-input:valid:checked{background-color:#198754}.form-check-input.is-valid:focus,.was-validated .form-check-input:valid:focus{box-shadow:0 0 0 .25rem rgba(25,135,84,.25)}.was-validated .form-check-input:valid~.form-check-label{color:#198754}.was-validated .input-group .form-control:valid,.was-validated .input-group .form-select:valid{z-index:1}.input-group .form-control.is-valid:focus,.input-group .form-select.is-valid:focus,.was-validated .input-group .form-control:valid:focus,.was-validated .input-group .form-select:valid:focus{z-index:3}.was-validated :invalid~.invalid-feedback,.was-validated :invalid~.invalid-tooltip{display:block}.was-validated .form-control:invalid{border-color:#dc3545;padding-right:calc(1.5em + .75rem);background-image:url(data:image/svg+xml,%3csvg\ xmlns=\'http://www.w3.org/2000/svg\'\ viewBox=\'0\ 0\ 12\ 12\'\ width=\'12\'\ height=\'12\'\ fill=\'none\'\ stroke=\'%23dc3545\'%3e%3ccircle\ cx=\'6\'\ cy=\'6\'\ r=\'4.5\'/%3e%3cpath\ stroke-linejoin=\'round\'\ d=\'M5.8\ 3.6h.4L6\ 6.5z\'/%3e%3ccircle\ cx=\'6\'\ cy=\'8.2\'\ r=\'.6\'\ fill=\'%23dc3545\'\ stroke=\'none\'/%3e%3c/svg%3e);background-repeat:no-repeat;background-position:right calc(.375em + .1875rem) center;background-size:calc(.75em + .375rem) calc(.75em + .375rem)}.form-control.is-invalid:focus,.was-validated .form-control:invalid:focus{border-color:#dc3545;box-shadow:0 0 0 .25rem rgba(220,53,69,.25)}.was-validated textarea.form-control:invalid{padding-right:calc(1.5em + .75rem);background-position:top calc(.375em + .1875rem) right calc(.375em + .1875rem)}.was-validated .form-select:invalid{border-color:#dc3545}.was-validated .form-select:invalid:not([multiple]):not([size]),.was-validated .form-select:invalid:not([multiple])[size="1"]{padding-right:4.125rem;background-image:url(data:image/svg+xml,%3csvg\ xmlns=\'http://www.w3.org/2000/svg\'\ viewBox=\'0\ 0\ 16\ 16\'%3e%3cpath\ fill=\'none\'\ stroke=\'%23343a40\'\ stroke-linecap=\'round\'\ stroke-linejoin=\'round\'\ stroke-width=\'2\'\ d=\'M2\ 5l6\ 6\ 6-6\'/%3e%3c/svg%3e),url(data:image/svg+xml,%3csvg\ xmlns=\'http://www.w3.org/2000/svg\'\ viewBox=\'0\ 0\ 12\ 12\'\ width=\'12\'\ height=\'12\'\ fill=\'none\'\ stroke=\'%23dc3545\'%3e%3ccircle\ cx=\'6\'\ cy=\'6\'\ r=\'4.5\'/%3e%3cpath\ stroke-linejoin=\'round\'\ d=\'M5.8\ 3.6h.4L6\ 6.5z\'/%3e%3ccircle\ cx=\'6\'\ cy=\'8.2\'\ r=\'.6\'\ fill=\'%23dc3545\'\ stroke=\'none\'/%3e%3c/svg%3e);background-position:right .75rem center,center right 2.25rem;background-size:16px 12px,calc(.75em + .375rem) calc(.75em + .375rem)}.form-select.is-invalid:focus,.was-validated .form-select:invalid:focus{border-color:#dc3545;box-shadow:0 0 0 .25rem rgba(220,53,69,.25)}.was-validated .form-check-input:invalid{border-color:#dc3545}.form-check-input.is-invalid:checked,.was-validated .form-check-input:invalid:checked{background-color:#dc3545}.form-check-input.is-invalid:focus,.was-validated .form-check-input:invalid:focus{box-shadow:0 0 0 .25rem rgba(220,53,69,.25)}.was-validated .form-check-input:invalid~.form-check-label{color:#dc3545}.was-validated .input-group .form-control:invalid,.was-validated .input-group .form-select:invalid{z-index:2}.input-group .form-control.is-invalid:focus,.input-group .form-select.is-invalid:focus,.was-validated .input-group .form-control:invalid:focus,.was-validated .input-group .form-select:invalid:focus{z-index:3}.btn{display:inline-block;font-weight:400;line-height:1.5;text-align:center;text-decoration:none;vertical-align:middle;cursor:pointer;-webkit-user-select:none;-moz-user-select:none;user-select:none;background-color:transparent;font-size:1rem;transition:color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out}@media (prefers-reduced-motion:reduce){.btn{transition:none}}.btn:hover{color:#212529}.btn-check:focus+.btn,.btn:focus{outline:0;box-shadow:0 0 0 .25rem rgba(13,110,253,.25)}.btn:disabled,fieldset:disabled .btn{pointer-events:none;opacity:.65}.btn-primary{border-color:#0d6efd}.btn-primary:hover{color:#fff;background-color:#0b5ed7;border-color:#0a58ca}.btn-check:focus+.btn-primary,.btn-primary:focus{color:#fff;background-color:#0b5ed7;border-color:#0a58ca;box-shadow:0 0 0 .25rem rgba(49,132,253,.5)}.btn-check:active+.btn-primary,.btn-check:checked+.btn-primary,.btn-primary:active{color:#fff;background-color:#0a58ca;border-color:#0a53be}.btn-check:active+.btn-primary:focus,.btn-check:checked+.btn-primary:focus,.btn-primary.active:focus,.btn-primary:active:focus,.show>.btn-primary.dropdown-toggle:focus{box-shadow:0 0 0 .25rem rgba(49,132,253,.5)}.btn-primary:disabled{color:#fff;background-color:#0d6efd;border-color:#0d6efd}.btn-secondary{border-color:#6c757d}.btn-secondary:hover{color:#fff;background-color:#5c636a;border-color:#565e64}.btn-check:focus+.btn-secondary,.btn-secondary:focus{color:#fff;background-color:#5c636a;border-color:#565e64;box-shadow:0 0 0 .25rem rgba(130,138,145,.5)}.btn-check:active+.btn-secondary,.btn-check:checked+.btn-secondary,.btn-secondary:active{color:#fff;background-color:#565e64;border-color:#51585e}.btn-check:active+.btn-secondary:focus,.btn-check:checked+.btn-secondary:focus,.btn-secondary.active:focus,.btn-secondary:active:focus,.show>.btn-secondary.dropdown-toggle:focus{box-shadow:0 0 0 .25rem rgba(130,138,145,.5)}.btn-secondary:disabled{color:#fff;background-color:#6c757d;border-color:#6c757d}.btn-success:hover{color:#fff;background-color:#157347;border-color:#146c43}.btn-check:focus+.btn-success,.btn-success:focus{color:#fff;background-color:#157347;border-color:#146c43;box-shadow:0 0 0 .25rem rgba(60,153,110,.5)}.btn-check:active+.btn-success,.btn-check:checked+.btn-success,.btn-success:active{color:#fff;background-color:#146c43;border-color:#13653f}.btn-check:active+.btn-success:focus,.btn-check:checked+.btn-success:focus,.btn-success.active:focus,.btn-success:active:focus,.show>.btn-success.dropdown-toggle:focus{box-shadow:0 0 0 .25rem rgba(60,153,110,.5)}.btn-success:disabled{color:#fff;background-color:#198754;border-color:#198754}.btn-info:hover{color:#000;background-color:#31d2f2;border-color:#25cff2}.btn-check:focus+.btn-info,.btn-info:focus{color:#000;background-color:#31d2f2;border-color:#25cff2;box-shadow:0 0 0 .25rem rgba(11,172,204,.5)}.btn-check:active+.btn-info,.btn-check:checked+.btn-info,.btn-info:active{color:#000;background-color:#3dd5f3;border-color:#25cff2}.btn-check:active+.btn-info:focus,.btn-check:checked+.btn-info:focus,.btn-info.active:focus,.btn-info:active:focus,.show>.btn-info.dropdown-toggle:focus{box-shadow:0 0 0 .25rem rgba(11,172,204,.5)}.btn-info:disabled{color:#000;background-color:#0dcaf0;border-color:#0dcaf0}.btn-warning:hover{color:#000;background-color:#ffca2c;border-color:#ffc720}.btn-check:focus+.btn-warning,.btn-warning:focus{color:#000;background-color:#ffca2c;border-color:#ffc720;box-shadow:0 0 0 .25rem rgba(217,164,6,.5)}.btn-check:active+.btn-warning,.btn-check:checked+.btn-warning,.btn-warning:active{color:#000;background-color:#ffcd39;border-color:#ffc720}.btn-check:active+.btn-warning:focus,.btn-check:checked+.btn-warning:focus,.btn-warning.active:focus,.btn-warning:active:focus,.show>.btn-warning.dropdown-toggle:focus{box-shadow:0 0 0 .25rem rgba(217,164,6,.5)}.btn-warning:disabled{color:#000;background-color:#ffc107;border-color:#ffc107}.btn-danger:hover{color:#fff;background-color:#bb2d3b;border-color:#b02a37}.btn-check:focus+.btn-danger,.btn-danger:focus{color:#fff;background-color:#bb2d3b;border-color:#b02a37;box-shadow:0 0 0 .25rem rgba(225,83,97,.5)}.btn-check:active+.btn-danger,.btn-check:checked+.btn-danger,.btn-danger:active{color:#fff;background-color:#b02a37;border-color:#a52834}.btn-check:active+.btn-danger:focus,.btn-check:checked+.btn-danger:focus,.btn-danger.active:focus,.btn-danger:active:focus,.show>.btn-danger.dropdown-toggle:focus{box-shadow:0 0 0 .25rem rgba(225,83,97,.5)}.btn-danger:disabled{color:#fff;background-color:#dc3545;border-color:#dc3545}.btn-light:hover{color:#000;background-color:#f9fafb;border-color:#f9fafb}.btn-check:focus+.btn-light,.btn-light:focus{color:#000;background-color:#f9fafb;border-color:#f9fafb;box-shadow:0 0 0 .25rem rgba(211,212,213,.5)}.btn-check:active+.btn-light,.btn-check:checked+.btn-light,.btn-light:active{color:#000;background-color:#f9fafb;border-color:#f9fafb}.btn-check:active+.btn-light:focus,.btn-check:checked+.btn-light:focus,.btn-light.active:focus,.btn-light:active:focus,.show>.btn-light.dropdown-toggle:focus{box-shadow:0 0 0 .25rem rgba(211,212,213,.5)}.btn-light:disabled{color:#000;background-color:#f8f9fa;border-color:#f8f9fa}.btn-dark:hover{color:#fff;background-color:#1c1f23;border-color:#1a1e21}.btn-check:focus+.btn-dark,.btn-dark:focus{color:#fff;background-color:#1c1f23;border-color:#1a1e21;box-shadow:0 0 0 .25rem rgba(66,70,73,.5)}.btn-check:active+.btn-dark,.btn-check:checked+.btn-dark,.btn-dark:active{color:#fff;background-color:#1a1e21;border-color:#191c1f}.btn-check:active+.btn-dark:focus,.btn-check:checked+.btn-dark:focus,.btn-dark.active:focus,.btn-dark:active:focus,.show>.btn-dark.dropdown-toggle:focus{box-shadow:0 0 0 .25rem rgba(66,70,73,.5)}.btn-dark:disabled{color:#fff;background-color:#212529;border-color:#212529}.btn-outline-primary:hover{color:#fff;background-color:#0d6efd;border-color:#0d6efd}.btn-check:focus+.btn-outline-primary,.btn-outline-primary:focus{box-shadow:0 0 0 .25rem rgba(13,110,253,.5)}.btn-check:active+.btn-outline-primary,.btn-check:checked+.btn-outline-primary,.btn-outline-primary:active{color:#fff;background-color:#0d6efd;border-color:#0d6efd}.btn-check:active+.btn-outline-primary:focus,.btn-check:checked+.btn-outline-primary:focus,.btn-outline-primary.active:focus,.btn-outline-primary.dropdown-toggle.show:focus,.btn-outline-primary:active:focus{box-shadow:0 0 0 .25rem rgba(13,110,253,.5)}.btn-outline-primary:disabled{color:#0d6efd;background-color:transparent}.btn-outline-secondary:hover{color:#fff;background-color:#6c757d;border-color:#6c757d}.btn-check:focus+.btn-outline-secondary,.btn-outline-secondary:focus{box-shadow:0 0 0 .25rem rgba(108,117,125,.5)}.btn-check:active+.btn-outline-secondary,.btn-check:checked+.btn-outline-secondary,.btn-outline-secondary:active{color:#fff;background-color:#6c757d;border-color:#6c757d}.btn-check:active+.btn-outline-secondary:focus,.btn-check:checked+.btn-outline-secondary:focus,.btn-outline-secondary.active:focus,.btn-outline-secondary.dropdown-toggle.show:focus,.btn-outline-secondary:active:focus{box-shadow:0 0 0 .25rem rgba(108,117,125,.5)}.btn-outline-secondary:disabled{color:#6c757d;background-color:transparent}.btn-outline-success:hover{color:#fff;background-color:#198754;border-color:#198754}.btn-check:focus+.btn-outline-success,.btn-outline-success:focus{box-shadow:0 0 0 .25rem rgba(25,135,84,.5)}.btn-check:active+.btn-outline-success,.btn-check:checked+.btn-outline-success,.btn-outline-success:active{color:#fff;background-color:#198754;border-color:#198754}.btn-check:active+.btn-outline-success:focus,.btn-check:checked+.btn-outline-success:focus,.btn-outline-success.active:focus,.btn-outline-success.dropdown-toggle.show:focus,.btn-outline-success:active:focus{box-shadow:0 0 0 .25rem rgba(25,135,84,.5)}.btn-outline-success:disabled{color:#198754;background-color:transparent}.btn-outline-info:hover{color:#000;background-color:#0dcaf0;border-color:#0dcaf0}.btn-check:focus+.btn-outline-info,.btn-outline-info:focus{box-shadow:0 0 0 .25rem rgba(13,202,240,.5)}.btn-check:active+.btn-outline-info,.btn-check:checked+.btn-outline-info,.btn-outline-info:active{color:#000;background-color:#0dcaf0;border-color:#0dcaf0}.btn-check:active+.btn-outline-info:focus,.btn-check:checked+.btn-outline-info:focus,.btn-outline-info.active:focus,.btn-outline-info.dropdown-toggle.show:focus,.btn-outline-info:active:focus{box-shadow:0 0 0 .25rem rgba(13,202,240,.5)}.btn-outline-info:disabled{color:#0dcaf0;background-color:transparent}.btn-outline-warning:hover{color:#000;background-color:#ffc107;border-color:#ffc107}.btn-check:focus+.btn-outline-warning,.btn-outline-warning:focus{box-shadow:0 0 0 .25rem rgba(255,193,7,.5)}.btn-check:active+.btn-outline-warning,.btn-check:checked+.btn-outline-warning,.btn-outline-warning:active{color:#000;background-color:#ffc107;border-color:#ffc107}.btn-check:active+.btn-outline-warning:focus,.btn-check:checked+.btn-outline-warning:focus,.btn-outline-warning.active:focus,.btn-outline-warning.dropdown-toggle.show:focus,.btn-outline-warning:active:focus{box-shadow:0 0 0 .25rem rgba(255,193,7,.5)}.btn-outline-warning:disabled{color:#ffc107;background-color:transparent}.btn-outline-danger:hover{color:#fff;background-color:#dc3545;border-color:#dc3545}.btn-check:focus+.btn-outline-danger,.btn-outline-danger:focus{box-shadow:0 0 0 .25rem rgba(220,53,69,.5)}.btn-check:active+.btn-outline-danger,.btn-check:checked+.btn-outline-danger,.btn-outline-danger:active{color:#fff;background-color:#dc3545;border-color:#dc3545}.btn-check:active+.btn-outline-danger:focus,.btn-check:checked+.btn-outline-danger:focus,.btn-outline-danger.active:focus,.btn-outline-danger.dropdown-toggle.show:focus,.btn-outline-danger:active:focus{box-shadow:0 0 0 .25rem rgba(220,53,69,.5)}.btn-outline-danger:disabled{color:#dc3545;background-color:transparent}.btn-outline-light:hover{color:#000;background-color:#f8f9fa;border-color:#f8f9fa}.btn-check:focus+.btn-outline-light,.btn-outline-light:focus{box-shadow:0 0 0 .25rem rgba(248,249,250,.5)}.btn-check:active+.btn-outline-light,.btn-check:checked+.btn-outline-light,.btn-outline-light:active{color:#000;background-color:#f8f9fa;border-color:#f8f9fa}.btn-check:active+.btn-outline-light:focus,.btn-check:checked+.btn-outline-light:focus,.btn-outline-light.active:focus,.btn-outline-light.dropdown-toggle.show:focus,.btn-outline-light:active:focus{box-shadow:0 0 0 .25rem rgba(248,249,250,.5)}.btn-outline-light:disabled{color:#f8f9fa;background-color:transparent}.btn-outline-dark:hover{color:#fff;background-color:#212529;border-color:#212529}.btn-check:focus+.btn-outline-dark,.btn-outline-dark:focus{box-shadow:0 0 0 .25rem rgba(33,37,41,.5)}.btn-check:active+.btn-outline-dark,.btn-check:checked+.btn-outline-dark,.btn-outline-dark:active{color:#fff;background-color:#212529;border-color:#212529}.btn-check:active+.btn-outline-dark:focus,.btn-check:checked+.btn-outline-dark:focus,.btn-outline-dark.active:focus,.btn-outline-dark.dropdown-toggle.show:focus,.btn-outline-dark:active:focus{box-shadow:0 0 0 .25rem rgba(33,37,41,.5)}.btn-outline-dark:disabled{color:#212529;background-color:transparent}.btn-link:hover{color:#0a58ca}.btn-link:disabled{color:#6c757d}.fade{transition:opacity .15s linear}@media (prefers-reduced-motion:reduce){.fade{transition:none}}.fade:not(.show){opacity:0}.dropdown{position:relative}.dropdown-toggle{white-space:nowrap}.dropdown-toggle::after{display:inline-block;margin-left:.255em;vertical-align:.255em;content:"";border-top:.3em solid;border-right:.3em solid transparent;border-bottom:0;border-left:.3em solid transparent}.dropdown-toggle:empty::after{margin-left:0}.dropdown-menu{position:absolute;z-index:1000;padding:.5rem 0;color:#212529;text-align:left;background-color:#fff;background-clip:padding-box;border:1px solid rgba(0,0,0,.15);border-radius:.25rem}.dropup .dropdown-toggle::after{display:inline-block;margin-left:.255em;vertical-align:.255em;content:"";border-top:0;border-right:.3em solid transparent;border-bottom:.3em solid;border-left:.3em solid transparent}.dropup .dropdown-toggle:empty::after{margin-left:0}.dropend .dropdown-toggle::after{display:inline-block;margin-left:.255em;vertical-align:.255em;content:"";border-top:.3em solid transparent;border-right:0;border-bottom:.3em solid transparent;border-left:.3em solid}.dropend .dropdown-toggle:empty::after{margin-left:0}.dropend .dropdown-toggle::after{vertical-align:0}.dropstart .dropdown-toggle::after{display:inline-block;margin-left:.255em;vertical-align:.255em;content:""}.dropstart .dropdown-toggle::after{display:none}.dropstart .dropdown-toggle::before{display:inline-block;margin-right:.255em;vertical-align:.255em;content:"";border-top:.3em solid transparent;border-right:.3em solid;border-bottom:.3em solid transparent}.dropstart .dropdown-toggle:empty::after{margin-left:0}.dropstart .dropdown-toggle::before{vertical-align:0}.dropdown-item:focus,.dropdown-item:hover{color:#1e2125;background-color:#e9ecef}.dropdown-item:active{color:#fff;text-decoration:none;background-color:#0d6efd}.dropdown-item:disabled{color:#adb5bd;pointer-events:none;background-color:transparent}.dropdown-menu-dark .dropdown-item:focus,.dropdown-menu-dark .dropdown-item:hover{color:#fff;background-color:rgba(255,255,255,.15)}.dropdown-menu-dark .dropdown-item:active{color:#fff;background-color:#0d6efd}.dropdown-menu-dark .dropdown-item:disabled{color:#adb5bd}.btn-group-vertical>.btn-check:checked+.btn,.btn-group-vertical>.btn-check:focus+.btn,.btn-group-vertical>.btn:active,.btn-group-vertical>.btn:focus,.btn-group-vertical>.btn:hover,.btn-group>.btn-check:checked+.btn,.btn-group>.btn-check:focus+.btn,.btn-group>.btn:active,.btn-group>.btn:focus,.btn-group>.btn:hover{z-index:1}.dropdown-toggle-split::after,.dropend .dropdown-toggle-split::after,.dropup .dropdown-toggle-split::after{margin-left:0}.dropstart .dropdown-toggle-split::before{margin-right:0}.nav-link:focus,.nav-link:hover{color:#0a58ca}.nav-tabs .nav-link:focus,.nav-tabs .nav-link:hover{border-color:#e9ecef #e9ecef #dee2e6;isolation:isolate}.navbar-toggler:hover{text-decoration:none}.navbar-toggler:focus{text-decoration:none;outline:0;box-shadow:0 0 0 .25rem}.navbar-light .navbar-brand:focus,.navbar-light .navbar-brand:hover{color:rgba(0,0,0,.9)}.navbar-light .navbar-nav .nav-link:focus,.navbar-light .navbar-nav .nav-link:hover{color:rgba(0,0,0,.7)}.navbar-light .navbar-text a:focus,.navbar-light .navbar-text a:hover{color:rgba(0,0,0,.9)}.navbar-dark .navbar-brand:focus,.navbar-dark .navbar-brand:hover{color:#fff}.navbar-dark .navbar-nav .nav-link:focus,.navbar-dark .navbar-nav .nav-link:hover{color:rgba(255,255,255,.75)}.navbar-dark .navbar-text a:focus,.navbar-dark .navbar-text a:hover{color:#fff}.card{position:relative;display:flex;flex-direction:column;min-width:0;word-wrap:break-word;background-color:#fff;background-clip:border-box}.card-body{flex:1 1 auto;padding:1rem 1rem}.card-title{margin-bottom:.5rem}.card-img{border-top-left-radius:calc(.25rem - 1px);border-top-right-radius:calc(.25rem - 1px)}.card-img{border-bottom-right-radius:calc(.25rem - 1px);border-bottom-left-radius:calc(.25rem - 1px)}.accordion-button:not(.collapsed)::after{background-image:url(data:image/svg+xml,%3csvg\ xmlns=\'http://www.w3.org/2000/svg\'\ viewBox=\'0\ 0\ 16\ 16\'\ fill=\'%230c63e4\'%3e%3cpath\ fill-rule=\'evenodd\'\ d=\'M1.646\ 4.646a.5.5\ 0\ 0\ 1\ .708\ 0L8\ 10.293l5.646-5.647a.5.5\ 0\ 0\ 1\ .708.708l-6\ 6a.5.5\ 0\ 0\ 1-.708\ 0l-6-6a.5.5\ 0\ 0\ 1\ 0-.708z\'/%3e%3c/svg%3e);transform:rotate(-180deg)}.accordion-button::after{flex-shrink:0;width:1.25rem;height:1.25rem;margin-left:auto;content:"";background-image:url(data:image/svg+xml,%3csvg\ xmlns=\'http://www.w3.org/2000/svg\'\ viewBox=\'0\ 0\ 16\ 16\'\ fill=\'%23212529\'%3e%3cpath\ fill-rule=\'evenodd\'\ d=\'M1.646\ 4.646a.5.5\ 0\ 0\ 1\ .708\ 0L8\ 10.293l5.646-5.647a.5.5\ 0\ 0\ 1\ .708.708l-6\ 6a.5.5\ 0\ 0\ 1-.708\ 0l-6-6a.5.5\ 0\ 0\ 1\ 0-.708z\'/%3e%3c/svg%3e);background-repeat:no-repeat;background-size:1.25rem;transition:transform .2s ease-in-out}@media (prefers-reduced-motion:reduce){.accordion-button::after{transition:none}}.accordion-button:hover{z-index:2}.accordion-button:focus{z-index:3;border-color:#86b7fe;outline:0;box-shadow:0 0 0 .25rem rgba(13,110,253,.25)}.breadcrumb-item+.breadcrumb-item::before{float:left;padding-right:.5rem;color:#6c757d;content:var(--bs-breadcrumb-divider,"/")}.page-link:hover{z-index:2;color:#0a58ca;background-color:#e9ecef;border-color:#dee2e6}.page-link:focus{z-index:3;color:#0a58ca;background-color:#e9ecef;outline:0;box-shadow:0 0 0 .25rem rgba(13,110,253,.25)}.alert{position:relative;padding:1rem 1rem;margin-bottom:1rem;border:1px solid transparent;border-radius:.25rem}.alert-danger{color:#842029;background-color:#f8d7da;border-color:#f5c2c7}@-webkit-keyframes progress-bar-stripes{0%{background-position-x:1rem}}@keyframes progress-bar-stripes{0%{background-position-x:1rem}}.list-group-numbered>li::before{content:counters(section,".")". ";counter-increment:section}.list-group-item-action:focus,.list-group-item-action:hover{z-index:1;color:#495057;text-decoration:none;background-color:#f8f9fa}.list-group-item-action:active{color:#212529;background-color:#e9ecef}.list-group-item:disabled{color:#6c757d;pointer-events:none;background-color:#fff}.list-group-item-primary.list-group-item-action:focus,.list-group-item-primary.list-group-item-action:hover{color:#084298;background-color:#bacbe6}.list-group-item-secondary.list-group-item-action:focus,.list-group-item-secondary.list-group-item-action:hover{color:#41464b;background-color:#cbccce}.list-group-item-success.list-group-item-action:focus,.list-group-item-success.list-group-item-action:hover{color:#0f5132;background-color:#bcd0c7}.list-group-item-info.list-group-item-action:focus,.list-group-item-info.list-group-item-action:hover{color:#055160;background-color:#badce3}.list-group-item-warning.list-group-item-action:focus,.list-group-item-warning.list-group-item-action:hover{color:#664d03;background-color:#e6dbb9}.list-group-item-danger.list-group-item-action:focus,.list-group-item-danger.list-group-item-action:hover{color:#842029;background-color:#dfc2c4}.list-group-item-light.list-group-item-action:focus,.list-group-item-light.list-group-item-action:hover{color:#636464;background-color:#e5e5e5}.list-group-item-dark.list-group-item-action:focus,.list-group-item-dark.list-group-item-action:hover{color:#141619;background-color:#bebebf}.btn-close:hover{color:#000;text-decoration:none;opacity:.75}.btn-close:focus{outline:0;box-shadow:0 0 0 .25rem rgba(13,110,253,.25);opacity:1}.btn-close:disabled{pointer-events:none;-webkit-user-select:none;-moz-user-select:none;user-select:none;opacity:.25}.modal{position:fixed;top:0;left:0;width:100%;height:100%;overflow-x:hidden;overflow-y:auto;outline:0}.tooltip .tooltip-arrow::before{position:absolute;content:"";border-color:transparent;border-style:solid}.bs-tooltip-auto[data-popper-placement^=top] .tooltip-arrow::before,.bs-tooltip-top .tooltip-arrow::before{top:-1px;border-width:.4rem .4rem 0;border-top-color:#000}.bs-tooltip-auto[data-popper-placement^=right] .tooltip-arrow::before,.bs-tooltip-end .tooltip-arrow::before{right:-1px;border-width:.4rem .4rem .4rem 0;border-right-color:#000}.bs-tooltip-auto[data-popper-placement^=bottom] .tooltip-arrow::before,.bs-tooltip-bottom .tooltip-arrow::before{bottom:-1px;border-width:0 .4rem .4rem;border-bottom-color:#000}.bs-tooltip-auto[data-popper-placement^=left] .tooltip-arrow::before,.bs-tooltip-start .tooltip-arrow::before{left:-1px;border-width:.4rem 0 .4rem .4rem;border-left-color:#000}.popover .popover-arrow::after,.popover .popover-arrow::before{position:absolute;display:block;content:"";border-color:transparent;border-style:solid}.bs-popover-auto[data-popper-placement^=top]>.popover-arrow::before,.bs-popover-top>.popover-arrow::before{bottom:0;border-width:.5rem .5rem 0;border-top-color:rgba(0,0,0,.25)}.bs-popover-auto[data-popper-placement^=top]>.popover-arrow::after,.bs-popover-top>.popover-arrow::after{bottom:1px;border-width:.5rem .5rem 0;border-top-color:#fff}.bs-popover-auto[data-popper-placement^=right]>.popover-arrow::before,.bs-popover-end>.popover-arrow::before{left:0;border-width:.5rem .5rem .5rem 0;border-right-color:rgba(0,0,0,.25)}.bs-popover-auto[data-popper-placement^=right]>.popover-arrow::after,.bs-popover-end>.popover-arrow::after{left:1px;border-width:.5rem .5rem .5rem 0;border-right-color:#fff}.bs-popover-auto[data-popper-placement^=bottom]>.popover-arrow::before,.bs-popover-bottom>.popover-arrow::before{top:0;border-width:0 .5rem .5rem .5rem;border-bottom-color:rgba(0,0,0,.25)}.bs-popover-auto[data-popper-placement^=bottom]>.popover-arrow::after,.bs-popover-bottom>.popover-arrow::after{top:1px;border-width:0 .5rem .5rem .5rem;border-bottom-color:#fff}.bs-popover-auto[data-popper-placement^=bottom] .popover-header::before,.bs-popover-bottom .popover-header::before{position:absolute;top:0;left:50%;display:block;width:1rem;margin-left:-.5rem;content:"";border-bottom:1px solid #f0f0f0}.bs-popover-auto[data-popper-placement^=left]>.popover-arrow::before,.bs-popover-start>.popover-arrow::before{right:0;border-width:.5rem 0 .5rem .5rem;border-left-color:rgba(0,0,0,.25)}.bs-popover-auto[data-popper-placement^=left]>.popover-arrow::after,.bs-popover-start>.popover-arrow::after{right:1px;border-width:.5rem 0 .5rem .5rem;border-left-color:#fff}.carousel-inner::after{display:block;clear:both;content:""}.carousel-control-next:focus,.carousel-control-next:hover,.carousel-control-prev:focus,.carousel-control-prev:hover{color:#fff;text-decoration:none;outline:0;opacity:.9}@-webkit-keyframes spinner-border{to{transform:rotate(360deg)}}@keyframes spinner-border{to{transform:rotate(360deg)}}@-webkit-keyframes spinner-grow{0%{transform:scale(0)}50%{opacity:1;transform:none}}@keyframes spinner-grow{0%{transform:scale(0)}50%{opacity:1;transform:none}}.placeholder.btn::before{display:inline-block;content:""}@-webkit-keyframes placeholder-glow{50%{opacity:.2}}@keyframes placeholder-glow{50%{opacity:.2}}@-webkit-keyframes placeholder-wave{100%{-webkit-mask-position:-200% 0%;mask-position:-200% 0%}}@keyframes placeholder-wave{100%{-webkit-mask-position:-200% 0%;mask-position:-200% 0%}}.clearfix::after{display:block;clear:both;content:""}.link-primary:focus,.link-primary:hover{color:#0a58ca}.link-secondary:focus,.link-secondary:hover{color:#565e64}.link-success:focus,.link-success:hover{color:#146c43}.link-info:focus,.link-info:hover{color:#3dd5f3}.link-warning:focus,.link-warning:hover{color:#ffcd39}.link-danger:focus,.link-danger:hover{color:#b02a37}.link-light:focus,.link-light:hover{color:#f9fafb}.link-dark:focus,.link-dark:hover{color:#1a1e21}.ratio::before{display:block;padding-top:var(--bs-aspect-ratio);content:""}.visually-hidden-focusable:not(:focus):not(:focus-within){position:absolute!important;width:1px!important;height:1px!important;padding:0!important;margin:-1px!important;overflow:hidden!important;clip:rect(0,0,0,0)!important;white-space:nowrap!important;border:0!important}.stretched-link::after{position:absolute;top:0;right:0;bottom:0;left:0;z-index:1;content:""}.overflow-hidden{overflow:hidden!important}.d-inline-block{display:inline-block!important}.d-flex{display:flex!important}.d-inline-flex{display:inline-flex!important}.position-relative{position:relative!important}.position-absolute{position:absolute!important}.top-0{top:0!important}.top-100{top:100%!important}.end-0{right:0!important}.w-50{width:50%!important}.w-100{width:100%!important}.h-100{height:100%!important}.h-auto{height:auto!important}.flex-column{flex-direction:column!important}.flex-wrap{flex-wrap:wrap!important}.gap-2{gap:.5rem!important}.gap-3{gap:1rem!important}.justify-content-end{justify-content:flex-end!important}.justify-content-center{justify-content:center!important}.justify-content-between{justify-content:space-between!important}.justify-content-evenly{justify-content:space-evenly!important}.align-items-center{align-items:center!important}.m-0{margin:0!important}.mx-3{margin-right:1rem!important;margin-left:1rem!important}.mx-auto{margin-right:auto!important;margin-left:auto!important}.mt-0{margin-top:0!important}.mt-3{margin-top:1rem!important}.mt-4{margin-top:1.5rem!important}.mt-5{margin-top:3rem!important}.me-2{margin-right:.5rem!important}.me-3{margin-right:1rem!important}.mb-0{margin-bottom:0!important}.mb-1{margin-bottom:.25rem!important}.mb-2{margin-bottom:.5rem!important}.mb-3{margin-bottom:1rem!important}.ms-0{margin-left:0!important}.ms-2{margin-left:.5rem!important}.ms-auto{margin-left:auto!important}.p-0{padding:0!important}.p-3{padding:1rem!important}.px-0{padding-right:0!important;padding-left:0!important}.px-5{padding-right:3rem!important;padding-left:3rem!important}.py-1{padding-top:.25rem!important;padding-bottom:.25rem!important}.py-3{padding-top:1rem!important;padding-bottom:1rem!important}.pt-3{padding-top:1rem!important}.pb-0{padding-bottom:0!important}.pb-3{padding-bottom:1rem!important}.ps-0{padding-left:0!important}.fs-6{font-size:1rem!important}.text-start{text-align:left!important}.text-end{text-align:right!important}.text-center{text-align:center!important}.text-decoration-none{text-decoration:none!important}.text-decoration-underline{text-decoration:underline!important}.text-primary{--bs-text-opacity:1}.text-dark{--bs-text-opacity:1}.text-black{--bs-text-opacity:1;color:rgba(var(--bs-black-rgb),var(--bs-text-opacity))!important}.text-white{--bs-text-opacity:1;color:rgba(var(--bs-white-rgb),var(--bs-text-opacity))!important}.pe-none{pointer-events:none!important}@media (min-width:576px){.flex-sm-row{flex-direction:row!important}.flex-sm-nowrap{flex-wrap:nowrap!important}.me-sm-3{margin-right:1rem!important}.text-sm-start{text-align:left!important}}
.slick-slider{-ms-user-select:none;-ms-touch-action:pan-y}.slick-list:focus{outline:none}.slick-slider .slick-track,.slick-slider .slick-list{-webkit-transform:translate3d(0,0,0);-moz-transform:translate3d(0,0,0);-ms-transform:translate3d(0,0,0);-o-transform:translate3d(0,0,0)}.slick-track:before,.slick-track:after{display:table;content:""}.slick-track:after{clear:both}
.slick-prev:hover,.slick-prev:focus,.slick-next:hover,.slick-next:focus{color:transparent;outline:none;background:transparent}.slick-prev:hover:before,.slick-prev:focus:before,.slick-next:hover:before,.slick-next:focus:before{opacity:1}.slick-prev.slick-disabled:before,.slick-next.slick-disabled:before{opacity:.25}.slick-prev:before,.slick-next:before{font-family:"slick";font-size:20px;line-height:1;opacity:.75;color:white;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}.slick-prev:before{content:"←"}[dir="rtl"] .slick-prev:before{content:"→"}.slick-next:before{content:"→"}[dir="rtl"] .slick-next:before{content:"←"}.slick-dotted.slick-slider{margin-bottom:30px}.slick-dots{position:absolute;display:block;width:100%;padding:0;margin:0;list-style:none;text-align:center}.slick-dots li{position:relative;display:inline-block;padding:0;cursor:pointer}.slick-dots li button{font-size:0;line-height:0;display:block;padding:5px;cursor:pointer;color:transparent;border:0;outline:none;background:transparent}.slick-dots li button:hover,.slick-dots li button:focus{outline:none}.slick-dots li button:hover:before,.slick-dots li button:focus:before{opacity:1}.slick-dots li button:before{font-family:"slick";font-size:6px;line-height:20px;position:absolute;top:0;left:0;width:20px;height:20px;content:"•";text-align:center;opacity:.25;color:black;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}.slick-dots li.slick-active button:before{opacity:.75;color:black}
.slick-slider{-webkit-touch-callout:none;-webkit-tap-highlight-color:transparent;box-sizing:border-box;touch-action:pan-y;-webkit-user-select:none;-moz-user-select:none;user-select:none;-khtml-user-select:none}.slick-list,.slick-slider{display:block;position:relative}.slick-list{margin:0;overflow:hidden;padding:0}.slick-list:focus{outline:none}.slick-slider .slick-list,.slick-slider .slick-track{transform:translateZ(0)}.slick-track{display:block;left:0;margin-left:auto;margin-right:auto;position:relative;top:0}.slick-track:after,.slick-track:before{content:"";display:table}.slick-track:after{clear:both}.slick-slide{float:left;height:100%;min-height:1px}.slick-slide img{display:block}.slick-initialized .slick-slide{display:block}
.fs-14{font-size:14px}.pt-40{padding-top:40px}@media (max-width:575px){.pt-40{padding-top:25px}}.pt-30{padding-top:30px}@media (max-width:575px){.pt-30{padding-top:20px}}.pb-30{padding-bottom:30px}@media (max-width:575px){.pb-30{padding-bottom:20px}}.fw-5{font-weight:500}a{text-decoration:none}.main-content{min-height:100vh}.flex-1{flex:1}.cursor-pointer{cursor:pointer}.btn-section{right:80px}.btn-section .fixed-btn-section .bars-btn{align-items:center;border-radius:50%;display:flex;height:60px;justify-content:center;min-width:60px;width:60px}.btn-section .fixed-btn-section .sub-btn{position:absolute}.slick-dots li button:before{font-size:30px;opacity:1}#AppointmentModal .modal-dialog .modal-content .modal-body .form-label:after{color:#f62947;content:"*";font-size:inherit;font-weight:700;position:relative}
/*!
 * Font Awesome Free 6.1.1 by @fontawesome - https://fontawesome.com
 * License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License)
 * Copyright 2022 Fonticons, Inc.
 */.fa-2x{font-size:2em}@-webkit-keyframes fa-beat{0%,90%{-webkit-transform:scale(1);transform:scale(1)}45%{-webkit-transform:scale(var(--fa-beat-scale,1.25));transform:scale(var(--fa-beat-scale,1.25))}}@keyframes fa-beat{0%,90%{-webkit-transform:scale(1);transform:scale(1)}45%{-webkit-transform:scale(var(--fa-beat-scale,1.25));transform:scale(var(--fa-beat-scale,1.25))}}@-webkit-keyframes fa-bounce{0%{-webkit-transform:scale(1) translateY(0);transform:scale(1) translateY(0)}10%{-webkit-transform:scale(var(--fa-bounce-start-scale-x,1.1),var(--fa-bounce-start-scale-y,.9)) translateY(0);transform:scale(var(--fa-bounce-start-scale-x,1.1),var(--fa-bounce-start-scale-y,.9)) translateY(0)}30%{-webkit-transform:scale(var(--fa-bounce-jump-scale-x,.9),var(--fa-bounce-jump-scale-y,1.1)) translateY(var(--fa-bounce-height,-.5em));transform:scale(var(--fa-bounce-jump-scale-x,.9),var(--fa-bounce-jump-scale-y,1.1)) translateY(var(--fa-bounce-height,-.5em))}50%{-webkit-transform:scale(var(--fa-bounce-land-scale-x,1.05),var(--fa-bounce-land-scale-y,.95)) translateY(0);transform:scale(var(--fa-bounce-land-scale-x,1.05),var(--fa-bounce-land-scale-y,.95)) translateY(0)}57%{-webkit-transform:scale(1) translateY(var(--fa-bounce-rebound,-.125em));transform:scale(1) translateY(var(--fa-bounce-rebound,-.125em))}64%{-webkit-transform:scale(1) translateY(0);transform:scale(1) translateY(0)}to{-webkit-transform:scale(1) translateY(0);transform:scale(1) translateY(0)}}@keyframes fa-bounce{0%{-webkit-transform:scale(1) translateY(0);transform:scale(1) translateY(0)}10%{-webkit-transform:scale(var(--fa-bounce-start-scale-x,1.1),var(--fa-bounce-start-scale-y,.9)) translateY(0);transform:scale(var(--fa-bounce-start-scale-x,1.1),var(--fa-bounce-start-scale-y,.9)) translateY(0)}30%{-webkit-transform:scale(var(--fa-bounce-jump-scale-x,.9),var(--fa-bounce-jump-scale-y,1.1)) translateY(var(--fa-bounce-height,-.5em));transform:scale(var(--fa-bounce-jump-scale-x,.9),var(--fa-bounce-jump-scale-y,1.1)) translateY(var(--fa-bounce-height,-.5em))}50%{-webkit-transform:scale(var(--fa-bounce-land-scale-x,1.05),var(--fa-bounce-land-scale-y,.95)) translateY(0);transform:scale(var(--fa-bounce-land-scale-x,1.05),var(--fa-bounce-land-scale-y,.95)) translateY(0)}57%{-webkit-transform:scale(1) translateY(var(--fa-bounce-rebound,-.125em));transform:scale(1) translateY(var(--fa-bounce-rebound,-.125em))}64%{-webkit-transform:scale(1) translateY(0);transform:scale(1) translateY(0)}to{-webkit-transform:scale(1) translateY(0);transform:scale(1) translateY(0)}}@-webkit-keyframes fa-fade{50%{opacity:var(--fa-fade-opacity,.4)}}@keyframes fa-fade{50%{opacity:var(--fa-fade-opacity,.4)}}@-webkit-keyframes fa-beat-fade{0%,to{opacity:var(--fa-beat-fade-opacity,.4);-webkit-transform:scale(1);transform:scale(1)}50%{opacity:1;-webkit-transform:scale(var(--fa-beat-fade-scale,1.125));transform:scale(var(--fa-beat-fade-scale,1.125))}}@keyframes fa-beat-fade{0%,to{opacity:var(--fa-beat-fade-opacity,.4);-webkit-transform:scale(1);transform:scale(1)}50%{opacity:1;-webkit-transform:scale(var(--fa-beat-fade-scale,1.125));transform:scale(var(--fa-beat-fade-scale,1.125))}}@-webkit-keyframes fa-flip{50%{-webkit-transform:rotate3d(var(--fa-flip-x,0),var(--fa-flip-y,1),var(--fa-flip-z,0),var(--fa-flip-angle,-180deg));transform:rotate3d(var(--fa-flip-x,0),var(--fa-flip-y,1),var(--fa-flip-z,0),var(--fa-flip-angle,-180deg))}}@keyframes fa-flip{50%{-webkit-transform:rotate3d(var(--fa-flip-x,0),var(--fa-flip-y,1),var(--fa-flip-z,0),var(--fa-flip-angle,-180deg));transform:rotate3d(var(--fa-flip-x,0),var(--fa-flip-y,1),var(--fa-flip-z,0),var(--fa-flip-angle,-180deg))}}@-webkit-keyframes fa-shake{0%{-webkit-transform:rotate(-15deg);transform:rotate(-15deg)}4%{-webkit-transform:rotate(15deg);transform:rotate(15deg)}24%,8%{-webkit-transform:rotate(-18deg);transform:rotate(-18deg)}12%,28%{-webkit-transform:rotate(18deg);transform:rotate(18deg)}16%{-webkit-transform:rotate(-22deg);transform:rotate(-22deg)}20%{-webkit-transform:rotate(22deg);transform:rotate(22deg)}32%{-webkit-transform:rotate(-12deg);transform:rotate(-12deg)}36%{-webkit-transform:rotate(12deg);transform:rotate(12deg)}40%,to{-webkit-transform:rotate(0);transform:rotate(0)}}@keyframes fa-shake{0%{-webkit-transform:rotate(-15deg);transform:rotate(-15deg)}4%{-webkit-transform:rotate(15deg);transform:rotate(15deg)}24%,8%{-webkit-transform:rotate(-18deg);transform:rotate(-18deg)}12%,28%{-webkit-transform:rotate(18deg);transform:rotate(18deg)}16%{-webkit-transform:rotate(-22deg);transform:rotate(-22deg)}20%{-webkit-transform:rotate(22deg);transform:rotate(22deg)}32%{-webkit-transform:rotate(-12deg);transform:rotate(-12deg)}36%{-webkit-transform:rotate(12deg);transform:rotate(12deg)}40%,to{-webkit-transform:rotate(0);transform:rotate(0)}}@-webkit-keyframes fa-spin{0%{-webkit-transform:rotate(0);transform:rotate(0)}to{-webkit-transform:rotate(1turn);transform:rotate(1turn)}}@keyframes fa-spin{0%{-webkit-transform:rotate(0);transform:rotate(0)}to{-webkit-transform:rotate(1turn);transform:rotate(1turn)}}.fa-address-book:before,.fa-address-card:before,.fa-contact-card:before,.fa-angle-double-down:before,.fa-angle-double-left:before,.fa-angle-double-right:before,.fa-angle-double-up:before,.fa-apple-alt:before,.fa-arrow-down-1-9:before,.fa-sort-numeric-asc:before,.fa-arrow-down-9-1:before,.fa-sort-numeric-desc:before,.fa-arrow-down-a-z:before,.fa-sort-alpha-asc:before,.fa-arrow-down-long:before,.fa-arrow-down-short-wide:before,.fa-sort-amount-desc:before,.fa-arrow-down-wide-short:before,.fa-sort-amount-asc:before,.fa-arrow-down-z-a:before,.fa-sort-alpha-desc:before,.fa-arrow-left-long:before,.fa-arrow-pointer:before,.fa-arrow-right-arrow-left:before,.fa-arrow-right-from-bracket:before,.fa-arrow-right-long:before,.fa-arrow-right-to-bracket:before,.fa-arrow-left-rotate:before,.fa-arrow-rotate-back:before,.fa-arrow-rotate-backward:before,.fa-arrow-rotate-left:before,.fa-arrow-right-rotate:before,.fa-arrow-rotate-forward:before,.fa-arrow-rotate-right:before,.fa-arrow-turn-down:before,.fa-arrow-turn-up:before,.fa-arrow-up-1-9:before,.fa-arrow-up-9-1:before,.fa-arrow-up-a-z:before,.fa-arrow-up-long:before,.fa-arrow-up-right-from-square:before,.fa-arrow-up-short-wide:before,.fa-arrow-up-wide-short:before,.fa-arrow-up-z-a:before,.fa-arrows-h:before,.fa-arrows-rotate:before,.fa-refresh:before,.fa-arrows-up-down:before,.fa-arrows-up-down-left-right:before,.fa-baby-carriage:before,.fa-backward-fast:before,.fa-backward-step:before,.fa-bag-shopping:before,.fa-ban:before,.fa-ban-smoking:before,.fa-band-aid:before,.fa-bars:before,.fa-bars-progress:before,.fa-bars-staggered:before,.fa-reorder:before,.fa-baseball-ball:before,.fa-basket-shopping:before,.fa-basketball-ball:before,.fa-bath:before,.fa-battery-0:before,.fa-battery-5:before,.fa-battery-full:before,.fa-battery-3:before,.fa-battery-2:before,.fa-battery-4:before,.fa-bed-pulse:before,.fa-beer-mug-empty:before,.fa-bell-concierge:before,.fa-bolt:before,.fa-atlas:before,.fa-bible:before,.fa-book-journal-whills:before,.fa-book-open-reader:before,.fa-book-quran:before,.fa-book-dead:before,.fa-border-style:before,.fa-archive:before,.fa-boxes-alt:before,.fa-boxes-stacked:before,.fa-broom-ball:before,.fa-quidditch-broom-ball:before,.fa-bank:before,.fa-building-columns:before,.fa-institution:before,.fa-museum:before,.fa-burger:before,.fa-bus-alt:before,.fa-briefcase-clock:before,.fa-birthday-cake:before,.fa-cake-candles:before,.fa-calendar-alt:before,.fa-calendar-times:before,.fa-camera-alt:before,.fa-automobile:before,.fa-battery-car:before,.fa-car-burst:before,.fa-car-alt:before,.fa-cart-flatbed:before,.fa-cart-flatbed-suitcase:before,.fa-cart-shopping:before,.fa-blackboard:before,.fa-chalkboard-teacher:before,.fa-champagne-glasses:before,.fa-area-chart:before,.fa-bar-chart:before,.fa-chart-line:before,.fa-chart-pie:before,.fa-check-to-slot:before,.fa-arrow-circle-down:before,.fa-arrow-circle-left:before,.fa-arrow-circle-right:before,.fa-arrow-circle-up:before,.fa-check-circle:before,.fa-chevron-circle-down:before,.fa-chevron-circle-left:before,.fa-chevron-circle-right:before,.fa-chevron-circle-up:before,.fa-circle-dollar-to-slot:before,.fa-circle-dot:before,.fa-arrow-alt-circle-down:before,.fa-circle-exclamation:before,.fa-circle-h:before,.fa-adjust:before,.fa-circle-info:before,.fa-arrow-alt-circle-left:before,.fa-circle-minus:before,.fa-circle-pause:before,.fa-circle-play:before,.fa-circle-plus:before,.fa-circle-question:before,.fa-circle-radiation:before,.fa-arrow-alt-circle-right:before,.fa-circle-stop:before,.fa-arrow-alt-circle-up:before,.fa-circle-user:before,.fa-circle-xmark:before,.fa-times-circle:before,.fa-clock-four:before,.fa-clock-rotate-left:before,.fa-cloud-arrow-down:before,.fa-cloud-download-alt:before,.fa-cloud-arrow-up:before,.fa-cloud-upload-alt:before,.fa-cloud-bolt:before,.fa-comment-dots:before,.fa-comment-sms:before,.fa-compass-drafting:before,.fa-computer-mouse:before,.fa-credit-card-alt:before,.fa-crop-alt:before,.fa-backspace:before,.fa-desktop-alt:before,.fa-diagram-project:before,.fa-diamond-turn-right:before,.fa-dollar-sign:before,.fa-dollar:before,.fa-dolly-box:before,.fa-compress-alt:before,.fa-down-long:before,.fa-droplet:before,.fa-droplet-slash:before,.fa-deaf:before,.fa-deafness:before,.fa-ear-deaf:before,.fa-assistive-listening-systems:before,.fa-earth-africa:before,.fa-earth-america:before,.fa-earth-americas:before,.fa-earth:before,.fa-earth-asia:before,.fa-earth-europe:before,.fa-earth-oceania:before,.fa-ellipsis-h:before,.fa-ellipsis-v:before,.fa-envelopes-bulk:before,.fa-eur:before,.fa-euro-sign:before,.fa-eye-dropper-empty:before,.fa-eye-dropper:before,.fa-eye-low-vision:before,.fa-angry:before,.fa-dizzy:before,.fa-face-flushed:before,.fa-face-frown:before,.fa-face-frown-open:before,.fa-face-grimace:before,.fa-face-grin:before,.fa-face-grin-beam:before,.fa-face-grin-beam-sweat:before,.fa-face-grin-hearts:before,.fa-face-grin-squint:before,.fa-face-grin-squint-tears:before,.fa-face-grin-stars:before,.fa-face-grin-tears:before,.fa-face-grin-tongue:before,.fa-face-grin-tongue-squint:before,.fa-face-grin-tongue-wink:before,.fa-face-grin-wide:before,.fa-face-grin-wink:before,.fa-face-kiss:before,.fa-face-kiss-beam:before,.fa-face-kiss-wink-heart:before,.fa-face-laugh:before,.fa-face-laugh-beam:before,.fa-face-laugh-squint:before,.fa-face-laugh-wink:before,.fa-face-meh:before,.fa-face-meh-blank:before,.fa-face-rolling-eyes:before,.fa-face-sad-cry:before,.fa-face-sad-tear:before,.fa-face-smile:before,.fa-face-smile-beam:before,.fa-face-smile-wink:before,.fa-face-surprise:before,.fa-face-tired:before,.fa-feather-alt:before,.fa-file-arrow-down:before,.fa-file-arrow-up:before,.fa-arrow-right-from-file:before,.fa-arrow-right-to-file:before,.fa-file-alt:before,.fa-file-lines:before,.fa-file-edit:before,.fa-file-medical-alt:before,.fa-file-archive:before,.fa-filter-circle-dollar:before,.fa-fire-alt:before,.fa-burn:before,.fa-floppy-disk:before,.fa-folder-blank:before,.fa-football-ball:before,.fa-fast-forward:before,.fa-forward-step:before,.fa-futbol-ball:before,.fa-futbol:before,.fa-dashboard:before,.fa-gauge-med:before,.fa-gauge:before,.fa-gauge-high:before,.fa-tachometer-alt-fast:before,.fa-gauge-simple-med:before,.fa-gauge-simple:before,.fa-gauge-simple-high:before,.fa-tachometer-fast:before,.fa-gavel:before,.fa-cog:before,.fa-cogs:before,.fa-golf-ball-tee:before,.fa-graduation-cap:before,.fa-grip-horizontal:before,.fa-hand-paper:before,.fa-hand-back-fist:before,.fa-allergies:before,.fa-fist-raised:before,.fa-hand-holding-dollar:before,.fa-hand-holding-droplet:before,.fa-hands:before,.fa-sign-language:before,.fa-american-sign-language-interpreting:before,.fa-asl-interpreting:before,.fa-hands-american-sign-language-interpreting:before,.fa-hands-bubbles:before,.fa-hands-praying:before,.fa-hands-helping:before,.fa-handshake-alt:before,.fa-handshake-alt-slash:before,.fa-hard-drive:before,.fa-header:before,.fa-headphones-alt:before,.fa-heart-broken:before,.fa-heart-pulse:before,.fa-hard-hat:before,.fa-hat-hard:before,.fa-hospital-alt:before,.fa-hospital-wide:before,.fa-hot-tub-person:before,.fa-hourglass-2:before,.fa-hourglass-half:before,.fa-hourglass-3:before,.fa-hourglass-1:before,.fa-home-alt:before,.fa-home-lg-alt:before,.fa-home:before,.fa-home-lg:before,.fa-house-chimney-crack:before,.fa-clinic-medical:before,.fa-house-laptop:before,.fa-home-user:before,.fa-hryvnia-sign:before,.fa-heart-music-camera-bolt:before,.fa-drivers-license:before,.fa-id-card-alt:before,.fa-image-portrait:before,.fa-indian-rupee-sign:before,.fa-indian-rupee:before,.fa-fighter-jet:before,.fa-first-aid:before,.fa-landmark-alt:before,.fa-left-long:before,.fa-arrows-alt-h:before,.fa-chain:before,.fa-chain-broken:before,.fa-chain-slash:before,.fa-link-slash:before,.fa-list-squares:before,.fa-list-check:before,.fa-list-1-2:before,.fa-list-numeric:before,.fa-list-dots:before,.fa-location-crosshairs:before,.fa-location-dot:before,.fa-location-pin:before,.fa-magnifying-glass:before,.fa-magnifying-glass-dollar:before,.fa-magnifying-glass-location:before,.fa-magnifying-glass-minus:before,.fa-magnifying-glass-plus:before,.fa-map-location:before,.fa-map-location-dot:before,.fa-mars-stroke-h:before,.fa-mars-stroke-up:before,.fa-glass-martini-alt:before,.fa-cocktail:before,.fa-glass-martini:before,.fa-masks-theater:before,.fa-expand-arrows-alt:before,.fa-comment-alt:before,.fa-microphone-alt:before,.fa-microphone-alt-slash:before,.fa-compress-arrows-alt:before,.fa-minus:before,.fa-mobile-android:before,.fa-mobile-phone:before,.fa-mobile-android-alt:before,.fa-mobile-alt:before,.fa-money-bill-1:before,.fa-money-bill-1-wave:before,.fa-money-check-alt:before,.fa-coffee:before,.fa-note-sticky:before,.fa-dedent:before,.fa-paint-brush:before,.fa-file-clipboard:before,.fa-pen-alt:before,.fa-pen-ruler:before,.fa-edit:before,.fa-pencil-alt:before,.fa-people-arrows-left-right:before,.fa-people-carry-box:before,.fa-percent:before,.fa-male:before,.fa-biking:before,.fa-digging:before,.fa-diagnoses:before,.fa-female:before,.fa-hiking:before,.fa-person-praying:before,.fa-person-running:before,.fa-person-skating:before,.fa-person-skiing:before,.fa-person-skiing-nordic:before,.fa-person-snowboarding:before,.fa-person-swimming:before,.fa-person-walking:before,.fa-blind:before,.fa-phone-alt:before,.fa-phone-volume:before,.fa-photo-film:before,.fa-add:before,.fa-poo-bolt:before,.fa-prescription-bottle-alt:before,.fa-quote-left-alt:before,.fa-quote-right-alt:before,.fa-ad:before,.fa-list-alt:before,.fa-rectangle-times:before,.fa-rectangle-xmark:before,.fa-times-rectangle:before,.fa-mail-reply:before,.fa-mail-reply-all:before,.fa-right-from-bracket:before,.fa-exchange-alt:before,.fa-long-arrow-alt-right:before,.fa-right-to-bracket:before,.fa-rotate:before,.fa-rotate-back:before,.fa-rotate-backward:before,.fa-rotate-left:before,.fa-redo-alt:before,.fa-rotate-forward:before,.fa-feed:before,.fa-rouble:before,.fa-rub:before,.fa-ruble-sign:before,.fa-rupee-sign:before,.fa-balance-scale:before,.fa-balance-scale-left:before,.fa-balance-scale-right:before,.fa-cut:before,.fa-screwdriver-wrench:before,.fa-scroll-torah:before,.fa-seedling:before,.fa-shapes:before,.fa-arrow-turn-right:before,.fa-mail-forward:before,.fa-share-from-square:before,.fa-share-alt:before,.fa-ils:before,.fa-shekel-sign:before,.fa-shekel:before,.fa-sheqel-sign:before,.fa-shield-blank:before,.fa-shield-alt:before,.fa-shirt:before,.fa-t-shirt:before,.fa-shop:before,.fa-shop-slash:before,.fa-random:before,.fa-shuttle-space:before,.fa-sign-hanging:before,.fa-signal-5:before,.fa-signal-perfect:before,.fa-map-signs:before,.fa-sliders-h:before,.fa-sort:before,.fa-sort-desc:before,.fa-sort-asc:before,.fa-pastafarianism:before,.fa-spoon:before,.fa-air-freshener:before,.fa-external-link-square:before,.fa-caret-square-down:before,.fa-caret-square-left:before,.fa-caret-square-right:before,.fa-caret-square-up:before,.fa-check-square:before,.fa-envelope-square:before,.fa-h-square:before,.fa-minus-square:before,.fa-parking:before,.fa-pen-square:before,.fa-pencil-square:before,.fa-phone-square:before,.fa-phone-square-alt:before,.fa-plus-square:before,.fa-poll-h:before,.fa-poll:before,.fa-square-root-alt:before,.fa-rss-square:before,.fa-share-alt-square:before,.fa-external-link-square-alt:before,.fa-square-xmark:before,.fa-times-square:before,.fa-rod-asclepius:before,.fa-rod-snake:before,.fa-staff-aesculapius:before,.fa-star-half-alt:before,.fa-gbp:before,.fa-pound-sign:before,.fa-medkit:before,.fa-table-cells:before,.fa-table-cells-large:before,.fa-columns:before,.fa-table-list:before,.fa-ping-pong-paddle-ball:before,.fa-table-tennis-paddle-ball:before,.fa-tablet-android:before,.fa-tablet-alt:before,.fa-digital-tachograph:before,.fa-cab:before,.fa-temperature-arrow-down:before,.fa-temperature-arrow-up:before,.fa-temperature-0:before,.fa-temperature-empty:before,.fa-thermometer-0:before,.fa-temperature-4:before,.fa-temperature-full:before,.fa-thermometer-4:before,.fa-temperature-2:before,.fa-temperature-half:before,.fa-thermometer-2:before,.fa-temperature-1:before,.fa-temperature-quarter:before,.fa-thermometer-1:before,.fa-temperature-3:before,.fa-temperature-three-quarters:before,.fa-thermometer-3:before,.fa-tenge-sign:before,.fa-remove-format:before,.fa-thumb-tack:before,.fa-ticket-alt:before,.fa-broadcast-tower:before,.fa-subway:before,.fa-train-tram:before,.fa-transgender-alt:before,.fa-trash-arrow-up:before,.fa-trash-alt:before,.fa-trash-can-arrow-up:before,.fa-exclamation-triangle:before,.fa-triangle-exclamation:before,.fa-shipping-fast:before,.fa-ambulance:before,.fa-truck-loading:before,.fa-teletype:before,.fa-try:before,.fa-turkish-lira-sign:before,.fa-level-down-alt:before,.fa-level-up-alt:before,.fa-television:before,.fa-tv-alt:before,.fa-unlock-alt:before,.fa-arrows-alt-v:before,.fa-arrows-alt:before,.fa-long-arrow-alt-up:before,.fa-expand-alt:before,.fa-external-link-alt:before,.fa-user-doctor:before,.fa-user-cog:before,.fa-user-friends:before,.fa-user-alt:before,.fa-user-alt-slash:before,.fa-user-edit:before,.fa-user-times:before,.fa-users-cog:before,.fa-cutlery:before,.fa-shuttle-van:before,.fa-video-camera:before,.fa-volleyball-ball:before,.fa-volume-high:before,.fa-volume-down:before,.fa-volume-mute:before,.fa-volume-times:before,.fa-magic:before,.fa-magic-wand-sparkles:before,.fa-ladder-water:before,.fa-swimming-pool:before,.fa-weight-scale:before,.fa-wheat-alt:before,.fa-wheelchair-alt:before,.fa-glass-whiskey:before,.fa-wifi-3:before,.fa-wifi-strong:before,.fa-wine-glass-alt:before,.fa-krw:before,.fa-won-sign:before,.fa-close:before,.fa-multiply:before,.fa-remove:before,.fa-times:before,.fa-cny:before,.fa-jpy:before,.fa-rmb:before,.fa-yen-sign:before,.fa-sr-only-focusable:not(:focus),.sr-only-focusable:not(:focus){position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border-width:0}:host,:root{--fa-font-brands:normal 400 1em/1"Font Awesome 6 Brands"}.fa-42-group:before,.fa-font-awesome-flag:before,.fa-font-awesome-logo-full:before,.fa-medium-m:before,.fa-slack-hash:before,.fa-snapchat-ghost:before,.fa-font-awesome-alt:before,.fa-telegram-plane:before,.fa-wirsindhandwerk:before,:host,:root{--fa-font-regular:normal 400 1em/1"Font Awesome 6 Free"}:host,:root{--fa-font-solid:normal 900 1em/1"Font Awesome 6 Free"}.bi::before,[class*=" bi-"]::before,[class^=bi-]::before{display:inline-block;font-display:block;font-family:bootstrap-icons!important;font-style:normal;font-weight:400!important;font-variant:normal;text-transform:none;line-height:1;vertical-align:-.125em;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}.select2-container .select2-search--inline .select2-search__field::-webkit-search-cancel-button{-webkit-appearance:none}.select2-search--dropdown .select2-search__field::-webkit-search-cancel-button{-webkit-appearance:none}.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:focus,.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover{background-color:#f1f1f1;color:#333;outline:0}.select2-container--classic .select2-selection--single:focus{border:1px solid #5897fb}.select2-container--classic .select2-selection--multiple:focus{border:1px solid #5897fb}.select2-container--classic .select2-selection--multiple .select2-selection__choice__remove:hover{color:#555;outline:0}.toast-message a:hover{color:#ccc;text-decoration:none}.toast-close-button:focus,.toast-close-button:hover{color:#000;text-decoration:none;cursor:pointer;opacity:.4}#toast-container>div:hover{-moz-box-shadow:0 0 12px #000;-webkit-box-shadow:0 0 12px #000;box-shadow:0 0 12px #000;opacity:1;cursor:pointer}.flatpickr-calendar{text-align:center;direction:ltr;border:0;font-size:14px;line-height:24px;border-radius:5px;position:absolute;-webkit-box-sizing:border-box;box-sizing:border-box;-ms-touch-action:manipulation;touch-action:manipulation;background:#fff;-webkit-box-shadow:1px 0 0#e6e6e6,-1px 0 0#e6e6e6,0 1px 0#e6e6e6,0-1px 0#e6e6e6,0 3px 13px rgba(0,0,0,.08)}.flatpickr-calendar.open{opacity:1;max-height:640px;visibility:visible}.flatpickr-calendar.open{display:inline-block;z-index:99999}.flatpickr-calendar.animate.open{-webkit-animation:fpFadeInDown .3s cubic-bezier(.23,1,.32,1);animation:fpFadeInDown .3s cubic-bezier(.23,1,.32,1)}.flatpickr-calendar:after,.flatpickr-calendar:before{position:absolute;display:block;pointer-events:none;border:solid transparent;content:"";height:0;width:0;left:22px}.flatpickr-calendar.arrowRight:after,.flatpickr-calendar.arrowRight:before,.flatpickr-calendar.rightMost:after,.flatpickr-calendar.rightMost:before{left:auto;right:22px}.flatpickr-calendar.arrowCenter:after,.flatpickr-calendar.arrowCenter:before{left:50%;right:50%}.flatpickr-calendar:before{border-width:5px;margin:0-5px}.flatpickr-calendar:after{border-width:4px;margin:0-4px}.flatpickr-calendar.arrowTop:after,.flatpickr-calendar.arrowTop:before{bottom:100%}.flatpickr-calendar.arrowTop:before{border-bottom-color:#e6e6e6}.flatpickr-calendar.arrowTop:after{border-bottom-color:#fff}.flatpickr-calendar.arrowBottom:after,.flatpickr-calendar.arrowBottom:before{top:100%}.flatpickr-calendar.arrowBottom:before{border-top-color:#e6e6e6}.flatpickr-calendar.arrowBottom:after{border-top-color:#fff}.flatpickr-calendar:focus{outline:0}.flatpickr-months{display:flex}.flatpickr-months .flatpickr-month{background:0 0;color:rgba(0,0,0,.9);fill:rgba(0,0,0,0.9);height:34px;line-height:1;text-align:center;position:relative;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;overflow:hidden;-webkit-box-flex:1;-webkit-flex:1;-ms-flex:1;flex:1}.flatpickr-months .flatpickr-next-month,.flatpickr-months .flatpickr-prev-month{-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;text-decoration:none;cursor:pointer;position:absolute;height:34px;z-index:3;color:rgba(0,0,0,.9);fill:rgba(0,0,0,0.9)}.flatpickr-months .flatpickr-next-month:hover,.flatpickr-months .flatpickr-prev-month:hover{color:#959ea9}.flatpickr-months .flatpickr-next-month:hover svg,.flatpickr-months .flatpickr-prev-month:hover svg{fill:#f64747}.flatpickr-months .flatpickr-next-month svg,.flatpickr-months .flatpickr-prev-month svg{width:14px;height:14px}.flatpickr-months .flatpickr-next-month svg path,.flatpickr-months .flatpickr-prev-month svg path{-webkit-transition:fill .1s;transition:fill .1s;fill:inherit}.numInputWrapper{position:relative;height:auto}.numInputWrapper input,.numInputWrapper span{display:inline-block}.numInputWrapper input{width:100%}.numInputWrapper input::-ms-clear{display:none}.numInputWrapper input::-webkit-inner-spin-button,.numInputWrapper input::-webkit-outer-spin-button{margin:0;-webkit-appearance:none}.numInputWrapper span{position:absolute;right:0;width:14px;padding:0 4px 0 2px;height:50%;line-height:50%;opacity:0;cursor:pointer;border:1px solid rgba(57,57,57,.15);-webkit-box-sizing:border-box;box-sizing:border-box}.numInputWrapper span:hover{background:rgba(0,0,0,.1)}.numInputWrapper span:active{background:rgba(0,0,0,.2)}.numInputWrapper span:after{display:block;content:"";position:absolute}.numInputWrapper span.arrowUp{top:0;border-bottom:0}.numInputWrapper span.arrowUp:after{border-left:4px solid transparent;border-right:4px solid transparent;border-bottom:4px solid rgba(57,57,57,.6);top:26%}.numInputWrapper span.arrowDown{top:50%}.numInputWrapper span.arrowDown:after{border-left:4px solid transparent;border-right:4px solid transparent;border-top:4px solid rgba(57,57,57,.6);top:40%}.numInputWrapper:hover{background:rgba(0,0,0,.05)}.numInputWrapper:hover span{opacity:1}.flatpickr-current-month{font-size:135%;font-weight:300;color:inherit;position:absolute;width:75%;left:12.5%;padding:7.48px 0 0 0;line-height:1;height:34px;text-align:center;-webkit-transform:translate3d(0,0,0);transform:translate3d(0,0,0)}.flatpickr-current-month span.cur-month:hover{background:rgba(0,0,0,.05)}.flatpickr-current-month .numInputWrapper{width:6ch;display:inline-block}.flatpickr-current-month .numInputWrapper span.arrowUp:after{border-bottom-color:rgba(0,0,0,.9)}.flatpickr-current-month .numInputWrapper span.arrowDown:after{border-top-color:rgba(0,0,0,.9)}.flatpickr-current-month input.cur-year{background:0 0;-webkit-box-sizing:border-box;box-sizing:border-box;color:inherit;cursor:text;padding:0 0 0 .5ch;margin:0;display:inline-block;font-size:inherit;font-family:inherit;font-weight:300;line-height:inherit;height:auto;border:0;border-radius:0;vertical-align:initial;-webkit-appearance:textfield;-moz-appearance:textfield;appearance:textfield}.flatpickr-current-month input.cur-year:focus{outline:0}.flatpickr-current-month input.cur-year[disabled]:hover{font-size:100%;color:rgba(0,0,0,.5);background:0 0;pointer-events:none}.flatpickr-current-month .flatpickr-monthDropdown-months{appearance:menulist;background:0 0;border:none;border-radius:0;box-sizing:border-box;cursor:pointer;font-family:inherit;height:auto;line-height:inherit;margin:-1px 0 0 0;outline:0;padding:0 0 0 .5ch;position:relative;vertical-align:initial;-webkit-box-sizing:border-box;-webkit-appearance:menulist;-moz-appearance:menulist;width:auto}.flatpickr-current-month .flatpickr-monthDropdown-months:active,.flatpickr-current-month .flatpickr-monthDropdown-months:focus{outline:0}.flatpickr-current-month .flatpickr-monthDropdown-months:hover{background:rgba(0,0,0,.05)}.flatpickr-current-month .flatpickr-monthDropdown-months .flatpickr-monthDropdown-month{background-color:transparent;outline:0;padding:0}.flatpickr-weekdays{background:0 0;text-align:center;overflow:hidden;width:100%;display:flex;-webkit-box-align:center;-webkit-align-items:center;-ms-flex-align:center;align-items:center;height:28px}.flatpickr-weekdays .flatpickr-weekdaycontainer{display:flex;-webkit-box-flex:1;-webkit-flex:1;-ms-flex:1;flex:1}span.flatpickr-weekday{cursor:default;background:0 0;line-height:1;margin:0;text-align:center;display:block;-webkit-box-flex:1;-webkit-flex:1;-ms-flex:1;flex:1}.flatpickr-days{position:relative;overflow:hidden;display:flex;-webkit-box-align:start;-webkit-align-items:flex-start;-ms-flex-align:start;align-items:flex-start}.flatpickr-days:focus{outline:0}.dayContainer{padding:0;outline:0;text-align:left;-webkit-box-sizing:border-box;box-sizing:border-box;display:flex;-webkit-flex-wrap:wrap;flex-wrap:wrap;-ms-flex-wrap:wrap;-ms-flex-pack:justify;-webkit-justify-content:space-around;justify-content:space-around;-webkit-transform:translate3d(0,0,0);transform:translate3d(0,0,0);opacity:1}.flatpickr-day{background:0 0;border:1px solid transparent;-webkit-box-sizing:border-box;box-sizing:border-box;cursor:pointer;font-weight:400;-webkit-flex-basis:14.2857143%;-ms-flex-preferred-size:14.2857143%;flex-basis:14.2857143%;margin:0;display:inline-block;position:relative;-webkit-box-pack:center;-webkit-justify-content:center;-ms-flex-pack:center;justify-content:center;text-align:center}.flatpickr-day.nextMonthDay:focus,.flatpickr-day.nextMonthDay:hover,.flatpickr-day.prevMonthDay:focus,.flatpickr-day.prevMonthDay:hover,.flatpickr-day:focus,.flatpickr-day:hover{cursor:pointer;outline:0;background:#e6e6e6;border-color:#e6e6e6}.flatpickr-day.today:focus,.flatpickr-day.today:hover{border-color:#959ea9;background:#959ea9;color:#fff}.flatpickr-day.endRange:focus,.flatpickr-day.endRange:hover,.flatpickr-day.selected:focus,.flatpickr-day.selected:hover,.flatpickr-day.startRange:focus,.flatpickr-day.startRange:hover{background:#569ff7;-webkit-box-shadow:none;box-shadow:none;color:#fff;border-color:#569ff7}.flatpickr-day.flatpickr-disabled,.flatpickr-day.flatpickr-disabled:hover,.flatpickr-day.nextMonthDay,.flatpickr-day.prevMonthDay{color:rgba(57,57,57,.3);background:0 0;border-color:transparent;cursor:default}.flatpickr-day.flatpickr-disabled,.flatpickr-day.flatpickr-disabled:hover{cursor:not-allowed;color:rgba(57,57,57,.1)}.flatpickr-weekwrapper span.flatpickr-day:hover{display:block;width:100%;max-width:none;color:rgba(57,57,57,.3);background:0 0;cursor:default;border:none}.flatpickr-innerContainer{display:flex;-webkit-box-sizing:border-box;box-sizing:border-box;overflow:hidden}.flatpickr-rContainer{display:inline-block;padding:0;-webkit-box-sizing:border-box;box-sizing:border-box}.flatpickr-time:after{content:"";display:table;clear:both}.flatpickr-time .numInputWrapper span.arrowUp:after{border-bottom-color:#393939}.flatpickr-time .numInputWrapper span.arrowDown:after{border-top-color:#393939}.flatpickr-time input:focus{outline:0;border:0}.flatpickr-time .flatpickr-am-pm:focus,.flatpickr-time .flatpickr-am-pm:hover,.flatpickr-time input:focus,.flatpickr-time input:hover{background:#eee}.flatpickr-input[readonly]{cursor:pointer}@-webkit-keyframes fpFadeInDown{from{opacity:0;-webkit-transform:translate3d(0,-20px,0);transform:translate3d(0,-20px,0)}to{opacity:1;-webkit-transform:translate3d(0,0,0);transform:translate3d(0,0,0)}}@keyframes fpFadeInDown{from{opacity:0;-webkit-transform:translate3d(0,-20px,0);transform:translate3d(0,-20px,0)}to{opacity:1;-webkit-transform:translate3d(0,0,0);transform:translate3d(0,0,0)}}/*!
 * Datepicker for Bootstrap v1.9.0 (https://github.com/uxsolutions/bootstrap-datepicker)
 *
 * Licensed under the Apache License v2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 */.datepicker-dropdown:before{content:"";display:inline-block;border-left:7px solid transparent;border-right:7px solid transparent;border-bottom:7px solid #999;border-top:0;border-bottom-color:rgba(0,0,0,.2);position:absolute}.datepicker-dropdown:after{content:"";display:inline-block;border-left:6px solid transparent;border-right:6px solid transparent;border-bottom:6px solid #fff;border-top:0;position:absolute}.datepicker-dropdown.datepicker-orient-left:before{left:6px}.datepicker-dropdown.datepicker-orient-left:after{left:7px}.datepicker-dropdown.datepicker-orient-right:before{right:6px}.datepicker-dropdown.datepicker-orient-right:after{right:7px}.datepicker-dropdown.datepicker-orient-bottom:before{top:-7px}.datepicker-dropdown.datepicker-orient-bottom:after{top:-6px}.datepicker-dropdown.datepicker-orient-top:before{bottom:-7px;border-bottom:0;border-top:7px solid #999}.datepicker-dropdown.datepicker-orient-top:after{bottom:-6px;border-bottom:0;border-top:6px solid #fff}.datepicker table tr td.day:hover{background:#eee;cursor:pointer}.datepicker table tr td.disabled:hover{background:0 0;color:#999;cursor:default}.datepicker table tr td.today.disabled:hover,.datepicker table tr td.today:hover{background-color:#fde19a;background-image:-moz-linear-gradient(to bottom,#fdd49a,#fdf59a);background-image:-ms-linear-gradient(to bottom,#fdd49a,#fdf59a);background-image:-webkit-gradient(linear,0 0,0 100%,from(#fdd49a),to(#fdf59a));background-image:-webkit-linear-gradient(to bottom,#fdd49a,#fdf59a);background-image:-o-linear-gradient(to bottom,#fdd49a,#fdf59a);background-image:linear-gradient(to bottom,#fdd49a,#fdf59a);background-repeat:repeat-x;border-color:#fdf59a #fdf59a #fbed50;border-color:rgba(0,0,0,.1) rgba(0,0,0,.1) rgba(0,0,0,.25);color:#000}.datepicker table tr td.today.disabled:active,.datepicker table tr td.today.disabled:hover,.datepicker table tr td.today.disabled:hover.active,.datepicker table tr td.today.disabled:hover.disabled,.datepicker table tr td.today.disabled:hover:active,.datepicker table tr td.today.disabled:hover:hover,.datepicker table tr td.today.disabled:hover[disabled],.datepicker table tr td.today:active,.datepicker table tr td.today:hover,.datepicker table tr td.today:hover.active,.datepicker table tr td.today:hover.disabled,.datepicker table tr td.today:hover:active,.datepicker table tr td.today:hover:hover,.datepicker table tr td.today:hover[disabled]{background-color:#fdf59a}.datepicker table tr td.today:hover:hover{color:#000}.datepicker table tr td.today.active:hover{color:#fff}.datepicker table tr td.range.disabled:hover,.datepicker table tr td.range:hover{background:#eee;-webkit-border-radius:0;-moz-border-radius:0;border-radius:0}.datepicker table tr td.range.today.disabled:hover,.datepicker table tr td.range.today:hover{background-color:#f3d17a;background-image:-moz-linear-gradient(to bottom,#f3c17a,#f3e97a);background-image:-ms-linear-gradient(to bottom,#f3c17a,#f3e97a);background-image:-webkit-gradient(linear,0 0,0 100%,from(#f3c17a),to(#f3e97a));background-image:-webkit-linear-gradient(to bottom,#f3c17a,#f3e97a);background-image:-o-linear-gradient(to bottom,#f3c17a,#f3e97a);background-image:linear-gradient(to bottom,#f3c17a,#f3e97a);background-repeat:repeat-x;border-color:#f3e97a #f3e97a #edde34;border-color:rgba(0,0,0,.1) rgba(0,0,0,.1) rgba(0,0,0,.25);-webkit-border-radius:0;-moz-border-radius:0;border-radius:0}.datepicker table tr td.range.today.disabled:active,.datepicker table tr td.range.today.disabled:hover,.datepicker table tr td.range.today.disabled:hover.active,.datepicker table tr td.range.today.disabled:hover.disabled,.datepicker table tr td.range.today.disabled:hover:active,.datepicker table tr td.range.today.disabled:hover:hover,.datepicker table tr td.range.today.disabled:hover[disabled],.datepicker table tr td.range.today:active,.datepicker table tr td.range.today:hover,.datepicker table tr td.range.today:hover.active,.datepicker table tr td.range.today:hover.disabled,.datepicker table tr td.range.today:hover:active,.datepicker table tr td.range.today:hover:hover,.datepicker table tr td.range.today:hover[disabled]{background-color:#f3e97a}.datepicker table tr td.selected.disabled:hover,.datepicker table tr td.selected:hover{background-color:#9e9e9e;background-image:-moz-linear-gradient(to bottom,#b3b3b3,grey);background-image:-ms-linear-gradient(to bottom,#b3b3b3,grey);background-image:-webkit-gradient(linear,0 0,0 100%,from(#b3b3b3),to(grey));background-image:-webkit-linear-gradient(to bottom,#b3b3b3,grey);background-image:-o-linear-gradient(to bottom,#b3b3b3,grey);background-image:linear-gradient(to bottom,#b3b3b3,grey);background-repeat:repeat-x;border-color:grey grey #595959;border-color:rgba(0,0,0,.1) rgba(0,0,0,.1) rgba(0,0,0,.25);color:#fff;text-shadow:0-1px 0 rgba(0,0,0,.25)}.datepicker table tr td.selected.disabled:active,.datepicker table tr td.selected.disabled:hover,.datepicker table tr td.selected.disabled:hover.active,.datepicker table tr td.selected.disabled:hover.disabled,.datepicker table tr td.selected.disabled:hover:active,.datepicker table tr td.selected.disabled:hover:hover,.datepicker table tr td.selected.disabled:hover[disabled],.datepicker table tr td.selected:active,.datepicker table tr td.selected:hover,.datepicker table tr td.selected:hover.active,.datepicker table tr td.selected:hover.disabled,.datepicker table tr td.selected:hover:active,.datepicker table tr td.selected:hover:hover,.datepicker table tr td.selected:hover[disabled]{background-color:grey}.datepicker table tr td.active.disabled:hover,.datepicker table tr td.active:hover{background-color:#006dcc;background-image:-moz-linear-gradient(to bottom,#08c,#04c);background-image:-ms-linear-gradient(to bottom,#08c,#04c);background-image:-webkit-gradient(linear,0 0,0 100%,from(#08c),to(#04c));background-image:-webkit-linear-gradient(to bottom,#08c,#04c);background-image:-o-linear-gradient(to bottom,#08c,#04c);background-image:linear-gradient(to bottom,#08c,#04c);background-repeat:repeat-x;border-color:#04c #04c #002a80;border-color:rgba(0,0,0,.1) rgba(0,0,0,.1) rgba(0,0,0,.25);color:#fff;text-shadow:0-1px 0 rgba(0,0,0,.25)}.datepicker table tr td.active.disabled:active,.datepicker table tr td.active.disabled:hover,.datepicker table tr td.active.disabled:hover.active,.datepicker table tr td.active.disabled:hover.disabled,.datepicker table tr td.active.disabled:hover:active,.datepicker table tr td.active.disabled:hover:hover,.datepicker table tr td.active.disabled:hover[disabled],.datepicker table tr td.active:active,.datepicker table tr td.active:hover,.datepicker table tr td.active:hover.active,.datepicker table tr td.active:hover.disabled,.datepicker table tr td.active:hover:active,.datepicker table tr td.active:hover:hover,.datepicker table tr td.active:hover[disabled]{background-color:#04c}.datepicker table tr td span:hover{background:#eee}.datepicker table tr td span.disabled:hover{background:0 0;color:#999;cursor:default}.datepicker table tr td span.active.disabled:hover,.datepicker table tr td span.active:hover{background-color:#006dcc;background-image:-moz-linear-gradient(to bottom,#08c,#04c);background-image:-ms-linear-gradient(to bottom,#08c,#04c);background-image:-webkit-gradient(linear,0 0,0 100%,from(#08c),to(#04c));background-image:-webkit-linear-gradient(to bottom,#08c,#04c);background-image:-o-linear-gradient(to bottom,#08c,#04c);background-image:linear-gradient(to bottom,#08c,#04c);background-repeat:repeat-x;border-color:#04c #04c #002a80;border-color:rgba(0,0,0,.1) rgba(0,0,0,.1) rgba(0,0,0,.25);color:#fff;text-shadow:0-1px 0 rgba(0,0,0,.25)}.datepicker table tr td span.active.disabled:active,.datepicker table tr td span.active.disabled:hover,.datepicker table tr td span.active.disabled:hover.active,.datepicker table tr td span.active.disabled:hover.disabled,.datepicker table tr td span.active.disabled:hover:active,.datepicker table tr td span.active.disabled:hover:hover,.datepicker table tr td span.active.disabled:hover[disabled],.datepicker table tr td span.active:active,.datepicker table tr td span.active:hover,.datepicker table tr td span.active:hover.active,.datepicker table tr td span.active:hover.disabled,.datepicker table tr td span.active:hover:active,.datepicker table tr td span.active:hover:hover,.datepicker table tr td span.active:hover[disabled]{background-color:#04c}.datepicker .datepicker-switch:hover,.datepicker .next:hover,.datepicker .prev:hover,.datepicker tfoot tr th:hover{background:#eee}.daterangepicker:after,.daterangepicker:before{position:absolute;display:inline-block;border-bottom-color:rgba(0,0,0,.2);content:""}.daterangepicker:before{top:-7px;border-right:7px solid transparent;border-left:7px solid transparent;border-bottom:7px solid #ccc}.daterangepicker:after{top:-6px;border-right:6px solid transparent;border-bottom:6px solid #fff;border-left:6px solid transparent}.daterangepicker.opensleft:before{right:9px}.daterangepicker.opensleft:after{right:10px}.daterangepicker.openscenter:before{left:0;right:0;width:0;margin-left:auto;margin-right:auto}.daterangepicker.openscenter:after{left:0;right:0;width:0;margin-left:auto;margin-right:auto}.daterangepicker.opensright:before{left:9px}.daterangepicker.opensright:after{left:10px}.daterangepicker.drop-up:before{top:initial;bottom:-7px;border-bottom:initial;border-top:7px solid #ccc}.daterangepicker.drop-up:after{top:initial;bottom:-6px;border-bottom:initial;border-top:6px solid #fff}.daterangepicker td.available:hover,.daterangepicker th.available:hover{background-color:#eee;border-color:transparent;color:inherit}.daterangepicker td.active:hover{background-color:#357ebd;border-color:transparent;color:#fff}.daterangepicker .ranges li:hover{background-color:#eee}table.dataTable thead td:active,table.dataTable thead th:active{outline:0}table.dataTable.display tbody tr:hover,table.dataTable.hover tbody tr:hover{background-color:#f6f6f6}table.dataTable.display tbody tr:hover.selected,table.dataTable.hover tbody tr:hover.selected{background-color:#aab7d1}table.dataTable.display tbody tr:hover>.sorting_1,table.dataTable.order-column.hover tbody tr:hover>.sorting_1{background-color:#eaeaea}table.dataTable.display tbody tr:hover>.sorting_2,table.dataTable.order-column.hover tbody tr:hover>.sorting_2{background-color:#ececec}table.dataTable.display tbody tr:hover>.sorting_3,table.dataTable.order-column.hover tbody tr:hover>.sorting_3{background-color:#efefef}table.dataTable.display tbody tr:hover.selected>.sorting_1,table.dataTable.order-column.hover tbody tr:hover.selected>.sorting_1{background-color:#a2aec7}table.dataTable.display tbody tr:hover.selected>.sorting_2,table.dataTable.order-column.hover tbody tr:hover.selected>.sorting_2{background-color:#a3b0c9}table.dataTable.display tbody tr:hover.selected>.sorting_3,table.dataTable.order-column.hover tbody tr:hover.selected>.sorting_3{background-color:#a5b2cb}.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover{color:#333!important;border:1px solid #979797;background-color:#fff;background:-webkit-gradient(linear,left top,left bottom,color-stop(0,#fff),color-stop(100%,#dcdcdc));background:-webkit-linear-gradient(top,#fff 0,#dcdcdc 100%);background:-moz-linear-gradient(top,#fff 0,#dcdcdc 100%);background:-ms-linear-gradient(top,#fff 0,#dcdcdc 100%);background:-o-linear-gradient(top,#fff 0,#dcdcdc 100%);background:linear-gradient(to bottom,#fff 0,#dcdcdc 100%)}.dataTables_wrapper .dataTables_paginate .paginate_button.disabled:active,.dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover{cursor:default;color:#666!important;border:1px solid transparent;background:0 0;box-shadow:none}.dataTables_wrapper .dataTables_paginate .paginate_button:hover{color:#fff!important;border:1px solid #111;background-color:#585858;background:-webkit-gradient(linear,left top,left bottom,color-stop(0,#585858),color-stop(100%,#111));background:-webkit-linear-gradient(top,#585858 0,#111 100%);background:-moz-linear-gradient(top,#585858 0,#111 100%);background:-ms-linear-gradient(top,#585858 0,#111 100%);background:-o-linear-gradient(top,#585858 0,#111 100%);background:linear-gradient(to bottom,#585858 0,#111 100%)}.dataTables_wrapper .dataTables_paginate .paginate_button:active{outline:0;background-color:#2b2b2b;background:-webkit-gradient(linear,left top,left bottom,color-stop(0,#2b2b2b),color-stop(100%,#0c0c0c));background:-webkit-linear-gradient(top,#2b2b2b 0,#0c0c0c 100%);background:-moz-linear-gradient(top,#2b2b2b 0,#0c0c0c 100%);background:-ms-linear-gradient(top,#2b2b2b 0,#0c0c0c 100%);background:-o-linear-gradient(top,#2b2b2b 0,#0c0c0c 100%);background:linear-gradient(to bottom,#2b2b2b 0,#0c0c0c 100%);box-shadow:inset 0 0 3px #111}.dataTables_wrapper:after{visibility:hidden;display:block;content:"";clear:both;height:0}.fc .fc-button:not(:disabled){cursor:pointer}.fc :after,.fc :before{box-sizing:border-box}.fc a[data-navlink]:hover{text-decoration:underline}.fc .fc-button:hover,a.fc-event:hover{text-decoration:none}.fc-icon-chevron-left:before{content:""}.fc-icon-chevron-right:before{content:""}.fc-icon-chevrons-left:before{content:""}.fc-icon-chevrons-right:before{content:""}.fc-icon-minus-square:before{content:""}.fc-icon-plus-square:before{content:""}.fc-icon-x:before{content:""}.fc .fc-button::-moz-focus-inner{padding:0;border-style:none}.fc .fc-button:focus{outline:0;box-shadow:0 0 0 .2rem rgba(44,62,80,.25)}.fc .fc-button-primary:focus,.fc .fc-button-primary:not(:disabled).fc-button-active:focus,.fc .fc-button-primary:not(:disabled):active:focus{box-shadow:0 0 0 .2rem rgba(76,91,106,.5)}.fc .fc-button:disabled{opacity:.65}.fc .fc-button-primary:hover{color:#fff;color:var(--fc-button-text-color,#fff);background-color:#1e2b37;background-color:var(--fc-button-hover-bg-color,#1e2b37);border-color:#1a252f;border-color:var(--fc-button-hover-border-color,#1a252f)}.fc .fc-button-primary:disabled{color:#fff;color:var(--fc-button-text-color,#fff);background-color:#2c3e50;background-color:var(--fc-button-bg-color,#2c3e50);border-color:#2c3e50;border-color:var(--fc-button-border-color,#2c3e50)}.fc .fc-button-primary:not(:disabled).fc-button-active,.fc .fc-button-primary:not(:disabled):active{color:#fff;color:var(--fc-button-text-color,#fff);background-color:#1a252f;background-color:var(--fc-button-active-bg-color,#1a252f);border-color:#151e27;border-color:var(--fc-button-active-border-color,#151e27)}.fc .fc-button-group>.fc-button:active,.fc .fc-button-group>.fc-button:focus,.fc .fc-button-group>.fc-button:hover{z-index:1}.fc-event:hover .fc-event-resizer{display:block}.fc-event-selected .fc-event-resizer:before{content:"";position:absolute;top:-20px;left:-20px;right:-20px;bottom:-20px}.fc-event:focus{box-shadow:0 2px 5px rgba(0,0,0,.2)}.fc-event-selected:before,.fc-event:focus:before{content:"";position:absolute;z-index:3;top:0;left:0;right:0;bottom:0}.fc-event-selected:after,.fc-event:focus:after{content:"";background:rgba(0,0,0,.25);background:var(--fc-event-selected-overlay-color,rgba(0,0,0,.25));position:absolute;z-index:1;top:-1px;left:-1px;right:-1px;bottom:-1px}.fc-h-event.fc-event-selected:before{top:-10px;bottom:-10px}:root{--fc-daygrid-event-dot-width:8px;--fc-list-event-dot-width:10px;--fc-list-event-hover-bg-color:#f5f5f5}.fc-daygrid-day-events:after,.fc-daygrid-day-events:before,.fc-daygrid-day-frame:after,.fc-daygrid-day-frame:before,.fc-daygrid-event-harness:after,.fc-daygrid-event-harness:before{content:"";clear:both;display:table}.fc .fc-daygrid-day-bottom:before{content:"";clear:both;display:table}.fc-daygrid-dot-event:hover{background:rgba(0,0,0,.1)}.fc-daygrid-dot-event.fc-event-selected:before{top:-10px;bottom:-10px}.fc-v-event.fc-event-selected:before{left:-10px;right:-10px}.fc .fc-timegrid-slot:empty:before{content:" "}.fc-timegrid-event-short .fc-event-time:after{content:" - "}.fc .fc-list-day-cushion:after{content:"";clear:both;display:table}.fc .fc-list-event:hover td{background-color:#f5f5f5;background-color:var(--fc-list-event-hover-bg-color,#f5f5f5)}.fc .fc-list-event.fc-event-forced-url:hover a{text-decoration:underline}.iti--allow-dropdown .iti__flag-container:hover{cursor:pointer}.iti--allow-dropdown .iti__flag-container:hover .iti__selected-flag{background-color:rgba(0,0,0,.05)}.iti--allow-dropdown input[disabled]+.iti__flag-container:hover,.iti--allow-dropdown input[readonly]+.iti__flag-container:hover{cursor:default}.iti--allow-dropdown input[disabled]+.iti__flag-container:hover .iti__selected-flag,.iti--allow-dropdown input[readonly]+.iti__flag-container:hover .iti__selected-flag{background-color:transparent}.iti--container:hover{cursor:pointer}/*!
 * Quill Editor v1.3.7
 * https://quilljs.com/
 * Copyright (c) 2014, Jason Chen
 * Copyright (c) 2013, salesforce.com
 */.ql-container.ql-disabled .ql-editor ul[data-checked]>li::before{pointer-events:none}.ql-editor ul>li::before{content:"•"}.ql-editor ul[data-checked=false]>li::before,.ql-editor ul[data-checked=true]>li::before{color:#777;cursor:pointer;pointer-events:all}.ql-editor ul[data-checked=true]>li::before{content:"☑"}.ql-editor ul[data-checked=false]>li::before{content:"☐"}.ql-editor li::before{display:inline-block;white-space:nowrap;width:1.2em}.ql-editor li:not(.ql-direction-rtl)::before{margin-left:-1.5em;margin-right:.3em;text-align:right}.ql-editor li.ql-direction-rtl::before{margin-left:.3em;margin-right:-1.5em}.ql-editor ol li:before{content:counter(list-0,decimal)". "}.ql-editor ol li.ql-indent-1:before{content:counter(list-1,lower-alpha)". "}.ql-editor ol li.ql-indent-2:before{content:counter(list-2,lower-roman)". "}.ql-editor ol li.ql-indent-3:before{content:counter(list-3,decimal)". "}.ql-editor ol li.ql-indent-4:before{content:counter(list-4,lower-alpha)". "}.ql-editor ol li.ql-indent-5:before{content:counter(list-5,lower-roman)". "}.ql-editor ol li.ql-indent-6:before{content:counter(list-6,decimal)". "}.ql-editor ol li.ql-indent-7:before{content:counter(list-7,lower-alpha)". "}.ql-editor ol li.ql-indent-8:before{content:counter(list-8,lower-roman)". "}.ql-editor ol li.ql-indent-9:before{content:counter(list-9,decimal)". "}.ql-editor.ql-blank::before{color:rgba(0,0,0,.6);content:attr(data-placeholder);font-style:italic;left:15px;pointer-events:none;position:absolute;right:15px}.ql-snow .ql-toolbar:after,.ql-snow.ql-toolbar:after{clear:both;content:"";display:table}.ql-snow .ql-toolbar button:active:hover,.ql-snow.ql-toolbar button:active:hover{outline:0}.ql-snow .ql-toolbar .ql-picker-item:hover,.ql-snow .ql-toolbar .ql-picker-label:hover,.ql-snow .ql-toolbar button:focus,.ql-snow .ql-toolbar button:hover,.ql-snow.ql-toolbar .ql-picker-item:hover,.ql-snow.ql-toolbar .ql-picker-label:hover,.ql-snow.ql-toolbar button:focus,.ql-snow.ql-toolbar button:hover{color:#06c}.ql-snow .ql-toolbar .ql-picker-item:hover .ql-fill,.ql-snow .ql-toolbar .ql-picker-item:hover .ql-stroke.ql-fill,.ql-snow .ql-toolbar .ql-picker-label:hover .ql-fill,.ql-snow .ql-toolbar .ql-picker-label:hover .ql-stroke.ql-fill,.ql-snow .ql-toolbar button:focus .ql-fill,.ql-snow .ql-toolbar button:focus .ql-stroke.ql-fill,.ql-snow .ql-toolbar button:hover .ql-fill,.ql-snow .ql-toolbar button:hover .ql-stroke.ql-fill,.ql-snow.ql-toolbar .ql-picker-item:hover .ql-fill,.ql-snow.ql-toolbar .ql-picker-item:hover .ql-stroke.ql-fill,.ql-snow.ql-toolbar .ql-picker-label:hover .ql-fill,.ql-snow.ql-toolbar .ql-picker-label:hover .ql-stroke.ql-fill,.ql-snow.ql-toolbar button:focus .ql-fill,.ql-snow.ql-toolbar button:focus .ql-stroke.ql-fill,.ql-snow.ql-toolbar button:hover .ql-fill,.ql-snow.ql-toolbar button:hover .ql-stroke.ql-fill{fill:#06c}.ql-snow .ql-toolbar .ql-picker-item:hover .ql-stroke,.ql-snow .ql-toolbar .ql-picker-item:hover .ql-stroke-miter,.ql-snow .ql-toolbar .ql-picker-label:hover .ql-stroke,.ql-snow .ql-toolbar .ql-picker-label:hover .ql-stroke-miter,.ql-snow .ql-toolbar button:focus .ql-stroke,.ql-snow .ql-toolbar button:focus .ql-stroke-miter,.ql-snow .ql-toolbar button:hover .ql-stroke,.ql-snow .ql-toolbar button:hover .ql-stroke-miter,.ql-snow.ql-toolbar .ql-picker-item:hover .ql-stroke,.ql-snow.ql-toolbar .ql-picker-item:hover .ql-stroke-miter,.ql-snow.ql-toolbar .ql-picker-label:hover .ql-stroke,.ql-snow.ql-toolbar .ql-picker-label:hover .ql-stroke-miter,.ql-snow.ql-toolbar button:focus .ql-stroke,.ql-snow.ql-toolbar button:focus .ql-stroke-miter,.ql-snow.ql-toolbar button:hover .ql-stroke,.ql-snow.ql-toolbar button:hover .ql-stroke-miter{stroke:#06c}@media (pointer:coarse){.ql-snow .ql-toolbar button:hover:not(.ql-active),.ql-snow.ql-toolbar button:hover:not(.ql-active){color:#444}.ql-snow .ql-toolbar button:hover:not(.ql-active) .ql-fill,.ql-snow .ql-toolbar button:hover:not(.ql-active) .ql-stroke.ql-fill,.ql-snow.ql-toolbar button:hover:not(.ql-active) .ql-fill,.ql-snow.ql-toolbar button:hover:not(.ql-active) .ql-stroke.ql-fill{fill:#444}.ql-snow .ql-toolbar button:hover:not(.ql-active) .ql-stroke,.ql-snow .ql-toolbar button:hover:not(.ql-active) .ql-stroke-miter,.ql-snow.ql-toolbar button:hover:not(.ql-active) .ql-stroke,.ql-snow.ql-toolbar button:hover:not(.ql-active) .ql-stroke-miter{stroke:#444}}.ql-snow .ql-formats:after{clear:both;content:"";display:table}.ql-snow .ql-picker-label::before{display:inline-block;line-height:22px}.ql-snow .ql-picker.ql-font .ql-picker-item[data-label]:not([data-label=""])::before,.ql-snow .ql-picker.ql-font .ql-picker-label[data-label]:not([data-label=""])::before,.ql-snow .ql-picker.ql-header .ql-picker-item[data-label]:not([data-label=""])::before,.ql-snow .ql-picker.ql-header .ql-picker-label[data-label]:not([data-label=""])::before,.ql-snow .ql-picker.ql-size .ql-picker-item[data-label]:not([data-label=""])::before,.ql-snow .ql-picker.ql-size .ql-picker-label[data-label]:not([data-label=""])::before{content:attr(data-label)}.ql-snow .ql-picker.ql-header .ql-picker-item::before,.ql-snow .ql-picker.ql-header .ql-picker-label::before{content:"Normal"}.ql-snow .ql-picker.ql-header .ql-picker-item[data-value="1"]::before,.ql-snow .ql-picker.ql-header .ql-picker-label[data-value="1"]::before{content:"Heading 1"}.ql-snow .ql-picker.ql-header .ql-picker-item[data-value="2"]::before,.ql-snow .ql-picker.ql-header .ql-picker-label[data-value="2"]::before{content:"Heading 2"}.ql-snow .ql-picker.ql-header .ql-picker-item[data-value="3"]::before,.ql-snow .ql-picker.ql-header .ql-picker-label[data-value="3"]::before{content:"Heading 3"}.ql-snow .ql-picker.ql-header .ql-picker-item[data-value="4"]::before,.ql-snow .ql-picker.ql-header .ql-picker-label[data-value="4"]::before{content:"Heading 4"}.ql-snow .ql-picker.ql-header .ql-picker-item[data-value="5"]::before,.ql-snow .ql-picker.ql-header .ql-picker-label[data-value="5"]::before{content:"Heading 5"}.ql-snow .ql-picker.ql-header .ql-picker-item[data-value="6"]::before,.ql-snow .ql-picker.ql-header .ql-picker-label[data-value="6"]::before{content:"Heading 6"}.ql-snow .ql-picker.ql-header .ql-picker-item[data-value="1"]::before{font-size:2em}.ql-snow .ql-picker.ql-header .ql-picker-item[data-value="2"]::before{font-size:1.5em}.ql-snow .ql-picker.ql-header .ql-picker-item[data-value="3"]::before{font-size:1.17em}.ql-snow .ql-picker.ql-header .ql-picker-item[data-value="4"]::before{font-size:1em}.ql-snow .ql-picker.ql-header .ql-picker-item[data-value="5"]::before{font-size:.83em}.ql-snow .ql-picker.ql-header .ql-picker-item[data-value="6"]::before{font-size:.67em}.ql-snow .ql-picker.ql-font .ql-picker-item::before,.ql-snow .ql-picker.ql-font .ql-picker-label::before{content:"Sans Serif"}.ql-snow .ql-picker.ql-font .ql-picker-item[data-value=serif]::before,.ql-snow .ql-picker.ql-font .ql-picker-label[data-value=serif]::before{content:"Serif"}.ql-snow .ql-picker.ql-font .ql-picker-item[data-value=monospace]::before,.ql-snow .ql-picker.ql-font .ql-picker-label[data-value=monospace]::before{content:"Monospace"}.ql-snow .ql-picker.ql-font .ql-picker-item[data-value=serif]::before{font-family:Georgia,Times New Roman,serif}.ql-snow .ql-picker.ql-font .ql-picker-item[data-value=monospace]::before{font-family:Monaco,Courier New,monospace}.ql-snow .ql-picker.ql-size .ql-picker-item::before,.ql-snow .ql-picker.ql-size .ql-picker-label::before{content:"Normal"}.ql-snow .ql-picker.ql-size .ql-picker-item[data-value=small]::before,.ql-snow .ql-picker.ql-size .ql-picker-label[data-value=small]::before{content:"Small"}.ql-snow .ql-picker.ql-size .ql-picker-item[data-value=large]::before,.ql-snow .ql-picker.ql-size .ql-picker-label[data-value=large]::before{content:"Large"}.ql-snow .ql-picker.ql-size .ql-picker-item[data-value=huge]::before,.ql-snow .ql-picker.ql-size .ql-picker-label[data-value=huge]::before{content:"Huge"}.ql-snow .ql-picker.ql-size .ql-picker-item[data-value=small]::before{font-size:10px}.ql-snow .ql-picker.ql-size .ql-picker-item[data-value=large]::before{font-size:18px}.ql-snow .ql-picker.ql-size .ql-picker-item[data-value=huge]::before{font-size:32px}.ql-toolbar.ql-snow .ql-color-picker .ql-picker-item:hover{border-color:#000}.ql-snow .ql-tooltip::before{content:"Visit URL:";line-height:26px;margin-right:8px}.ql-snow .ql-tooltip a.ql-action::after{border-right:1px solid #ccc;content:"Edit";margin-left:16px;padding-right:8px}.ql-snow .ql-tooltip a.ql-remove::before{content:"Remove";margin-left:8px}.ql-snow .ql-tooltip.ql-editing a.ql-action::after{border-right:0px;content:"Save";padding-right:0}.ql-snow .ql-tooltip[data-mode=link]::before{content:"Enter link:"}.ql-snow .ql-tooltip[data-mode=formula]::before{content:"Enter formula:"}.ql-snow .ql-tooltip[data-mode=video]::before{content:"Enter video:"}/*!
 * Quill Editor v1.3.7
 * https://quilljs.com/
 * Copyright (c) 2014, Jason Chen
 * Copyright (c) 2013, salesforce.com
 */.ql-container.ql-disabled .ql-editor ul[data-checked]>li::before{pointer-events:none}.ql-editor ul>li::before{content:"•"}.ql-editor ul[data-checked=false]>li::before,.ql-editor ul[data-checked=true]>li::before{color:#777;cursor:pointer;pointer-events:all}.ql-editor ul[data-checked=true]>li::before{content:"☑"}.ql-editor ul[data-checked=false]>li::before{content:"☐"}.ql-editor li::before{display:inline-block;white-space:nowrap;width:1.2em}.ql-editor li:not(.ql-direction-rtl)::before{margin-left:-1.5em;margin-right:.3em;text-align:right}.ql-editor li.ql-direction-rtl::before{margin-left:.3em;margin-right:-1.5em}.ql-editor ol li:before{content:counter(list-0,decimal)". "}.ql-editor ol li.ql-indent-1:before{content:counter(list-1,lower-alpha)". "}.ql-editor ol li.ql-indent-2:before{content:counter(list-2,lower-roman)". "}.ql-editor ol li.ql-indent-3:before{content:counter(list-3,decimal)". "}.ql-editor ol li.ql-indent-4:before{content:counter(list-4,lower-alpha)". "}.ql-editor ol li.ql-indent-5:before{content:counter(list-5,lower-roman)". "}.ql-editor ol li.ql-indent-6:before{content:counter(list-6,decimal)". "}.ql-editor ol li.ql-indent-7:before{content:counter(list-7,lower-alpha)". "}.ql-editor ol li.ql-indent-8:before{content:counter(list-8,lower-roman)". "}.ql-editor ol li.ql-indent-9:before{content:counter(list-9,decimal)". "}.ql-editor.ql-blank::before{color:rgba(0,0,0,.6);content:attr(data-placeholder);font-style:italic;left:15px;pointer-events:none;position:absolute;right:15px}.ql-bubble .ql-toolbar:after,.ql-bubble.ql-toolbar:after{clear:both;content:"";display:table}.ql-bubble .ql-toolbar button:active:hover,.ql-bubble.ql-toolbar button:active:hover{outline:0}.ql-bubble .ql-toolbar .ql-picker-item:hover,.ql-bubble .ql-toolbar .ql-picker-label:hover,.ql-bubble .ql-toolbar button:focus,.ql-bubble .ql-toolbar button:hover,.ql-bubble.ql-toolbar .ql-picker-item:hover,.ql-bubble.ql-toolbar .ql-picker-label:hover,.ql-bubble.ql-toolbar button:focus,.ql-bubble.ql-toolbar button:hover{color:#fff}.ql-bubble .ql-toolbar .ql-picker-item:hover .ql-fill,.ql-bubble .ql-toolbar .ql-picker-item:hover .ql-stroke.ql-fill,.ql-bubble .ql-toolbar .ql-picker-label:hover .ql-fill,.ql-bubble .ql-toolbar .ql-picker-label:hover .ql-stroke.ql-fill,.ql-bubble .ql-toolbar button:focus .ql-fill,.ql-bubble .ql-toolbar button:focus .ql-stroke.ql-fill,.ql-bubble .ql-toolbar button:hover .ql-fill,.ql-bubble .ql-toolbar button:hover .ql-stroke.ql-fill,.ql-bubble.ql-toolbar .ql-picker-item:hover .ql-fill,.ql-bubble.ql-toolbar .ql-picker-item:hover .ql-stroke.ql-fill,.ql-bubble.ql-toolbar .ql-picker-label:hover .ql-fill,.ql-bubble.ql-toolbar .ql-picker-label:hover .ql-stroke.ql-fill,.ql-bubble.ql-toolbar button:focus .ql-fill,.ql-bubble.ql-toolbar button:focus .ql-stroke.ql-fill,.ql-bubble.ql-toolbar button:hover .ql-fill,.ql-bubble.ql-toolbar button:hover .ql-stroke.ql-fill{fill:#fff}.ql-bubble .ql-toolbar .ql-picker-item:hover .ql-stroke,.ql-bubble .ql-toolbar .ql-picker-item:hover .ql-stroke-miter,.ql-bubble .ql-toolbar .ql-picker-label:hover .ql-stroke,.ql-bubble .ql-toolbar .ql-picker-label:hover .ql-stroke-miter,.ql-bubble .ql-toolbar button:focus .ql-stroke,.ql-bubble .ql-toolbar button:focus .ql-stroke-miter,.ql-bubble .ql-toolbar button:hover .ql-stroke,.ql-bubble .ql-toolbar button:hover .ql-stroke-miter,.ql-bubble.ql-toolbar .ql-picker-item:hover .ql-stroke,.ql-bubble.ql-toolbar .ql-picker-item:hover .ql-stroke-miter,.ql-bubble.ql-toolbar .ql-picker-label:hover .ql-stroke,.ql-bubble.ql-toolbar .ql-picker-label:hover .ql-stroke-miter,.ql-bubble.ql-toolbar button:focus .ql-stroke,.ql-bubble.ql-toolbar button:focus .ql-stroke-miter,.ql-bubble.ql-toolbar button:hover .ql-stroke,.ql-bubble.ql-toolbar button:hover .ql-stroke-miter{stroke:#fff}@media (pointer:coarse){.ql-bubble .ql-toolbar button:hover:not(.ql-active),.ql-bubble.ql-toolbar button:hover:not(.ql-active){color:#ccc}.ql-bubble .ql-toolbar button:hover:not(.ql-active) .ql-fill,.ql-bubble .ql-toolbar button:hover:not(.ql-active) .ql-stroke.ql-fill,.ql-bubble.ql-toolbar button:hover:not(.ql-active) .ql-fill,.ql-bubble.ql-toolbar button:hover:not(.ql-active) .ql-stroke.ql-fill{fill:#ccc}.ql-bubble .ql-toolbar button:hover:not(.ql-active) .ql-stroke,.ql-bubble .ql-toolbar button:hover:not(.ql-active) .ql-stroke-miter,.ql-bubble.ql-toolbar button:hover:not(.ql-active) .ql-stroke,.ql-bubble.ql-toolbar button:hover:not(.ql-active) .ql-stroke-miter{stroke:#ccc}}.ql-bubble .ql-formats:after{clear:both;content:"";display:table}.ql-bubble .ql-picker-label::before{display:inline-block;line-height:22px}.ql-bubble .ql-picker.ql-font .ql-picker-item[data-label]:not([data-label=""])::before,.ql-bubble .ql-picker.ql-font .ql-picker-label[data-label]:not([data-label=""])::before,.ql-bubble .ql-picker.ql-header .ql-picker-item[data-label]:not([data-label=""])::before,.ql-bubble .ql-picker.ql-header .ql-picker-label[data-label]:not([data-label=""])::before,.ql-bubble .ql-picker.ql-size .ql-picker-item[data-label]:not([data-label=""])::before,.ql-bubble .ql-picker.ql-size .ql-picker-label[data-label]:not([data-label=""])::before{content:attr(data-label)}.ql-bubble .ql-picker.ql-header .ql-picker-item::before,.ql-bubble .ql-picker.ql-header .ql-picker-label::before{content:"Normal"}.ql-bubble .ql-picker.ql-header .ql-picker-item[data-value="1"]::before,.ql-bubble .ql-picker.ql-header .ql-picker-label[data-value="1"]::before{content:"Heading 1"}.ql-bubble .ql-picker.ql-header .ql-picker-item[data-value="2"]::before,.ql-bubble .ql-picker.ql-header .ql-picker-label[data-value="2"]::before{content:"Heading 2"}.ql-bubble .ql-picker.ql-header .ql-picker-item[data-value="3"]::before,.ql-bubble .ql-picker.ql-header .ql-picker-label[data-value="3"]::before{content:"Heading 3"}.ql-bubble .ql-picker.ql-header .ql-picker-item[data-value="4"]::before,.ql-bubble .ql-picker.ql-header .ql-picker-label[data-value="4"]::before{content:"Heading 4"}.ql-bubble .ql-picker.ql-header .ql-picker-item[data-value="5"]::before,.ql-bubble .ql-picker.ql-header .ql-picker-label[data-value="5"]::before{content:"Heading 5"}.ql-bubble .ql-picker.ql-header .ql-picker-item[data-value="6"]::before,.ql-bubble .ql-picker.ql-header .ql-picker-label[data-value="6"]::before{content:"Heading 6"}.ql-bubble .ql-picker.ql-header .ql-picker-item[data-value="1"]::before{font-size:2em}.ql-bubble .ql-picker.ql-header .ql-picker-item[data-value="2"]::before{font-size:1.5em}.ql-bubble .ql-picker.ql-header .ql-picker-item[data-value="3"]::before{font-size:1.17em}.ql-bubble .ql-picker.ql-header .ql-picker-item[data-value="4"]::before{font-size:1em}.ql-bubble .ql-picker.ql-header .ql-picker-item[data-value="5"]::before{font-size:.83em}.ql-bubble .ql-picker.ql-header .ql-picker-item[data-value="6"]::before{font-size:.67em}.ql-bubble .ql-picker.ql-font .ql-picker-item::before,.ql-bubble .ql-picker.ql-font .ql-picker-label::before{content:"Sans Serif"}.ql-bubble .ql-picker.ql-font .ql-picker-item[data-value=serif]::before,.ql-bubble .ql-picker.ql-font .ql-picker-label[data-value=serif]::before{content:"Serif"}.ql-bubble .ql-picker.ql-font .ql-picker-item[data-value=monospace]::before,.ql-bubble .ql-picker.ql-font .ql-picker-label[data-value=monospace]::before{content:"Monospace"}.ql-bubble .ql-picker.ql-font .ql-picker-item[data-value=serif]::before{font-family:Georgia,Times New Roman,serif}.ql-bubble .ql-picker.ql-font .ql-picker-item[data-value=monospace]::before{font-family:Monaco,Courier New,monospace}.ql-bubble .ql-picker.ql-size .ql-picker-item::before,.ql-bubble .ql-picker.ql-size .ql-picker-label::before{content:"Normal"}.ql-bubble .ql-picker.ql-size .ql-picker-item[data-value=small]::before,.ql-bubble .ql-picker.ql-size .ql-picker-label[data-value=small]::before{content:"Small"}.ql-bubble .ql-picker.ql-size .ql-picker-item[data-value=large]::before,.ql-bubble .ql-picker.ql-size .ql-picker-label[data-value=large]::before{content:"Large"}.ql-bubble .ql-picker.ql-size .ql-picker-item[data-value=huge]::before,.ql-bubble .ql-picker.ql-size .ql-picker-label[data-value=huge]::before{content:"Huge"}.ql-bubble .ql-picker.ql-size .ql-picker-item[data-value=small]::before{font-size:10px}.ql-bubble .ql-picker.ql-size .ql-picker-item[data-value=large]::before{font-size:18px}.ql-bubble .ql-picker.ql-size .ql-picker-item[data-value=huge]::before{font-size:32px}.ql-bubble .ql-color-picker .ql-picker-item:hover{border-color:#fff}.ql-bubble .ql-tooltip-editor a:before{color:#ccc;content:"×";font-size:16px;font-weight:700}.ql-container.ql-bubble:not(.ql-disabled) a::before{background-color:#444;border-radius:15px;top:-5px;font-size:12px;color:#fff;content:attr(href);font-weight:400;overflow:hidden;padding:5px 15px;text-decoration:none;z-index:1}.ql-container.ql-bubble:not(.ql-disabled) a::after{border-top:6px solid #444;border-left:6px solid transparent;border-right:6px solid transparent;top:0;content:" ";height:0;width:0}.ql-container.ql-bubble:not(.ql-disabled) a::after,.ql-container.ql-bubble:not(.ql-disabled) a::before{left:0;margin-left:50%;position:absolute;transform:translate(-50%,-100%);transition:visibility 0s ease .2s;visibility:hidden}.ql-container.ql-bubble:not(.ql-disabled) a:hover::after,.ql-container.ql-bubble:not(.ql-disabled) a:hover::before{visibility:visible}/*!
 * 
 * Super simple WYSIWYG editor v0.8.20
 * https://summernote.org
 *
 *
 * Copyright 2013- Alan Hong and contributors
 * Summernote may be freely distributed under the MIT license.
 *
 * Date: 2021-10-14T21:15Z
 *
 */[class*=" note-icon"]:before,[class^=note-icon]:before{display:inline-block;font-family:summernote;font-style:normal;font-size:inherit;text-decoration:inherit;text-rendering:auto;text-transform:none;vertical-align:middle;-moz-osx-font-smoothing:grayscale;-webkit-font-smoothing:antialiased;speak:none}.note-icon-align::before{content:""}.note-icon-align-center::before{content:""}.note-icon-align-indent::before{content:""}.note-icon-align-justify::before{content:""}.note-icon-align-left::before{content:""}.note-icon-align-outdent::before{content:""}.note-icon-align-right::before{content:""}.note-icon-arrow-circle-down::before{content:""}.note-icon-arrow-circle-left::before{content:""}.note-icon-arrow-circle-right::before{content:""}.note-icon-arrow-circle-up::before{content:""}.note-icon-arrows-alt::before{content:""}.note-icon-arrows-h::before{content:""}.note-icon-arrows-v::before{content:""}.note-icon-bold::before{content:""}.note-icon-caret::before{content:""}.note-icon-chain-broken::before{content:""}.note-icon-circle::before{content:""}.note-icon-close::before{content:""}.note-icon-code::before{content:""}.note-icon-col-after::before{content:""}.note-icon-col-before::before{content:""}.note-icon-col-remove::before{content:""}.note-icon-eraser::before{content:""}.note-icon-float-left::before{content:""}.note-icon-float-none::before{content:""}.note-icon-float-right::before{content:""}.note-icon-font::before{content:""}.note-icon-frame::before{content:""}.note-icon-italic::before{content:""}.note-icon-link::before{content:""}.note-icon-magic::before{content:""}.note-icon-menu-check::before{content:""}.note-icon-minus::before{content:""}.note-icon-orderedlist::before{content:""}.note-icon-pencil::before{content:""}.note-icon-picture::before{content:""}.note-icon-question::before{content:""}.note-icon-redo::before{content:""}.note-icon-rollback::before{content:""}.note-icon-row-above::before{content:""}.note-icon-row-below::before{content:""}.note-icon-row-remove::before{content:""}.note-icon-special-character::before{content:""}.note-icon-square::before{content:""}.note-icon-strikethrough::before{content:""}.note-icon-subscript::before{content:""}.note-icon-summernote::before{content:""}.note-icon-superscript::before{content:""}.note-icon-table::before{content:""}.note-icon-text-height::before{content:""}.note-icon-trash::before{content:""}.note-icon-underline::before{content:""}.note-icon-undo::before{content:""}.note-icon-unorderedlist::before{content:""}.note-icon-video::before{content:""}.note-btn:focus{color:#333;background-color:#ebebeb;border-color:#dae0e5}.note-btn:hover{color:#333;background-color:#ebebeb;border-color:#dae0e5}.note-btn.disabled:focus,.note-btn[disabled]:focus,fieldset[disabled] .note-btn:focus{background-color:#fff;border-color:#dae0e5}.note-btn:focus,.note-btn:hover{color:#333;text-decoration:none;border:1px solid #dae0e5;background-color:#ebebeb;outline:0;border-radius:1px}.note-btn:active{outline:0;background-image:none;color:#333;text-decoration:none;border:1px solid #dae0e5;background-color:#ebebeb;outline:0;border-radius:1px;box-shadow:inset 0 3px 5px rgba(0,0,0,.125)}.note-btn-primary:focus,.note-btn-primary:hover{color:#fff;text-decoration:none;border:1px solid #dae0e5;background-color:#fa6362;border-radius:1px}.close:hover{-webkit-opacity:1;-khtml-opacity:1;-moz-opacity:1;opacity:1}.note-dropdown-item:hover{background-color:#ebebeb}a.note-dropdown-item:hover{margin:5px 0;color:#000;text-decoration:none}.note-modal-footer a:focus,.note-modal-footer a:hover{color:#23527c;text-decoration:underline}.note-modal .note-nav-link:focus,.note-modal .note-nav-link:hover{color:#0056b3;text-decoration:none}.note-modal .note-nav-tabs .note-nav-link:focus,.note-modal .note-nav-tabs .note-nav-link:hover{border-color:#e9ecef #e9ecef #ddd}.note-modal .note-tab-content>.note-tab-pane:target~.note-tab-pane:last-child{display:none}.note-modal .note-tab-content>.note-tab-pane:target{display:block}.note-input::-webkit-input-placeholder{color:#eee}.note-input:-moz-placeholder{color:#eee}.note-input::-moz-placeholder{color:#eee}.note-input:-ms-input-placeholder{color:#eee}.note-popover.bottom .note-popover-arrow::after{top:1px;margin-left:-10px;content:" ";border-top-width:0;border-bottom-color:#fff}.note-popover.top .note-popover-arrow::after{bottom:1px;margin-left:-10px;content:" ";border-bottom-width:0;border-top-color:#fff}.note-popover.right .note-popover-arrow::after{left:1px;margin-top:-10px;content:" ";border-left-width:0;border-right-color:#fff}.note-popover.left .note-popover-arrow::after{right:1px;margin-top:-10px;content:" ";border-right-width:0;border-left-color:#fff}.note-popover-arrow::after{position:absolute;display:block;width:0;height:0;border-color:transparent;border-style:solid;content:" ";border-width:10px}.note-editor .note-toolbar .note-color .note-dropdown-menu .note-palette .note-color-reset:hover,.note-editor .note-toolbar .note-color .note-dropdown-menu .note-palette .note-color-select:hover,.note-popover .popover-content .note-color .note-dropdown-menu .note-palette .note-color-reset:hover,.note-popover .popover-content .note-color .note-dropdown-menu .note-palette .note-color-select:hover{background:#eee}.note-editor .note-toolbar .note-dropdown-menu.right::before,.note-popover .popover-content .note-dropdown-menu.right::before{right:9px;left:auto!important}.note-editor .note-toolbar .note-dropdown-menu.right::after,.note-popover .popover-content .note-dropdown-menu.right::after{right:10px;left:auto!important}.note-editor .note-toolbar .note-color-palette div .note-color-btn:hover,.note-popover .popover-content .note-color-palette div .note-color-btn:hover{transform:scale(1.2);transition:all .2s}@-moz-document url-prefix(){.note-modal .note-image-input{height:auto}}.note-hint-popover .popover-content .note-hint-group .note-hint-item:hover{display:block;clear:both;font-weight:400;line-height:1.4;color:#fff;white-space:nowrap;text-decoration:none;background-color:#428bca;outline:0;cursor:pointer}.note-editor .note-editing-area .note-editable a:focus,.note-editor .note-editing-area .note-editable a:hover{color:#23527c;text-decoration:underline;outline:0}.note-modal .note-modal-body .help-list-item:hover{background-color:#e0e0e0}@-moz-document url-prefix(){.note-modal .note-image-input{height:auto}}/*! Pickr 1.9.1 MIT | https://github.com/Simonwep/pickr */.pickr .pcr-button::before{position:absolute;content:"";top:0;left:0;width:100%;height:100%;background:url(data:image/svg+xml;utf8,\ <svg\ xmlns=\"http://www.w3.org/2000/svg\"\ viewBox=\"0\ 0\ 2\ 2\"><path\ fill=\"white\"\ d=\"M1,0H2V1H1V0ZM0,1H1V2H0V1Z\"\/><path\ fill=\"gray\"\ d=\"M0,0H1V1H0V0ZM1,1H2V2H1V1Z\"\/><\/svg>);background-size:.5em;border-radius:.15em;z-index:-1}.pickr .pcr-button::before{z-index:initial}.pickr .pcr-button::after{position:absolute;content:"";top:0;left:0;height:100%;width:100%;transition:background .3s;background:var(--pcr-color);border-radius:.15em}.pickr .pcr-button.clear::before{opacity:0}.pickr .pcr-button.clear:focus{box-shadow:0 0 0 1px rgba(255,255,255,.85),0 0 0 3px var(--pcr-color)}.pcr-app button:focus,.pcr-app input:focus,.pickr button:focus,.pickr input:focus{box-shadow:0 0 0 1px rgba(255,255,255,.85),0 0 0 3px var(--pcr-color)}.pcr-app .pcr-palette:focus,.pcr-app .pcr-slider:focus,.pickr .pcr-palette:focus,.pickr .pcr-slider:focus{box-shadow:0 0 0 1px rgba(255,255,255,.85),0 0 0 3px rgba(0,0,0,.25)}.pcr-app .pcr-swatches>button::before{position:absolute;content:"";top:0;left:0;width:100%;height:100%;background:url(data:image/svg+xml;utf8,\ <svg\ xmlns=\"http://www.w3.org/2000/svg\"\ viewBox=\"0\ 0\ 2\ 2\"><path\ fill=\"white\"\ d=\"M1,0H2V1H1V0ZM0,1H1V2H0V1Z\"\/><path\ fill=\"gray\"\ d=\"M0,0H1V1H0V0ZM1,1H2V2H1V1Z\"\/><\/svg>);background-size:6px;border-radius:.15em;z-index:-1}.pcr-app .pcr-swatches>button::after{content:"";position:absolute;top:0;left:0;width:100%;height:100%;background:var(--pcr-color);border:1px solid rgba(0,0,0,.05);border-radius:.15em;box-sizing:border-box}.pcr-app .pcr-swatches>button:hover{filter:brightness(1.05)}.pcr-app .pcr-interaction input:hover{filter:brightness(.975)}.pcr-app .pcr-interaction input:focus{box-shadow:0 0 0 1px rgba(255,255,255,.85),0 0 0 3px rgba(66,133,244,.75)}.pcr-app .pcr-interaction .pcr-result::-moz-selection{background:#4285f4;color:#fff}.pcr-app .pcr-interaction .pcr-result::selection{background:#4285f4;color:#fff}.pcr-app .pcr-interaction .pcr-cancel:hover,.pcr-app .pcr-interaction .pcr-clear:hover,.pcr-app .pcr-interaction .pcr-save:hover{filter:brightness(.925)}.pcr-app .pcr-interaction .pcr-cancel:focus,.pcr-app .pcr-interaction .pcr-clear:focus{box-shadow:0 0 0 1px rgba(255,255,255,.85),0 0 0 3px rgba(244,66,80,.75)}.pcr-app .pcr-selection .pcr-color-chooser:active,.pcr-app .pcr-selection .pcr-color-opacity:active,.pcr-app .pcr-selection .pcr-color-palette:active{cursor:grabbing;cursor:-webkit-grabbing}.pcr-app[data-theme=classic] .pcr-selection .pcr-color-preview::before{position:absolute;content:"";top:0;left:0;width:100%;height:100%;background:url(data:image/svg+xml;utf8,\ <svg\ xmlns=\"http://www.w3.org/2000/svg\"\ viewBox=\"0\ 0\ 2\ 2\"><path\ fill=\"white\"\ d=\"M1,0H2V1H1V0ZM0,1H1V2H0V1Z\"\/><path\ fill=\"gray\"\ d=\"M0,0H1V1H0V0ZM1,1H2V2H1V1Z\"\/><\/svg>);background-size:.5em;border-radius:.15em;z-index:-1}.pcr-app[data-theme=classic] .pcr-selection .pcr-color-palette .pcr-palette::before{position:absolute;content:"";top:0;left:0;width:100%;height:100%;background:url(data:image/svg+xml;utf8,\ <svg\ xmlns=\"http://www.w3.org/2000/svg\"\ viewBox=\"0\ 0\ 2\ 2\"><path\ fill=\"white\"\ d=\"M1,0H2V1H1V0ZM0,1H1V2H0V1Z\"\/><path\ fill=\"gray\"\ d=\"M0,0H1V1H0V0ZM1,1H2V2H1V1Z\"\/><\/svg>);background-size:.5em;border-radius:.15em;z-index:-1}/*! Pickr 1.9.1 MIT | https://github.com/Simonwep/pickr */.pickr .pcr-button::before{position:absolute;content:"";top:0;left:0;width:100%;height:100%;background:url(data:image/svg+xml;utf8,\ <svg\ xmlns=\"http://www.w3.org/2000/svg\"\ viewBox=\"0\ 0\ 2\ 2\"><path\ fill=\"white\"\ d=\"M1,0H2V1H1V0ZM0,1H1V2H0V1Z\"\/><path\ fill=\"gray\"\ d=\"M0,0H1V1H0V0ZM1,1H2V2H1V1Z\"\/><\/svg>);background-size:.5em;border-radius:.15em;z-index:-1}.pickr .pcr-button::before{z-index:initial}.pickr .pcr-button::after{position:absolute;content:"";top:0;left:0;height:100%;width:100%;transition:background .3s;background:var(--pcr-color);border-radius:.15em}.pickr .pcr-button.clear::before{opacity:0}.pickr .pcr-button.clear:focus{box-shadow:0 0 0 1px rgba(255,255,255,.85),0 0 0 3px var(--pcr-color)}.pcr-app button:focus,.pcr-app input:focus,.pickr button:focus,.pickr input:focus{box-shadow:0 0 0 1px rgba(255,255,255,.85),0 0 0 3px var(--pcr-color)}.pcr-app .pcr-palette:focus,.pcr-app .pcr-slider:focus,.pickr .pcr-palette:focus,.pickr .pcr-slider:focus{box-shadow:0 0 0 1px rgba(255,255,255,.85),0 0 0 3px rgba(0,0,0,.25)}.pcr-app .pcr-swatches>button::before{position:absolute;content:"";top:0;left:0;width:100%;height:100%;background:url(data:image/svg+xml;utf8,\ <svg\ xmlns=\"http://www.w3.org/2000/svg\"\ viewBox=\"0\ 0\ 2\ 2\"><path\ fill=\"white\"\ d=\"M1,0H2V1H1V0ZM0,1H1V2H0V1Z\"\/><path\ fill=\"gray\"\ d=\"M0,0H1V1H0V0ZM1,1H2V2H1V1Z\"\/><\/svg>);background-size:6px;border-radius:.15em;z-index:-1}.pcr-app .pcr-swatches>button::after{content:"";position:absolute;top:0;left:0;width:100%;height:100%;background:var(--pcr-color);border:1px solid rgba(0,0,0,.05);border-radius:.15em;box-sizing:border-box}.pcr-app .pcr-swatches>button:hover{filter:brightness(1.05)}.pcr-app .pcr-interaction input:hover{filter:brightness(.975)}.pcr-app .pcr-interaction input:focus{box-shadow:0 0 0 1px rgba(255,255,255,.85),0 0 0 3px rgba(66,133,244,.75)}.pcr-app .pcr-interaction .pcr-result::-moz-selection{background:#4285f4;color:#fff}.pcr-app .pcr-interaction .pcr-result::selection{background:#4285f4;color:#fff}.pcr-app .pcr-interaction .pcr-cancel:hover,.pcr-app .pcr-interaction .pcr-clear:hover,.pcr-app .pcr-interaction .pcr-save:hover{filter:brightness(.925)}.pcr-app .pcr-interaction .pcr-cancel:focus,.pcr-app .pcr-interaction .pcr-clear:focus{box-shadow:0 0 0 1px rgba(255,255,255,.85),0 0 0 3px rgba(244,66,80,.75)}.pcr-app .pcr-selection .pcr-color-chooser:active,.pcr-app .pcr-selection .pcr-color-opacity:active,.pcr-app .pcr-selection .pcr-color-palette:active{cursor:grabbing;cursor:-webkit-grabbing}.pcr-app[data-theme=monolith] .pcr-selection .pcr-color-preview::before{position:absolute;content:"";top:0;left:0;width:100%;height:100%;background:url(data:image/svg+xml;utf8,\ <svg\ xmlns=\"http://www.w3.org/2000/svg\"\ viewBox=\"0\ 0\ 2\ 2\"><path\ fill=\"white\"\ d=\"M1,0H2V1H1V0ZM0,1H1V2H0V1Z\"\/><path\ fill=\"gray\"\ d=\"M0,0H1V1H0V0ZM1,1H2V2H1V1Z\"\/><\/svg>);background-size:.5em;border-radius:.15em;z-index:-1}.pcr-app[data-theme=monolith] .pcr-selection .pcr-color-palette .pcr-palette::before{position:absolute;content:"";top:0;left:0;width:100%;height:100%;background:url(data:image/svg+xml;utf8,\ <svg\ xmlns=\"http://www.w3.org/2000/svg\"\ viewBox=\"0\ 0\ 2\ 2\"><path\ fill=\"white\"\ d=\"M1,0H2V1H1V0ZM0,1H1V2H0V1Z\"\/><path\ fill=\"gray\"\ d=\"M0,0H1V1H0V0ZM1,1H2V2H1V1Z\"\/><\/svg>);background-size:.5em;border-radius:.15em;z-index:-1}/*! Pickr 1.9.1 MIT | https://github.com/Simonwep/pickr */.pickr .pcr-button::before{position:absolute;content:"";top:0;left:0;width:100%;height:100%;background:url(data:image/svg+xml;utf8,\ <svg\ xmlns=\"http://www.w3.org/2000/svg\"\ viewBox=\"0\ 0\ 2\ 2\"><path\ fill=\"white\"\ d=\"M1,0H2V1H1V0ZM0,1H1V2H0V1Z\"\/><path\ fill=\"gray\"\ d=\"M0,0H1V1H0V0ZM1,1H2V2H1V1Z\"\/><\/svg>);background-size:.5em;border-radius:.15em;z-index:-1}.pickr .pcr-button::before{z-index:initial}.pickr .pcr-button::after{position:absolute;content:"";top:0;left:0;height:100%;width:100%;transition:background .3s;background:var(--pcr-color);border-radius:.15em}.pickr .pcr-button.clear::before{opacity:0}.pickr .pcr-button.clear:focus{box-shadow:0 0 0 1px rgba(255,255,255,.85),0 0 0 3px var(--pcr-color)}.pcr-app button:focus,.pcr-app input:focus,.pickr button:focus,.pickr input:focus{box-shadow:0 0 0 1px rgba(255,255,255,.85),0 0 0 3px var(--pcr-color)}.pcr-app .pcr-palette:focus,.pcr-app .pcr-slider:focus,.pickr .pcr-palette:focus,.pickr .pcr-slider:focus{box-shadow:0 0 0 1px rgba(255,255,255,.85),0 0 0 3px rgba(0,0,0,.25)}.pcr-app .pcr-swatches>button::before{position:absolute;content:"";top:0;left:0;width:100%;height:100%;background:url(data:image/svg+xml;utf8,\ <svg\ xmlns=\"http://www.w3.org/2000/svg\"\ viewBox=\"0\ 0\ 2\ 2\"><path\ fill=\"white\"\ d=\"M1,0H2V1H1V0ZM0,1H1V2H0V1Z\"\/><path\ fill=\"gray\"\ d=\"M0,0H1V1H0V0ZM1,1H2V2H1V1Z\"\/><\/svg>);background-size:6px;border-radius:.15em;z-index:-1}.pcr-app .pcr-swatches>button::after{content:"";position:absolute;top:0;left:0;width:100%;height:100%;background:var(--pcr-color);border:1px solid rgba(0,0,0,.05);border-radius:.15em;box-sizing:border-box}.pcr-app .pcr-swatches>button:hover{filter:brightness(1.05)}.pcr-app .pcr-interaction input:hover{filter:brightness(.975)}.pcr-app .pcr-interaction input:focus{box-shadow:0 0 0 1px rgba(255,255,255,.85),0 0 0 3px rgba(66,133,244,.75)}.pcr-app .pcr-interaction .pcr-result::-moz-selection{background:#4285f4;color:#fff}.pcr-app .pcr-interaction .pcr-result::selection{background:#4285f4;color:#fff}.pcr-app .pcr-interaction .pcr-cancel:hover,.pcr-app .pcr-interaction .pcr-clear:hover,.pcr-app .pcr-interaction .pcr-save:hover{filter:brightness(.925)}.pcr-app .pcr-interaction .pcr-cancel:focus,.pcr-app .pcr-interaction .pcr-clear:focus{box-shadow:0 0 0 1px rgba(255,255,255,.85),0 0 0 3px rgba(244,66,80,.75)}.pcr-app .pcr-selection .pcr-color-chooser:active,.pcr-app .pcr-selection .pcr-color-opacity:active,.pcr-app .pcr-selection .pcr-color-palette:active{cursor:grabbing;cursor:-webkit-grabbing}.pcr-app[data-theme=nano] .pcr-selection .pcr-color-preview .pcr-current-color::before{position:absolute;content:"";top:0;left:0;width:100%;height:100%;background:url(data:image/svg+xml;utf8,\ <svg\ xmlns=\"http://www.w3.org/2000/svg\"\ viewBox=\"0\ 0\ 2\ 2\"><path\ fill=\"white\"\ d=\"M1,0H2V1H1V0ZM0,1H1V2H0V1Z\"\/><path\ fill=\"gray\"\ d=\"M0,0H1V1H0V0ZM1,1H2V2H1V1Z\"\/><\/svg>);background-size:.5em;border-radius:.15em;z-index:-1}.pcr-app[data-theme=nano] .pcr-selection .pcr-color-palette .pcr-palette::before{position:absolute;content:"";top:0;left:0;width:100%;height:100%;background:url(data:image/svg+xml;utf8,\ <svg\ xmlns=\"http://www.w3.org/2000/svg\"\ viewBox=\"0\ 0\ 2\ 2\"><path\ fill=\"white\"\ d=\"M1,0H2V1H1V0ZM0,1H1V2H0V1Z\"\/><path\ fill=\"gray\"\ d=\"M0,0H1V1H0V0ZM1,1H2V2H1V1Z\"\/><\/svg>);background-size:.5em;border-radius:.15em;z-index:-1}
/*!
 * Select2 v4 Bootstrap 5 theme v1.3.0
*/.select2-container--bootstrap-5 :focus{outline:0}.select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__clear:hover,.select2-container--bootstrap-5 .select2-selection--single .select2-selection__clear:hover{background:transparent url(data:image/svg+xml;charset=utf-8,%3Csvg\ xmlns=\'http://www.w3.org/2000/svg\'\ viewBox=\'0\ 0\ 16\ 16\'\ fill=\'%23ADB5BD\'%3E%3Cpath\ d=\'M.293.293a1\ 1\ 0\ 0\ 1\ 1.414\ 0L8\ 6.586\ 14.293.293a1\ 1\ 0\ 1\ 1\ 1.414\ 1.414L9.414\ 8l6.293\ 6.293a1\ 1\ 0\ 0\ 1-1.414\ 1.414L8\ 9.414l-6.293\ 6.293a1\ 1\ 0\ 0\ 1-1.414-1.414L6.586\ 8\ .293\ 1.707a1\ 1\ 0\ 0\ 1\ 0-1.414z\'/%3E%3C/svg%3E)50%/.75rem auto no-repeat}.select2-container--bootstrap-5 .select2-dropdown .select2-search .select2-search__field:focus{border-color:#ced4da;box-shadow:none}.select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered .select2-selection__choice .select2-selection__choice__remove:hover{background:transparent url(data:image/svg+xml;charset=utf-8,%3Csvg\ xmlns=\'http://www.w3.org/2000/svg\'\ viewBox=\'0\ 0\ 16\ 16\'\ fill=\'%23ADB5BD\'%3E%3Cpath\ d=\'M.293.293a1\ 1\ 0\ 0\ 1\ 1.414\ 0L8\ 6.586\ 14.293.293a1\ 1\ 0\ 1\ 1\ 1.414\ 1.414L9.414\ 8l6.293\ 6.293a1\ 1\ 0\ 0\ 1-1.414\ 1.414L8\ 9.414l-6.293\ 6.293a1\ 1\ 0\ 0\ 1-1.414-1.414L6.586\ 8\ .293\ 1.707a1\ 1\ 0\ 0\ 1\ 0-1.414z\'/%3E%3C/svg%3E)50%/.75rem auto no-repeat}.was-validated select:valid+.select2-container--bootstrap-5 .select2-selection{border-color:#0ac074}.was-validated select:valid+.select2-container--bootstrap-5.select2-container--focus .select2-selection,.was-validated select:valid+.select2-container--bootstrap-5.select2-container--open .select2-selection{border-color:#0ac074;box-shadow:0 0 0 .25rem rgba(10,192,116,.25)}.was-validated select:valid+.select2-container--bootstrap-5.select2-container--open.select2-container--below .select2-selection{border-bottom:0 solid transparent}.was-validated select:valid+.select2-container--bootstrap-5.select2-container--open.select2-container--above .select2-selection{border-top:0 solid transparent;border-top-left-radius:0;border-top-right-radius:0}.was-validated select:invalid+.select2-container--bootstrap-5 .select2-selection{border-color:#f62947}.was-validated select:invalid+.select2-container--bootstrap-5.select2-container--focus .select2-selection,.was-validated select:invalid+.select2-container--bootstrap-5.select2-container--open .select2-selection{border-color:#f62947;box-shadow:0 0 0 .25rem rgba(246,41,71,.25)}.was-validated select:invalid+.select2-container--bootstrap-5.select2-container--open.select2-container--below .select2-selection{border-bottom:0 solid transparent}.was-validated select:invalid+.select2-container--bootstrap-5.select2-container--open.select2-container--above .select2-selection{border-top:0 solid transparent;border-top-left-radius:0;border-top-right-radius:0}.select2-container--bootstrap-5 .select2--small.select2-selection--multiple .select2-selection__clear:hover,.select2-container--bootstrap-5 .select2--small.select2-selection--single .select2-selection__clear:hover{background:transparent url(data:image/svg+xml;charset=utf-8,%3Csvg\ xmlns=\'http://www.w3.org/2000/svg\'\ viewBox=\'0\ 0\ 16\ 16\'\ fill=\'%23ADB5BD\'%3E%3Cpath\ d=\'M.293.293a1\ 1\ 0\ 0\ 1\ 1.414\ 0L8\ 6.586\ 14.293.293a1\ 1\ 0\ 1\ 1\ 1.414\ 1.414L9.414\ 8l6.293\ 6.293a1\ 1\ 0\ 0\ 1-1.414\ 1.414L8\ 9.414l-6.293\ 6.293a1\ 1\ 0\ 0\ 1-1.414-1.414L6.586\ 8\ .293\ 1.707a1\ 1\ 0\ 0\ 1\ 0-1.414z\'/%3E%3C/svg%3E)50%/.5rem auto no-repeat}.select2-container--bootstrap-5 .select2--small.select2-selection--multiple .select2-selection__rendered .select2-selection__choice .select2-selection__choice__remove:hover{background:transparent url(data:image/svg+xml;charset=utf-8,%3Csvg\ xmlns=\'http://www.w3.org/2000/svg\'\ viewBox=\'0\ 0\ 16\ 16\'\ fill=\'%23ADB5BD\'%3E%3Cpath\ d=\'M.293.293a1\ 1\ 0\ 0\ 1\ 1.414\ 0L8\ 6.586\ 14.293.293a1\ 1\ 0\ 1\ 1\ 1.414\ 1.414L9.414\ 8l6.293\ 6.293a1\ 1\ 0\ 0\ 1-1.414\ 1.414L8\ 9.414l-6.293\ 6.293a1\ 1\ 0\ 0\ 1-1.414-1.414L6.586\ 8\ .293\ 1.707a1\ 1\ 0\ 0\ 1\ 0-1.414z\'/%3E%3C/svg%3E)50%/.5rem auto no-repeat}.select2-container--bootstrap-5 .select2--large.select2-selection--multiple .select2-selection__clear:hover,.select2-container--bootstrap-5 .select2--large.select2-selection--single .select2-selection__clear:hover{background:transparent url(data:image/svg+xml;charset=utf-8,%3Csvg\ xmlns=\'http://www.w3.org/2000/svg\'\ viewBox=\'0\ 0\ 16\ 16\'\ fill=\'%23ADB5BD\'%3E%3Cpath\ d=\'M.293.293a1\ 1\ 0\ 0\ 1\ 1.414\ 0L8\ 6.586\ 14.293.293a1\ 1\ 0\ 1\ 1\ 1.414\ 1.414L9.414\ 8l6.293\ 6.293a1\ 1\ 0\ 0\ 1-1.414\ 1.414L8\ 9.414l-6.293\ 6.293a1\ 1\ 0\ 0\ 1-1.414-1.414L6.586\ 8\ .293\ 1.707a1\ 1\ 0\ 0\ 1\ 0-1.414z\'/%3E%3C/svg%3E)50%/1rem auto no-repeat}.select2-container--bootstrap-5 .select2--large.select2-selection--multiple .select2-selection__rendered .select2-selection__choice .select2-selection__choice__remove:hover{background:transparent url(data:image/svg+xml;charset=utf-8,%3Csvg\ xmlns=\'http://www.w3.org/2000/svg\'\ viewBox=\'0\ 0\ 16\ 16\'\ fill=\'%23ADB5BD\'%3E%3Cpath\ d=\'M.293.293a1\ 1\ 0\ 0\ 1\ 1.414\ 0L8\ 6.586\ 14.293.293a1\ 1\ 0\ 1\ 1\ 1.414\ 1.414L9.414\ 8l6.293\ 6.293a1\ 1\ 0\ 0\ 1-1.414\ 1.414L8\ 9.414l-6.293\ 6.293a1\ 1\ 0\ 0\ 1-1.414-1.414L6.586\ 8\ .293\ 1.707a1\ 1\ 0\ 0\ 1\ 0-1.414z\'/%3E%3C/svg%3E)50%/1rem auto no-repeat}.form-select-sm~.select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__clear:hover,.form-select-sm~.select2-container--bootstrap-5 .select2-selection--single .select2-selection__clear:hover{background:transparent url(data:image/svg+xml;charset=utf-8,%3Csvg\ xmlns=\'http://www.w3.org/2000/svg\'\ viewBox=\'0\ 0\ 16\ 16\'\ fill=\'%23ADB5BD\'%3E%3Cpath\ d=\'M.293.293a1\ 1\ 0\ 0\ 1\ 1.414\ 0L8\ 6.586\ 14.293.293a1\ 1\ 0\ 1\ 1\ 1.414\ 1.414L9.414\ 8l6.293\ 6.293a1\ 1\ 0\ 0\ 1-1.414\ 1.414L8\ 9.414l-6.293\ 6.293a1\ 1\ 0\ 0\ 1-1.414-1.414L6.586\ 8\ .293\ 1.707a1\ 1\ 0\ 0\ 1\ 0-1.414z\'/%3E%3C/svg%3E)50%/.5rem auto no-repeat}.form-select-sm~.select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered .select2-selection__choice .select2-selection__choice__remove:hover{background:transparent url(data:image/svg+xml;charset=utf-8,%3Csvg\ xmlns=\'http://www.w3.org/2000/svg\'\ viewBox=\'0\ 0\ 16\ 16\'\ fill=\'%23ADB5BD\'%3E%3Cpath\ d=\'M.293.293a1\ 1\ 0\ 0\ 1\ 1.414\ 0L8\ 6.586\ 14.293.293a1\ 1\ 0\ 1\ 1\ 1.414\ 1.414L9.414\ 8l6.293\ 6.293a1\ 1\ 0\ 0\ 1-1.414\ 1.414L8\ 9.414l-6.293\ 6.293a1\ 1\ 0\ 0\ 1-1.414-1.414L6.586\ 8\ .293\ 1.707a1\ 1\ 0\ 0\ 1\ 0-1.414z\'/%3E%3C/svg%3E)50%/.5rem auto no-repeat}.form-select-lg~.select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__clear:hover,.form-select-lg~.select2-container--bootstrap-5 .select2-selection--single .select2-selection__clear:hover{background:transparent url(data:image/svg+xml;charset=utf-8,%3Csvg\ xmlns=\'http://www.w3.org/2000/svg\'\ viewBox=\'0\ 0\ 16\ 16\'\ fill=\'%23ADB5BD\'%3E%3Cpath\ d=\'M.293.293a1\ 1\ 0\ 0\ 1\ 1.414\ 0L8\ 6.586\ 14.293.293a1\ 1\ 0\ 1\ 1\ 1.414\ 1.414L9.414\ 8l6.293\ 6.293a1\ 1\ 0\ 0\ 1-1.414\ 1.414L8\ 9.414l-6.293\ 6.293a1\ 1\ 0\ 0\ 1-1.414-1.414L6.586\ 8\ .293\ 1.707a1\ 1\ 0\ 0\ 1\ 0-1.414z\'/%3E%3C/svg%3E)50%/1rem auto no-repeat}.form-select-lg~.select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered .select2-selection__choice .select2-selection__choice__remove:hover{background:transparent url(data:image/svg+xml;charset=utf-8,%3Csvg\ xmlns=\'http://www.w3.org/2000/svg\'\ viewBox=\'0\ 0\ 16\ 16\'\ fill=\'%23ADB5BD\'%3E%3Cpath\ d=\'M.293.293a1\ 1\ 0\ 0\ 1\ 1.414\ 0L8\ 6.586\ 14.293.293a1\ 1\ 0\ 1\ 1\ 1.414\ 1.414L9.414\ 8l6.293\ 6.293a1\ 1\ 0\ 0\ 1-1.414\ 1.414L8\ 9.414l-6.293\ 6.293a1\ 1\ 0\ 0\ 1-1.414-1.414L6.586\ 8\ .293\ 1.707a1\ 1\ 0\ 0\ 1\ 0-1.414z\'/%3E%3C/svg%3E)50%/1rem auto no-repeat}#toast-container>div:hover{box-shadow:0 0 20px rgba(173,181,189,.1)}.flatpickr-calendar{background-color:#fff;border-color:#fff;box-shadow:0 5px 20px rgba(173,181,189,.2)!important;padding:1.25rem .875rem .875rem!important;width:320px!important}.flatpickr-calendar:after,.flatpickr-calendar:before{content:none}.flatpickr-calendar .flatpickr-months .flatpickr-monthDropdown-months,.flatpickr-calendar .flatpickr-months .numInputWrapper{color:#212529;font-size:1rem;font-weight:500}.flatpickr-calendar .flatpickr-months .flatpickr-month .flatpickr-current-month{align-items:center;display:flex;justify-content:center;padding-top:0}.flatpickr-calendar .flatpickr-months .flatpickr-month .flatpickr-current-month .flatpickr-monthDropdown-months:hover,.flatpickr-calendar .flatpickr-months .flatpickr-month .flatpickr-current-month .numInputWrapper:hover{background:0 0}.flatpickr-calendar .flatpickr-months .flatpickr-month .flatpickr-current-month .flatpickr-monthDropdown-months{padding-left:0}.flatpickr-calendar .flatpickr-months .flatpickr-month .flatpickr-current-month .numInputWrapper{padding-left:.313rem}.flatpickr-calendar .flatpickr-months .flatpickr-next-month,.flatpickr-calendar .flatpickr-months .flatpickr-prev-month{padding:0;top:24px}.flatpickr-calendar .flatpickr-months .flatpickr-next-month:hover svg,.flatpickr-calendar .flatpickr-months .flatpickr-prev-month:hover svg{fill:#6571ff}.flatpickr-calendar .flatpickr-months .flatpickr-prev-month{left:30px!important}.flatpickr-calendar .flatpickr-months .flatpickr-next-month{right:30px!important}.flatpickr-calendar .flatpickr-innerContainer{margin-top:.625rem}.flatpickr-calendar .flatpickr-innerContainer .flatpickr-rContainer .flatpickr-weekdays{margin-bottom:.75rem}.flatpickr-calendar .flatpickr-innerContainer .flatpickr-rContainer .flatpickr-weekdays .flatpickr-weekday{color:#212529;font-size:0;font-weight:500}.flatpickr-calendar .flatpickr-innerContainer .flatpickr-rContainer .flatpickr-weekdays .flatpickr-weekday:first-letter{font-size:.875rem}.flatpickr-calendar .flatpickr-innerContainer .flatpickr-rContainer .flatpickr-days{width:290px}.flatpickr-calendar .flatpickr-innerContainer .flatpickr-rContainer .flatpickr-days .dayContainer{max-width:290px;min-width:290px;width:290px}.flatpickr-calendar .flatpickr-innerContainer .flatpickr-rContainer .flatpickr-days .dayContainer .flatpickr-day{border-radius:.313rem;color:#495057;height:35px;line-height:35px;margin-bottom:.375rem;max-width:unset;width:35px}.flatpickr-calendar .flatpickr-innerContainer .flatpickr-rContainer .flatpickr-days .dayContainer .flatpickr-day:hover{background-color:#f8f9fa;border-color:#f8f9fa}.flatpickr-calendar .flatpickr-innerContainer .flatpickr-rContainer .flatpickr-days .dayContainer .flatpickr-day.today{background-color:#e0e3ff;border-color:#e0e3ff;color:#6571ff}.flatpickr-calendar .flatpickr-innerContainer .flatpickr-rContainer .flatpickr-days .dayContainer .flatpickr-day.nextMonthDay,.flatpickr-calendar .flatpickr-innerContainer .flatpickr-rContainer .flatpickr-days .dayContainer .flatpickr-day.prevMonthDay{color:#ced4da}.flatpickr-calendar .flatpickr-time .numInputWrapper:hover{background-color:transparent}.flatpickr-calendar .flatpickr-time .flatpickr-am-pm:focus,.flatpickr-calendar .flatpickr-time .flatpickr-am-pm:hover,.flatpickr-calendar .flatpickr-time .numInput:focus,.flatpickr-calendar .flatpickr-time .numInput:hover,.flatpickr-calendar .flatpickr-time span:focus,.flatpickr-calendar .flatpickr-time span:hover{background-color:transparent}.flatpickr-calendar .flatpickr-time .arrowUp:after{border-bottom-color:#495057}.flatpickr-calendar .flatpickr-time .arrowDown:after{border-top-color:#495057}.datepicker.datepicker-dropdown.dropdown-menu:after,.datepicker.datepicker-dropdown.dropdown-menu:before{content:none}.datepicker.datepicker-dropdown.dropdown-menu .table-condensed .day:hover,.datepicker.datepicker-dropdown.dropdown-menu .table-condensed .month:hover{background:#f8f9fa;color:#212529}.datepicker.datepicker-dropdown.dropdown-menu .table-condensed .datepicker-switch:hover{background:0 0}.datepicker.datepicker-dropdown.dropdown-menu .datepicker-days .table-condensed thead tr th.dow:first-letter{font-size:.875rem}.daterangepicker:after,.daterangepicker:before{content:none}.daterangepicker .drp-calendar .calendar-table .table-condensed thead tr:nth-child(2) th:first-letter{font-size:.875rem}.daterangepicker .drp-calendar .calendar-table tr .available:hover{background:#f8f9fa;color:#212529}.daterangepicker .ranges ul li:hover{background-color:#f8f9fa;color:#212529}.swal-modal .swal-icon--success:after,.swal-modal .swal-icon--success:before{background-color:#fff}.swal-modal .swal-icon--success:after{left:29px}.swal-modal .swal-icon--success:before{left:-32px}.swal-modal .swal-footer .swal-button:focus{box-shadow:none}.dataTables_wrapper div.dataTables_paginate .paginate_button.current:active,.dataTables_wrapper div.dataTables_paginate .paginate_button.current:focus,.dataTables_wrapper div.dataTables_paginate .paginate_button.current:hover{background:#6571ff;border-color:#6571ff;color:#fff!important}.dataTables_wrapper div.dataTables_paginate .paginate_button:active,.dataTables_wrapper div.dataTables_paginate .paginate_button:focus,.dataTables_wrapper div.dataTables_paginate .paginate_button:hover{background:#e9ecef;border-color:#e9ecef;box-shadow:none;color:#6571ff!important}.fc-media-screen .fc-header-toolbar .fc-toolbar-chunk .btn-primary:hover{background-color:#6571ff;border-color:#6571ff;color:#fff}.fc-media-screen .fc-header-toolbar .fc-toolbar-chunk .btn-primary span:before{font-weight:600!important}
*{box-sizing:border-box;margin:0;padding:0}.sticky-vcard-div{left:0;position:fixed}.modal .modal-dialog .modal-content .modal-body .required:after{color:#f62947;content:"*";font-size:inherit;font-weight:700;position:relative}.modal .modal-dialog .modal-content .modal-body .input-box:focus{background-color:#eef3f7;box-shadow:unset}.modal .modal-dialog .modal-content .modal-footer .submit-btn:hover{background-color:#6571ff!important}.modal .modal-dialog .modal-content .modal-footer .submit-btn:focus{box-shadow:unset}input::-moz-placeholder{overflow:visible}input::placeholder{overflow:visible}.lightbox{flex-direction:column-reverse}#wpNumber::-webkit-inner-spin-button{display:none}.input:focus{outline:none}.input::-webkit-input-placeholder{color:#aaa}.input:focus::-webkit-input-placeholder{color:#969696}.input:focus+.underline{transform:scale(1)}.vcard11-input:focus+.vcard11-underline{transform:scale(1)!important}.vcard11-input:focus{outline:none}.sub-btn{border-radius:20px;flex-direction:column;top:0}.object-fit-cover{-o-object-fit:cover;object-fit:cover}
.text-gradient{-webkit-text-fill-color:transparent;background:linear-gradient(90deg,#f6a932,#ff5a0e);-webkit-background-clip:text;display:inline-block}.text-gray-100{color:#a9afb8!important}.text-primary{color:#ff5a0e!important}.text-dark{color:#414141!important}.btn-primary{background-color:#ff5a0e!important;border:1px solid #ff5a0e!important;border-radius:10px 10px 0 10px!important;color:#fff!important;padding:7px 28px!important;transition:all .3s ease-in}.btn-primary:focus,.btn-primary:hover{background-color:#fff!important;color:#ff5a0e!important}body{background-color:#ffcdad!important;color:#414141;font-family:Poppins;font-weight:400!important;height:auto!important}body{padding-right:0!important}.fw-6{font-weight:600!important}.btn:focus{box-shadow:none!important;outline:none!important}.btn-gradient:focus,.btn-gradient:hover{background:linear-gradient(302deg,#f6895e,#f79b64,#f7ad68)}@media (max-width:575px){h2{font-size:25px}}.main-content{margin-bottom:4px;margin-top:4px;max-width:670px!important;position:relative}.main-content,.main-section{background-repeat:no-repeat;background-size:cover;border-radius:15px}.main-section{background-image:url(/images/templates/eventmanagementx/eve-018.webp);background-position:50%;margin:10px 0;overflow:hidden;transition:all .3s ease-in}.main-section:hover{transform:translateY(-5px)}.banner-section .banner-img{border-radius:15px;overflow:hidden;position:relative;width:100%}@media (max-width:767px){.banner-section .banner-img{height:280px}}.profile-section .card{background:transparent;border:1px solid #f57f21;border-radius:30px 30px 0 30px;padding:16px}@media (max-width:576px){.profile-section .card{gap:10px}}.profile-section .card .card-img{aspect-ratio:1;border:2px solid #ff5a0e;border-radius:20px 20px 0 20px;height:130px!important;overflow:hidden;width:130px!important}@media (max-width:576px){.profile-section .card .card-img{height:100px!important;width:100px!important}}.social-media{position:relative}.section-heading{margin:0 auto 40px;position:relative;text-align:center}.section-heading h2{display:inline-block;font-family:Merienda;font-size:28px;font-style:normal;font-weight:700!important;position:relative;z-index:1}@media (max-width:575px){.section-heading h2{font-size:25px}}.section-heading h2:before{background:#ff5a0e;border-radius:10px;bottom:-5px;content:"";height:3px;left:0;margin:auto;position:absolute;right:0;width:60px}.contact-section .contact-box{align-items:center;background:#ffcdad;border:1px solid #f55a0f;border-radius:15px 15px 0 15px;gap:12px;margin-top:25px;padding:10px;position:relative}.contact-section .contact-box .contact-icon{background:#fff;border:1px solid #ff5a0e;border-radius:12px 12px 0 12px;height:42px;left:0;margin:auto;min-width:42px;position:absolute;right:0;top:-24px;width:42px}.contact-section .contact-box .contact-icon img{height:20px;-o-object-fit:contain;object-fit:contain;width:20px}.contact-section .contact-box .contact-desc{margin-top:15px;text-align:center}.contact-section .contact-box .contact-desc p{color:#414141;font-size:16px}.contact-section .contact-box .contact-desc a{color:#414141;display:block;font-size:16px!important;word-break:break-word!important}.our-services-section .center-heading h2:before{display:none}.our-services-section .services{position:relative;z-index:2}.our-services-section .services .service-card{-webkit-backdrop-filter:blur(5px);backdrop-filter:blur(5px);background-color:#292d3380;border:1px solid #282c32!important;border-radius:20px 20px 0 20px;box-shadow:0 0 5px #0000001a;padding:15px}.our-services-section .services .service-card .card-img{background-color:#fff;border:1px solid #282c32;border-radius:12px;height:176px;overflow:hidden;width:100%}.our-services-section .services .service-card .card-img a{display:block;height:100%;width:100%}.our-services-section .services .service-card .card-img img{-o-object-fit:cover;object-fit:cover}.our-services-section .services .service-card .card-title{font-size:18px}.our-services-section .services .service-card .description-text{font-size:14px}.appointment-section .appointment-card{-webkit-backdrop-filter:blur(5px);backdrop-filter:blur(5px);background-color:#292d3380;border:1px solid #282c32!important;border-radius:20px 20px 0 20px}.appointment-section .appointment-card .appointment-input{background-color:#0e0802;border:1px solid #282c32;border-radius:10px;color:#fff;height:50px;padding:12px 20px;width:100%}.appointment-section .appointment-card .appointment-input::-moz-placeholder{color:#a9afb8}.appointment-section .appointment-card .appointment-input::placeholder{color:#a9afb8}.appointment-section .appointment-card .appointment-input:focus{outline:none}.appointment-section .appointment-card .appoint-input::-moz-placeholder{color:#a9afb8}.appointment-section .appointment-card .appoint-input::placeholder{color:#a9afb8}.appointment-section .appointment-card .appoint-input:focus{outline:none}.appointment-section .appointment-card .calendar-icon{position:absolute;right:18px;top:11px}.center-heading h2:after,.center-heading h2:before{background-color:rgba(40,44,50,.5);content:"";height:35px;position:absolute;top:0;width:200px}.center-heading h2:before{margin-right:40px;right:100%}@media (max-width:575px){.center-heading h2:before{margin-right:30px}}.center-heading h2:after{left:100%;margin-left:40px}@media (max-width:575px){.center-heading h2:after{margin-left:30px}.gallery-section{padding-left:10px;padding-right:10px}}.gallery-section .gallery-slider{position:relative}.gallery-section .gallery-slider .gallery-img{background-position:50%;background-size:cover;width:100%}.gallery-section .gallery-slider .slick-slide{padding:0 10px}.gallery-section .gallery-slider .gallery-img{aspect-ratio:2;-webkit-backdrop-filter:blur(5px);backdrop-filter:blur(5px);background-color:#292d3380;border:1px solid #ff5a0e!important;border-radius:20px 20px 0 20px;height:282px;margin:0 auto;max-height:282px;overflow:hidden;position:relative}.gallery-section .gallery-slider .gallery-img .expand-icon{align-items:center;background-color:#f55a0f;border-radius:50%;cursor:pointer;display:inline-flex;height:40px;justify-content:center;position:absolute;right:10px;top:10px;width:40px}@media (max-width:480px){.gallery-section .gallery-slider .gallery-img{height:240px}}.gallery-section .gallery-slider .gallery-img img{height:100%;-o-object-fit:contain;object-fit:contain}.product-section .product-slider .slick-slide{padding:0 10px}.product-section .product-slider .product-card{-webkit-backdrop-filter:blur(5px);backdrop-filter:blur(5px);background-color:#292d3380;border:1px solid #282c32!important;border-radius:20px 20px 0 20px;box-shadow:0 0 5px #0000001a;padding:15px}.product-section .product-slider .product-card .product-img{background-color:#fff;border:1px solid #282c32;border-radius:12px;height:176px;overflow:hidden;width:100%}.product-section .product-slider .product-card .product-img img{height:100%;-o-object-fit:cover;object-fit:cover;width:100%}.product-section .product-slider .product-card .product-title h3{-webkit-line-clamp:2;-webkit-box-orient:vertical;font-size:18px;font-weight:500;min-height:44px;overflow:hidden}.product-section .product-slider .product-card .product-amount{-webkit-line-clamp:1;-webkit-box-orient:vertical;color:#414141;font-size:22px;font-weight:600;line-height:1.25;overflow:hidden}.product-section .product-slider .product-card .product-desc{padding:10px 0 0}.testimonial-section .testimonial-slider .slick-slide{padding:15px 10px}.testimonial-section .testimonial-slider .testimonial-card{background-color:#ffcdad;border:1px solid #eebea7!important;border-radius:20px 20px 0 20px;box-shadow:0 0 5px #0000001a;font-size:14px;padding:20px 42px}.testimonial-section .testimonial-slider .testimonial-card .quote-img{position:absolute}.testimonial-section .testimonial-slider .testimonial-card .quote-img img{height:30px;width:auto}@media (max-width:575px){.testimonial-section .testimonial-slider .testimonial-card .quote-img img{height:20px}}.testimonial-section .testimonial-slider .testimonial-card .quote-img.quote-left-img{left:15px;top:-15px}.testimonial-section .testimonial-slider .testimonial-card .quote-img.quote-right-img{bottom:-15px;right:15px}.testimonial-section .testimonial-slider .testimonial-card .card-body .desc{-webkit-line-clamp:4;-webkit-box-orient:vertical;font-size:14px;min-height:84px;overflow:hidden;text-align:center}.testimonial-section .testimonial-slider .testimonial-card .card-body h6{-webkit-line-clamp:1;-webkit-box-orient:vertical;font-size:18px;overflow:hidden}.testimonial-section .testimonial-slider .testimonial-card .card-body .profile-img{border:2px solid #ff5a0e;border-radius:50%;height:80px;margin:auto;min-width:80px;overflow:hidden;width:80px}@media (max-width:575px){.blog-section{padding-left:10px;padding-right:10px}}.blog-section .blog-slider .blog-card{-webkit-backdrop-filter:blur(5px);backdrop-filter:blur(5px);background-color:#292d3380;border:1px solid #282c32!important;border-radius:20px 20px 0 20px;max-width:100%;padding:15px;width:100%}.blog-section .blog-slider .blog-card a{display:block;height:100%;width:100%}.blog-section .blog-slider .blog-card .card-img{border:1px solid #282c32!important;border-radius:15px;height:280px;overflow:hidden;width:100%}.blog-section .blog-slider .blog-card .card-img img{-o-object-fit:cover;object-fit:cover}.blog-section .blog-slider .blog-card .card-body{max-height:135px;min-height:135px;padding:15px 0 0}.blog-section .blog-slider .blog-card .card-body h5{-webkit-line-clamp:2;-webkit-box-orient:vertical;min-height:48px;overflow:hidden}.blog-section .blog-slider .blog-card .card-body .blog-desc{-webkit-line-clamp:2;-webkit-box-orient:vertical;color:#a9afb8!important;font-size:14px;line-height:1.2;margin-bottom:0!important;min-height:33px;overflow:hidden}.blog-section .slick-slide{padding:0 10px}.qr-code-section .qr-code{background-color:#ffcdad;border:1px solid #eebea7;border-radius:20px 20px 0 20px;max-width:550px;padding:20px;width:100%}.qr-code-section .qr-code .qr-code-img{align-items:center;background:#fff;border:2px solid #ff5a0e;border-radius:10px;display:flex;height:140px;justify-content:center;min-width:140px;padding:8px;width:140px}.qr-code-section .qr-code .qr-code-img svg{border-radius:6px}.bussiness-hour-section .bussiness-hour-card .business-box{background:#facdad;border:1px solid #eebea7;border-radius:15px 15px 0 15px;margin-top:20px;padding:10px;position:relative}.bussiness-hour-section .bussiness-hour-card .business-box .time-icons{align-items:center;background:#fff;border:1px solid #ff5a0e;border-radius:12px 12px 0 12px;display:flex;height:42px;justify-content:center;left:0;margin:auto;min-width:42px;position:absolute;right:0;top:-24px;width:42px}.contact-us-section .contact-form{-webkit-backdrop-filter:blur(5px);backdrop-filter:blur(5px);background-color:#292d3380;border:1px solid #282c32!important;border-radius:20px 20px 0 20px}.contact-us-section .contact-form form .form-control{background-color:#0e0802;border:1px solid #282c32;border-radius:15px 15px 0 15px;color:#fff;height:50px;margin-bottom:20px;padding:13px 16px}.contact-us-section .contact-form form .form-control::-moz-placeholder{color:#a9afb8}.contact-us-section .contact-form form .form-control::placeholder{color:#a9afb8}.contact-us-section .contact-form form .form-control:focus{box-shadow:none;outline:none}.btn-section .fixed-btn-section .event-bars-btn{background-color:#ff5a0e}.slick-dots{bottom:-35px!important}@media (max-width:575px){.slick-dots{bottom:-30px!important}}.slick-dots li{margin:0 3px}.slick-dots li,.slick-dots li button{height:10px;width:10px}.slick-dots li button:before{background-color:#dedede;border-radius:3px 3px 0 3px;font-size:0!important;height:10px;opacity:1!important;width:10px}.slick-dots li.slick-active button:before{background-color:#ff5a0e;opacity:1}.video-play-button:before{animation:pulse-border 1.5s ease-out infinite;height:60px;width:60px;z-index:0}.video-play-button:after,.video-play-button:before{background:#fff;border-radius:50%;content:"";display:block;left:50%;position:absolute;top:50%;transform:translateX(-50%) translateY(-50%)}.video-play-button:after{height:50px;transition:all .2s;width:50px;z-index:1}@keyframes pulse-border{0%{opacity:1;transform:translateX(-50%) translateY(-50%) translateZ(0) scale(1)}to{opacity:0;transform:translateX(-50%) translateY(-50%) translateZ(0) scale(1.5)}}.social-icons{gap:14px;position:relative}@media (max-width:576px){.social-icons{gap:10px}}.social-icons a{align-items:center;background-color:#ffcdad;border:1px solid #282c32;border-radius:12px 12px 0 12px;display:flex;height:50px;justify-content:center;min-width:50px;text-decoration:none;transition:all .3s ease-in-out;width:50px}.social-icons a:focus,.social-icons a:hover{background-color:#1b1a1a}.social-icons a:focus img,.social-icons a:focus svg,.social-icons a:hover img,.social-icons a:hover svg{color:#ff5a0e!important;transition:all .3s ease-in-out}.social-icons a:focus img path,.social-icons a:focus svg path,.social-icons a:hover img path,.social-icons a:hover svg path{fill:#ff5a0e!important}.social-icons a svg{height:24px;transition:all .3s ease-in-out;width:38px}.social-icons a svg path{fill:#ff5a0e}.vcard-fourteen-btn:hover{background-color:#fff!important;color:#ff5a0e!important}.language ul{list-style:none}.language ul .lang-list{background:#ff5a0e;border:none;border-radius:6px;font-size:14px;outline:none;padding:3px 9px;transition:all .3s ease;width:fit-content}.language ul .lang-list .lang-head{color:#fff}.language ul .lang-list .lang-hover-list{font-size:14px;margin:15px 0 0;min-width:70px;right:0;width:100%}.language ul .lang-list .lang-hover-list li:hover{background-color:#ffe5d8!important}.language ul .lang-list .lang-hover-list li:hover a{color:#ff5a0e!important}.add-contact-btn{border-radius:15px 15px 0 15px!important;color:#fff!important;transition:all .3s ease-in}.add-contact-btn svg{height:auto!important;width:20px}.add-contact-btn:hover{transform:scale(1.05);transition:all .3s ease-in}.product-btn:hover{background-color:rgba(255,90,14,.9)}.modal{background-color:#00000080;z-index:99999!important}.modal .news-modal #newsLatter-content .modal-body .required:after{color:#f62947;content:"*";font-size:inherit;font-weight:700;position:relative}.modal .news-modal #newsLatter-content .modal-body .input-box:focus{background-color:#eef3f7;box-shadow:unset}.modal .news-modal #newsLatter-content .modal-footer .submit-btn:hover{background-color:#6571ff!important}.modal .news-modal #newsLatter-content .modal-footer .submit-btn:focus{box-shadow:unset}@keyframes animatebottom{0%{bottom:-300px;opacity:0}to{bottom:0;opacity:1}}.insta-feed::-webkit-scrollbar{width:0}.instagram-btn:before{background-color:#ff5a0e;content:"";height:2px;position:absolute;top:100%;transition:width .3s ease;width:0}.instagram-btn.active:before{width:80%}.support-banner .support_text::-webkit-scrollbar{width:4px}.support-banner .support_text::-webkit-scrollbar-track{background:transparent}.support-banner .support_text::-webkit-scrollbar-thumb{background:#888}.act-now:hover{background-color:#fff!important;color:#ff5a0e!important}.verification-icon{color:#ff5a0e}.main-content.rtl .slick-dots li button:before{border-radius:3px 3px 3px 0!important}.input-box{background:#0f0a04;border:1px solid #282c32;border-radius:15px 15px 0 15px;color:#414141;cursor:pointer;display:grid;margin-bottom:5px;padding:16px;place-items:center}.input-box h4{color:#a9afb8!important;font-size:medium!important;margin:0}small{font-size:12px}.modal{padding-right:0!important}.product-img-slider .slick-dots li button:before{height:10px!important;padding:0!important;width:10px!important}.pwa-support{background:#0f0a04;border:1px solid #282c32;border-radius:20px 20px 0 20px;bottom:20px;height:auto!important;left:0;margin:0 auto;max-width:400px;padding:24px;position:fixed!important;right:0;width:100%;z-index:99999!important}.pwa-support .pwa-heading{font-size:20px;margin-bottom:12px}.pwa-support .pwa-text{font-size:.875rem!important;margin-bottom:16px}.pwa-install-button{background:#ff5a0e;border:1px solid #ff5a0e;border-radius:10px 10px 0 10px!important;color:#fff;font-size:.875rem!important;padding:.563rem 1.563rem!important}.pwa-install-button:hover{background-color:#fff!important;color:#ff5a0e!important}.pwa-cancel-button{background-color:#adb5bd!important;border:none!important;border-radius:10px 10px 0 10px!important;color:#414141;font-size:.875rem!important;padding:.563rem 1.563rem!important}.pwa-cancel-button:hover{background-color:#d1d5db!important;border:none!important;color:#414141!important}.youtube-link-14{padding-top:56.25%;position:relative}.youtube-link-14 iframe{height:100%!important;left:0!important;position:absolute!important;top:0;width:100%!important}.fs-24{font-size:24px}@media (max-width:576px){.fs-24{font-size:20px}}.fs-20{font-size:20px}@media (max-width:576px){.fs-20{font-size:18px}}.btn-section{position:absolute;top:50%;z-index:9}.btn-section .fixed-btn-section{align-items:center;cursor:move;display:flex;position:fixed;top:50%;transform:translateY(-50%);width:60px;z-index:10}.btn-section .fixed-btn-section .sub-btn{left:-65px!important;right:auto!important;width:auto!important}.sticky-vcard-div{bottom:60px!important;z-index:9!important}.bars-btn{box-shadow:none!important;position:relative!important}.row-gap-15px{row-gap:15px}.right-arrow-animation{animation:right-arrow 1s linear 1s infinite alternate}@keyframes right-arrow{0%{transform:translateX(0)}to{transform:translateX(8px)}}.profile-desc p{margin-bottom:0!important}.px-20{padding-left:20px;padding-right:20px}@media (max-width:575px){.px-20{padding-left:10px;padding-right:10px}}.px-30{padding-left:30px;padding-right:30px}@media (max-width:575px){.px-30{padding-left:20px;padding-right:20px}}.language-btn{z-index:10!important}@media (max-width:576px){.vector-all img{width:60%!important}}.vector-1{bottom:0;right:3px;text-align:end}.vector-1 img{transform:rotate(332deg)}.vector-11,.vector-13,.vector-3,.vector-5,.vector-7{right:15px;text-align:end;top:15px}@media (max-width:576px){.vector-11,.vector-13,.vector-3,.vector-5,.vector-7{right:10px;top:10px}}.vector-10,.vector-4,.vector-6,.vector-8{left:15px;top:15px}@media (max-width:576px){.vector-10,.vector-4,.vector-6,.vector-8{left:10px;top:10px}}.vector-main-1{right:0;top:0}.bg-vectors{height:100vh;overflow:hidden;position:fixed;width:100vw;z-index:-1}.bg-vectors .spark{position:absolute;transform-origin:0 0}.bg-vectors .fire{background:#eebea7;height:5px;left:-3px;position:absolute;width:5px}.bg-vectors .fire:before{background:#ff5a0e;content:"";height:100%;opacity:.5;position:absolute;transform:translateZ(.1px);width:100%}.bg-vectors .line:first-child{transform:rotateY(60deg)}.bg-vectors .line:first-child .spark{animation:spark1 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:232px;width:238px}.bg-vectors .line:first-child .fire{animation:fire 1335ms linear -27ms infinite}@keyframes spark1{0%{transform:translateY(0)}to{transform:rotate(96deg) translateX(849px)}}.bg-vectors .line:nth-child(2){transform:rotateY(325deg)}.bg-vectors .line:nth-child(2) .spark{animation:spark2 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:316px;width:347px}.bg-vectors .line:nth-child(2) .fire{animation:fire 1975ms linear -445ms infinite}@keyframes spark2{0%{transform:translateY(0)}to{transform:rotate(140deg) translateX(529px)}}.bg-vectors .line:nth-child(3){transform:rotateY(41deg)}.bg-vectors .line:nth-child(3) .spark{animation:spark3 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:395px;width:275px}.bg-vectors .line:nth-child(3) .fire{animation:fire 1812ms linear -58ms infinite}@keyframes spark3{0%{transform:translateY(0)}to{transform:rotate(223deg) translateX(961px)}}.bg-vectors .line:nth-child(4){transform:rotateY(196deg)}.bg-vectors .line:nth-child(4) .spark{animation:spark4 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:389px;width:357px}.bg-vectors .line:nth-child(4) .fire{animation:fire 1207ms linear -447ms infinite}@keyframes spark4{0%{transform:translateY(0)}to{transform:rotate(40deg) translateX(862px)}}.bg-vectors .line:nth-child(5){transform:rotateY(128deg)}.bg-vectors .line:nth-child(5) .spark{animation:spark5 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:278px;width:306px}.bg-vectors .line:nth-child(5) .fire{animation:fire 1788ms linear -438ms infinite}@keyframes spark5{0%{transform:translateY(0)}to{transform:rotate(159deg) translateX(523px)}}.bg-vectors .line:nth-child(6){transform:rotateY(223deg)}.bg-vectors .line:nth-child(6) .spark{animation:spark6 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:255px;width:340px}.bg-vectors .line:nth-child(6) .fire{animation:fire 1438ms linear -238ms infinite}@keyframes spark6{0%{transform:translateY(0)}to{transform:rotate(240deg) translateX(285px)}}.bg-vectors .line:nth-child(7){transform:rotateY(333deg)}.bg-vectors .line:nth-child(7) .spark{animation:spark7 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:346px;width:353px}.bg-vectors .line:nth-child(7) .fire{animation:fire 1115ms linear -.51s infinite}@keyframes spark7{0%{transform:translateY(0)}to{transform:rotate(352deg) translateX(1093px)}}.bg-vectors .line:nth-child(8){transform:rotateY(333deg)}.bg-vectors .line:nth-child(8) .spark{animation:spark8 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:262px;width:282px}.bg-vectors .line:nth-child(8) .fire{animation:fire 1177ms linear -137ms infinite}@keyframes spark8{0%{transform:translateY(0)}to{transform:rotate(92deg) translateX(287px)}}.bg-vectors .line:nth-child(9){transform:rotateY(329deg)}.bg-vectors .line:nth-child(9) .spark{animation:spark9 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:222px;width:369px}.bg-vectors .line:nth-child(9) .fire{animation:fire 1.04s linear -982ms infinite}@keyframes spark9{0%{transform:translateY(0)}to{transform:rotate(114deg) translateX(675px)}}.bg-vectors .line:nth-child(10){transform:rotateY(203deg)}.bg-vectors .line:nth-child(10) .spark{animation:spark10 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:320px;width:201px}.bg-vectors .line:nth-child(10) .fire{animation:fire 1498ms linear -592ms infinite}@keyframes spark10{0%{transform:translateY(0)}to{transform:rotate(264deg) translateX(668px)}}.bg-vectors .line:nth-child(11){transform:rotateY(208deg)}.bg-vectors .line:nth-child(11) .spark{animation:spark11 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:207px;width:346px}.bg-vectors .line:nth-child(11) .fire{animation:fire 1639ms linear -341ms infinite}@keyframes spark11{0%{transform:translateY(0)}to{transform:rotate(143deg) translateX(895px)}}.bg-vectors .line:nth-child(12){transform:rotateY(44deg)}.bg-vectors .line:nth-child(12) .spark{animation:spark12 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:234px;width:275px}.bg-vectors .line:nth-child(12) .fire{animation:fire 1933ms linear -35ms infinite}@keyframes spark12{0%{transform:translateY(0)}to{transform:rotate(329deg) translateX(898px)}}.bg-vectors .line:nth-child(13){transform:rotateY(185deg)}.bg-vectors .line:nth-child(13) .spark{animation:spark13 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:249px;width:296px}.bg-vectors .line:nth-child(13) .fire{animation:fire 1253ms linear -16ms infinite}@keyframes spark13{0%{transform:translateY(0)}to{transform:rotate(60deg) translateX(565px)}}.bg-vectors .line:nth-child(14){transform:rotateY(112deg)}.bg-vectors .line:nth-child(14) .spark{animation:spark14 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:345px;width:274px}.bg-vectors .line:nth-child(14) .fire{animation:fire 1006ms linear -689ms infinite}@keyframes spark14{0%{transform:translateY(0)}to{transform:rotate(79deg) translateX(597px)}}.bg-vectors .line:nth-child(15){transform:rotateY(63deg)}.bg-vectors .line:nth-child(15) .spark{animation:spark15 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:313px;width:288px}.bg-vectors .line:nth-child(15) .fire{animation:fire 1744ms linear -357ms infinite}@keyframes spark15{0%{transform:translateY(0)}to{transform:rotate(59deg) translateX(735px)}}.bg-vectors .line:nth-child(16){transform:rotateY(60deg)}.bg-vectors .line:nth-child(16) .spark{animation:spark16 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:387px;width:279px}.bg-vectors .line:nth-child(16) .fire{animation:fire 1801ms linear -487ms infinite}@keyframes spark16{0%{transform:translateY(0)}to{transform:rotate(282deg) translateX(711px)}}.bg-vectors .line:nth-child(17){transform:rotateY(308deg)}.bg-vectors .line:nth-child(17) .spark{animation:spark17 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:376px;width:345px}.bg-vectors .line:nth-child(17) .fire{animation:fire 1716ms linear -242ms infinite}@keyframes spark17{0%{transform:translateY(0)}to{transform:rotate(296deg) translateX(197px)}}.bg-vectors .line:nth-child(18){transform:rotateY(169deg)}.bg-vectors .line:nth-child(18) .spark{animation:spark18 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:389px;width:272px}.bg-vectors .line:nth-child(18) .fire{animation:fire 1581ms linear -208ms infinite}@keyframes spark18{0%{transform:translateY(0)}to{transform:rotate(183deg) translateX(421px)}}.bg-vectors .line:nth-child(19){transform:rotateY(338deg)}.bg-vectors .line:nth-child(19) .spark{animation:spark19 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:317px;width:262px}.bg-vectors .line:nth-child(19) .fire{animation:fire 1582ms linear -93ms infinite}@keyframes spark19{0%{transform:translateY(0)}to{transform:rotate(328deg) translateX(814px)}}.bg-vectors .line:nth-child(20){transform:rotateY(192deg)}.bg-vectors .line:nth-child(20) .spark{animation:spark20 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:268px;width:320px}.bg-vectors .line:nth-child(20) .fire{animation:fire 1929ms linear -886ms infinite}@keyframes spark20{0%{transform:translateY(0)}to{transform:rotate(7deg) translateX(397px)}}.bg-vectors .line:nth-child(21){transform:rotateY(138deg)}.bg-vectors .line:nth-child(21) .spark{animation:spark21 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:267px;width:239px}.bg-vectors .line:nth-child(21) .fire{animation:fire 1531ms linear -581ms infinite}@keyframes spark21{0%{transform:translateY(0)}to{transform:rotate(271deg) translateX(1053px)}}.bg-vectors .line:nth-child(22){transform:rotateY(56deg)}.bg-vectors .line:nth-child(22) .spark{animation:spark22 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:348px;width:365px}.bg-vectors .line:nth-child(22) .fire{animation:fire 1262ms linear -325ms infinite}@keyframes spark22{0%{transform:translateY(0)}to{transform:rotate(115deg) translateX(678px)}}.bg-vectors .line:nth-child(23){transform:rotateY(122deg)}.bg-vectors .line:nth-child(23) .spark{animation:spark23 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:292px;width:202px}.bg-vectors .line:nth-child(23) .fire{animation:fire 1514ms linear -571ms infinite}@keyframes spark23{0%{transform:translateY(0)}to{transform:rotate(89deg) translateX(823px)}}.bg-vectors .line:nth-child(24){transform:rotateY(75deg)}.bg-vectors .line:nth-child(24) .spark{animation:spark24 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:261px;width:299px}.bg-vectors .line:nth-child(24) .fire{animation:fire 1.02s linear -877ms infinite}@keyframes spark24{0%{transform:translateY(0)}to{transform:rotate(222deg) translateX(1031px)}}.bg-vectors .line:nth-child(25){transform:rotateY(333deg)}.bg-vectors .line:nth-child(25) .spark{animation:spark25 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:329px;width:218px}.bg-vectors .line:nth-child(25) .fire{animation:fire 1743ms linear -81ms infinite}@keyframes spark25{0%{transform:translateY(0)}to{transform:rotate(99deg) translateX(594px)}}.bg-vectors .line:nth-child(26){transform:rotateY(215deg)}.bg-vectors .line:nth-child(26) .spark{animation:spark26 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:393px;width:268px}.bg-vectors .line:nth-child(26) .fire{animation:fire 1404ms linear -427ms infinite}@keyframes spark26{0%{transform:translateY(0)}to{transform:rotate(266deg) translateX(246px)}}.bg-vectors .line:nth-child(27){transform:rotateY(354deg)}.bg-vectors .line:nth-child(27) .spark{animation:spark27 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:318px;width:297px}.bg-vectors .line:nth-child(27) .fire{animation:fire 1268ms linear -396ms infinite}@keyframes spark27{0%{transform:translateY(0)}to{transform:rotate(86deg) translateX(832px)}}.bg-vectors .line:nth-child(28){transform:rotateY(280deg)}.bg-vectors .line:nth-child(28) .spark{animation:spark28 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:220px;width:335px}.bg-vectors .line:nth-child(28) .fire{animation:fire 1852ms linear -539ms infinite}@keyframes spark28{0%{transform:translateY(0)}to{transform:rotate(319deg) translateX(148px)}}.bg-vectors .line:nth-child(29){transform:rotateY(345deg)}.bg-vectors .line:nth-child(29) .spark{animation:spark29 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:224px;width:275px}.bg-vectors .line:nth-child(29) .fire{animation:fire 1696ms linear -972ms infinite}@keyframes spark29{0%{transform:translateY(0)}to{transform:rotate(120deg) translateX(824px)}}.bg-vectors .line:nth-child(30){transform:rotateY(7deg)}.bg-vectors .line:nth-child(30) .spark{animation:spark30 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:364px;width:345px}.bg-vectors .line:nth-child(30) .fire{animation:fire 1535ms linear -633ms infinite}@keyframes spark30{0%{transform:translateY(0)}to{transform:rotate(220deg) translateX(122px)}}.bg-vectors .line:nth-child(31){transform:rotateY(31deg)}.bg-vectors .line:nth-child(31) .spark{animation:spark31 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:399px;width:379px}.bg-vectors .line:nth-child(31) .fire{animation:fire 1883ms linear -863ms infinite}@keyframes spark31{0%{transform:translateY(0)}to{transform:rotate(60deg) translateX(249px)}}.bg-vectors .line:nth-child(32){transform:rotateY(118deg)}.bg-vectors .line:nth-child(32) .spark{animation:spark32 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:344px;width:214px}.bg-vectors .line:nth-child(32) .fire{animation:fire 1564ms linear -277ms infinite}@keyframes spark32{0%{transform:translateY(0)}to{transform:rotate(275deg) translateX(538px)}}.bg-vectors .line:nth-child(33){transform:rotateY(200deg)}.bg-vectors .line:nth-child(33) .spark{animation:spark33 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:304px;width:369px}.bg-vectors .line:nth-child(33) .fire{animation:fire 1.76s linear -108ms infinite}@keyframes spark33{0%{transform:translateY(0)}to{transform:rotate(317deg) translateX(622px)}}.bg-vectors .line:nth-child(34){transform:rotateY(181deg)}.bg-vectors .line:nth-child(34) .spark{animation:spark34 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:248px;width:307px}.bg-vectors .line:nth-child(34) .fire{animation:fire 1064ms linear -952ms infinite}@keyframes spark34{0%{transform:translateY(0)}to{transform:rotate(278deg) translateX(572px)}}.bg-vectors .line:nth-child(35){transform:rotateY(131deg)}.bg-vectors .line:nth-child(35) .spark{animation:spark35 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:216px;width:368px}.bg-vectors .line:nth-child(35) .fire{animation:fire 1707ms linear -.91s infinite}@keyframes spark35{0%{transform:translateY(0)}to{transform:rotate(248deg) translateX(752px)}}.bg-vectors .line:nth-child(36){transform:rotateY(29deg)}.bg-vectors .line:nth-child(36) .spark{animation:spark36 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:230px;width:375px}.bg-vectors .line:nth-child(36) .fire{animation:fire 1027ms linear -.28s infinite}@keyframes spark36{0%{transform:translateY(0)}to{transform:rotate(150deg) translateX(886px)}}.bg-vectors .line:nth-child(37){transform:rotateY(75deg)}.bg-vectors .line:nth-child(37) .spark{animation:spark37 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:368px;width:309px}.bg-vectors .line:nth-child(37) .fire{animation:fire 1258ms linear -75ms infinite}@keyframes spark37{0%{transform:translateY(0)}to{transform:rotate(238deg) translateX(871px)}}.bg-vectors .line:nth-child(38){transform:rotateY(258deg)}.bg-vectors .line:nth-child(38) .spark{animation:spark38 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:310px;width:353px}.bg-vectors .line:nth-child(38) .fire{animation:fire 1377ms linear -391ms infinite}@keyframes spark38{0%{transform:translateY(0)}to{transform:rotate(257deg) translateX(349px)}}.bg-vectors .line:nth-child(39){transform:rotateY(51deg)}.bg-vectors .line:nth-child(39) .spark{animation:spark39 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:218px;width:341px}.bg-vectors .line:nth-child(39) .fire{animation:fire 1634ms linear -466ms infinite}@keyframes spark39{0%{transform:translateY(0)}to{transform:rotate(127deg) translateX(869px)}}.bg-vectors .line:nth-child(40){transform:rotateY(7deg)}.bg-vectors .line:nth-child(40) .spark{animation:spark40 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:382px;width:264px}.bg-vectors .line:nth-child(40) .fire{animation:fire 1433ms linear -379ms infinite}@keyframes spark40{0%{transform:translateY(0)}to{transform:rotate(11deg) translateX(366px)}}.bg-vectors .line:nth-child(41){transform:rotateY(257deg)}.bg-vectors .line:nth-child(41) .spark{animation:spark41 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:278px;width:351px}.bg-vectors .line:nth-child(41) .fire{animation:fire 1544ms linear -322ms infinite}@keyframes spark41{0%{transform:translateY(0)}to{transform:rotate(234deg) translateX(959px)}}.bg-vectors .line:nth-child(42){transform:rotateY(172deg)}.bg-vectors .line:nth-child(42) .spark{animation:spark42 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:275px;width:276px}.bg-vectors .line:nth-child(42) .fire{animation:fire 1511ms linear -765ms infinite}@keyframes spark42{0%{transform:translateY(0)}to{transform:rotate(255deg) translateX(216px)}}.bg-vectors .line:nth-child(43){transform:rotateY(344deg)}.bg-vectors .line:nth-child(43) .spark{animation:spark43 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:261px;width:284px}.bg-vectors .line:nth-child(43) .fire{animation:fire 1496ms linear -137ms infinite}@keyframes spark43{0%{transform:translateY(0)}to{transform:rotate(147deg) translateX(350px)}}.bg-vectors .line:nth-child(44){transform:rotateY(303deg)}.bg-vectors .line:nth-child(44) .spark{animation:spark44 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:324px;width:295px}.bg-vectors .line:nth-child(44) .fire{animation:fire 1605ms linear -896ms infinite}@keyframes spark44{0%{transform:translateY(0)}to{transform:rotate(121deg) translateX(773px)}}.bg-vectors .line:nth-child(45){transform:rotateY(27deg)}.bg-vectors .line:nth-child(45) .spark{animation:spark45 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:222px;width:235px}.bg-vectors .line:nth-child(45) .fire{animation:fire 1699ms linear -343ms infinite}@keyframes spark45{0%{transform:translateY(0)}to{transform:rotate(136deg) translateX(910px)}}.bg-vectors .line:nth-child(46){transform:rotateY(3deg)}.bg-vectors .line:nth-child(46) .spark{animation:spark46 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:294px;width:261px}.bg-vectors .line:nth-child(46) .fire{animation:fire 1.89s linear -421ms infinite}@keyframes spark46{0%{transform:translateY(0)}to{transform:rotate(193deg) translateX(255px)}}.bg-vectors .line:nth-child(47){transform:rotateY(188deg)}.bg-vectors .line:nth-child(47) .spark{animation:spark47 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:264px;width:324px}.bg-vectors .line:nth-child(47) .fire{animation:fire 1.2s linear -456ms infinite}@keyframes spark47{0%{transform:translateY(0)}to{transform:rotate(141deg) translateX(181px)}}.bg-vectors .line:nth-child(48){transform:rotateY(156deg)}.bg-vectors .line:nth-child(48) .spark{animation:spark48 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:249px;width:362px}.bg-vectors .line:nth-child(48) .fire{animation:fire 1095ms linear -63ms infinite}@keyframes spark48{0%{transform:translateY(0)}to{transform:rotate(149deg) translateX(380px)}}.bg-vectors .line:nth-child(49){transform:rotateY(158deg)}.bg-vectors .line:nth-child(49) .spark{animation:spark49 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:302px;width:337px}.bg-vectors .line:nth-child(49) .fire{animation:fire 1232ms linear -.41s infinite}@keyframes spark49{0%{transform:translateY(0)}to{transform:rotate(101deg) translateX(120px)}}.bg-vectors .line:nth-child(50){transform:rotateY(323deg)}.bg-vectors .line:nth-child(50) .spark{animation:spark50 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:220px;width:280px}.bg-vectors .line:nth-child(50) .fire{animation:fire 1921ms linear -.22s infinite}@keyframes spark50{0%{transform:translateY(0)}to{transform:rotate(181deg) translateX(520px)}}.bg-vectors .line:nth-child(51){transform:rotateY(118deg)}.bg-vectors .line:nth-child(51) .spark{animation:spark51 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:254px;width:370px}.bg-vectors .line:nth-child(51) .fire{animation:fire 1672ms linear -5ms infinite}@keyframes spark51{0%{transform:translateY(0)}to{transform:rotate(98deg) translateX(429px)}}.bg-vectors .line:nth-child(52){transform:rotateY(285deg)}.bg-vectors .line:nth-child(52) .spark{animation:spark52 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:295px;width:283px}.bg-vectors .line:nth-child(52) .fire{animation:fire 1476ms linear -64ms infinite}@keyframes spark52{0%{transform:translateY(0)}to{transform:rotate(165deg) translateX(1023px)}}.bg-vectors .line:nth-child(53){transform:rotateY(284deg)}.bg-vectors .line:nth-child(53) .spark{animation:spark53 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:320px;width:332px}.bg-vectors .line:nth-child(53) .fire{animation:fire 1035ms linear -228ms infinite}@keyframes spark53{0%{transform:translateY(0)}to{transform:rotate(231deg) translateX(508px)}}.bg-vectors .line:nth-child(54){transform:rotateY(358deg)}.bg-vectors .line:nth-child(54) .spark{animation:spark54 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:240px;width:371px}.bg-vectors .line:nth-child(54) .fire{animation:fire 1801ms linear -96ms infinite}@keyframes spark54{0%{transform:translateY(0)}to{transform:rotate(158deg) translateX(124px)}}.bg-vectors .line:nth-child(55){transform:rotateY(196deg)}.bg-vectors .line:nth-child(55) .spark{animation:spark55 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:283px;width:277px}.bg-vectors .line:nth-child(55) .fire{animation:fire 1469ms linear -273ms infinite}@keyframes spark55{0%{transform:translateY(0)}to{transform:rotate(346deg) translateX(117px)}}.bg-vectors .line:nth-child(56){transform:rotateY(314deg)}.bg-vectors .line:nth-child(56) .spark{animation:spark56 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:224px;width:316px}.bg-vectors .line:nth-child(56) .fire{animation:fire 1401ms linear -188ms infinite}@keyframes spark56{0%{transform:translateY(0)}to{transform:rotate(122deg) translateX(260px)}}.bg-vectors .line:nth-child(57){transform:rotateY(8deg)}.bg-vectors .line:nth-child(57) .spark{animation:spark57 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:371px;width:320px}.bg-vectors .line:nth-child(57) .fire{animation:fire 1961ms linear -70ms infinite}@keyframes spark57{0%{transform:translateY(0)}to{transform:rotate(271deg) translateX(980px)}}.bg-vectors .line:nth-child(58){transform:rotateY(351deg)}.bg-vectors .line:nth-child(58) .spark{animation:spark58 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:327px;width:387px}.bg-vectors .line:nth-child(58) .fire{animation:fire 1149ms linear -9ms infinite}@keyframes spark58{0%{transform:translateY(0)}to{transform:rotate(235deg) translateX(321px)}}.bg-vectors .line:nth-child(59){transform:rotateY(276deg)}.bg-vectors .line:nth-child(59) .spark{animation:spark59 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:270px;width:274px}.bg-vectors .line:nth-child(59) .fire{animation:fire 1959ms linear -.2s infinite}@keyframes spark59{0%{transform:translateY(0)}to{transform:rotate(3deg) translateX(509px)}}.bg-vectors .line:nth-child(60){transform:rotateY(136deg)}.bg-vectors .line:nth-child(60) .spark{animation:spark60 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:330px;width:396px}.bg-vectors .line:nth-child(60) .fire{animation:fire 1837ms linear -364ms infinite}@keyframes spark60{0%{transform:translateY(0)}to{transform:rotate(189deg) translateX(974px)}}.bg-vectors .line:nth-child(61){transform:rotateY(69deg)}.bg-vectors .line:nth-child(61) .spark{animation:spark61 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:316px;width:375px}.bg-vectors .line:nth-child(61) .fire{animation:fire 1086ms linear -774ms infinite}@keyframes spark61{0%{transform:translateY(0)}to{transform:rotate(125deg) translateX(342px)}}.bg-vectors .line:nth-child(62){transform:rotateY(86deg)}.bg-vectors .line:nth-child(62) .spark{animation:spark62 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:299px;width:311px}.bg-vectors .line:nth-child(62) .fire{animation:fire 1781ms linear -947ms infinite}@keyframes spark62{0%{transform:translateY(0)}to{transform:rotate(75deg) translateX(950px)}}.bg-vectors .line:nth-child(63){transform:rotateY(230deg)}.bg-vectors .line:nth-child(63) .spark{animation:spark63 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:395px;width:369px}.bg-vectors .line:nth-child(63) .fire{animation:fire 1103ms linear -641ms infinite}@keyframes spark63{0%{transform:translateY(0)}to{transform:rotate(328deg) translateX(755px)}}.bg-vectors .line:nth-child(64){transform:rotateY(327deg)}.bg-vectors .line:nth-child(64) .spark{animation:spark64 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:363px;width:313px}.bg-vectors .line:nth-child(64) .fire{animation:fire 1958ms linear -949ms infinite}@keyframes spark64{0%{transform:translateY(0)}to{transform:rotate(18deg) translateX(299px)}}.bg-vectors .line:nth-child(65){transform:rotateY(230deg)}.bg-vectors .line:nth-child(65) .spark{animation:spark65 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:385px;width:328px}.bg-vectors .line:nth-child(65) .fire{animation:fire 1262ms linear -277ms infinite}@keyframes spark65{0%{transform:translateY(0)}to{transform:rotate(114deg) translateX(940px)}}.bg-vectors .line:nth-child(66){transform:rotateY(121deg)}.bg-vectors .line:nth-child(66) .spark{animation:spark66 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:273px;width:265px}.bg-vectors .line:nth-child(66) .fire{animation:fire 1204ms linear -489ms infinite}@keyframes spark66{0%{transform:translateY(0)}to{transform:rotate(301deg) translateX(221px)}}.bg-vectors .line:nth-child(67){transform:rotateY(283deg)}.bg-vectors .line:nth-child(67) .spark{animation:spark67 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:317px;width:206px}.bg-vectors .line:nth-child(67) .fire{animation:fire 1492ms linear -788ms infinite}@keyframes spark67{0%{transform:translateY(0)}to{transform:rotate(186deg) translateX(504px)}}.bg-vectors .line:nth-child(68){transform:rotateY(357deg)}.bg-vectors .line:nth-child(68) .spark{animation:spark68 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:240px;width:232px}.bg-vectors .line:nth-child(68) .fire{animation:fire 1626ms linear -289ms infinite}@keyframes spark68{0%{transform:translateY(0)}to{transform:rotate(187deg) translateX(205px)}}.bg-vectors .line:nth-child(69){transform:rotateY(269deg)}.bg-vectors .line:nth-child(69) .spark{animation:spark69 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:241px;width:233px}.bg-vectors .line:nth-child(69) .fire{animation:fire 1996ms linear -417ms infinite}@keyframes spark69{0%{transform:translateY(0)}to{transform:rotate(261deg) translateX(112px)}}.bg-vectors .line:nth-child(70){transform:rotateY(129deg)}.bg-vectors .line:nth-child(70) .spark{animation:spark70 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:235px;width:356px}.bg-vectors .line:nth-child(70) .fire{animation:fire 1.27s linear -858ms infinite}@keyframes spark70{0%{transform:translateY(0)}to{transform:rotate(23deg) translateX(278px)}}.bg-vectors .line:nth-child(71){transform:rotateY(136deg)}.bg-vectors .line:nth-child(71) .spark{animation:spark71 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:346px;width:333px}.bg-vectors .line:nth-child(71) .fire{animation:fire 1267ms linear -627ms infinite}@keyframes spark71{0%{transform:translateY(0)}to{transform:rotate(305deg) translateX(1074px)}}.bg-vectors .line:nth-child(72){transform:rotateY(209deg)}.bg-vectors .line:nth-child(72) .spark{animation:spark72 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:206px;width:316px}.bg-vectors .line:nth-child(72) .fire{animation:fire 1139ms linear -829ms infinite}@keyframes spark72{0%{transform:translateY(0)}to{transform:rotate(241deg) translateX(832px)}}.bg-vectors .line:nth-child(73){transform:rotateY(98deg)}.bg-vectors .line:nth-child(73) .spark{animation:spark73 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:387px;width:218px}.bg-vectors .line:nth-child(73) .fire{animation:fire 1227ms linear -554ms infinite}@keyframes spark73{0%{transform:translateY(0)}to{transform:rotate(279deg) translateX(1081px)}}.bg-vectors .line:nth-child(74){transform:rotateY(229deg)}.bg-vectors .line:nth-child(74) .spark{animation:spark74 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:250px;width:220px}.bg-vectors .line:nth-child(74) .fire{animation:fire 1277ms linear -801ms infinite}@keyframes spark74{0%{transform:translateY(0)}to{transform:rotate(120deg) translateX(968px)}}.bg-vectors .line:nth-child(75){transform:rotateY(83deg)}.bg-vectors .line:nth-child(75) .spark{animation:spark75 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:310px;width:293px}.bg-vectors .line:nth-child(75) .fire{animation:fire 1.64s linear -671ms infinite}@keyframes spark75{0%{transform:translateY(0)}to{transform:rotate(55deg) translateX(853px)}}.bg-vectors .line:nth-child(76){transform:rotateY(323deg)}.bg-vectors .line:nth-child(76) .spark{animation:spark76 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:261px;width:330px}.bg-vectors .line:nth-child(76) .fire{animation:fire 1647ms linear -402ms infinite}@keyframes spark76{0%{transform:translateY(0)}to{transform:rotate(271deg) translateX(750px)}}.bg-vectors .line:nth-child(77){transform:rotateY(13deg)}.bg-vectors .line:nth-child(77) .spark{animation:spark77 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:313px;width:275px}.bg-vectors .line:nth-child(77) .fire{animation:fire 1908ms linear -326ms infinite}@keyframes spark77{0%{transform:translateY(0)}to{transform:rotate(57deg) translateX(291px)}}.bg-vectors .line:nth-child(78){transform:rotateY(105deg)}.bg-vectors .line:nth-child(78) .spark{animation:spark78 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:304px;width:264px}.bg-vectors .line:nth-child(78) .fire{animation:fire 1553ms linear -.16s infinite}@keyframes spark78{0%{transform:translateY(0)}to{transform:rotate(190deg) translateX(631px)}}.bg-vectors .line:nth-child(79){transform:rotateY(31deg)}.bg-vectors .line:nth-child(79) .spark{animation:spark79 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:298px;width:296px}.bg-vectors .line:nth-child(79) .fire{animation:fire 1045ms linear -542ms infinite}@keyframes spark79{0%{transform:translateY(0)}to{transform:rotate(9deg) translateX(728px)}}.bg-vectors .line:nth-child(80){transform:rotateY(77deg)}.bg-vectors .line:nth-child(80) .spark{animation:spark80 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:258px;width:310px}.bg-vectors .line:nth-child(80) .fire{animation:fire 1958ms linear -891ms infinite}@keyframes spark80{0%{transform:translateY(0)}to{transform:rotate(267deg) translateX(698px)}}.bg-vectors .line:nth-child(81){transform:rotateY(87deg)}.bg-vectors .line:nth-child(81) .spark{animation:spark81 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:296px;width:390px}.bg-vectors .line:nth-child(81) .fire{animation:fire 1048ms linear -1ms infinite}@keyframes spark81{0%{transform:translateY(0)}to{transform:rotate(175deg) translateX(324px)}}.bg-vectors .line:nth-child(82){transform:rotateY(147deg)}.bg-vectors .line:nth-child(82) .spark{animation:spark82 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:360px;width:249px}.bg-vectors .line:nth-child(82) .fire{animation:fire 1.62s linear -78ms infinite}@keyframes spark82{0%{transform:translateY(0)}to{transform:rotate(62deg) translateX(590px)}}.bg-vectors .line:nth-child(83){transform:rotateY(181deg)}.bg-vectors .line:nth-child(83) .spark{animation:spark83 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:281px;width:272px}.bg-vectors .line:nth-child(83) .fire{animation:fire 1828ms linear -804ms infinite}@keyframes spark83{0%{transform:translateY(0)}to{transform:rotate(312deg) translateX(854px)}}.bg-vectors .line:nth-child(84){transform:rotateY(64deg)}.bg-vectors .line:nth-child(84) .spark{animation:spark84 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:239px;width:226px}.bg-vectors .line:nth-child(84) .fire{animation:fire 1683ms linear -274ms infinite}@keyframes spark84{0%{transform:translateY(0)}to{transform:rotate(137deg) translateX(262px)}}.bg-vectors .line:nth-child(85){transform:rotateY(63deg)}.bg-vectors .line:nth-child(85) .spark{animation:spark85 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:327px;width:205px}.bg-vectors .line:nth-child(85) .fire{animation:fire 1369ms linear -509ms infinite}@keyframes spark85{0%{transform:translateY(0)}to{transform:rotate(34deg) translateX(519px)}}.bg-vectors .line:nth-child(86){transform:rotateY(20deg)}.bg-vectors .line:nth-child(86) .spark{animation:spark86 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:387px;width:237px}.bg-vectors .line:nth-child(86) .fire{animation:fire 1541ms linear -339ms infinite}@keyframes spark86{0%{transform:translateY(0)}to{transform:rotate(187deg) translateX(585px)}}.bg-vectors .line:nth-child(87){transform:rotateY(311deg)}.bg-vectors .line:nth-child(87) .spark{animation:spark87 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:203px;width:259px}.bg-vectors .line:nth-child(87) .fire{animation:fire 1327ms linear -213ms infinite}@keyframes spark87{0%{transform:translateY(0)}to{transform:rotate(49deg) translateX(1081px)}}.bg-vectors .line:nth-child(88){transform:rotateY(206deg)}.bg-vectors .line:nth-child(88) .spark{animation:spark88 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:370px;width:376px}.bg-vectors .line:nth-child(88) .fire{animation:fire 1897ms linear -506ms infinite}@keyframes spark88{0%{transform:translateY(0)}to{transform:rotate(325deg) translateX(511px)}}.bg-vectors .line:nth-child(89){transform:rotateY(198deg)}.bg-vectors .line:nth-child(89) .spark{animation:spark89 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:321px;width:333px}.bg-vectors .line:nth-child(89) .fire{animation:fire 1475ms linear -595ms infinite}@keyframes spark89{0%{transform:translateY(0)}to{transform:rotate(317deg) translateX(858px)}}.bg-vectors .line:nth-child(90){transform:rotateY(119deg)}.bg-vectors .line:nth-child(90) .spark{animation:spark90 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:346px;width:294px}.bg-vectors .line:nth-child(90) .fire{animation:fire 1853ms linear -225ms infinite}@keyframes spark90{0%{transform:translateY(0)}to{transform:rotate(13deg) translateX(787px)}}.bg-vectors .line:nth-child(91){transform:rotateY(116deg)}.bg-vectors .line:nth-child(91) .spark{animation:spark91 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:254px;width:223px}.bg-vectors .line:nth-child(91) .fire{animation:fire 1545ms linear -.24s infinite}@keyframes spark91{0%{transform:translateY(0)}to{transform:rotate(257deg) translateX(1022px)}}.bg-vectors .line:nth-child(92){transform:rotateY(262deg)}.bg-vectors .line:nth-child(92) .spark{animation:spark92 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:275px;width:337px}.bg-vectors .line:nth-child(92) .fire{animation:fire 1289ms linear -278ms infinite}@keyframes spark92{0%{transform:translateY(0)}to{transform:rotate(165deg) translateX(570px)}}.bg-vectors .line:nth-child(93){transform:rotateY(125deg)}.bg-vectors .line:nth-child(93) .spark{animation:spark93 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:374px;width:314px}.bg-vectors .line:nth-child(93) .fire{animation:fire 1152ms linear -599ms infinite}@keyframes spark93{0%{transform:translateY(0)}to{transform:rotate(54deg) translateX(824px)}}.bg-vectors .line:nth-child(94){transform:rotateY(185deg)}.bg-vectors .line:nth-child(94) .spark{animation:spark94 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:244px;width:310px}.bg-vectors .line:nth-child(94) .fire{animation:fire 1887ms linear -603ms infinite}@keyframes spark94{0%{transform:translateY(0)}to{transform:rotate(260deg) translateX(139px)}}.bg-vectors .line:nth-child(95){transform:rotateY(11deg)}.bg-vectors .line:nth-child(95) .spark{animation:spark95 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:254px;width:248px}.bg-vectors .line:nth-child(95) .fire{animation:fire 1903ms linear -68ms infinite}@keyframes spark95{0%{transform:translateY(0)}to{transform:rotate(163deg) translateX(148px)}}.bg-vectors .line:nth-child(96){transform:rotateY(109deg)}.bg-vectors .line:nth-child(96) .spark{animation:spark96 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:263px;width:355px}.bg-vectors .line:nth-child(96) .fire{animation:fire 1053ms linear -167ms infinite}@keyframes spark96{0%{transform:translateY(0)}to{transform:rotate(270deg) translateX(241px)}}.bg-vectors .line:nth-child(97){transform:rotateY(342deg)}.bg-vectors .line:nth-child(97) .spark{animation:spark97 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:252px;width:210px}.bg-vectors .line:nth-child(97) .fire{animation:fire 1763ms linear -237ms infinite}@keyframes spark97{0%{transform:translateY(0)}to{transform:rotate(193deg) translateX(999px)}}.bg-vectors .line:nth-child(98){transform:rotateY(259deg)}.bg-vectors .line:nth-child(98) .spark{animation:spark98 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:242px;width:342px}.bg-vectors .line:nth-child(98) .fire{animation:fire 1547ms linear -42ms infinite}@keyframes spark98{0%{transform:translateY(0)}to{transform:rotate(231deg) translateX(882px)}}.bg-vectors .line:nth-child(99){transform:rotateY(26deg)}.bg-vectors .line:nth-child(99) .spark{animation:spark99 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:277px;width:296px}.bg-vectors .line:nth-child(99) .fire{animation:fire 1036ms linear -613ms infinite}@keyframes spark99{0%{transform:translateY(0)}to{transform:rotate(105deg) translateX(880px)}}.bg-vectors .line:nth-child(100){transform:rotateY(52deg)}.bg-vectors .line:nth-child(100) .spark{animation:spark100 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:336px;width:387px}.bg-vectors .line:nth-child(100) .fire{animation:fire 1998ms linear -.27s infinite}@keyframes spark100{0%{transform:translateY(0)}to{transform:rotate(306deg) translateX(408px)}}.bg-vectors .line:nth-child(101){transform:rotateY(43deg)}.bg-vectors .line:nth-child(101) .spark{animation:spark101 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:381px;width:340px}.bg-vectors .line:nth-child(101) .fire{animation:fire 1782ms linear -686ms infinite}@keyframes spark101{0%{transform:translateY(0)}to{transform:rotate(355deg) translateX(132px)}}.bg-vectors .line:nth-child(102){transform:rotateY(214deg)}.bg-vectors .line:nth-child(102) .spark{animation:spark102 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:325px;width:318px}.bg-vectors .line:nth-child(102) .fire{animation:fire 1023ms linear -705ms infinite}@keyframes spark102{0%{transform:translateY(0)}to{transform:rotate(169deg) translateX(792px)}}.bg-vectors .line:nth-child(103){transform:rotateY(268deg)}.bg-vectors .line:nth-child(103) .spark{animation:spark103 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:282px;width:281px}.bg-vectors .line:nth-child(103) .fire{animation:fire 1184ms linear -363ms infinite}@keyframes spark103{0%{transform:translateY(0)}to{transform:rotate(166deg) translateX(714px)}}.bg-vectors .line:nth-child(104){transform:rotateY(282deg)}.bg-vectors .line:nth-child(104) .spark{animation:spark104 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:295px;width:296px}.bg-vectors .line:nth-child(104) .fire{animation:fire 1975ms linear -705ms infinite}@keyframes spark104{0%{transform:translateY(0)}to{transform:rotate(85deg) translateX(960px)}}.bg-vectors .line:nth-child(105){transform:rotateY(167deg)}.bg-vectors .line:nth-child(105) .spark{animation:spark105 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:275px;width:228px}.bg-vectors .line:nth-child(105) .fire{animation:fire 1243ms linear -454ms infinite}@keyframes spark105{0%{transform:translateY(0)}to{transform:rotate(50deg) translateX(505px)}}.bg-vectors .line:nth-child(106){transform:rotateY(346deg)}.bg-vectors .line:nth-child(106) .spark{animation:spark106 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:399px;width:299px}.bg-vectors .line:nth-child(106) .fire{animation:fire 1892ms linear -275ms infinite}@keyframes spark106{0%{transform:translateY(0)}to{transform:rotate(340deg) translateX(358px)}}.bg-vectors .line:nth-child(107){transform:rotateY(59deg)}.bg-vectors .line:nth-child(107) .spark{animation:spark107 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:395px;width:263px}.bg-vectors .line:nth-child(107) .fire{animation:fire 1386ms linear -355ms infinite}@keyframes spark107{0%{transform:translateY(0)}to{transform:rotate(160deg) translateX(124px)}}.bg-vectors .line:nth-child(108){transform:rotateY(236deg)}.bg-vectors .line:nth-child(108) .spark{animation:spark108 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:220px;width:352px}.bg-vectors .line:nth-child(108) .fire{animation:fire 1778ms linear -553ms infinite}@keyframes spark108{0%{transform:translateY(0)}to{transform:rotate(319deg) translateX(368px)}}.bg-vectors .line:nth-child(109){transform:rotateY(271deg)}.bg-vectors .line:nth-child(109) .spark{animation:spark109 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:384px;width:323px}.bg-vectors .line:nth-child(109) .fire{animation:fire 1077ms linear -501ms infinite}@keyframes spark109{0%{transform:translateY(0)}to{transform:rotate(356deg) translateX(846px)}}.bg-vectors .line:nth-child(110){transform:rotateY(173deg)}.bg-vectors .line:nth-child(110) .spark{animation:spark110 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:233px;width:225px}.bg-vectors .line:nth-child(110) .fire{animation:fire 1233ms linear -927ms infinite}@keyframes spark110{0%{transform:translateY(0)}to{transform:rotate(110deg) translateX(505px)}}.bg-vectors .line:nth-child(111){transform:rotateY(130deg)}.bg-vectors .line:nth-child(111) .spark{animation:spark111 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:292px;width:230px}.bg-vectors .line:nth-child(111) .fire{animation:fire 1762ms linear -769ms infinite}@keyframes spark111{0%{transform:translateY(0)}to{transform:rotate(326deg) translateX(443px)}}.bg-vectors .line:nth-child(112){transform:rotateY(61deg)}.bg-vectors .line:nth-child(112) .spark{animation:spark112 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:374px;width:203px}.bg-vectors .line:nth-child(112) .fire{animation:fire 1.61s linear -447ms infinite}@keyframes spark112{0%{transform:translateY(0)}to{transform:rotate(23deg) translateX(401px)}}.bg-vectors .line:nth-child(113){transform:rotateY(165deg)}.bg-vectors .line:nth-child(113) .spark{animation:spark113 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:326px;width:264px}.bg-vectors .line:nth-child(113) .fire{animation:fire 1589ms linear -746ms infinite}@keyframes spark113{0%{transform:translateY(0)}to{transform:rotate(130deg) translateX(434px)}}.bg-vectors .line:nth-child(114){transform:rotateY(93deg)}.bg-vectors .line:nth-child(114) .spark{animation:spark114 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:270px;width:365px}.bg-vectors .line:nth-child(114) .fire{animation:fire 1784ms linear -674ms infinite}@keyframes spark114{0%{transform:translateY(0)}to{transform:rotate(243deg) translateX(222px)}}.bg-vectors .line:nth-child(115){transform:rotateY(229deg)}.bg-vectors .line:nth-child(115) .spark{animation:spark115 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:319px;width:384px}.bg-vectors .line:nth-child(115) .fire{animation:fire 1378ms linear -655ms infinite}@keyframes spark115{0%{transform:translateY(0)}to{transform:rotate(129deg) translateX(649px)}}.bg-vectors .line:nth-child(116){transform:rotateY(283deg)}.bg-vectors .line:nth-child(116) .spark{animation:spark116 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:239px;width:269px}.bg-vectors .line:nth-child(116) .fire{animation:fire 1848ms linear -529ms infinite}@keyframes spark116{0%{transform:translateY(0)}to{transform:rotate(201deg) translateX(470px)}}.bg-vectors .line:nth-child(117){transform:rotateY(262deg)}.bg-vectors .line:nth-child(117) .spark{animation:spark117 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:269px;width:379px}.bg-vectors .line:nth-child(117) .fire{animation:fire 1322ms linear -543ms infinite}@keyframes spark117{0%{transform:translateY(0)}to{transform:rotate(92deg) translateX(528px)}}.bg-vectors .line:nth-child(118){transform:rotateY(73deg)}.bg-vectors .line:nth-child(118) .spark{animation:spark118 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:363px;width:286px}.bg-vectors .line:nth-child(118) .fire{animation:fire 1.43s linear -443ms infinite}@keyframes spark118{0%{transform:translateY(0)}to{transform:rotate(299deg) translateX(389px)}}.bg-vectors .line:nth-child(119){transform:rotateY(215deg)}.bg-vectors .line:nth-child(119) .spark{animation:spark119 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:206px;width:298px}.bg-vectors .line:nth-child(119) .fire{animation:fire 1452ms linear -681ms infinite}@keyframes spark119{0%{transform:translateY(0)}to{transform:rotate(330deg) translateX(985px)}}.bg-vectors .line:nth-child(120){transform:rotateY(97deg)}.bg-vectors .line:nth-child(120) .spark{animation:spark120 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:288px;width:363px}.bg-vectors .line:nth-child(120) .fire{animation:fire 1488ms linear -241ms infinite}@keyframes spark120{0%{transform:translateY(0)}to{transform:rotate(353deg) translateX(945px)}}.bg-vectors .line:nth-child(121){transform:rotateY(168deg)}.bg-vectors .line:nth-child(121) .spark{animation:spark121 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:219px;width:361px}.bg-vectors .line:nth-child(121) .fire{animation:fire 1353ms linear -309ms infinite}@keyframes spark121{0%{transform:translateY(0)}to{transform:rotate(17deg) translateX(124px)}}.bg-vectors .line:nth-child(122){transform:rotateY(83deg)}.bg-vectors .line:nth-child(122) .spark{animation:spark122 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:376px;width:212px}.bg-vectors .line:nth-child(122) .fire{animation:fire 1012ms linear -51ms infinite}@keyframes spark122{0%{transform:translateY(0)}to{transform:rotate(298deg) translateX(1014px)}}.bg-vectors .line:nth-child(123){transform:rotateY(271deg)}.bg-vectors .line:nth-child(123) .spark{animation:spark123 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:328px;width:204px}.bg-vectors .line:nth-child(123) .fire{animation:fire 1493ms linear -.47s infinite}@keyframes spark123{0%{transform:translateY(0)}to{transform:rotate(9deg) translateX(439px)}}.bg-vectors .line:nth-child(124){transform:rotateY(249deg)}.bg-vectors .line:nth-child(124) .spark{animation:spark124 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:212px;width:209px}.bg-vectors .line:nth-child(124) .fire{animation:fire 1869ms linear -.32s infinite}@keyframes spark124{0%{transform:translateY(0)}to{transform:rotate(265deg) translateX(371px)}}.bg-vectors .line:nth-child(125){transform:rotateY(63deg)}.bg-vectors .line:nth-child(125) .spark{animation:spark125 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:351px;width:346px}.bg-vectors .line:nth-child(125) .fire{animation:fire 1.67s linear -103ms infinite}@keyframes spark125{0%{transform:translateY(0)}to{transform:rotate(329deg) translateX(156px)}}.bg-vectors .line:nth-child(126){transform:rotateY(202deg)}.bg-vectors .line:nth-child(126) .spark{animation:spark126 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:294px;width:270px}.bg-vectors .line:nth-child(126) .fire{animation:fire 1595ms linear -68ms infinite}@keyframes spark126{0%{transform:translateY(0)}to{transform:rotate(52deg) translateX(922px)}}.bg-vectors .line:nth-child(127){transform:rotateY(241deg)}.bg-vectors .line:nth-child(127) .spark{animation:spark127 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:305px;width:214px}.bg-vectors .line:nth-child(127) .fire{animation:fire 1717ms linear -983ms infinite}@keyframes spark127{0%{transform:translateY(0)}to{transform:rotate(113deg) translateX(405px)}}.bg-vectors .line:nth-child(128){transform:rotateY(183deg)}.bg-vectors .line:nth-child(128) .spark{animation:spark128 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:369px;width:336px}.bg-vectors .line:nth-child(128) .fire{animation:fire 1423ms linear -899ms infinite}@keyframes spark128{0%{transform:translateY(0)}to{transform:rotate(359deg) translateX(579px)}}.bg-vectors .line:nth-child(129){transform:rotateY(51deg)}.bg-vectors .line:nth-child(129) .spark{animation:spark129 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:362px;width:253px}.bg-vectors .line:nth-child(129) .fire{animation:fire 1744ms linear -19ms infinite}@keyframes spark129{0%{transform:translateY(0)}to{transform:rotate(250deg) translateX(910px)}}.bg-vectors .line:nth-child(130){transform:rotateY(154deg)}.bg-vectors .line:nth-child(130) .spark{animation:spark130 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:353px;width:211px}.bg-vectors .line:nth-child(130) .fire{animation:fire 1536ms linear -523ms infinite}@keyframes spark130{0%{transform:translateY(0)}to{transform:rotate(325deg) translateX(382px)}}.bg-vectors .line:nth-child(131){transform:rotateY(75deg)}.bg-vectors .line:nth-child(131) .spark{animation:spark131 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:356px;width:266px}.bg-vectors .line:nth-child(131) .fire{animation:fire 1779ms linear -733ms infinite}@keyframes spark131{0%{transform:translateY(0)}to{transform:rotate(268deg) translateX(967px)}}.bg-vectors .line:nth-child(132){transform:rotateY(96deg)}.bg-vectors .line:nth-child(132) .spark{animation:spark132 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:400px;width:303px}.bg-vectors .line:nth-child(132) .fire{animation:fire 1022ms linear -807ms infinite}@keyframes spark132{0%{transform:translateY(0)}to{transform:rotate(28deg) translateX(397px)}}.bg-vectors .line:nth-child(133){transform:rotateY(299deg)}.bg-vectors .line:nth-child(133) .spark{animation:spark133 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:204px;width:227px}.bg-vectors .line:nth-child(133) .fire{animation:fire 1846ms linear -372ms infinite}@keyframes spark133{0%{transform:translateY(0)}to{transform:rotate(138deg) translateX(448px)}}.bg-vectors .line:nth-child(134){transform:rotateY(98deg)}.bg-vectors .line:nth-child(134) .spark{animation:spark134 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:387px;width:299px}.bg-vectors .line:nth-child(134) .fire{animation:fire 1933ms linear -299ms infinite}@keyframes spark134{0%{transform:translateY(0)}to{transform:rotate(72deg) translateX(961px)}}.bg-vectors .line:nth-child(135){transform:rotateY(325deg)}.bg-vectors .line:nth-child(135) .spark{animation:spark135 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:247px;width:210px}.bg-vectors .line:nth-child(135) .fire{animation:fire 1078ms linear -649ms infinite}@keyframes spark135{0%{transform:translateY(0)}to{transform:rotate(191deg) translateX(200px)}}.bg-vectors .line:nth-child(136){transform:rotateY(223deg)}.bg-vectors .line:nth-child(136) .spark{animation:spark136 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:230px;width:276px}.bg-vectors .line:nth-child(136) .fire{animation:fire 1772ms linear -226ms infinite}@keyframes spark136{0%{transform:translateY(0)}to{transform:rotate(2deg) translateX(459px)}}.bg-vectors .line:nth-child(137){transform:rotateY(175deg)}.bg-vectors .line:nth-child(137) .spark{animation:spark137 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:208px;width:214px}.bg-vectors .line:nth-child(137) .fire{animation:fire 1439ms linear -.41s infinite}@keyframes spark137{0%{transform:translateY(0)}to{transform:rotate(260deg) translateX(127px)}}.bg-vectors .line:nth-child(138){transform:rotateY(147deg)}.bg-vectors .line:nth-child(138) .spark{animation:spark138 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:356px;width:393px}.bg-vectors .line:nth-child(138) .fire{animation:fire 1834ms linear -678ms infinite}@keyframes spark138{0%{transform:translateY(0)}to{transform:rotate(304deg) translateX(317px)}}.bg-vectors .line:nth-child(139){transform:rotateY(3deg)}.bg-vectors .line:nth-child(139) .spark{animation:spark139 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:239px;width:274px}.bg-vectors .line:nth-child(139) .fire{animation:fire 1268ms linear -766ms infinite}@keyframes spark139{0%{transform:translateY(0)}to{transform:rotate(233deg) translateX(580px)}}.bg-vectors .line:nth-child(140){transform:rotateY(139deg)}.bg-vectors .line:nth-child(140) .spark{animation:spark140 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:394px;width:321px}.bg-vectors .line:nth-child(140) .fire{animation:fire 1393ms linear -303ms infinite}@keyframes spark140{0%{transform:translateY(0)}to{transform:rotate(199deg) translateX(639px)}}.bg-vectors .line:nth-child(141){transform:rotateY(218deg)}.bg-vectors .line:nth-child(141) .spark{animation:spark141 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:348px;width:213px}.bg-vectors .line:nth-child(141) .fire{animation:fire 1.25s linear -393ms infinite}@keyframes spark141{0%{transform:translateY(0)}to{transform:rotate(172deg) translateX(1099px)}}.bg-vectors .line:nth-child(142){transform:rotateY(45deg)}.bg-vectors .line:nth-child(142) .spark{animation:spark142 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:257px;width:242px}.bg-vectors .line:nth-child(142) .fire{animation:fire 1101ms linear -499ms infinite}@keyframes spark142{0%{transform:translateY(0)}to{transform:rotate(230deg) translateX(306px)}}.bg-vectors .line:nth-child(143){transform:rotateY(198deg)}.bg-vectors .line:nth-child(143) .spark{animation:spark143 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:253px;width:382px}.bg-vectors .line:nth-child(143) .fire{animation:fire 1423ms linear -.26s infinite}@keyframes spark143{0%{transform:translateY(0)}to{transform:rotate(37deg) translateX(178px)}}.bg-vectors .line:nth-child(144){transform:rotateY(296deg)}.bg-vectors .line:nth-child(144) .spark{animation:spark144 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:252px;width:304px}.bg-vectors .line:nth-child(144) .fire{animation:fire 1004ms linear -441ms infinite}@keyframes spark144{0%{transform:translateY(0)}to{transform:rotate(26deg) translateX(172px)}}.bg-vectors .line:nth-child(145){transform:rotateY(218deg)}.bg-vectors .line:nth-child(145) .spark{animation:spark145 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:358px;width:204px}.bg-vectors .line:nth-child(145) .fire{animation:fire 1993ms linear -341ms infinite}@keyframes spark145{0%{transform:translateY(0)}to{transform:rotate(210deg) translateX(795px)}}.bg-vectors .line:nth-child(146){transform:rotateY(59deg)}.bg-vectors .line:nth-child(146) .spark{animation:spark146 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:323px;width:321px}.bg-vectors .line:nth-child(146) .fire{animation:fire 1.73s linear -296ms infinite}@keyframes spark146{0%{transform:translateY(0)}to{transform:rotate(74deg) translateX(948px)}}.bg-vectors .line:nth-child(147){transform:rotateY(334deg)}.bg-vectors .line:nth-child(147) .spark{animation:spark147 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:255px;width:339px}.bg-vectors .line:nth-child(147) .fire{animation:fire 1928ms linear -29ms infinite}@keyframes spark147{0%{transform:translateY(0)}to{transform:rotate(229deg) translateX(700px)}}.bg-vectors .line:nth-child(148){transform:rotateY(288deg)}.bg-vectors .line:nth-child(148) .spark{animation:spark148 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:330px;width:309px}.bg-vectors .line:nth-child(148) .fire{animation:fire 1302ms linear -426ms infinite}@keyframes spark148{0%{transform:translateY(0)}to{transform:rotate(269deg) translateX(777px)}}.bg-vectors .line:nth-child(149){transform:rotateY(99deg)}.bg-vectors .line:nth-child(149) .spark{animation:spark149 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:266px;width:397px}.bg-vectors .line:nth-child(149) .fire{animation:fire 1065ms linear -825ms infinite}@keyframes spark149{0%{transform:translateY(0)}to{transform:rotate(191deg) translateX(456px)}}.bg-vectors .line:nth-child(150){transform:rotateY(322deg)}.bg-vectors .line:nth-child(150) .spark{animation:spark150 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:326px;width:388px}.bg-vectors .line:nth-child(150) .fire{animation:fire 1673ms linear -533ms infinite}@keyframes spark150{0%{transform:translateY(0)}to{transform:rotate(196deg) translateX(250px)}}.bg-vectors .line:nth-child(151){transform:rotateY(333deg)}.bg-vectors .line:nth-child(151) .spark{animation:spark151 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:229px;width:304px}.bg-vectors .line:nth-child(151) .fire{animation:fire 1666ms linear -435ms infinite}@keyframes spark151{0%{transform:translateY(0)}to{transform:rotate(232deg) translateX(594px)}}.bg-vectors .line:nth-child(152){transform:rotateY(336deg)}.bg-vectors .line:nth-child(152) .spark{animation:spark152 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:301px;width:207px}.bg-vectors .line:nth-child(152) .fire{animation:fire 1083ms linear -807ms infinite}@keyframes spark152{0%{transform:translateY(0)}to{transform:rotate(138deg) translateX(293px)}}.bg-vectors .line:nth-child(153){transform:rotateY(9deg)}.bg-vectors .line:nth-child(153) .spark{animation:spark153 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:332px;width:265px}.bg-vectors .line:nth-child(153) .fire{animation:fire 1309ms linear -14ms infinite}@keyframes spark153{0%{transform:translateY(0)}to{transform:rotate(24deg) translateX(190px)}}.bg-vectors .line:nth-child(154){transform:rotateY(235deg)}.bg-vectors .line:nth-child(154) .spark{animation:spark154 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:301px;width:235px}.bg-vectors .line:nth-child(154) .fire{animation:fire 1524ms linear -308ms infinite}@keyframes spark154{0%{transform:translateY(0)}to{transform:rotate(198deg) translateX(779px)}}.bg-vectors .line:nth-child(155){transform:rotateY(276deg)}.bg-vectors .line:nth-child(155) .spark{animation:spark155 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:236px;width:316px}.bg-vectors .line:nth-child(155) .fire{animation:fire 1774ms linear -276ms infinite}@keyframes spark155{0%{transform:translateY(0)}to{transform:rotate(91deg) translateX(533px)}}.bg-vectors .line:nth-child(156){transform:rotateY(28deg)}.bg-vectors .line:nth-child(156) .spark{animation:spark156 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:326px;width:226px}.bg-vectors .line:nth-child(156) .fire{animation:fire 1506ms linear -482ms infinite}@keyframes spark156{0%{transform:translateY(0)}to{transform:rotate(104deg) translateX(325px)}}.bg-vectors .line:nth-child(157){transform:rotateY(18deg)}.bg-vectors .line:nth-child(157) .spark{animation:spark157 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:252px;width:245px}.bg-vectors .line:nth-child(157) .fire{animation:fire 1953ms linear -.92s infinite}@keyframes spark157{0%{transform:translateY(0)}to{transform:rotate(272deg) translateX(114px)}}.bg-vectors .line:nth-child(158){transform:rotateY(4deg)}.bg-vectors .line:nth-child(158) .spark{animation:spark158 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:387px;width:338px}.bg-vectors .line:nth-child(158) .fire{animation:fire 1762ms linear -832ms infinite}@keyframes spark158{0%{transform:translateY(0)}to{transform:rotate(220deg) translateX(964px)}}.bg-vectors .line:nth-child(159){transform:rotateY(316deg)}.bg-vectors .line:nth-child(159) .spark{animation:spark159 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:292px;width:313px}.bg-vectors .line:nth-child(159) .fire{animation:fire 1011ms linear -574ms infinite}@keyframes spark159{0%{transform:translateY(0)}to{transform:rotate(265deg) translateX(456px)}}.bg-vectors .line:nth-child(160){transform:rotateY(11deg)}.bg-vectors .line:nth-child(160) .spark{animation:spark160 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:320px;width:346px}.bg-vectors .line:nth-child(160) .fire{animation:fire 1.13s linear -918ms infinite}@keyframes spark160{0%{transform:translateY(0)}to{transform:rotate(38deg) translateX(786px)}}.bg-vectors .line:nth-child(161){transform:rotateY(12deg)}.bg-vectors .line:nth-child(161) .spark{animation:spark161 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:255px;width:394px}.bg-vectors .line:nth-child(161) .fire{animation:fire 1374ms linear -19ms infinite}@keyframes spark161{0%{transform:translateY(0)}to{transform:rotate(138deg) translateX(110px)}}.bg-vectors .line:nth-child(162){transform:rotateY(33deg)}.bg-vectors .line:nth-child(162) .spark{animation:spark162 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:296px;width:367px}.bg-vectors .line:nth-child(162) .fire{animation:fire 1047ms linear -487ms infinite}@keyframes spark162{0%{transform:translateY(0)}to{transform:rotate(86deg) translateX(535px)}}.bg-vectors .line:nth-child(163){transform:rotateY(19deg)}.bg-vectors .line:nth-child(163) .spark{animation:spark163 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:386px;width:234px}.bg-vectors .line:nth-child(163) .fire{animation:fire 1792ms linear -503ms infinite}@keyframes spark163{0%{transform:translateY(0)}to{transform:rotate(274deg) translateX(470px)}}.bg-vectors .line:nth-child(164){transform:rotateY(86deg)}.bg-vectors .line:nth-child(164) .spark{animation:spark164 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:249px;width:318px}.bg-vectors .line:nth-child(164) .fire{animation:fire 1249ms linear -187ms infinite}@keyframes spark164{0%{transform:translateY(0)}to{transform:rotate(335deg) translateX(1096px)}}.bg-vectors .line:nth-child(165){transform:rotateY(24deg)}.bg-vectors .line:nth-child(165) .spark{animation:spark165 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:222px;width:320px}.bg-vectors .line:nth-child(165) .fire{animation:fire 1244ms linear -424ms infinite}@keyframes spark165{0%{transform:translateY(0)}to{transform:rotate(359deg) translateX(434px)}}.bg-vectors .line:nth-child(166){transform:rotateY(166deg)}.bg-vectors .line:nth-child(166) .spark{animation:spark166 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:322px;width:339px}.bg-vectors .line:nth-child(166) .fire{animation:fire 1543ms linear -817ms infinite}@keyframes spark166{0%{transform:translateY(0)}to{transform:rotate(273deg) translateX(796px)}}.bg-vectors .line:nth-child(167){transform:rotateY(294deg)}.bg-vectors .line:nth-child(167) .spark{animation:spark167 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:363px;width:357px}.bg-vectors .line:nth-child(167) .fire{animation:fire 1261ms linear -316ms infinite}@keyframes spark167{0%{transform:translateY(0)}to{transform:rotate(345deg) translateX(921px)}}.bg-vectors .line:nth-child(168){transform:rotateY(339deg)}.bg-vectors .line:nth-child(168) .spark{animation:spark168 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:235px;width:297px}.bg-vectors .line:nth-child(168) .fire{animation:fire 1768ms linear -815ms infinite}@keyframes spark168{0%{transform:translateY(0)}to{transform:rotate(187deg) translateX(471px)}}.bg-vectors .line:nth-child(169){transform:rotateY(36deg)}.bg-vectors .line:nth-child(169) .spark{animation:spark169 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:247px;width:244px}.bg-vectors .line:nth-child(169) .fire{animation:fire 1003ms linear -243ms infinite}@keyframes spark169{0%{transform:translateY(0)}to{transform:rotate(331deg) translateX(269px)}}.bg-vectors .line:nth-child(170){transform:rotateY(322deg)}.bg-vectors .line:nth-child(170) .spark{animation:spark170 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:216px;width:288px}.bg-vectors .line:nth-child(170) .fire{animation:fire 1087ms linear -.93s infinite}@keyframes spark170{0%{transform:translateY(0)}to{transform:rotate(185deg) translateX(976px)}}.bg-vectors .line:nth-child(171){transform:rotateY(219deg)}.bg-vectors .line:nth-child(171) .spark{animation:spark171 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:337px;width:308px}.bg-vectors .line:nth-child(171) .fire{animation:fire 1294ms linear -18ms infinite}@keyframes spark171{0%{transform:translateY(0)}to{transform:rotate(171deg) translateX(721px)}}.bg-vectors .line:nth-child(172){transform:rotateY(36deg)}.bg-vectors .line:nth-child(172) .spark{animation:spark172 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:260px;width:293px}.bg-vectors .line:nth-child(172) .fire{animation:fire 1459ms linear -456ms infinite}@keyframes spark172{0%{transform:translateY(0)}to{transform:rotate(57deg) translateX(637px)}}.bg-vectors .line:nth-child(173){transform:rotateY(87deg)}.bg-vectors .line:nth-child(173) .spark{animation:spark173 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:269px;width:356px}.bg-vectors .line:nth-child(173) .fire{animation:fire 1221ms linear -70ms infinite}@keyframes spark173{0%{transform:translateY(0)}to{transform:rotate(2deg) translateX(291px)}}.bg-vectors .line:nth-child(174){transform:rotateY(289deg)}.bg-vectors .line:nth-child(174) .spark{animation:spark174 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:248px;width:278px}.bg-vectors .line:nth-child(174) .fire{animation:fire 1643ms linear -904ms infinite}@keyframes spark174{0%{transform:translateY(0)}to{transform:rotate(295deg) translateX(217px)}}.bg-vectors .line:nth-child(175){transform:rotateY(21deg)}.bg-vectors .line:nth-child(175) .spark{animation:spark175 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:205px;width:221px}.bg-vectors .line:nth-child(175) .fire{animation:fire 1879ms linear -109ms infinite}@keyframes spark175{0%{transform:translateY(0)}to{transform:rotate(229deg) translateX(195px)}}.bg-vectors .line:nth-child(176){transform:rotateY(236deg)}.bg-vectors .line:nth-child(176) .spark{animation:spark176 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:399px;width:312px}.bg-vectors .line:nth-child(176) .fire{animation:fire 1397ms linear -603ms infinite}@keyframes spark176{0%{transform:translateY(0)}to{transform:rotate(274deg) translateX(389px)}}.bg-vectors .line:nth-child(177){transform:rotateY(25deg)}.bg-vectors .line:nth-child(177) .spark{animation:spark177 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:269px;width:290px}.bg-vectors .line:nth-child(177) .fire{animation:fire 1063ms linear -906ms infinite}@keyframes spark177{0%{transform:translateY(0)}to{transform:rotate(44deg) translateX(520px)}}.bg-vectors .line:nth-child(178){transform:rotateY(202deg)}.bg-vectors .line:nth-child(178) .spark{animation:spark178 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:346px;width:304px}.bg-vectors .line:nth-child(178) .fire{animation:fire 1343ms linear -211ms infinite}@keyframes spark178{0%{transform:translateY(0)}to{transform:rotate(205deg) translateX(646px)}}.bg-vectors .line:nth-child(179){transform:rotateY(184deg)}.bg-vectors .line:nth-child(179) .spark{animation:spark179 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:246px;width:224px}.bg-vectors .line:nth-child(179) .fire{animation:fire 1521ms linear -714ms infinite}@keyframes spark179{0%{transform:translateY(0)}to{transform:rotate(116deg) translateX(1035px)}}.bg-vectors .line:nth-child(180){transform:rotateY(285deg)}.bg-vectors .line:nth-child(180) .spark{animation:spark180 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:385px;width:395px}.bg-vectors .line:nth-child(180) .fire{animation:fire 1569ms linear -727ms infinite}@keyframes spark180{0%{transform:translateY(0)}to{transform:rotate(7deg) translateX(361px)}}.bg-vectors .line:nth-child(181){transform:rotateY(173deg)}.bg-vectors .line:nth-child(181) .spark{animation:spark181 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:320px;width:313px}.bg-vectors .line:nth-child(181) .fire{animation:fire 1639ms linear -823ms infinite}@keyframes spark181{0%{transform:translateY(0)}to{transform:rotate(63deg) translateX(645px)}}.bg-vectors .line:nth-child(182){transform:rotateY(292deg)}.bg-vectors .line:nth-child(182) .spark{animation:spark182 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:357px;width:382px}.bg-vectors .line:nth-child(182) .fire{animation:fire 1624ms linear -578ms infinite}@keyframes spark182{0%{transform:translateY(0)}to{transform:rotate(72deg) translateX(196px)}}.bg-vectors .line:nth-child(183){transform:rotateY(238deg)}.bg-vectors .line:nth-child(183) .spark{animation:spark183 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:385px;width:228px}.bg-vectors .line:nth-child(183) .fire{animation:fire 1815ms linear -808ms infinite}@keyframes spark183{0%{transform:translateY(0)}to{transform:rotate(17deg) translateX(959px)}}.bg-vectors .line:nth-child(184){transform:rotateY(1deg)}.bg-vectors .line:nth-child(184) .spark{animation:spark184 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:312px;width:226px}.bg-vectors .line:nth-child(184) .fire{animation:fire 1645ms linear -313ms infinite}@keyframes spark184{0%{transform:translateY(0)}to{transform:rotate(6deg) translateX(298px)}}.bg-vectors .line:nth-child(185){transform:rotateY(186deg)}.bg-vectors .line:nth-child(185) .spark{animation:spark185 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:338px;width:228px}.bg-vectors .line:nth-child(185) .fire{animation:fire 1116ms linear -616ms infinite}@keyframes spark185{0%{transform:translateY(0)}to{transform:rotate(136deg) translateX(482px)}}.bg-vectors .line:nth-child(186){transform:rotateY(132deg)}.bg-vectors .line:nth-child(186) .spark{animation:spark186 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:295px;width:383px}.bg-vectors .line:nth-child(186) .fire{animation:fire 1407ms linear -.34s infinite}@keyframes spark186{0%{transform:translateY(0)}to{transform:rotate(23deg) translateX(441px)}}.bg-vectors .line:nth-child(187){transform:rotateY(241deg)}.bg-vectors .line:nth-child(187) .spark{animation:spark187 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:336px;width:320px}.bg-vectors .line:nth-child(187) .fire{animation:fire 1853ms linear -.86s infinite}@keyframes spark187{0%{transform:translateY(0)}to{transform:rotate(190deg) translateX(413px)}}.bg-vectors .line:nth-child(188){transform:rotateY(97deg)}.bg-vectors .line:nth-child(188) .spark{animation:spark188 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:395px;width:396px}.bg-vectors .line:nth-child(188) .fire{animation:fire 1754ms linear -866ms infinite}@keyframes spark188{0%{transform:translateY(0)}to{transform:rotate(77deg) translateX(772px)}}.bg-vectors .line:nth-child(189){transform:rotateY(327deg)}.bg-vectors .line:nth-child(189) .spark{animation:spark189 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:232px;width:281px}.bg-vectors .line:nth-child(189) .fire{animation:fire 1505ms linear -285ms infinite}@keyframes spark189{0%{transform:translateY(0)}to{transform:rotate(271deg) translateX(684px)}}.bg-vectors .line:nth-child(190){transform:rotateY(263deg)}.bg-vectors .line:nth-child(190) .spark{animation:spark190 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:320px;width:316px}.bg-vectors .line:nth-child(190) .fire{animation:fire 1039ms linear -494ms infinite}@keyframes spark190{0%{transform:translateY(0)}to{transform:rotate(299deg) translateX(1015px)}}.bg-vectors .line:nth-child(191){transform:rotateY(147deg)}.bg-vectors .line:nth-child(191) .spark{animation:spark191 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:255px;width:300px}.bg-vectors .line:nth-child(191) .fire{animation:fire 1344ms linear -718ms infinite}@keyframes spark191{0%{transform:translateY(0)}to{transform:rotate(306deg) translateX(558px)}}.bg-vectors .line:nth-child(192){transform:rotateY(156deg)}.bg-vectors .line:nth-child(192) .spark{animation:spark192 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:344px;width:330px}.bg-vectors .line:nth-child(192) .fire{animation:fire 1498ms linear -427ms infinite}@keyframes spark192{0%{transform:translateY(0)}to{transform:rotate(304deg) translateX(901px)}}.bg-vectors .line:nth-child(193){transform:rotateY(264deg)}.bg-vectors .line:nth-child(193) .spark{animation:spark193 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:380px;width:330px}.bg-vectors .line:nth-child(193) .fire{animation:fire 1448ms linear -987ms infinite}@keyframes spark193{0%{transform:translateY(0)}to{transform:rotate(87deg) translateX(1076px)}}.bg-vectors .line:nth-child(194){transform:rotateY(253deg)}.bg-vectors .line:nth-child(194) .spark{animation:spark194 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:356px;width:251px}.bg-vectors .line:nth-child(194) .fire{animation:fire 1412ms linear -585ms infinite}@keyframes spark194{0%{transform:translateY(0)}to{transform:rotate(149deg) translateX(584px)}}.bg-vectors .line:nth-child(195){transform:rotateY(294deg)}.bg-vectors .line:nth-child(195) .spark{animation:spark195 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:316px;width:343px}.bg-vectors .line:nth-child(195) .fire{animation:fire 1798ms linear -338ms infinite}@keyframes spark195{0%{transform:translateY(0)}to{transform:rotate(349deg) translateX(1059px)}}.bg-vectors .line:nth-child(196){transform:rotateY(307deg)}.bg-vectors .line:nth-child(196) .spark{animation:spark196 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:303px;width:366px}.bg-vectors .line:nth-child(196) .fire{animation:fire 1315ms linear -484ms infinite}@keyframes spark196{0%{transform:translateY(0)}to{transform:rotate(148deg) translateX(263px)}}.bg-vectors .line:nth-child(197){transform:rotateY(137deg)}.bg-vectors .line:nth-child(197) .spark{animation:spark197 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:253px;width:386px}.bg-vectors .line:nth-child(197) .fire{animation:fire 1913ms linear -866ms infinite}@keyframes spark197{0%{transform:translateY(0)}to{transform:rotate(1deg) translateX(546px)}}.bg-vectors .line:nth-child(198){transform:rotateY(22deg)}.bg-vectors .line:nth-child(198) .spark{animation:spark198 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:216px;width:225px}.bg-vectors .line:nth-child(198) .fire{animation:fire 1591ms linear -589ms infinite}@keyframes spark198{0%{transform:translateY(0)}to{transform:rotate(340deg) translateX(709px)}}.bg-vectors .line:nth-child(199){transform:rotateY(280deg)}.bg-vectors .line:nth-child(199) .spark{animation:spark199 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:211px;width:256px}.bg-vectors .line:nth-child(199) .fire{animation:fire 1081ms linear -204ms infinite}@keyframes spark199{0%{transform:translateY(0)}to{transform:rotate(299deg) translateX(1012px)}}.bg-vectors .line:nth-child(200){transform:rotateY(307deg)}.bg-vectors .line:nth-child(200) .spark{animation:spark200 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:204px;width:234px}.bg-vectors .line:nth-child(200) .fire{animation:fire 1856ms linear -236ms infinite}@keyframes spark200{0%{transform:translateY(0)}to{transform:rotate(87deg) translateX(384px)}}.bg-vectors .line:nth-child(201){transform:rotateY(176deg)}.bg-vectors .line:nth-child(201) .spark{animation:spark201 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:301px;width:252px}.bg-vectors .line:nth-child(201) .fire{animation:fire 1762ms linear -414ms infinite}@keyframes spark201{0%{transform:translateY(0)}to{transform:rotate(250deg) translateX(784px)}}.bg-vectors .line:nth-child(202){transform:rotateY(211deg)}.bg-vectors .line:nth-child(202) .spark{animation:spark202 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:219px;width:395px}.bg-vectors .line:nth-child(202) .fire{animation:fire 1357ms linear -701ms infinite}@keyframes spark202{0%{transform:translateY(0)}to{transform:rotate(18deg) translateX(555px)}}.bg-vectors .line:nth-child(203){transform:rotateY(187deg)}.bg-vectors .line:nth-child(203) .spark{animation:spark203 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:346px;width:244px}.bg-vectors .line:nth-child(203) .fire{animation:fire 1803ms linear -695ms infinite}@keyframes spark203{0%{transform:translateY(0)}to{transform:rotate(325deg) translateX(614px)}}.bg-vectors .line:nth-child(204){transform:rotateY(54deg)}.bg-vectors .line:nth-child(204) .spark{animation:spark204 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:397px;width:229px}.bg-vectors .line:nth-child(204) .fire{animation:fire 1178ms linear -29ms infinite}@keyframes spark204{0%{transform:translateY(0)}to{transform:rotate(206deg) translateX(489px)}}.bg-vectors .line:nth-child(205){transform:rotateY(143deg)}.bg-vectors .line:nth-child(205) .spark{animation:spark205 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:207px;width:320px}.bg-vectors .line:nth-child(205) .fire{animation:fire 1.17s linear -356ms infinite}@keyframes spark205{0%{transform:translateY(0)}to{transform:rotate(253deg) translateX(614px)}}.bg-vectors .line:nth-child(206){transform:rotateY(88deg)}.bg-vectors .line:nth-child(206) .spark{animation:spark206 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:346px;width:244px}.bg-vectors .line:nth-child(206) .fire{animation:fire 1487ms linear -666ms infinite}@keyframes spark206{0%{transform:translateY(0)}to{transform:rotate(68deg) translateX(978px)}}.bg-vectors .line:nth-child(207){transform:rotateY(352deg)}.bg-vectors .line:nth-child(207) .spark{animation:spark207 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:336px;width:371px}.bg-vectors .line:nth-child(207) .fire{animation:fire 1322ms linear -431ms infinite}@keyframes spark207{0%{transform:translateY(0)}to{transform:rotate(176deg) translateX(244px)}}.bg-vectors .line:nth-child(208){transform:rotateY(71deg)}.bg-vectors .line:nth-child(208) .spark{animation:spark208 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:295px;width:387px}.bg-vectors .line:nth-child(208) .fire{animation:fire 1427ms linear -392ms infinite}@keyframes spark208{0%{transform:translateY(0)}to{transform:rotate(217deg) translateX(345px)}}.bg-vectors .line:nth-child(209){transform:rotateY(184deg)}.bg-vectors .line:nth-child(209) .spark{animation:spark209 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:214px;width:354px}.bg-vectors .line:nth-child(209) .fire{animation:fire 1503ms linear -.96s infinite}@keyframes spark209{0%{transform:translateY(0)}to{transform:rotate(341deg) translateX(985px)}}.bg-vectors .line:nth-child(210){transform:rotateY(169deg)}.bg-vectors .line:nth-child(210) .spark{animation:spark210 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:315px;width:229px}.bg-vectors .line:nth-child(210) .fire{animation:fire 1479ms linear -663ms infinite}@keyframes spark210{0%{transform:translateY(0)}to{transform:rotate(143deg) translateX(1044px)}}.bg-vectors .line:nth-child(211){transform:rotateY(20deg)}.bg-vectors .line:nth-child(211) .spark{animation:spark211 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:352px;width:241px}.bg-vectors .line:nth-child(211) .fire{animation:fire 1547ms linear -436ms infinite}@keyframes spark211{0%{transform:translateY(0)}to{transform:rotate(185deg) translateX(796px)}}.bg-vectors .line:nth-child(212){transform:rotateY(340deg)}.bg-vectors .line:nth-child(212) .spark{animation:spark212 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:290px;width:382px}.bg-vectors .line:nth-child(212) .fire{animation:fire 1602ms linear -43ms infinite}@keyframes spark212{0%{transform:translateY(0)}to{transform:rotate(207deg) translateX(268px)}}.bg-vectors .line:nth-child(213){transform:rotateY(226deg)}.bg-vectors .line:nth-child(213) .spark{animation:spark213 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:400px;width:245px}.bg-vectors .line:nth-child(213) .fire{animation:fire 1999ms linear -666ms infinite}@keyframes spark213{0%{transform:translateY(0)}to{transform:rotate(166deg) translateX(1044px)}}.bg-vectors .line:nth-child(214){transform:rotateY(227deg)}.bg-vectors .line:nth-child(214) .spark{animation:spark214 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:361px;width:286px}.bg-vectors .line:nth-child(214) .fire{animation:fire 1364ms linear -865ms infinite}@keyframes spark214{0%{transform:translateY(0)}to{transform:rotate(319deg) translateX(231px)}}.bg-vectors .line:nth-child(215){transform:rotateY(27deg)}.bg-vectors .line:nth-child(215) .spark{animation:spark215 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:374px;width:241px}.bg-vectors .line:nth-child(215) .fire{animation:fire 1898ms linear -746ms infinite}@keyframes spark215{0%{transform:translateY(0)}to{transform:rotate(329deg) translateX(312px)}}.bg-vectors .line:nth-child(216){transform:rotateY(16deg)}.bg-vectors .line:nth-child(216) .spark{animation:spark216 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:399px;width:368px}.bg-vectors .line:nth-child(216) .fire{animation:fire 1493ms linear -779ms infinite}@keyframes spark216{0%{transform:translateY(0)}to{transform:rotate(337deg) translateX(592px)}}.bg-vectors .line:nth-child(217){transform:rotateY(352deg)}.bg-vectors .line:nth-child(217) .spark{animation:spark217 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:208px;width:227px}.bg-vectors .line:nth-child(217) .fire{animation:fire 1527ms linear -812ms infinite}@keyframes spark217{0%{transform:translateY(0)}to{transform:rotate(336deg) translateX(529px)}}.bg-vectors .line:nth-child(218){transform:rotateY(230deg)}.bg-vectors .line:nth-child(218) .spark{animation:spark218 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:358px;width:370px}.bg-vectors .line:nth-child(218) .fire{animation:fire 1811ms linear -2ms infinite}@keyframes spark218{0%{transform:translateY(0)}to{transform:rotate(242deg) translateX(599px)}}.bg-vectors .line:nth-child(219){transform:rotateY(112deg)}.bg-vectors .line:nth-child(219) .spark{animation:spark219 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:321px;width:305px}.bg-vectors .line:nth-child(219) .fire{animation:fire 1086ms linear -725ms infinite}@keyframes spark219{0%{transform:translateY(0)}to{transform:rotate(236deg) translateX(602px)}}.bg-vectors .line:nth-child(220){transform:rotateY(287deg)}.bg-vectors .line:nth-child(220) .spark{animation:spark220 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:261px;width:297px}.bg-vectors .line:nth-child(220) .fire{animation:fire 1622ms linear -603ms infinite}@keyframes spark220{0%{transform:translateY(0)}to{transform:rotate(28deg) translateX(411px)}}.bg-vectors .line:nth-child(221){transform:rotateY(318deg)}.bg-vectors .line:nth-child(221) .spark{animation:spark221 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:234px;width:270px}.bg-vectors .line:nth-child(221) .fire{animation:fire 1426ms linear -728ms infinite}@keyframes spark221{0%{transform:translateY(0)}to{transform:rotate(299deg) translateX(314px)}}.bg-vectors .line:nth-child(222){transform:rotateY(342deg)}.bg-vectors .line:nth-child(222) .spark{animation:spark222 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:227px;width:320px}.bg-vectors .line:nth-child(222) .fire{animation:fire 1.09s linear -966ms infinite}@keyframes spark222{0%{transform:translateY(0)}to{transform:rotate(100deg) translateX(279px)}}.bg-vectors .line:nth-child(223){transform:rotateY(105deg)}.bg-vectors .line:nth-child(223) .spark{animation:spark223 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:277px;width:359px}.bg-vectors .line:nth-child(223) .fire{animation:fire 1402ms linear -228ms infinite}@keyframes spark223{0%{transform:translateY(0)}to{transform:rotate(140deg) translateX(217px)}}.bg-vectors .line:nth-child(224){transform:rotateY(175deg)}.bg-vectors .line:nth-child(224) .spark{animation:spark224 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:244px;width:285px}.bg-vectors .line:nth-child(224) .fire{animation:fire 1837ms linear -689ms infinite}@keyframes spark224{0%{transform:translateY(0)}to{transform:rotate(256deg) translateX(1092px)}}.bg-vectors .line:nth-child(225){transform:rotateY(106deg)}.bg-vectors .line:nth-child(225) .spark{animation:spark225 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:231px;width:251px}.bg-vectors .line:nth-child(225) .fire{animation:fire 1442ms linear -588ms infinite}@keyframes spark225{0%{transform:translateY(0)}to{transform:rotate(93deg) translateX(329px)}}.bg-vectors .line:nth-child(226){transform:rotateY(265deg)}.bg-vectors .line:nth-child(226) .spark{animation:spark226 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:203px;width:241px}.bg-vectors .line:nth-child(226) .fire{animation:fire 1.2s linear -.63s infinite}@keyframes spark226{0%{transform:translateY(0)}to{transform:rotate(133deg) translateX(263px)}}.bg-vectors .line:nth-child(227){transform:rotateY(297deg)}.bg-vectors .line:nth-child(227) .spark{animation:spark227 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:262px;width:368px}.bg-vectors .line:nth-child(227) .fire{animation:fire 1498ms linear -1s infinite}@keyframes spark227{0%{transform:translateY(0)}to{transform:rotate(329deg) translateX(403px)}}.bg-vectors .line:nth-child(228){transform:rotateY(63deg)}.bg-vectors .line:nth-child(228) .spark{animation:spark228 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:215px;width:269px}.bg-vectors .line:nth-child(228) .fire{animation:fire 1769ms linear -.61s infinite}@keyframes spark228{0%{transform:translateY(0)}to{transform:rotate(9deg) translateX(326px)}}.bg-vectors .line:nth-child(229){transform:rotateY(115deg)}.bg-vectors .line:nth-child(229) .spark{animation:spark229 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:250px;width:284px}.bg-vectors .line:nth-child(229) .fire{animation:fire 1.18s linear -709ms infinite}@keyframes spark229{0%{transform:translateY(0)}to{transform:rotate(46deg) translateX(702px)}}.bg-vectors .line:nth-child(230){transform:rotateY(249deg)}.bg-vectors .line:nth-child(230) .spark{animation:spark230 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:262px;width:263px}.bg-vectors .line:nth-child(230) .fire{animation:fire 1297ms linear -57ms infinite}@keyframes spark230{0%{transform:translateY(0)}to{transform:rotate(315deg) translateX(811px)}}.bg-vectors .line:nth-child(231){transform:rotateY(159deg)}.bg-vectors .line:nth-child(231) .spark{animation:spark231 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:356px;width:237px}.bg-vectors .line:nth-child(231) .fire{animation:fire 1913ms linear -41ms infinite}@keyframes spark231{0%{transform:translateY(0)}to{transform:rotate(346deg) translateX(582px)}}.bg-vectors .line:nth-child(232){transform:rotateY(303deg)}.bg-vectors .line:nth-child(232) .spark{animation:spark232 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:249px;width:265px}.bg-vectors .line:nth-child(232) .fire{animation:fire 1356ms linear -895ms infinite}@keyframes spark232{0%{transform:translateY(0)}to{transform:rotate(181deg) translateX(584px)}}.bg-vectors .line:nth-child(233){transform:rotateY(128deg)}.bg-vectors .line:nth-child(233) .spark{animation:spark233 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:400px;width:292px}.bg-vectors .line:nth-child(233) .fire{animation:fire 1435ms linear -629ms infinite}@keyframes spark233{0%{transform:translateY(0)}to{transform:rotate(141deg) translateX(615px)}}.bg-vectors .line:nth-child(234){transform:rotateY(300deg)}.bg-vectors .line:nth-child(234) .spark{animation:spark234 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:311px;width:335px}.bg-vectors .line:nth-child(234) .fire{animation:fire 1537ms linear -499ms infinite}@keyframes spark234{0%{transform:translateY(0)}to{transform:rotate(246deg) translateX(294px)}}.bg-vectors .line:nth-child(235){transform:rotateY(78deg)}.bg-vectors .line:nth-child(235) .spark{animation:spark235 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:289px;width:228px}.bg-vectors .line:nth-child(235) .fire{animation:fire 1664ms linear -886ms infinite}@keyframes spark235{0%{transform:translateY(0)}to{transform:rotate(84deg) translateX(767px)}}.bg-vectors .line:nth-child(236){transform:rotateY(68deg)}.bg-vectors .line:nth-child(236) .spark{animation:spark236 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:328px;width:207px}.bg-vectors .line:nth-child(236) .fire{animation:fire 1776ms linear -742ms infinite}@keyframes spark236{0%{transform:translateY(0)}to{transform:rotate(188deg) translateX(534px)}}.bg-vectors .line:nth-child(237){transform:rotateY(116deg)}.bg-vectors .line:nth-child(237) .spark{animation:spark237 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:381px;width:318px}.bg-vectors .line:nth-child(237) .fire{animation:fire 1248ms linear -898ms infinite}@keyframes spark237{0%{transform:translateY(0)}to{transform:rotate(60deg) translateX(588px)}}.bg-vectors .line:nth-child(238){transform:rotateY(230deg)}.bg-vectors .line:nth-child(238) .spark{animation:spark238 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:243px;width:214px}.bg-vectors .line:nth-child(238) .fire{animation:fire 1805ms linear -686ms infinite}@keyframes spark238{0%{transform:translateY(0)}to{transform:rotate(289deg) translateX(625px)}}.bg-vectors .line:nth-child(239){transform:rotateY(182deg)}.bg-vectors .line:nth-child(239) .spark{animation:spark239 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:266px;width:208px}.bg-vectors .line:nth-child(239) .fire{animation:fire 1884ms linear -74ms infinite}@keyframes spark239{0%{transform:translateY(0)}to{transform:rotate(146deg) translateX(697px)}}.bg-vectors .line:nth-child(240){transform:rotateY(212deg)}.bg-vectors .line:nth-child(240) .spark{animation:spark240 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:287px;width:270px}.bg-vectors .line:nth-child(240) .fire{animation:fire 1371ms linear -229ms infinite}@keyframes spark240{0%{transform:translateY(0)}to{transform:rotate(279deg) translateX(670px)}}.bg-vectors .line:nth-child(241){transform:rotateY(173deg)}.bg-vectors .line:nth-child(241) .spark{animation:spark241 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:329px;width:295px}.bg-vectors .line:nth-child(241) .fire{animation:fire 1563ms linear -13ms infinite}@keyframes spark241{0%{transform:translateY(0)}to{transform:rotate(58deg) translateX(862px)}}.bg-vectors .line:nth-child(242){transform:rotateY(358deg)}.bg-vectors .line:nth-child(242) .spark{animation:spark242 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:234px;width:226px}.bg-vectors .line:nth-child(242) .fire{animation:fire 1789ms linear -676ms infinite}@keyframes spark242{0%{transform:translateY(0)}to{transform:rotate(260deg) translateX(234px)}}.bg-vectors .line:nth-child(243){transform:rotateY(177deg)}.bg-vectors .line:nth-child(243) .spark{animation:spark243 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:318px;width:239px}.bg-vectors .line:nth-child(243) .fire{animation:fire 1491ms linear -10ms infinite}@keyframes spark243{0%{transform:translateY(0)}to{transform:rotate(44deg) translateX(234px)}}.bg-vectors .line:nth-child(244){transform:rotateY(101deg)}.bg-vectors .line:nth-child(244) .spark{animation:spark244 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:354px;width:347px}.bg-vectors .line:nth-child(244) .fire{animation:fire 1216ms linear -227ms infinite}@keyframes spark244{0%{transform:translateY(0)}to{transform:rotate(269deg) translateX(1091px)}}.bg-vectors .line:nth-child(245){transform:rotateY(169deg)}.bg-vectors .line:nth-child(245) .spark{animation:spark245 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:233px;width:359px}.bg-vectors .line:nth-child(245) .fire{animation:fire 1235ms linear -631ms infinite}@keyframes spark245{0%{transform:translateY(0)}to{transform:rotate(10deg) translateX(1027px)}}.bg-vectors .line:nth-child(246){transform:rotateY(325deg)}.bg-vectors .line:nth-child(246) .spark{animation:spark246 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:380px;width:345px}.bg-vectors .line:nth-child(246) .fire{animation:fire 1825ms linear -902ms infinite}@keyframes spark246{0%{transform:translateY(0)}to{transform:rotate(43deg) translateX(396px)}}.bg-vectors .line:nth-child(247){transform:rotateY(204deg)}.bg-vectors .line:nth-child(247) .spark{animation:spark247 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:321px;width:350px}.bg-vectors .line:nth-child(247) .fire{animation:fire 1187ms linear -997ms infinite}@keyframes spark247{0%{transform:translateY(0)}to{transform:rotate(113deg) translateX(457px)}}.bg-vectors .line:nth-child(248){transform:rotateY(206deg)}.bg-vectors .line:nth-child(248) .spark{animation:spark248 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:337px;width:356px}.bg-vectors .line:nth-child(248) .fire{animation:fire 1.53s linear -367ms infinite}@keyframes spark248{0%{transform:translateY(0)}to{transform:rotate(253deg) translateX(984px)}}.bg-vectors .line:nth-child(249){transform:rotateY(215deg)}.bg-vectors .line:nth-child(249) .spark{animation:spark249 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:284px;width:298px}.bg-vectors .line:nth-child(249) .fire{animation:fire 1792ms linear -174ms infinite}@keyframes spark249{0%{transform:translateY(0)}to{transform:rotate(45deg) translateX(499px)}}.bg-vectors .line:nth-child(250){transform:rotateY(200deg)}.bg-vectors .line:nth-child(250) .spark{animation:spark250 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:381px;width:345px}.bg-vectors .line:nth-child(250) .fire{animation:fire 1618ms linear -509ms infinite}@keyframes spark250{0%{transform:translateY(0)}to{transform:rotate(277deg) translateX(671px)}}.bg-vectors .line:nth-child(251){transform:rotateY(263deg)}.bg-vectors .line:nth-child(251) .spark{animation:spark251 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:212px;width:327px}.bg-vectors .line:nth-child(251) .fire{animation:fire 1853ms linear -222ms infinite}@keyframes spark251{0%{transform:translateY(0)}to{transform:rotate(105deg) translateX(753px)}}.bg-vectors .line:nth-child(252){transform:rotateY(235deg)}.bg-vectors .line:nth-child(252) .spark{animation:spark252 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:351px;width:246px}.bg-vectors .line:nth-child(252) .fire{animation:fire 1397ms linear -305ms infinite}@keyframes spark252{0%{transform:translateY(0)}to{transform:rotate(350deg) translateX(673px)}}.bg-vectors .line:nth-child(253){transform:rotateY(110deg)}.bg-vectors .line:nth-child(253) .spark{animation:spark253 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:243px;width:224px}.bg-vectors .line:nth-child(253) .fire{animation:fire 1586ms linear -233ms infinite}@keyframes spark253{0%{transform:translateY(0)}to{transform:rotate(165deg) translateX(1040px)}}.bg-vectors .line:nth-child(254){transform:rotateY(291deg)}.bg-vectors .line:nth-child(254) .spark{animation:spark254 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:289px;width:317px}.bg-vectors .line:nth-child(254) .fire{animation:fire 1082ms linear -457ms infinite}@keyframes spark254{0%{transform:translateY(0)}to{transform:rotate(92deg) translateX(281px)}}.bg-vectors .line:nth-child(255){transform:rotateY(113deg)}.bg-vectors .line:nth-child(255) .spark{animation:spark255 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:275px;width:319px}.bg-vectors .line:nth-child(255) .fire{animation:fire 1188ms linear -633ms infinite}@keyframes spark255{0%{transform:translateY(0)}to{transform:rotate(2deg) translateX(906px)}}.bg-vectors .line:nth-child(256){transform:rotateY(125deg)}.bg-vectors .line:nth-child(256) .spark{animation:spark256 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:222px;width:223px}.bg-vectors .line:nth-child(256) .fire{animation:fire 1122ms linear -27ms infinite}@keyframes spark256{0%{transform:translateY(0)}to{transform:rotate(51deg) translateX(180px)}}.bg-vectors .line:nth-child(257){transform:rotateY(74deg)}.bg-vectors .line:nth-child(257) .spark{animation:spark257 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:347px;width:400px}.bg-vectors .line:nth-child(257) .fire{animation:fire 1331ms linear -546ms infinite}@keyframes spark257{0%{transform:translateY(0)}to{transform:rotate(150deg) translateX(329px)}}.bg-vectors .line:nth-child(258){transform:rotateY(351deg)}.bg-vectors .line:nth-child(258) .spark{animation:spark258 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:379px;width:333px}.bg-vectors .line:nth-child(258) .fire{animation:fire 1.27s linear -817ms infinite}@keyframes spark258{0%{transform:translateY(0)}to{transform:rotate(34deg) translateX(851px)}}.bg-vectors .line:nth-child(259){transform:rotateY(240deg)}.bg-vectors .line:nth-child(259) .spark{animation:spark259 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:276px;width:399px}.bg-vectors .line:nth-child(259) .fire{animation:fire 1612ms linear -869ms infinite}@keyframes spark259{0%{transform:translateY(0)}to{transform:rotate(182deg) translateX(849px)}}.bg-vectors .line:nth-child(260){transform:rotateY(182deg)}.bg-vectors .line:nth-child(260) .spark{animation:spark260 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:337px;width:308px}.bg-vectors .line:nth-child(260) .fire{animation:fire 1837ms linear -95ms infinite}@keyframes spark260{0%{transform:translateY(0)}to{transform:rotate(57deg) translateX(589px)}}.bg-vectors .line:nth-child(261){transform:rotateY(80deg)}.bg-vectors .line:nth-child(261) .spark{animation:spark261 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:212px;width:256px}.bg-vectors .line:nth-child(261) .fire{animation:fire 1.35s linear -8ms infinite}@keyframes spark261{0%{transform:translateY(0)}to{transform:rotate(105deg) translateX(196px)}}.bg-vectors .line:nth-child(262){transform:rotateY(109deg)}.bg-vectors .line:nth-child(262) .spark{animation:spark262 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:282px;width:211px}.bg-vectors .line:nth-child(262) .fire{animation:fire 1616ms linear -407ms infinite}@keyframes spark262{0%{transform:translateY(0)}to{transform:rotate(320deg) translateX(554px)}}.bg-vectors .line:nth-child(263){transform:rotateY(204deg)}.bg-vectors .line:nth-child(263) .spark{animation:spark263 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:266px;width:203px}.bg-vectors .line:nth-child(263) .fire{animation:fire 1224ms linear -66ms infinite}@keyframes spark263{0%{transform:translateY(0)}to{transform:rotate(76deg) translateX(421px)}}.bg-vectors .line:nth-child(264){transform:rotateY(90deg)}.bg-vectors .line:nth-child(264) .spark{animation:spark264 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:267px;width:356px}.bg-vectors .line:nth-child(264) .fire{animation:fire 1408ms linear -468ms infinite}@keyframes spark264{0%{transform:translateY(0)}to{transform:rotate(49deg) translateX(374px)}}.bg-vectors .line:nth-child(265){transform:rotateY(37deg)}.bg-vectors .line:nth-child(265) .spark{animation:spark265 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:343px;width:313px}.bg-vectors .line:nth-child(265) .fire{animation:fire 1343ms linear -241ms infinite}@keyframes spark265{0%{transform:translateY(0)}to{transform:rotate(180deg) translateX(102px)}}.bg-vectors .line:nth-child(266){transform:rotateY(3deg)}.bg-vectors .line:nth-child(266) .spark{animation:spark266 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:294px;width:345px}.bg-vectors .line:nth-child(266) .fire{animation:fire 1103ms linear -584ms infinite}@keyframes spark266{0%{transform:translateY(0)}to{transform:rotate(238deg) translateX(389px)}}.bg-vectors .line:nth-child(267){transform:rotateY(256deg)}.bg-vectors .line:nth-child(267) .spark{animation:spark267 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:355px;width:290px}.bg-vectors .line:nth-child(267) .fire{animation:fire 1838ms linear -606ms infinite}@keyframes spark267{0%{transform:translateY(0)}to{transform:rotate(242deg) translateX(694px)}}.bg-vectors .line:nth-child(268){transform:rotateY(352deg)}.bg-vectors .line:nth-child(268) .spark{animation:spark268 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:361px;width:273px}.bg-vectors .line:nth-child(268) .fire{animation:fire 1711ms linear -382ms infinite}@keyframes spark268{0%{transform:translateY(0)}to{transform:rotate(146deg) translateX(454px)}}.bg-vectors .line:nth-child(269){transform:rotateY(268deg)}.bg-vectors .line:nth-child(269) .spark{animation:spark269 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:258px;width:232px}.bg-vectors .line:nth-child(269) .fire{animation:fire 1809ms linear -451ms infinite}@keyframes spark269{0%{transform:translateY(0)}to{transform:rotate(217deg) translateX(933px)}}.bg-vectors .line:nth-child(270){transform:rotateY(130deg)}.bg-vectors .line:nth-child(270) .spark{animation:spark270 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:387px;width:219px}.bg-vectors .line:nth-child(270) .fire{animation:fire 1518ms linear -942ms infinite}@keyframes spark270{0%{transform:translateY(0)}to{transform:rotate(21deg) translateX(1060px)}}.bg-vectors .line:nth-child(271){transform:rotateY(182deg)}.bg-vectors .line:nth-child(271) .spark{animation:spark271 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:280px;width:327px}.bg-vectors .line:nth-child(271) .fire{animation:fire 1581ms linear -47ms infinite}@keyframes spark271{0%{transform:translateY(0)}to{transform:rotate(48deg) translateX(217px)}}.bg-vectors .line:nth-child(272){transform:rotateY(181deg)}.bg-vectors .line:nth-child(272) .spark{animation:spark272 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:378px;width:360px}.bg-vectors .line:nth-child(272) .fire{animation:fire 1768ms linear -311ms infinite}@keyframes spark272{0%{transform:translateY(0)}to{transform:rotate(163deg) translateX(174px)}}.bg-vectors .line:nth-child(273){transform:rotateY(224deg)}.bg-vectors .line:nth-child(273) .spark{animation:spark273 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:246px;width:316px}.bg-vectors .line:nth-child(273) .fire{animation:fire 1722ms linear -817ms infinite}@keyframes spark273{0%{transform:translateY(0)}to{transform:rotate(338deg) translateX(975px)}}.bg-vectors .line:nth-child(274){transform:rotateY(196deg)}.bg-vectors .line:nth-child(274) .spark{animation:spark274 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:303px;width:207px}.bg-vectors .line:nth-child(274) .fire{animation:fire 1952ms linear -785ms infinite}@keyframes spark274{0%{transform:translateY(0)}to{transform:rotate(277deg) translateX(267px)}}.bg-vectors .line:nth-child(275){transform:rotateY(309deg)}.bg-vectors .line:nth-child(275) .spark{animation:spark275 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:235px;width:245px}.bg-vectors .line:nth-child(275) .fire{animation:fire 1491ms linear -291ms infinite}@keyframes spark275{0%{transform:translateY(0)}to{transform:rotate(275deg) translateX(607px)}}.bg-vectors .line:nth-child(276){transform:rotateY(345deg)}.bg-vectors .line:nth-child(276) .spark{animation:spark276 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:223px;width:224px}.bg-vectors .line:nth-child(276) .fire{animation:fire 1468ms linear -332ms infinite}@keyframes spark276{0%{transform:translateY(0)}to{transform:rotate(247deg) translateX(769px)}}.bg-vectors .line:nth-child(277){transform:rotateY(40deg)}.bg-vectors .line:nth-child(277) .spark{animation:spark277 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:274px;width:391px}.bg-vectors .line:nth-child(277) .fire{animation:fire 1.32s linear -933ms infinite}@keyframes spark277{0%{transform:translateY(0)}to{transform:rotate(6deg) translateX(998px)}}.bg-vectors .line:nth-child(278){transform:rotateY(243deg)}.bg-vectors .line:nth-child(278) .spark{animation:spark278 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:246px;width:355px}.bg-vectors .line:nth-child(278) .fire{animation:fire 1819ms linear -796ms infinite}@keyframes spark278{0%{transform:translateY(0)}to{transform:rotate(84deg) translateX(540px)}}.bg-vectors .line:nth-child(279){transform:rotateY(180deg)}.bg-vectors .line:nth-child(279) .spark{animation:spark279 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:321px;width:211px}.bg-vectors .line:nth-child(279) .fire{animation:fire 1156ms linear -314ms infinite}@keyframes spark279{0%{transform:translateY(0)}to{transform:rotate(353deg) translateX(297px)}}.bg-vectors .line:nth-child(280){transform:rotateY(94deg)}.bg-vectors .line:nth-child(280) .spark{animation:spark280 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:392px;width:400px}.bg-vectors .line:nth-child(280) .fire{animation:fire 1242ms linear -969ms infinite}@keyframes spark280{0%{transform:translateY(0)}to{transform:rotate(221deg) translateX(371px)}}.bg-vectors .line:nth-child(281){transform:rotateY(77deg)}.bg-vectors .line:nth-child(281) .spark{animation:spark281 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:302px;width:209px}.bg-vectors .line:nth-child(281) .fire{animation:fire 1368ms linear -834ms infinite}@keyframes spark281{0%{transform:translateY(0)}to{transform:rotate(154deg) translateX(985px)}}.bg-vectors .line:nth-child(282){transform:rotateY(150deg)}.bg-vectors .line:nth-child(282) .spark{animation:spark282 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:337px;width:389px}.bg-vectors .line:nth-child(282) .fire{animation:fire 1145ms linear -817ms infinite}@keyframes spark282{0%{transform:translateY(0)}to{transform:rotate(274deg) translateX(991px)}}.bg-vectors .line:nth-child(283){transform:rotateY(326deg)}.bg-vectors .line:nth-child(283) .spark{animation:spark283 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:368px;width:211px}.bg-vectors .line:nth-child(283) .fire{animation:fire 1056ms linear -437ms infinite}@keyframes spark283{0%{transform:translateY(0)}to{transform:rotate(167deg) translateX(433px)}}.bg-vectors .line:nth-child(284){transform:rotateY(354deg)}.bg-vectors .line:nth-child(284) .spark{animation:spark284 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:233px;width:322px}.bg-vectors .line:nth-child(284) .fire{animation:fire 1733ms linear -869ms infinite}@keyframes spark284{0%{transform:translateY(0)}to{transform:rotate(311deg) translateX(583px)}}.bg-vectors .line:nth-child(285){transform:rotateY(326deg)}.bg-vectors .line:nth-child(285) .spark{animation:spark285 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:204px;width:360px}.bg-vectors .line:nth-child(285) .fire{animation:fire 1258ms linear -1s infinite}@keyframes spark285{0%{transform:translateY(0)}to{transform:rotate(91deg) translateX(116px)}}.bg-vectors .line:nth-child(286){transform:rotateY(349deg)}.bg-vectors .line:nth-child(286) .spark{animation:spark286 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:358px;width:258px}.bg-vectors .line:nth-child(286) .fire{animation:fire 1409ms linear -135ms infinite}@keyframes spark286{0%{transform:translateY(0)}to{transform:rotate(279deg) translateX(283px)}}.bg-vectors .line:nth-child(287){transform:rotateY(68deg)}.bg-vectors .line:nth-child(287) .spark{animation:spark287 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:244px;width:330px}.bg-vectors .line:nth-child(287) .fire{animation:fire 1.08s linear -.77s infinite}@keyframes spark287{0%{transform:translateY(0)}to{transform:rotate(97deg) translateX(570px)}}.bg-vectors .line:nth-child(288){transform:rotateY(139deg)}.bg-vectors .line:nth-child(288) .spark{animation:spark288 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:267px;width:262px}.bg-vectors .line:nth-child(288) .fire{animation:fire 1265ms linear -863ms infinite}@keyframes spark288{0%{transform:translateY(0)}to{transform:rotate(159deg) translateX(224px)}}.bg-vectors .line:nth-child(289){transform:rotateY(190deg)}.bg-vectors .line:nth-child(289) .spark{animation:spark289 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:248px;width:317px}.bg-vectors .line:nth-child(289) .fire{animation:fire 1676ms linear -128ms infinite}@keyframes spark289{0%{transform:translateY(0)}to{transform:rotate(184deg) translateX(1001px)}}.bg-vectors .line:nth-child(290){transform:rotateY(118deg)}.bg-vectors .line:nth-child(290) .spark{animation:spark290 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:348px;width:238px}.bg-vectors .line:nth-child(290) .fire{animation:fire 1.18s linear -.5s infinite}@keyframes spark290{0%{transform:translateY(0)}to{transform:rotate(264deg) translateX(1084px)}}.bg-vectors .line:nth-child(291){transform:rotateY(321deg)}.bg-vectors .line:nth-child(291) .spark{animation:spark291 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:218px;width:204px}.bg-vectors .line:nth-child(291) .fire{animation:fire 1686ms linear -479ms infinite}@keyframes spark291{0%{transform:translateY(0)}to{transform:rotate(323deg) translateX(650px)}}.bg-vectors .line:nth-child(292){transform:rotateY(352deg)}.bg-vectors .line:nth-child(292) .spark{animation:spark292 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:287px;width:348px}.bg-vectors .line:nth-child(292) .fire{animation:fire 1495ms linear -134ms infinite}@keyframes spark292{0%{transform:translateY(0)}to{transform:rotate(297deg) translateX(803px)}}.bg-vectors .line:nth-child(293){transform:rotateY(205deg)}.bg-vectors .line:nth-child(293) .spark{animation:spark293 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:212px;width:292px}.bg-vectors .line:nth-child(293) .fire{animation:fire 1506ms linear -795ms infinite}@keyframes spark293{0%{transform:translateY(0)}to{transform:rotate(160deg) translateX(185px)}}.bg-vectors .line:nth-child(294){transform:rotateY(78deg)}.bg-vectors .line:nth-child(294) .spark{animation:spark294 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:338px;width:202px}.bg-vectors .line:nth-child(294) .fire{animation:fire 1043ms linear -828ms infinite}@keyframes spark294{0%{transform:translateY(0)}to{transform:rotate(259deg) translateX(1068px)}}.bg-vectors .line:nth-child(295){transform:rotateY(155deg)}.bg-vectors .line:nth-child(295) .spark{animation:spark295 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:314px;width:304px}.bg-vectors .line:nth-child(295) .fire{animation:fire 1662ms linear -459ms infinite}@keyframes spark295{0%{transform:translateY(0)}to{transform:rotate(232deg) translateX(454px)}}.bg-vectors .line:nth-child(296){transform:rotateY(301deg)}.bg-vectors .line:nth-child(296) .spark{animation:spark296 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:329px;width:348px}.bg-vectors .line:nth-child(296) .fire{animation:fire 1893ms linear -968ms infinite}@keyframes spark296{0%{transform:translateY(0)}to{transform:rotate(54deg) translateX(557px)}}.bg-vectors .line:nth-child(297){transform:rotateY(327deg)}.bg-vectors .line:nth-child(297) .spark{animation:spark297 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:284px;width:207px}.bg-vectors .line:nth-child(297) .fire{animation:fire 1271ms linear -454ms infinite}@keyframes spark297{0%{transform:translateY(0)}to{transform:rotate(29deg) translateX(385px)}}.bg-vectors .line:nth-child(298){transform:rotateY(124deg)}.bg-vectors .line:nth-child(298) .spark{animation:spark298 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:261px;width:292px}.bg-vectors .line:nth-child(298) .fire{animation:fire 1769ms linear -128ms infinite}@keyframes spark298{0%{transform:translateY(0)}to{transform:rotate(294deg) translateX(1074px)}}.bg-vectors .line:nth-child(299){transform:rotateY(301deg)}.bg-vectors .line:nth-child(299) .spark{animation:spark299 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:398px;width:211px}.bg-vectors .line:nth-child(299) .fire{animation:fire 1772ms linear -482ms infinite}@keyframes spark299{0%{transform:translateY(0)}to{transform:rotate(353deg) translateX(528px)}}.bg-vectors .line:nth-child(300){transform:rotateY(166deg)}.bg-vectors .line:nth-child(300) .spark{animation:spark300 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:366px;width:378px}.bg-vectors .line:nth-child(300) .fire{animation:fire 1182ms linear -783ms infinite}@keyframes spark300{0%{transform:translateY(0)}to{transform:rotate(301deg) translateX(683px)}}.bg-vectors .line:nth-child(301){transform:rotateY(120deg)}.bg-vectors .line:nth-child(301) .spark{animation:spark301 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:316px;width:345px}.bg-vectors .line:nth-child(301) .fire{animation:fire 1876ms linear -507ms infinite}@keyframes spark301{0%{transform:translateY(0)}to{transform:rotate(52deg) translateX(495px)}}.bg-vectors .line:nth-child(302){transform:rotateY(15deg)}.bg-vectors .line:nth-child(302) .spark{animation:spark302 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:398px;width:270px}.bg-vectors .line:nth-child(302) .fire{animation:fire 1657ms linear -951ms infinite}@keyframes spark302{0%{transform:translateY(0)}to{transform:rotate(241deg) translateX(444px)}}.bg-vectors .line:nth-child(303){transform:rotateY(247deg)}.bg-vectors .line:nth-child(303) .spark{animation:spark303 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:378px;width:363px}.bg-vectors .line:nth-child(303) .fire{animation:fire 1489ms linear -186ms infinite}@keyframes spark303{0%{transform:translateY(0)}to{transform:rotate(308deg) translateX(101px)}}.bg-vectors .line:nth-child(304){transform:rotateY(202deg)}.bg-vectors .line:nth-child(304) .spark{animation:spark304 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:268px;width:248px}.bg-vectors .line:nth-child(304) .fire{animation:fire 1712ms linear -777ms infinite}@keyframes spark304{0%{transform:translateY(0)}to{transform:rotate(359deg) translateX(1031px)}}.bg-vectors .line:nth-child(305){transform:rotateY(259deg)}.bg-vectors .line:nth-child(305) .spark{animation:spark305 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:328px;width:207px}.bg-vectors .line:nth-child(305) .fire{animation:fire 1358ms linear -34ms infinite}@keyframes spark305{0%{transform:translateY(0)}to{transform:rotate(142deg) translateX(736px)}}.bg-vectors .line:nth-child(306){transform:rotateY(321deg)}.bg-vectors .line:nth-child(306) .spark{animation:spark306 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:295px;width:236px}.bg-vectors .line:nth-child(306) .fire{animation:fire 1846ms linear -39ms infinite}@keyframes spark306{0%{transform:translateY(0)}to{transform:rotate(305deg) translateX(492px)}}.bg-vectors .line:nth-child(307){transform:rotateY(259deg)}.bg-vectors .line:nth-child(307) .spark{animation:spark307 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:346px;width:379px}.bg-vectors .line:nth-child(307) .fire{animation:fire 1375ms linear -332ms infinite}@keyframes spark307{0%{transform:translateY(0)}to{transform:rotate(104deg) translateX(392px)}}.bg-vectors .line:nth-child(308){transform:rotateY(241deg)}.bg-vectors .line:nth-child(308) .spark{animation:spark308 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:324px;width:217px}.bg-vectors .line:nth-child(308) .fire{animation:fire 1712ms linear -.73s infinite}@keyframes spark308{0%{transform:translateY(0)}to{transform:rotate(79deg) translateX(1055px)}}.bg-vectors .line:nth-child(309){transform:rotateY(312deg)}.bg-vectors .line:nth-child(309) .spark{animation:spark309 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:276px;width:263px}.bg-vectors .line:nth-child(309) .fire{animation:fire 1547ms linear -.97s infinite}@keyframes spark309{0%{transform:translateY(0)}to{transform:rotate(302deg) translateX(350px)}}.bg-vectors .line:nth-child(310){transform:rotateY(227deg)}.bg-vectors .line:nth-child(310) .spark{animation:spark310 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:354px;width:221px}.bg-vectors .line:nth-child(310) .fire{animation:fire 1314ms linear -116ms infinite}@keyframes spark310{0%{transform:translateY(0)}to{transform:rotate(68deg) translateX(637px)}}.bg-vectors .line:nth-child(311){transform:rotateY(275deg)}.bg-vectors .line:nth-child(311) .spark{animation:spark311 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:265px;width:387px}.bg-vectors .line:nth-child(311) .fire{animation:fire 1815ms linear -.1s infinite}@keyframes spark311{0%{transform:translateY(0)}to{transform:rotate(277deg) translateX(309px)}}.bg-vectors .line:nth-child(312){transform:rotateY(357deg)}.bg-vectors .line:nth-child(312) .spark{animation:spark312 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:282px;width:265px}.bg-vectors .line:nth-child(312) .fire{animation:fire 1892ms linear -298ms infinite}@keyframes spark312{0%{transform:translateY(0)}to{transform:rotate(96deg) translateX(982px)}}.bg-vectors .line:nth-child(313){transform:rotateY(355deg)}.bg-vectors .line:nth-child(313) .spark{animation:spark313 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:235px;width:389px}.bg-vectors .line:nth-child(313) .fire{animation:fire 1458ms linear -63ms infinite}@keyframes spark313{0%{transform:translateY(0)}to{transform:rotate(344deg) translateX(213px)}}.bg-vectors .line:nth-child(314){transform:rotateY(40deg)}.bg-vectors .line:nth-child(314) .spark{animation:spark314 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:350px;width:399px}.bg-vectors .line:nth-child(314) .fire{animation:fire 1058ms linear -753ms infinite}@keyframes spark314{0%{transform:translateY(0)}to{transform:rotate(58deg) translateX(470px)}}.bg-vectors .line:nth-child(315){transform:rotateY(273deg)}.bg-vectors .line:nth-child(315) .spark{animation:spark315 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:312px;width:369px}.bg-vectors .line:nth-child(315) .fire{animation:fire 1.15s linear -572ms infinite}@keyframes spark315{0%{transform:translateY(0)}to{transform:rotate(265deg) translateX(755px)}}.bg-vectors .line:nth-child(316){transform:rotateY(76deg)}.bg-vectors .line:nth-child(316) .spark{animation:spark316 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:294px;width:252px}.bg-vectors .line:nth-child(316) .fire{animation:fire 1825ms linear -261ms infinite}@keyframes spark316{0%{transform:translateY(0)}to{transform:rotate(213deg) translateX(679px)}}.bg-vectors .line:nth-child(317){transform:rotateY(326deg)}.bg-vectors .line:nth-child(317) .spark{animation:spark317 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:338px;width:376px}.bg-vectors .line:nth-child(317) .fire{animation:fire 1428ms linear -711ms infinite}@keyframes spark317{0%{transform:translateY(0)}to{transform:rotate(335deg) translateX(1048px)}}.bg-vectors .line:nth-child(318){transform:rotateY(312deg)}.bg-vectors .line:nth-child(318) .spark{animation:spark318 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:328px;width:257px}.bg-vectors .line:nth-child(318) .fire{animation:fire 1141ms linear -88ms infinite}@keyframes spark318{0%{transform:translateY(0)}to{transform:rotate(334deg) translateX(1054px)}}.bg-vectors .line:nth-child(319){transform:rotateY(110deg)}.bg-vectors .line:nth-child(319) .spark{animation:spark319 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:386px;width:348px}.bg-vectors .line:nth-child(319) .fire{animation:fire 1.16s linear -358ms infinite}@keyframes spark319{0%{transform:translateY(0)}to{transform:rotate(256deg) translateX(648px)}}.bg-vectors .line:nth-child(320){transform:rotateY(216deg)}.bg-vectors .line:nth-child(320) .spark{animation:spark320 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:248px;width:382px}.bg-vectors .line:nth-child(320) .fire{animation:fire 1321ms linear -28ms infinite}@keyframes spark320{0%{transform:translateY(0)}to{transform:rotate(225deg) translateX(589px)}}.bg-vectors .line:nth-child(321){transform:rotateY(249deg)}.bg-vectors .line:nth-child(321) .spark{animation:spark321 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:328px;width:356px}.bg-vectors .line:nth-child(321) .fire{animation:fire 1.4s linear -168ms infinite}@keyframes spark321{0%{transform:translateY(0)}to{transform:rotate(325deg) translateX(703px)}}.bg-vectors .line:nth-child(322){transform:rotateY(158deg)}.bg-vectors .line:nth-child(322) .spark{animation:spark322 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:385px;width:208px}.bg-vectors .line:nth-child(322) .fire{animation:fire 1523ms linear -47ms infinite}@keyframes spark322{0%{transform:translateY(0)}to{transform:rotate(131deg) translateX(944px)}}.bg-vectors .line:nth-child(323){transform:rotateY(147deg)}.bg-vectors .line:nth-child(323) .spark{animation:spark323 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:396px;width:314px}.bg-vectors .line:nth-child(323) .fire{animation:fire 1124ms linear -354ms infinite}@keyframes spark323{0%{transform:translateY(0)}to{transform:rotate(88deg) translateX(980px)}}.bg-vectors .line:nth-child(324){transform:rotateY(59deg)}.bg-vectors .line:nth-child(324) .spark{animation:spark324 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:387px;width:330px}.bg-vectors .line:nth-child(324) .fire{animation:fire 1284ms linear -456ms infinite}@keyframes spark324{0%{transform:translateY(0)}to{transform:rotate(308deg) translateX(791px)}}.bg-vectors .line:nth-child(325){transform:rotateY(243deg)}.bg-vectors .line:nth-child(325) .spark{animation:spark325 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:333px;width:296px}.bg-vectors .line:nth-child(325) .fire{animation:fire 1804ms linear -167ms infinite}@keyframes spark325{0%{transform:translateY(0)}to{transform:rotate(299deg) translateX(873px)}}.bg-vectors .line:nth-child(326){transform:rotateY(288deg)}.bg-vectors .line:nth-child(326) .spark{animation:spark326 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:297px;width:370px}.bg-vectors .line:nth-child(326) .fire{animation:fire 1432ms linear -505ms infinite}@keyframes spark326{0%{transform:translateY(0)}to{transform:rotate(76deg) translateX(771px)}}.bg-vectors .line:nth-child(327){transform:rotateY(91deg)}.bg-vectors .line:nth-child(327) .spark{animation:spark327 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:374px;width:228px}.bg-vectors .line:nth-child(327) .fire{animation:fire 1853ms linear -616ms infinite}@keyframes spark327{0%{transform:translateY(0)}to{transform:rotate(31deg) translateX(403px)}}.bg-vectors .line:nth-child(328){transform:rotateY(229deg)}.bg-vectors .line:nth-child(328) .spark{animation:spark328 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:319px;width:330px}.bg-vectors .line:nth-child(328) .fire{animation:fire 1083ms linear -381ms infinite}@keyframes spark328{0%{transform:translateY(0)}to{transform:rotate(1deg) translateX(341px)}}.bg-vectors .line:nth-child(329){transform:rotateY(69deg)}.bg-vectors .line:nth-child(329) .spark{animation:spark329 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:373px;width:392px}.bg-vectors .line:nth-child(329) .fire{animation:fire 1933ms linear -359ms infinite}@keyframes spark329{0%{transform:translateY(0)}to{transform:rotate(170deg) translateX(939px)}}.bg-vectors .line:nth-child(330){transform:rotateY(25deg)}.bg-vectors .line:nth-child(330) .spark{animation:spark330 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:383px;width:261px}.bg-vectors .line:nth-child(330) .fire{animation:fire 1949ms linear -.65s infinite}@keyframes spark330{0%{transform:translateY(0)}to{transform:rotate(306deg) translateX(333px)}}.bg-vectors .line:nth-child(331){transform:rotateY(200deg)}.bg-vectors .line:nth-child(331) .spark{animation:spark331 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:213px;width:299px}.bg-vectors .line:nth-child(331) .fire{animation:fire 1491ms linear -826ms infinite}@keyframes spark331{0%{transform:translateY(0)}to{transform:rotate(260deg) translateX(775px)}}.bg-vectors .line:nth-child(332){transform:rotateY(123deg)}.bg-vectors .line:nth-child(332) .spark{animation:spark332 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:280px;width:398px}.bg-vectors .line:nth-child(332) .fire{animation:fire 1204ms linear -749ms infinite}@keyframes spark332{0%{transform:translateY(0)}to{transform:rotate(259deg) translateX(139px)}}.bg-vectors .line:nth-child(333){transform:rotateY(152deg)}.bg-vectors .line:nth-child(333) .spark{animation:spark333 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:285px;width:305px}.bg-vectors .line:nth-child(333) .fire{animation:fire 1.37s linear -961ms infinite}@keyframes spark333{0%{transform:translateY(0)}to{transform:rotate(11deg) translateX(1019px)}}.bg-vectors .line:nth-child(334){transform:rotateY(130deg)}.bg-vectors .line:nth-child(334) .spark{animation:spark334 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:363px;width:318px}.bg-vectors .line:nth-child(334) .fire{animation:fire 1649ms linear -803ms infinite}@keyframes spark334{0%{transform:translateY(0)}to{transform:rotate(257deg) translateX(867px)}}.bg-vectors .line:nth-child(335){transform:rotateY(7deg)}.bg-vectors .line:nth-child(335) .spark{animation:spark335 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:321px;width:258px}.bg-vectors .line:nth-child(335) .fire{animation:fire 1804ms linear -597ms infinite}@keyframes spark335{0%{transform:translateY(0)}to{transform:rotate(338deg) translateX(430px)}}.bg-vectors .line:nth-child(336){transform:rotateY(267deg)}.bg-vectors .line:nth-child(336) .spark{animation:spark336 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:354px;width:262px}.bg-vectors .line:nth-child(336) .fire{animation:fire 1932ms linear -.53s infinite}@keyframes spark336{0%{transform:translateY(0)}to{transform:rotate(281deg) translateX(868px)}}.bg-vectors .line:nth-child(337){transform:rotateY(1turn)}.bg-vectors .line:nth-child(337) .spark{animation:spark337 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:372px;width:364px}.bg-vectors .line:nth-child(337) .fire{animation:fire 1378ms linear -913ms infinite}@keyframes spark337{0%{transform:translateY(0)}to{transform:rotate(176deg) translateX(1034px)}}.bg-vectors .line:nth-child(338){transform:rotateY(327deg)}.bg-vectors .line:nth-child(338) .spark{animation:spark338 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:398px;width:379px}.bg-vectors .line:nth-child(338) .fire{animation:fire 1428ms linear -393ms infinite}@keyframes spark338{0%{transform:translateY(0)}to{transform:rotate(19deg) translateX(863px)}}.bg-vectors .line:nth-child(339){transform:rotateY(216deg)}.bg-vectors .line:nth-child(339) .spark{animation:spark339 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:276px;width:260px}.bg-vectors .line:nth-child(339) .fire{animation:fire 1862ms linear -342ms infinite}@keyframes spark339{0%{transform:translateY(0)}to{transform:rotate(211deg) translateX(1010px)}}.bg-vectors .line:nth-child(340){transform:rotateY(13deg)}.bg-vectors .line:nth-child(340) .spark{animation:spark340 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:263px;width:277px}.bg-vectors .line:nth-child(340) .fire{animation:fire 1865ms linear -942ms infinite}@keyframes spark340{0%{transform:translateY(0)}to{transform:rotate(33deg) translateX(329px)}}.bg-vectors .line:nth-child(341){transform:rotateY(107deg)}.bg-vectors .line:nth-child(341) .spark{animation:spark341 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:284px;width:357px}.bg-vectors .line:nth-child(341) .fire{animation:fire 1673ms linear -487ms infinite}@keyframes spark341{0%{transform:translateY(0)}to{transform:rotate(85deg) translateX(963px)}}.bg-vectors .line:nth-child(342){transform:rotateY(278deg)}.bg-vectors .line:nth-child(342) .spark{animation:spark342 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:363px;width:357px}.bg-vectors .line:nth-child(342) .fire{animation:fire 1343ms linear -907ms infinite}@keyframes spark342{0%{transform:translateY(0)}to{transform:rotate(232deg) translateX(259px)}}.bg-vectors .line:nth-child(343){transform:rotateY(150deg)}.bg-vectors .line:nth-child(343) .spark{animation:spark343 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:273px;width:246px}.bg-vectors .line:nth-child(343) .fire{animation:fire 1.33s linear -316ms infinite}@keyframes spark343{0%{transform:translateY(0)}to{transform:rotate(248deg) translateX(479px)}}.bg-vectors .line:nth-child(344){transform:rotateY(129deg)}.bg-vectors .line:nth-child(344) .spark{animation:spark344 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:390px;width:266px}.bg-vectors .line:nth-child(344) .fire{animation:fire 1947ms linear -976ms infinite}@keyframes spark344{0%{transform:translateY(0)}to{transform:rotate(197deg) translateX(265px)}}.bg-vectors .line:nth-child(345){transform:rotateY(257deg)}.bg-vectors .line:nth-child(345) .spark{animation:spark345 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:216px;width:303px}.bg-vectors .line:nth-child(345) .fire{animation:fire 1727ms linear -718ms infinite}@keyframes spark345{0%{transform:translateY(0)}to{transform:rotate(69deg) translateX(1044px)}}.bg-vectors .line:nth-child(346){transform:rotateY(261deg)}.bg-vectors .line:nth-child(346) .spark{animation:spark346 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:217px;width:339px}.bg-vectors .line:nth-child(346) .fire{animation:fire 1946ms linear -865ms infinite}@keyframes spark346{0%{transform:translateY(0)}to{transform:rotate(75deg) translateX(960px)}}.bg-vectors .line:nth-child(347){transform:rotateY(262deg)}.bg-vectors .line:nth-child(347) .spark{animation:spark347 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:224px;width:285px}.bg-vectors .line:nth-child(347) .fire{animation:fire 1228ms linear -108ms infinite}@keyframes spark347{0%{transform:translateY(0)}to{transform:rotate(318deg) translateX(642px)}}.bg-vectors .line:nth-child(348){transform:rotateY(85deg)}.bg-vectors .line:nth-child(348) .spark{animation:spark348 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:202px;width:249px}.bg-vectors .line:nth-child(348) .fire{animation:fire 1824ms linear -531ms infinite}@keyframes spark348{0%{transform:translateY(0)}to{transform:rotate(230deg) translateX(635px)}}.bg-vectors .line:nth-child(349){transform:rotateY(274deg)}.bg-vectors .line:nth-child(349) .spark{animation:spark349 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:340px;width:209px}.bg-vectors .line:nth-child(349) .fire{animation:fire 1.07s linear -209ms infinite}@keyframes spark349{0%{transform:translateY(0)}to{transform:rotate(1turn) translateX(646px)}}.bg-vectors .line:nth-child(350){transform:rotateY(350deg)}.bg-vectors .line:nth-child(350) .spark{animation:spark350 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:302px;width:233px}.bg-vectors .line:nth-child(350) .fire{animation:fire 1269ms linear -965ms infinite}@keyframes spark350{0%{transform:translateY(0)}to{transform:rotate(269deg) translateX(929px)}}.bg-vectors .line:nth-child(351){transform:rotateY(238deg)}.bg-vectors .line:nth-child(351) .spark{animation:spark351 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:330px;width:292px}.bg-vectors .line:nth-child(351) .fire{animation:fire 1022ms linear -96ms infinite}@keyframes spark351{0%{transform:translateY(0)}to{transform:rotate(45deg) translateX(668px)}}.bg-vectors .line:nth-child(352){transform:rotateY(177deg)}.bg-vectors .line:nth-child(352) .spark{animation:spark352 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:234px;width:298px}.bg-vectors .line:nth-child(352) .fire{animation:fire 1558ms linear -556ms infinite}@keyframes spark352{0%{transform:translateY(0)}to{transform:rotate(285deg) translateX(590px)}}.bg-vectors .line:nth-child(353){transform:rotateY(343deg)}.bg-vectors .line:nth-child(353) .spark{animation:spark353 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:336px;width:326px}.bg-vectors .line:nth-child(353) .fire{animation:fire 1262ms linear -792ms infinite}@keyframes spark353{0%{transform:translateY(0)}to{transform:rotate(168deg) translateX(578px)}}.bg-vectors .line:nth-child(354){transform:rotateY(213deg)}.bg-vectors .line:nth-child(354) .spark{animation:spark354 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:227px;width:275px}.bg-vectors .line:nth-child(354) .fire{animation:fire 1166ms linear -47ms infinite}@keyframes spark354{0%{transform:translateY(0)}to{transform:rotate(47deg) translateX(134px)}}.bg-vectors .line:nth-child(355){transform:rotateY(159deg)}.bg-vectors .line:nth-child(355) .spark{animation:spark355 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:337px;width:305px}.bg-vectors .line:nth-child(355) .fire{animation:fire 1093ms linear -919ms infinite}@keyframes spark355{0%{transform:translateY(0)}to{transform:rotate(244deg) translateX(1036px)}}.bg-vectors .line:nth-child(356){transform:rotateY(196deg)}.bg-vectors .line:nth-child(356) .spark{animation:spark356 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:347px;width:333px}.bg-vectors .line:nth-child(356) .fire{animation:fire 1666ms linear -768ms infinite}@keyframes spark356{0%{transform:translateY(0)}to{transform:rotate(183deg) translateX(854px)}}.bg-vectors .line:nth-child(357){transform:rotateY(4deg)}.bg-vectors .line:nth-child(357) .spark{animation:spark357 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:320px;width:288px}.bg-vectors .line:nth-child(357) .fire{animation:fire 1492ms linear -289ms infinite}@keyframes spark357{0%{transform:translateY(0)}to{transform:rotate(45deg) translateX(157px)}}.bg-vectors .line:nth-child(358){transform:rotateY(77deg)}.bg-vectors .line:nth-child(358) .spark{animation:spark358 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:243px;width:399px}.bg-vectors .line:nth-child(358) .fire{animation:fire 1583ms linear -518ms infinite}@keyframes spark358{0%{transform:translateY(0)}to{transform:rotate(356deg) translateX(790px)}}.bg-vectors .line:nth-child(359){transform:rotateY(119deg)}.bg-vectors .line:nth-child(359) .spark{animation:spark359 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:228px;width:375px}.bg-vectors .line:nth-child(359) .fire{animation:fire 1523ms linear -.28s infinite}@keyframes spark359{0%{transform:translateY(0)}to{transform:rotate(96deg) translateX(435px)}}.bg-vectors .line:nth-child(360){transform:rotateY(247deg)}.bg-vectors .line:nth-child(360) .spark{animation:spark360 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:348px;width:322px}.bg-vectors .line:nth-child(360) .fire{animation:fire 1157ms linear -855ms infinite}@keyframes spark360{0%{transform:translateY(0)}to{transform:rotate(162deg) translateX(177px)}}.bg-vectors .line:nth-child(361){transform:rotateY(59deg)}.bg-vectors .line:nth-child(361) .spark{animation:spark361 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:302px;width:337px}.bg-vectors .line:nth-child(361) .fire{animation:fire 1704ms linear -961ms infinite}@keyframes spark361{0%{transform:translateY(0)}to{transform:rotate(16deg) translateX(644px)}}.bg-vectors .line:nth-child(362){transform:rotateY(270deg)}.bg-vectors .line:nth-child(362) .spark{animation:spark362 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:349px;width:291px}.bg-vectors .line:nth-child(362) .fire{animation:fire 1026ms linear -162ms infinite}@keyframes spark362{0%{transform:translateY(0)}to{transform:rotate(112deg) translateX(476px)}}.bg-vectors .line:nth-child(363){transform:rotateY(211deg)}.bg-vectors .line:nth-child(363) .spark{animation:spark363 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:276px;width:371px}.bg-vectors .line:nth-child(363) .fire{animation:fire 1676ms linear -14ms infinite}@keyframes spark363{0%{transform:translateY(0)}to{transform:rotate(163deg) translateX(608px)}}.bg-vectors .line:nth-child(364){transform:rotateY(271deg)}.bg-vectors .line:nth-child(364) .spark{animation:spark364 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:389px;width:383px}.bg-vectors .line:nth-child(364) .fire{animation:fire 1115ms linear -822ms infinite}@keyframes spark364{0%{transform:translateY(0)}to{transform:rotate(240deg) translateX(349px)}}.bg-vectors .line:nth-child(365){transform:rotateY(218deg)}.bg-vectors .line:nth-child(365) .spark{animation:spark365 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:207px;width:340px}.bg-vectors .line:nth-child(365) .fire{animation:fire 1852ms linear -654ms infinite}@keyframes spark365{0%{transform:translateY(0)}to{transform:rotate(125deg) translateX(677px)}}.bg-vectors .line:nth-child(366){transform:rotateY(305deg)}.bg-vectors .line:nth-child(366) .spark{animation:spark366 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:222px;width:295px}.bg-vectors .line:nth-child(366) .fire{animation:fire 1092ms linear -778ms infinite}@keyframes spark366{0%{transform:translateY(0)}to{transform:rotate(143deg) translateX(226px)}}.bg-vectors .line:nth-child(367){transform:rotateY(343deg)}.bg-vectors .line:nth-child(367) .spark{animation:spark367 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:355px;width:235px}.bg-vectors .line:nth-child(367) .fire{animation:fire 1495ms linear -257ms infinite}@keyframes spark367{0%{transform:translateY(0)}to{transform:rotate(111deg) translateX(496px)}}.bg-vectors .line:nth-child(368){transform:rotateY(76deg)}.bg-vectors .line:nth-child(368) .spark{animation:spark368 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:399px;width:397px}.bg-vectors .line:nth-child(368) .fire{animation:fire 1185ms linear -4ms infinite}@keyframes spark368{0%{transform:translateY(0)}to{transform:rotate(239deg) translateX(455px)}}.bg-vectors .line:nth-child(369){transform:rotateY(234deg)}.bg-vectors .line:nth-child(369) .spark{animation:spark369 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:366px;width:395px}.bg-vectors .line:nth-child(369) .fire{animation:fire 1966ms linear -643ms infinite}@keyframes spark369{0%{transform:translateY(0)}to{transform:rotate(184deg) translateX(849px)}}.bg-vectors .line:nth-child(370){transform:rotateY(186deg)}.bg-vectors .line:nth-child(370) .spark{animation:spark370 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:352px;width:300px}.bg-vectors .line:nth-child(370) .fire{animation:fire 1349ms linear -536ms infinite}@keyframes spark370{0%{transform:translateY(0)}to{transform:rotate(87deg) translateX(1083px)}}.bg-vectors .line:nth-child(371){transform:rotateY(223deg)}.bg-vectors .line:nth-child(371) .spark{animation:spark371 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:379px;width:268px}.bg-vectors .line:nth-child(371) .fire{animation:fire 1062ms linear -232ms infinite}@keyframes spark371{0%{transform:translateY(0)}to{transform:rotate(281deg) translateX(1036px)}}.bg-vectors .line:nth-child(372){transform:rotateY(8deg)}.bg-vectors .line:nth-child(372) .spark{animation:spark372 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:320px;width:370px}.bg-vectors .line:nth-child(372) .fire{animation:fire 1698ms linear -37ms infinite}@keyframes spark372{0%{transform:translateY(0)}to{transform:rotate(340deg) translateX(707px)}}.bg-vectors .line:nth-child(373){transform:rotateY(203deg)}.bg-vectors .line:nth-child(373) .spark{animation:spark373 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:333px;width:311px}.bg-vectors .line:nth-child(373) .fire{animation:fire 1618ms linear -153ms infinite}@keyframes spark373{0%{transform:translateY(0)}to{transform:rotate(69deg) translateX(1079px)}}.bg-vectors .line:nth-child(374){transform:rotateY(359deg)}.bg-vectors .line:nth-child(374) .spark{animation:spark374 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:316px;width:237px}.bg-vectors .line:nth-child(374) .fire{animation:fire 1995ms linear -673ms infinite}@keyframes spark374{0%{transform:translateY(0)}to{transform:rotate(259deg) translateX(896px)}}.bg-vectors .line:nth-child(375){transform:rotateY(308deg)}.bg-vectors .line:nth-child(375) .spark{animation:spark375 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:267px;width:299px}.bg-vectors .line:nth-child(375) .fire{animation:fire 1479ms linear -909ms infinite}@keyframes spark375{0%{transform:translateY(0)}to{transform:rotate(270deg) translateX(150px)}}.bg-vectors .line:nth-child(376){transform:rotateY(212deg)}.bg-vectors .line:nth-child(376) .spark{animation:spark376 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:336px;width:301px}.bg-vectors .line:nth-child(376) .fire{animation:fire 1124ms linear -19ms infinite}@keyframes spark376{0%{transform:translateY(0)}to{transform:rotate(51deg) translateX(1012px)}}.bg-vectors .line:nth-child(377){transform:rotateY(338deg)}.bg-vectors .line:nth-child(377) .spark{animation:spark377 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:330px;width:301px}.bg-vectors .line:nth-child(377) .fire{animation:fire 1741ms linear -992ms infinite}@keyframes spark377{0%{transform:translateY(0)}to{transform:rotate(56deg) translateX(341px)}}.bg-vectors .line:nth-child(378){transform:rotateY(106deg)}.bg-vectors .line:nth-child(378) .spark{animation:spark378 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:259px;width:356px}.bg-vectors .line:nth-child(378) .fire{animation:fire 1754ms linear -192ms infinite}@keyframes spark378{0%{transform:translateY(0)}to{transform:rotate(168deg) translateX(982px)}}.bg-vectors .line:nth-child(379){transform:rotateY(104deg)}.bg-vectors .line:nth-child(379) .spark{animation:spark379 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:288px;width:395px}.bg-vectors .line:nth-child(379) .fire{animation:fire 1555ms linear -483ms infinite}@keyframes spark379{0%{transform:translateY(0)}to{transform:rotate(24deg) translateX(162px)}}.bg-vectors .line:nth-child(380){transform:rotateY(87deg)}.bg-vectors .line:nth-child(380) .spark{animation:spark380 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:278px;width:263px}.bg-vectors .line:nth-child(380) .fire{animation:fire 1425ms linear -275ms infinite}@keyframes spark380{0%{transform:translateY(0)}to{transform:rotate(47deg) translateX(801px)}}.bg-vectors .line:nth-child(381){transform:rotateY(273deg)}.bg-vectors .line:nth-child(381) .spark{animation:spark381 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:345px;width:312px}.bg-vectors .line:nth-child(381) .fire{animation:fire 1.59s linear -683ms infinite}@keyframes spark381{0%{transform:translateY(0)}to{transform:rotate(193deg) translateX(850px)}}.bg-vectors .line:nth-child(382){transform:rotateY(299deg)}.bg-vectors .line:nth-child(382) .spark{animation:spark382 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:267px;width:382px}.bg-vectors .line:nth-child(382) .fire{animation:fire 1685ms linear -556ms infinite}@keyframes spark382{0%{transform:translateY(0)}to{transform:rotate(30deg) translateX(952px)}}.bg-vectors .line:nth-child(383){transform:rotateY(50deg)}.bg-vectors .line:nth-child(383) .spark{animation:spark383 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:245px;width:384px}.bg-vectors .line:nth-child(383) .fire{animation:fire 1429ms linear -746ms infinite}@keyframes spark383{0%{transform:translateY(0)}to{transform:rotate(22deg) translateX(404px)}}.bg-vectors .line:nth-child(384){transform:rotateY(174deg)}.bg-vectors .line:nth-child(384) .spark{animation:spark384 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:244px;width:278px}.bg-vectors .line:nth-child(384) .fire{animation:fire 1794ms linear -493ms infinite}@keyframes spark384{0%{transform:translateY(0)}to{transform:rotate(231deg) translateX(540px)}}.bg-vectors .line:nth-child(385){transform:rotateY(299deg)}.bg-vectors .line:nth-child(385) .spark{animation:spark385 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:260px;width:202px}.bg-vectors .line:nth-child(385) .fire{animation:fire 1.59s linear -44ms infinite}@keyframes spark385{0%{transform:translateY(0)}to{transform:rotate(146deg) translateX(835px)}}.bg-vectors .line:nth-child(386){transform:rotateY(80deg)}.bg-vectors .line:nth-child(386) .spark{animation:spark386 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:388px;width:241px}.bg-vectors .line:nth-child(386) .fire{animation:fire 1463ms linear -275ms infinite}@keyframes spark386{0%{transform:translateY(0)}to{transform:rotate(80deg) translateX(591px)}}.bg-vectors .line:nth-child(387){transform:rotateY(203deg)}.bg-vectors .line:nth-child(387) .spark{animation:spark387 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:207px;width:294px}.bg-vectors .line:nth-child(387) .fire{animation:fire 1.19s linear -.28s infinite}@keyframes spark387{0%{transform:translateY(0)}to{transform:rotate(97deg) translateX(296px)}}.bg-vectors .line:nth-child(388){transform:rotateY(336deg)}.bg-vectors .line:nth-child(388) .spark{animation:spark388 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:244px;width:216px}.bg-vectors .line:nth-child(388) .fire{animation:fire 1795ms linear -574ms infinite}@keyframes spark388{0%{transform:translateY(0)}to{transform:rotate(201deg) translateX(990px)}}.bg-vectors .line:nth-child(389){transform:rotateY(147deg)}.bg-vectors .line:nth-child(389) .spark{animation:spark389 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:272px;width:335px}.bg-vectors .line:nth-child(389) .fire{animation:fire 1709ms linear -156ms infinite}@keyframes spark389{0%{transform:translateY(0)}to{transform:rotate(275deg) translateX(924px)}}.bg-vectors .line:nth-child(390){transform:rotateY(299deg)}.bg-vectors .line:nth-child(390) .spark{animation:spark390 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:247px;width:388px}.bg-vectors .line:nth-child(390) .fire{animation:fire 1.37s linear -949ms infinite}@keyframes spark390{0%{transform:translateY(0)}to{transform:rotate(50deg) translateX(575px)}}.bg-vectors .line:nth-child(391){transform:rotateY(343deg)}.bg-vectors .line:nth-child(391) .spark{animation:spark391 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:377px;width:203px}.bg-vectors .line:nth-child(391) .fire{animation:fire 1535ms linear -401ms infinite}@keyframes spark391{0%{transform:translateY(0)}to{transform:rotate(193deg) translateX(977px)}}.bg-vectors .line:nth-child(392){transform:rotateY(121deg)}.bg-vectors .line:nth-child(392) .spark{animation:spark392 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:295px;width:307px}.bg-vectors .line:nth-child(392) .fire{animation:fire 1695ms linear -847ms infinite}@keyframes spark392{0%{transform:translateY(0)}to{transform:rotate(343deg) translateX(789px)}}.bg-vectors .line:nth-child(393){transform:rotateY(4deg)}.bg-vectors .line:nth-child(393) .spark{animation:spark393 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:398px;width:392px}.bg-vectors .line:nth-child(393) .fire{animation:fire 1364ms linear -.93s infinite}@keyframes spark393{0%{transform:translateY(0)}to{transform:rotate(324deg) translateX(982px)}}.bg-vectors .line:nth-child(394){transform:rotateY(304deg)}.bg-vectors .line:nth-child(394) .spark{animation:spark394 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:374px;width:261px}.bg-vectors .line:nth-child(394) .fire{animation:fire 1716ms linear -786ms infinite}@keyframes spark394{0%{transform:translateY(0)}to{transform:rotate(300deg) translateX(165px)}}.bg-vectors .line:nth-child(395){transform:rotateY(23deg)}.bg-vectors .line:nth-child(395) .spark{animation:spark395 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:274px;width:365px}.bg-vectors .line:nth-child(395) .fire{animation:fire 1958ms linear -116ms infinite}@keyframes spark395{0%{transform:translateY(0)}to{transform:rotate(64deg) translateX(220px)}}.bg-vectors .line:nth-child(396){transform:rotateY(114deg)}.bg-vectors .line:nth-child(396) .spark{animation:spark396 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:234px;width:252px}.bg-vectors .line:nth-child(396) .fire{animation:fire 1205ms linear -761ms infinite}@keyframes spark396{0%{transform:translateY(0)}to{transform:rotate(246deg) translateX(806px)}}.bg-vectors .line:nth-child(397){transform:rotateY(32deg)}.bg-vectors .line:nth-child(397) .spark{animation:spark397 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:310px;width:354px}.bg-vectors .line:nth-child(397) .fire{animation:fire 1.81s linear -219ms infinite}@keyframes spark397{0%{transform:translateY(0)}to{transform:rotate(128deg) translateX(768px)}}.bg-vectors .line:nth-child(398){transform:rotateY(119deg)}.bg-vectors .line:nth-child(398) .spark{animation:spark398 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:355px;width:235px}.bg-vectors .line:nth-child(398) .fire{animation:fire 1108ms linear -152ms infinite}@keyframes spark398{0%{transform:translateY(0)}to{transform:rotate(176deg) translateX(241px)}}.bg-vectors .line:nth-child(399){transform:rotateY(170deg)}.bg-vectors .line:nth-child(399) .spark{animation:spark399 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:247px;width:263px}.bg-vectors .line:nth-child(399) .fire{animation:fire 1058ms linear -121ms infinite}@keyframes spark399{0%{transform:translateY(0)}to{transform:rotate(192deg) translateX(433px)}}.bg-vectors .line:nth-child(400){transform:rotateY(329deg)}.bg-vectors .line:nth-child(400) .spark{animation:spark400 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:372px;width:257px}.bg-vectors .line:nth-child(400) .fire{animation:fire 1.79s linear -634ms infinite}@keyframes spark400{0%{transform:translateY(0)}to{transform:rotate(113deg) translateX(537px)}}.bg-vectors .line:nth-child(401){transform:rotateY(254deg)}.bg-vectors .line:nth-child(401) .spark{animation:spark401 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:204px;width:385px}.bg-vectors .line:nth-child(401) .fire{animation:fire 1731ms linear -212ms infinite}@keyframes spark401{0%{transform:translateY(0)}to{transform:rotate(48deg) translateX(427px)}}.bg-vectors .line:nth-child(402){transform:rotateY(55deg)}.bg-vectors .line:nth-child(402) .spark{animation:spark402 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:299px;width:373px}.bg-vectors .line:nth-child(402) .fire{animation:fire 1137ms linear -243ms infinite}@keyframes spark402{0%{transform:translateY(0)}to{transform:rotate(107deg) translateX(340px)}}.bg-vectors .line:nth-child(403){transform:rotateY(318deg)}.bg-vectors .line:nth-child(403) .spark{animation:spark403 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:340px;width:261px}.bg-vectors .line:nth-child(403) .fire{animation:fire 1002ms linear -928ms infinite}@keyframes spark403{0%{transform:translateY(0)}to{transform:rotate(296deg) translateX(422px)}}.bg-vectors .line:nth-child(404){transform:rotateY(133deg)}.bg-vectors .line:nth-child(404) .spark{animation:spark404 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:364px;width:380px}.bg-vectors .line:nth-child(404) .fire{animation:fire 1344ms linear -208ms infinite}@keyframes spark404{0%{transform:translateY(0)}to{transform:rotate(283deg) translateX(328px)}}.bg-vectors .line:nth-child(405){transform:rotateY(278deg)}.bg-vectors .line:nth-child(405) .spark{animation:spark405 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:355px;width:238px}.bg-vectors .line:nth-child(405) .fire{animation:fire 1551ms linear -645ms infinite}@keyframes spark405{0%{transform:translateY(0)}to{transform:rotate(237deg) translateX(919px)}}.bg-vectors .line:nth-child(406){transform:rotateY(195deg)}.bg-vectors .line:nth-child(406) .spark{animation:spark406 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:282px;width:207px}.bg-vectors .line:nth-child(406) .fire{animation:fire 1674ms linear -657ms infinite}@keyframes spark406{0%{transform:translateY(0)}to{transform:rotate(3deg) translateX(159px)}}.bg-vectors .line:nth-child(407){transform:rotateY(112deg)}.bg-vectors .line:nth-child(407) .spark{animation:spark407 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:215px;width:273px}.bg-vectors .line:nth-child(407) .fire{animation:fire 1977ms linear -936ms infinite}@keyframes spark407{0%{transform:translateY(0)}to{transform:rotate(181deg) translateX(528px)}}.bg-vectors .line:nth-child(408){transform:rotateY(138deg)}.bg-vectors .line:nth-child(408) .spark{animation:spark408 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:244px;width:218px}.bg-vectors .line:nth-child(408) .fire{animation:fire 1044ms linear -287ms infinite}@keyframes spark408{0%{transform:translateY(0)}to{transform:rotate(216deg) translateX(651px)}}.bg-vectors .line:nth-child(409){transform:rotateY(321deg)}.bg-vectors .line:nth-child(409) .spark{animation:spark409 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:396px;width:248px}.bg-vectors .line:nth-child(409) .fire{animation:fire 1263ms linear -958ms infinite}@keyframes spark409{0%{transform:translateY(0)}to{transform:rotate(352deg) translateX(709px)}}.bg-vectors .line:nth-child(410){transform:rotateY(44deg)}.bg-vectors .line:nth-child(410) .spark{animation:spark410 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:220px;width:394px}.bg-vectors .line:nth-child(410) .fire{animation:fire 1525ms linear -345ms infinite}@keyframes spark410{0%{transform:translateY(0)}to{transform:rotate(148deg) translateX(631px)}}.bg-vectors .line:nth-child(411){transform:rotateY(1deg)}.bg-vectors .line:nth-child(411) .spark{animation:spark411 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:252px;width:304px}.bg-vectors .line:nth-child(411) .fire{animation:fire 1206ms linear -69ms infinite}@keyframes spark411{0%{transform:translateY(0)}to{transform:rotate(247deg) translateX(666px)}}.bg-vectors .line:nth-child(412){transform:rotateY(64deg)}.bg-vectors .line:nth-child(412) .spark{animation:spark412 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:257px;width:334px}.bg-vectors .line:nth-child(412) .fire{animation:fire 1.28s linear -564ms infinite}@keyframes spark412{0%{transform:translateY(0)}to{transform:rotate(172deg) translateX(946px)}}.bg-vectors .line:nth-child(413){transform:rotateY(13deg)}.bg-vectors .line:nth-child(413) .spark{animation:spark413 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:258px;width:372px}.bg-vectors .line:nth-child(413) .fire{animation:fire 1994ms linear -822ms infinite}@keyframes spark413{0%{transform:translateY(0)}to{transform:rotate(307deg) translateX(557px)}}.bg-vectors .line:nth-child(414){transform:rotateY(50deg)}.bg-vectors .line:nth-child(414) .spark{animation:spark414 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:215px;width:289px}.bg-vectors .line:nth-child(414) .fire{animation:fire 1681ms linear -36ms infinite}@keyframes spark414{0%{transform:translateY(0)}to{transform:rotate(185deg) translateX(647px)}}.bg-vectors .line:nth-child(415){transform:rotateY(154deg)}.bg-vectors .line:nth-child(415) .spark{animation:spark415 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:334px;width:208px}.bg-vectors .line:nth-child(415) .fire{animation:fire 1216ms linear -844ms infinite}@keyframes spark415{0%{transform:translateY(0)}to{transform:rotate(339deg) translateX(514px)}}.bg-vectors .line:nth-child(416){transform:rotateY(303deg)}.bg-vectors .line:nth-child(416) .spark{animation:spark416 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:219px;width:302px}.bg-vectors .line:nth-child(416) .fire{animation:fire 1814ms linear -139ms infinite}@keyframes spark416{0%{transform:translateY(0)}to{transform:rotate(272deg) translateX(471px)}}.bg-vectors .line:nth-child(417){transform:rotateY(188deg)}.bg-vectors .line:nth-child(417) .spark{animation:spark417 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:322px;width:315px}.bg-vectors .line:nth-child(417) .fire{animation:fire 1902ms linear -663ms infinite}@keyframes spark417{0%{transform:translateY(0)}to{transform:rotate(117deg) translateX(667px)}}.bg-vectors .line:nth-child(418){transform:rotateY(31deg)}.bg-vectors .line:nth-child(418) .spark{animation:spark418 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:351px;width:232px}.bg-vectors .line:nth-child(418) .fire{animation:fire 1434ms linear -464ms infinite}@keyframes spark418{0%{transform:translateY(0)}to{transform:rotate(119deg) translateX(723px)}}.bg-vectors .line:nth-child(419){transform:rotateY(307deg)}.bg-vectors .line:nth-child(419) .spark{animation:spark419 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:219px;width:215px}.bg-vectors .line:nth-child(419) .fire{animation:fire 1634ms linear -351ms infinite}@keyframes spark419{0%{transform:translateY(0)}to{transform:rotate(29deg) translateX(125px)}}.bg-vectors .line:nth-child(420){transform:rotateY(43deg)}.bg-vectors .line:nth-child(420) .spark{animation:spark420 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:400px;width:319px}.bg-vectors .line:nth-child(420) .fire{animation:fire 1112ms linear -.71s infinite}@keyframes spark420{0%{transform:translateY(0)}to{transform:rotate(4deg) translateX(282px)}}.bg-vectors .line:nth-child(421){transform:rotateY(159deg)}.bg-vectors .line:nth-child(421) .spark{animation:spark421 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:374px;width:333px}.bg-vectors .line:nth-child(421) .fire{animation:fire 1679ms linear -443ms infinite}@keyframes spark421{0%{transform:translateY(0)}to{transform:rotate(133deg) translateX(412px)}}.bg-vectors .line:nth-child(422){transform:rotateY(329deg)}.bg-vectors .line:nth-child(422) .spark{animation:spark422 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:396px;width:281px}.bg-vectors .line:nth-child(422) .fire{animation:fire 1633ms linear -104ms infinite}@keyframes spark422{0%{transform:translateY(0)}to{transform:rotate(208deg) translateX(241px)}}.bg-vectors .line:nth-child(423){transform:rotateY(80deg)}.bg-vectors .line:nth-child(423) .spark{animation:spark423 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:355px;width:255px}.bg-vectors .line:nth-child(423) .fire{animation:fire 1.55s linear -.16s infinite}@keyframes spark423{0%{transform:translateY(0)}to{transform:rotate(151deg) translateX(514px)}}.bg-vectors .line:nth-child(424){transform:rotateY(159deg)}.bg-vectors .line:nth-child(424) .spark{animation:spark424 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:235px;width:265px}.bg-vectors .line:nth-child(424) .fire{animation:fire 1865ms linear -60ms infinite}@keyframes spark424{0%{transform:translateY(0)}to{transform:rotate(150deg) translateX(727px)}}.bg-vectors .line:nth-child(425){transform:rotateY(280deg)}.bg-vectors .line:nth-child(425) .spark{animation:spark425 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:285px;width:343px}.bg-vectors .line:nth-child(425) .fire{animation:fire 1137ms linear -776ms infinite}@keyframes spark425{0%{transform:translateY(0)}to{transform:rotate(20deg) translateX(628px)}}.bg-vectors .line:nth-child(426){transform:rotateY(192deg)}.bg-vectors .line:nth-child(426) .spark{animation:spark426 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:248px;width:353px}.bg-vectors .line:nth-child(426) .fire{animation:fire 1695ms linear -.83s infinite}@keyframes spark426{0%{transform:translateY(0)}to{transform:rotate(254deg) translateX(1086px)}}.bg-vectors .line:nth-child(427){transform:rotateY(113deg)}.bg-vectors .line:nth-child(427) .spark{animation:spark427 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:395px;width:379px}.bg-vectors .line:nth-child(427) .fire{animation:fire 1694ms linear -123ms infinite}@keyframes spark427{0%{transform:translateY(0)}to{transform:rotate(192deg) translateX(937px)}}.bg-vectors .line:nth-child(428){transform:rotateY(294deg)}.bg-vectors .line:nth-child(428) .spark{animation:spark428 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:224px;width:221px}.bg-vectors .line:nth-child(428) .fire{animation:fire 1037ms linear -108ms infinite}@keyframes spark428{0%{transform:translateY(0)}to{transform:rotate(138deg) translateX(223px)}}.bg-vectors .line:nth-child(429){transform:rotateY(184deg)}.bg-vectors .line:nth-child(429) .spark{animation:spark429 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:220px;width:390px}.bg-vectors .line:nth-child(429) .fire{animation:fire 1681ms linear -65ms infinite}@keyframes spark429{0%{transform:translateY(0)}to{transform:rotate(237deg) translateX(281px)}}.bg-vectors .line:nth-child(430){transform:rotateY(25deg)}.bg-vectors .line:nth-child(430) .spark{animation:spark430 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:247px;width:208px}.bg-vectors .line:nth-child(430) .fire{animation:fire 1423ms linear -382ms infinite}@keyframes spark430{0%{transform:translateY(0)}to{transform:rotate(63deg) translateX(430px)}}.bg-vectors .line:nth-child(431){transform:rotateY(205deg)}.bg-vectors .line:nth-child(431) .spark{animation:spark431 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:374px;width:211px}.bg-vectors .line:nth-child(431) .fire{animation:fire 1457ms linear -679ms infinite}@keyframes spark431{0%{transform:translateY(0)}to{transform:rotate(308deg) translateX(261px)}}.bg-vectors .line:nth-child(432){transform:rotateY(247deg)}.bg-vectors .line:nth-child(432) .spark{animation:spark432 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:346px;width:252px}.bg-vectors .line:nth-child(432) .fire{animation:fire 1736ms linear -312ms infinite}@keyframes spark432{0%{transform:translateY(0)}to{transform:rotate(298deg) translateX(874px)}}.bg-vectors .line:nth-child(433){transform:rotateY(55deg)}.bg-vectors .line:nth-child(433) .spark{animation:spark433 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:394px;width:310px}.bg-vectors .line:nth-child(433) .fire{animation:fire 1203ms linear -773ms infinite}@keyframes spark433{0%{transform:translateY(0)}to{transform:rotate(261deg) translateX(894px)}}.bg-vectors .line:nth-child(434){transform:rotateY(230deg)}.bg-vectors .line:nth-child(434) .spark{animation:spark434 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:341px;width:278px}.bg-vectors .line:nth-child(434) .fire{animation:fire 1709ms linear -378ms infinite}@keyframes spark434{0%{transform:translateY(0)}to{transform:rotate(189deg) translateX(258px)}}.bg-vectors .line:nth-child(435){transform:rotateY(339deg)}.bg-vectors .line:nth-child(435) .spark{animation:spark435 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:257px;width:266px}.bg-vectors .line:nth-child(435) .fire{animation:fire 1.33s linear -33ms infinite}@keyframes spark435{0%{transform:translateY(0)}to{transform:rotate(237deg) translateX(532px)}}.bg-vectors .line:nth-child(436){transform:rotateY(130deg)}.bg-vectors .line:nth-child(436) .spark{animation:spark436 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:255px;width:279px}.bg-vectors .line:nth-child(436) .fire{animation:fire 1285ms linear -866ms infinite}@keyframes spark436{0%{transform:translateY(0)}to{transform:rotate(1turn) translateX(116px)}}.bg-vectors .line:nth-child(437){transform:rotateY(313deg)}.bg-vectors .line:nth-child(437) .spark{animation:spark437 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:261px;width:234px}.bg-vectors .line:nth-child(437) .fire{animation:fire 1265ms linear -718ms infinite}@keyframes spark437{0%{transform:translateY(0)}to{transform:rotate(321deg) translateX(701px)}}.bg-vectors .line:nth-child(438){transform:rotateY(66deg)}.bg-vectors .line:nth-child(438) .spark{animation:spark438 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:359px;width:390px}.bg-vectors .line:nth-child(438) .fire{animation:fire 1371ms linear -173ms infinite}@keyframes spark438{0%{transform:translateY(0)}to{transform:rotate(131deg) translateX(939px)}}.bg-vectors .line:nth-child(439){transform:rotateY(94deg)}.bg-vectors .line:nth-child(439) .spark{animation:spark439 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:331px;width:252px}.bg-vectors .line:nth-child(439) .fire{animation:fire 1144ms linear -677ms infinite}@keyframes spark439{0%{transform:translateY(0)}to{transform:rotate(325deg) translateX(813px)}}.bg-vectors .line:nth-child(440){transform:rotateY(44deg)}.bg-vectors .line:nth-child(440) .spark{animation:spark440 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:341px;width:331px}.bg-vectors .line:nth-child(440) .fire{animation:fire 1416ms linear -857ms infinite}@keyframes spark440{0%{transform:translateY(0)}to{transform:rotate(357deg) translateX(865px)}}.bg-vectors .line:nth-child(441){transform:rotateY(252deg)}.bg-vectors .line:nth-child(441) .spark{animation:spark441 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:304px;width:359px}.bg-vectors .line:nth-child(441) .fire{animation:fire 1532ms linear -704ms infinite}@keyframes spark441{0%{transform:translateY(0)}to{transform:rotate(33deg) translateX(689px)}}.bg-vectors .line:nth-child(442){transform:rotateY(173deg)}.bg-vectors .line:nth-child(442) .spark{animation:spark442 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:330px;width:230px}.bg-vectors .line:nth-child(442) .fire{animation:fire 1641ms linear -186ms infinite}@keyframes spark442{0%{transform:translateY(0)}to{transform:rotate(52deg) translateX(598px)}}.bg-vectors .line:nth-child(443){transform:rotateY(18deg)}.bg-vectors .line:nth-child(443) .spark{animation:spark443 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:372px;width:206px}.bg-vectors .line:nth-child(443) .fire{animation:fire 1971ms linear -.9s infinite}@keyframes spark443{0%{transform:translateY(0)}to{transform:rotate(265deg) translateX(773px)}}.bg-vectors .line:nth-child(444){transform:rotateY(349deg)}.bg-vectors .line:nth-child(444) .spark{animation:spark444 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:210px;width:338px}.bg-vectors .line:nth-child(444) .fire{animation:fire 1805ms linear -26ms infinite}@keyframes spark444{0%{transform:translateY(0)}to{transform:rotate(113deg) translateX(330px)}}.bg-vectors .line:nth-child(445){transform:rotateY(57deg)}.bg-vectors .line:nth-child(445) .spark{animation:spark445 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:244px;width:384px}.bg-vectors .line:nth-child(445) .fire{animation:fire 1846ms linear -281ms infinite}@keyframes spark445{0%{transform:translateY(0)}to{transform:rotate(330deg) translateX(910px)}}.bg-vectors .line:nth-child(446){transform:rotateY(314deg)}.bg-vectors .line:nth-child(446) .spark{animation:spark446 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:242px;width:251px}.bg-vectors .line:nth-child(446) .fire{animation:fire 1202ms linear -266ms infinite}@keyframes spark446{0%{transform:translateY(0)}to{transform:rotate(26deg) translateX(205px)}}.bg-vectors .line:nth-child(447){transform:rotateY(111deg)}.bg-vectors .line:nth-child(447) .spark{animation:spark447 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:210px;width:337px}.bg-vectors .line:nth-child(447) .fire{animation:fire 1817ms linear -.15s infinite}@keyframes spark447{0%{transform:translateY(0)}to{transform:rotate(178deg) translateX(293px)}}.bg-vectors .line:nth-child(448){transform:rotateY(254deg)}.bg-vectors .line:nth-child(448) .spark{animation:spark448 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:379px;width:388px}.bg-vectors .line:nth-child(448) .fire{animation:fire 1788ms linear -314ms infinite}@keyframes spark448{0%{transform:translateY(0)}to{transform:rotate(300deg) translateX(856px)}}.bg-vectors .line:nth-child(449){transform:rotateY(201deg)}.bg-vectors .line:nth-child(449) .spark{animation:spark449 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:262px;width:301px}.bg-vectors .line:nth-child(449) .fire{animation:fire 1.16s linear -896ms infinite}@keyframes spark449{0%{transform:translateY(0)}to{transform:rotate(61deg) translateX(467px)}}.bg-vectors .line:nth-child(450){transform:rotateY(67deg)}.bg-vectors .line:nth-child(450) .spark{animation:spark450 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:306px;width:311px}.bg-vectors .line:nth-child(450) .fire{animation:fire 1225ms linear -634ms infinite}@keyframes spark450{0%{transform:translateY(0)}to{transform:rotate(18deg) translateX(388px)}}.bg-vectors .line:nth-child(451){transform:rotateY(165deg)}.bg-vectors .line:nth-child(451) .spark{animation:spark451 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:249px;width:229px}.bg-vectors .line:nth-child(451) .fire{animation:fire 1735ms linear -77ms infinite}@keyframes spark451{0%{transform:translateY(0)}to{transform:rotate(243deg) translateX(677px)}}.bg-vectors .line:nth-child(452){transform:rotateY(140deg)}.bg-vectors .line:nth-child(452) .spark{animation:spark452 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:239px;width:352px}.bg-vectors .line:nth-child(452) .fire{animation:fire 1.87s linear -53ms infinite}@keyframes spark452{0%{transform:translateY(0)}to{transform:rotate(183deg) translateX(335px)}}.bg-vectors .line:nth-child(453){transform:rotateY(68deg)}.bg-vectors .line:nth-child(453) .spark{animation:spark453 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:342px;width:396px}.bg-vectors .line:nth-child(453) .fire{animation:fire 1197ms linear -24ms infinite}@keyframes spark453{0%{transform:translateY(0)}to{transform:rotate(332deg) translateX(610px)}}.bg-vectors .line:nth-child(454){transform:rotateY(270deg)}.bg-vectors .line:nth-child(454) .spark{animation:spark454 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:371px;width:346px}.bg-vectors .line:nth-child(454) .fire{animation:fire 1349ms linear -729ms infinite}@keyframes spark454{0%{transform:translateY(0)}to{transform:rotate(30deg) translateX(196px)}}.bg-vectors .line:nth-child(455){transform:rotateY(85deg)}.bg-vectors .line:nth-child(455) .spark{animation:spark455 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:288px;width:386px}.bg-vectors .line:nth-child(455) .fire{animation:fire 1439ms linear -515ms infinite}@keyframes spark455{0%{transform:translateY(0)}to{transform:rotate(35deg) translateX(1025px)}}.bg-vectors .line:nth-child(456){transform:rotateY(154deg)}.bg-vectors .line:nth-child(456) .spark{animation:spark456 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:263px;width:212px}.bg-vectors .line:nth-child(456) .fire{animation:fire 1853ms linear -302ms infinite}@keyframes spark456{0%{transform:translateY(0)}to{transform:rotate(343deg) translateX(811px)}}.bg-vectors .line:nth-child(457){transform:rotateY(184deg)}.bg-vectors .line:nth-child(457) .spark{animation:spark457 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:239px;width:281px}.bg-vectors .line:nth-child(457) .fire{animation:fire 1923ms linear -642ms infinite}@keyframes spark457{0%{transform:translateY(0)}to{transform:rotate(288deg) translateX(1017px)}}.bg-vectors .line:nth-child(458){transform:rotateY(351deg)}.bg-vectors .line:nth-child(458) .spark{animation:spark458 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:205px;width:262px}.bg-vectors .line:nth-child(458) .fire{animation:fire 1899ms linear -911ms infinite}@keyframes spark458{0%{transform:translateY(0)}to{transform:rotate(66deg) translateX(277px)}}.bg-vectors .line:nth-child(459){transform:rotateY(259deg)}.bg-vectors .line:nth-child(459) .spark{animation:spark459 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:282px;width:248px}.bg-vectors .line:nth-child(459) .fire{animation:fire 1555ms linear -614ms infinite}@keyframes spark459{0%{transform:translateY(0)}to{transform:rotate(42deg) translateX(933px)}}.bg-vectors .line:nth-child(460){transform:rotateY(173deg)}.bg-vectors .line:nth-child(460) .spark{animation:spark460 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:300px;width:230px}.bg-vectors .line:nth-child(460) .fire{animation:fire 1231ms linear -218ms infinite}@keyframes spark460{0%{transform:translateY(0)}to{transform:rotate(225deg) translateX(826px)}}.bg-vectors .line:nth-child(461){transform:rotateY(237deg)}.bg-vectors .line:nth-child(461) .spark{animation:spark461 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:271px;width:273px}.bg-vectors .line:nth-child(461) .fire{animation:fire 1.13s linear -933ms infinite}@keyframes spark461{0%{transform:translateY(0)}to{transform:rotate(346deg) translateX(687px)}}.bg-vectors .line:nth-child(462){transform:rotateY(280deg)}.bg-vectors .line:nth-child(462) .spark{animation:spark462 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:267px;width:379px}.bg-vectors .line:nth-child(462) .fire{animation:fire 1485ms linear -.96s infinite}@keyframes spark462{0%{transform:translateY(0)}to{transform:rotate(1turn) translateX(954px)}}.bg-vectors .line:nth-child(463){transform:rotateY(95deg)}.bg-vectors .line:nth-child(463) .spark{animation:spark463 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:263px;width:210px}.bg-vectors .line:nth-child(463) .fire{animation:fire 1686ms linear -38ms infinite}@keyframes spark463{0%{transform:translateY(0)}to{transform:rotate(321deg) translateX(741px)}}.bg-vectors .line:nth-child(464){transform:rotateY(87deg)}.bg-vectors .line:nth-child(464) .spark{animation:spark464 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:229px;width:359px}.bg-vectors .line:nth-child(464) .fire{animation:fire 1329ms linear -189ms infinite}@keyframes spark464{0%{transform:translateY(0)}to{transform:rotate(17deg) translateX(1097px)}}.bg-vectors .line:nth-child(465){transform:rotateY(327deg)}.bg-vectors .line:nth-child(465) .spark{animation:spark465 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:340px;width:285px}.bg-vectors .line:nth-child(465) .fire{animation:fire 1484ms linear -802ms infinite}@keyframes spark465{0%{transform:translateY(0)}to{transform:rotate(212deg) translateX(138px)}}.bg-vectors .line:nth-child(466){transform:rotateY(123deg)}.bg-vectors .line:nth-child(466) .spark{animation:spark466 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:388px;width:263px}.bg-vectors .line:nth-child(466) .fire{animation:fire 1552ms linear -246ms infinite}@keyframes spark466{0%{transform:translateY(0)}to{transform:rotate(112deg) translateX(384px)}}.bg-vectors .line:nth-child(467){transform:rotateY(263deg)}.bg-vectors .line:nth-child(467) .spark{animation:spark467 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:285px;width:301px}.bg-vectors .line:nth-child(467) .fire{animation:fire 1534ms linear -697ms infinite}@keyframes spark467{0%{transform:translateY(0)}to{transform:rotate(168deg) translateX(986px)}}.bg-vectors .line:nth-child(468){transform:rotateY(253deg)}.bg-vectors .line:nth-child(468) .spark{animation:spark468 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:215px;width:355px}.bg-vectors .line:nth-child(468) .fire{animation:fire 1045ms linear -678ms infinite}@keyframes spark468{0%{transform:translateY(0)}to{transform:rotate(359deg) translateX(591px)}}.bg-vectors .line:nth-child(469){transform:rotateY(138deg)}.bg-vectors .line:nth-child(469) .spark{animation:spark469 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:355px;width:214px}.bg-vectors .line:nth-child(469) .fire{animation:fire 1784ms linear -62ms infinite}@keyframes spark469{0%{transform:translateY(0)}to{transform:rotate(17deg) translateX(704px)}}.bg-vectors .line:nth-child(470){transform:rotateY(347deg)}.bg-vectors .line:nth-child(470) .spark{animation:spark470 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:283px;width:322px}.bg-vectors .line:nth-child(470) .fire{animation:fire 1063ms linear -589ms infinite}@keyframes spark470{0%{transform:translateY(0)}to{transform:rotate(167deg) translateX(623px)}}.bg-vectors .line:nth-child(471){transform:rotateY(41deg)}.bg-vectors .line:nth-child(471) .spark{animation:spark471 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:227px;width:400px}.bg-vectors .line:nth-child(471) .fire{animation:fire 1623ms linear -796ms infinite}@keyframes spark471{0%{transform:translateY(0)}to{transform:rotate(48deg) translateX(238px)}}.bg-vectors .line:nth-child(472){transform:rotateY(22deg)}.bg-vectors .line:nth-child(472) .spark{animation:spark472 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:384px;width:265px}.bg-vectors .line:nth-child(472) .fire{animation:fire 1257ms linear -411ms infinite}@keyframes spark472{0%{transform:translateY(0)}to{transform:rotate(194deg) translateX(129px)}}.bg-vectors .line:nth-child(473){transform:rotateY(162deg)}.bg-vectors .line:nth-child(473) .spark{animation:spark473 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:329px;width:334px}.bg-vectors .line:nth-child(473) .fire{animation:fire 1.12s linear -94ms infinite}@keyframes spark473{0%{transform:translateY(0)}to{transform:rotate(120deg) translateX(654px)}}.bg-vectors .line:nth-child(474){transform:rotateY(175deg)}.bg-vectors .line:nth-child(474) .spark{animation:spark474 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:313px;width:222px}.bg-vectors .line:nth-child(474) .fire{animation:fire 1394ms linear -.37s infinite}@keyframes spark474{0%{transform:translateY(0)}to{transform:rotate(89deg) translateX(916px)}}.bg-vectors .line:nth-child(475){transform:rotateY(223deg)}.bg-vectors .line:nth-child(475) .spark{animation:spark475 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:356px;width:391px}.bg-vectors .line:nth-child(475) .fire{animation:fire 1058ms linear -446ms infinite}@keyframes spark475{0%{transform:translateY(0)}to{transform:rotate(113deg) translateX(199px)}}.bg-vectors .line:nth-child(476){transform:rotateY(63deg)}.bg-vectors .line:nth-child(476) .spark{animation:spark476 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:320px;width:332px}.bg-vectors .line:nth-child(476) .fire{animation:fire 1221ms linear -841ms infinite}@keyframes spark476{0%{transform:translateY(0)}to{transform:rotate(19deg) translateX(526px)}}.bg-vectors .line:nth-child(477){transform:rotateY(107deg)}.bg-vectors .line:nth-child(477) .spark{animation:spark477 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:215px;width:315px}.bg-vectors .line:nth-child(477) .fire{animation:fire 1827ms linear -466ms infinite}@keyframes spark477{0%{transform:translateY(0)}to{transform:rotate(321deg) translateX(778px)}}.bg-vectors .line:nth-child(478){transform:rotateY(112deg)}.bg-vectors .line:nth-child(478) .spark{animation:spark478 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:250px;width:231px}.bg-vectors .line:nth-child(478) .fire{animation:fire 1347ms linear -.83s infinite}@keyframes spark478{0%{transform:translateY(0)}to{transform:rotate(166deg) translateX(424px)}}.bg-vectors .line:nth-child(479){transform:rotateY(320deg)}.bg-vectors .line:nth-child(479) .spark{animation:spark479 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:310px;width:244px}.bg-vectors .line:nth-child(479) .fire{animation:fire 1923ms linear -886ms infinite}@keyframes spark479{0%{transform:translateY(0)}to{transform:rotate(93deg) translateX(753px)}}.bg-vectors .line:nth-child(480){transform:rotateY(185deg)}.bg-vectors .line:nth-child(480) .spark{animation:spark480 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:211px;width:359px}.bg-vectors .line:nth-child(480) .fire{animation:fire 1598ms linear -286ms infinite}@keyframes spark480{0%{transform:translateY(0)}to{transform:rotate(89deg) translateX(1084px)}}.bg-vectors .line:nth-child(481){transform:rotateY(208deg)}.bg-vectors .line:nth-child(481) .spark{animation:spark481 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:238px;width:264px}.bg-vectors .line:nth-child(481) .fire{animation:fire 1545ms linear -.41s infinite}@keyframes spark481{0%{transform:translateY(0)}to{transform:rotate(172deg) translateX(215px)}}.bg-vectors .line:nth-child(482){transform:rotateY(44deg)}.bg-vectors .line:nth-child(482) .spark{animation:spark482 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:202px;width:237px}.bg-vectors .line:nth-child(482) .fire{animation:fire 1.61s linear -515ms infinite}@keyframes spark482{0%{transform:translateY(0)}to{transform:rotate(255deg) translateX(382px)}}.bg-vectors .line:nth-child(483){transform:rotateY(228deg)}.bg-vectors .line:nth-child(483) .spark{animation:spark483 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:283px;width:316px}.bg-vectors .line:nth-child(483) .fire{animation:fire 1747ms linear -763ms infinite}@keyframes spark483{0%{transform:translateY(0)}to{transform:rotate(110deg) translateX(764px)}}.bg-vectors .line:nth-child(484){transform:rotateY(216deg)}.bg-vectors .line:nth-child(484) .spark{animation:spark484 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:281px;width:276px}.bg-vectors .line:nth-child(484) .fire{animation:fire 1143ms linear -208ms infinite}@keyframes spark484{0%{transform:translateY(0)}to{transform:rotate(119deg) translateX(1033px)}}.bg-vectors .line:nth-child(485){transform:rotateY(115deg)}.bg-vectors .line:nth-child(485) .spark{animation:spark485 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:357px;width:352px}.bg-vectors .line:nth-child(485) .fire{animation:fire 1877ms linear -456ms infinite}@keyframes spark485{0%{transform:translateY(0)}to{transform:rotate(67deg) translateX(690px)}}.bg-vectors .line:nth-child(486){transform:rotateY(248deg)}.bg-vectors .line:nth-child(486) .spark{animation:spark486 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:228px;width:346px}.bg-vectors .line:nth-child(486) .fire{animation:fire 1451ms linear -655ms infinite}@keyframes spark486{0%{transform:translateY(0)}to{transform:rotate(60deg) translateX(956px)}}.bg-vectors .line:nth-child(487){transform:rotateY(209deg)}.bg-vectors .line:nth-child(487) .spark{animation:spark487 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:268px;width:280px}.bg-vectors .line:nth-child(487) .fire{animation:fire 1359ms linear -494ms infinite}@keyframes spark487{0%{transform:translateY(0)}to{transform:rotate(75deg) translateX(750px)}}.bg-vectors .line:nth-child(488){transform:rotateY(166deg)}.bg-vectors .line:nth-child(488) .spark{animation:spark488 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:218px;width:317px}.bg-vectors .line:nth-child(488) .fire{animation:fire 1907ms linear -.92s infinite}@keyframes spark488{0%{transform:translateY(0)}to{transform:rotate(54deg) translateX(1044px)}}.bg-vectors .line:nth-child(489){transform:rotateY(39deg)}.bg-vectors .line:nth-child(489) .spark{animation:spark489 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:359px;width:304px}.bg-vectors .line:nth-child(489) .fire{animation:fire 1478ms linear -405ms infinite}@keyframes spark489{0%{transform:translateY(0)}to{transform:rotate(76deg) translateX(314px)}}.bg-vectors .line:nth-child(490){transform:rotateY(267deg)}.bg-vectors .line:nth-child(490) .spark{animation:spark490 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:334px;width:202px}.bg-vectors .line:nth-child(490) .fire{animation:fire 1383ms linear -779ms infinite}@keyframes spark490{0%{transform:translateY(0)}to{transform:rotate(272deg) translateX(775px)}}.bg-vectors .line:nth-child(491){transform:rotateY(114deg)}.bg-vectors .line:nth-child(491) .spark{animation:spark491 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:225px;width:249px}.bg-vectors .line:nth-child(491) .fire{animation:fire 1708ms linear -926ms infinite}@keyframes spark491{0%{transform:translateY(0)}to{transform:rotate(79deg) translateX(875px)}}.bg-vectors .line:nth-child(492){transform:rotateY(255deg)}.bg-vectors .line:nth-child(492) .spark{animation:spark492 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:258px;width:235px}.bg-vectors .line:nth-child(492) .fire{animation:fire 1406ms linear -353ms infinite}@keyframes spark492{0%{transform:translateY(0)}to{transform:rotate(191deg) translateX(931px)}}.bg-vectors .line:nth-child(493){transform:rotateY(271deg)}.bg-vectors .line:nth-child(493) .spark{animation:spark493 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:367px;width:271px}.bg-vectors .line:nth-child(493) .fire{animation:fire 1715ms linear -158ms infinite}@keyframes spark493{0%{transform:translateY(0)}to{transform:rotate(186deg) translateX(586px)}}.bg-vectors .line:nth-child(494){transform:rotateY(215deg)}.bg-vectors .line:nth-child(494) .spark{animation:spark494 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:267px;width:305px}.bg-vectors .line:nth-child(494) .fire{animation:fire 1131ms linear -858ms infinite}@keyframes spark494{0%{transform:translateY(0)}to{transform:rotate(222deg) translateX(495px)}}.bg-vectors .line:nth-child(495){transform:rotateY(206deg)}.bg-vectors .line:nth-child(495) .spark{animation:spark495 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:247px;width:276px}.bg-vectors .line:nth-child(495) .fire{animation:fire 1.84s linear -127ms infinite}@keyframes spark495{0%{transform:translateY(0)}to{transform:rotate(188deg) translateX(619px)}}.bg-vectors .line:nth-child(496){transform:rotateY(213deg)}.bg-vectors .line:nth-child(496) .spark{animation:spark496 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:336px;width:326px}.bg-vectors .line:nth-child(496) .fire{animation:fire 1.26s linear -284ms infinite}@keyframes spark496{0%{transform:translateY(0)}to{transform:rotate(43deg) translateX(645px)}}.bg-vectors .line:nth-child(497){transform:rotateY(336deg)}.bg-vectors .line:nth-child(497) .spark{animation:spark497 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:351px;width:339px}.bg-vectors .line:nth-child(497) .fire{animation:fire 1988ms linear -36ms infinite}@keyframes spark497{0%{transform:translateY(0)}to{transform:rotate(156deg) translateX(385px)}}.bg-vectors .line:nth-child(498){transform:rotateY(269deg)}.bg-vectors .line:nth-child(498) .spark{animation:spark498 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:342px;width:278px}.bg-vectors .line:nth-child(498) .fire{animation:fire 1.85s linear -926ms infinite}@keyframes spark498{0%{transform:translateY(0)}to{transform:rotate(294deg) translateX(251px)}}.bg-vectors .line:nth-child(499){transform:rotateY(93deg)}.bg-vectors .line:nth-child(499) .spark{animation:spark499 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:340px;width:267px}.bg-vectors .line:nth-child(499) .fire{animation:fire 1518ms linear -662ms infinite}@keyframes spark499{0%{transform:translateY(0)}to{transform:rotate(167deg) translateX(217px)}}.bg-vectors .line:nth-child(500){transform:rotateY(204deg)}.bg-vectors .line:nth-child(500) .spark{animation:spark500 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:220px;width:370px}.bg-vectors .line:nth-child(500) .fire{animation:fire 1484ms linear -602ms infinite}@keyframes spark500{0%{transform:translateY(0)}to{transform:rotate(18deg) translateX(104px)}}.bg-vectors .line:nth-child(501){transform:rotateY(184deg)}.bg-vectors .line:nth-child(501) .spark{animation:spark501 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:322px;width:398px}.bg-vectors .line:nth-child(501) .fire{animation:fire 1348ms linear -476ms infinite}@keyframes spark501{0%{transform:translateY(0)}to{transform:rotate(197deg) translateX(533px)}}.bg-vectors .line:nth-child(502){transform:rotateY(22deg)}.bg-vectors .line:nth-child(502) .spark{animation:spark502 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:285px;width:287px}.bg-vectors .line:nth-child(502) .fire{animation:fire 1604ms linear -.82s infinite}@keyframes spark502{0%{transform:translateY(0)}to{transform:rotate(92deg) translateX(230px)}}.bg-vectors .line:nth-child(503){transform:rotateY(130deg)}.bg-vectors .line:nth-child(503) .spark{animation:spark503 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:337px;width:271px}.bg-vectors .line:nth-child(503) .fire{animation:fire 1.62s linear -153ms infinite}@keyframes spark503{0%{transform:translateY(0)}to{transform:rotate(198deg) translateX(328px)}}.bg-vectors .line:nth-child(504){transform:rotateY(108deg)}.bg-vectors .line:nth-child(504) .spark{animation:spark504 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:225px;width:367px}.bg-vectors .line:nth-child(504) .fire{animation:fire 1078ms linear -804ms infinite}@keyframes spark504{0%{transform:translateY(0)}to{transform:rotate(79deg) translateX(909px)}}.bg-vectors .line:nth-child(505){transform:rotateY(4deg)}.bg-vectors .line:nth-child(505) .spark{animation:spark505 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:268px;width:362px}.bg-vectors .line:nth-child(505) .fire{animation:fire 1903ms linear -325ms infinite}@keyframes spark505{0%{transform:translateY(0)}to{transform:rotate(147deg) translateX(454px)}}.bg-vectors .line:nth-child(506){transform:rotateY(215deg)}.bg-vectors .line:nth-child(506) .spark{animation:spark506 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:386px;width:241px}.bg-vectors .line:nth-child(506) .fire{animation:fire 1516ms linear -688ms infinite}@keyframes spark506{0%{transform:translateY(0)}to{transform:rotate(187deg) translateX(392px)}}.bg-vectors .line:nth-child(507){transform:rotateY(309deg)}.bg-vectors .line:nth-child(507) .spark{animation:spark507 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:251px;width:265px}.bg-vectors .line:nth-child(507) .fire{animation:fire 1908ms linear -794ms infinite}@keyframes spark507{0%{transform:translateY(0)}to{transform:rotate(147deg) translateX(455px)}}.bg-vectors .line:nth-child(508){transform:rotateY(236deg)}.bg-vectors .line:nth-child(508) .spark{animation:spark508 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:209px;width:336px}.bg-vectors .line:nth-child(508) .fire{animation:fire 1965ms linear -602ms infinite}@keyframes spark508{0%{transform:translateY(0)}to{transform:rotate(49deg) translateX(739px)}}.bg-vectors .line:nth-child(509){transform:rotateY(327deg)}.bg-vectors .line:nth-child(509) .spark{animation:spark509 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:287px;width:204px}.bg-vectors .line:nth-child(509) .fire{animation:fire 1.32s linear -298ms infinite}@keyframes spark509{0%{transform:translateY(0)}to{transform:rotate(324deg) translateX(679px)}}.bg-vectors .line:nth-child(510){transform:rotateY(221deg)}.bg-vectors .line:nth-child(510) .spark{animation:spark510 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:355px;width:387px}.bg-vectors .line:nth-child(510) .fire{animation:fire 1591ms linear -349ms infinite}@keyframes spark510{0%{transform:translateY(0)}to{transform:rotate(105deg) translateX(502px)}}.bg-vectors .line:nth-child(511){transform:rotateY(75deg)}.bg-vectors .line:nth-child(511) .spark{animation:spark511 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:205px;width:263px}.bg-vectors .line:nth-child(511) .fire{animation:fire 1155ms linear -354ms infinite}@keyframes spark511{0%{transform:translateY(0)}to{transform:rotate(241deg) translateX(984px)}}.bg-vectors .line:nth-child(512){transform:rotateY(123deg)}.bg-vectors .line:nth-child(512) .spark{animation:spark512 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:388px;width:316px}.bg-vectors .line:nth-child(512) .fire{animation:fire 1232ms linear -391ms infinite}@keyframes spark512{0%{transform:translateY(0)}to{transform:rotate(123deg) translateX(737px)}}.bg-vectors .line:nth-child(513){transform:rotateY(154deg)}.bg-vectors .line:nth-child(513) .spark{animation:spark513 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:379px;width:369px}.bg-vectors .line:nth-child(513) .fire{animation:fire 1031ms linear -695ms infinite}@keyframes spark513{0%{transform:translateY(0)}to{transform:rotate(34deg) translateX(196px)}}.bg-vectors .line:nth-child(514){transform:rotateY(289deg)}.bg-vectors .line:nth-child(514) .spark{animation:spark514 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:338px;width:231px}.bg-vectors .line:nth-child(514) .fire{animation:fire 1083ms linear -691ms infinite}@keyframes spark514{0%{transform:translateY(0)}to{transform:rotate(157deg) translateX(204px)}}.bg-vectors .line:nth-child(515){transform:rotateY(61deg)}.bg-vectors .line:nth-child(515) .spark{animation:spark515 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:372px;width:380px}.bg-vectors .line:nth-child(515) .fire{animation:fire 1736ms linear -646ms infinite}@keyframes spark515{0%{transform:translateY(0)}to{transform:rotate(351deg) translateX(578px)}}.bg-vectors .line:nth-child(516){transform:rotateY(25deg)}.bg-vectors .line:nth-child(516) .spark{animation:spark516 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:316px;width:389px}.bg-vectors .line:nth-child(516) .fire{animation:fire 1863ms linear -444ms infinite}@keyframes spark516{0%{transform:translateY(0)}to{transform:rotate(207deg) translateX(297px)}}.bg-vectors .line:nth-child(517){transform:rotateY(349deg)}.bg-vectors .line:nth-child(517) .spark{animation:spark517 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:362px;width:377px}.bg-vectors .line:nth-child(517) .fire{animation:fire 1703ms linear -871ms infinite}@keyframes spark517{0%{transform:translateY(0)}to{transform:rotate(120deg) translateX(1041px)}}.bg-vectors .line:nth-child(518){transform:rotateY(111deg)}.bg-vectors .line:nth-child(518) .spark{animation:spark518 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:385px;width:372px}.bg-vectors .line:nth-child(518) .fire{animation:fire 1648ms linear -478ms infinite}@keyframes spark518{0%{transform:translateY(0)}to{transform:rotate(150deg) translateX(244px)}}.bg-vectors .line:nth-child(519){transform:rotateY(209deg)}.bg-vectors .line:nth-child(519) .spark{animation:spark519 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:336px;width:352px}.bg-vectors .line:nth-child(519) .fire{animation:fire 1182ms linear -213ms infinite}@keyframes spark519{0%{transform:translateY(0)}to{transform:rotate(57deg) translateX(1083px)}}.bg-vectors .line:nth-child(520){transform:rotateY(168deg)}.bg-vectors .line:nth-child(520) .spark{animation:spark520 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:379px;width:219px}.bg-vectors .line:nth-child(520) .fire{animation:fire 1447ms linear -346ms infinite}@keyframes spark520{0%{transform:translateY(0)}to{transform:rotate(209deg) translateX(621px)}}.bg-vectors .line:nth-child(521){transform:rotateY(256deg)}.bg-vectors .line:nth-child(521) .spark{animation:spark521 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:217px;width:251px}.bg-vectors .line:nth-child(521) .fire{animation:fire 1522ms linear -426ms infinite}@keyframes spark521{0%{transform:translateY(0)}to{transform:rotate(264deg) translateX(698px)}}.bg-vectors .line:nth-child(522){transform:rotateY(60deg)}.bg-vectors .line:nth-child(522) .spark{animation:spark522 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:396px;width:273px}.bg-vectors .line:nth-child(522) .fire{animation:fire 1377ms linear -93ms infinite}@keyframes spark522{0%{transform:translateY(0)}to{transform:rotate(24deg) translateX(989px)}}.bg-vectors .line:nth-child(523){transform:rotateY(287deg)}.bg-vectors .line:nth-child(523) .spark{animation:spark523 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:260px;width:368px}.bg-vectors .line:nth-child(523) .fire{animation:fire 1573ms linear -185ms infinite}@keyframes spark523{0%{transform:translateY(0)}to{transform:rotate(67deg) translateX(909px)}}.bg-vectors .line:nth-child(524){transform:rotateY(103deg)}.bg-vectors .line:nth-child(524) .spark{animation:spark524 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:361px;width:203px}.bg-vectors .line:nth-child(524) .fire{animation:fire 1633ms linear -.21s infinite}@keyframes spark524{0%{transform:translateY(0)}to{transform:rotate(75deg) translateX(654px)}}.bg-vectors .line:nth-child(525){transform:rotateY(287deg)}.bg-vectors .line:nth-child(525) .spark{animation:spark525 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:398px;width:270px}.bg-vectors .line:nth-child(525) .fire{animation:fire 1982ms linear -117ms infinite}@keyframes spark525{0%{transform:translateY(0)}to{transform:rotate(202deg) translateX(711px)}}.bg-vectors .line:nth-child(526){transform:rotateY(138deg)}.bg-vectors .line:nth-child(526) .spark{animation:spark526 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:283px;width:287px}.bg-vectors .line:nth-child(526) .fire{animation:fire 1111ms linear -174ms infinite}@keyframes spark526{0%{transform:translateY(0)}to{transform:rotate(2deg) translateX(1009px)}}.bg-vectors .line:nth-child(527){transform:rotateY(49deg)}.bg-vectors .line:nth-child(527) .spark{animation:spark527 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:293px;width:362px}.bg-vectors .line:nth-child(527) .fire{animation:fire 1019ms linear -.59s infinite}@keyframes spark527{0%{transform:translateY(0)}to{transform:rotate(196deg) translateX(839px)}}.bg-vectors .line:nth-child(528){transform:rotateY(107deg)}.bg-vectors .line:nth-child(528) .spark{animation:spark528 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:249px;width:289px}.bg-vectors .line:nth-child(528) .fire{animation:fire 1503ms linear -3ms infinite}@keyframes spark528{0%{transform:translateY(0)}to{transform:rotate(94deg) translateX(258px)}}.bg-vectors .line:nth-child(529){transform:rotateY(40deg)}.bg-vectors .line:nth-child(529) .spark{animation:spark529 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:294px;width:235px}.bg-vectors .line:nth-child(529) .fire{animation:fire 1904ms linear -.78s infinite}@keyframes spark529{0%{transform:translateY(0)}to{transform:rotate(281deg) translateX(188px)}}.bg-vectors .line:nth-child(530){transform:rotateY(280deg)}.bg-vectors .line:nth-child(530) .spark{animation:spark530 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:376px;width:390px}.bg-vectors .line:nth-child(530) .fire{animation:fire 1987ms linear -9ms infinite}@keyframes spark530{0%{transform:translateY(0)}to{transform:rotate(128deg) translateX(1047px)}}.bg-vectors .line:nth-child(531){transform:rotateY(86deg)}.bg-vectors .line:nth-child(531) .spark{animation:spark531 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:273px;width:246px}.bg-vectors .line:nth-child(531) .fire{animation:fire 1476ms linear -923ms infinite}@keyframes spark531{0%{transform:translateY(0)}to{transform:rotate(245deg) translateX(938px)}}.bg-vectors .line:nth-child(532){transform:rotateY(37deg)}.bg-vectors .line:nth-child(532) .spark{animation:spark532 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:279px;width:325px}.bg-vectors .line:nth-child(532) .fire{animation:fire 1373ms linear -481ms infinite}@keyframes spark532{0%{transform:translateY(0)}to{transform:rotate(237deg) translateX(514px)}}.bg-vectors .line:nth-child(533){transform:rotateY(166deg)}.bg-vectors .line:nth-child(533) .spark{animation:spark533 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:360px;width:234px}.bg-vectors .line:nth-child(533) .fire{animation:fire 1621ms linear -904ms infinite}@keyframes spark533{0%{transform:translateY(0)}to{transform:rotate(83deg) translateX(663px)}}.bg-vectors .line:nth-child(534){transform:rotateY(136deg)}.bg-vectors .line:nth-child(534) .spark{animation:spark534 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:357px;width:219px}.bg-vectors .line:nth-child(534) .fire{animation:fire 1936ms linear -861ms infinite}@keyframes spark534{0%{transform:translateY(0)}to{transform:rotate(214deg) translateX(222px)}}.bg-vectors .line:nth-child(535){transform:rotateY(17deg)}.bg-vectors .line:nth-child(535) .spark{animation:spark535 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:202px;width:237px}.bg-vectors .line:nth-child(535) .fire{animation:fire 1043ms linear -287ms infinite}@keyframes spark535{0%{transform:translateY(0)}to{transform:rotate(51deg) translateX(597px)}}.bg-vectors .line:nth-child(536){transform:rotateY(296deg)}.bg-vectors .line:nth-child(536) .spark{animation:spark536 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:306px;width:256px}.bg-vectors .line:nth-child(536) .fire{animation:fire 1275ms linear -749ms infinite}@keyframes spark536{0%{transform:translateY(0)}to{transform:rotate(15deg) translateX(363px)}}.bg-vectors .line:nth-child(537){transform:rotateY(281deg)}.bg-vectors .line:nth-child(537) .spark{animation:spark537 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:301px;width:322px}.bg-vectors .line:nth-child(537) .fire{animation:fire 1558ms linear -92ms infinite}@keyframes spark537{0%{transform:translateY(0)}to{transform:rotate(244deg) translateX(442px)}}.bg-vectors .line:nth-child(538){transform:rotateY(58deg)}.bg-vectors .line:nth-child(538) .spark{animation:spark538 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:223px;width:289px}.bg-vectors .line:nth-child(538) .fire{animation:fire 1388ms linear -887ms infinite}@keyframes spark538{0%{transform:translateY(0)}to{transform:rotate(258deg) translateX(529px)}}.bg-vectors .line:nth-child(539){transform:rotateY(268deg)}.bg-vectors .line:nth-child(539) .spark{animation:spark539 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:363px;width:225px}.bg-vectors .line:nth-child(539) .fire{animation:fire 1669ms linear -903ms infinite}@keyframes spark539{0%{transform:translateY(0)}to{transform:rotate(150deg) translateX(559px)}}.bg-vectors .line:nth-child(540){transform:rotateY(249deg)}.bg-vectors .line:nth-child(540) .spark{animation:spark540 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:356px;width:380px}.bg-vectors .line:nth-child(540) .fire{animation:fire 1165ms linear -357ms infinite}@keyframes spark540{0%{transform:translateY(0)}to{transform:rotate(342deg) translateX(455px)}}.bg-vectors .line:nth-child(541){transform:rotateY(34deg)}.bg-vectors .line:nth-child(541) .spark{animation:spark541 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:363px;width:265px}.bg-vectors .line:nth-child(541) .fire{animation:fire 1775ms linear -225ms infinite}@keyframes spark541{0%{transform:translateY(0)}to{transform:rotate(27deg) translateX(1003px)}}.bg-vectors .line:nth-child(542){transform:rotateY(232deg)}.bg-vectors .line:nth-child(542) .spark{animation:spark542 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:248px;width:397px}.bg-vectors .line:nth-child(542) .fire{animation:fire 1846ms linear -338ms infinite}@keyframes spark542{0%{transform:translateY(0)}to{transform:rotate(118deg) translateX(893px)}}.bg-vectors .line:nth-child(543){transform:rotateY(132deg)}.bg-vectors .line:nth-child(543) .spark{animation:spark543 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:283px;width:379px}.bg-vectors .line:nth-child(543) .fire{animation:fire 1538ms linear -.62s infinite}@keyframes spark543{0%{transform:translateY(0)}to{transform:rotate(276deg) translateX(492px)}}.bg-vectors .line:nth-child(544){transform:rotateY(349deg)}.bg-vectors .line:nth-child(544) .spark{animation:spark544 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:252px;width:387px}.bg-vectors .line:nth-child(544) .fire{animation:fire 1985ms linear -555ms infinite}@keyframes spark544{0%{transform:translateY(0)}to{transform:rotate(35deg) translateX(738px)}}.bg-vectors .line:nth-child(545){transform:rotateY(213deg)}.bg-vectors .line:nth-child(545) .spark{animation:spark545 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:399px;width:298px}.bg-vectors .line:nth-child(545) .fire{animation:fire 1.25s linear -674ms infinite}@keyframes spark545{0%{transform:translateY(0)}to{transform:rotate(69deg) translateX(343px)}}.bg-vectors .line:nth-child(546){transform:rotateY(323deg)}.bg-vectors .line:nth-child(546) .spark{animation:spark546 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:243px;width:301px}.bg-vectors .line:nth-child(546) .fire{animation:fire 1426ms linear -.22s infinite}@keyframes spark546{0%{transform:translateY(0)}to{transform:rotate(244deg) translateX(619px)}}.bg-vectors .line:nth-child(547){transform:rotateY(258deg)}.bg-vectors .line:nth-child(547) .spark{animation:spark547 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:306px;width:347px}.bg-vectors .line:nth-child(547) .fire{animation:fire 1905ms linear -61ms infinite}@keyframes spark547{0%{transform:translateY(0)}to{transform:rotate(230deg) translateX(227px)}}.bg-vectors .line:nth-child(548){transform:rotateY(96deg)}.bg-vectors .line:nth-child(548) .spark{animation:spark548 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:224px;width:300px}.bg-vectors .line:nth-child(548) .fire{animation:fire 1262ms linear -688ms infinite}@keyframes spark548{0%{transform:translateY(0)}to{transform:rotate(305deg) translateX(915px)}}.bg-vectors .line:nth-child(549){transform:rotateY(91deg)}.bg-vectors .line:nth-child(549) .spark{animation:spark549 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:300px;width:219px}.bg-vectors .line:nth-child(549) .fire{animation:fire 1301ms linear -654ms infinite}@keyframes spark549{0%{transform:translateY(0)}to{transform:rotate(128deg) translateX(659px)}}.bg-vectors .line:nth-child(550){transform:rotateY(115deg)}.bg-vectors .line:nth-child(550) .spark{animation:spark550 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:202px;width:201px}.bg-vectors .line:nth-child(550) .fire{animation:fire 1968ms linear -629ms infinite}@keyframes spark550{0%{transform:translateY(0)}to{transform:rotate(149deg) translateX(771px)}}.bg-vectors .line:nth-child(551){transform:rotateY(9deg)}.bg-vectors .line:nth-child(551) .spark{animation:spark551 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:328px;width:396px}.bg-vectors .line:nth-child(551) .fire{animation:fire 1859ms linear -405ms infinite}@keyframes spark551{0%{transform:translateY(0)}to{transform:rotate(224deg) translateX(489px)}}.bg-vectors .line:nth-child(552){transform:rotateY(278deg)}.bg-vectors .line:nth-child(552) .spark{animation:spark552 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:357px;width:209px}.bg-vectors .line:nth-child(552) .fire{animation:fire 1347ms linear -102ms infinite}@keyframes spark552{0%{transform:translateY(0)}to{transform:rotate(359deg) translateX(249px)}}.bg-vectors .line:nth-child(553){transform:rotateY(352deg)}.bg-vectors .line:nth-child(553) .spark{animation:spark553 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:385px;width:311px}.bg-vectors .line:nth-child(553) .fire{animation:fire 1433ms linear -936ms infinite}@keyframes spark553{0%{transform:translateY(0)}to{transform:rotate(356deg) translateX(446px)}}.bg-vectors .line:nth-child(554){transform:rotateY(50deg)}.bg-vectors .line:nth-child(554) .spark{animation:spark554 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:265px;width:362px}.bg-vectors .line:nth-child(554) .fire{animation:fire 1569ms linear -155ms infinite}@keyframes spark554{0%{transform:translateY(0)}to{transform:rotate(269deg) translateX(1076px)}}.bg-vectors .line:nth-child(555){transform:rotateY(70deg)}.bg-vectors .line:nth-child(555) .spark{animation:spark555 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:270px;width:252px}.bg-vectors .line:nth-child(555) .fire{animation:fire 1768ms linear -13ms infinite}@keyframes spark555{0%{transform:translateY(0)}to{transform:rotate(284deg) translateX(380px)}}.bg-vectors .line:nth-child(556){transform:rotateY(135deg)}.bg-vectors .line:nth-child(556) .spark{animation:spark556 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:386px;width:398px}.bg-vectors .line:nth-child(556) .fire{animation:fire 1281ms linear -111ms infinite}@keyframes spark556{0%{transform:translateY(0)}to{transform:rotate(64deg) translateX(679px)}}.bg-vectors .line:nth-child(557){transform:rotateY(238deg)}.bg-vectors .line:nth-child(557) .spark{animation:spark557 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:219px;width:264px}.bg-vectors .line:nth-child(557) .fire{animation:fire 1592ms linear -395ms infinite}@keyframes spark557{0%{transform:translateY(0)}to{transform:rotate(196deg) translateX(858px)}}.bg-vectors .line:nth-child(558){transform:rotateY(32deg)}.bg-vectors .line:nth-child(558) .spark{animation:spark558 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:226px;width:240px}.bg-vectors .line:nth-child(558) .fire{animation:fire 1.48s linear -461ms infinite}@keyframes spark558{0%{transform:translateY(0)}to{transform:rotate(233deg) translateX(248px)}}.bg-vectors .line:nth-child(559){transform:rotateY(210deg)}.bg-vectors .line:nth-child(559) .spark{animation:spark559 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:288px;width:265px}.bg-vectors .line:nth-child(559) .fire{animation:fire 1047ms linear -254ms infinite}@keyframes spark559{0%{transform:translateY(0)}to{transform:rotate(357deg) translateX(824px)}}.bg-vectors .line:nth-child(560){transform:rotateY(183deg)}.bg-vectors .line:nth-child(560) .spark{animation:spark560 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:247px;width:299px}.bg-vectors .line:nth-child(560) .fire{animation:fire 1072ms linear -796ms infinite}@keyframes spark560{0%{transform:translateY(0)}to{transform:rotate(298deg) translateX(584px)}}.bg-vectors .line:nth-child(561){transform:rotateY(345deg)}.bg-vectors .line:nth-child(561) .spark{animation:spark561 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:287px;width:316px}.bg-vectors .line:nth-child(561) .fire{animation:fire 1083ms linear -672ms infinite}@keyframes spark561{0%{transform:translateY(0)}to{transform:rotate(127deg) translateX(886px)}}.bg-vectors .line:nth-child(562){transform:rotateY(199deg)}.bg-vectors .line:nth-child(562) .spark{animation:spark562 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:330px;width:350px}.bg-vectors .line:nth-child(562) .fire{animation:fire 1296ms linear -338ms infinite}@keyframes spark562{0%{transform:translateY(0)}to{transform:rotate(323deg) translateX(949px)}}.bg-vectors .line:nth-child(563){transform:rotateY(141deg)}.bg-vectors .line:nth-child(563) .spark{animation:spark563 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:230px;width:382px}.bg-vectors .line:nth-child(563) .fire{animation:fire 1784ms linear -561ms infinite}@keyframes spark563{0%{transform:translateY(0)}to{transform:rotate(64deg) translateX(677px)}}.bg-vectors .line:nth-child(564){transform:rotateY(340deg)}.bg-vectors .line:nth-child(564) .spark{animation:spark564 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:368px;width:322px}.bg-vectors .line:nth-child(564) .fire{animation:fire 1713ms linear -511ms infinite}@keyframes spark564{0%{transform:translateY(0)}to{transform:rotate(223deg) translateX(281px)}}.bg-vectors .line:nth-child(565){transform:rotateY(251deg)}.bg-vectors .line:nth-child(565) .spark{animation:spark565 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:287px;width:323px}.bg-vectors .line:nth-child(565) .fire{animation:fire 1861ms linear -509ms infinite}@keyframes spark565{0%{transform:translateY(0)}to{transform:rotate(204deg) translateX(176px)}}.bg-vectors .line:nth-child(566){transform:rotateY(338deg)}.bg-vectors .line:nth-child(566) .spark{animation:spark566 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:392px;width:307px}.bg-vectors .line:nth-child(566) .fire{animation:fire 1605ms linear -905ms infinite}@keyframes spark566{0%{transform:translateY(0)}to{transform:rotate(245deg) translateX(354px)}}.bg-vectors .line:nth-child(567){transform:rotateY(310deg)}.bg-vectors .line:nth-child(567) .spark{animation:spark567 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:378px;width:301px}.bg-vectors .line:nth-child(567) .fire{animation:fire 1292ms linear -881ms infinite}@keyframes spark567{0%{transform:translateY(0)}to{transform:rotate(127deg) translateX(1039px)}}.bg-vectors .line:nth-child(568){transform:rotateY(356deg)}.bg-vectors .line:nth-child(568) .spark{animation:spark568 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:213px;width:368px}.bg-vectors .line:nth-child(568) .fire{animation:fire 1.18s linear -983ms infinite}@keyframes spark568{0%{transform:translateY(0)}to{transform:rotate(27deg) translateX(715px)}}.bg-vectors .line:nth-child(569){transform:rotateY(179deg)}.bg-vectors .line:nth-child(569) .spark{animation:spark569 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:334px;width:338px}.bg-vectors .line:nth-child(569) .fire{animation:fire 1.36s linear -573ms infinite}@keyframes spark569{0%{transform:translateY(0)}to{transform:rotate(120deg) translateX(445px)}}.bg-vectors .line:nth-child(570){transform:rotateY(78deg)}.bg-vectors .line:nth-child(570) .spark{animation:spark570 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:202px;width:225px}.bg-vectors .line:nth-child(570) .fire{animation:fire 1.7s linear -.6s infinite}@keyframes spark570{0%{transform:translateY(0)}to{transform:rotate(147deg) translateX(590px)}}.bg-vectors .line:nth-child(571){transform:rotateY(346deg)}.bg-vectors .line:nth-child(571) .spark{animation:spark571 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:346px;width:308px}.bg-vectors .line:nth-child(571) .fire{animation:fire 1268ms linear -214ms infinite}@keyframes spark571{0%{transform:translateY(0)}to{transform:rotate(294deg) translateX(440px)}}.bg-vectors .line:nth-child(572){transform:rotateY(60deg)}.bg-vectors .line:nth-child(572) .spark{animation:spark572 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:328px;width:209px}.bg-vectors .line:nth-child(572) .fire{animation:fire 1806ms linear -492ms infinite}@keyframes spark572{0%{transform:translateY(0)}to{transform:rotate(62deg) translateX(480px)}}.bg-vectors .line:nth-child(573){transform:rotateY(328deg)}.bg-vectors .line:nth-child(573) .spark{animation:spark573 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:256px;width:252px}.bg-vectors .line:nth-child(573) .fire{animation:fire 1807ms linear -479ms infinite}@keyframes spark573{0%{transform:translateY(0)}to{transform:rotate(338deg) translateX(229px)}}.bg-vectors .line:nth-child(574){transform:rotateY(95deg)}.bg-vectors .line:nth-child(574) .spark{animation:spark574 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:297px;width:385px}.bg-vectors .line:nth-child(574) .fire{animation:fire 1025ms linear -129ms infinite}@keyframes spark574{0%{transform:translateY(0)}to{transform:rotate(181deg) translateX(1005px)}}.bg-vectors .line:nth-child(575){transform:rotateY(78deg)}.bg-vectors .line:nth-child(575) .spark{animation:spark575 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:297px;width:242px}.bg-vectors .line:nth-child(575) .fire{animation:fire 1122ms linear -942ms infinite}@keyframes spark575{0%{transform:translateY(0)}to{transform:rotate(349deg) translateX(1025px)}}.bg-vectors .line:nth-child(576){transform:rotateY(135deg)}.bg-vectors .line:nth-child(576) .spark{animation:spark576 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:332px;width:345px}.bg-vectors .line:nth-child(576) .fire{animation:fire 1.71s linear -172ms infinite}@keyframes spark576{0%{transform:translateY(0)}to{transform:rotate(37deg) translateX(1065px)}}.bg-vectors .line:nth-child(577){transform:rotateY(294deg)}.bg-vectors .line:nth-child(577) .spark{animation:spark577 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:250px;width:272px}.bg-vectors .line:nth-child(577) .fire{animation:fire 1366ms linear -456ms infinite}@keyframes spark577{0%{transform:translateY(0)}to{transform:rotate(191deg) translateX(384px)}}.bg-vectors .line:nth-child(578){transform:rotateY(128deg)}.bg-vectors .line:nth-child(578) .spark{animation:spark578 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:247px;width:395px}.bg-vectors .line:nth-child(578) .fire{animation:fire 1978ms linear -421ms infinite}@keyframes spark578{0%{transform:translateY(0)}to{transform:rotate(345deg) translateX(147px)}}.bg-vectors .line:nth-child(579){transform:rotateY(18deg)}.bg-vectors .line:nth-child(579) .spark{animation:spark579 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:215px;width:378px}.bg-vectors .line:nth-child(579) .fire{animation:fire 1009ms linear -144ms infinite}@keyframes spark579{0%{transform:translateY(0)}to{transform:rotate(227deg) translateX(901px)}}.bg-vectors .line:nth-child(580){transform:rotateY(227deg)}.bg-vectors .line:nth-child(580) .spark{animation:spark580 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:340px;width:336px}.bg-vectors .line:nth-child(580) .fire{animation:fire 1.06s linear -399ms infinite}@keyframes spark580{0%{transform:translateY(0)}to{transform:rotate(172deg) translateX(270px)}}.bg-vectors .line:nth-child(581){transform:rotateY(195deg)}.bg-vectors .line:nth-child(581) .spark{animation:spark581 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:253px;width:233px}.bg-vectors .line:nth-child(581) .fire{animation:fire 1305ms linear -395ms infinite}@keyframes spark581{0%{transform:translateY(0)}to{transform:rotate(141deg) translateX(738px)}}.bg-vectors .line:nth-child(582){transform:rotateY(244deg)}.bg-vectors .line:nth-child(582) .spark{animation:spark582 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:290px;width:207px}.bg-vectors .line:nth-child(582) .fire{animation:fire 1921ms linear -911ms infinite}@keyframes spark582{0%{transform:translateY(0)}to{transform:rotate(226deg) translateX(924px)}}.bg-vectors .line:nth-child(583){transform:rotateY(182deg)}.bg-vectors .line:nth-child(583) .spark{animation:spark583 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:238px;width:377px}.bg-vectors .line:nth-child(583) .fire{animation:fire 1132ms linear -767ms infinite}@keyframes spark583{0%{transform:translateY(0)}to{transform:rotate(75deg) translateX(624px)}}.bg-vectors .line:nth-child(584){transform:rotateY(358deg)}.bg-vectors .line:nth-child(584) .spark{animation:spark584 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:371px;width:306px}.bg-vectors .line:nth-child(584) .fire{animation:fire 1022ms linear -863ms infinite}@keyframes spark584{0%{transform:translateY(0)}to{transform:rotate(72deg) translateX(498px)}}.bg-vectors .line:nth-child(585){transform:rotateY(63deg)}.bg-vectors .line:nth-child(585) .spark{animation:spark585 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:350px;width:373px}.bg-vectors .line:nth-child(585) .fire{animation:fire 1374ms linear -.52s infinite}@keyframes spark585{0%{transform:translateY(0)}to{transform:rotate(181deg) translateX(271px)}}.bg-vectors .line:nth-child(586){transform:rotateY(327deg)}.bg-vectors .line:nth-child(586) .spark{animation:spark586 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:380px;width:298px}.bg-vectors .line:nth-child(586) .fire{animation:fire 1384ms linear -784ms infinite}@keyframes spark586{0%{transform:translateY(0)}to{transform:rotate(5deg) translateX(963px)}}.bg-vectors .line:nth-child(587){transform:rotateY(107deg)}.bg-vectors .line:nth-child(587) .spark{animation:spark587 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:322px;width:286px}.bg-vectors .line:nth-child(587) .fire{animation:fire 1052ms linear -43ms infinite}@keyframes spark587{0%{transform:translateY(0)}to{transform:rotate(211deg) translateX(854px)}}.bg-vectors .line:nth-child(588){transform:rotateY(237deg)}.bg-vectors .line:nth-child(588) .spark{animation:spark588 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:205px;width:359px}.bg-vectors .line:nth-child(588) .fire{animation:fire 1843ms linear -812ms infinite}@keyframes spark588{0%{transform:translateY(0)}to{transform:rotate(127deg) translateX(434px)}}.bg-vectors .line:nth-child(589){transform:rotateY(297deg)}.bg-vectors .line:nth-child(589) .spark{animation:spark589 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:350px;width:223px}.bg-vectors .line:nth-child(589) .fire{animation:fire 1142ms linear -492ms infinite}@keyframes spark589{0%{transform:translateY(0)}to{transform:rotate(349deg) translateX(1086px)}}.bg-vectors .line:nth-child(590){transform:rotateY(313deg)}.bg-vectors .line:nth-child(590) .spark{animation:spark590 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:246px;width:348px}.bg-vectors .line:nth-child(590) .fire{animation:fire 1706ms linear -629ms infinite}@keyframes spark590{0%{transform:translateY(0)}to{transform:rotate(88deg) translateX(577px)}}.bg-vectors .line:nth-child(591){transform:rotateY(56deg)}.bg-vectors .line:nth-child(591) .spark{animation:spark591 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:388px;width:340px}.bg-vectors .line:nth-child(591) .fire{animation:fire 1603ms linear -393ms infinite}@keyframes spark591{0%{transform:translateY(0)}to{transform:rotate(94deg) translateX(273px)}}.bg-vectors .line:nth-child(592){transform:rotateY(105deg)}.bg-vectors .line:nth-child(592) .spark{animation:spark592 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:370px;width:327px}.bg-vectors .line:nth-child(592) .fire{animation:fire 1699ms linear -36ms infinite}@keyframes spark592{0%{transform:translateY(0)}to{transform:rotate(233deg) translateX(1065px)}}.bg-vectors .line:nth-child(593){transform:rotateY(281deg)}.bg-vectors .line:nth-child(593) .spark{animation:spark593 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:323px;width:377px}.bg-vectors .line:nth-child(593) .fire{animation:fire 1688ms linear -134ms infinite}@keyframes spark593{0%{transform:translateY(0)}to{transform:rotate(291deg) translateX(404px)}}.bg-vectors .line:nth-child(594){transform:rotateY(158deg)}.bg-vectors .line:nth-child(594) .spark{animation:spark594 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:274px;width:242px}.bg-vectors .line:nth-child(594) .fire{animation:fire 1691ms linear -629ms infinite}@keyframes spark594{0%{transform:translateY(0)}to{transform:rotate(337deg) translateX(165px)}}.bg-vectors .line:nth-child(595){transform:rotateY(5deg)}.bg-vectors .line:nth-child(595) .spark{animation:spark595 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:385px;width:350px}.bg-vectors .line:nth-child(595) .fire{animation:fire 1852ms linear -65ms infinite}@keyframes spark595{0%{transform:translateY(0)}to{transform:rotate(244deg) translateX(423px)}}.bg-vectors .line:nth-child(596){transform:rotateY(281deg)}.bg-vectors .line:nth-child(596) .spark{animation:spark596 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:260px;width:364px}.bg-vectors .line:nth-child(596) .fire{animation:fire 1699ms linear -895ms infinite}@keyframes spark596{0%{transform:translateY(0)}to{transform:rotate(211deg) translateX(482px)}}.bg-vectors .line:nth-child(597){transform:rotateY(123deg)}.bg-vectors .line:nth-child(597) .spark{animation:spark597 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:388px;width:264px}.bg-vectors .line:nth-child(597) .fire{animation:fire 1918ms linear -667ms infinite}@keyframes spark597{0%{transform:translateY(0)}to{transform:rotate(152deg) translateX(399px)}}.bg-vectors .line:nth-child(598){transform:rotateY(346deg)}.bg-vectors .line:nth-child(598) .spark{animation:spark598 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:228px;width:222px}.bg-vectors .line:nth-child(598) .fire{animation:fire 1098ms linear -217ms infinite}@keyframes spark598{0%{transform:translateY(0)}to{transform:rotate(137deg) translateX(792px)}}.bg-vectors .line:nth-child(599){transform:rotateY(43deg)}.bg-vectors .line:nth-child(599) .spark{animation:spark599 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:299px;width:365px}.bg-vectors .line:nth-child(599) .fire{animation:fire 1.22s linear -703ms infinite}@keyframes spark599{0%{transform:translateY(0)}to{transform:rotate(19deg) translateX(948px)}}.bg-vectors .line:nth-child(600){transform:rotateY(291deg)}.bg-vectors .line:nth-child(600) .spark{animation:spark600 5s cubic-bezier(.39,.575,.565,1) infinite,opacity 5s ease-out infinite;height:306px;width:312px}.bg-vectors .line:nth-child(600) .fire{animation:fire 1786ms linear -429ms infinite}@keyframes spark600{0%{transform:translateY(0)}to{transform:rotate(50deg) translateX(961px)}}@keyframes opacity{0%{opacity:0}40%{opacity:1}}@keyframes fire{0%{transform:rotateX(0deg) rotateY(0deg) rotate(0deg)}to{transform:rotateX(1turn) rotateY(2turn) rotate(3turn)}}
.lightbox{text-align:center;line-height:0;position:absolute;left:0}.lightboxOverlay{position:absolute;top:0;left:0;z-index:9999;background-color:#000;opacity:.8}.lightbox{width:100%;z-index:10000;font-weight:400;outline:0}.lb-outerContainer:after{content:"";display:table;clear:both}.lb-nav a.lb-prev:hover{opacity:1}.lb-nav a.lb-next:hover{opacity:1}.lb-dataContainer:after{content:"";display:table;clear:both}.lb-data .lb-close:hover{cursor:pointer;opacity:1}


body{font-family:{
                    {
                    $vcard->font_family
                }
            }}{! ! $vcard->custom_css  ! !}
.sf-hidden{display:none!important}
<style name=www-roboto nonce>
.html5-video-player{position:relative;width:100%;height:100%;overflow:hidden;z-index:0;outline:0;font-family:YouTube Noto,Roboto,Arial,Helvetica,sans-serif;color:#eee;text-align:left;direction:ltr;font-size:11px;line-height:1.3;-webkit-font-smoothing:antialiased;-webkit-tap-highlight-color:rgba(0,0,0,0);touch-action:manipulation}.html5-video-player{-ms-high-contrast-adjust:none;forced-color-adjust:none}.html5-video-player:not(.ytp-transparent){background-color:#000}.ytp-autohide{cursor:none}.html5-video-player a:hover{color:#fff;-webkit-transition:color .1s cubic-bezier(.4,0,1,1);transition:color .1s cubic-bezier(.4,0,1,1)}.ytp-probably-keyboard-focus a:focus{-webkit-box-shadow:inset 0 0 0 2px rgba(27,127,204,.8);box-shadow:inset 0 0 0 2px rgba(27,127,204,.8)}.html5-video-player:not(.ytp-touch-mode) ::-webkit-scrollbar{width:10px;background-color:#424242}.ytp-big-mode:not(.ytp-touch-mode) ::-webkit-scrollbar{width:15px}.html5-video-player:not(.ytp-touch-mode) ::-webkit-scrollbar-track{background-color:#424242}.html5-video-player:not(.ytp-touch-mode) ::-webkit-scrollbar-thumb{background-color:#8e8e8e;border:1px solid #424242;border-radius:5px}.ytp-big-mode:not(.ytp-touch-mode) ::-webkit-scrollbar-thumb{border-radius:8px}.html5-video-container{z-index:10;position:relative}.html5-main-video{outline:0}.ytp-fit-cover-video .html5-main-video{-o-object-fit:cover;object-fit:cover}.html5-main-video[data-no-fullscreen=true]::-webkit-media-controls-fullscreen-button{display:none}.html5-main-video:not([controls])::-webkit-media-controls,.html5-main-video:not([controls])::-webkit-media-controls-start-playback-button{display:none}.html5-main-video::-webkit-media-controls-timeline{display:inline}.unstarted-mode .html5-main-video::-webkit-media-controls-start-playback-button{display:none}@media screen and (max-width:325px){.html5-main-video::-webkit-media-controls-wireless-playback-picker-button{display:none}}.html5-main-video::-webkit-media-controls-current-time-display,.html5-main-video::-webkit-media-controls-time-remaining-display{display:-webkit-flex}.ytp-player-content{position:absolute}.ytp-player-content.ytp-timely-actions-content{left:12px;right:12px}.ytp-autohide .ytp-player-content:not(.html5-endscreen),.ytp-autohide .ytp-player-content:not(.ytp-upnext){top:0;-webkit-transition:bottom .1s cubic-bezier(.4,0,1,1),top .1s cubic-bezier(.4,0,1,1);transition:bottom .1s cubic-bezier(.4,0,1,1),top .1s cubic-bezier(.4,0,1,1)}.ytp-autohide:not(.ytp-ad-overlay-open) .ytp-timely-actions-content,.ytp-hide-controls .ytp-timely-actions-content{bottom:12px}.ytp-button{border:none;background-color:transparent;text-align:inherit;font-family:inherit;line-height:inherit}.ytp-button,.ytp-button:focus{outline:0}.ytp-button::-moz-focus-inner{padding:0;border:0}.ytp-button:not([aria-disabled=true]):not([disabled]):not([aria-hidden=true]){cursor:pointer}.ytp-probably-keyboard-focus .ytp-button:focus{-webkit-box-shadow:inset 0 0 0 2px rgba(27,127,204,.8);box-shadow:inset 0 0 0 2px rgba(27,127,204,.8)}.ytp-delhi-modern .ytp-chrome-controls .ytp-button[aria-pressed]:after{display:none}.ytp-delhi-modern .ytp-chrome-controls .ytp-right-controls .ytp-button:before{content:&quot;&quot;;position:absolute;display:block;top:50%;left:50%;-webkit-transform:translate(-50%,-50%);-ms-transform:translate(-50%,-50%);transform:translate(-50%,-50%);border-radius:40px;background-color:transparent;pointer-events:none;width:48px;height:calc(var(--yt-delhi-pill-height,48px) - 8px)}.ytp-delhi-modern .ytp-chrome-controls .ytp-right-controls .ytp-button:hover:before{background-color:var(--yt-sys-color-baseline--overlay-button-secondary,rgba(255,255,255,.1));-webkit-transition:background-color .2s cubic-bezier(.05,0,0,1);transition:background-color .2s cubic-bezier(.05,0,0,1)}.ytp-delhi-modern .ytp-chrome-controls .ytp-right-controls .ytp-button:active:before{background-color:var(--yt-sys-color-baseline--overlay-tonal-hover,rgba(255,255,255,.2));-webkit-transition:background-color .2s cubic-bezier(.05,0,0,1);transition:background-color .2s cubic-bezier(.05,0,0,1)}.ytp-delhi-modern .ytp-chrome-controls .ytp-next-button:not(.ytp-miniplayer-button-container>*):after{content:&quot;&quot;;position:absolute;inset:4px;background:transparent;pointer-events:none;border-radius:28px;inset:4px 4px 4px 0}.ytp-delhi-modern .ytp-chrome-controls .ytp-next-button:not(.ytp-miniplayer-button-container>*):hover:after{background-color:var(--yt-sys-color-baseline--overlay-button-secondary,rgba(255,255,255,.1));-webkit-transition:background-color .2s cubic-bezier(.05,0,0,1);transition:background-color .2s cubic-bezier(.05,0,0,1)}.ytp-delhi-modern .ytp-chrome-controls .ytp-next-button:not(.ytp-miniplayer-button-container>*):active:after{background-color:var(--yt-sys-color-baseline--overlay-tonal-hover,rgba(255,255,255,.2));-webkit-transition:background-color .2s cubic-bezier(.05,0,0,1);transition:background-color .2s cubic-bezier(.05,0,0,1)}.ytp-delhi-modern.ytp-big-mode:not(.ytp-xsmall-width-mode) .ytp-chrome-controls .ytp-right-controls .ytp-button:before{height:calc(var(--yt-delhi-big-mode-pill-height,56px) - 8px);width:var(--yt-delhi-big-mode-pill-height,56px)}.ytp-play-button:not(.ytp-play-button-playlist):before,.ytp-prev-button:before{content:&quot;&quot;;display:block;width:12px;position:absolute;top:5px;bottom:0;left:-12px}.ytp-delhi-modern .ytp-play-button:after{content:&quot;&quot;;position:absolute;inset:4px;background:transparent;pointer-events:none;border-radius:28px;border-radius:27px}.ytp-big-mode.ytp-delhi-modern .ytp-play-button:after{border-radius:calc(var(--yt-delhi-big-mode-bottom-controls-height,96px)/2)}.ytp-delhi-modern .ytp-play-button:hover:after,.ytp-delhi-modern.ytp-big-mode .ytp-play-button:hover:after{background-color:var(--yt-sys-color-baseline--overlay-button-secondary,rgba(255,255,255,.1));-webkit-transition:background-color .2s cubic-bezier(.05,0,0,1);transition:background-color .2s cubic-bezier(.05,0,0,1)}.ytp-delhi-modern .ytp-play-button:active:after,.ytp-delhi-modern.ytp-big-mode .ytp-play-button:active:after{background-color:var(--yt-sys-color-baseline--overlay-tonal-hover,rgba(255,255,255,.2));-webkit-transition:background-color .2s cubic-bezier(.05,0,0,1);transition:background-color .2s cubic-bezier(.05,0,0,1)}.ytp-delhi-modern .ytp-prev-button:not(.ytp-miniplayer-button-container>*):after{content:&quot;&quot;;position:absolute;inset:4px;background:transparent;pointer-events:none;border-radius:28px;inset:4px 0 4px 4px}.ytp-delhi-modern .ytp-prev-button:not(.ytp-miniplayer-button-container>*):hover:after{background-color:var(--yt-sys-color-baseline--overlay-button-secondary,rgba(255,255,255,.1));-webkit-transition:background-color .2s cubic-bezier(.05,0,0,1);transition:background-color .2s cubic-bezier(.05,0,0,1)}.ytp-delhi-modern .ytp-prev-button:not(.ytp-miniplayer-button-container>*):active:after{background-color:var(--yt-sys-color-baseline--overlay-tonal-hover,rgba(255,255,255,.2));-webkit-transition:background-color .2s cubic-bezier(.05,0,0,1);transition:background-color .2s cubic-bezier(.05,0,0,1)}.ytp-fullscreen-button:after{content:&quot;&quot;;display:block;width:12px;position:absolute;top:5px;bottom:0;left:100%}.ytp-big-mode .ytp-play-button:not(.ytp-play-button-playlist):before,.ytp-big-mode .ytp-prev-button:before{width:24px;left:-24px}.ytp-chrome-controls .ytp-button:not([aria-disabled=true]):not([disabled]):hover,.ytp-chrome-top .ytp-button:hover,.ytp-replay-button:hover{opacity:1;-webkit-transition:opacity .1s cubic-bezier(0,0,.2,1);transition:opacity .1s cubic-bezier(0,0,.2,1)}.ytp-svg-fill{fill:#fff}.ytp-chrome-controls .ytp-button[aria-pressed]:after{content:&quot;&quot;;display:block;position:absolute;width:0;height:3px;border-radius:3px;left:24px;bottom:9px;background-color:var(--yt-sys-color-baseline--red-indicator,#e1002d);-webkit-transition:left .1s cubic-bezier(.4,0,1,1),width .1s cubic-bezier(.4,0,1,1);transition:left .1s cubic-bezier(.4,0,1,1),width .1s cubic-bezier(.4,0,1,1)}.ytp-embed .ytp-chrome-controls .ytp-button[aria-pressed]:after{height:2px;border-radius:2px;left:20px;bottom:8px}.ytp-big-mode .ytp-chrome-controls .ytp-button[aria-pressed]:after{height:3px;border-radius:3px;left:27px;bottom:10px}.ytp-dni .ytp-chrome-controls .ytp-button[aria-pressed]:after{background-color:#fff}.ytp-chrome-controls .ytp-button[aria-pressed=true]:after{width:24px;left:12px;-webkit-transition:left .25s cubic-bezier(0,0,.2,1),width .25s cubic-bezier(0,0,.2,1);transition:left .25s cubic-bezier(0,0,.2,1),width .25s cubic-bezier(0,0,.2,1)}.ytp-small-mode .ytp-chrome-controls .ytp-button[aria-pressed=true]:after,.ytp-xsmall-width-mode .ytp-chrome-controls .ytp-button[aria-pressed=true]:after{width:18px;left:9px}.ytp-embed .ytp-chrome-controls .ytp-button[aria-pressed=true]:after{width:20px;left:10px}.ytp-big-mode .ytp-chrome-controls .ytp-button[aria-pressed=true]:after{width:27px;left:14px}.ytp-embed-mobile .ytp-chrome-controls .ytp-button[aria-pressed=true]:after,.ytp-embed-mobile.ytp-small-mode .ytp-chrome-controls .ytp-button[aria-pressed=true]:after{left:15px}.ytp-color-white .ytp-chrome-controls .ytp-button[aria-pressed]:after{background-color:#ddd}.ytp-color-party .ytp-chrome-controls .ytp-button[aria-pressed]:after{-webkit-animation:ytp-party-background-color .1s linear infinite;animation:ytp-party-background-color .1s linear infinite}.ytp-delhi-modern .ytp-left-controls .ytp-mute-button.ytp-standalone-mute-button:after{content:&quot;&quot;;position:absolute;inset:4px;background:transparent;pointer-events:none;border-radius:28px}.ytp-delhi-modern .ytp-left-controls .ytp-mute-button.ytp-standalone-mute-button:hover:after{background-color:var(--yt-sys-color-baseline--overlay-button-secondary,rgba(255,255,255,.1));-webkit-transition:background-color .2s cubic-bezier(.05,0,0,1);transition:background-color .2s cubic-bezier(.05,0,0,1)}.ytp-delhi-modern .ytp-left-controls .ytp-mute-button.ytp-standalone-mute-button:active:after{background-color:var(--yt-sys-color-baseline--overlay-tonal-hover,rgba(255,255,255,.2));-webkit-transition:background-color .2s cubic-bezier(.05,0,0,1);transition:background-color .2s cubic-bezier(.05,0,0,1)}.ytp-delhi-modern.ytp-big-mode:not(.ytp-xsmall-width-mode) .ytp-right-controls .ytp-right-controls-left:after{height:28px;margin:10px 14px}.ytp-delhi-modern.ytp-xsmall-width-mode .ytp-chrome-bottom .ytp-button:not(.ytp-live-badge):before{border-radius:50%;height:32px;width:32px}.ytp-delhi-modern.ytp-xsmall-width-mode .ytp-right-controls .ytp-right-controls-left:after{display:none}.ytp-delhi-modern.ytp-delhi-horizontal-volume-controls .ytp-volume-area:after{content:&quot;&quot;;position:absolute;inset:4px;background:transparent;pointer-events:none;border-radius:28px}.ytp-delhi-modern.ytp-delhi-horizontal-volume-controls .ytp-volume-area:hover:after{background-color:var(--yt-sys-color-baseline--overlay-button-secondary,rgba(255,255,255,.1));-webkit-transition:background-color .2s cubic-bezier(.05,0,0,1);transition:background-color .2s cubic-bezier(.05,0,0,1)}@-webkit-keyframes ytp-party-background-color{0%{background-color:#f00}20%{background-color:#0f0}40%{background-color:#00f}60%{background-color:#f0f}80%{background-color:#ff0}to{background-color:#0ff}}@keyframes ytp-party-background-color{0%{background-color:#f00}20%{background-color:#0f0}40%{background-color:#00f}60%{background-color:#f0f}80%{background-color:#ff0}to{background-color:#0ff}}@-webkit-keyframes ytp-party-color{0%{color:#f00}20%{color:#0f0}40%{color:#00f}60%{color:#f0f}80%{color:#ff0}to{color:#0ff}}@keyframes ytp-party-color{0%{color:#f00}20%{color:#0f0}40%{color:#00f}60%{color:#f0f}80%{color:#ff0}to{color:#0ff}}@-webkit-keyframes ytp-party-fill{0%{fill:#f00}20%{fill:#0f0}40%{fill:#00f}60%{fill:#f0f}80%{fill:#ff0}to{fill:#0ff}}@keyframes ytp-party-fill{0%{fill:#f00}20%{fill:#0f0}40%{fill:#00f}60%{fill:#f0f}80%{fill:#ff0}to{fill:#0ff}}.html5-video-player .video-stream{display:block;position:absolute}.ytp-probably-keyboard-focus .ytp-ad-player-overlay-updated-focus-style.ytp-ad-player-overlay .ytp-ad-button:focus,.ytp-probably-keyboard-focus .ytp-ad-player-overlay-updated-focus-style.ytp-ad-player-overlay button:focus{-webkit-box-shadow:inset 0 0 0 2px rgba(27,127,204,.8);box-shadow:inset 0 0 0 2px rgba(27,127,204,.8)}.ytp-ad-button-link:active,.ytp-ad-button-link:hover{background:transparent;text-decoration:underline;-webkit-box-shadow:none;box-shadow:none}.ytp-flyout-cta:hover{background-color:transparent}.ytp-flyout-cta-large:hover{background-color:rgba(0,0,0,.7)}.ytp-flyout-cta:hover .ytp-flyout-cta-body{background-color:rgb(255,255,255)}.ytp-flyout-cta:hover .ytp-flyout-cta-body-large{background-color:transparent}.ytp-flyout-cta .ytp-flyout-cta-action-button:hover{background:#126db3}.ytp-flyout-cta .ytp-flyout-cta-action-button:active{background:#095b99;-webkit-box-shadow:inset 0 1px 0 rgba(0,0,0,.5);box-shadow:inset 0 1px 0 rgba(0,0,0,.5)}.ytp-ad-duration-remaining--clean-player:before{content:&quot;•&quot;;padding-inline:4px}.html5-video-player .ytp-ad-info-hover-text-button .ytp-ad-hover-text-container a:hover{color:#167ac6;text-decoration:underline}.ytp-ad-info-hover-text-button:hover .ytp-ad-hover-text-container,.ytp-ad-overlay-ad-info-button-container:hover .ytp-ad-hover-text-container{display:inline-block}.ytp-ad-info-hover-text-button .ytp-ad-button:hover,.ytp-ad-info-icon-button:hover{opacity:1}.ytp-ad-confirm-dialog-close-overlay-button:hover,.ytp-ad-feedback-dialog-close-button:hover,.ytp-ad-info-dialog-close-button:hover{opacity:1}.ytp-ad-feedback-dialog-form a:hover,.ytp-ad-info-dialog-form a:hover{color:#167ac6;cursor:pointer}.ytp-ad-confirm-dialog-cancel-button:hover,.ytp-ad-confirm-dialog-confirm-button:hover,.ytp-ad-feedback-dialog-cancel-button:hover,.ytp-ad-feedback-dialog-confirm-button:hover,.ytp-ad-info-dialog-confirm-button:hover{color:#167ac6;cursor:pointer;text-transform:uppercase}.ytp-ad-visit-advertiser-button:before{content:&quot;&quot;;cursor:pointer;position:absolute;left:-2px;right:-2px;top:-7px;bottom:-8px}.ytp-ad-visit-advertiser-button:hover .ytp-ad-button-text{color:rgb(255,255,255);text-decoration:underline}.ytp-ad-visit-advertiser-button:hover .ytp-ad-button-icon{opacity:1}.ytp-ad-preview-container-detached:before{content:&quot;&quot;;display:inline-block;height:100%;vertical-align:middle}.ytp-ad-skip-button:hover{background:rgba(0,0,0,.9);border:1px solid rgb(255,255,255);border-right:0}.ytp-ad-skip-button-modern:hover{background:-webkit-gradient(linear,left top,left bottom,from(rgba(255,255,255,.2)),to(rgba(255,255,255,.2))),-webkit-gradient(linear,left top,left bottom,from(rgb(15,15,15)),to(rgb(15,15,15)));background:-webkit-linear-gradient(rgba(255,255,255,.2),rgba(255,255,255,.2)),-webkit-linear-gradient(rgb(15,15,15),rgb(15,15,15));background:linear-gradient(rgba(255,255,255,.2),rgba(255,255,255,.2)),linear-gradient(rgb(15,15,15),rgb(15,15,15))}.ytp-ad-skip-button-container .ytp-ad-skip-button-modern:focus{background:rgb(15,15,15);outline:none;-webkit-box-shadow:none;box-shadow:none}.ytp-ad-skip-button-container .ytp-ad-skip-button-modern:focus-visible{background:#fff;-webkit-box-shadow:0 0 0 2px #0f0f0f;box-shadow:0 0 0 2px #0f0f0f;color:#0f0f0f}.ytp-ad-skip-button-modern:focus-visible .ytp-ad-skip-button-icon-modern svg path{fill:#0f0f0f}.ytp-ad-text-overlay:hover{border:1px solid rgb(58,58,58)}.ytp-ad-text-overlay:hover .ytp-ad-overlay-title{text-decoration:underline}.ytp-ad-image-overlay:hover{text-decoration:underline}.ytp-ad-overlay-ad-info-button-container:hover .ytp-ad-button,.ytp-ad-overlay-close-container:hover .ytp-ad-overlay-close-button{fill-opacity:1}.ytp-ad-overlay-attribution:hover{color:#fff;-webkit-transition:color .1s cubic-bezier(0,0,.2,1);transition:color .1s cubic-bezier(0,0,.2,1)}.ytp-ad-action-interstitial-action-button.ytp-ad-action-interstitial-action-button-unified:before{content:&quot;&quot;;position:absolute;inset:-6px 0;cursor:pointer}.ytp-ad-action-interstitial-action-button.ytp-ad-action-interstitial-action-button-unified-mono:before{content:&quot;&quot;;position:absolute;inset:-6px 0;cursor:pointer}.ytp-ad-survey-answer-button:hover,.ytp-ad-survey-answer-toggle-button:hover{font-weight:500}.ytp-ad-toggle-button:hover .ytp-ad-toggle-button-tooltip{display:inline-block}.ytp-ad-instream-user-sentiment-container .ytp-ad-toggle-button:hover .ytp-ad-toggle-button-icon{opacity:1}.ytp-ad-instream-user-sentiment-container .ytp-ad-toggle-button:hover .ytp-ad-toggle-button-tooltip{bottom:46px;right:20px}.ytp-ad-player-overlay-top-bar-gradients .ytp-ad-visit-advertiser-button:before{content:none}.ytp-ad-clickable-element:hover{cursor:pointer}.ytp-ad-underlay-action-button:hover{background-color:#e9e9e9}.ytp-ad-underlay-action-button-blue:hover{background-color:#66b8ff}.ytp-probably-keyboard-focus .ytp-ad-underlay-action-button:focus{outline:none;border:2px solid #fff;background:transparent;color:#fff}.ytp-probably-keyboard-focus .ytp-ad-underlay-action-button-blue:focus{outline:none;border:2px solid #3ea6ff;background:transparent;color:#3ea6ff}.ytp-ad-badge:after{content:&quot;·&quot;;padding-inline:4px}.ytp-ad-badge__pod-index:before{content:&quot;·&quot;;padding-inline:4px 3px}.ytp-ad-pod-index:before{content:&quot;•&quot;;padding-inline:4px 3px}.ytp-ad-button-vm--style-transparent:hover{background-color:rgba(255,255,255,.2)}.ytp-ad-button-vm--style-filled:hover{background-color:#0556bf}.ytp-ad-button-vm--style-filled-white:hover{background-color:#d9d9d9}.ytp-ad-button-vm--style-filled-white:active{background-color:#d9d9d9}.ytp-ad-button-vm--style-filled-dark:hover{background-color:#65b8ff}.ytp-ad-button-vm--style-mono-filled:hover{background-color:#3f3f3f}.ytp-ad-button-vm--style-mono-filled:active{background-color:#3f3f3f}.ytp-ad-avatar-lockup-card:hover{background-color:#fff}.ytp-ad-avatar-lockup-card--large:hover{background-color:rgba(0,0,0,.7)}.ytp-ad-avatar-lockup-card__button--large:hover{background-color:#65b8ff}.ytp-video-interstitial-buttoned-centered-layout .ytp-ad-badge:after{padding-right:unset}.ytp-video-interstitial-buttoned-centered-layout .ytp-ad-button-vm:before{content:&quot;&quot;;position:absolute;inset:-6px 0;cursor:pointer}.ytp-skip-ad-button:hover{background:-webkit-gradient(linear,left top,left bottom,from(rgba(255,255,255,.2)),to(rgba(255,255,255,.2))),-webkit-gradient(linear,left top,left bottom,from(rgb(15,15,15)),to(rgb(15,15,15)));background:-webkit-linear-gradient(rgba(255,255,255,.2),rgba(255,255,255,.2)),-webkit-linear-gradient(rgb(15,15,15),rgb(15,15,15));background:linear-gradient(rgba(255,255,255,.2),rgba(255,255,255,.2)),linear-gradient(rgb(15,15,15),rgb(15,15,15))}.ytp-skip-ad-button:focus{background:rgb(15,15,15);outline:none;-webkit-box-shadow:none;box-shadow:none}.ytp-skip-ad-button:focus-visible{background:#fff;-webkit-box-shadow:0 0 0 2px #0f0f0f;box-shadow:0 0 0 2px #0f0f0f;color:#0f0f0f}.ytp-skip-ad-button:focus-visible .ytp-skip-ad-button__icon svg path{fill:#0f0f0f}.ytp-delhi-modern .ytp-skip-ad-button:after{content:&quot;&quot;;position:absolute;display:block;left:0;width:100%;height:100%}.ytp-delhi-modern .ytp-skip-ad-button:hover:after{background-color:var(--yt-sys-color-baseline--overlay-button-secondary,rgba(255,255,255,.1));-webkit-transition:background-color .2s cubic-bezier(.05,0,0,1);transition:background-color .2s cubic-bezier(.05,0,0,1)}.ytp-delhi-modern .ytp-skip-ad-button:active:after{background-color:var(--yt-sys-color-baseline--overlay-tonal-hover,rgba(255,255,255,.2));-webkit-transition:background-color .2s cubic-bezier(.05,0,0,1);transition:background-color .2s cubic-bezier(.05,0,0,1)}.ytp-visit-advertiser-link:before{content:&quot;&quot;;cursor:pointer;position:absolute;inset:-8px}.ytp-visit-advertiser-link:hover .ytp-visit-advertiser-link__text{color:rgb(255,255,255);text-decoration:underline}.annotation-close-button:hover{opacity:1}.iv-branding img.iv-click-target:hover{opacity:1}.iv-promo:hover{background-color:rgba(0,0,0,.9)}.iv-promo .iv-promo-contents .iv-promo-txt .iv-promo-link:after{position:absolute;top:2px;right:0;opacity:.5;content:&quot;&quot;}.iv-promo .iv-promo-contents .iv-promo-txt:hover .iv-promo-link:after{opacity:1}.iv-button:hover{text-decoration:none}.iv-button:focus,.iv-button:focus:hover{-webkit-box-shadow:0 0 0 2px rgba(27,127,204,.4);box-shadow:0 0 0 2px rgba(27,127,204,.4)}.no-focus-outline .yt-uix-button:focus,.no-focus-outline .yt-uix-button:focus:hover{-webkit-box-shadow:none;box-shadow:none}.iv-button::-moz-focus-inner{border:0;padding:0}.iv-button[disabled]:active,.iv-button[disabled]:focus,.iv-button[disabled]:hover{opacity:.5;cursor:auto;-webkit-box-shadow:none;box-shadow:none}.iv-button[disabled]:active,.iv-button[disabled]:focus,.iv-button[disabled]:hover{border-color:#167ac6;background:#167ac6;color:#fff}.iv-button:hover{background:#126db3}.iv-button:active{background:#095b99;-webkit-box-shadow:inset 0 1px 0 rgba(0,0,0,.5);box-shadow:inset 0 1px 0 rgba(0,0,0,.5)}a.iv-button:after{content:&quot;&quot;;display:inline-block;vertical-align:middle;height:100%}.iv-promo .iv-promo-actions .iv-promo-close:after,.iv-promo .iv-promo-actions .iv-promo-expand:after{display:block;content:&quot;&quot;}.iv-promo .iv-promo-actions .iv-promo-close:after{margin:16px 10px 15px 12px;opacity:.5}.iv-promo .iv-promo-actions .iv-promo-expand:after{margin:16px 12px 15px}.iv-promo-website-card-cta-redesign:hover{background-color:transparent}.iv-promo-website-card-cta-redesign:hover .iv-promo-contents{background-color:rgb(255,255,255)}.iv-promo-website-card-cta-redesign .iv-promo-contents .iv-promo-txt .iv-promo-link:after{display:none}.iv-promo-website-card-cta-redesign .iv-promo-round-expand-icon:after{display:block;content:&quot;&quot;}.iv-drawer-content::-webkit-scrollbar{background-color:transparent;width:16px}.iv-drawer-content::-webkit-scrollbar-thumb{border:4px solid transparent;border-radius:8px;background-clip:content-box;background-color:rgba(102,102,102,.5)}.iv-drawer-content::-webkit-scrollbar-track{background-color:transparent}.ytp-autohide .iv-drawer-content::-webkit-scrollbar-thumb{background-color:transparent}.iv-drawer-content:hover::-webkit-scrollbar-thumb{background-color:#666!important}.iv-drawer-close-button:after{display:block;content:&quot;&quot;}.iv-drawer-close-button:hover{opacity:1}.iv-card a.iv-click-target:focus,.iv-card a.iv-click-target:hover{display:block;color:#767676;text-decoration:none}.iv-card:hover .iv-card-primary-link{color:#167ac6!important}.webkit .iv-card h2:after,.webkit .iv-card-action:after{content:&quot;‌&quot;;position:static;visibility:hidden}.iv-card-playlist-video-count:after{display:block;margin:auto;opacity:.5;content:&quot;&quot;}.iv-card-unavailable:hover .iv-card-content,.iv-card-unavailable:hover .iv-click-target{visibility:hidden}.iv-card-unavailable:hover .iv-card-sign-in{visibility:visible}.iv-ad-info-container .iv-ad-info a:hover{color:#167ac6}.iv-ad-info-container .iv-ad-info a:hover{text-decoration:underline}.iv-ad-info-icon-container:after{left:0;border-left:5px solid transparent;border-right:5px solid transparent;border-top:5px solid;width:0;height:0}.iv-ad-info-icon-container:after{content:&quot;&quot;;bottom:17px;border-top-color:#fff}.iv-ad-info-icon-container:after{visibility:hidden;position:absolute;-webkit-transition:visibility 0s .1s;transition:visibility 0s .1s}.iv-ad-info-container:hover .iv-ad-info,.iv-ad-info-container:hover .iv-ad-info-callout,.iv-ad-info-container:hover .iv-ad-info-icon-container:after{visibility:visible;-webkit-transition-delay:0s;transition-delay:0s}.ytp-autonav-endscreen-upnext-thumbnail:hover,.ytp-autonav-thumbnail-small:hover{border-color:rgba(255,255,255,.8);-webkit-box-sizing:border-box;box-sizing:border-box}.ytp-autonav-toggle-button:after{content:&quot;&quot;;position:absolute;top:0;left:0;height:20.4px;width:20.4px;border-radius:20.4px;margin-top:-3px;background-image:url(/images/templates/eventmanagementx/eve-020.svg);-webkit-background-size:cover;background-size:cover;-webkit-transition:all .08s cubic-bezier(.4,0,1,1);transition:all .08s cubic-bezier(.4,0,1,1)}.ytp-delhi-modern.ytp-delhi-modern-icons .ytp-autonav-toggle-button:after{height:14px;width:14px;border-radius:7px;margin:2px;background-image:url(/images/templates/eventmanagementx/eve-021.svg);background-color:rgba(255,255,255,.3);-webkit-background-size:9px 9px;background-size:9px;background-repeat:no-repeat;background-position:50%;-webkit-transition:all .2s cubic-bezier(.05,0,0,1);transition:all .2s cubic-bezier(.05,0,0,1)}.ytp-delhi-modern.ytp-delhi-modern-icons .delhi-fast-follow-autonav-toggle .ytp-autonav-toggle-button:after{background-image:url(/images/templates/eventmanagementx/eve-022.svg);background-color:var(--yt-sys-color-baseline--overlay-text-primary,#fff)}.ytp-xsmall-width-mode.ytp-delhi-modern-icons.ytp-delhi-modern .ytp-autonav-toggle-button:after{height:10.5px;width:10.5px;border-radius:5.25px;margin:1px}.ytp-small-mode .ytp-autonav-toggle-button:after{left:-2px;height:15.3px;width:15.3px;border-radius:15.3px;margin-top:-2.25px}.ytp-big-mode .ytp-autonav-toggle-button:after{left:0;height:25.5px;width:25.5px;border-radius:25.5px;margin-top:-3.75px}.ytp-embed .ytp-autonav-toggle-button:after{left:0;height:17px;width:17px;border-radius:17px;margin-top:-2.5px}.ytp-autonav-toggle-button[aria-checked=true]:after{background-image:url(/images/templates/eventmanagementx/eve-023.svg);left:1px;-webkit-background-size:cover;background-size:cover;background-color:transparent;-webkit-transform:translateX(15.6px);-ms-transform:translateX(15.6px);transform:translateX(15.6px)}.ytp-delhi-modern.ytp-delhi-modern-icons .ytp-autonav-toggle-button[aria-checked=true]:after{background-image:url(/images/templates/eventmanagementx/eve-024.svg);background-color:#fff;-webkit-background-size:9px 9px;background-size:9px;background-repeat:no-repeat;background-position:50%;-webkit-transform:translateX(12px);-ms-transform:translateX(12px);transform:translateX(12px)}.ytp-delhi-modern.ytp-delhi-modern-icons .delhi-fast-follow-autonav-toggle .ytp-autonav-toggle-button[aria-checked=true]:after{background-image:url(/images/templates/eventmanagementx/eve-025.svg);background-color:var(--yt-sys-color-baseline--overlay-background-solid,#0f0f0f)}.ytp-small-mode .ytp-autonav-toggle-button[aria-checked=true]:after{-webkit-transform:translateX(11.7px);-ms-transform:translateX(11.7px);transform:translateX(11.7px)}.ytp-big-mode .ytp-autonav-toggle-button[aria-checked=true]:after{-webkit-transform:translateX(19.5px);-ms-transform:translateX(19.5px);transform:translateX(19.5px)}.ytp-xsmall-width-mode.ytp-delhi-modern-icons.ytp-delhi-modern .ytp-autonav-toggle-button[aria-checked=true]:after{-webkit-transform:translateX(8px);-ms-transform:translateX(8px);transform:translateX(8px)}.ytp-embed .ytp-autonav-toggle-button[aria-checked=true]:after{-webkit-transform:translateX(13px);-ms-transform:translateX(13px);transform:translateX(13px)}@-webkit-keyframes ytp-bezel-fadeout{0%{opacity:1}to{opacity:0;-webkit-transform:scale(2);transform:scale(2)}}@keyframes ytp-bezel-fadeout{0%{opacity:1}to{opacity:0;-webkit-transform:scale(2);transform:scale(2)}}@-webkit-keyframes ytp-delhi-modern-bezel-fadeout{0%{opacity:0}25%,75%{opacity:1;-webkit-transform:scale(1.33);transform:scale(1.33)}to{opacity:0;-webkit-transform:scale(1);transform:scale(1)}}@keyframes ytp-delhi-modern-bezel-fadeout{0%{opacity:0}25%,75%{opacity:1;-webkit-transform:scale(1.33);transform:scale(1.33)}to{opacity:0;-webkit-transform:scale(1);transform:scale(1)}}@-webkit-keyframes ytp-delhi-modern-bezel-text-fadeout{0%{opacity:0}25%,75%{opacity:1}to{opacity:0}}@keyframes ytp-delhi-modern-bezel-text-fadeout{0%{opacity:0}25%,75%{opacity:1}to{opacity:0}}.ytp-cards-teaser-shown .ytp-cards-teaser:hover{opacity:1}@-webkit-keyframes ytp-title-channel-fade-in{0%{background-color:transparent;max-width:10%}25%{background-color:rgba(35,35,35,.9)}75%{max-width:100%}}@keyframes ytp-title-channel-fade-in{0%{background-color:transparent;max-width:10%}25%{background-color:rgba(35,35,35,.9)}75%{max-width:100%}}@-webkit-keyframes ytp-title-channel-fade-out{0%{background-color:rgba(35,35,35,.9);width:500px}75%{background-color:rgba(35,35,35,.9);width:50px}}@keyframes ytp-title-channel-fade-out{0%{background-color:rgba(35,35,35,.9);width:500px}75%{background-color:rgba(35,35,35,.9);width:50px}}@-webkit-keyframes ytp-title-channel-fade-in-big-mode{0%{background-color:transparent;max-width:15%}25%{background-color:rgba(35,35,35,.9)}75%{max-width:100%}}@keyframes ytp-title-channel-fade-in-big-mode{0%{background-color:transparent;max-width:15%}25%{background-color:rgba(35,35,35,.9)}75%{max-width:100%}}@-webkit-keyframes ytp-title-channel-fade-out-big-mode{0%{background-color:rgba(35,35,35,.9);width:500px}75%{background-color:rgba(35,35,35,.9);width:70px}}@keyframes ytp-title-channel-fade-out-big-mode{0%{background-color:rgba(35,35,35,.9);width:500px}75%{background-color:rgba(35,35,35,.9);width:70px}}@-webkit-keyframes ytp-title-expanded-fade-in{0%{opacity:0}25%{opacity:0}to{opacity:1}}@keyframes ytp-title-expanded-fade-in{0%{opacity:0}25%{opacity:0}to{opacity:1}}@-webkit-keyframes ytp-title-expanded-fade-out{0%{opacity:1}25%{opacity:1}to{opacity:0}}@keyframes ytp-title-expanded-fade-out{0%{opacity:1}25%{opacity:1}to{opacity:0}}@-webkit-keyframes ytp-title-beacon-pulse{0%{background:black;width:40px;height:40px;left:5px;top:5px}50%{background:transparent;width:50px;height:50px;left:0;top:0}}@keyframes ytp-title-beacon-pulse{0%{background:black;width:40px;height:40px;left:5px;top:5px}50%{background:transparent;width:50px;height:50px;left:0;top:0}}.ytp-delhi-modern .ytp-chapter-title.ytp-button:after{content:&quot;&quot;;position:absolute;inset:4px;background:transparent;pointer-events:none;border-radius:28px}.ytp-delhi-modern .ytp-chapter-title.ytp-button:hover:after{background-color:var(--yt-sys-color-baseline--overlay-button-secondary,rgba(255,255,255,.1));-webkit-transition:background-color .2s cubic-bezier(.05,0,0,1);transition:background-color .2s cubic-bezier(.05,0,0,1)}.ytp-delhi-modern .ytp-chapter-title.ytp-button:active:after{background-color:var(--yt-sys-color-baseline--overlay-tonal-hover,rgba(255,255,255,.2));-webkit-transition:background-color .2s cubic-bezier(.05,0,0,1);transition:background-color .2s cubic-bezier(.05,0,0,1)}.ytp-contextmenu .ytp-collapse:hover{opacity:1}.ytp-contextmenu a:focus,.ytp-contextmenu a:hover{color:inherit;text-decoration:none}.ytp-copytext::-moz-selection{background-color:white;color:black}.yt-ui-ellipsis:after,.yt-ui-ellipsis:before{background-color:inherit;position:absolute}.yt-ui-ellipsis:before{content:&quot;…&quot;;right:0}.yt-ui-ellipsis:after{content:&quot;&quot;;height:100%;width:100%}.yt-ui-ellipsis-2:before{top:1.3em}.yt-ui-ellipsis-3:before{top:2.6em}.yt-ui-ellipsis-4:before{top:3.9em}.yt-ui-ellipsis-6:before{top:6.5em}.yt-ui-ellipsis-10:before{top:11.7em}.webkit .yt-ui-ellipsis:before{content:normal}.webkit .yt-ui-ellipsis:after{content:&quot;‌&quot;;position:static;visibility:hidden}.yt-ui-ellipsis[dir=rtl]:before{left:0;right:auto}a.ytp-ce-link:hover,a.ytp-ce-link:visited{color:#167ac6}a.ytp-ce-link:hover{text-decoration:underline}.ytp-ce-element.ytp-ce-element-show:focus,.ytp-ce-element.ytp-ce-element-show:hover{outline:none}@-webkit-keyframes arrow-fade-out-1{0%{opacity:0}17%{opacity:.9}33%{opacity:.6}50%{opacity:.3}67%{opacity:.3}83%{opacity:.3}to{opacity:0}}@keyframes arrow-fade-out-1{0%{opacity:0}17%{opacity:.9}33%{opacity:.6}50%{opacity:.3}67%{opacity:.3}83%{opacity:.3}to{opacity:0}}@-webkit-keyframes arrow-fade-out-2{0%{opacity:0}17%{opacity:.3}33%{opacity:.9}50%{opacity:.6}67%{opacity:.3}83%{opacity:.3}to{opacity:0}}@keyframes arrow-fade-out-2{0%{opacity:0}17%{opacity:.3}33%{opacity:.9}50%{opacity:.6}67%{opacity:.3}83%{opacity:.3}to{opacity:0}}@-webkit-keyframes arrow-fade-out-3{0%{opacity:0}17%{opacity:.3}33%{opacity:.3}50%{opacity:.9}67%{opacity:.6}83%{opacity:.3}to{opacity:0}}@keyframes arrow-fade-out-3{0%{opacity:0}17%{opacity:.3}33%{opacity:.3}50%{opacity:.9}67%{opacity:.6}83%{opacity:.3}to{opacity:0}}@-webkit-keyframes grow-circle{0%{-webkit-transform:scale(0);transform:scale(0)}to{-webkit-transform:scale(1) translateY(-25%);transform:scale(1) translateY(-25%)}}@keyframes grow-circle{0%{-webkit-transform:scale(0);transform:scale(0)}to{-webkit-transform:scale(1) translateY(-25%);transform:scale(1) translateY(-25%)}}.ytp-more-videos-view a.ytp-suggestion-link:focus:after{content:&quot;&quot;;position:absolute;top:0;left:0;width:100%;height:100%;-webkit-box-shadow:inset 0 0 0 2px rgba(27,127,204,.8);box-shadow:inset 0 0 0 2px rgba(27,127,204,.8)}.ytp-touch-mode .ytp-more-videos-view .ytp-suggestions::-webkit-scrollbar{display:none}.ytp-more-videos-view .ytp-next:hover,.ytp-more-videos-view .ytp-previous:hover{-webkit-box-shadow:0 4px 5px rgba(0,0,0,.2);box-shadow:0 4px 5px rgba(0,0,0,.2)}.html5-video-player:not(.ytp-shorts-mode) .ytp-more-videos-view a.ytp-suggestion-link:focus .ytp-suggestion-overlay,.html5-video-player:not(.ytp-shorts-mode) .ytp-more-videos-view a.ytp-suggestion-link:hover .ytp-suggestion-overlay{opacity:1}@-webkit-keyframes ytp-equalizer-animation{0%{-webkit-transform:scaleY(1);transform:scaleY(1)}50%{-webkit-transform:scaleY(.5);transform:scaleY(.5)}to{-webkit-transform:scaleY(1);transform:scaleY(1)}}@keyframes ytp-equalizer-animation{0%{-webkit-transform:scaleY(1);transform:scaleY(1)}50%{-webkit-transform:scaleY(.5);transform:scaleY(.5)}to{-webkit-transform:scaleY(1);transform:scaleY(1)}}.ytp-pause-overlay .ytp-collapse:hover{opacity:1}.ytp-pause-overlay .ytp-expand:hover{background:rgba(0,0,0,.8)}.ytp-related-on-error-overlay a.ytp-suggestion-link:focus .ytp-suggestion-overlay,.ytp-related-on-error-overlay a.ytp-suggestion-link:hover .ytp-suggestion-overlay{opacity:1}:not(.ytp-mweb-player) .ytp-watermark:not(.ytp-no-hover):not(.ytp-muted-autoplay-watermark):hover{opacity:1}:not(.ytp-mweb-player) .ytp-endscreen-next:hover{opacity:1;-webkit-transition:opacity .1s cubic-bezier(.4,0,1,1);transition:opacity .1s cubic-bezier(.4,0,1,1)}:not(.ytp-mweb-player) .ytp-endscreen-previous:hover{opacity:1;-webkit-transition:opacity .1s cubic-bezier(.4,0,1,1);transition:opacity .1s cubic-bezier(.4,0,1,1)}.ytp-endscreen-previous:hover{opacity:1;-webkit-transition:opacity .1s cubic-bezier(.4,0,1,1);transition:opacity .1s cubic-bezier(.4,0,1,1)}.ytp-fullscreen-grid-expand-button:before{content:&quot;&quot;;display:block;position:absolute;left:50%;top:50%;-webkit-transform:translate(-50%,-50%);-ms-transform:translate(-50%,-50%);transform:translate(-50%,-50%);width:calc(100% - 8px);height:calc(100% - 8px);border-radius:50%}:not(.ytp-grid-scrolling) .ytp-fullscreen-grid-expand-button:hover:before,:not(.ytp-grid-scrolling) .ytp-fullscreen-grid-hover-overlay:hover .ytp-fullscreen-grid-expand-button:before,:not(.ytp-grid-scrolling) .ytp-fullscreen-grid-hover-overlay:hover .ytp-more-videos-button:after,:not(.ytp-grid-scrolling) .ytp-more-videos-button:hover:after{background-color:var(--yt-sys-color-baseline--overlay-button-secondary,rgba(255,255,255,.1))}:not(.ytp-grid-scrolling) .ytp-fullscreen-grid-expand-button:active:before,:not(.ytp-grid-scrolling) .ytp-fullscreen-grid-hover-overlay:active .ytp-fullscreen-grid-expand-button:before,:not(.ytp-grid-scrolling) .ytp-fullscreen-grid-hover-overlay:active .ytp-more-videos-button:after,:not(.ytp-grid-scrolling) .ytp-more-videos-button:active:after{background-color:var(--yt-sys-color-baseline--overlay-button-secondary,rgba(255,255,255,.2))}:not(.ytp-grid-scrolling) .ytp-fullscreen-grid-peeking .ytp-fullscreen-grid-hover-overlay:has(.ytp-fullscreen-grid-expand-button:hover),:not(.ytp-grid-scrolling) .ytp-fullscreen-grid-peeking .ytp-fullscreen-grid-hover-overlay:hover{background-color:var(--yt-sys-color-baseline--overlay-button-secondary)}:not(.ytp-grid-scrolling) .ytp-fullscreen-grid-peeking .ytp-fullscreen-grid-hover-overlay:active,:not(.ytp-grid-scrolling) .ytp-fullscreen-grid-peeking .ytp-fullscreen-grid-hover-overlay:has(.ytp-fullscreen-grid-expand-button:active){background-color:var(--yt-sys-color-baseline--overlay-tonal-hover)}.ytp-more-videos-button:after{content:&quot;&quot;;position:absolute;top:4px;left:4px;width:calc(100% - 8px);height:calc(100% - 8px);border-radius:28px}.ytp-big-mode .ytp-fullscreen-button:after{width:24px}@-webkit-keyframes ytp-fullscreen-button-corner-0-animation{50%{-webkit-transform:translate(-1px,-1px);transform:translate(-1px,-1px)}}@keyframes ytp-fullscreen-button-corner-0-animation{50%{-webkit-transform:translate(-1px,-1px);transform:translate(-1px,-1px)}}@-webkit-keyframes ytp-fullscreen-button-corner-1-animation{50%{-webkit-transform:translate(1px,-1px);transform:translate(1px,-1px)}}@keyframes ytp-fullscreen-button-corner-1-animation{50%{-webkit-transform:translate(1px,-1px);transform:translate(1px,-1px)}}@-webkit-keyframes ytp-fullscreen-button-corner-2-animation{50%{-webkit-transform:translate(1px,1px);transform:translate(1px,1px)}}@keyframes ytp-fullscreen-button-corner-2-animation{50%{-webkit-transform:translate(1px,1px);transform:translate(1px,1px)}}@-webkit-keyframes ytp-fullscreen-button-corner-3-animation{50%{-webkit-transform:translate(-1px,1px);transform:translate(-1px,1px)}}@keyframes ytp-fullscreen-button-corner-3-animation{50%{-webkit-transform:translate(-1px,1px);transform:translate(-1px,1px)}}.ytp-fullscreen-button:not([aria-disabled=true]):hover .ytp-fullscreen-button-corner-0{-webkit-animation:ytp-fullscreen-button-corner-0-animation .4s cubic-bezier(.4,0,.2,1);animation:ytp-fullscreen-button-corner-0-animation .4s cubic-bezier(.4,0,.2,1)}.ytp-fullscreen-button:not([aria-disabled=true]):hover .ytp-fullscreen-button-corner-1{-webkit-animation:ytp-fullscreen-button-corner-1-animation .4s cubic-bezier(.4,0,.2,1);animation:ytp-fullscreen-button-corner-1-animation .4s cubic-bezier(.4,0,.2,1)}.ytp-fullscreen-button:not([aria-disabled=true]):hover .ytp-fullscreen-button-corner-2{-webkit-animation:ytp-fullscreen-button-corner-2-animation .4s cubic-bezier(.4,0,.2,1);animation:ytp-fullscreen-button-corner-2-animation .4s cubic-bezier(.4,0,.2,1)}.ytp-fullscreen-button:not([aria-disabled=true]):hover .ytp-fullscreen-button-corner-3{-webkit-animation:ytp-fullscreen-button-corner-3-animation .4s cubic-bezier(.4,0,.2,1);animation:ytp-fullscreen-button-corner-3-animation .4s cubic-bezier(.4,0,.2,1)}.ytp-info-panel-detail-close:hover path{fill:#fff}.ytp-input-slider::-webkit-slider-runnable-track{background:-webkit-gradient(linear,left top,right top,from(#fff),color-stop(#fff),color-stop(#666),to(#666));background:-webkit-linear-gradient(left,#fff 0,#fff var(--yt-slider-shape-gradient-percent),#666 var(--yt-slider-shape-gradient-percent),#666 100%);background:linear-gradient(to right,#fff 0,#fff var(--yt-slider-shape-gradient-percent),#666 var(--yt-slider-shape-gradient-percent),#666 100%);height:4px;border-radius:12px}.ytp-input-slider::-moz-range-track{background:linear-gradient(to right,#fff 0,#fff var(--yt-slider-shape-gradient-percent),rgba(255,255,255,.2) var(--yt-slider-shape-gradient-percent),rgba(255,255,255,.2) 100%);height:4px;border-radius:12px}.ytp-input-slider::-webkit-slider-thumb{-webkit-appearance:none;appearance:none;background:#fff;width:16px;height:16px;border-radius:8px;margin-top:-6px}.ytp-input-slider::-moz-range-thumb{-moz-appearance:none;appearance:none;background:#fff;width:16px;height:16px;border-radius:8px}.ytp-input-slider::-ms-thumb{appearance:none;background:#fff;width:16px;height:16px;border-radius:8px}.ytp-varispeed-input-slider::-webkit-slider-runnable-track{background:-webkit-gradient(linear,left top,right top,from(#fff),color-stop(#fff),color-stop(#909090),to(#909090));background:-webkit-linear-gradient(left,#fff 0,#fff var(--yt-slider-shape-gradient-percent),#909090 var(--yt-slider-shape-gradient-percent),#909090 100%);background:linear-gradient(to right,#fff 0,#fff var(--yt-slider-shape-gradient-percent),#909090 var(--yt-slider-shape-gradient-percent),#909090 100%)}@supports ((-webkit-writing-mode:vertical-rl) or (writing-mode:vertical-rl)){.ytp-vertical-slider .ytp-input-slider::-webkit-slider-runnable-track{background:-webkit-gradient(linear,left bottom,left top,from(#fff),color-stop(#fff),color-stop(#666),to(#666));background:-webkit-linear-gradient(bottom,#fff 0,#fff var(--yt-slider-shape-gradient-percent),#666 var(--yt-slider-shape-gradient-percent),#666 100%);background:linear-gradient(to top,#fff 0,#fff var(--yt-slider-shape-gradient-percent),#666 var(--yt-slider-shape-gradient-percent),#666 100%);width:4px}.ytp-vertical-slider .ytp-input-slider::-moz-range-track{background:linear-gradient(to top,#fff 0,#fff var(--yt-slider-shape-gradient-percent),rgba(255,255,255,.2) var(--yt-slider-shape-gradient-percent),rgba(255,255,255,.2) 100%);width:4px;height:100%}.ytp-vertical-slider .ytp-input-slider::-webkit-slider-thumb{margin-right:-6px}.ytp-vertical-slider .ytp-input-slider::-moz-range-thumb{margin-right:0}}@-webkit-keyframes ytp-jump-spin{0%{-webkit-transform:rotate(0deg);transform:rotate(0deg)}to{-webkit-transform:rotate(1turn);transform:rotate(1turn)}}@keyframes ytp-jump-spin{0%{-webkit-transform:rotate(0deg);transform:rotate(0deg)}to{-webkit-transform:rotate(1turn);transform:rotate(1turn)}}@-webkit-keyframes ytp-jump-spin-backwards{0%{-webkit-transform:rotate(1turn);transform:rotate(1turn)}to{-webkit-transform:rotate(0deg);transform:rotate(0deg)}}@keyframes ytp-jump-spin-backwards{0%{-webkit-transform:rotate(1turn);transform:rotate(1turn)}to{-webkit-transform:rotate(0deg);transform:rotate(0deg)}}.ytp-dni .ytp-cued-thumbnail-overlay:hover .ytp-dni-large-play-button-bg{-webkit-transition:fill .1s cubic-bezier(0,0,.2,1),fill-opacity .1s cubic-bezier(0,0,.2,1);transition:fill .1s cubic-bezier(0,0,.2,1),fill-opacity .1s cubic-bezier(0,0,.2,1);fill-opacity:1}.ytp-cued-thumbnail-overlay:hover .ytp-large-play-button-bg,.ytp-muted-autoplay-endscreen-overlay:hover .ytp-large-play-button-bg{-webkit-transition:fill .1s cubic-bezier(0,0,.2,1),fill-opacity .1s cubic-bezier(0,0,.2,1);transition:fill .1s cubic-bezier(0,0,.2,1),fill-opacity .1s cubic-bezier(0,0,.2,1);fill:var(--yt-sys-color-baseline--static-brand-red,#f03);fill-opacity:1}.ytp-color-party .ytp-cued-thumbnail-overlay:hover .ytp-large-play-button-bg,.ytp-color-party .ytp-muted-autoplay-endscreen-overlay:hover .ytp-large-play-button-bg{-webkit-animation:ytp-party-fill .15s linear infinite;animation:ytp-party-fill .15s linear infinite}.house-brand .ytp-cued-thumbnail-overlay:hover .ytp-large-play-button-bg,.house-brand .ytp-muted-autoplay-endscreen-overlay:hover .ytp-large-play-button-bg{fill:#000}.ytp-miniplayer-scrim:focus-within{opacity:1}.ytp-miniplayer-scrim:hover{opacity:1}.ytp-player-minimized .ytp-progress-bar-container:hover .ytp-exp-chapter-hover-container:hover,.ytp-player-minimized .ytp-progress-bar-container:hover .ytp-exp-chapter-hover-effect{bottom:1px;-webkit-transform:scaleY(1.4);-ms-transform:scaleY(1.4);transform:scaleY(1.4);-webkit-transition:-webkit-transform .1s cubic-bezier(0,0,.2,1);transition:transform .1s cubic-bezier(0,0,.2,1),-webkit-transform .1s cubic-bezier(0,0,.2,1)}.ytp-player-minimized:not(.ad-showing) .ytp-progress-bar-container:hover{bottom:0}.ytp-reminder-menu-item:not([aria-disabled=true]):hover{background-color:rgba(255,255,255,.1)}.ytp-offline-slate-premiere-trailer .ytp-offline-slate-open-button:before{background:rgba(0,0,0,.8);content:&quot;&quot;;display:block;height:28px;left:15px;position:absolute;top:10px;width:20px}.ytp-overflow-panel-close:hover path{fill:#fff}.ytp-menuitem:not([aria-disabled=true]):hover{background-color:rgba(255,255,255,.1)}.ytp-probably-keyboard-focus .ytp-menuitem:focus .ytp-menuitem-icon{-webkit-box-shadow:inset 2px 2px 0 rgba(27,127,204,.8),inset 0-2px 0 rgba(27,127,204,.8);box-shadow:inset 2px 2px 0 rgba(27,127,204,.8),inset 0-2px 0 rgba(27,127,204,.8)}.ytp-probably-keyboard-focus .ytp-menuitem:focus .ytp-menuitem-label{-webkit-box-shadow:inset 0 2px 0 rgba(27,127,204,.8),inset 0-2px 0 rgba(27,127,204,.8);box-shadow:inset 0 2px 0 rgba(27,127,204,.8),inset 0-2px 0 rgba(27,127,204,.8)}.ytp-probably-keyboard-focus .ytp-menuitem:focus .ytp-menuitem-label:first-child{-webkit-box-shadow:inset 2px 2px 0 rgba(27,127,204,.8),inset 0-2px 0 rgba(27,127,204,.8);box-shadow:inset 2px 2px 0 rgba(27,127,204,.8),inset 0-2px 0 rgba(27,127,204,.8)}.ytp-probably-keyboard-focus .ytp-menuitem:focus .ytp-menuitem-content{-webkit-box-shadow:inset -2px -2px 0 rgba(27,127,204,.8),inset 0 2px 0 rgba(27,127,204,.8);box-shadow:inset -2px -2px 0 rgba(27,127,204,.8),inset 0 2px 0 rgba(27,127,204,.8)}.ytp-probably-keyboard-focus .ytp-menuitem[role=menuitemradio]:focus .ytp-menuitem-label{-webkit-box-shadow:inset 2px 2px 0 rgba(27,127,204,.8),inset -2px -2px 0 rgba(27,127,204,.8);box-shadow:inset 2px 2px 0 rgba(27,127,204,.8),inset -2px -2px 0 rgba(27,127,204,.8)}.ytp-menuitem-inline-survey-response:hover{background-color:rgba(255,255,255,.1)}.ytp-panel-title:hover{cursor:pointer}.ytp-playlist-menu-close:hover path{fill:#fff}.ytp-progress-bar-container:hover:not([aria-disabled=true]) .ytp-scrubber-container .ytp-scrubber-button.ytp-scrubber-button-hover{-webkit-transform:scale(1.54);-ms-transform:scale(1.54);transform:scale(1.54)}.ytp-probably-keyboard-focus .ytp-progress-bar:focus{-webkit-box-shadow:0 0 0 2px rgba(27,127,204,.8);box-shadow:0 0 0 2px rgba(27,127,204,.8)}.ytp-progress-bar-container:hover:not([aria-disabled=true]) .ytp-scrubber-button{-webkit-transform:none;-ms-transform:none;transform:none;-webkit-transition:-webkit-transform .1s cubic-bezier(0,0,.2,1);transition:transform .1s cubic-bezier(0,0,.2,1),-webkit-transform .1s cubic-bezier(0,0,.2,1)}.ytp-delhi-modern .ytp-progress-bar-container:hover:not([aria-disabled=true]) .ytp-scrubber-button{-webkit-transform:scale(1.67);-ms-transform:scale(1.67);transform:scale(1.67);-webkit-transition:-webkit-transform .2s cubic-bezier(.05,0,0,1);transition:transform .2s cubic-bezier(.05,0,0,1),-webkit-transform .2s cubic-bezier(.05,0,0,1)}.ytp-progress-bar-container:hover:not([aria-disabled=true]) .ytp-decorated-scrubber-container .ytp-scrubber-button,.ytp-progress-bar-container:hover:not([aria-disabled=true]) .ytp-decorated-scrubber-container .ytp-scrubber-button.ytp-scrubber-button-hover{-webkit-transform:scale(1.1666666667);-ms-transform:scale(1.1666666667);transform:scale(1.1666666667);-webkit-transition:-webkit-transform .1s cubic-bezier(0,0,.2,1);transition:transform .1s cubic-bezier(0,0,.2,1),-webkit-transform .1s cubic-bezier(0,0,.2,1)}.ytp-delhi-modern .ytp-progress-bar-container:hover:not([aria-disabled=true]) .ytp-decorated-scrubber-container .ytp-scrubber-button,.ytp-delhi-modern .ytp-progress-bar-container:hover:not([aria-disabled=true]) .ytp-decorated-scrubber-container .ytp-scrubber-button.ytp-scrubber-button-hover{-webkit-transform:scale(1.1666666667);-ms-transform:scale(1.1666666667);transform:scale(1.1666666667);-webkit-transition:-webkit-transform .2s cubic-bezier(.05,0,0,1);transition:transform .2s cubic-bezier(.05,0,0,1),-webkit-transform .2s cubic-bezier(.05,0,0,1)}.ytp-scrubber-pull-indicator:after,.ytp-scrubber-pull-indicator:before{display:block;position:absolute;content:&quot;&quot;;top:0;left:0;opacity:0;width:6.5px;height:6.5px;border-style:solid;border-width:2px 0 0 2px;border-color:#eaeaea}.ytp-big-mode .ytp-scrubber-pull-indicator:after,.ytp-big-mode .ytp-scrubber-pull-indicator:before{width:10px;height:10px}.ytp-scrubber-pull-indicator:after{-webkit-transition:all .1s;transition:all .1s}.ytp-scrubber-pull-indicator:before{-webkit-transition:all .2s;transition:all .2s}.ytp-progress-bar-container:hover:not([aria-disabled=true]) .ytp-progress-list{-webkit-transform:none;-ms-transform:none;transform:none;-webkit-transition:-webkit-transform .1s cubic-bezier(0,0,.2,1);transition:transform .1s cubic-bezier(0,0,.2,1),-webkit-transform .1s cubic-bezier(0,0,.2,1)}.ytp-delhi-modern .ytp-progress-bar-container:hover:not([aria-disabled=true]) .ytp-progress-list{-webkit-transform:none;-ms-transform:none;transform:none;-webkit-transition:-webkit-transform .2s cubic-bezier(.05,0,0,1);transition:transform .2s cubic-bezier(.05,0,0,1),-webkit-transform .2s cubic-bezier(.05,0,0,1)}.ytp-exp-chapter-hover-container:hover,.ytp-progress-bar-container:hover:not([aria-disabled=true]) .ytp-exp-chapter-hover-effect,.ytp-progress-bar-container:hover:not([aria-disabled=true]).ytp-timed-markers-enabled .ytp-progress-list{-webkit-transform:scaleY(1.8);-ms-transform:scaleY(1.8);transform:scaleY(1.8);-webkit-transition:-webkit-transform .1s cubic-bezier(0,0,.2,1);transition:transform .1s cubic-bezier(0,0,.2,1),-webkit-transform .1s cubic-bezier(0,0,.2,1)}.ytp-delhi-modern .ytp-exp-chapter-hover-container:hover,.ytp-delhi-modern .ytp-progress-bar-container:hover:not([aria-disabled=true]) .ytp-exp-chapter-hover-effect,.ytp-delhi-modern .ytp-progress-bar-container:hover:not([aria-disabled=true]).ytp-timed-markers-enabled .ytp-progress-list{-webkit-transition:-webkit-transform unset;transition:transform unset,-webkit-transform unset}.ytp-bound-time-left:after,.ytp-bound-time-right:after{position:absolute;content:&quot;&quot;;bottom:-5px;width:0;height:0;border-style:solid}.ytp-bound-time-left:after{left:0;border-width:5px 5px 0 0;border-color:rgba(28,28,28,.9) transparent transparent transparent}.ytp-bound-time-right:after{right:0;border-width:0 5px 5px 0;border-color:transparent rgba(28,28,28,.9) transparent transparent}.ytp-progress-bar-container:hover .ytp-timed-markers-container{-webkit-transform:scaleY(1.8);-ms-transform:scaleY(1.8);transform:scaleY(1.8);-webkit-transition:-webkit-transform .1s cubic-bezier(0,0,.2,1);transition:transform .1s cubic-bezier(0,0,.2,1),-webkit-transform .1s cubic-bezier(0,0,.2,1);top:-2px}.ytp-delhi-modern .ytp-progress-bar-container:hover .ytp-timed-markers-container{-webkit-transform:scaleY(1.667);-ms-transform:scaleY(1.667);transform:scaleY(1.667);-webkit-transition:-webkit-transform .2s cubic-bezier(.05,0,0,1);transition:transform .2s cubic-bezier(.05,0,0,1),-webkit-transform .2s cubic-bezier(.05,0,0,1)}.ytp-progress-bar-container:hover .ytp-timed-marker.ytp-timed-marker-hover{width:9px;height:3px;bottom:1px}.ytp-delhi-modern .ytp-progress-bar-container:hover .ytp-timed-marker.ytp-timed-marker-hover{width:12px;height:4.8px;bottom:.6px}.ytp-progress-bar-container:hover .ytp-timed-marker{width:5px;height:1.67px;bottom:1.5px}.ytp-delhi-modern .ytp-progress-bar-container:hover .ytp-timed-marker{width:6px;height:2.4px;bottom:2px}.ytp-progress-bar-container:hover:not([aria-disabled=true]) .ytp-clip-end,.ytp-progress-bar-container:hover:not([aria-disabled=true]) .ytp-clip-start{-webkit-transform:none;-ms-transform:none;transform:none;-webkit-transition:-webkit-transform .1s cubic-bezier(0,0,.2,1);transition:transform .1s cubic-bezier(0,0,.2,1),-webkit-transform .1s cubic-bezier(0,0,.2,1)}.ytp-chapter-hover-container:hover:not([aria-disabled=true]) .ytp-progress-bar-padding{height:22px;bottom:-6px}.ytp-big-mode:not(.ytp-touch-mode) .ytp-chapter-hover-container:hover:not([aria-disabled=true]) .ytp-progress-bar-padding{height:33px;bottom:-9px}.ytp-delhi-modern.ytp-retro-player:not(.ad-showing):not(.ad-interrupting) .ytp-chrome-bottom .ytp-volume-area:hover:after{background:transparent}.ytp-delhi-modern.ytp-retro-player:not(.ad-showing):not(.ad-interrupting) .ytp-chrome-bottom .ytp-play-button:hover:after{background:transparent}.ytp-delhi-modern.ytp-retro-player:not(.ad-showing):not(.ad-interrupting) .ytp-chrome-bottom .ytp-time-wrapper:not(.ytp-miniplayer-ui *):hover:after{background:transparent}.ytp-delhi-modern.ytp-retro-player:not(.ad-showing):not(.ad-interrupting) .ytp-chrome-bottom .ytp-chapter-title.ytp-button:hover:after{background:transparent}.ytp-delhi-modern.ytp-retro-player:not(.ad-showing):not(.ad-interrupting) .ytp-chrome-bottom .ytp-chrome-controls .ytp-right-controls .ytp-button:hover:before{background:transparent}.ytp-delhi-modern.ytp-retro-player:not(.ad-showing):not(.ad-interrupting) .ytp-chrome-bottom .ytp-next-button:not(.ytp-miniplayer-button-container>*):hover:after,.ytp-delhi-modern.ytp-retro-player:not(.ad-showing):not(.ad-interrupting) .ytp-chrome-bottom .ytp-prev-button:not(.ytp-miniplayer-button-container>*):hover:after{background:transparent}.ytp-retro-player:not(.ad-showing):not(.ad-interrupting) .ytp-chrome-bottom .ytp-button:active:focus{background:-webkit-gradient(linear,left top,left bottom,from(rgb(19,19,19)),to(rgb(52,52,52)));background:-webkit-linear-gradient(top,rgb(19,19,19),rgb(52,52,52));background:linear-gradient(180deg,rgb(19,19,19),rgb(52,52,52))}.ytp-retro-player:not(.ad-showing):not(.ad-interrupting) .ytp-volume-slider-handle:before{background:-webkit-gradient(linear,left bottom,left top,from(rgb(194,0,0)),to(rgb(230,6,6)));background:-webkit-linear-gradient(bottom,rgb(194,0,0),rgb(230,6,6));background:linear-gradient(0deg,rgb(194,0,0),rgb(230,6,6));margin-left:-6px}@-webkit-keyframes bezel-fade-in{0%{opacity:0}25%,75%{opacity:1;-webkit-transform:scale(2);transform:scale(2)}to{opacity:0;-webkit-transform:scale(1);transform:scale(1)}}@keyframes bezel-fade-in{0%{opacity:0}25%,75%{opacity:1;-webkit-transform:scale(2);transform:scale(2)}to{opacity:0;-webkit-transform:scale(1);transform:scale(1)}}.ytp-settings-button.ytp-3d-badge-grey:after,.ytp-settings-button.ytp-3d-badge:after,.ytp-settings-button.ytp-4k-quality-badge:after,.ytp-settings-button.ytp-5k-quality-badge:after,.ytp-settings-button.ytp-8k-quality-badge:after,.ytp-settings-button.ytp-hd-quality-badge:after,.ytp-settings-button.ytp-hdr-quality-badge:after{content:&quot;&quot;;position:absolute;top:10px;right:5px;height:9px;width:13px;background-color:var(--yt-sys-color-baseline--red-indicator,#e1002d);border-radius:1px;line-height:normal}.ytp-settings-button.ytp-3d-badge-grey:after{background-color:#666}.ytp-color-white .ytp-settings-button.ytp-3d-badge-grey:after,.ytp-color-white .ytp-settings-button.ytp-3d-badge:after,.ytp-color-white .ytp-settings-button.ytp-4k-quality-badge:after,.ytp-color-white .ytp-settings-button.ytp-5k-quality-badge:after,.ytp-color-white .ytp-settings-button.ytp-8k-quality-badge:after,.ytp-color-white .ytp-settings-button.ytp-hd-quality-badge:after,.ytp-color-white .ytp-settings-button.ytp-hdr-quality-badge:after{background-color:#ddd}.ytp-color-party .ytp-settings-button.ytp-3d-badge-grey:after,.ytp-color-party .ytp-settings-button.ytp-3d-badge:after,.ytp-color-party .ytp-settings-button.ytp-4k-quality-badge:after,.ytp-color-party .ytp-settings-button.ytp-5k-quality-badge:after,.ytp-color-party .ytp-settings-button.ytp-8k-quality-badge:after,.ytp-color-party .ytp-settings-button.ytp-hd-quality-badge:after,.ytp-color-party .ytp-settings-button.ytp-hdr-quality-badge:after{-webkit-animation:ytp-party-background-color .1s linear infinite;animation:ytp-party-background-color .1s linear infinite}.ytp-settings-button.ytp-hd-quality-badge:after{background-image:url(/images/templates/eventmanagementx/eve-026.svg)}.ytp-settings-button.ytp-hdr-quality-badge:after{background-image:url(/images/templates/eventmanagementx/eve-027.svg);height:6px;width:14px;right:3px;border-style:solid;border-color:#e1002d;border-width:1px}.ytp-settings-button.ytp-4k-quality-badge:after{background-image:url(/images/templates/eventmanagementx/eve-028.svg)}.ytp-settings-button.ytp-5k-quality-badge:after{background-image:url(/images/templates/eventmanagementx/eve-029.svg)}.ytp-settings-button.ytp-8k-quality-badge:after{background-image:url(/images/templates/eventmanagementx/eve-030.svg)}.ytp-settings-button.ytp-3d-badge-grey:after,.ytp-settings-button.ytp-3d-badge:after{background-image:url(/images/templates/eventmanagementx/eve-031.svg)}.ytp-color-white .ytp-settings-button.ytp-hd-quality-badge:after{background-image:url(/images/templates/eventmanagementx/eve-032.svg)}.ytp-color-white .ytp-settings-button.ytp-hdr-quality-badge:after{background-image:url(/images/templates/eventmanagementx/eve-033.svg)}.ytp-color-white .ytp-settings-button.ytp-4k-quality-badge:after{background-image:url(/images/templates/eventmanagementx/eve-034.svg)}.ytp-color-white .ytp-settings-button.ytp-5k-quality-badge:after{background-image:url(/images/templates/eventmanagementx/eve-035.svg)}.ytp-color-white .ytp-settings-button.ytp-8k-quality-badge:after{background-image:url(/images/templates/eventmanagementx/eve-036.svg)}.ytp-color-white .ytp-settings-button.ytp-3d-badge-grey:after,.ytp-color-white .ytp-settings-button.ytp-3d-badge:after{background-image:url(/images/templates/eventmanagementx/eve-037.svg)}.ytp-big-mode .ytp-settings-button.ytp-3d-badge-grey:after,.ytp-big-mode .ytp-settings-button.ytp-3d-badge:after,.ytp-big-mode .ytp-settings-button.ytp-4k-quality-badge:after,.ytp-big-mode .ytp-settings-button.ytp-5k-quality-badge:after,.ytp-big-mode .ytp-settings-button.ytp-8k-quality-badge:after,.ytp-big-mode .ytp-settings-button.ytp-hd-quality-badge:after,.ytp-big-mode .ytp-settings-button.ytp-hdr-quality-badge:after{top:15px;right:6px;padding:2px;font-family:Verdana,sans-serif;font-size:10px;font-weight:700;color:#fff;text-shadow:0 2px 0 rgba(0,0,0,.6);background-image:none;border-radius:1.5px;height:auto;width:auto}.ytp-delhi-modern.ytp-big-mode .ytp-settings-button.ytp-3d-badge-grey:after,.ytp-delhi-modern.ytp-big-mode .ytp-settings-button.ytp-3d-badge:after,.ytp-delhi-modern.ytp-big-mode .ytp-settings-button.ytp-4k-quality-badge:after,.ytp-delhi-modern.ytp-big-mode .ytp-settings-button.ytp-5k-quality-badge:after,.ytp-delhi-modern.ytp-big-mode .ytp-settings-button.ytp-8k-quality-badge:after,.ytp-delhi-modern.ytp-big-mode .ytp-settings-button.ytp-hd-quality-badge:after,.ytp-delhi-modern.ytp-big-mode .ytp-settings-button.ytp-hdr-quality-badge:after{top:4px;right:4px}.ytp-color-white.ytp-big-mode .ytp-settings-button.ytp-3d-badge-grey:after,.ytp-color-white.ytp-big-mode .ytp-settings-button.ytp-3d-badge:after,.ytp-color-white.ytp-big-mode .ytp-settings-button.ytp-4k-quality-badge:after,.ytp-color-white.ytp-big-mode .ytp-settings-button.ytp-5k-quality-badge:after,.ytp-color-white.ytp-big-mode .ytp-settings-button.ytp-8k-quality-badge:after,.ytp-color-white.ytp-big-mode .ytp-settings-button.ytp-hd-quality-badge:after,.ytp-color-white.ytp-big-mode .ytp-settings-button.ytp-hdr-quality-badge:after{color:#000;text-shadow:none}.ytp-big-mode .ytp-settings-button.ytp-hd-quality-badge:after{content:&quot;HD&quot;}.ytp-big-mode .ytp-settings-button.ytp-hdr-quality-badge:after{content:&quot;HDR&quot;;font-size:8px}.ytp-big-mode .ytp-settings-button.ytp-4k-quality-badge:after{content:&quot;4K&quot;}.ytp-big-mode .ytp-settings-button.ytp-5k-quality-badge:after{content:&quot;5K&quot;}.ytp-big-mode .ytp-settings-button.ytp-8k-quality-badge:after{content:&quot;8K&quot;}.ytp-big-mode .ytp-settings-button.ytp-3d-badge-grey:after,.ytp-big-mode .ytp-settings-button.ytp-3d-badge:after{content:&quot;3D&quot;}.ytp-popup{overflow:hidden;border-radius:2px;text-shadow:0 0 2px rgba(0,0,0,.5);-webkit-transition:opacity .1s cubic-bezier(0,0,.2,1);transition:opacity .1s cubic-bezier(0,0,.2,1);-moz-user-select:none;-ms-user-select:none;-webkit-user-select:none}.ytp-speedslider-component .ytp-slider-handle:after,.ytp-speedslider-component .ytp-slider-handle:before{height:5px}.ytp-share-panel-close:hover path{fill:#fff}.ytp-skip-intro-button:hover{background:#000}.ytp-slider-handle:after,.ytp-slider-handle:before{content:&quot;&quot;;position:absolute;display:block;top:50%;left:0;height:3px;margin-top:-2px;width:170px;outline:0}.ytp-slider-handle:before{left:-160px;background:#fff}.ytp-slider-handle:after{left:10px;background:rgba(255,255,255,.2)}.ytp-probably-keyboard-focus .ytp-slider-section:focus{-webkit-box-shadow:inset 0 0 0 2px rgba(27,127,204,.8);box-shadow:inset 0 0 0 2px rgba(27,127,204,.8)}.ytp-webgl-spherical-control:hover{opacity:1}.ytp-probably-keyboard-focus .ytp-webgl-spherical-control:focus{-webkit-box-shadow:inset 0 0 0 2px rgba(27,127,204,.8);box-shadow:inset 0 0 0 2px rgba(27,127,204,.8)}@-webkit-keyframes ytp-spinner-linspin{to{-webkit-transform:rotate(1turn)}}@keyframes ytp-spinner-linspin{to{-webkit-transform:rotate(1turn);transform:rotate(1turn)}}@-webkit-keyframes ytp-spinner-easespin{12.5%{-webkit-transform:rotate(135deg)}25%{-webkit-transform:rotate(270deg)}37.5%{-webkit-transform:rotate(405deg)}50%{-webkit-transform:rotate(540deg)}62.5%{-webkit-transform:rotate(675deg)}75%{-webkit-transform:rotate(810deg)}87.5%{-webkit-transform:rotate(945deg)}to{-webkit-transform:rotate(3turn)}}@keyframes ytp-spinner-easespin{12.5%{-webkit-transform:rotate(135deg);transform:rotate(135deg)}25%{-webkit-transform:rotate(270deg);transform:rotate(270deg)}37.5%{-webkit-transform:rotate(405deg);transform:rotate(405deg)}50%{-webkit-transform:rotate(540deg);transform:rotate(540deg)}62.5%{-webkit-transform:rotate(675deg);transform:rotate(675deg)}75%{-webkit-transform:rotate(810deg);transform:rotate(810deg)}87.5%{-webkit-transform:rotate(945deg);transform:rotate(945deg)}to{-webkit-transform:rotate(3turn);transform:rotate(3turn)}}@-webkit-keyframes ytp-spinner-left-spin{0%{-webkit-transform:rotate(130deg)}50%{-webkit-transform:rotate(-5deg)}to{-webkit-transform:rotate(130deg)}}@keyframes ytp-spinner-left-spin{0%{-webkit-transform:rotate(130deg);transform:rotate(130deg)}50%{-webkit-transform:rotate(-5deg);transform:rotate(-5deg)}to{-webkit-transform:rotate(130deg);transform:rotate(130deg)}}@-webkit-keyframes ytp-right-spin{0%{-webkit-transform:rotate(-130deg)}50%{-webkit-transform:rotate(5deg)}to{-webkit-transform:rotate(-130deg)}}@keyframes ytp-right-spin{0%{-webkit-transform:rotate(-130deg);transform:rotate(-130deg)}50%{-webkit-transform:rotate(5deg);transform:rotate(5deg)}to{-webkit-transform:rotate(-130deg);transform:rotate(-130deg)}}.ytp-sb-subscribe:focus,.ytp-sb-unsubscribe:focus{outline:none}.caption-edit:focus,.caption-window:focus .caption-edit,.ytp-caption-window-rollup .caption-edit:hover,.ytp-caption-window-rollup:hover .caption-edit{opacity:1}.ytp-suggested-action-badge:hover{-webkit-transition:opacity .25s cubic-bezier(0,0,.2,1),background-color .1s cubic-bezier(0,0,.2,1),transform .2s cubic-bezier(.4,0,.2,1),-webkit-transform .2s cubic-bezier(.4,0,.2,1),width .2s cubic-bezier(.4,0,.2,1);transition:opacity .25s cubic-bezier(0,0,.2,1),background-color .1s cubic-bezier(0,0,.2,1),transform .2s cubic-bezier(.4,0,.2,1),-webkit-transform .2s cubic-bezier(.4,0,.2,1),width .2s cubic-bezier(.4,0,.2,1);background-color:rgba(33,33,33,.95)}.ytp-suggested-action-badge-dismiss-button-icon:hover{opacity:1;-webkit-transition:opacity .1s cubic-bezier(0,0,.2,1);transition:opacity .1s cubic-bezier(0,0,.2,1)}.ytp-delhi-modern .ytp-time-wrapper:not(.ytp-miniplayer-ui *):after{content:&quot;&quot;;position:absolute;inset:4px;background:transparent;pointer-events:none;border-radius:28px}.ytp-delhi-modern .ytp-time-wrapper:not(.ytp-miniplayer-ui *):hover:after{background-color:var(--yt-sys-color-baseline--overlay-button-secondary,rgba(255,255,255,.1));-webkit-transition:background-color .2s cubic-bezier(.05,0,0,1);transition:background-color .2s cubic-bezier(.05,0,0,1)}.ytp-delhi-modern .ytp-time-wrapper:not(.ytp-miniplayer-ui *):active:after{background-color:var(--yt-sys-color-baseline--overlay-tonal-hover,rgba(255,255,255,.2));-webkit-transition:background-color .2s cubic-bezier(.05,0,0,1);transition:background-color .2s cubic-bezier(.05,0,0,1)}.ytp-live-badge:before{display:inline-block;width:6px;height:6px;vertical-align:4%;margin-right:5px;background:#757575;content:&quot;&quot;;border-radius:6px}.ytp-big-mode .ytp-live-badge:before{width:9px;height:9px;border-radius:9px}.ytp-livebadge-color .ytp-live-badge.ytp-live-badge-is-livehead:before{background:var(--yt-sys-color-baseline--overlay-background-brand,rgba(225,0,45,.9))}.ytp-contextmenu .ytp-menuitem-toggle-checkbox:after{content:none}.ytp-contextmenu .ytp-menuitem[aria-checked=true] .ytp-menuitem-toggle-checkbox:before{-webkit-transform:none;-ms-transform:none;transform:none}.ytp-big-mode .ytp-contextmenu .ytp-menuitem[aria-checked=true] .ytp-menuitem-toggle-checkbox:before{-webkit-transform:none;-ms-transform:none;transform:none}.ytp-menuitem-toggle-checkbox:after{content:&quot;&quot;;position:absolute;left:0;top:0;height:20px;width:20px;border-radius:20px;margin-top:-3px;background-color:#bdbdbd;-webkit-box-shadow:0 1px 5px 0 rgba(0,0,0,.6);box-shadow:0 1px 5px 0 rgba(0,0,0,.6);-webkit-transition:all .08s cubic-bezier(.4,0,1,1);transition:all .08s cubic-bezier(.4,0,1,1)}.ytp-big-mode .ytp-menuitem-toggle-checkbox:after{left:0;height:30px;width:30px;border-radius:30px;margin-top:divide(sub(21px,30px),2)}.ytp-menuitem[aria-checked=true] .ytp-menuitem-toggle-checkbox:after{background-color:#fff;-webkit-transform:translateX(16px);-ms-transform:translateX(16px);transform:translateX(16px)}.ytp-big-mode .ytp-menuitem[aria-checked=true] .ytp-menuitem-toggle-checkbox:after{-webkit-transform:translateX(24px);-ms-transform:translateX(24px);transform:translateX(24px)}.ytp-delhi-modern .ytp-menuitem-toggle-checkbox:after,.ytp-delhi-modern.big-mode .ytp-menuitem-toggle-checkbox:after{width:20px;height:20px;margin-left:2px;margin-top:2px;-webkit-box-shadow:none;box-shadow:none;background-color:var(--yt-sys-color-baseline--overlay-text-secondary,rgba(255,255,255,.7))}.ytp-delhi-modern .ytp-menuitem[aria-checked=true] .ytp-menuitem-toggle-checkbox:after,.ytp-delhi-modern.big-mode .ytp-menuitem[aria-checked=true] .ytp-menuitem-toggle-checkbox:after{-webkit-transform:translateX(16px);-ms-transform:translateX(16px);transform:translateX(16px)}.ytp-unmute.ytp-popup{position:absolute;left:0;z-index:1001;text-transform:uppercase;color:#000;font-size:127%;font-weight:500;background:none;padding:12px}.ytp-autohide .ytp-unmute{top:0}@-webkit-keyframes ytp-unmute-width-anim{0%{width:0}to{width:100%}}@keyframes ytp-unmute-width-anim{0%{width:0}to{width:100%}}@-webkit-keyframes ytp-unmute-alpha-anim{0%{opacity:0}to{opacity:1}}@keyframes ytp-unmute-alpha-anim{0%{opacity:0}to{opacity:1}}.ytp-upnext-cancel-button:hover{background-color:rgba(255,255,255,.15);border-radius:2px}.ytp-user-info-panel .ytp-collapse:hover{opacity:1}.ytp-video-menu-item:hover,.ytp-video-menu-item[aria-checked=true]:hover{background-color:rgba(255,255,255,.15)}.ytp-videowall-still:focus .ytp-videowall-still-listlabel-mix,.ytp-videowall-still:focus .ytp-videowall-still-listlabel-regular,.ytp-videowall-still:hover .ytp-videowall-still-listlabel-mix,.ytp-videowall-still:hover .ytp-videowall-still-listlabel-regular{background-color:rgba(0,0,0,0)}.ytp-videowall-still:focus .ytp-videowall-still-info-content,.ytp-videowall-still:hover .ytp-videowall-still-info-content{opacity:1}.ytp-videowall-still:focus .ytp-videowall-still-listlabel,.ytp-videowall-still:hover .ytp-videowall-still-listlabel{background:rgba(0,0,0,.86)}.ytp-modern-videowall-still:hover{background-color:var(--yt-sys-color-baseline--overlay-button-secondary);-webkit-box-shadow:0 0 0 8px var(--yt-sys-color-baseline--overlay-button-secondary);box-shadow:0 0 0 8px var(--yt-sys-color-baseline--overlay-button-secondary);border-radius:8px}.ytp-modern-videowall-still:active{background-color:var(--yt-sys-color-baseline--overlay-tonal-hover);-webkit-box-shadow:0 0 0 8px var(--yt-sys-color-baseline--overlay-tonal-hover);box-shadow:0 0 0 8px var(--yt-sys-color-baseline--overlay-tonal-hover);border-radius:8px}.ytp-variable-speed-panel-button:hover{background-color:rgba(255,255,255,.2)}.ytp-variable-speed-panel-button:active{background-color:rgba(255,255,255,.3)}.ytp-delhi-modern .ytp-chrome-controls .ytp-volume-popover .ytp-input-slider::-webkit-slider-thumb{width:12px;height:12px;-webkit-box-shadow:0 1px 1px rgba(0,0,0,.8);box-shadow:0 1px 1px rgba(0,0,0,.8);margin-right:-5px}.ytp-delhi-modern .ytp-chrome-controls .ytp-volume-popover .ytp-input-slider::-moz-range-thumb{width:12px;height:12px;box-shadow:0 1px 1px rgba(0,0,0,.8)}.ytp-delhi-modern .ytp-chrome-controls .ytp-volume-popover .ytp-input-slider::-ms-thumb{width:12px;height:12px;box-shadow:0 1px 1px rgba(0,0,0,.8)}.ytp-delhi-modern .ytp-chrome-controls .ytp-volume-popover .ytp-input-slider::-webkit-slider-runnable-track{width:2px;-webkit-box-shadow:0 1px 1px rgba(0,0,0,.8);box-shadow:0 1px 1px rgba(0,0,0,.8)}.ytp-delhi-modern .ytp-chrome-controls .ytp-volume-popover .ytp-input-slider::-moz-range-track{width:2px;box-shadow:0 1px 1px rgba(0,0,0,.8)}.ytp-delhi-modern .ytp-chrome-controls .ytp-volume-popover .ytp-input-slider:hover{cursor:pointer}.ytp-probably-keyboard-focus .ytp-volume-panel:focus{-webkit-box-shadow:inset 0 0 0 2px rgba(27,127,204,.8);box-shadow:inset 0 0 0 2px rgba(27,127,204,.8)}.ytp-volume-slider-handle:after,.ytp-volume-slider-handle:before{content:&quot;&quot;;position:absolute;display:block;top:50%;left:0;height:3px;margin-top:-2px;width:64px}.ytp-big-mode.ytp-delhi-horizontal-volume-controls .ytp-volume-slider-handle:after,.ytp-big-mode.ytp-delhi-horizontal-volume-controls .ytp-volume-slider-handle:before,.ytp-delhi-horizontal-volume-controls .ytp-volume-slider-handle:after,.ytp-delhi-horizontal-volume-controls .ytp-volume-slider-handle:before{height:2px;margin-top:-1px}.ytp-big-mode .ytp-volume-slider-handle:after,.ytp-big-mode .ytp-volume-slider-handle:before{height:4px;margin-top:-2px;width:96px}.ytp-volume-slider-handle:before{left:-58px;background:#fff}.ytp-big-mode .ytp-volume-slider-handle:before{left:-87px}.ytp-volume-slider-handle:after{left:6px;background:rgba(255,255,255,.2)}.ytp-big-mode .ytp-volume-slider-handle:after{left:9px;background:rgba(255,255,255,.2)}.ytp-watch-on-youtube-button:hover{background:rgba(255,255,255,.1)}.ytp-continue-watching-button.ytp-watch-on-youtube-button:hover{background-color:#e5e5e5;color:#0f0f0f}.ytp-watch-on-youtube-button:focus{border:2px solid white}.ytp-continue-watching-button.ytp-watch-on-youtube-button:focus{border:2px solid white;color:#fff;background-color:#0f0f0f}.ytp-watch-on-youtube-button:active{-webkit-transform:scale(.94);-ms-transform:scale(.94);transform:scale(.94);-webkit-transition:-webkit-transform .1s cubic-bezier(.05,0,0,1);transition:transform .1s cubic-bezier(.05,0,0,1),-webkit-transform .1s cubic-bezier(.05,0,0,1)}.ytp-drawer-open-button:after{display:inline-block;border:5px solid transparent;border-right-color:#aaa;border-left:none;content:&quot;&quot;}.ytp-big-mode .ytp-drawer-open-button:after{border-bottom-width:8px;border-right-width:8px;border-top-width:8px}.html5-ypc-purchase:hover{background:#126db3}.ytp-ypc-clickwrap-confirm:hover{background-color:#26c}
:root{--t7f4f2c6d54836ce0:initial;--t7d8b8e5ee291aec0:initial;--t4726c68ef6d7b08f:initial;--tf0a0d1154827847f:initial;--t3e41d7b17b187f69:initial;--tff65993384bb4560:initial;--te2867b13d771d055:initial;--t933d7922f3158dc5:initial;--t264e2c9cb87891f9:initial;--t206f3fa2e01af22d:initial;--t34f46f0f2c13d328:initial;--t854c79338d8ca8a7:initial;--t20480717de80f555:initial;--t2d807bb79e75606d:initial;--t617db776af0de196:initial;--tb628117fc164ad87:initial;--td545df04e2c659d7:initial;--t53991d599b1488f5:initial;--ta889dfda9605a358:initial;--t5978da8d584b8fe9:initial;--tdbe50d27d51f5093:initial;--t99f774684cbb9703:initial;--tb8b31ac562fbe9b5:initial;--tef70ca245445b52b:initial;--t4f0922e5a3d0c20f:initial;--t7dfa6f84c9346edf:initial;--tecbdf924b63b27fc:initial;--t1405e70a39276293:initial;--tad8443faed66c111:initial;--te6d2b428de68554a:initial;--tc980c5478124c8c7:initial;--t965644ecdbe4b7b8:initial;--tb14a10c3b43a15a3:initial;--tb13ff8797829e79e:initial;--te5668e6111aba0ed:initial;--ta7d628facfe84d18:initial;--tbe42269534655e67:initial;--tab8059236063cadb:initial;--t735e435a8b7ad67c:initial;--t21af08fdbf6a1406:initial;--tc604113d09fee05d:initial;--t08a7c6c176cbc5c2:initial;--t8cdc9846a76caf1f:initial;--te1b64e6971040396:initial;--t416e5931fc464589:initial;--tb7d74bb3291c951d:initial;--tf3fc855af2285f5f:initial;--t0a1249f7c9e82b5e:initial;--t02c0a2c868c14ce8:initial;--td8562cdc203bc683:initial;--t53dda9125e8d1324:initial;--t544e31714f63d53c:initial;--t87eabf113a15ac96:initial;--t858b809f8044e962:initial;--t0f59291ce5504543:initial;--tfaae96692cc182e4:initial;--t518e925f61bdcb91:initial;--tb927f5c0149004c6:initial;--t832f22ce6618e99e:initial;--t45b2a38314924357:initial;--t6931aa1826b373b2:initial;--ted4236536899e4b3:initial;--tdc8af6750f0dad0d:initial;--t3410c91649e5eb8a:initial;--t4aeaa857de8a786b:initial;--t296c7f81dd09a9c3:initial;--tfa3475c508f5dfef:initial;--t9bc0b740242da017:initial;--t441a0e44e495381a:initial;--tde41338fc2bd4ba5:initial;--t4d7776c28db21122:initial;--t7e34d5baa4ea6277:initial;--tc4b26042d4cb141f:initial;--t5208fd177a788cfa:initial;--tffc2fd3a644f6275:initial;--t6216186c28b3834b:initial;--t4a6da19e16bf221a:initial;--td9c19f4f8cecd56e:initial;--t3f3bdb4140d3ead7:initial;--t904a88c623ca27ab:initial;--t2c3bbff6c15a3eb2:initial;--t0ccd1ace000d5e93:initial;--tc87f6a7f05e65fc4:initial;--tc04ba3e73561953c:initial;--t7c4965c11f8537c0:initial;--t432fbdbd7f2f3f71:initial;--taf1bdd961423c15f:initial;--t9d7bdcaefc975d44:initial;--t4fb0f67b251e1a42:initial;--ta08e036410c14538:initial;--t7f9b7e1603e20b94:initial}@-webkit-keyframes spinner{0%{-webkit-transform:rotate(0deg)}to{-webkit-transform:rotate(1turn)}}@keyframes spinner{0%{-webkit-transform:rotate(0deg);transform:rotate(0deg)}to{-webkit-transform:rotate(1turn);transform:rotate(1turn)}}html{font-size:10px;font-family:Roboto,Arial,sans-serif}body{font-size:1.2rem;overflow-x:hidden}[hidden][hidden]{visibility:hidden}*{-ms-flex:0 1 auto}button{padding:0;border:none;outline:none;font:inherit;text-transform:inherit;color:inherit;background:transparent}html{word-wrap:break-word;color:#030303;background-color:#fff;-webkit-text-size-adjust:100%}input:focus{margin-bottom:0;border-bottom-width:2px;border-bottom-color:#030303}input::-webkit-input-placeholder{color:#606060;opacity:1}input::placeholder{color:#606060;opacity:1}textarea::-webkit-input-placeholder{color:#606060;opacity:1}textarea::placeholder{color:#606060;opacity:1}a{color:currentColor;text-decoration:none}h2{-webkit-box-orient:vertical;max-height:2.5em;-webkit-line-clamp:2;overflow:hidden;line-height:1.25;text-overflow:ellipsis;font-weight:400;font-size:1.8rem;margin-bottom:0}img{-webkit-filter:none;filter:none}img:not([src]){visibility:hidden}:focus{outline:none}button{cursor:pointer}@-webkit-keyframes notification-bell-fade-in{0%{opacity:0;-webkit-transform:translateX(-100%);transform:translateX(-100%)}to{opacity:1;-webkit-transform:none;transform:none}}@keyframes notification-bell-fade-in{0%{opacity:0;-webkit-transform:translateX(-100%);transform:translateX(-100%)}to{opacity:1;-webkit-transform:none;transform:none}}.ytCoreImageHost{display:inline-block;min-height:1px;min-width:1px}.ytCoreImageLoaded{visibility:inherit}.ytCoreImageFillParentHeight{height:100%}.ytCoreImageFillParentWidth{width:100%}.ytCoreImageContentModeScaleToFill{object-fit:fill}.ytCoreImageContentModeScaleAspectFill{object-fit:cover}:root{--yt-attributed-string-link-hover-color:unset}.ytAttributedStringInlineBlockMod{display:inline-block}.ytAttributedStringImageAlignmentVerticalCenter{vertical-align:middle;position:relative;top:-.07em}.ytAttributedStringLinkInheritColor .ytAttributedStringLinkCallToActionColor:hover{color:var(--yt-attributed-string-link-hover-color)}.ytAttributedStringEllipsisTruncate{display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.ytAttributedStringWhiteSpaceNoWrap{white-space:nowrap}.ytAttributedStringWhiteSpacePreWrap{white-space:pre-wrap}input:checked+label>.ytRadioShapeRadio>:first-child{display:none}input:not(:checked)+label>.ytRadioShapeRadio>:last-child{display:none}input:not(:checked)+label>.ytRadioShapeCheckIcon>:first-child{visibility:hidden}.ytListItemViewModelHost:focus-visible{outline:none}.ytListItemViewModelKeyboardFocused:focus-within{background-color:rgba(0,0,0,0.05);outline:2px solid currentColor;outline-offset:-2px;border-radius:8px}.ytListItemViewModelButtonOrAnchor:focus-visible{outline:none}.ytListItemViewModelTappable:hover{background-color:rgba(0,0,0,0.05)}input:checked~.ytCheckboxShapeCheckbox>:first-child{display:none}input:not(:checked)~.ytCheckboxShapeCheckbox>:last-child{display:none}.YtmCompactMediaItemStats~.YtmCompactMediaItemStats:before{content:&quot;&nbsp;·&nbsp;&quot;}.ytSpecTouchFeedbackShapeHost{display:inline-block;border-radius:inherit;position:absolute;top:0;right:0;bottom:0;left:0}.ytSpecTouchFeedbackShapeFill,.ytSpecTouchFeedbackShapeStroke{will-change:opacity;opacity:0;border-radius:inherit;position:absolute;top:0;right:0;bottom:0;left:0}.ytSpecTouchFeedbackShapeTouchResponse .ytSpecTouchFeedbackShapeFill{background-color:#000}.ytSpecTouchFeedbackShapeTouchResponse .ytSpecTouchFeedbackShapeStroke{border:1px solid #000}.ytSpecTouchFeedbackShapeOverlayTouchResponse .ytSpecTouchFeedbackShapeFill{background-color:#fff}.ytSpecTouchFeedbackShapeOverlayTouchResponse .ytSpecTouchFeedbackShapeStroke{border:1px solid #fff}@-webkit-keyframes popover-fade-in{0%{opacity:0}}@keyframes popover-fade-in{0%{opacity:0}}@-webkit-keyframes popover-fade-out{to{opacity:0}}@keyframes popover-fade-out{to{opacity:0}}.ytPopoverComponentHost:popover-open{-webkit-animation:popover-fade-in .15s ease-in;animation:popover-fade-in .15s ease-in}.ytPopoverComponentHostClosing:popover-open{-webkit-animation:popover-fade-out 75ms ease-out forwards;animation:popover-fade-out 75ms ease-out forwards}.ytSpecButtonShapeNextHost{position:relative;margin:0;white-space:nowrap;min-width:0;text-transform:none;font-family:Roboto,Arial,sans-serif;font-weight:500;border:none;cursor:pointer;outline-width:0;box-sizing:border-box;background:none;text-decoration:none;-webkit-tap-highlight-color:transparent;-webkit-flex:1;-webkit-box-flex:1;flex:1;-webkit-flex-basis:0.000000001px;flex-basis:0.000000001px;display:flex;-webkit-flex-direction:row;-webkit-box-orient:horizontal;-webkit-box-direction:normal;flex-direction:row;-webkit-align-items:center;-webkit-box-align:center;align-items:center;-webkit-justify-content:center;-webkit-box-pack:center;justify-content:center}.ytSpecButtonShapeNextIcon{line-height:0;fill:currentColor}.ytSpecButtonShapeNextButtonTextContent{text-overflow:ellipsis;overflow:hidden}.ytSpecButtonShapeNextSizeXs.ytSpecButtonShapeNextSegmentedStart:after{content:&quot;&quot;;background:rgba(0,0,0,0.1);position:absolute;right:0;top:4px;height:16px;width:1px}.ytSpecButtonShapeNextSizeS.ytSpecButtonShapeNextSegmentedStart:after{content:&quot;&quot;;background:rgba(0,0,0,0.1);position:absolute;right:0;top:8px;height:16px;width:1px}.ytSpecButtonShapeNextSizeM{font-size:14px;line-height:36px}.ytSpecButtonShapeNextSizeM .ytSpecButtonShapeNextIcon{width:24px;height:24px}.ytSpecButtonShapeNextSizeM.ytSpecButtonShapeNextSegmentedStart:after{content:&quot;&quot;;background:rgba(0,0,0,0.1);position:absolute;right:0;top:6px;height:24px;width:1px}.ytSpecButtonShapeNextSizeL{padding:0 24px}.ytSpecButtonShapeNextSizeL{height:48px;font-size:18px;line-height:48px;border-radius:24px}.ytSpecButtonShapeNextSizeL.ytSpecButtonShapeNextSegmentedStart:after{content:&quot;&quot;;background:rgba(0,0,0,0.1);position:absolute;right:0;top:12px;height:24px;width:1px}.ytSpecButtonShapeNextSizeXl.ytSpecButtonShapeNextSegmentedStart:after{content:&quot;&quot;;background:rgba(0,0,0,0.1);position:absolute;right:0;top:16px;height:24px;width:1px}.ytSpecButtonShapeNextIconOnlyDefault{min-width:0;border-radius:50%;width:40px;height:40px;padding:0}.ytSpecButtonShapeNextCallToAction.ytSpecButtonShapeNextFilled:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#0556bf;border-color:transparent}@media (hover:none){.ytSpecButtonShapeNextCallToAction.ytSpecButtonShapeNextFilled:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#065fd4}}@media (hover:none){.ytSpecButtonShapeNextCallToAction.ytSpecButtonShapeNextFilled:active:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#0556bf;border-color:transparent}}.ytSpecButtonShapeNextCallToAction.ytSpecButtonShapeNextOutline:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#def1ff;border-color:transparent}@media (hover:none){.ytSpecButtonShapeNextCallToAction.ytSpecButtonShapeNextOutline:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:transparent;border-color:rgba(0,0,0,0.1)}}@media (hover:none){.ytSpecButtonShapeNextCallToAction.ytSpecButtonShapeNextOutline:active:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#def1ff;border-color:transparent}}.ytSpecButtonShapeNextCallToAction.ytSpecButtonShapeNextText:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#def1ff;border-color:transparent}@media (hover:none){.ytSpecButtonShapeNextCallToAction.ytSpecButtonShapeNextText:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:transparent}}@media (hover:none){.ytSpecButtonShapeNextCallToAction.ytSpecButtonShapeNextText:active:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#def1ff;border-color:transparent}}.ytSpecButtonShapeNextCallToAction.ytSpecButtonShapeNextTonal:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#dadfe6;border-color:transparent}@media (hover:none){.ytSpecButtonShapeNextCallToAction.ytSpecButtonShapeNextTonal:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#def1ff}}@media (hover:none){.ytSpecButtonShapeNextCallToAction.ytSpecButtonShapeNextTonal:active:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#dadfe6;border-color:transparent}}.ytSpecButtonShapeNextCallToAction.ytSpecButtonShapeNextFocused:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#def1ff;border-color:transparent}@media (hover:none){.ytSpecButtonShapeNextCallToAction.ytSpecButtonShapeNextFocused:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:unset;border-color:#065fd4}}@media (hover:none){.ytSpecButtonShapeNextCallToAction.ytSpecButtonShapeNextFocused:active:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#def1ff;border-color:transparent}}.ytSpecButtonShapeNextCallToActionInverse.ytSpecButtonShapeNextText:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#263850;border-color:transparent}@media (hover:none){.ytSpecButtonShapeNextCallToActionInverse.ytSpecButtonShapeNextText:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:transparent}}@media (hover:none){.ytSpecButtonShapeNextCallToActionInverse.ytSpecButtonShapeNextText:active:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#263850;border-color:transparent}}.ytSpecButtonShapeNextCallToActionOverlay.ytSpecButtonShapeNextFilled:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#65b8ff;border-color:transparent}@media (hover:none){.ytSpecButtonShapeNextCallToActionOverlay.ytSpecButtonShapeNextFilled:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#3ea6ff}}@media (hover:none){.ytSpecButtonShapeNextCallToActionOverlay.ytSpecButtonShapeNextFilled:active:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#65b8ff;border-color:transparent}}.ytSpecButtonShapeNextCallToActionOverlay.ytSpecButtonShapeNextFocused:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#65b8ff;border-color:transparent}@media (hover:none){.ytSpecButtonShapeNextCallToActionOverlay.ytSpecButtonShapeNextFocused:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#000;border-color:#3ea6ff}}@media (hover:none){.ytSpecButtonShapeNextCallToActionOverlay.ytSpecButtonShapeNextFocused:active:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#65b8ff;border-color:transparent}}.ytSpecButtonShapeNextMono.ytSpecButtonShapeNextFilled:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#272727;border-color:transparent}@media (hover:none){.ytSpecButtonShapeNextMono.ytSpecButtonShapeNextFilled:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#030303}}@media (hover:none){.ytSpecButtonShapeNextMono.ytSpecButtonShapeNextFilled:active:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#272727;border-color:transparent}}.ytSpecButtonShapeNextMono.ytSpecButtonShapeNextOutline:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(0,0,0,0.1);border-color:transparent}@media (hover:none){.ytSpecButtonShapeNextMono.ytSpecButtonShapeNextOutline:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:transparent;border-color:rgba(0,0,0,0.1)}}@media (hover:none){.ytSpecButtonShapeNextMono.ytSpecButtonShapeNextOutline:active:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(0,0,0,0.1);border-color:transparent}}.ytSpecButtonShapeNextMono.ytSpecButtonShapeNextText{color:#030303}.ytSpecButtonShapeNextMono.ytSpecButtonShapeNextText:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(0,0,0,0.1);border-color:transparent}@media (hover:none){.ytSpecButtonShapeNextMono.ytSpecButtonShapeNextText:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:transparent}}@media (hover:none){.ytSpecButtonShapeNextMono.ytSpecButtonShapeNextText:active:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(0,0,0,0.1);border-color:transparent}}@media (-ms-high-contrast:active),(forced-colors:active){.ytSpecButtonShapeNextMono.ytSpecButtonShapeNextText{border:1px solid rgba(0,0,0,0.1)}}.ytSpecButtonShapeNextMono.ytSpecButtonShapeNextTonal:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(0,0,0,0.1);border-color:transparent}@media (hover:none){.ytSpecButtonShapeNextMono.ytSpecButtonShapeNextTonal:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(0,0,0,0.05)}}@media (hover:none){.ytSpecButtonShapeNextMono.ytSpecButtonShapeNextTonal:active:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(0,0,0,0.1);border-color:transparent}}.ytSpecButtonShapeNextMono.ytSpecButtonShapeNextFocused:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(0,0,0,0.1);border-color:transparent}@media (hover:none){.ytSpecButtonShapeNextMono.ytSpecButtonShapeNextFocused:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:unset;border-color:#030303}}@media (hover:none){.ytSpecButtonShapeNextMono.ytSpecButtonShapeNextFocused:active:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(0,0,0,0.1);border-color:transparent}}.ytSpecButtonShapeNextMonoInverse.ytSpecButtonShapeNextFilled:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#d9d9d9;border-color:transparent}@media (hover:none){.ytSpecButtonShapeNextMonoInverse.ytSpecButtonShapeNextFilled:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#fff}}@media (hover:none){.ytSpecButtonShapeNextMonoInverse.ytSpecButtonShapeNextFilled:active:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#d9d9d9;border-color:transparent}}.ytSpecButtonShapeNextMonoInverse.ytSpecButtonShapeNextOutline:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(255,255,255,0.2);border-color:transparent}@media (hover:none){.ytSpecButtonShapeNextMonoInverse.ytSpecButtonShapeNextOutline:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:transparent;border-color:rgba(255,255,255,0.2)}}@media (hover:none){.ytSpecButtonShapeNextMonoInverse.ytSpecButtonShapeNextOutline:active:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(255,255,255,0.2);border-color:transparent}}.ytSpecButtonShapeNextMonoInverse.ytSpecButtonShapeNextText:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(0,0,0,0.1);border-color:transparent}@media (hover:none){.ytSpecButtonShapeNextMonoInverse.ytSpecButtonShapeNextText:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:transparent}}@media (hover:none){.ytSpecButtonShapeNextMonoInverse.ytSpecButtonShapeNextText:active:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(0,0,0,0.1);border-color:transparent}}.ytSpecButtonShapeNextMonoInverse.ytSpecButtonShapeNextTonal:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(255,255,255,0.2);border-color:transparent}@media (hover:none){.ytSpecButtonShapeNextMonoInverse.ytSpecButtonShapeNextTonal:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(255,255,255,0.1)}}@media (hover:none){.ytSpecButtonShapeNextMonoInverse.ytSpecButtonShapeNextTonal:active:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(255,255,255,0.2);border-color:transparent}}.ytSpecButtonShapeNextMonoInverse.ytSpecButtonShapeNextFocused:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(255,255,255,0.2);border-color:transparent}@media (hover:none){.ytSpecButtonShapeNextMonoInverse.ytSpecButtonShapeNextFocused:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:unset;border-color:#fff}}@media (hover:none){.ytSpecButtonShapeNextMonoInverse.ytSpecButtonShapeNextFocused:active:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(255,255,255,0.2);border-color:transparent}}.ytSpecButtonShapeNextOverlay.ytSpecButtonShapeNextFilled:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#e6e6e6;border-color:transparent}@media (hover:none){.ytSpecButtonShapeNextOverlay.ytSpecButtonShapeNextFilled:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#fff}}@media (hover:none){.ytSpecButtonShapeNextOverlay.ytSpecButtonShapeNextFilled:active:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#e6e6e6;border-color:transparent}}.ytSpecButtonShapeNextOverlay.ytSpecButtonShapeNextOutline:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(255,255,255,0.1);border-color:transparent}@media (hover:none){.ytSpecButtonShapeNextOverlay.ytSpecButtonShapeNextOutline:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:transparent;border-color:rgba(255,255,255,0.3)}}@media (hover:none){.ytSpecButtonShapeNextOverlay.ytSpecButtonShapeNextOutline:active:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(255,255,255,0.1);border-color:transparent}}.ytSpecButtonShapeNextOverlay.ytSpecButtonShapeNextText{color:#fff}.ytSpecButtonShapeNextOverlay.ytSpecButtonShapeNextText:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(255,255,255,0.1);border-color:transparent}@media (hover:none){.ytSpecButtonShapeNextOverlay.ytSpecButtonShapeNextText:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:transparent}}@media (hover:none){.ytSpecButtonShapeNextOverlay.ytSpecButtonShapeNextText:active:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(255,255,255,0.1);border-color:transparent}}.ytSpecButtonShapeNextOverlay.ytSpecButtonShapeNextTonal:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(255,255,255,0.2);border-color:transparent}@media (hover:none){.ytSpecButtonShapeNextOverlay.ytSpecButtonShapeNextTonal:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(255,255,255,0.1)}}@media (hover:none){.ytSpecButtonShapeNextOverlay.ytSpecButtonShapeNextTonal:active:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(255,255,255,0.2);border-color:transparent}}.ytSpecButtonShapeNextOverlay.ytSpecButtonShapeNextFocused:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(255,255,255,0.1);border-color:transparent}@media (hover:none){.ytSpecButtonShapeNextOverlay.ytSpecButtonShapeNextFocused:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#000;border-color:#fff}}@media (hover:none){.ytSpecButtonShapeNextOverlay.ytSpecButtonShapeNextFocused:active:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(255,255,255,0.1);border-color:transparent}}.ytSpecButtonShapeNextOverlayDark.ytSpecButtonShapeNextFilled:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#e6e6e6;border-color:transparent}@media (hover:none){.ytSpecButtonShapeNextOverlayDark.ytSpecButtonShapeNextFilled:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#fff}}@media (hover:none){.ytSpecButtonShapeNextOverlayDark.ytSpecButtonShapeNextFilled:active:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#e6e6e6;border-color:transparent}}.ytSpecButtonShapeNextOverlayDark.ytSpecButtonShapeNextTonal:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(40,40,40,0.6);border-color:transparent}@media (hover:none){.ytSpecButtonShapeNextOverlayDark.ytSpecButtonShapeNextTonal:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(0,0,0,0.3)}}@media (hover:none){.ytSpecButtonShapeNextOverlayDark.ytSpecButtonShapeNextTonal:active:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(40,40,40,0.6);border-color:transparent}}.ytSpecButtonShapeNextOverlayDark.ytSpecButtonShapeNextFocused:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(40,40,40,0.6);border-color:transparent}@media (hover:none){.ytSpecButtonShapeNextOverlayDark.ytSpecButtonShapeNextFocused:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#000;border-color:rgba(0,0,0,0.3)}}@media (hover:none){.ytSpecButtonShapeNextOverlayDark.ytSpecButtonShapeNextFocused:active:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(40,40,40,0.6);border-color:transparent}}.ytSpecButtonShapeNextBrandGradient.ytSpecButtonShapeNextFilled:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:-webkit-linear-gradient(45deg,#e1002d 30%,#e01378 85%);background:linear-gradient(45deg,#e1002d 30%,#e01378 85%);border-color:transparent}@media (hover:none){.ytSpecButtonShapeNextBrandGradient.ytSpecButtonShapeNextFilled:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:-webkit-linear-gradient(45deg,#e1002d 30%,#e01378 85%);background:linear-gradient(45deg,#e1002d 30%,#e01378 85%)}}@media (hover:none){.ytSpecButtonShapeNextBrandGradient.ytSpecButtonShapeNextFilled:active:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:-webkit-linear-gradient(45deg,#e1002d 30%,#e01378 85%);background:linear-gradient(45deg,#e1002d 30%,#e01378 85%);border-color:transparent}}.ytSpecButtonShapeNextBrandGradient.ytSpecButtonShapeNextFocused:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(0,0,0,0.1);border-color:transparent}@media (hover:none){.ytSpecButtonShapeNextBrandGradient.ytSpecButtonShapeNextFocused:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:unset;border-color:#030303}}@media (hover:none){.ytSpecButtonShapeNextBrandGradient.ytSpecButtonShapeNextFocused:active:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(0,0,0,0.1);border-color:transparent}}.ytSpecButtonShapeNextGenAiGradient.ytSpecButtonShapeNextFilled:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:-webkit-gradient(linear,left top,right top,color-stop(0,#007a65),to(#7f0e7f));background:-webkit-linear-gradient(left,#007a65 0,#7f0e7f 100%);background:linear-gradient(90deg,#007a65 0,#7f0e7f 100%);border-color:transparent}@media (hover:none){.ytSpecButtonShapeNextGenAiGradient.ytSpecButtonShapeNextFilled:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:-webkit-gradient(linear,left top,right top,color-stop(0,#7f0e7f),color-stop(20%,#aa09aa),to(#ff4e45));background:-webkit-linear-gradient(left,#7f0e7f 0,#aa09aa 20%,#ff4e45 100%);background:linear-gradient(90deg,#7f0e7f 0,#aa09aa 20%,#ff4e45 100%)}}@media (hover:none){.ytSpecButtonShapeNextGenAiGradient.ytSpecButtonShapeNextFilled:active:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:-webkit-gradient(linear,left top,right top,color-stop(0,#007a65),to(#7f0e7f));background:-webkit-linear-gradient(left,#007a65 0,#7f0e7f 100%);background:linear-gradient(90deg,#007a65 0,#7f0e7f 100%);border-color:transparent}}.ytSpecButtonShapeNextGenAiGradient.ytSpecButtonShapeNextTonal:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:-webkit-gradient(linear,left top,right top,color-stop(0,rgba(0,122,101,0.2)),to(rgba(127,14,127,0.2)));background:-webkit-linear-gradient(left,rgba(0,122,101,0.2)0,rgba(127,14,127,0.2) 100%);background:linear-gradient(90deg,rgba(0,122,101,0.2)0,rgba(127,14,127,0.2) 100%);border-color:transparent}@media (hover:none){.ytSpecButtonShapeNextGenAiGradient.ytSpecButtonShapeNextTonal:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:-webkit-gradient(linear,left top,right top,from(rgba(127,14,127,0.2)),color-stop(20%,rgba(170,9,170,0.2)),to(rgba(255,78,69,0.2)));background:-webkit-linear-gradient(left,rgba(127,14,127,0.2)0,rgba(170,9,170,0.2) 20%,rgba(255,78,69,0.2) 100%);background:linear-gradient(90deg,rgba(127,14,127,0.2)0,rgba(170,9,170,0.2) 20%,rgba(255,78,69,0.2) 100%)}}@media (hover:none){.ytSpecButtonShapeNextGenAiGradient.ytSpecButtonShapeNextTonal:active:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:-webkit-gradient(linear,left top,right top,color-stop(0,rgba(0,122,101,0.2)),to(rgba(127,14,127,0.2)));background:-webkit-linear-gradient(left,rgba(0,122,101,0.2)0,rgba(127,14,127,0.2) 100%);background:linear-gradient(90deg,rgba(0,122,101,0.2)0,rgba(127,14,127,0.2) 100%);border-color:transparent}}.ytSpecButtonShapeNextGenAiGradient.ytSpecButtonShapeNextFocused:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(0,0,0,0.1);border-color:transparent}@media (hover:none){.ytSpecButtonShapeNextGenAiGradient.ytSpecButtonShapeNextFocused:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:unset;border-color:#030303}}@media (hover:none){.ytSpecButtonShapeNextGenAiGradient.ytSpecButtonShapeNextFocused:active:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:rgba(0,0,0,0.1);border-color:transparent}}.ytSpecButtonShapeNextRaised.ytSpecButtonShapeNextOutline:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#272727;border-color:transparent}@media (hover:none){.ytSpecButtonShapeNextRaised.ytSpecButtonShapeNextOutline:hover:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#fff;border-color:transparent}}@media (hover:none){.ytSpecButtonShapeNextRaised.ytSpecButtonShapeNextOutline:active:not(.ytSpecButtonShapeNextEnableLightTouchResponse){background:#272727;border-color:transparent}}:root{--yt-light-rim-color:unset;--yt-light-wash-opacity:0;--yt-light-wash-x:0;--yt-light-wash-y:0;--yt-light-wash-size:96px;--yt-light-wash-color:rgba(255,255,255,0.3)}.contribYtLightShapeStaticRimLight:before{content:&quot;&quot;;position:absolute;inset:0;border-radius:inherit;padding:.5px;-webkit-mask:-webkit-gradient(linear,left top,left bottom,color-stop(0,#fff)) content-box,-webkit-gradient(linear,left top,left bottom,color-stop(0,#fff));-webkit-mask:-webkit-linear-gradient(#fff 0 0) content-box,-webkit-linear-gradient(#fff 0 0);-webkit-mask-composite:xor;mask-composite:exclude;pointer-events:none}.contribYtLightShapeStaticRimLightSolid:before{background:-webkit-gradient(linear,left top,left bottom,from(var(--yt-light-rim-color,rgba(255,255,255,0.2))),color-stop(75%,transparent));background:-webkit-linear-gradient(var(--yt-light-rim-color,rgba(255,255,255,0.2)),transparent 75%);background:linear-gradient(var(--yt-light-rim-color,rgba(255,255,255,0.2)),transparent 75%)}.contribYtLightShapeStaticRimLightTonal:before{background:-webkit-gradient(linear,left top,left bottom,from(var(--yt-light-rim-color,rgba(0,0,0,0.05))),color-stop(75%,transparent));background:-webkit-linear-gradient(var(--yt-light-rim-color,rgba(0,0,0,0.05)),transparent 75%);background:linear-gradient(var(--yt-light-rim-color,rgba(0,0,0,0.05)),transparent 75%)}.contribYtLightShapeStaticRimLightOutline:before{background:-webkit-gradient(linear,left top,left bottom,from(var(--yt-light-rim-color,rgba(255,255,255,0.3))),color-stop(75%,transparent));background:-webkit-linear-gradient(var(--yt-light-rim-color,rgba(255,255,255,0.3)),transparent 75%);background:linear-gradient(var(--yt-light-rim-color,rgba(255,255,255,0.3)),transparent 75%)}.contribYtLightShapeStaticRimLightSolidInverse:before{background:-webkit-gradient(linear,left top,left bottom,from(var(--yt-light-rim-color,rgba(255,255,255,0.2))),color-stop(75%,transparent));background:-webkit-linear-gradient(var(--yt-light-rim-color,rgba(255,255,255,0.2)),transparent 75%);background:linear-gradient(var(--yt-light-rim-color,rgba(255,255,255,0.2)),transparent 75%)}.contribYtLightShapeStaticRimLightTonalInverse:before{background:-webkit-gradient(linear,left top,left bottom,from(var(--yt-light-rim-color,rgba(255,255,255,0.15))),color-stop(75%,transparent));background:-webkit-linear-gradient(var(--yt-light-rim-color,rgba(255,255,255,0.15)),transparent 75%);background:linear-gradient(var(--yt-light-rim-color,rgba(255,255,255,0.15)),transparent 75%)}.contribYtLightShapeStaticRimLightOutlineInverse:before{background:-webkit-gradient(linear,left top,left bottom,from(var(--yt-light-rim-color,rgba(255,255,255,0.15))),color-stop(75%,transparent));background:-webkit-linear-gradient(var(--yt-light-rim-color,rgba(255,255,255,0.15)),transparent 75%);background:linear-gradient(var(--yt-light-rim-color,rgba(255,255,255,0.15)),transparent 75%)}.contribYtLightShapeStaticRimLightOverlaySolid:before{background:-webkit-gradient(linear,left top,left bottom,from(var(--yt-light-rim-color,rgba(255,255,255,0.2))),color-stop(75%,transparent));background:-webkit-linear-gradient(var(--yt-light-rim-color,rgba(255,255,255,0.2)),transparent 75%);background:linear-gradient(var(--yt-light-rim-color,rgba(255,255,255,0.2)),transparent 75%)}.contribYtLightShapeStaticRimLightOverlayTonal:before{background:-webkit-gradient(linear,left top,left bottom,from(var(--yt-light-rim-color,rgba(255,255,255,0.15))),color-stop(75%,transparent));background:-webkit-linear-gradient(var(--yt-light-rim-color,rgba(255,255,255,0.15)),transparent 75%);background:linear-gradient(var(--yt-light-rim-color,rgba(255,255,255,0.15)),transparent 75%)}.contribYtLightShapeStaticRimLightOverlayOutline:before{background:-webkit-gradient(linear,left top,left bottom,from(var(--yt-light-rim-color,rgba(255,255,255,0.15))),color-stop(75%,transparent));background:-webkit-linear-gradient(var(--yt-light-rim-color,rgba(255,255,255,0.15)),transparent 75%);background:linear-gradient(var(--yt-light-rim-color,rgba(255,255,255,0.15)),transparent 75%)}.ytLightTouchFeedbackShapeHoverOverlayDefault:hover .ytLightTouchFeedbackShapeHoverOverlay{opacity:.05}:root[dark] .ytLightTouchFeedbackShapeHoverOverlayDefault:hover .ytLightTouchFeedbackShapeHoverOverlay{opacity:.2}.ytLightTouchFeedbackShapeHoverOverlayInverse:hover .ytLightTouchFeedbackShapeHoverOverlay{opacity:.2}:root[dark] .ytLightTouchFeedbackShapeHoverOverlayInverse:hover .ytLightTouchFeedbackShapeHoverOverlay{opacity:.05}.ytLightTouchFeedbackShapeHost:hover .ytLightTouchFeedbackShapeHoverLight{opacity:.2}.ytSpecButtonViewModelHost{display:flex}.ytSpecIconShapeHost{display:flex;-webkit-box-align:center;-webkit-align-items:center;align-items:center;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;width:100%;height:100%}@-webkit-keyframes spinner{0%{-webkit-transform:rotate(0deg)}to{-webkit-transform:rotate(1turn)}}@keyframes spinner{0%{-webkit-transform:rotate(0deg);transform:rotate(0deg)}to{-webkit-transform:rotate(1turn);transform:rotate(1turn)}}.ytSliderShapeHostIncrementButton:disabled{cursor:default;color:#909090}.ytSliderShapeHostSlider::-webkit-slider-runnable-track{background:-webkit-gradient(linear,left top,right top,from(#030303),color-stop(#030303),color-stop(#909090),to(#909090));background:-webkit-linear-gradient(left,#030303 0,#030303 var(--yt-slider-shape-gradient-percent),#909090 var(--yt-slider-shape-gradient-percent),#909090 100%);background:linear-gradient(to right,#030303 0,#030303 var(--yt-slider-shape-gradient-percent),#909090 var(--yt-slider-shape-gradient-percent),#909090 100%);height:4px;border-radius:12px}.ytSliderShapeHostSlider::-moz-range-track{background:linear-gradient(to right,#030303 0,#030303 var(--yt-slider-shape-gradient-percent),#909090 var(--yt-slider-shape-gradient-percent),#909090 100%);height:4px;border-radius:12px}.ytSliderShapeHostSlider::-webkit-slider-thumb{-webkit-appearance:none;appearance:none;background:#030303;width:16px;height:16px;border-radius:8px;margin-top:-6px}.ytSliderShapeHostSlider::-moz-range-thumb{background:#030303;width:16px;height:16px;border-radius:8px}.ytSliderShapeHostSlider::-ms-thumb{background:#030303;width:16px;height:16px;border-radius:8px}.ytSliderShapeVerticalSlider::-webkit-slider-runnable-track{background:-webkit-gradient(linear,left bottom,left top,from(#030303),color-stop(#030303),color-stop(#909090),to(#909090));background:-webkit-linear-gradient(bottom,#030303 0,#030303 var(--yt-slider-shape-gradient-percent),#909090 var(--yt-slider-shape-gradient-percent),#909090 100%);background:linear-gradient(to top,#030303 0,#030303 var(--yt-slider-shape-gradient-percent),#909090 var(--yt-slider-shape-gradient-percent),#909090 100%);width:4px;border-radius:12px}.ytSliderShapeVerticalSlider::-moz-range-track{background:linear-gradient(to top,#030303 0,#030303 var(--yt-slider-shape-gradient-percent),#909090 var(--yt-slider-shape-gradient-percent),#909090 100%);width:4px;border-radius:12px}.ytSliderShapeVerticalSlider::-webkit-slider-thumb{-webkit-appearance:none;appearance:none;background:#030303;width:16px;height:16px;border-radius:8px;margin-right:-6px}.ytSliderShapeVerticalSlider::-moz-range-thumb{background:#030303;width:16px;height:16px;border-radius:8px}.ytSliderShapeVerticalSlider::-ms-thumb{background:#030303;width:16px;height:16px;border-radius:8px}.ytContextualSheetLayoutContentContainer::-webkit-scrollbar{background:transparent;width:16px}.ytContextualSheetLayoutContentContainer::-webkit-scrollbar-thumb{height:56px;border-radius:8px;border:4px solid transparent;background-clip:content-box;background-color:transparent}.ytContextualSheetLayoutContentContainer:hover{scrollbar-color:#909090 transparent}.ytContextualSheetLayoutContentContainer:hover::-webkit-scrollbar-thumb{background-color:#909090}bottom-sheet-container{position:fixed;z-index:5}c3-icon{display:inline-flex;-webkit-flex-shrink:0;flex-shrink:0;fill:currentColor;stroke:none}.icon-button{border:none;background:transparent;box-sizing:border-box}ytm-menu-item .menu-item-button:hover{background-color:rgba(0,0,0,0.05)}.ytBadgeShapeHost{display:flex;-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-flex-direction:row;flex-direction:row;-webkit-box-align:center;-webkit-align-items:center;align-items:center}.ytBadgeShapeTypography{font-family:Roboto,&quot;Arial&quot;,sans-serif;font-size:1.2rem;line-height:1.8rem;font-weight:500}.ytBadgeShapeThumbnailBadge{border-radius:4px;padding:1px 4px}.ytBadgeShapeText{display:block;white-space:nowrap}.ytBadgeShapeThumbnailDefault{color:#fff;background:rgba(0,0,0,0.6)}.ytBadgeShapeAd.ytBadgeShapeAdsIncludeDot:after{content:&quot;·&quot;;-webkit-padding-end:4px;padding-inline-end:4px;-webkit-padding-start:4px;padding-inline-start:4px}ytm-thumbnail-overlay-time-status-renderer[thumbnail-size=large]{margin:8px}.video-thumbnail-container-large{position:relative;-webkit-flex-shrink:0;flex-shrink:0;overflow:hidden}.video-thumbnail-img{position:absolute;top:0;bottom:0;left:0;right:0;width:100%;min-height:100%;margin:auto}.video-thumbnail-bg{background-color:rgba(0,0,0,0.1)}.video-thumbnail-container-large{display:flex;-webkit-box-align:center;-webkit-align-items:center;align-items:center;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;padding-bottom:56.25%}@media (max-width:549px) and (orientation:portrait){.style-recommendations-in-portrait .video-thumbnail-container-large.rounded-thumbnail{border-radius:12px;margin:0;padding-top:0}}@media (max-width:299px) and (orientation:portrait){.video-thumbnail-container-large{margin:0;padding-top:0}}@media (min-width:550px) and (orientation:portrait),(min-width:931px) and (orientation:landscape){.video-thumbnail-container-large.rounded-thumbnail{border-radius:12px}}@media (min-aspect-ratio:13/9) and (orientation:landscape),(min-width:931px) and (orientation:landscape){.video-thumbnail-container-large.rounded-thumbnail{border-radius:12px}}@media (max-width:930px) and (orientation:landscape){.style-recommendations-in-portrait .video-thumbnail-container-large.rounded-thumbnail,player-fullscreen-controls .video-thumbnail-container-large.rounded-thumbnail{border-radius:12px}}.ytwReelActionBarViewModelHostMobile .ytSpecButtonShapeNextHost:focus{background:unset}.copyDebugInfoPlayerDebugInfo::-webkit-input-placeholder{color:#606060;opacity:1}.copyDebugInfoPlayerDebugInfo::placeholder{color:#606060;opacity:1}.ytPlayerControlsContainerHost{-webkit-tap-highlight-color:rgba(0,0,0,0);-webkit-user-select:none}.ytPlayerControlsContainerRendered{position:fixed;inset:0;padding-bottom:0}.ytmCuedOverlayHost{display:block;cursor:pointer;position:absolute;top:0;bottom:0;left:0;right:0;z-index:1;-webkit-transition:-webkit-transform .7s;transition:transform .7s,-webkit-transform .7s;-webkit-transition-property:opacity;transition-property:opacity}.ytmCuedOverlayHidden{visibility:hidden;opacity:0}.ytmCuedOverlayPlayButton{position:absolute;top:50%;left:50%;margin-left:-36px;margin-top:-36px;width:72px;height:72px;padding:0;border:0;background:transparent;cursor:pointer}.ytmCuedOverlayPlayButtonIcon{width:100%;height:100%;color:red}@media (forced-colors:active){.ytmCuedOverlayPlayButtonIcon{color:ButtonText}}.ytmCuedOverlayGradient{width:100%;position:absolute;background:linear-gradient(rgba(0,0,0,.8),rgba(0,0,0,.6),rgba(0,0,0,0));height:7rem}@-webkit-keyframes fade-in{0%{opacity:0}to{opacity:1}}@keyframes fade-in{0%{opacity:0}to{opacity:1}}.ytwPlayerUserEduTooltipHost{display:block}.ytwPlayerUserEduTooltipTooltipContainer{display:flex;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;padding:0 8px;position:absolute;width:100%;box-sizing:border-box}.ytwPlayerUserEduTooltipTooltipWrapper{position:relative;width:max-content;margin-top:16px;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;z-index:6;border-radius:16px;background-color:rgba(0,0,0,0.6);padding:7px 16px;max-width:100%;box-sizing:border-box}.ytmVideoCoverHost{display:block;cursor:pointer;position:absolute;top:0;bottom:0;left:0;right:0;z-index:1;-webkit-transition:-webkit-transform .7s;transition:transform .7s,-webkit-transform .7s;-webkit-transition-property:opacity;transition-property:opacity}.ytmVideoCoverHost.hidden{visibility:hidden;opacity:0}.ytmVideoCoverThumbnail{background-position:50%;background-repeat:no-repeat;background-size:cover;width:100%;height:100%}.ytdVolumeControlsNativeSlider::-webkit-slider-runnable-track{background:-webkit-gradient(linear,left top,right top,from(#fff),color-stop(#fff),color-stop(rgba(255,255,255,0.3)),to(rgba(255,255,255,0.3)));background:-webkit-linear-gradient(left,#fff 0,#fff var(--gradient-percent),rgba(255,255,255,0.3) var(--gradient-percent),rgba(255,255,255,0.3) 100%);background:linear-gradient(to right,#fff 0,#fff var(--gradient-percent),rgba(255,255,255,0.3) var(--gradient-percent),rgba(255,255,255,0.3) 100%);height:4px;border-radius:12px}.ytdVolumeControlsNativeSlider::-moz-range-track{background:linear-gradient(to right,#fff 0,#fff var(--gradient-percent),rgba(0,0,0,0.1) var(--gradient-percent),rgba(0,0,0,0.1) 100%);height:4px;border-radius:12px}.ytdVolumeControlsNativeSlider::-webkit-slider-thumb{-webkit-appearance:none;width:16px;height:16px;background-color:white;border-radius:50%;margin-top:-6.5px}.ytdVolumeControlsNativeSlider::-moz-range-thumb{width:16px;height:16px;background-color:white;border-radius:50%;border:none}.ytdVolumeControlsNativeSlider::-webkit-slider-runnable-track{height:2px}.ytdVolumeControlsNativeSlider::-moz-range-track{height:2px}.ytdVolumeControlsBackgroundScrimExpandedHoverState:after{content:&quot;&quot;;position:absolute;inset:4px;background:transparent;border-radius:28px;background-color:rgba(255,255,255,0.1);-webkit-transition-property:width,background-color;transition-property:width,background-color}.ytwPlayerFullscreenActionMenuHost{display:block}.ytmFullscreenRelatedVideosEntryPointViewModelHost{display:block}.ytmFullscreenRelatedVideosEntryPointViewModelButton{display:flex;-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-flex-direction:row;flex-direction:row;-webkit-box-align:center;-webkit-align-items:center;align-items:center;height:48px;border-radius:24px;padding-left:12px;padding-right:12px;white-space:nowrap;color:#fff;border:none;font-family:Roboto,&quot;Arial&quot;,sans-serif;font-size:1.4rem;line-height:2rem;font-weight:400;background:rgba(0,0,0,0.3);text-shadow:0 0 2px #000}.ytmFullscreenRelatedVideosEntryPointViewModelThumbnailStack{display:flex;-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;flex-direction:column;-webkit-box-align:center;-webkit-align-items:center;align-items:center;margin-top:-1px}.ytmFullscreenRelatedVideosEntryPointViewModelHost:not(.ytmFullscreenRelatedVideosEntryPointViewModelTitleHidden) .ytmFullscreenRelatedVideosEntryPointViewModelThumbnailStack{margin-left:8px}.ytmFullscreenRelatedVideosEntryPointViewModelFadedThumbnail{height:2px;margin-bottom:1px;border-top-left-radius:2px;border-top-right-radius:2px}.ytmFullscreenRelatedVideosEntryPointViewModelSmallFadedThumbnail{width:10px;background:rgba(255,255,255,0.3)}.ytmFullscreenRelatedVideosEntryPointViewModelLargeFadedThumbnail{width:20px;background:rgba(255,255,255,0.7)}.ytmFullscreenRelatedVideosEntryPointViewModelThumbnailContainer{display:flex;-webkit-box-align:center;-webkit-align-items:center;align-items:center;width:36px;height:20px;border:2px solid white;border-radius:8px;overflow:hidden}.ytmFullscreenRelatedVideosEntryPointViewModelThumbnail{display:block}.fullscreen-controls{--controls-height:215px;bottom:calc(var(--controls-height)*-1);left:0;right:0;position:absolute;height:var(--controls-height);z-index:2;display:flex;-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;flex-direction:column;padding:12px;box-sizing:border-box}.fullscreen-recommendations-wrapper::-webkit-scrollbar{display:none}@media (pointer:fine){.fullscreen-controls{--controls-height:228px}.fullscreen-recommendations-wrapper::-webkit-scrollbar{width:16px}.fullscreen-recommendations-wrapper::-webkit-scrollbar-thumb{height:56px;border-radius:8px;border:4px solid transparent;background-clip:content-box;background-color:#606060}.fullscreen-recommendations-wrapper::-webkit-scrollbar-thumb:hover{background-color:#909090}.fullscreen-recommendations-wrapper::-webkit-scrollbar{display:block}}.fullscreen-action-menu{position:absolute;bottom:12px;height:48px;display:flex;-webkit-box-align:center;-webkit-align-items:center;align-items:center;z-index:2}.action-menu-engagement-buttons-wrapper{display:flex;border-radius:48px;padding:4px 0;background:rgba(0,0,0,0.3);text-shadow:0 0 2px #000}.action-menu-engagement-buttons-wrapper button{text-shadow:0 0 2px #000}.action-menu-engagement-buttons-wrapper svg{-webkit-filter:drop-shadow(0 0 1px rgba(0,0,0,.8));filter:drop-shadow(0 0 1px rgba(0,0,0,.8))}.action-menu-engagement-buttons-wrapper>:first-child{margin-left:8px}.fullscreen-action-menu,.fullscreen-action-menu .ytAttributedStringHost{color:#fff}.fullscreen-watch-next-entrypoint-wrapper{margin-left:auto;height:100%}.quick-actions-wrapper{display:flex;-webkit-box-align:center;-webkit-align-items:center;align-items:center;height:100%;margin-left:auto}.watch-on-youtube-button-wrapper{direction:ltr}@media (max-height:221px){.watch-on-youtube-button-wrapper .ytSpecButtonShapeNextHost{height:40px;padding:0 20px}}.watch-on-youtube-button{padding-left:4px}.no-label-exp ytm-slim-metadata-button-renderer{margin-right:8px}@media (max-width:299px) and (orientation:landscape),(max-width:299px) and (orientation:portrait){ytm-custom-control .icon-button:not(.player-settings-icon){height:40px;width:40px;padding:8px}ytm-custom-control .icon-button:not(.player-settings-icon)>c3-icon{height:24px;width:24px}}ytm-custom-control .player-controls-middle .icon-button c3-icon svg path{fill:#fff}ytm-custom-control .player-controls-middle .icon-button.icon-disable c3-icon svg path{fill:rgba(255,255,255,0.3)}ytm-custom-control .player-controls-top{display:flex;position:absolute;top:0;z-index:2;right:24px}@media (max-height:221px){ytm-custom-control .player-controls-top{right:0}}ytm-custom-control .player-controls-top.ytwPlayerTopControlsContainerWithEmbeddedVideoDetails{left:0}@media (min-width:380px){ytm-custom-control .player-controls-top.ytwPlayerTopControlsContainerWithEmbeddedVideoDetails{left:14px}}#player-control-overlay{position:absolute;top:0;left:0;right:0;bottom:0;touch-action:manipulation}#player-control-overlay .player-controls-content{height:100%;overflow:hidden;visibility:hidden}#player-control-overlay .player-controls-background{position:absolute;top:0;left:0;right:0;bottom:0;z-index:0;opacity:0}#player-control-overlay .player-controls-background:not(.fullscreen-recs-expanded){background-image:linear-gradient(180deg,rgba(0,0,0,.6)0,rgba(0,0,0,.54) 25%,rgba(0,0,0,.36) 50%,rgba(0,0,0,.18) 75%,rgba(0,0,0,0));background-size:1px 50%;background-repeat:repeat-x}#player-control-overlay .player-controls-background-container{position:absolute;z-index:0;left:0;right:0;bottom:0;top:0;visibility:visible}#player-control-overlay.animation-enabled{-webkit-transition:-webkit-transform .7s;transition:transform .7s,-webkit-transform .7s;-webkit-transition-property:all;transition-property:all}#player-control-overlay.animation-enabled .player-controls-background{-webkit-transition-property:opacity;transition-property:opacity;-webkit-transition-duration:.2s;transition-duration:.2s}.fullscreen-controls-always-on .fullscreen-action-menu{left:12px;right:12px}@media (min-width:380px){.fullscreen-controls-always-on .fullscreen-action-menu{left:30px;right:30px}}@media (max-height:221px){.fullscreen-controls-always-on .fullscreen-action-menu{left:2px;right:2px;bottom:2px}}@-webkit-keyframes ytm-equalizer-animation{0%{-webkit-transform:scaleY(1);transform:scaleY(1)}50%{-webkit-transform:scaleY(.5);transform:scaleY(.5)}to{-webkit-transform:scaleY(1);transform:scaleY(1)}}@keyframes ytm-equalizer-animation{0%{-webkit-transform:scaleY(1);transform:scaleY(1)}50%{-webkit-transform:scaleY(.5);transform:scaleY(.5)}to{-webkit-transform:scaleY(1);transform:scaleY(1)}}.ytmA11yStylesHiddenButton{position:fixed;top:0;left:0;height:12px;width:12px}.ytmCreatorEndscreenHost{position:absolute;top:0;left:0;width:100%;height:100%;font-size:1.2rem;visibility:hidden}.ytmExpandingEndscreenElementOverlayCallToAction:hover{text-decoration:underline;cursor:pointer}.ytPfpControlsHost :focus-visible{box-shadow:inset 0 0 0 2px #3ea6ff}.ytPfpControlsOverlay:hover .ytPfpControlsLargePlayButtonIconBg{fill-opacity:1;-webkit-transition:fill .1s cubic-bezier(0,0,.2,1),fill-opacity .1s cubic-bezier(0,0,.2,1);transition:fill .1s cubic-bezier(0,0,.2,1),fill-opacity .1s cubic-bezier(0,0,.2,1)}.ytPfpEndscreenVideoRendererHost:focus-visible .ytPfpEndscreenVideoRendererOverlay,.ytPfpEndscreenVideoRendererHost:focus-within .ytPfpEndscreenVideoRendererOverlay,.ytPfpEndscreenVideoRendererHost:hover .ytPfpEndscreenVideoRendererOverlay{opacity:1}.ytPfpMoreVideosCloseButton:hover{background-color:rgba(255,255,255,.1)}.ytPfpMoreVideosVideoList::-webkit-scrollbar{display:none}.ytPfpMoreVideosPaddleLeft:hover,.ytPfpMoreVideosPaddleRight:hover{background-color:rgb(50,50,50)}.ytPfpMoreVideosMoreVideosButton:hover{background-color:rgba(255,255,255,.1)}.ytPfpEndscreenPlaylistRendererHost:focus-visible .ytPfpEndscreenPlaylistRendererOverlay,.ytPfpEndscreenPlaylistRendererHost:focus-within .ytPfpEndscreenPlaylistRendererOverlay,.ytPfpEndscreenPlaylistRendererHost:hover .ytPfpEndscreenPlaylistRendererOverlay{opacity:1}.ytPfpVideoWallEndscreenPaddleLeft:hover,.ytPfpVideoWallEndscreenPaddleRight:hover{background-color:rgb(50,50,50)}@-webkit-keyframes action-fade-out{0%{opacity:1}to{opacity:0;-webkit-transform:scale(2);transform:scale(2)}}@keyframes action-fade-out{0%{opacity:1}to{opacity:0;-webkit-transform:scale(2);transform:scale(2)}}.ytwPlayerTimeDisplayLiveDot:before{content:&quot;&quot;;display:block;height:6px;width:6px;border-radius:50%;background-color:rgba(255,255,255,0.7);margin-right:4px}.ytwPlayerTimeDisplayLiveDot.ytwPlayerTimeDisplayLiveHead:before{background-color:rgba(225,0,45,0.9);opacity:1}.ytwPlayerSeekOverlayHost{position:absolute;top:0;left:0;right:0;bottom:0;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;-webkit-box-align:center;-webkit-align-items:center;align-items:center;pointer-events:none;direction:ltr}.ytmCustomControlHost{display:block}.ytmCustomControlHost :focus-visible{box-shadow:inset 0 0 0 2px #3ea6ff}@-webkit-keyframes fade{to{opacity:0}}@keyframes fade{to{opacity:0;pointer-events:none}}.ytFullscreenVideoRecommendationsHost{-webkit-box-flex:1;-webkit-flex:1;flex:1;display:flex;overflow-x:auto}@media not (pointer:fine){.ytFullscreenVideoRecommendationsHost{-ms-overflow-style:none;scrollbar-width:none}.ytFullscreenVideoRecommendationsHost::-webkit-scrollbar{display:none}}@media (pointer:fine){.ytFullscreenVideoRecommendationsHost{overflow-y:auto}.ytFullscreenVideoRecommendationsHost::-webkit-scrollbar{width:16px}.ytFullscreenVideoRecommendationsHost::-webkit-scrollbar-thumb{height:56px;border-radius:8px;border:4px solid transparent;background-clip:content-box;background-color:#606060}.ytFullscreenVideoRecommendationsHost::-webkit-scrollbar-thumb:hover{background-color:#909090}}.ytFullscreenVideoRecommendationsRecommendation{width:200px;min-width:200px;margin:0 8px;color:#fff}.ytwPlayerFullscreenControlsHost{display:block}.ytwPlayerFullscreenTopControlsHost{padding:12px;color:#fff;display:flex;position:absolute;top:0;left:0;right:0;z-index:6}.ytwPlayerFullscreenTopControlsFullscreenControlsVideoTitle{margin:0;color:#fff}.ytwPlayerFullscreenTopControlsFullscreenCloseButtonWrapper{margin-top:-8px;margin-left:auto}@-webkit-keyframes fade{to{opacity:0}}@keyframes fade{to{opacity:0;pointer-events:none}}.ytwPlayerMiddleControlsA11ySeekButton{position:absolute;top:0;height:12px;width:12px}ytm-custom-control .player-controls-middle{display:flex;-webkit-box-align:center;-webkit-align-items:center;align-items:center;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;position:absolute;top:50%;z-index:6}ytm-custom-control .player-controls-middle.prevent-controls-collision{width:fit-content;left:50%;-webkit-transform:translate(-50%,-50%);transform:translate(-50%,-50%)}ytm-custom-control .player-controls-middle .icon-button{border-radius:24px;background-color:rgba(0,0,0,0.3)}ytm-custom-control .player-controls-middle .player-middle-controls-prev-next-button{height:48px;width:48px;padding:6px;background:none}ytm-custom-control .player-controls-middle .player-middle-controls-prev-next-button c3-icon{height:24px;width:24px}ytm-custom-control .player-controls-middle .player-middle-controls-prev-next-button .player-middle-controls-prev-next-visible-area{box-sizing:border-box;height:36px;width:36px;padding:6px;border-radius:50%;background:rgba(0,0,0,0.3);text-shadow:0 0 2px #000}ytm-custom-control .player-controls-middle .player-middle-controls-prev-next-button .player-middle-controls-prev-next-visible-area svg{-webkit-filter:drop-shadow(0 0 1px rgba(0,0,0,.8));filter:drop-shadow(0 0 1px rgba(0,0,0,.8))}ytm-custom-control .player-controls-middle .player-control-play-pause-icon.icon-button{height:56px;width:56px;padding:10px;border-radius:50%}ytm-custom-control .player-controls-middle .player-control-play-pause-icon.icon-button>c3-icon{height:36px;width:36px;padding:unset}ytm-custom-control .player-controls-middle .player-control-play-pause-icon{margin:0 34px}ytm-custom-control .player-controls-middle .player-controls-middle-core-buttons{display:flex;-webkit-box-align:center;-webkit-align-items:center;align-items:center;-webkit-box-pack:center;-webkit-justify-content:center;justify-content:center;direction:ltr}.ytmVideoInfoHost{-webkit-box-flex:1;-webkit-flex:1;flex:1;overflow:hidden;min-width:0}.ytmVideoInfoVideoDetailsContainer{display:flex;padding-left:12px;min-height:5.2rem}.ytmVideoInfoVideoTitle{font-family:Roboto,&quot;Arial&quot;,sans-serif;font-size:2rem;line-height:2.8rem;font-weight:700}@media (max-width:527.9px){.ytmVideoInfoVideoTitle{font-family:Roboto,&quot;Arial&quot;,sans-serif;font-size:1.8rem;line-height:2.6rem;font-weight:700}}.ytmVideoInfoVideoTitle{display:block;max-width:100%;padding-top:10px;overflow:hidden;white-space:nowrap;text-decoration:none;mask-image:linear-gradient(to right,#fff 95%,rgba(0,0,0,0))}.ytmVideoInfoChannelTitle{max-width:100%;font-size:1.2rem;overflow:hidden;white-space:nowrap;text-decoration:none;opacity:1;line-height:1.1}.ytmVideoInfoVideoTitleContainer{min-height:36px;-webkit-box-flex:1;-webkit-flex:1;flex:1;overflow:hidden;color:#fff;margin-right:12px}.ytmVideoInfoLogoEnabled{padding-left:52px}.ytmVideoInfoChannelLogo{width:36px;height:36px;display:block;background-size:contain;background-repeat:no-repeat;-webkit-flex-shrink:0;flex-shrink:0;margin:12px;margin-top:8px;border-radius:50%;z-index:7;position:absolute;top:0}.ytmVideoInfoChannelContainer{height:5.2rem;position:absolute;left:0;min-width:52px;max-width:calc(100% - 12px);margin:4px;margin-top:8px;overflow:hidden}.ytmVideoInfoOverlay{display:flex;-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-flex-direction:row;flex-direction:row;border-radius:10px;background-color:rgba(0,0,0,0.8);margin-left:4px;height:100%;-webkit-transition:all .2s;transition:all .2s;-webkit-transition-timing-function:cubic-bezier(.05,0,0,1);transition-timing-function:cubic-bezier(.05,0,0,1);opacity:0;max-width:0;padding-left:52px;padding-right:10px;z-index:5;position:relative}.ytmVideoInfoChannelInfo{display:block;padding-right:8px;-webkit-box-flex:1;-webkit-flex:1;flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;-webkit-align-self:center;align-self:center;max-width:100%}.ytmVideoInfoFlyoutChannelTitle{font-size:1.4rem;color:#fff;text-overflow:ellipsis;max-width:100%;display:block;margin-bottom:2px;-webkit-align-self:center;align-self:center}.ytmVideoInfoFlyoutChannelSubtitle{font-size:1.2rem;color:#fff;text-overflow:ellipsis;max-width:100%;display:block}.ytmVideoInfoLink:hover{cursor:pointer}.ytmVideoInfoChannelAvatar{display:block;position:absolute;width:48px;height:48px}.ytwPlayerTopControlsHost{display:block}.ytwPlayerTopControlsContainerWithEmbeddedVideoDetails{-webkit-box-pack:end;-webkit-justify-content:flex-end;justify-content:flex-end}@media (max-aspect-ratio:3/4){.ytwPlayerTopControlsContainerWithEmbeddedVideoDetails{-webkit-box-orient:vertical;-webkit-box-direction:reverse;-webkit-flex-direction:column-reverse;flex-direction:column-reverse}}.ytmWatchPlayerControlsHost{display:block}.ytmWatchPlayerControlsBackgroundActionItems{position:absolute;z-index:2}.unified-share-url-input:focus{margin-bottom:0;border-bottom-width:2px;border-bottom-color:#030303}.unified-share-url-input::-webkit-input-placeholder{color:#606060;opacity:1}.unified-share-url-input::placeholder{color:#606060;opacity:1}.ytChipBarViewModelChipBarScrollContainer::-webkit-scrollbar{display:none}.ytChipBarViewModelLeftArrowContainer:after{width:50px;content:&quot;&quot;;pointer-events:none;background:-webkit-gradient(linear,right top,left top,from(rgba(255,255,255,0)),color-stop(25%,rgba(255,255,255,0.3)),color-stop(50%,rgba(255,255,255,0.6)),color-stop(75%,rgba(255,255,255,0.9)),to(white));background:-webkit-linear-gradient(right,rgba(255,255,255,0)0,rgba(255,255,255,0.3) 25%,rgba(255,255,255,0.6) 50%,rgba(255,255,255,0.9) 75%,white 100%);background:linear-gradient(to left,rgba(255,255,255,0)0,rgba(255,255,255,0.3) 25%,rgba(255,255,255,0.6) 50%,rgba(255,255,255,0.9) 75%,white 100%)}.ytChipBarViewModelRightArrowContainer:before{width:50px;content:&quot;&quot;;pointer-events:none;background:-webkit-gradient(linear,left top,right top,from(rgba(255,255,255,0)),color-stop(25%,rgba(255,255,255,0.3)),color-stop(50%,rgba(255,255,255,0.6)),color-stop(75%,rgba(255,255,255,0.9)),to(white));background:-webkit-linear-gradient(left,rgba(255,255,255,0)0,rgba(255,255,255,0.3) 25%,rgba(255,255,255,0.6) 50%,rgba(255,255,255,0.9) 75%,white 100%);background:linear-gradient(to right,rgba(255,255,255,0)0,rgba(255,255,255,0.3) 25%,rgba(255,255,255,0.6) 50%,rgba(255,255,255,0.9) 75%,white 100%)}.ytStandardsTextareaShapeTextareaContainerOutline:focus-within{border:2px solid #030303;padding:23px 11px 7px}.ytStandardsTextareaShapeTextareaContainerOutlineError:focus-within{border-color:#c30027}.ytStandardsTextareaShapeTextareaContainerLabelHidden.ytStandardsTextareaShapeTextareaContainerOutline:focus-within{padding:9px 11px 6px}.ytStandardsTextareaShapeTextarea::-webkit-input-placeholder{color:transparent}.ytStandardsTextareaShapeTextarea::placeholder{color:transparent}@media (forced-colors:active){.ytStandardsTextareaShapeTextarea::-webkit-input-placeholder{opacity:0}.ytStandardsTextareaShapeTextarea::placeholder{opacity:0}}.ytStandardsTextareaShapeTextarea:disabled{color:#909090}.ytwChannelBlocksViewModelListWrapper::-webkit-scrollbar{background:transparent;width:16px}.ytwChannelBlocksViewModelListWrapper::-webkit-scrollbar-thumb{height:56px;border-radius:8px;border:4px solid transparent;background-clip:content-box;background-color:transparent}.ytwChannelBlocksViewModelListWrapper:hover{scrollbar-color:#909090 transparent}.ytwChannelBlocksViewModelListWrapper:hover::-webkit-scrollbar-thumb{background-color:#909090}.ytwAdDisclosureBannerViewModelHostIsClickableAdComponent:focus-visible{outline:2px solid #fff;outline-offset:2px}.ytFlexibleActionsViewModelScrollable::-webkit-scrollbar{background:transparent;width:8px}.ytFlexibleActionsViewModelScrollable::-webkit-scrollbar-thumb{height:56px;background:transparent}.ytFlexibleActionsViewModelScrollable:hover{scrollbar-color:#909090 transparent}.ytFlexibleActionsViewModelScrollable:hover::-webkit-scrollbar-thumb{background:#909090}.ytwCarouselAdCardCollectionViewModelMetadataAttachmentCarousel::-webkit-scrollbar{display:none}.ytwCarouselAdCardImageViewModelHost:after,.ytwCarouselAdCardImageViewModelHostIsClickableAdComponent:after{content:&quot;&quot;;background-color:#000;will-change:opacity;opacity:0;-webkit-transition:opacity .3s cubic-bezier(.05,0,0,1);transition:opacity .3s cubic-bezier(.05,0,0,1);pointer-events:none;position:absolute;top:0;right:0;bottom:0;left:0}.ytwCarouselAdCardImageViewModelHost:hover:after,.ytwCarouselAdCardImageViewModelHostIsClickableAdComponent:hover:after{opacity:.1}.ytSlimlineSurveyViewModelHost:before{content:&quot;&quot;;background-color:#fff;box-shadow:0 0 4px rgba(0,0,0,0.25);position:absolute;margin-left:-12px;width:24px;height:24px;top:-12px;left:50%;-webkit-transform:rotate(45deg);transform:rotate(45deg)}.ytSlimlineSurveyViewModelCollapsed:before{opacity:0}.YtmBadgeAndBylineRendererHost{-webkit-box-orient:vertical;-webkit-line-clamp:2;max-height:3em;text-overflow:ellipsis;overflow:hidden}@media (max-width:299px) and (orientation:landscape),(max-width:299px) and (orientation:portrait){.YtmBadgeAndBylineRendererHost{display:block;max-height:none;overflow:visible}}.YtmBadgeAndBylineRendererItemByline{margin-right:4px}.YtmBadgeAndBylineRendererItemByline{font-size:1.2rem;display:inline;opacity:.6}@-webkit-keyframes metadata-bounce{0%,to{-webkit-transform:translateX(0);transform:translateX(0)}20%,80%{-webkit-transform:translateX(-120px);transform:translateX(-120px)}}@keyframes metadata-bounce{0%,to{-webkit-transform:translateX(0);transform:translateX(0)}20%,80%{-webkit-transform:translateX(-120px);transform:translateX(-120px)}}ytm-media-item{display:block;min-width:0}ytm-media-item[use-vertical-layout]{padding:0}ytm-media-item[use-vertical-layout] .media-item-metadata{margin-top:8px}ytm-media-item[use-vertical-layout] .media-channel{margin-top:8px}ytm-media-item>a{display:block}.media-item-thumbnail-container{position:relative}.media-item-metadata{display:flex;-webkit-box-flex:1;-webkit-flex-grow:1;flex-grow:1;-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;flex-direction:column;min-width:0}.media-item-headline{-webkit-box-orient:vertical;max-height:2.5em;-webkit-line-clamp:2;overflow:hidden;line-height:1.25;text-overflow:ellipsis;font-weight:400;margin:0 0 3px;font-size:1.4rem}.media-channel{-webkit-flex-shrink:0;flex-shrink:0}.media-item-info,ytm-media-item .media-item-details{-webkit-box-flex:1;-webkit-flex-grow:1;flex-grow:1;min-width:0}.media-item-info{display:flex;-webkit-box-align:start;-webkit-align-items:flex-start;align-items:flex-start}.media-item-info[no-channel-avatar=true]{margin-left:0;margin-right:0}ytm-media-item .media-item-details{display:flex}ytm-media-item[use-vertical-layout] .media-item-details{margin-top:4px}@media (min-aspect-ratio:13/9) and (orientation:landscape),(min-width:931px) and (orientation:landscape){ytm-media-item[use-vertical-layout] .media-item-headline{margin-bottom:8px}}@media (max-width:299px) and (orientation:landscape),(max-width:299px) and (orientation:portrait){ytm-media-item{padding:0 8px}ytm-media-item .media-item-details{margin-top:12px;margin-bottom:12px}}ytm-video-with-context-renderer{display:block}@media (max-width:299px) and (orientation:landscape),(max-width:299px) and (orientation:portrait){ytm-video-with-context-renderer.feed-item{margin-bottom:16px}}@media (min-width:300px) and (orientation:landscape),(min-width:300px) and (orientation:portrait){ytm-video-with-context-renderer.feed-item{margin-bottom:24px}}.videoThumbnailGroupOverlayBottomLeftRightGroup{width:100%;bottom:0;position:absolute;display:flex;-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;flex-direction:column}.videoThumbnailGroupTimeStatus{right:0;bottom:0;position:absolute}.videoThumbnailGroupOverlayLeftRightGroup{position:relative;width:100%;bottom:0;height:100%}.ytContentMetadataViewModelMetadataRow:focus-visible{outline:2px solid currentColor;outline-offset:2px;border-radius:4px}@property --poll-choice-percentage{syntax:&quot;<number>&quot;;inherits:false;initial-value:0}.ytUpsellViewModelHostUpsellTitle:focus,.ytUpsellViewModelHostUpsellTitle:focus-visible{outline:none}.ytMiniAppScoreChallengeViewModelScoreRadial:before{content:&quot;&quot;;position:absolute;top:0;left:0;width:100%;height:100%;-webkit-transform-origin:center;transform-origin:center;border-radius:31px;-webkit-mask-image:-webkit-radial-gradient(rgb(0,0,0) 20%,transparent 60%);mask-image:radial-gradient(rgb(0,0,0) 20%,transparent 60%);-webkit-filter:blur(2px);filter:blur(2px);background:repeating-conic-gradient(from 0deg,#f7d7c5 0deg 10deg,transparent 10deg 20deg)}.ytMiniAppScoreChallengeViewModelDarkScoreRadial:before{background:repeating-conic-gradient(from 0deg,#424f5d 0deg 10deg,transparent 10deg 20deg)}.ytLockupMetadataViewModelMenuButton:focus-within{opacity:1}.ytThumbnailHoverOverlayViewModelHost:focus,.ytThumbnailHoverOverlayViewModelHost:focus-within,.ytThumbnailHoverOverlayViewModelHost:hover{opacity:1}.ytShelfHeaderLayoutHoverable:hover{background-color:rgba(0,0,0,0.05)}@-webkit-keyframes scrolling-parent{to{-webkit-transform:translateX(var(--marquee-translate,0));transform:translateX(var(--marquee-translate,0))}}@keyframes scrolling-parent{to{-webkit-transform:translateX(var(--marquee-translate,0));transform:translateX(var(--marquee-translate,0))}}.ytSpecAvatarShapeLiveRing:after{border-radius:50%;padding:2px;position:absolute;content:&quot;&quot;;inset:-4px;mask:-webkit-gradient(linear,left top,left bottom,color-stop(0,#fff)) content-box,-webkit-gradient(linear,left top,left bottom,color-stop(0,#fff));mask:linear-gradient(#fff 0 0) content-box,linear-gradient(#fff 0 0);-webkit-mask:-webkit-gradient(linear,left top,left bottom,color-stop(0,#fff)) content-box,-webkit-gradient(linear,left top,left bottom,color-stop(0,#fff));-webkit-mask:-webkit-linear-gradient(#fff 0 0) content-box,-webkit-linear-gradient(#fff 0 0);-webkit-mask-composite:xor;mask-composite:exclude;background:-webkit-gradient(linear,left bottom,right top,color-stop(60%,#e1002d),color-stop(85%,#e01378));background:-webkit-linear-gradient(bottom left,#e1002d 60%,#e01378 85%);background:linear-gradient(to top right,#e1002d 60%,#e01378 85%)}.interstitialViewModelModelContainer:before{content:&quot;&quot;;position:absolute;top:0;left:0;width:100%;height:100%;background-repeat:no-repeat;background-color:#0f0f0f;background-image:-webkit-gradient(linear,left top,right top,from(transparent),color-stop(35%,transparent),color-stop(35%,rgba(170,9,170,.2)),color-stop(84%,rgba(169,0,255,.2)),to(rgba(254,70,160,.2)));background-image:-webkit-linear-gradient(left,transparent,transparent 35%,rgba(170,9,170,.2)0,rgba(169,0,255,.2) 84%,rgba(254,70,160,.2));background-image:linear-gradient(90deg,transparent 0,transparent 35%,rgba(170,9,170,.2)0,rgba(169,0,255,.2) 84%,rgba(254,70,160,.2));-webkit-mask-image:-webkit-radial-gradient(77% 50%,27% 58%,#0f0f0f 0,transparent 100%);mask-image:radial-gradient(27% 58%at 77% 50%,#0f0f0f 0,transparent 100%)}[dir=rtl] .interstitialViewModelModelContainer:before{-webkit-mask-image:-webkit-radial-gradient(23% 50%,27% 58%,#0f0f0f 0,transparent 100%);mask-image:radial-gradient(27% 58%at 23% 50%,#0f0f0f 0,transparent 100%)}.interstitialViewModelModelContainer:before{-webkit-mask-repeat:no-repeat;mask-repeat:no-repeat}.interstitialViewModelLogoContainer:before{content:&quot;&quot;;display:block;padding-top:22.727%}.ytShortsSuggestedActionViewModelStaticHost:hover{background-color:rgba(0,0,0,0.3)}.ytShortsSuggestedActionViewModelDynamicHostContainer:hover{background-color:rgba(0,0,0,0.6);border-color:transparent}.ytShortsSuggestedActionViewModelDynamicHostCollapsedTrailingSection:hover{background-color:rgba(0,0,0,0.6)}.ytShortsSuggestedActionViewModelDynamicHostExpandedTrailingSection:hover{background-color:rgba(0,0,0,0.6)}.ytShortsSuggestedActionViewModelExtractOverlayContainer:hover{background-color:rgba(0,0,0,0.1)}.ytShortsSuggestedActionViewModelExtractOverlayCollapsedTrailingSection:hover{background-color:rgba(0,0,0,0.1)}.ytShortsSuggestedActionViewModelExtractOverlayExpandedTrailingSection:hover{background-color:rgba(0,0,0,0.1)}@-webkit-keyframes fadeIn{0%{opacity:0}to{opacity:1}}@keyframes fadeIn{0%{opacity:0}to{opacity:1}}
<style nonce>html{overflow:hidden}body{font:12px Roboto,Arial,sans-serif;background-color:#000;color:#fff;height:100%;width:100%;overflow:hidden;position:absolute;margin:0;padding:0}h3{margin-top:6px;margin-bottom:3px}
.sf-hidden{display:none!important}
@keyframes rzp-rot{to{transform:rotate(360deg)}}@-webkit-keyframes rzp-rot{to{-webkit-transform:rotate(360deg)}}</style><style>html,body{overflow-y:auto!important;height:auto!important;min-height:100%!important;position:relative!important;}html,body{background-color:#ffcdad!important;}.container{max-width:540px!important;margin-left:auto!important;margin-right:auto!important;}.add-to-contact-btn,.add-to-contact-section,[class*=add-to-contact]{left:50%!important;right:auto!important;transform:translateX(-50%)!important;max-width:540px!important;width:100%!important;}.blog-section,.blog-card,[class*=blog-],[class*=__blog],[class*=blog-section]{display:none!important;}.product-slider,.gallery-slider,.testimonial-slider{overflow:hidden;}.product-slider .slick-slide,.gallery-slider .slick-slide{padding:0 8px;box-sizing:border-box;}.pwa-support,.news-modal,#newsLatter-content{display:none!important}.social-icon i,.social-icon svg,.social-icon .icon{color:#2563eb!important;fill:#2563eb!important;opacity:1!important}.our-services-section .section-heading,.business-hour-section .section-heading{text-align:center!important}.our-services-section .section-heading h2,.business-hour-section .section-heading h2,.qr-code-section .section-heading h2{color:#2563eb!important}.business-hour-section .business-hour-card{background:rgba(127,127,127,.14)!important;border:1px solid rgba(127,127,127,.3)!important;border-radius:10px!important;padding:10px!important;margin-bottom:10px!important}.business-hour-section .business-hour-card span,.business-hour-section .business-hour-card .time-icon{color:#2563eb!important}.qr-code-section p,.qr-code-section span,.qr-code-section h4,.qr-code-section h5{color:#2563eb!important}</style><?php if(!empty($vcard["custom_css"])): ?><style><?= $vcard["custom_css"] ?></style><?php endif; ?></head><body>
 <div class=bg-vectors>
 <div class=fireworks>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 <div class=line>
 <div class=spark>
 <div class=fire></div>
 </div>
 </div>
 </div>
</div>
 <div class="position-relative z-index-2">
 <div class="container p-0">
 <div id=passwordModal class="modal fade sf-hidden" role=dialog data-bs-backdrop=static data-bs-keyboard=false>
 
</div>
 <div class="main-content mx-auto w-100 overflow-hidden">
 <div class="d-flex justify-content-end position-absolute top-0 end-0 mx-3 pt-3 language-btn">
 <div class=language>
 <ul class="text-decoration-none ps-0">
 <li class="dropdown1 dropdown lang-list">
 <a class="dropdown-toggle lang-head text-decoration-none" data-toggle=dropdown role=button aria-haspopup=true aria-expanded=false>
 EN
 </a>
 <ul class="dropdown-menu top-dropdown lang-hover-list top-100 mt-0 sf-hidden">
 
 
 
 
 
 
 
 
 
 
 
 
 </ul>
 </li>
 </ul>
 </div>
 </div>
 
 <div class=mt-0>
 <div class="pwa-support d-flex align-items-center justify-content-center">
 <div>
 <h1 class="text-start text-gradient pwa-heading">Install as App</h1>
 <p class="text-start pwa-text text-white">Get a seamless experience by adding this website to your home screen—just like an app! </p>
 <div class="text-end d-flex">
 <button id=installPwaBtn class="pwa-install-button w-50 mb-1 btn" fdprocessedid=e89hh1j>Install </button>
 <button class="pwa-cancel-button w-50 ms-2 pwa-close btn btn-secondary mb-1" fdprocessedid=g67yv5>Cancel</button>
 </div>
 </div>
 </div>
 </div>
 
 <div class="banner-section position-relative w-100"><div class="banner-img" style="position:relative;overflow:hidden;height:315px;"><?php $cvType=$vcard["cover_type"]??"image";$cvVal=$vcard["cover_image"]??"";$isVid=($cvType==="video")||preg_match("#youtube\.com|youtu\.be|instagram\.com|\.mp4#i",$cvVal);if($isVid&&!empty($cvVal)){if(preg_match("#(?:youtube\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^\"&?/\s]{11})#i",$cvVal,$mm)){$yt=$mm[1];echo "<iframe style=\"width:100%;height:100%;display:block;border:none;\" src=\"https://www.youtube.com/embed/".$yt."?autoplay=1&mute=1&loop=1&playlist=".$yt."&controls=0&showinfo=0&rel=0&playsinline=1\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share\" allowfullscreen></iframe>";}elseif(stripos($cvVal,"instagram.com")!==false){echo "<iframe style=\"width:100%;height:100%;display:block;border:none;\" src=\"".htmlspecialchars(rtrim($cvVal,"/")."/embed")."\" allowtransparency=\"true\"></iframe>";}else{echo "<video src=\"".htmlspecialchars(imgUrl($cvVal))."\" autoplay loop muted playsinline style=\"width:100%;height:100%;object-fit:cover;display:block;\"></video>";}}else{echo "<img src=\"".htmlspecialchars($coverImg)."\" alt=\"".htmlspecialchars($fullName)."\" style=\"width:100%;height:100%;object-fit:cover;display:block;\">";} ?><div style="position:absolute;inset:0;background:linear-gradient(to bottom,rgba(0,0,0,0.1),rgba(0,0,0,0.5));"></div></div></div>
 <div class="profile-section main-section px-30 pt-30 pb-30 position-relative overflow-hidden">
 <div class="position-absolute vector-main-1">
 <img src="/images/templates/eventmanagementx/eve-042.webp" alt=vector-img class=w-100>
 </div>
 <div>
 <div class="card flex-sm-row align-items-center">
 <div class="card-img me-sm-3">
 <img src="<?= $profileImg ?>" class="h-100 w-100 object-fit-cover profileImage" loading=lazy>
 </div>
 <div class="card-body text-sm-start text-center p-0">
 <div class=profile-name>
 <h2 class="text-white fs-24 fw-6 mb-0">
 <?= htmlspecialchars($fullName) ?>
 <i class="verification-icon bi-patch-check-fill"></i>
 </h2>
 <p class="fs-16 text-gradient mb-0"><?= htmlspecialchars($vcard["occupation"] ?? "") ?></p>
 <p class="fs-14 text-gray-100 mb-0">Event Planner</p>
 <p class="fs-14 text-gray-100 mb-0"></p>
 </div>
 </div>
 </div>
 </div>
 
 </div>
 <div class="main-section pt-30 pb-30 px-30 position-relative">
 <div class="position-absolute vector-all vector-1">
 <img src=/images/templates/eventmanagementx/eve-043.webp alt=vector-img class=w-100>
 </div>
 <div class="text-white mb-0 fs-14 text-center profile-desc"><?php if(!empty($vcard["description"])): ?><?= nl2br(htmlspecialchars(trim(html_entity_decode(strip_tags($vcard["description"]),ENT_QUOTES)))) ?><?php else: ?>
 <p>As an <strong>Event Planner</strong>, I specialize in organizing and coordinating events, ensuring every detail is planned meticulously. From corporate events to weddings, my goal is to create unforgettable experiences for clients. I work closely with vendors, clients, and teams to ensure seamless execution and satisfaction.</p>
 <?php endif; ?></div>
 <div class="social-media d-flex justify-content-end"><?php foreach ($socialLinks as $s): $__sp=strtolower($s["platform"] ?? ""); $__svg=$socialSvgs[$__sp] ?? $socialSvgs["globe"]; ?><a href="<?= htmlspecialchars($s["url"]) ?>" target="_blank" rel="noopener" class="social-icon"><?= $__svg ?></a><?php endforeach; ?></div>
 
 </div>
 
 
 <div class="contact-section main-section pt-30 pb-30 px-30 position-relative">
 <div class="position-absolute vector-all vector-2">
 <img src="/images/templates/eventmanagementx/eve-044.webp" alt=vector-img class=w-100>
 </div>
 <div class=section-heading>
 <h2 class="text-white mb-0">Contact</h2>
 </div>
 
 <div class=position-relative>
 <div class="row row-gap-15px">
 <div class=col-sm-6>
 <div class=contact-box>
 <div class="contact-icon d-flex justify-content-center align-items-center">
 <img src=/images/templates/eventmanagementx/eve-045.svg>
 </div>
 <div class=contact-desc>
 <a href=mailto:<?= htmlspecialchars($vcard["email"] ?? "") ?>><?= htmlspecialchars($vcard["email"] ?? "") ?></a>
 </div>
 </div>
 </div>
 
 <div class=col-sm-6>
 <div class=contact-box>
 <div class="contact-icon d-flex justify-content-center align-items-center">
 <img src=/images/templates/eventmanagementx/eve-045.svg>
 </div>
 <div class=contact-desc>
 <a href=mailto:<?= htmlspecialchars($vcard["alternate_email"] ?? "") ?>><?= htmlspecialchars($vcard["alternate_email"] ?? "") ?></a>
 </div>
 </div>
 </div>
 
 <div class=col-sm-6>
 <div class=contact-box>
 <div class="contact-icon d-flex justify-content-center align-items-center">
 <img src="/images/templates/eventmanagementx/eve-046.svg">
 </div>
 <div class=contact-desc>
 <a href="tel:<?= htmlspecialchars($vcard["phone"] ?? "") ?>" dir=ltr><?= htmlspecialchars($vcard["phone"] ?? "") ?></a>
 </div>
 </div>
 </div>
 
 <div class=col-sm-6>
 <div class=contact-box>
 <div class="contact-icon d-flex justify-content-center align-items-center">
 <img src="/images/templates/eventmanagementx/eve-046.svg">
 </div>
 <div class=contact-desc>
 <a href="tel:<?= htmlspecialchars($vcard["alternate_phone"] ?? "") ?>" dir=ltr><?= htmlspecialchars($vcard["phone"] ?? "") ?></a>
 </div>
 </div>
 </div>
 
 <div class=col-sm-6>
 <div class=contact-box>
 <div class="contact-icon d-flex justify-content-center align-items-center">
 <img src="/images/templates/eventmanagementx/eve-047.svg">
 </div>
 <div class=contact-desc>
 <p class=mb-0><?= !empty($vcard["dob"]) ? htmlspecialchars(date("jS F, Y", strtotime($vcard["dob"]))) : "" ?></p>
 </div>
 </div>
 </div>
 <div class=col-sm-6>
 <div class=contact-box>
 <div class="contact-icon d-flex justify-content-center align-items-center">
 <img src="/images/templates/eventmanagementx/eve-048.svg">
 </div>
 <div class=contact-desc>
 <p class="mb-0 fs-12">New Delhi,India</p>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
 
 <div class="gallery-section main-section pt-30 pb-30 position-relative">
 <div class="position-absolute vector-all vector-3">
 <img src="/images/templates/eventmanagementx/eve-049.webp" alt=vector-img class=w-100>
 </div>
 <div class="section-heading px-30">
 <h2 class="text-white mb-0">Gallery</h2>
 </div>
 <div class=px-40>
 <div class="gallery-slider"><?php foreach ((isset($__ga)?$__ga:($galleries ?? [])) as $g): foreach (($g["images"] ?? []) as $im): $gi=imgUrl($im["image_url"] ?? ($im["image"] ?? "")); ?><div class="px-2"><div class="gallery-img-wrapper"><div class="gallery-img" style="background-image:url('<?= htmlspecialchars($gi) ?>');background-size:cover;background-position:center;height:280px;border-radius:12px;"></div></div></div><?php endforeach; endforeach; ?></div>
 </div>
 </div>
 
 <?php if(!empty($services)): ?><div class="our-services-section pt-50 position-relative"><div class="section-heading"><h2>Our Services</h2></div><div class="services"><div class="px-30"><div class="row"><?php foreach ((isset($__sv)?$__sv:($services ?? [])) as $sv): $svimg=!empty($sv["image"])?imgUrl($sv["image"]):"/images/templates/eventmanagementx/eve-038.png"; ?><div class="col-sm-6 mb-sm-0 mb-40 p-3"><div class="card-wrapper h-100"><a href="javascript:void(0)" class="text-decoration-none"><div class="service-card card h-100"><div class="card-img mx-auto"><img src="<?= htmlspecialchars($svimg) ?>" alt="<?= htmlspecialchars($sv["name"] ?? "") ?>" class="w-100 h-100 object-fit-cover" loading="lazy"></div><div class="card-body text-center"><h3 class="card-title text-primary"><?= htmlspecialchars($sv["name"] ?? "") ?></h3><?php if(!empty($sv["description"])): ?><p class="mb-0 text-gray"><?= htmlspecialchars($sv["description"]) ?></p><?php endif; ?></div></div></a></div></div><?php endforeach; ?></div></div></div></div><?php endif; ?>
 
 <div class="qr-code-section main-section pt-30 pb-30 px-30 position-relative">
 <div class="position-absolute vector-all vector-5">
 <img src="/images/templates/eventmanagementx/eve-055.webp" alt=vector-img class=w-100>
 </div>
 <div class=section-heading>
 <h2 class="text-white mb-0">QR Code</h2>
 </div>
 <div class="qr-code mx-auto position-relative">
 <div class="d-flex gap-3 align-items-center flex-wrap flex-sm-nowrap justify-content-center text-center text-sm-start">
 <div class="qr-code-img text-center" id=qr-code-fourteen>
 
<svg xmlns=http://www.w3.org/2000/svg version=1.1 width=130 height=130 viewBox="0 0 130 130"><rect x=0 y=0 width=130 height=130 fill=#ffffff></rect><g transform=scale(4.483)><g transform=translate(0,0)><path fill-rule=evenodd d="M8 0L8 4L9 4L9 5L8 5L8 7L9 7L9 6L10 6L10 9L11 9L11 8L12 8L12 9L13 9L13 10L15 10L15 11L14 11L14 12L13 12L13 11L12 11L12 12L11 12L11 13L7 13L7 12L6 12L6 11L8 11L8 12L10 12L10 11L11 11L11 10L9 10L9 9L7 9L7 8L4 8L4 9L2 9L2 8L0 8L0 9L2 9L2 10L1 10L1 11L2 11L2 16L5 16L5 17L4 17L4 18L2 18L2 19L3 19L3 21L4 21L4 19L5 19L5 20L6 20L6 21L7 21L7 20L8 20L8 22L9 22L9 23L8 23L8 25L9 25L9 26L10 26L10 27L8 27L8 29L12 29L12 28L13 28L13 29L16 29L16 26L17 26L17 29L19 29L19 28L20 28L20 29L25 29L25 28L26 28L26 27L27 27L27 29L28 29L28 28L29 28L29 25L28 25L28 24L29 24L29 17L28 17L28 16L29 16L29 13L28 13L28 12L29 12L29 8L25 8L25 9L24 9L24 8L23 8L23 9L22 9L22 11L19 11L19 9L21 9L21 8L20 8L20 7L21 7L21 6L20 6L20 5L19 5L19 4L20 4L20 3L21 3L21 2L20 2L20 1L19 1L19 0L15 0L15 1L14 1L14 0L13 0L13 1L11 1L11 2L12 2L12 5L13 5L13 1L14 1L14 2L15 2L15 4L14 4L14 5L17 5L17 6L16 6L16 7L17 7L17 9L16 9L16 8L15 8L15 9L14 9L14 7L15 7L15 6L14 6L14 7L13 7L13 6L12 6L12 7L11 7L11 3L10 3L10 2L9 2L9 0ZM15 1L15 2L16 2L16 3L17 3L17 4L19 4L19 3L17 3L17 2L19 2L19 1L17 1L17 2L16 2L16 1ZM18 5L18 6L17 6L17 7L18 7L18 6L19 6L19 7L20 7L20 6L19 6L19 5ZM12 7L12 8L13 8L13 7ZM18 8L18 9L17 9L17 10L18 10L18 9L19 9L19 8ZM4 9L4 10L3 10L3 11L4 11L4 12L3 12L3 13L4 13L4 14L3 14L3 15L6 15L6 16L7 16L7 17L6 17L6 18L7 18L7 19L6 19L6 20L7 20L7 19L8 19L8 18L7 18L7 17L9 17L9 18L10 18L10 19L9 19L9 20L10 20L10 21L9 21L9 22L12 22L12 21L13 21L13 22L15 22L15 21L13 21L13 20L14 20L14 19L15 19L15 18L13 18L13 17L12 17L12 16L13 16L13 15L12 15L12 16L11 16L11 15L10 15L10 14L9 14L9 16L8 16L8 14L7 14L7 13L6 13L6 14L5 14L5 11L4 11L4 10L5 10L5 9ZM6 9L6 10L7 10L7 9ZM8 10L8 11L9 11L9 10ZM23 10L23 11L22 11L22 13L27 13L27 11L28 11L28 10ZM15 11L15 12L16 12L16 13L17 13L17 14L16 14L16 15L18 15L18 17L17 17L17 16L15 16L15 15L14 15L14 17L17 17L17 18L18 18L18 17L19 17L19 20L18 20L18 21L20 21L20 19L21 19L21 20L23 20L23 19L24 19L24 20L26 20L26 21L25 21L25 22L27 22L27 23L25 23L25 24L26 24L26 25L22 25L22 27L21 27L21 25L20 25L20 26L19 26L19 23L18 23L18 22L17 22L17 23L18 23L18 25L17 25L17 26L19 26L19 27L18 27L18 28L19 28L19 27L21 27L21 28L23 28L23 27L24 27L24 28L25 28L25 26L28 26L28 25L27 25L27 24L28 24L28 20L27 20L27 19L28 19L28 18L27 18L27 15L28 15L28 14L27 14L27 15L25 15L25 14L24 14L24 17L26 17L26 18L23 18L23 19L22 19L22 18L21 18L21 17L19 17L19 16L20 16L20 15L21 15L21 16L22 16L22 17L23 17L23 16L22 16L22 15L23 15L23 14L22 14L22 15L21 15L21 14L20 14L20 12L18 12L18 13L17 13L17 11ZM23 11L23 12L26 12L26 11ZM0 12L0 16L1 16L1 12ZM12 12L12 13L11 13L11 14L12 14L12 13L13 13L13 14L15 14L15 13L13 13L13 12ZM6 14L6 15L7 15L7 14ZM18 14L18 15L19 15L19 14ZM0 17L0 18L1 18L1 17ZM10 17L10 18L11 18L11 17ZM26 18L26 19L27 19L27 18ZM12 19L12 20L11 20L11 21L12 21L12 20L13 20L13 19ZM16 19L16 21L17 21L17 19ZM0 20L0 21L2 21L2 20ZM21 21L21 24L24 24L24 21ZM22 22L22 23L23 23L23 22ZM9 23L9 25L10 25L10 26L11 26L11 28L12 28L12 27L13 27L13 28L14 28L14 27L15 27L15 26L13 26L13 25L12 25L12 24L13 24L13 23L12 23L12 24L10 24L10 23ZM14 23L14 25L16 25L16 24L15 24L15 23ZM0 0L0 7L7 7L7 0ZM1 1L1 6L6 6L6 1ZM2 2L2 5L5 5L5 2ZM22 0L22 7L29 7L29 0ZM23 1L23 6L28 6L28 1ZM24 2L24 5L27 5L27 2ZM0 22L0 29L7 29L7 22ZM1 23L1 28L6 28L6 23ZM2 24L2 27L5 27L5 24Z" fill=#000000></path></g></g></svg>
 </div>
 <div>
 <h5>Scan to Contact</h5>
 <p class="fs-14 text-gray mb-0">Point your phone’s camera at the QR code to quickly add our contact information. You can also use the "Add to Contacts" button below for fast saving.</p>
 </div>
 </div>
 </div>
 </div>
 
 <div class="product-section main-section pt-30 pb-30 position-relative">
 <div class="position-absolute vector-all vector-6">
 <img src="/images/templates/eventmanagementx/eve-056.webp" alt=vector-img class=w-100>
 </div>
 <div class=section-heading>
 <h2 class="text-white mb-0">Products</h2>
 </div>
 <div class=px-20>
 <div class="product-slider"><?php foreach ((isset($__pr)?$__pr:($products ?? [])) as $p): $pi=!empty($p["image"])?imgUrl($p["image"]):"/images/templates/eventmanagementx/eve-038.png"; ?><div class="px-2"><div class="product-card card"><div class="product-img card-img"><img src="<?= htmlspecialchars($pi) ?>" class="w-100 h-100 object-fit-cover" loading="lazy"></div><div class="product-desc card-body d-flex flex-column align-items-center justify-content-between"><div class="product-title"><h3 class="text-dark text-center"><?= htmlspecialchars($p["name"] ?? "") ?></h3></div><?php if(isset($p["price"]) && $p["price"]!==""): ?><div class="product-amount"><span>₹ <?= htmlspecialchars($p["price"]) ?></span></div><?php endif; ?></div></div></div><?php endforeach; ?></div>
 </div>
 <div class="text-center mt-5">
 <a class="fs-6 text-decoration-underline btn-primary view-more d-inline-flex gap-2 align-items-center" href=https://tapifyworld.com/products/36/event-planner>View More Products
 <svg class="svg-inline--fa fa-arrow-right right-arrow-animation text-decoration-none" aria-hidden=true focusable=false data-prefix=fas data-icon=arrow-right role=img xmlns=http://www.w3.org/2000/svg viewBox="0 0 448 512" data-fa-i2svg><path fill=currentColor d="M438.6 278.6l-160 160C272.4 444.9 264.2 448 256 448s-16.38-3.125-22.62-9.375c-12.5-12.5-12.5-32.75 0-45.25L338.8 288H32C14.33 288 .0016 273.7 .0016 256S14.33 224 32 224h306.8l-105.4-105.4c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0l160 160C451.1 245.9 451.1 266.1 438.6 278.6z"></path></svg></a>
 </div>
 </div>
 
 <div class="testimonial-section main-section pt-30 pb-30 position-relative">
 <div class="position-absolute vector-all vector-7">
 <img src=/images/templates/eventmanagementx/eve-057.webp alt=vector-img class=w-100>
 </div>
 <div class=section-heading>
 <h2 class="text-white mb-0">Testimonials</h2>
 </div>
 <div class=px-20>
 <div class="testimonial-slider"><?php foreach ((isset($__te)?$__te:($testimonials ?? [])) as $t): ?><div class="px-2"><div class="testimonial-card p-0"><div class="card-body text-center position-relative"><div class="text-center"><p class="text-gray mb-0">“<?= htmlspecialchars($t["message"] ?? "") ?>”</p></div></div><div class="d-flex flex-column align-items-center justify-content-center gap-2 profile-desc"><?php if(!empty($t["image"])): ?><div class="card-img" style="width:60px;height:60px;border-radius:50%;overflow:hidden;"><img src="<?= htmlspecialchars(imgUrl($t["image"])) ?>" class="w-100 h-100 object-fit-cover"></div><?php endif; ?><h5 class="fw-6 mb-0"><?= htmlspecialchars($t["author_name"] ?? ($t["author"] ?? "")) ?></h5></div></div></div><?php endforeach; ?></div>
 </div>
 </div>
 
 
 <div class="appointment-section main-section pt-30 pb-30 px-30 position-relative">
 <div class="position-absolute vector-all vector-8">
 <img src="/images/templates/eventmanagementx/eve-058.webp" alt=vector-img class=w-100>
 </div>
 <div class=section-heading>
 <h2 class="text-white mb-0">Make an Appointment</h2>
 </div>
 <div class="appointment-card p-3">
 <div class=row>
 <div class=col-12>
 <div class=position-relative>
 <input class="date appointment-input text-start flatpickr-input active" placeholder="Pick a Date" id=pickUpDate name=date type=text readonly fdprocessedid=ffqj53 value>
 <span class=calendar-icon>
 <svg width=20 height=20 viewBox="0 0 20 20" xmlns=http://www.w3.org/2000/svg>
 <path d="M6.25 9.375V10.625C6.25 10.9705 5.97047 11.25 5.625 11.25H4.375C4.02953 11.25 3.75 10.9705 3.75 10.625V9.375C3.75 9.02953 4.02953 8.75 4.375 8.75H5.625C5.97047 8.75 6.25 9.02953 6.25 9.375ZM5.625 13.75H4.375C4.02953 13.75 3.75 14.0295 3.75 14.375V15.625C3.75 15.9705 4.02953 16.25 4.375 16.25H5.625C5.97047 16.25 6.25 15.9705 6.25 15.625V14.375C6.25 14.0295 5.97047 13.75 5.625 13.75ZM10.625 8.75H9.375C9.02953 8.75 8.75 9.02953 8.75 9.375V10.625C8.75 10.9705 9.02953 11.25 9.375 11.25H10.625C10.9705 11.25 11.25 10.9705 11.25 10.625V9.375C11.25 9.02953 10.9705 8.75 10.625 8.75ZM10.625 13.75H9.375C9.02953 13.75 8.75 14.0295 8.75 14.375V15.625C8.75 15.9705 9.02953 16.25 9.375 16.25H10.625C10.9705 16.25 11.25 15.9705 11.25 15.625V14.375C11.25 14.0295 10.9705 13.75 10.625 13.75ZM15.625 8.75H14.375C14.0295 8.75 13.75 9.02953 13.75 9.375V10.625C13.75 10.9705 14.0295 11.25 14.375 11.25H15.625C15.9705 11.25 16.25 10.9705 16.25 10.625V9.375C16.25 9.02953 15.9705 8.75 15.625 8.75ZM15.625 13.75H14.375C14.0295 13.75 13.75 14.0295 13.75 14.375V15.625C13.75 15.9705 14.0295 16.25 14.375 16.25H15.625C15.9705 16.25 16.25 15.9705 16.25 15.625V14.375C16.25 14.0295 15.9705 13.75 15.625 13.75ZM4.375 3.75H5.625C5.97047 3.75 6.25 3.47047 6.25 3.125V0.625C6.25 0.279531 5.97047 0 5.625 0H4.375C4.02953 0 3.75 0.279531 3.75 0.625V3.125C3.75 3.47047 4.02953 3.75 4.375 3.75ZM20 5V17.5C20 18.8806 18.8806 20 17.5 20H2.5C1.11937 20 0 18.8806 0 17.5V5C0 3.61937 1.11937 2.5 2.5 2.5H3.125V3.125C3.125 3.81348 3.6859 4.375 4.375 4.375H5.625C6.3141 4.375 6.875 3.81348 6.875 3.125V2.5H13.125V3.125C13.125 3.81348 13.6865 4.375 14.375 4.375H15.625C16.3135 4.375 16.875 3.81348 16.875 3.125V2.5H17.5C18.8806 2.5 20 3.61937 20 5ZM18.75 7.5C18.75 6.81152 18.1897 6.25 17.5 6.25H2.5C1.8109 6.25 1.25 6.81152 1.25 7.5V17.5C1.25 18.1897 1.8109 18.75 2.5 18.75H17.5C18.1897 18.75 18.75 18.1897 18.75 17.5V7.5ZM14.375 3.75H15.625C15.9705 3.75 16.25 3.47047 16.25 3.125V0.625C16.25 0.279531 15.9705 0 15.625 0H14.375C14.0295 0 13.75 0.279531 13.75 0.625V3.125C13.75 3.47047 14.0295 3.75 14.375 3.75Z" fill=#a8aeb7></path>
 </svg>
 </span>
 </div>
 </div>
 </div>
 <div class=col-12>
 <div id=slotData class=row>
 </div>
 <div class="col-12 d-flex justify-content-center">
 <button type=submit class="btn btn-primary appointmentAdd d-none sf-hidden">
 Make an Appointment
 </button>
 </div>
 </div>
 </div>
 </div>
 <div class="modal fade appointment-modal sf-hidden" id=AppointmentModal tabindex=-1 aria-hidden=true>
 
</div>
 
 
 <div class="blog-section main-section pt-30 pb-30 position-relative">
 <div class="position-absolute vector-all vector-10">
 <img src=/images/templates/eventmanagementx/eve-059.webp alt=vector-img class=w-100>
 </div>
 <div class=section-heading>
 <h2 class="text-white mb-0">Blog</h2>
 </div>
 <div class="blog-slider slick-initialized slick-slider slick-dotted"><div class="slick-list draggable" style="padding:0px 45px"><div class=slick-track style=opacity:1;width:5800px;transform:translate3d(-1160px,0px,0px)><div class="slick-slide slick-cloned" data-slick-index=-2 aria-hidden=true tabindex=-1 style=width:580px><div><div style=width:100%;display:inline-block>
 <div class=blog-card>
 <div class=card-img>
 <a href=https://tapifyworld.com/event-planner/blog/71 tabindex=-1>
 <img src='data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==' alt=profile class="w-100 h-100 object-fit-cover mx-auto" loading=lazy style="background-blend-mode:normal!important;background-clip:content-box!important;background-position:50% 50%!important;background-color:rgba(0,0,0,0)!important;background-image:var(--sf-img-55)!important;background-size:cover!important;background-origin:content-box!important;background-repeat:no-repeat!important">
 </a>
 </div>
 <div class="card-body d-flex flex-column justify-content-between text-start">
 <h5 class="text-gradient fs-20 mb-2"> Serving Dinner or Light Fare</h5>
 <div class="text-dark blog-desc mb-1">
 <p><span style=color:rgb(108,117,125);font-family:Poppins,Helvetica,sans-serif>Depending on your event’s nature, you’ll want to serve either a full dinner or light fare. Since the event is from 5 PM to 8 PM, it’s often best to serve lighter options unless the event is a formal dinner.</span></p>
 </div>
 <div class="text-end ms-auto">
 <a href=https://tapifyworld.com/event-planner/blog/71 class="text-primary d-inline-block fs-14" tabindex=-1>
 Read More...
 </a>
 </div>
 </div>
 </div>
 </div></div></div><div class="slick-slide slick-cloned" data-slick-index=-1 aria-hidden=true tabindex=-1 style=width:580px><div><div style=width:100%;display:inline-block>
 <div class=blog-card>
 <div class=card-img>
 <a href=https://tapifyworld.com/event-planner/blog/72 tabindex=-1>
 <img src='data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==' alt=profile class="w-100 h-100 object-fit-cover mx-auto" loading=lazy style="background-blend-mode:normal!important;background-clip:content-box!important;background-position:50% 50%!important;background-color:rgba(0,0,0,0)!important;background-image:var(--sf-img-56)!important;background-size:cover!important;background-origin:content-box!important;background-repeat:no-repeat!important">
 </a>
 </div>
 <div class="card-body d-flex flex-column justify-content-between text-start">
 <h5 class="text-gradient fs-20 mb-2"> Plan a Creative Welcome Drink or Appetizers</h5>
 <div class="text-dark blog-desc mb-1">
 <p style=color:rgb(108,117,125);font-family:Poppins,Helvetica,sans-serif><span data-start=1589 data-end=1602 style=font-weight:bolder>Cocktails</span>: Signature drinks that match the theme of your event or venue can make a big impression. A refreshing cocktail like a spritz or a gin-based drink would be perfect for a light and breezy evening.<p style=color:rgb(108,117,125);font-family:Poppins,Helvetica,sans-serif><span data-start=1803 data-end=1817 style=font-weight:bolder>Appetizers</span>: Opt for easy-to-eat finger foods that allow people to mingle and snack without interrupting conversations. Think mini sliders, bruschetta, or fresh seafood.</p>
 </div>
 <div class="text-end ms-auto">
 <a href=https://tapifyworld.com/event-planner/blog/72 class="text-primary d-inline-block fs-14" tabindex=-1>
 Read More...
 </a>
 </div>
 </div>
 </div>
 </div></div></div><div class="slick-slide slick-current slick-active slick-center" data-slick-index=0 aria-hidden=false role=tabpanel id=slick-slide30 style=width:580px><div><div style=width:100%;display:inline-block>
 <div class=blog-card>
 <div class=card-img>
 <a href=https://tapifyworld.com/event-planner/blog/69 tabindex=0>
 <img src='data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==' alt=profile class="w-100 h-100 object-fit-cover mx-auto" loading=lazy style="background-blend-mode:normal!important;background-clip:content-box!important;background-position:50% 50%!important;background-color:rgba(0,0,0,0)!important;background-image:var(--sf-img-57)!important;background-size:cover!important;background-origin:content-box!important;background-repeat:no-repeat!important">
 </a>
 </div>
 <div class="card-body d-flex flex-column justify-content-between text-start">
 <h5 class="text-gradient fs-20 mb-2"> Create a Flow for the Evening</h5>
 <div class="text-dark blog-desc mb-1">
 <p><span style=color:rgb(108,117,125);font-family:Poppins,Helvetica,sans-serif>Planning how your event will unfold from 5 PM to 8 PM is key. Typically, the first hour is reserved for guests to arrive, mingle, and enjoy drinks and appetizers. Between 6 PM and 7 PM, you can transition to your main program, whether it's speeches, entertainment, or activities.</span></p>
 </div>
 <div class="text-end ms-auto">
 <a href=https://tapifyworld.com/event-planner/blog/69 class="text-primary d-inline-block fs-14" tabindex=0>
 Read More...
 </a>
 </div>
 </div>
 </div>
 </div></div></div><div class=slick-slide data-slick-index=1 aria-hidden=true tabindex=-1 role=tabpanel id=slick-slide31 style=width:580px><div><div style=width:100%;display:inline-block>
 <div class=blog-card>
 <div class=card-img>
 <a href=https://tapifyworld.com/event-planner/blog/70 tabindex=-1>
 <img src='data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==' alt=profile class="w-100 h-100 object-fit-cover mx-auto" loading=lazy style="background-blend-mode:normal!important;background-clip:content-box!important;background-position:50% 50%!important;background-color:rgba(0,0,0,0)!important;background-image:var(--sf-img-58)!important;background-size:cover!important;background-origin:content-box!important;background-repeat:no-repeat!important">
 </a>
 </div>
 <div class="card-body d-flex flex-column justify-content-between text-start">
 <h5 class="text-gradient fs-20 mb-2"> Consider Your Guests’ Transportation</h5>
 <div class="text-dark blog-desc mb-1">
 <p><span style=color:rgb(108,117,125);font-family:Poppins,Helvetica,sans-serif>Because your event ends at 8 PM, some guests might be concerned about how they’ll get home. If you’re expecting a large number of people who might not be driving, consider offering transportation options, such as arranging for rideshares or a shuttle service.</span></p>
 </div>
 <div class="text-end ms-auto">
 <a href=https://tapifyworld.com/event-planner/blog/70 class="text-primary d-inline-block fs-14" tabindex=-1>
 Read More...
 </a>
 </div>
 </div>
 </div>
 </div></div></div><div class=slick-slide data-slick-index=2 aria-hidden=true tabindex=-1 role=tabpanel id=slick-slide32 style=width:580px><div><div style=width:100%;display:inline-block>
 <div class=blog-card>
 <div class=card-img>
 <a href=https://tapifyworld.com/event-planner/blog/71 tabindex=-1>
 <img src='data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==' alt=profile class="w-100 h-100 object-fit-cover mx-auto" loading=lazy style="background-blend-mode:normal!important;background-clip:content-box!important;background-position:50% 50%!important;background-color:rgba(0,0,0,0)!important;background-image:var(--sf-img-55)!important;background-size:cover!important;background-origin:content-box!important;background-repeat:no-repeat!important">
 </a>
 </div>
 <div class="card-body d-flex flex-column justify-content-between text-start">
 <h5 class="text-gradient fs-20 mb-2"> Serving Dinner or Light Fare</h5>
 <div class="text-dark blog-desc mb-1">
 <p><span style=color:rgb(108,117,125);font-family:Poppins,Helvetica,sans-serif>Depending on your event’s nature, you’ll want to serve either a full dinner or light fare. Since the event is from 5 PM to 8 PM, it’s often best to serve lighter options unless the event is a formal dinner.</span></p>
 </div>
 <div class="text-end ms-auto">
 <a href=https://tapifyworld.com/event-planner/blog/71 class="text-primary d-inline-block fs-14" tabindex=-1>
 Read More...
 </a>
 </div>
 </div>
 </div>
 </div></div></div><div class=slick-slide data-slick-index=3 aria-hidden=true tabindex=-1 role=tabpanel id=slick-slide33 style=width:580px><div><div style=width:100%;display:inline-block>
 <div class=blog-card>
 <div class=card-img>
 <a href=https://tapifyworld.com/event-planner/blog/72 tabindex=-1>
 <img src='data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==' alt=profile class="w-100 h-100 object-fit-cover mx-auto" loading=lazy style="background-blend-mode:normal!important;background-clip:content-box!important;background-position:50% 50%!important;background-color:rgba(0,0,0,0)!important;background-image:var(--sf-img-56)!important;background-size:cover!important;background-origin:content-box!important;background-repeat:no-repeat!important">
 </a>
 </div>
 <div class="card-body d-flex flex-column justify-content-between text-start">
 <h5 class="text-gradient fs-20 mb-2"> Plan a Creative Welcome Drink or Appetizers</h5>
 <div class="text-dark blog-desc mb-1">
 <p style=color:rgb(108,117,125);font-family:Poppins,Helvetica,sans-serif><span data-start=1589 data-end=1602 style=font-weight:bolder>Cocktails</span>: Signature drinks that match the theme of your event or venue can make a big impression. A refreshing cocktail like a spritz or a gin-based drink would be perfect for a light and breezy evening.<p style=color:rgb(108,117,125);font-family:Poppins,Helvetica,sans-serif><span data-start=1803 data-end=1817 style=font-weight:bolder>Appetizers</span>: Opt for easy-to-eat finger foods that allow people to mingle and snack without interrupting conversations. Think mini sliders, bruschetta, or fresh seafood.</p>
 </div>
 <div class="text-end ms-auto">
 <a href=https://tapifyworld.com/event-planner/blog/72 class="text-primary d-inline-block fs-14" tabindex=-1>
 Read More...
 </a>
 </div>
 </div>
 </div>
 </div></div></div><div class="slick-slide slick-cloned" data-slick-index=4 aria-hidden=true tabindex=-1 style=width:580px><div><div style=width:100%;display:inline-block>
 <div class=blog-card>
 <div class=card-img>
 <a href=https://tapifyworld.com/event-planner/blog/69 tabindex=-1>
 <img src='data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==' alt=profile class="w-100 h-100 object-fit-cover mx-auto" loading=lazy style="background-blend-mode:normal!important;background-clip:content-box!important;background-position:50% 50%!important;background-color:rgba(0,0,0,0)!important;background-image:var(--sf-img-57)!important;background-size:cover!important;background-origin:content-box!important;background-repeat:no-repeat!important">
 </a>
 </div>
 <div class="card-body d-flex flex-column justify-content-between text-start">
 <h5 class="text-gradient fs-20 mb-2"> Create a Flow for the Evening</h5>
 <div class="text-dark blog-desc mb-1">
 <p><span style=color:rgb(108,117,125);font-family:Poppins,Helvetica,sans-serif>Planning how your event will unfold from 5 PM to 8 PM is key. Typically, the first hour is reserved for guests to arrive, mingle, and enjoy drinks and appetizers. Between 6 PM and 7 PM, you can transition to your main program, whether it's speeches, entertainment, or activities.</span></p>
 </div>
 <div class="text-end ms-auto">
 <a href=https://tapifyworld.com/event-planner/blog/69 class="text-primary d-inline-block fs-14" tabindex=-1>
 Read More...
 </a>
 </div>
 </div>
 </div>
 </div></div></div><div class="slick-slide slick-cloned" data-slick-index=5 aria-hidden=true tabindex=-1 style=width:580px><div><div style=width:100%;display:inline-block>
 <div class=blog-card>
 <div class=card-img>
 <a href=https://tapifyworld.com/event-planner/blog/70 tabindex=-1>
 <img src='data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==' alt=profile class="w-100 h-100 object-fit-cover mx-auto" loading=lazy style="background-blend-mode:normal!important;background-clip:content-box!important;background-position:50% 50%!important;background-color:rgba(0,0,0,0)!important;background-image:var(--sf-img-58)!important;background-size:cover!important;background-origin:content-box!important;background-repeat:no-repeat!important">
 </a>
 </div>
 <div class="card-body d-flex flex-column justify-content-between text-start">
 <h5 class="text-gradient fs-20 mb-2"> Consider Your Guests’ Transportation</h5>
 <div class="text-dark blog-desc mb-1">
 <p><span style=color:rgb(108,117,125);font-family:Poppins,Helvetica,sans-serif>Because your event ends at 8 PM, some guests might be concerned about how they’ll get home. If you’re expecting a large number of people who might not be driving, consider offering transportation options, such as arranging for rideshares or a shuttle service.</span></p>
 </div>
 <div class="text-end ms-auto">
 <a href=https://tapifyworld.com/event-planner/blog/70 class="text-primary d-inline-block fs-14" tabindex=-1>
 Read More...
 </a>
 </div>
 </div>
 </div>
 </div></div></div><div class="slick-slide slick-cloned slick-center" data-slick-index=6 aria-hidden=true tabindex=-1 style=width:580px><div><div style=width:100%;display:inline-block>
 <div class=blog-card>
 <div class=card-img>
 <a href=https://tapifyworld.com/event-planner/blog/71 tabindex=-1>
 <img src='data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==' alt=profile class="w-100 h-100 object-fit-cover mx-auto" loading=lazy style="background-blend-mode:normal!important;background-clip:content-box!important;background-position:50% 50%!important;background-color:rgba(0,0,0,0)!important;background-image:var(--sf-img-55)!important;background-size:cover!important;background-origin:content-box!important;background-repeat:no-repeat!important">
 </a>
 </div>
 <div class="card-body d-flex flex-column justify-content-between text-start">
 <h5 class="text-gradient fs-20 mb-2"> Serving Dinner or Light Fare</h5>
 <div class="text-dark blog-desc mb-1">
 <p><span style=color:rgb(108,117,125);font-family:Poppins,Helvetica,sans-serif>Depending on your event’s nature, you’ll want to serve either a full dinner or light fare. Since the event is from 5 PM to 8 PM, it’s often best to serve lighter options unless the event is a formal dinner.</span></p>
 </div>
 <div class="text-end ms-auto">
 <a href=https://tapifyworld.com/event-planner/blog/71 class="text-primary d-inline-block fs-14" tabindex=-1>
 Read More...
 </a>
 </div>
 </div>
 </div>
 </div></div></div><div class="slick-slide slick-cloned" data-slick-index=7 aria-hidden=true tabindex=-1 style=width:580px><div><div style=width:100%;display:inline-block>
 <div class=blog-card>
 <div class=card-img>
 <a href=https://tapifyworld.com/event-planner/blog/72 tabindex=-1>
 <img src='data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==' alt=profile class="w-100 h-100 object-fit-cover mx-auto" loading=lazy style="background-blend-mode:normal!important;background-clip:content-box!important;background-position:50% 50%!important;background-color:rgba(0,0,0,0)!important;background-image:var(--sf-img-56)!important;background-size:cover!important;background-origin:content-box!important;background-repeat:no-repeat!important">
 </a>
 </div>
 <div class="card-body d-flex flex-column justify-content-between text-start">
 <h5 class="text-gradient fs-20 mb-2"> Plan a Creative Welcome Drink or Appetizers</h5>
 <div class="text-dark blog-desc mb-1">
 <p style=color:rgb(108,117,125);font-family:Poppins,Helvetica,sans-serif><span data-start=1589 data-end=1602 style=font-weight:bolder>Cocktails</span>: Signature drinks that match the theme of your event or venue can make a big impression. A refreshing cocktail like a spritz or a gin-based drink would be perfect for a light and breezy evening.<p style=color:rgb(108,117,125);font-family:Poppins,Helvetica,sans-serif><span data-start=1803 data-end=1817 style=font-weight:bolder>Appetizers</span>: Opt for easy-to-eat finger foods that allow people to mingle and snack without interrupting conversations. Think mini sliders, bruschetta, or fresh seafood.</p>
 </div>
 <div class="text-end ms-auto">
 <a href=https://tapifyworld.com/event-planner/blog/72 class="text-primary d-inline-block fs-14" tabindex=-1>
 Read More...
 </a>
 </div>
 </div>
 </div>
 </div></div></div></div></div><ul class=slick-dots role=tablist><li class=slick-active role=presentation><button type=button role=tab id=slick-slide-control30 aria-controls=slick-slide30 aria-label="1 of 4" tabindex=0 aria-selected=true fdprocessedid=ui35h>1</button><li role=presentation><button type=button role=tab id=slick-slide-control31 aria-controls=slick-slide31 aria-label="2 of 4" tabindex=-1>2</button><li role=presentation><button type=button role=tab id=slick-slide-control32 aria-controls=slick-slide32 aria-label="3 of 4" tabindex=-1>3</button><li role=presentation><button type=button role=tab id=slick-slide-control33 aria-controls=slick-slide33 aria-label="4 of 4" tabindex=-1>4</button></ul></div>
 </div>
 
 <div class="bussiness-hour-section main-section px-30 pt-30 pb-30 position-relative">
 <div class="position-absolute vector-all vector-11">
 <img src=/images/templates/eventmanagementx/eve-060.webp alt=vector-img class=w-100>
 </div>
 <div class=section-heading>
 <h2 class="text-white mb-0">Business Hours
 </h2>
 </div>
 <div class=position-relative>
 <div class="row bussiness-hour-card row-gap-15px justify-content-center">
 <div class=col-md-6>
 <div class="d-flex align-items-center justify-content-center flex-wrap flex-sm-nowrap gap-3 business-box">
 <div class="time-icons text-primary">
 <svg xmlns=http://www.w3.org/2000/svg width=24 height=24 viewBox="0 0 24 24" fill=none stroke=currentColor stroke-width=2 stroke-linecap=round stroke-linejoin=round>
 <path d="M10.5 21h-4.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v3">
 </path>
 <path d="M16 3v4"></path>
 <path d="M8 3v4"></path>
 <path d="M4 11h10"></path>
 <path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
 <path d="M18 16.5v1.5l.5 .5"></path>
 </svg>
 </div>
 <div class="d-flex mt-3 justify-content-center gap-2">
 <div class="fs-14 fw-5 text-black">
 Monday :
 </div>
 <div class="d-flex gap-3 align-items-center justify-content-end fs-14 text-black">
 <div>
 12:00 AM - 12:00 AM
 </div>
 </div>
 </div>
 </div>
 </div>
 <div class=col-md-6>
 <div class="d-flex align-items-center justify-content-center flex-wrap flex-sm-nowrap gap-3 business-box">
 <div class="time-icons text-primary">
 <svg xmlns=http://www.w3.org/2000/svg width=24 height=24 viewBox="0 0 24 24" fill=none stroke=currentColor stroke-width=2 stroke-linecap=round stroke-linejoin=round>
 <path d="M10.5 21h-4.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v3">
 </path>
 <path d="M16 3v4"></path>
 <path d="M8 3v4"></path>
 <path d="M4 11h10"></path>
 <path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
 <path d="M18 16.5v1.5l.5 .5"></path>
 </svg>
 </div>
 <div class="d-flex mt-3 justify-content-center gap-2">
 <div class="fs-14 fw-5 text-black">
 Tuesday :
 </div>
 <div class="d-flex gap-3 align-items-center justify-content-end fs-14 text-black">
 <div>
 12:00 AM - 12:00 AM
 </div>
 </div>
 </div>
 </div>
 </div>
 <div class=col-md-6>
 <div class="d-flex align-items-center justify-content-center flex-wrap flex-sm-nowrap gap-3 business-box">
 <div class="time-icons text-primary">
 <svg xmlns=http://www.w3.org/2000/svg width=24 height=24 viewBox="0 0 24 24" fill=none stroke=currentColor stroke-width=2 stroke-linecap=round stroke-linejoin=round>
 <path d="M10.5 21h-4.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v3">
 </path>
 <path d="M16 3v4"></path>
 <path d="M8 3v4"></path>
 <path d="M4 11h10"></path>
 <path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
 <path d="M18 16.5v1.5l.5 .5"></path>
 </svg>
 </div>
 <div class="d-flex mt-3 justify-content-center gap-2">
 <div class="fs-14 fw-5 text-black">
 Wednesday :
 </div>
 <div class="d-flex gap-3 align-items-center justify-content-end fs-14 text-black">
 <div>
 12:00 AM - 12:00 AM
 </div>
 </div>
 </div>
 </div>
 </div>
 <div class=col-md-6>
 <div class="d-flex align-items-center justify-content-center flex-wrap flex-sm-nowrap gap-3 business-box">
 <div class="time-icons text-primary">
 <svg xmlns=http://www.w3.org/2000/svg width=24 height=24 viewBox="0 0 24 24" fill=none stroke=currentColor stroke-width=2 stroke-linecap=round stroke-linejoin=round>
 <path d="M10.5 21h-4.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v3">
 </path>
 <path d="M16 3v4"></path>
 <path d="M8 3v4"></path>
 <path d="M4 11h10"></path>
 <path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
 <path d="M18 16.5v1.5l.5 .5"></path>
 </svg>
 </div>
 <div class="d-flex mt-3 justify-content-center gap-2">
 <div class="fs-14 fw-5 text-black">
 Thursday :
 </div>
 <div class="d-flex gap-3 align-items-center justify-content-end fs-14 text-black">
 <div>
 12:00 AM - 12:00 AM
 </div>
 </div>
 </div>
 </div>
 </div>
 <div class=col-md-6>
 <div class="d-flex align-items-center justify-content-center flex-wrap flex-sm-nowrap gap-3 business-box">
 <div class="time-icons text-primary">
 <svg xmlns=http://www.w3.org/2000/svg width=24 height=24 viewBox="0 0 24 24" fill=none stroke=currentColor stroke-width=2 stroke-linecap=round stroke-linejoin=round>
 <path d="M10.5 21h-4.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v3">
 </path>
 <path d="M16 3v4"></path>
 <path d="M8 3v4"></path>
 <path d="M4 11h10"></path>
 <path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
 <path d="M18 16.5v1.5l.5 .5"></path>
 </svg>
 </div>
 <div class="d-flex mt-3 justify-content-center gap-2">
 <div class="fs-14 fw-5 text-black">
 Friday :
 </div>
 <div class="d-flex gap-3 align-items-center justify-content-end fs-14 text-black">
 <div>
 12:00 AM - 12:00 AM
 </div>
 </div>
 </div>
 </div>
 </div>
 <div class=col-md-6>
 <div class="d-flex align-items-center justify-content-center flex-wrap flex-sm-nowrap gap-3 business-box">
 <div class="time-icons text-primary">
 <svg xmlns=http://www.w3.org/2000/svg width=24 height=24 viewBox="0 0 24 24" fill=none stroke=currentColor stroke-width=2 stroke-linecap=round stroke-linejoin=round>
 <path d="M10.5 21h-4.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v3">
 </path>
 <path d="M16 3v4"></path>
 <path d="M8 3v4"></path>
 <path d="M4 11h10"></path>
 <path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
 <path d="M18 16.5v1.5l.5 .5"></path>
 </svg>
 </div>
 <div class="d-flex mt-3 justify-content-center gap-2">
 <div class="fs-14 fw-5 text-black">
 Saturday :
 </div>
 <div class="d-flex gap-3 align-items-center justify-content-end fs-14 text-black">
 <div>
 12:00 AM - 12:00 AM
 </div>
 </div>
 </div>
 </div>
 </div>
 <div class=col-md-6>
 <div class="d-flex align-items-center justify-content-center flex-wrap flex-sm-nowrap gap-3 business-box">
 <div class="time-icons text-primary">
 <svg xmlns=http://www.w3.org/2000/svg width=24 height=24 viewBox="0 0 24 24" fill=none stroke=currentColor stroke-width=2 stroke-linecap=round stroke-linejoin=round>
 <path d="M10.5 21h-4.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v3">
 </path>
 <path d="M16 3v4"></path>
 <path d="M8 3v4"></path>
 <path d="M4 11h10"></path>
 <path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
 <path d="M18 16.5v1.5l.5 .5"></path>
 </svg>
 </div>
 <div class="d-flex mt-3 justify-content-center gap-2">
 <div class="fs-14 fw-5 text-black">
 Sunday :
 </div>
 <div class="d-flex gap-3 align-items-center justify-content-end fs-14 text-black">
 <div>
 12:00 AM - 12:00 AM
 </div>
 </div>
 </div>
 </div>
 </div>
 
 </div>
 </div>
 </div>
 
 
 <div class="contact-us-section main-section px-30 pt-30 pb-30 position-relative">
 <div class="position-absolute vector-all vector-13">
 <img src=/images/templates/eventmanagementx/eve-061.webp alt=vector-img class=w-100>
 </div>
 <div class=section-heading>
 <h2 class="text-white mb-0">Inquiries</h2>
 </div>
 <div class="contact-form p-3">
 <form id="enquiryForm" onsubmit="tfSubmitInquiry(event)" enctype="multipart/form-data"><input type="hidden" name="vcard_id" value="<?= $vcardId ?>"><label class="w-100 mb-2" style="display:block;text-align:left"><span style="font-size:13px;opacity:.85">Attachment (optional)</span><input type="file" name="attachment" class="form-control" accept="image/*,.pdf" style="margin-top:4px"></label>
 <div class=row>
 <div id=enquiryError class="alert alert-danger d-none sf-hidden"></div>
 <div class=col-12>
 <input type=text name=name class=form-control placeholder="Your Name" fdprocessedid=9gyeb value>
 </div>
 <div class=col-12>
 <input type=tel name=phone class=form-control placeholder="Enter Phone Number" fdprocessedid=clid7oe value>
 </div>
 <div class=col-12>
 <input type=email name=email class=form-control placeholder="Email Address" fdprocessedid=2kpfw value>
 </div>
 <div class=col-12>
 <textarea class="form-control h-100" name=message placeholder="Type a message here..." rows=3></textarea>
 </div>
 <div class="mb-3 mt-3">
 <div class=wrapper-file-input>
 <div class=input-box id=fileInputTrigger>
 <h4> <svg class="svg-inline--fa fa-upload me-2" aria-hidden=true focusable=false data-prefix=fas data-icon=upload role=img xmlns=http://www.w3.org/2000/svg viewBox="0 0 512 512" data-fa-i2svg><path fill=currentColor d="M105.4 182.6c12.5 12.49 32.76 12.5 45.25 .001L224 109.3V352c0 17.67 14.33 32 32 32c17.67 0 32-14.33 32-32V109.3l73.38 73.38c12.49 12.49 32.75 12.49 45.25-.001c12.49-12.49 12.49-32.75 0-45.25l-128-128C272.4 3.125 264.2 0 256 0S239.6 3.125 233.4 9.375L105.4 137.4C92.88 149.9 92.88 170.1 105.4 182.6zM480 352h-160c0 35.35-28.65 64-64 64s-64-28.65-64-64H32c-17.67 0-32 14.33-32 32v96c0 17.67 14.33 32 32 32h448c17.67 0 32-14.33 32-32v-96C512 366.3 497.7 352 480 352zM432 456c-13.2 0-24-10.8-24-24c0-13.2 10.8-24 24-24s24 10.8 24 24C456 445.2 445.2 456 432 456z"></path></svg>Choose File to upload
 </h4> <input type=file id=attachment name=attachment hidden multiple value class=sf-hidden>
 </div> <small class=text-white>Files Supported: JPG, PNG, JPEG</small>
 </div>
 <div class=wrapper-file-section>
 <div class=selected-files id=selectedFilesSection style=display:none>
 
 
 </div>
 </div>
 </div>
 <div class="col-12 d-flex justify-content-center mt-4">
 <button class="contact-btn btn btn-primary" type=submit fdprocessedid=aqiof8>
 Send Message
 </button>
 </div>
 </div>
 </form>
 </div>
 </div>
 
 
 
 
 
 <div class="w-100 d-flex justify-content-center sticky-vcard-div">
 <div>
 <a href=https://tapifyworld.com/add-contact/36 class="btn btn-primary add-contact-btn d-flex justify-content-center ms-0 gap-2 align-items-center rounded px-5 text-decoration-none p-2 py-1"><svg class="svg-inline--fa fa-address-book" aria-hidden=true focusable=false data-prefix=fas data-icon=address-book role=img xmlns=http://www.w3.org/2000/svg viewBox="0 0 512 512" data-fa-i2svg><path fill=currentColor d="M384 0H96C60.65 0 32 28.65 32 64v384c0 35.35 28.65 64 64 64h288c35.35 0 64-28.65 64-64V64C448 28.65 419.3 0 384 0zM240 128c35.35 0 64 28.65 64 64s-28.65 64-64 64c-35.34 0-64-28.65-64-64S204.7 128 240 128zM336 384h-192C135.2 384 128 376.8 128 368C128 323.8 163.8 288 208 288h64c44.18 0 80 35.82 80 80C352 376.8 344.8 384 336 384zM496 64H480v96h16C504.8 160 512 152.8 512 144v-64C512 71.16 504.8 64 496 64zM496 192H480v96h16C504.8 288 512 280.8 512 272v-64C512 199.2 504.8 192 496 192zM496 320H480v96h16c8.836 0 16-7.164 16-16v-64C512 327.2 504.8 320 496 320z"></path></svg>
 &nbsp;Add to contact</a>
 </div>
 <div class="modal fade py-3 sf-hidden" id=askContactDetailFormModel tabindex=-1 aria-hidden=true aria-labelledby=askContactDetailFormModelLabel>
 
</div>
 </div>
 <div class="btn-section cursor-pointer">
 <div class=fixed-btn-section>
 <div class="bars-btn event-bars-btn">
 <svg xmlns=http://www.w3.org/2000/svg width=25 height=25 viewBox="0 0 25 25" fill=none>
 <path d="M15.413 0.540771H22.4892C23.5719 0.540851 24.4597 1.42874 24.4599 2.51147V9.58765C24.4598 10.6777 23.5731 11.5583 22.4892 11.5583H15.413C14.322 11.5581 13.4424 10.6786 13.4423 9.58765V2.51147C13.4425 1.42774 14.3232 0.540993 15.413 0.540771Z" stroke=#ffffff></path>
 <path d="M2.97168 0.5H8.74609C10.113 0.500108 11.2178 1.61252 11.2178 2.97168V8.74609C11.2177 10.114 10.114 11.2177 8.74609 11.2178H2.97168C1.61252 11.2178 0.500108 10.113 0.5 8.74609V2.97168L0.512695 2.71973C0.631537 1.56119 1.56119 0.631537 2.71973 0.512695L2.97168 0.5Z" stroke=#ffffff></path>
 <path d="M2.97168 13.783H8.74609C10.114 13.7831 11.2178 14.8867 11.2178 16.2546V22.0291C11.2177 23.3881 10.1129 24.5006 8.74609 24.5007H2.97168C1.6136 24.5007 0.500109 23.3871 0.5 22.0291V16.2546L0.512695 16.0017C0.631233 14.837 1.56023 13.9136 2.71973 13.7957L2.97168 13.783Z" stroke=#ffffff></path>
 <path d="M16.254 13.783H22.0284C23.3875 13.7831 24.5 14.8877 24.5 16.2546V22.0291C24.4999 23.387 23.3863 24.5006 22.0284 24.5007H16.254C14.8871 24.5007 13.7824 23.3882 13.7823 22.0291V16.2546L13.795 16.0007C13.913 14.8359 14.8352 13.9136 16 13.7957L16.254 13.783Z" stroke=#ffffff></path>
 </svg>
 </div>
 <div class="sub-btn d-none sf-hidden">
 
 </div>
 </div>
 </div>
 <div class="d-flex justify-content-evenly main-section pt-3 pb-3 flex-wrap flex-sm-nowrap">
 <div class=text-center>
 <span class="text-gradient fw-5 fs-14">Made By
 Tapify</span>
 </div>
 </div>
 </div>
 </div>
 
 <div class="modal fade sf-hidden" id=newsLatterModal tabindex=-1 aria-labelledby=newsLatterModalLabel aria-hidden=true>
 
 </div>
 
 <div id=vcard14-shareModel class="modal fade sf-hidden" role=dialog>
 
 </div>
</div>
<div class=razorpay-container style=z-index:2147483647;position:fixed;top:0px;display:none;left:0px;height:100%;width:100%;max-height:100dvh;backface-visibility:hidden;overflow-y:visible><style>@keyframes rzp-rot{to{transform:rotate(360deg)}}@-webkit-keyframes rzp-rot{to{-webkit-transform:rotate(360deg)}}</style></div>
<div class="flatpickr-calendar animate open arrowBottom arrowLeft" tabindex=-1 style=top:3743.24px;left:556.778px;right:auto><div class=flatpickr-months><span class="flatpickr-prev-month flatpickr-disabled sf-hidden"><svg version=1.1 xmlns=http://www.w3.org/2000/svg xmlns:xlink=http://www.w3.org/1999/xlink viewBox="0 0 17 17"><g></g><path d="M5.207 8.471l7.146 7.147-0.707 0.707-7.853-7.854 7.854-7.853 0.707 0.707-7.147 7.146z"></path></svg></span><div class=flatpickr-month><div class=flatpickr-current-month><select class=flatpickr-monthDropdown-months aria-label=Month tabindex=-1><option class=flatpickr-monthDropdown-month value=6 tabindex=-1 selected>July<option class=flatpickr-monthDropdown-month value=7 tabindex=-1>August<option class=flatpickr-monthDropdown-month value=8 tabindex=-1>September<option class=flatpickr-monthDropdown-month value=9 tabindex=-1>October<option class=flatpickr-monthDropdown-month value=10 tabindex=-1>November<option class=flatpickr-monthDropdown-month value=11 tabindex=-1>December</select><div class=numInputWrapper><input class="numInput cur-year" type=number tabindex=-1 aria-label=Year min=2026 value=2026><span class=arrowUp></span><span class=arrowDown></span></div></div></div><span class=flatpickr-next-month><svg version=1.1 xmlns=http://www.w3.org/2000/svg xmlns:xlink=http://www.w3.org/1999/xlink viewBox="0 0 17 17"><g></g><path d="M13.207 8.472l-7.854 7.854-0.707-0.707 7.146-7.146-7.146-7.148 0.707-0.707 7.854 7.854z"></path></svg></span></div><div class=flatpickr-innerContainer><div class=flatpickr-rContainer><div class=flatpickr-weekdays><div class=flatpickr-weekdaycontainer>
 <span class=flatpickr-weekday>
 Sun</span><span class=flatpickr-weekday>Mon</span><span class=flatpickr-weekday>Tue</span><span class=flatpickr-weekday>Wed</span><span class=flatpickr-weekday>Thu</span><span class=flatpickr-weekday>Fri</span><span class=flatpickr-weekday>Sat
 </span>
 </div></div><div class=flatpickr-days tabindex=-1><div class=dayContainer><span class="flatpickr-day prevMonthDay flatpickr-disabled" aria-label="June 28, 2026">28</span><span class="flatpickr-day prevMonthDay flatpickr-disabled" aria-label="June 29, 2026">29</span><span class="flatpickr-day prevMonthDay flatpickr-disabled" aria-label="June 30, 2026">30</span><span class="flatpickr-day flatpickr-disabled" aria-label="July 1, 2026">1</span><span class="flatpickr-day flatpickr-disabled" aria-label="July 2, 2026">2</span><span class="flatpickr-day flatpickr-disabled" aria-label="July 3, 2026">3</span><span class="flatpickr-day flatpickr-disabled" aria-label="July 4, 2026">4</span><span class="flatpickr-day flatpickr-disabled" aria-label="July 5, 2026">5</span><span class="flatpickr-day today" aria-label="July 6, 2026" aria-current=date tabindex=-1>6</span><span class=flatpickr-day aria-label="July 7, 2026" tabindex=-1>7</span><span class=flatpickr-day aria-label="July 8, 2026" tabindex=-1>8</span><span class=flatpickr-day aria-label="July 9, 2026" tabindex=-1>9</span><span class=flatpickr-day aria-label="July 10, 2026" tabindex=-1>10</span><span class=flatpickr-day aria-label="July 11, 2026" tabindex=-1>11</span><span class=flatpickr-day aria-label="July 12, 2026" tabindex=-1>12</span><span class=flatpickr-day aria-label="July 13, 2026" tabindex=-1>13</span><span class=flatpickr-day aria-label="July 14, 2026" tabindex=-1>14</span><span class=flatpickr-day aria-label="July 15, 2026" tabindex=-1>15</span><span class=flatpickr-day aria-label="July 16, 2026" tabindex=-1>16</span><span class=flatpickr-day aria-label="July 17, 2026" tabindex=-1>17</span><span class=flatpickr-day aria-label="July 18, 2026" tabindex=-1>18</span><span class=flatpickr-day aria-label="July 19, 2026" tabindex=-1>19</span><span class=flatpickr-day aria-label="July 20, 2026" tabindex=-1>20</span><span class=flatpickr-day aria-label="July 21, 2026" tabindex=-1>21</span><span class=flatpickr-day aria-label="July 22, 2026" tabindex=-1>22</span><span class=flatpickr-day aria-label="July 23, 2026" tabindex=-1>23</span><span class=flatpickr-day aria-label="July 24, 2026" tabindex=-1>24</span><span class=flatpickr-day aria-label="July 25, 2026" tabindex=-1>25</span><span class=flatpickr-day aria-label="July 26, 2026" tabindex=-1>26</span><span class=flatpickr-day aria-label="July 27, 2026" tabindex=-1>27</span><span class=flatpickr-day aria-label="July 28, 2026" tabindex=-1>28</span><span class=flatpickr-day aria-label="July 29, 2026" tabindex=-1>29</span><span class=flatpickr-day aria-label="July 30, 2026" tabindex=-1>30</span><span class=flatpickr-day aria-label="July 31, 2026" tabindex=-1>31</span><span class="flatpickr-day nextMonthDay" aria-label="August 1, 2026" tabindex=-1>1</span><span class="flatpickr-day nextMonthDay" aria-label="August 2, 2026" tabindex=-1>2</span><span class="flatpickr-day nextMonthDay" aria-label="August 3, 2026" tabindex=-1>3</span><span class="flatpickr-day nextMonthDay" aria-label="August 4, 2026" tabindex=-1>4</span><span class="flatpickr-day nextMonthDay" aria-label="August 5, 2026" tabindex=-1>5</span><span class="flatpickr-day nextMonthDay" aria-label="August 6, 2026" tabindex=-1>6</span><span class="flatpickr-day nextMonthDay" aria-label="August 7, 2026" tabindex=-1>7</span><span class="flatpickr-day nextMonthDay" aria-label="August 8, 2026" tabindex=-1>8</span></div></div></div></div></div><div id=lightboxOverlay tabindex=-1 class=lightboxOverlay style=display:none></div><div id=lightbox tabindex=-1 class=lightbox style=display:none></div>"><script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script><script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"><script src="https://cdn.jsdelivr.net/npm/flatpickr"></script><script>function tfInit(){if(typeof jQuery==="undefined"||!jQuery.fn||!jQuery.fn.slick){return setTimeout(tfInit,120);}jQuery(function($){$(".product-slider,.gallery-slider,.testimonial-slider,.blog-slider").each(function(){if($(this).children().length===0){var s=$(this).closest("[class*=section]");if(s.length&&!/main|wrapper|content|page|body/i.test(s.attr("class")||"")&&s.find("[class*=section]").length===0){s.hide();}$(this).hide();}});$("[class*=instagram],[class*=insta-feed],[class*=insta-section],[class*=insta-feed-section]").each(function(){if($(this).find("img,iframe,.slick-slide,.insta-item,a[href*=instagram]").length===0){var s=$(this).closest("[class*=section]");if(s.length&&!/main|wrapper|content|page|body/i.test(s.attr("class")||"")&&s.find("[class*=section]").length===0){s.hide();}$(this).hide();}});$("[class*=__gallery],[class*=__product],[class*=__testimonial]").each(function(){var sl=$(this).find(".gallery-slider,.product-slider,.testimonial-slider").first();if(sl.length&&sl.children().length===0){$(this).hide();}});$("a").each(function(){var h=$(this).attr("href")||"";var tx=$(this).text().replace(/\s+/g,"");if((h==="mailto:"||h==="tel:")&&tx===""){$(this).closest(".contact-box,.contact-item,li,.col-sm-6,.col-md-6,.col-6,.col-12,.col").hide();}});$("[class*=contact-box],[class*=contact-item]").each(function(){if($(this).text().replace(/\s+/g,"")===""){$(this).hide();}});window.tfSubmitInquiry=async function(ev){ev.preventDefault();var f=ev.target;var b=f.querySelector("button[type=submit]");var fd=new FormData(f);if(b)b.disabled=true;try{var r=await fetch("/inquiry-submit.php",{method:"POST",body:fd});var j=await r.json();if(j.success){if(window.showToast)showToast("Message sent!","success");f.reset();}else{if(window.showToast)showToast(j.message||"Failed","error");}}catch(e){if(window.showToast)showToast("Connection error","error");}finally{if(b)b.disabled=false;}};function ini(s,o){var $s=$(s);if(!$s.length||$s.hasClass("slick-initialized"))return;$s.slick(o);}ini(".product-slider",{slidesToShow:2,arrows:false,dots:true,infinite:true,autoplay:true,autoplaySpeed:2500,responsive:[{breakpoint:576,settings:{slidesToShow:1}}]});ini(".gallery-slider",{slidesToShow:2,arrows:false,dots:true,infinite:true,autoplay:true,autoplaySpeed:2500,responsive:[{breakpoint:576,settings:{slidesToShow:1}}]});ini(".testimonial-slider",{slidesToShow:1,arrows:false,dots:true,infinite:true,autoplay:true,autoplaySpeed:4000});if(window.flatpickr){flatpickr("#pickUpDate",{minDate:"today",dateFormat:"Y-m-d"});flatpickr(".flatpickr-input",{minDate:"today",dateFormat:"Y-m-d"});}});}tfInit();</script><!--tf-datefix--><script>(function(){function ready(){var d=document.getElementById("pickUpDate");if(!d||d.dataset.tfDateBound)return;d.dataset.tfDateBound="1";if(window.flatpickr&&!d._flatpickr&&(d.classList.contains("flatpickr-input")||d.type==="text")){try{flatpickr(d,{minDate:"today",dateFormat:"Y-m-d"});}catch(e){}}if(!d._flatpickr&&d.type!=="date"){try{d.type="date";}catch(e){}d.removeAttribute("readonly");if(!d.getAttribute("min")){d.min=new Date().toISOString().slice(0,10);}}var open=function(){if(d._flatpickr){d._flatpickr.open();return;}try{d.showPicker();}catch(e){d.focus();}};d.addEventListener("click",open);var icon=document.querySelector(".calendar-icon");if(icon){icon.style.cursor="pointer";icon.style.pointerEvents="auto";icon.addEventListener("click",function(e){e.preventDefault();open();});}}if(document.readyState!=="loading")ready();else document.addEventListener("DOMContentLoaded",ready);setTimeout(ready,600);setTimeout(ready,1600);})();</script><?php if(!empty($vcard["custom_js"])): ?><script><?= $vcard["custom_js"] ?></script><?php endif; ?><script>
(function(){
  var dateEl=document.getElementById('pickUpDate'),slotWrap=document.getElementById('slotData');
  var btn=document.querySelector('.appointmentAdd');
  if(!dateEl||!slotWrap)return;
  var chosen={date:'',time:'',label:''};
  function esc(s){var d=document.createElement('div');d.textContent=s;return d.innerHTML;}
  function loadSlots(date){
    chosen.date=date;chosen.time='';
    if(btn){btn.classList.add('d-none');}
    slotWrap.innerHTML='<div class="col-12 text-center py-2" style="color:#40b5c5">Loading slots\u2026</div>';
    fetch('/api/appointments/slots_public.php?vcard_id=<?= $vcardId ?>&date='+encodeURIComponent(date))
      .then(function(r){return r.json();})
      .then(function(res){
        slotWrap.innerHTML='';
        var slots=(res&&res.success&&res.data)?res.data:[];
        if(!slots.length){slotWrap.innerHTML='<div class="col-12 text-center py-2" style="color:#6b7280">No slots available for this date.</div>';return;}
        slots.forEach(function(s){
          var v=(s&&s.value)||s,l=(s&&s.label)||s;
          var c=document.createElement('div');c.className='col-6 col-sm-4 mt-2';
          var b=document.createElement('button');b.type='button';b.className='btn tf-slot w-100';b.textContent=l;
          b.onclick=function(){
            slotWrap.querySelectorAll('.tf-slot').forEach(function(x){x.classList.remove('active');});
            b.classList.add('active');chosen.time=v;chosen.label=l;
            if(btn){btn.classList.remove('d-none');btn.classList.remove('sf-hidden');}
          };
          c.appendChild(b);slotWrap.appendChild(c);
        });
      })
      .catch(function(){slotWrap.innerHTML='<div class="col-12 text-center py-2" style="color:#6b7280">Could not load slots.</div>';});
  }
  
  dateEl.addEventListener('change',function(){if(dateEl.value)loadSlots(dateEl.value);});

  // Minimal booking dialog (template-styled); posts to the existing endpoint.
  var overlay=null;
  function openBookForm(){
    if(overlay){overlay.remove();}
    overlay=document.createElement('div');
    overlay.style.cssText='position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:99999;display:flex;align-items:center;justify-content:center;padding:16px';
    overlay.innerHTML='<div style="background:linear-gradient(0deg,#f9fcfc,#c2e2e6);border:2px solid #40b5c5;border-radius:0 30px 0 30px;max-width:380px;width:100%;padding:24px" onclick="event.stopPropagation()">'
      +'<h5 style="color:#144660;font-weight:600;margin-bottom:4px">Make an Appointment</h5>'
      +'<p style="font-size:13px;color:#6b7280;margin-bottom:14px">'+esc(chosen.date)+' \u00b7 '+esc(chosen.label)+'</p>'
      +'<input id="tfApName" class="form-control" placeholder="Your Name *" style="margin-bottom:10px">'
      +'<input id="tfApPhone" class="form-control" type="tel" placeholder="Phone Number *" style="margin-bottom:10px">'
      +'<input id="tfApEmail" class="form-control" type="email" placeholder="Email (optional)" style="margin-bottom:14px">'
      +'<div style="display:flex;gap:10px;justify-content:flex-end">'
      +'<button type="button" id="tfApCancel" class="btn" style="border:1px solid #40b5c5;color:#40b5c5;border-radius:0 14px 0 14px">Cancel</button>'
      +'<button type="button" id="tfApGo" class="btn" style="background:#40b5c5;color:#fff;border-radius:0 14px 0 14px">Book Now</button>'
      +'</div></div>';
    overlay.addEventListener('click',function(){overlay.remove();overlay=null;});
    document.body.appendChild(overlay);
    document.getElementById('tfApCancel').onclick=function(){overlay.remove();overlay=null;};
    document.getElementById('tfApGo').onclick=function(){
      var name=document.getElementById('tfApName').value.trim();
      var phone=document.getElementById('tfApPhone').value.trim();
      var email=document.getElementById('tfApEmail').value.trim();
      if(!name||!phone){if(typeof showToast==='function')showToast('Name and phone are required','error');return;}
      var go=document.getElementById('tfApGo');go.disabled=true;go.textContent='Booking\u2026';
      fetch('/appointment-submit.php',{method:'POST',headers:{'Content-Type':'application/json'},
        body:JSON.stringify({vcard_id:<?= $vcardId ?>,name:name,phone:phone,email:email,date:chosen.date,time:chosen.time})})
        .then(function(r){return r.json();})
        .then(function(res){
          if(res.success){if(typeof showToast==='function')showToast('\u2713 Appointment booked! We will confirm shortly.','success');overlay.remove();overlay=null;}
          else{if(typeof showToast==='function')showToast(res.message||'Failed to book','error');go.disabled=false;go.textContent='Book Now';}
        })
        .catch(function(){if(typeof showToast==='function')showToast('Connection error','error');go.disabled=false;go.textContent='Book Now';});
    };
  }
  if(btn){btn.addEventListener('click',openBookForm);}
})();
</script><?php include __DIR__ . "/_shared-scripts.php"; ?><style>/*tf-fixups*/.our-services-section{background:transparent!important}.business-hour-section input,.business-hour-section .flatpickr-input{display:none!important}.flatpickr-calendar:not(.open){display:none!important}</style></body></html>