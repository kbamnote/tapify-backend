<?php
/**
 * SHARED SCRIPTS - Included at end of every template
 * Provides: Save Contact (.vcf), Share, Inquiry submit, Toast, and Floating Action Button (FAB)
 */
?>

<!-- FAB (Floating Action Button) -->
<div class="fab-container">
    <div class="fab-options">
        <a href="javascript:void(0)" onclick="openWhatsAppModal()" class="fab-option fab-wa" title="Share via WhatsApp">
            <i class="fab fa-whatsapp"></i>
        </a>
        <a href="javascript:shareCard()" class="fab-option fab-share" title="Share">
            <i class="fas fa-share-alt"></i>
        </a>
    </div>
    <button class="fab-main" onclick="toggleFab()">
        <i class="fas fa-plus"></i>
    </button>
</div>

<!-- WhatsApp Share Modal -->
<div id="waModal" class="wa-modal">
    <div class="wa-modal-content">
        <span class="wa-close" onclick="closeWhatsAppModal()">&times;</span>
        <h3>Share via WhatsApp</h3>
        <p>Enter WhatsApp number with country code (e.g., 919876543210) to share your profile link.</p>
        <input type="tel" id="waNumber" placeholder="919876543210" class="wa-input">
        <button onclick="sendWhatsApp()" class="wa-btn"><i class="fab fa-whatsapp"></i> Send on WhatsApp</button>
    </div>
</div>

<style>
/* FAB Styles */
.fab-container { position: fixed; bottom: 25px; right: 25px; z-index: 9999; display: flex; flex-direction: column; align-items: center; }
.fab-options { display: flex; flex-direction: column; gap: 12px; margin-bottom: 15px; opacity: 0; visibility: hidden; transform: translateY(20px); transition: all 0.3s ease; }
.fab-container.active .fab-options { opacity: 1; visibility: visible; transform: translateY(0); }
.fab-option { width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px; text-decoration: none; box-shadow: 0 4px 10px rgba(0,0,0,0.2); transition: transform 0.2s; }
.fab-option:hover { transform: scale(1.1); color: white; }
.fab-wa { background-color: #25D366; }
.fab-share { background-color: var(--primary, #8338ec); }
.fab-main { width: 55px; height: 55px; border-radius: 50%; background-color: var(--primary, #8338ec); color: white; border: none; font-size: 24px; cursor: pointer; box-shadow: 0 4px 15px rgba(0,0,0,0.3); transition: transform 0.3s ease; display: flex; align-items: center; justify-content: center; }
.fab-container.active .fab-main { transform: rotate(45deg); }

/* WhatsApp Modal */
.wa-modal { display: none; position: fixed; z-index: 10000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); align-items: center; justify-content: center; }
.wa-modal-content { background-color: var(--surface, #ffffff); color: var(--text, #1a2035); padding: 25px; border-radius: 16px; width: 90%; max-width: 350px; position: relative; box-shadow: 0 10px 30px rgba(0,0,0,0.3); font-family: 'Poppins', sans-serif; }
.wa-close { position: absolute; top: 15px; right: 20px; font-size: 24px; cursor: pointer; color: var(--muted, #6b7280); line-height: 1; }
.wa-modal-content h3 { margin: 0 0 10px 0; font-size: 18px; font-weight: 600; }
.wa-modal-content p { font-size: 13px; color: var(--muted, #6b7280); margin: 0 0 15px 0; line-height: 1.4; }
.wa-input { width: 100%; padding: 12px 15px; border: 1px solid var(--muted, #6b7280); border-radius: 8px; margin-bottom: 15px; font-size: 15px; background: var(--bg, #ffffff); color: var(--text, #1a2035); box-sizing: border-box; outline: none; }
.wa-input:focus { border-color: var(--primary, #8338ec); }
.wa-btn { width: 100%; padding: 12px; background-color: #25D366; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: background 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; }
.wa-btn:hover { background-color: #1ebd5a; }
</style>

<script>
const VCARD_DATA = {
    name: <?= json_encode($fullName) ?>,
    firstName: <?= json_encode($vcard['first_name'] ?? '') ?>,
    lastName: <?= json_encode($vcard['last_name'] ?? '') ?>,
    occupation: <?= json_encode($vcard['occupation'] ?? '') ?>,
    company: <?= json_encode($vcard['company'] ?? '') ?>,
    email: <?= json_encode($vcard['email'] ?? '') ?>,
    phone: <?= json_encode($vcard['phone'] ?? '') ?>,
    altEmail: <?= json_encode($vcard['alternate_email'] ?? '') ?>,
    altPhone: <?= json_encode($vcard['alternate_phone'] ?? '') ?>,
    location: <?= json_encode($vcard['location'] ?? '') ?>,
    url: window.location.href
};

function saveContact() {
    const vcf = [
        'BEGIN:VCARD',
        'VERSION:3.0',
        `FN:${VCARD_DATA.name}`,
        VCARD_DATA.firstName ? `N:${VCARD_DATA.lastName};${VCARD_DATA.firstName};;;` : '',
        VCARD_DATA.occupation ? `TITLE:${VCARD_DATA.occupation}` : '',
        VCARD_DATA.company ? `ORG:${VCARD_DATA.company}` : '',
        VCARD_DATA.email ? `EMAIL;TYPE=WORK:${VCARD_DATA.email}` : '',
        VCARD_DATA.altEmail ? `EMAIL;TYPE=HOME:${VCARD_DATA.altEmail}` : '',
        VCARD_DATA.phone ? `TEL;TYPE=CELL:${VCARD_DATA.phone}` : '',
        VCARD_DATA.altPhone ? `TEL;TYPE=WORK:${VCARD_DATA.altPhone}` : '',
        VCARD_DATA.location ? `ADR;TYPE=WORK:;;${VCARD_DATA.location};;;;` : '',
        `URL:${VCARD_DATA.url}`,
        'END:VCARD'
    ].filter(Boolean).join('\n');

    const blob = new Blob([vcf], { type: 'text/vcard' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `${VCARD_DATA.name.replace(/\s+/g, '_')}.vcf`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    showToast('✓ Contact saved!', 'success');
}

async function shareCard() {
    if (navigator.share) {
        try {
            await navigator.share({
                title: VCARD_DATA.name,
                text: `Check out ${VCARD_DATA.name}'s digital business card`,
                url: VCARD_DATA.url
            });
        } catch(e) {}
    } else {
        navigator.clipboard.writeText(VCARD_DATA.url);
        showToast('✓ Link copied to clipboard!', 'success');
    }
}

function toggleFab() {
    document.querySelector('.fab-container').classList.toggle('active');
}

function openWhatsAppModal() {
    document.getElementById('waModal').style.display = 'flex';
}

function closeWhatsAppModal() {
    document.getElementById('waModal').style.display = 'none';
    document.getElementById('waNumber').value = '';
}

function sendWhatsApp() {
    const number = document.getElementById('waNumber').value.replace(/\D/g, '');
    if (!number) {
        showToast('Please enter a valid number', 'error');
        return;
    }
    const message = encodeURIComponent(`Check out ${VCARD_DATA.name}'s digital business card:\n${VCARD_DATA.url}`);
    window.open(`https://wa.me/${number}?text=${message}`, '_blank');
    closeWhatsAppModal();
}

async function submitInquiry(event) {
    event.preventDefault();
    const form = event.target;
    const btn = form.querySelector('button[type="submit"]');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    const originalText = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

    try {
        const response = await fetch('/inquiry-submit.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await response.json();
        if (result.success) {
            showToast('✓ Message sent! We will get back to you soon.', 'success');
            form.reset();
        } else {
            showToast(result.message || 'Failed to send', 'error');
        }
    } catch (err) {
        showToast('Connection error', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}

async function submitAppointment(event) {
    event.preventDefault();
    const form = event.target;
    const btn = form.querySelector('button[type="submit"]');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    const originalText = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Booking...';

    try {
        const response = await fetch('/appointment-submit.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await response.json();
        if (result.success) {
            showToast('✓ Appointment booked! We will confirm shortly.', 'success');
            form.reset();
        } else {
            showToast(result.message || 'Failed to book', 'error');
        }
    } catch (err) {
        showToast('Connection error', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}

async function fetchAvailableSlots(date, vcardId) {
    const timeSelect = document.getElementById('appointment-time');
    const timeContainer = document.getElementById('time-container');
    
    if (!date) {
        timeSelect.innerHTML = '<option value="">Select date first</option>';
        timeSelect.disabled = true;
        if (timeContainer) timeContainer.style.display = 'none';
        return;
    }

    if (timeContainer) timeContainer.style.display = 'block';
    timeSelect.innerHTML = '<option value="">Loading slots...</option>';
    timeSelect.disabled = true;

    try {
        const response = await fetch(`/api/appointments/slots_public.php?vcard_id=${vcardId}&date=${date}`);
        const result = await response.json();
        
        if (result.success) {
            const dateObj = new Date(date);
            const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const dayName = dayNames[dateObj.getUTCDay()];
            
            if (result.data.length > 0) {
                timeSelect.innerHTML = `<option value="">Select time for ${dayName}</option>`;
                result.data.forEach(slot => {
                    const option = document.createElement('option');
                    option.value = slot.value || slot;
                    option.textContent = slot.label || slot;
                    timeSelect.appendChild(option);
                });
                timeSelect.disabled = false;
            } else {
                timeSelect.innerHTML = '<option value="">No slots available for this date</option>';
            }
        } else {
            timeSelect.innerHTML = '<option value="">Failed to load slots</option>';
        }
    } catch (err) {
        timeSelect.innerHTML = '<option value="">Error loading slots</option>';
    }
}

function showToast(msg, type = 'success') {
    const old = document.querySelector('.toast');
    if (old) old.remove();
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = msg;
    document.body.appendChild(toast);
    setTimeout(() => toast.classList.add('show'), 10);
    setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 400); }, 3500);
}
</script>

<style>
.toast {
    position: fixed;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%) translateY(100px);
    background: #1a2035;
    color: white;
    padding: 14px 24px;
    border-radius: 50px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    transition: transform 0.4s;
    z-index: 9999;
    font-weight: 600;
    font-family: 'Poppins', sans-serif;
    max-width: 90%;
}
.toast.show { transform: translateX(-50%) translateY(0); }
.toast.success { background: #10b981; }
.toast.error { background: #ef4444; }

/* Name reveal animation (letter by letter) */
@keyframes tfNameReveal { from { opacity:0; transform: translateY(16px) rotate(6deg); } to { opacity:1; transform:none; } }
.tf-name-char { display:inline-block; opacity:0; animation: tfNameReveal .5s cubic-bezier(.2,.7,.3,1) forwards; will-change: transform, opacity; }
.tf-name-space { display:inline-block; width:.32em; }

/* ── Carousel (shared by Products & Gallery, all templates) ── */
.tf-carousel{position:relative;}
.tf-track{display:flex;gap:14px;overflow-x:auto;scroll-snap-type:x mandatory;-webkit-overflow-scrolling:touch;scrollbar-width:none;padding:4px 22px 4px;}
.tf-track::-webkit-scrollbar{display:none;}
.tf-slide{flex:0 0 80%;scroll-snap-align:center;}
.tf-arrow{position:absolute;top:42%;transform:translateY(-50%);width:38px;height:38px;border-radius:50%;border:none;background:rgba(0,0,0,.45);color:#fff;font-size:15px;display:flex;align-items:center;justify-content:center;cursor:pointer;z-index:4;opacity:.85;transition:opacity .2s,background .2s;-webkit-backdrop-filter:blur(4px);backdrop-filter:blur(4px);}
.tf-arrow:hover{opacity:1;background:rgba(0,0,0,.65);}
.tf-arrow-prev{left:8px;}
.tf-arrow-next{right:8px;}
.tf-dots{display:flex;gap:7px;justify-content:center;margin-top:12px;}
.tf-dot{width:7px;height:7px;border-radius:50%;background:currentColor;opacity:.25;transition:opacity .25s,width .25s;cursor:pointer;}
.tf-dot.active{opacity:.9;width:20px;border-radius:4px;}
/* Products (carousel cards) */
.tf-prod{position:relative;display:block;border-radius:18px;overflow:hidden;text-decoration:none;color:#fff;background:rgba(128,128,128,.07);border:1px solid rgba(128,128,128,.12);box-shadow:0 8px 24px rgba(0,0,0,.18);}
.tf-prod-img{width:100%;height:300px;object-fit:cover;display:block;transition:transform 6s ease;}
.tf-slide:hover .tf-prod-img{transform:scale(1.08);}
.tf-prod-no-img{width:100%;height:300px;display:flex;align-items:center;justify-content:center;background:rgba(128,128,128,.12);font-size:3rem;color:rgba(128,128,128,.35);}
.tf-prod-info{position:absolute;left:0;right:0;bottom:0;padding:34px 16px 16px;background:linear-gradient(to top,rgba(0,0,0,.82),rgba(0,0,0,.35) 55%,transparent);}
.tf-prod-name{font-size:16px;font-weight:700;margin-bottom:4px;line-height:1.25;text-shadow:0 1px 4px rgba(0,0,0,.5);color:#fff;}
.tf-prod-price{display:inline-block;font-size:12px;font-weight:700;padding:3px 10px;border-radius:20px;background:rgba(255,255,255,.18);-webkit-backdrop-filter:blur(4px);backdrop-filter:blur(4px);margin-bottom:6px;color:#fff;}
.tf-prod-desc{font-size:11px;opacity:.8;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;line-height:1.45;color:#fff;}
/* Gallery (carousel) */
.tf-gal-slide{position:relative;border-radius:18px;overflow:hidden;cursor:zoom-in;box-shadow:0 8px 24px rgba(0,0,0,.18);}
.tf-gal-slide img{width:100%;height:340px;object-fit:cover;display:block;transition:transform 6s ease;}
.tf-gal-slide:hover img{transform:scale(1.07);}
.tf-gal-zoom{position:absolute;top:10px;right:10px;width:30px;height:30px;border-radius:50%;background:rgba(0,0,0,.45);color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;-webkit-backdrop-filter:blur(4px);backdrop-filter:blur(4px);}
/* Lightbox */
.tf-lightbox{position:fixed;inset:0;z-index:11000;background:rgba(0,0,0,.94);display:none;align-items:center;justify-content:center;}
.tf-lightbox.open{display:flex;}
.tf-lightbox img{max-width:92vw;max-height:82vh;border-radius:10px;box-shadow:0 20px 60px rgba(0,0,0,.6);-webkit-user-select:none;user-select:none;}
.tf-lb-close{position:absolute;top:18px;right:20px;width:42px;height:42px;border-radius:50%;border:none;background:rgba(255,255,255,.12);color:#fff;font-size:20px;cursor:pointer;}
.tf-lb-nav{position:absolute;top:50%;transform:translateY(-50%);width:46px;height:46px;border-radius:50%;border:none;background:rgba(255,255,255,.12);color:#fff;font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;}
.tf-lb-prev{left:14px;}
.tf-lb-next{right:14px;}
.tf-lb-count{position:absolute;bottom:22px;left:50%;transform:translateX(-50%);color:rgba(255,255,255,.8);font-size:12px;letter-spacing:1px;}
</style>

<!-- Shared Gallery Lightbox -->
<div class="tf-lightbox" id="tfLightbox" aria-hidden="true">
  <button class="tf-lb-close" type="button" aria-label="Close">&times;</button>
  <button class="tf-lb-nav tf-lb-prev" type="button" aria-label="Previous"><i class="fas fa-chevron-left"></i></button>
  <img id="tfLightboxImg" src="" alt="Gallery image">
  <button class="tf-lb-nav tf-lb-next" type="button" aria-label="Next"><i class="fas fa-chevron-right"></i></button>
  <div class="tf-lb-count" id="tfLightboxCount"></div>
</div>

<script>
(function () {
  /* ---------- 1. Name letter-by-letter reveal ---------- */
  function animateNames() {
    var nodes = document.querySelectorAll('.profile-name, .tf-name, [data-animate-name]');
    nodes.forEach(function (el) {
      // For a .profile-name wrapper, animate the name heading INSIDE it (not the
      // wrapper itself) so we don't flatten the occupation line or stack each
      // character on its own row when the wrapper is a flex column.
      if (!el.matches('.tf-name, [data-animate-name]')) {
        var h = el.querySelector('h1,h2,h3,h4,h5');
        if (h) el = h;
        else if (el.children.length) return; // structured but no heading -> skip
      }
      if (el.dataset.tfAnimated) return;
      el.dataset.tfAnimated = '1';
      var keep = el.querySelector('i, svg'); // preserve verification icon etc.
      var text = (el.textContent || '').replace(/\s+$/, '');
      el.textContent = '';
      var i = 0;
      text.split('').forEach(function (ch) {
        if (ch === ' ') {
          var sp = document.createElement('span');
          sp.className = 'tf-name-space';
          el.appendChild(sp);
          return;
        }
        var s = document.createElement('span');
        s.className = 'tf-name-char';
        s.textContent = ch;
        s.style.animationDelay = (i * 0.06) + 's';
        el.appendChild(s);
        i++;
      });
      if (keep) { el.appendChild(document.createTextNode(' ')); el.appendChild(keep); }
    });
  }

  /* ---------- 2. Carousels (Products & Gallery) ---------- */
  function initCarousels() {
    document.querySelectorAll('[data-carousel]').forEach(function (root) {
      var track = root.querySelector('[data-carousel-track]');
      if (!track) return;
      var slides = Array.prototype.slice.call(track.children);
      var dotsWrap = root.querySelector('[data-carousel-dots]');
      var prev = root.querySelector('[data-carousel-prev]');
      var next = root.querySelector('[data-carousel-next]');
      if (slides.length <= 1) return;

      // Build dots
      var dots = [];
      if (dotsWrap) {
        slides.forEach(function (_, idx) {
          var d = document.createElement('span');
          d.className = 'tf-dot' + (idx === 0 ? ' active' : '');
          d.addEventListener('click', function () { scrollToIndex(idx); });
          dotsWrap.appendChild(d);
          dots.push(d);
        });
      }

      function currentIndex() {
        var c = track.scrollLeft + track.clientWidth / 2;
        var best = 0, bestDist = Infinity;
        slides.forEach(function (s, idx) {
          var center = s.offsetLeft + s.offsetWidth / 2;
          var dist = Math.abs(center - c);
          if (dist < bestDist) { bestDist = dist; best = idx; }
        });
        return best;
      }
      function scrollToIndex(idx) {
        idx = Math.max(0, Math.min(slides.length - 1, idx));
        var s = slides[idx];
        track.scrollTo({ left: s.offsetLeft - (track.clientWidth - s.offsetWidth) / 2, behavior: 'smooth' });
      }
      function syncDots() {
        var idx = currentIndex();
        dots.forEach(function (d, i) { d.classList.toggle('active', i === idx); });
      }

      if (prev) prev.addEventListener('click', function () { scrollToIndex(currentIndex() - 1); resetAuto(); });
      if (next) next.addEventListener('click', function () { scrollToIndex(currentIndex() + 1); resetAuto(); });

      var ticking = false;
      track.addEventListener('scroll', function () {
        if (!ticking) { window.requestAnimationFrame(function () { syncDots(); ticking = false; }); ticking = true; }
      });

      // Autoplay (pause on touch / hover)
      var autoTimer = null;
      function startAuto() {
        stopAuto();
        autoTimer = setInterval(function () {
          var idx = currentIndex();
          scrollToIndex(idx >= slides.length - 1 ? 0 : idx + 1);
        }, 4500);
      }
      function stopAuto() { if (autoTimer) { clearInterval(autoTimer); autoTimer = null; } }
      function resetAuto() { startAuto(); }
      ['touchstart', 'mouseenter'].forEach(function (ev) { root.addEventListener(ev, stopAuto, { passive: true }); });
      ['touchend', 'mouseleave'].forEach(function (ev) { root.addEventListener(ev, startAuto, { passive: true }); });
      startAuto();
    });
  }

  /* ---------- 3. Gallery lightbox ---------- */
  function initLightbox() {
    var items = Array.prototype.slice.call(document.querySelectorAll('[data-lightbox]'));
    if (!items.length) return;
    var box = document.getElementById('tfLightbox');
    var img = document.getElementById('tfLightboxImg');
    var count = document.getElementById('tfLightboxCount');
    var closeBtn = box.querySelector('.tf-lb-close');
    var prevBtn = box.querySelector('.tf-lb-prev');
    var nextBtn = box.querySelector('.tf-lb-next');
    var srcs = items.map(function (it) { return it.getAttribute('data-lightbox'); });
    var cur = 0;

    function open(idx) { cur = idx; render(); box.classList.add('open'); box.setAttribute('aria-hidden', 'false'); }
    function close() { box.classList.remove('open'); box.setAttribute('aria-hidden', 'true'); }
    function render() {
      img.src = srcs[cur];
      count.textContent = (cur + 1) + ' / ' + srcs.length;
      prevBtn.style.display = nextBtn.style.display = srcs.length > 1 ? 'flex' : 'none';
    }
    function go(d) { cur = (cur + d + srcs.length) % srcs.length; render(); }

    items.forEach(function (it, idx) { it.addEventListener('click', function () { open(idx); }); });
    closeBtn.addEventListener('click', close);
    prevBtn.addEventListener('click', function (e) { e.stopPropagation(); go(-1); });
    nextBtn.addEventListener('click', function (e) { e.stopPropagation(); go(1); });
    box.addEventListener('click', function (e) { if (e.target === box) close(); });
    document.addEventListener('keydown', function (e) {
      if (!box.classList.contains('open')) return;
      if (e.key === 'Escape') close();
      else if (e.key === 'ArrowLeft') go(-1);
      else if (e.key === 'ArrowRight') go(1);
    });
    // Swipe on the lightbox
    var sx = 0;
    box.addEventListener('touchstart', function (e) { sx = e.touches[0].clientX; }, { passive: true });
    box.addEventListener('touchend', function (e) {
      var dx = e.changedTouches[0].clientX - sx;
      if (Math.abs(dx) > 50) go(dx < 0 ? 1 : -1);
    }, { passive: true });
  }

  function init() { animateNames(); initCarousels(); initLightbox(); }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})();
</script>
