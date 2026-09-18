<?php
/**
 * TAPIFY - Tap-tracking snippet for public pages.
 *
 * One delegated click listener, injected into every rendered card, site and
 * store. It reports the taps the server cannot see — Call, WhatsApp, Email,
 * Save Contact, Directions, Share, social icons, outside links — to
 * api/public/tap.php.
 *
 * Injected rather than added to the 127 card templates so a template never has
 * to know about tracking, and a new template is covered the day it is added.
 * It is deliberately tiny, dependency-free and wrapped in try/catch: a card
 * must render and work exactly the same if any of this fails.
 */
require_once __DIR__ . '/Engagement.php';

/**
 * @param string $type card|site|store
 * @param int    $assetId
 */
function tapify_tap_snippet(string $type, int $assetId): string
{
    if ($assetId <= 0 || !in_array($type, ['card', 'site', 'store'], true)) {
        return '';
    }
    $ctx = json_encode(
        ['t' => $type, 'id' => $assetId, 's' => Engagement::source()],
        JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
    );

    return <<<HTML
<script>/* tapify engagement */
(function(){try{
  var C=$ctx, ENDPOINT='/api/public/tap.php', last={};
  var SOCIAL=['instagram','facebook','linkedin','twitter','x.com','youtube','telegram','pinterest','snapchat','threads','tiktok','github','behance','dribbble'];
  function send(e,l){
    try{
      if(last[e] && Date.now()-last[e]<2000) return;
      last[e]=Date.now();
      var body=JSON.stringify({t:C.t,id:C.id,e:e,s:C.s,l:l||''});
      if(navigator.sendBeacon){
        navigator.sendBeacon(ENDPOINT,new Blob([body],{type:'text/plain;charset=UTF-8'}));
      }else{
        fetch(ENDPOINT,{method:'POST',body:body,keepalive:true});
      }
    }catch(_){}
  }
  function fromLink(href){
    var h=href.toLowerCase();
    if(h.indexOf('tel:')===0) return ['tap_call',''];
    if(h.indexOf('mailto:')===0) return ['tap_email',''];
    if(h.indexOf('sms:')===0) return ['tap_call','sms'];
    if(h.indexOf('wa.me')>-1||h.indexOf('whatsapp')>-1) return ['tap_whatsapp',''];
    if(h.indexOf('maps')>-1&&(h.indexOf('google')>-1||h.indexOf('goo.gl')>-1)) return ['tap_directions',''];
    if(h.indexOf('.vcf')>-1||h.indexOf('save-contact')>-1) return ['tap_save_contact',''];
    for(var i=0;i<SOCIAL.length;i++){ if(h.indexOf(SOCIAL[i])>-1) return ['tap_social',SOCIAL[i]]; }
    if(h.indexOf('http')===0){
      try{
        var host=new URL(href).hostname;
        if(host && host!==location.hostname) return ['tap_link',host];
      }catch(_){}
    }
    return null;
  }
  function fromHandler(el){
    var s=((el.getAttribute('onclick')||'')+' '+(el.getAttribute('class')||'')).toLowerCase();
    if(s.indexOf('savecontact')>-1||s.indexOf('save-btn')>-1||s.indexOf('addcontact')>-1) return ['tap_save_contact',''];
    if(s.indexOf('sharecard')>-1||s.indexOf('share(')>-1) return ['tap_share',''];
    if(s.indexOf('downloadqr')>-1||s.indexOf('download_qr')>-1) return ['tap_qr_download',''];
    if(s.indexOf('bookappointment')>-1||s.indexOf('openbooking')>-1) return ['tap_book',''];
    if(s.indexOf('whatsapp')>-1) return ['tap_whatsapp',''];
    if(s.indexOf('checkout')>-1||s.indexOf('placeorder')>-1) return ['tap_order',''];
    return null;
  }
  document.addEventListener('click',function(ev){
    try{
      var t=ev.target;
      if(!t||!t.closest) return;
      var a=t.closest('a[href]'), hit=null;
      if(a){ hit=fromLink(a.getAttribute('href')||''); }
      if(!hit){
        var el=t.closest('[onclick],[data-tap],button,.save-btn');
        if(el){
          var tap=(el.getAttribute('data-tap')||'').replace(/^tap_/,'');
          hit=tap?['tap_'+tap,'']:fromHandler(el);
        }
      }
      if(hit) send(hit[0],hit[1]);
    }catch(_){}
  },true);
}catch(_){}})();</script>
HTML;
}
