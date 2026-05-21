<!-- Global WhatsApp Order Modal -->
<div class="modal fade" id="whatsappOrderModal" tabindex="-1" aria-hidden="true" style="z-index: 10000;">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
      <div class="modal-header" style="background: linear-gradient(135deg, #128C7E 0%, #25D366 100%); color: white; border-bottom: none; padding: 20px;">
        <h5 class="modal-title" style="font-weight: 700; font-size: 1.2rem; margin: 0;"><i class="fab fa-whatsapp me-2"></i> Complete Your Order</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="opacity: 1;"></button>
      </div>
      <div class="modal-body" style="padding: 24px;">
        <p style="color: #6b7280; font-size: 0.95rem; margin-bottom: 20px;">Please enter your details to proceed with your WhatsApp order.</p>
        <form id="whatsappOrderForm">
          <input type="hidden" id="waOrderStoreId" value="<?php echo isset($store['id']) ? $store['id'] : 0; ?>">
          <input type="hidden" id="waOrderProductName" value="">
          <input type="hidden" id="waOrderProductPrice" value="0">
          <input type="hidden" id="waOrderUrl" value="">
          
          <div class="mb-3">
            <label class="form-label" style="font-weight: 600; font-size: 0.9rem; color: #374151;">Full Name</label>
            <input type="text" class="form-control" id="waCustomerName" required placeholder="John Doe" style="padding: 12px 16px; border-radius: 10px; border: 2px solid #e5e7eb;">
          </div>
          <div class="mb-4">
            <label class="form-label" style="font-weight: 600; font-size: 0.9rem; color: #374151;">WhatsApp Number</label>
            <input type="tel" class="form-control" id="waCustomerPhone" required placeholder="+1 234 567 8900" style="padding: 12px 16px; border-radius: 10px; border: 2px solid #e5e7eb;">
          </div>
          <button type="submit" class="btn w-100" style="background: #25D366; color: white; padding: 14px; border-radius: 50px; font-weight: 700; font-size: 1rem; border: none; box-shadow: 0 8px 20px rgba(37,211,102,0.3); transition: all 0.3s;">
            <i class="fab fa-whatsapp" style="font-size: 1.1rem; margin-right: 6px;"></i> Send Order to WhatsApp
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if Bootstrap is loaded
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap JS is required for the WhatsApp Order Modal to function.');
        return;
    }

    // Initialize Modal
    const orderModal = new bootstrap.Modal(document.getElementById('whatsappOrderModal'));
    
    // Intercept all WhatsApp order buttons in the template
    const waButtons = document.querySelectorAll('.btn-whatsapp');
    
    waButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            // Only intercept if it's a product order button (contains text param)
            const url = this.getAttribute('href');
            if (url && url.includes('wa.me') && url.includes('text=')) {
                e.preventDefault();
                
                let productName = "Template Product";
                let productPrice = 0;
                
                try {
                    const urlObj = new URL(url);
                    const text = urlObj.searchParams.get('text');
                    if (text) {
                        // Regex to parse "Hi, I want to order: {Name} at {Currency}{Price}"
                        const match = text.match(/order:\s*(.*?)\s*at\s*.*?([\d.,]+)/i);
                        if (match) {
                            productName = match[1];
                            productPrice = parseFloat(match[2].replace(/[^\d.]/g, ''));
                        } else {
                            productName = text.substring(0, 50) + "..."; // Fallback
                        }
                    }
                } catch (err) {}
                
                document.getElementById('waOrderUrl').value = url;
                document.getElementById('waOrderProductName').value = productName;
                document.getElementById('waOrderProductPrice').value = productPrice;
                
                orderModal.show();
            }
        });
    });

    document.getElementById('whatsappOrderForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
        submitBtn.disabled = true;

        const storeId = document.getElementById('waOrderStoreId').value;
        const name = document.getElementById('waCustomerName').value;
        const phone = document.getElementById('waCustomerPhone').value;
        const productName = document.getElementById('waOrderProductName').value;
        const productPrice = parseFloat(document.getElementById('waOrderProductPrice').value) || 0;
        const waUrl = document.getElementById('waOrderUrl').value;
        
        // Ensure accurate tracking in the DB
        const payload = {
            store_id: storeId,
            customer_name: name,
            customer_phone: phone,
            items: [{ name: productName, price: productPrice, qty: 1 }],
            subtotal: productPrice,
            delivery_charge: 0,
            total_amount: productPrice
        };
        
        // Post the order to the backend
        fetch('/store-order-submit.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        }).then(res => {
            orderModal.hide();
            window.location.href = waUrl;
        }).catch(err => {
            console.error('Failed to submit order to DB', err);
            orderModal.hide();
            window.location.href = waUrl;
        });
    });
});
</script>
