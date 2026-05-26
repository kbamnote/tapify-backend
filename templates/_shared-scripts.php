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
</style>
