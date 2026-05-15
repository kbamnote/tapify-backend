<?php
/**
 * TAPIFY - Email Templates
 * Beautiful HTML emails for notifications
 */

class EmailTemplates {

    private static function baseTemplate($title, $content, $accentColor = '#8338ec') {
        return '<!DOCTYPE html>
<html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>' . htmlspecialchars($title) . '</title>
</head>
<body style="margin:0;padding:0;font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,sans-serif;background:#f5f7fa;color:#1a2035;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f7fa;padding:30px 10px;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background:white;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
<tr><td style="background:linear-gradient(135deg,' . $accentColor . ',#a855f7);padding:30px;text-align:center;">
<h1 style="color:white;margin:0;font-size:1.8rem;font-weight:700;">Tapify</h1>
<p style="color:rgba(255,255,255,0.9);margin:8px 0 0;font-size:0.92rem;">Digital Business Cards</p>
</td></tr>
<tr><td style="padding:35px 30px;">
' . $content . '
</td></tr>
<tr><td style="background:#f9fafb;padding:25px 30px;text-align:center;border-top:1px solid #e5e7eb;">
<p style="color:#6b7280;font-size:0.85rem;margin:0 0 8px;">This is an automated email from Tapify</p>
<p style="color:#9ca3af;font-size:0.78rem;margin:0;">© ' . date('Y') . ' MRPRINT WORLD PVT LTD · An Innovative Product</p>
</td></tr>
</table>
</td></tr>
</table>
</body></html>';
    }

    /**
     * NEW INQUIRY notification to admin
     */
    public static function newInquiryAdmin($data) {
        $content = '
<h2 style="color:#1a2035;margin:0 0 8px;font-size:1.4rem;">🔔 New Inquiry Received</h2>
<p style="color:#6b7280;margin:0 0 25px;">Someone has submitted an inquiry through your vCard.</p>

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;border-radius:12px;padding:20px;margin-bottom:20px;">
<tr><td>
<div style="margin-bottom:15px"><strong style="color:#8338ec;font-size:0.78rem;text-transform:uppercase;letter-spacing:1px;">From vCard</strong>
<div style="color:#1a2035;font-size:1.05rem;font-weight:600;margin-top:3px;">' . htmlspecialchars($data['vcard_name']) . '</div></div>
<div style="margin-bottom:15px"><strong style="color:#8338ec;font-size:0.78rem;text-transform:uppercase;letter-spacing:1px;">Customer Name</strong>
<div style="color:#1a2035;font-size:1.05rem;font-weight:600;margin-top:3px;">' . htmlspecialchars($data['name']) . '</div></div>';

        if (!empty($data['email'])) {
            $content .= '<div style="margin-bottom:15px"><strong style="color:#8338ec;font-size:0.78rem;text-transform:uppercase;letter-spacing:1px;">Email</strong>
<div style="margin-top:3px;"><a href="mailto:' . htmlspecialchars($data['email']) . '" style="color:#8338ec;text-decoration:none;font-weight:600;">' . htmlspecialchars($data['email']) . '</a></div></div>';
        }

        if (!empty($data['phone'])) {
            $phoneClean = preg_replace('/\D/', '', $data['phone']);
            $content .= '<div style="margin-bottom:15px"><strong style="color:#8338ec;font-size:0.78rem;text-transform:uppercase;letter-spacing:1px;">Phone</strong>
<div style="margin-top:3px;"><a href="tel:' . htmlspecialchars($data['phone']) . '" style="color:#8338ec;text-decoration:none;font-weight:600;">' . htmlspecialchars($data['phone']) . '</a></div></div>';
        }

        $content .= '</td></tr></table>';

        if (!empty($data['message'])) {
            $content .= '
<div style="background:#fef3c7;border-left:4px solid #f59e0b;border-radius:8px;padding:18px;margin-bottom:25px;">
<strong style="color:#92400e;font-size:0.78rem;text-transform:uppercase;letter-spacing:1px;">Message</strong>
<div style="color:#1a2035;margin-top:8px;line-height:1.6;">' . nl2br(htmlspecialchars($data['message'])) . '</div>
</div>';
        }

        $content .= '
<div style="text-align:center;margin-top:30px;">';

        if (!empty($data['phone'])) {
            $phoneClean = preg_replace('/\D/', '', $data['phone']);
            $content .= '<a href="https://wa.me/' . $phoneClean . '" style="display:inline-block;background:#25D366;color:white;padding:12px 24px;border-radius:50px;text-decoration:none;font-weight:600;margin:5px;">📱 WhatsApp</a>';
        }
        if (!empty($data['email'])) {
            $content .= '<a href="mailto:' . htmlspecialchars($data['email']) . '" style="display:inline-block;background:#8338ec;color:white;padding:12px 24px;border-radius:50px;text-decoration:none;font-weight:600;margin:5px;">✉️ Reply</a>';
        }

        $content .= '
</div>
<p style="color:#6b7280;font-size:0.85rem;text-align:center;margin-top:25px;">View all inquiries in your <a href="' . SITE_URL . '/inquiries.html" style="color:#8338ec;font-weight:600;">Tapify Admin Panel</a></p>';

        return [
            'subject' => '🔔 New Inquiry from ' . $data['name'] . ' - ' . $data['vcard_name'],
            'html' => self::baseTemplate('New Inquiry', $content)
        ];
    }

    /**
     * INQUIRY CONFIRMATION to customer
     */
    public static function inquiryConfirmation($data) {
        $content = '
<h2 style="color:#1a2035;margin:0 0 8px;font-size:1.4rem;">✓ Message Received</h2>
<p style="color:#6b7280;margin:0 0 25px;">Hi <strong>' . htmlspecialchars($data['name']) . '</strong>, thank you for reaching out!</p>

<div style="background:#d1fae5;border-left:4px solid #10b981;border-radius:8px;padding:18px;margin-bottom:25px;">
<p style="color:#065f46;margin:0;font-weight:600;">Your message has been delivered to ' . htmlspecialchars($data['vcard_name']) . '.</p>
<p style="color:#065f46;margin:8px 0 0;font-size:0.92rem;">We will get back to you as soon as possible.</p>
</div>

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;border-radius:12px;padding:20px;margin-bottom:20px;">
<tr><td>
<strong style="color:#8338ec;font-size:0.78rem;text-transform:uppercase;letter-spacing:1px;">Your Message</strong>
<div style="color:#1a2035;margin-top:8px;line-height:1.6;font-style:italic;">' . nl2br(htmlspecialchars($data['message'])) . '</div>
</td></tr>
</table>

<p style="color:#6b7280;font-size:0.92rem;text-align:center;margin-top:25px;">If you need urgent assistance, please call us directly.</p>';

        return [
            'subject' => '✓ Message Received - ' . $data['vcard_name'],
            'html' => self::baseTemplate('Message Confirmation', $content)
        ];
    }

    /**
     * NEW APPOINTMENT notification to admin
     */
    public static function newAppointmentAdmin($data) {
        $content = '
<h2 style="color:#1a2035;margin:0 0 8px;font-size:1.4rem;">📅 New Appointment Booked</h2>
<p style="color:#6b7280;margin:0 0 25px;">A customer has booked an appointment with you.</p>

<table width="100%" cellpadding="0" cellspacing="0" style="background:linear-gradient(135deg,#8338ec,#a855f7);border-radius:12px;padding:25px;margin-bottom:20px;">
<tr><td style="text-align:center;color:white;">
<div style="font-size:0.85rem;opacity:0.85;text-transform:uppercase;letter-spacing:2px;">Appointment</div>
<div style="font-size:1.8rem;font-weight:700;margin:8px 0;">' . date('d M Y', strtotime($data['appointment_date'])) . '</div>
<div style="font-size:1.4rem;font-weight:600;">' . date('h:i A', strtotime($data['appointment_time'])) . '</div>
<div style="font-size:0.88rem;opacity:0.9;margin-top:6px;">' . date('l', strtotime($data['appointment_date'])) . '</div>
</td></tr>
</table>

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;border-radius:12px;padding:20px;margin-bottom:20px;">
<tr><td>
<div style="margin-bottom:15px"><strong style="color:#8338ec;font-size:0.78rem;text-transform:uppercase;letter-spacing:1px;">For vCard</strong>
<div style="color:#1a2035;font-size:1.05rem;font-weight:600;margin-top:3px;">' . htmlspecialchars($data['vcard_name']) . '</div></div>
<div style="margin-bottom:15px"><strong style="color:#8338ec;font-size:0.78rem;text-transform:uppercase;letter-spacing:1px;">Customer</strong>
<div style="color:#1a2035;font-size:1.05rem;font-weight:600;margin-top:3px;">' . htmlspecialchars($data['customer_name']) . '</div></div>';

        if (!empty($data['customer_phone'])) {
            $content .= '<div style="margin-bottom:15px"><strong style="color:#8338ec;font-size:0.78rem;text-transform:uppercase;letter-spacing:1px;">Phone</strong>
<div style="margin-top:3px;"><a href="tel:' . htmlspecialchars($data['customer_phone']) . '" style="color:#8338ec;text-decoration:none;font-weight:600;">' . htmlspecialchars($data['customer_phone']) . '</a></div></div>';
        }

        if (!empty($data['service_name'])) {
            $content .= '<div style="margin-bottom:15px"><strong style="color:#8338ec;font-size:0.78rem;text-transform:uppercase;letter-spacing:1px;">Service</strong>
<div style="color:#1a2035;margin-top:3px;">' . htmlspecialchars($data['service_name']) . '</div></div>';
        }

        $content .= '</td></tr></table>';

        if (!empty($data['customer_notes'])) {
            $content .= '
<div style="background:#fef3c7;border-left:4px solid #f59e0b;border-radius:8px;padding:18px;margin-bottom:25px;">
<strong style="color:#92400e;font-size:0.78rem;text-transform:uppercase;letter-spacing:1px;">Customer Notes</strong>
<div style="color:#1a2035;margin-top:8px;line-height:1.6;">' . nl2br(htmlspecialchars($data['customer_notes'])) . '</div>
</div>';
        }

        $content .= '
<div style="text-align:center;margin-top:30px;">';

        if (!empty($data['customer_phone'])) {
            $phoneClean = preg_replace('/\D/', '', $data['customer_phone']);
            $content .= '<a href="https://wa.me/' . $phoneClean . '" style="display:inline-block;background:#25D366;color:white;padding:12px 24px;border-radius:50px;text-decoration:none;font-weight:600;margin:5px;">📱 WhatsApp</a>';
            $content .= '<a href="tel:' . htmlspecialchars($data['customer_phone']) . '" style="display:inline-block;background:#3b82f6;color:white;padding:12px 24px;border-radius:50px;text-decoration:none;font-weight:600;margin:5px;">📞 Call</a>';
        }

        $content .= '
</div>
<p style="color:#6b7280;font-size:0.85rem;text-align:center;margin-top:25px;">View all appointments in your <a href="' . SITE_URL . '/appointments.html" style="color:#8338ec;font-weight:600;">Tapify Admin Panel</a></p>';

        return [
            'subject' => '📅 New Appointment - ' . $data['customer_name'] . ' on ' . date('d M', strtotime($data['appointment_date'])),
            'html' => self::baseTemplate('New Appointment', $content)
        ];
    }

    /**
     * APPOINTMENT CONFIRMATION to customer
     */
    public static function appointmentConfirmation($data) {
        $content = '
<h2 style="color:#1a2035;margin:0 0 8px;font-size:1.4rem;">✓ Appointment Booked!</h2>
<p style="color:#6b7280;margin:0 0 25px;">Hi <strong>' . htmlspecialchars($data['customer_name']) . '</strong>, your appointment has been received.</p>

<table width="100%" cellpadding="0" cellspacing="0" style="background:linear-gradient(135deg,#10b981,#059669);border-radius:12px;padding:25px;margin-bottom:20px;">
<tr><td style="text-align:center;color:white;">
<div style="font-size:0.85rem;opacity:0.85;text-transform:uppercase;letter-spacing:2px;">Your Appointment</div>
<div style="font-size:1.8rem;font-weight:700;margin:8px 0;">' . date('d M Y', strtotime($data['appointment_date'])) . '</div>
<div style="font-size:1.4rem;font-weight:600;">' . date('h:i A', strtotime($data['appointment_time'])) . '</div>
<div style="font-size:0.88rem;opacity:0.9;margin-top:6px;">' . date('l', strtotime($data['appointment_date'])) . '</div>
</td></tr>
</table>

<div style="background:#fef3c7;border-radius:8px;padding:18px;margin-bottom:20px;">
<p style="color:#92400e;margin:0;font-weight:600;">⏳ Status: Pending Confirmation</p>
<p style="color:#92400e;margin:5px 0 0;font-size:0.92rem;">' . htmlspecialchars($data['vcard_name']) . ' will contact you shortly to confirm.</p>
</div>';

        if (!empty($data['service_name'])) {
            $content .= '
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;border-radius:12px;padding:20px;margin-bottom:20px;">
<tr><td>
<strong style="color:#8338ec;font-size:0.78rem;text-transform:uppercase;letter-spacing:1px;">Service</strong>
<div style="color:#1a2035;margin-top:5px;font-weight:600;">' . htmlspecialchars($data['service_name']) . '</div>
</td></tr>
</table>';
        }

        $content .= '
<p style="color:#6b7280;font-size:0.92rem;text-align:center;margin-top:25px;">Need to make changes? Please contact us directly.</p>';

        return [
            'subject' => '✓ Appointment Booked - ' . date('d M Y', strtotime($data['appointment_date'])),
            'html' => self::baseTemplate('Appointment Confirmation', $content, '#10b981')
        ];
    }

    /**
     * NEW ORDER notification to admin
     */
    public static function newOrderAdmin($data) {
        $itemsHtml = '';
        if (!empty($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                $itemsHtml .= '<tr><td style="padding:8px 0;border-bottom:1px solid #e5e7eb;">' . htmlspecialchars($item['name']) . ' × ' . $item['qty'] . '</td>
                <td style="padding:8px 0;border-bottom:1px solid #e5e7eb;text-align:right;font-weight:600;">₹' . number_format($item['price'] * $item['qty'], 2) . '</td></tr>';
            }
        }

        $content = '
<h2 style="color:#1a2035;margin:0 0 8px;font-size:1.4rem;">🛍️ New Order Received!</h2>
<p style="color:#6b7280;margin:0 0 25px;">A customer has placed an order through your WhatsApp store.</p>

<table width="100%" cellpadding="0" cellspacing="0" style="background:linear-gradient(135deg,#25D366,#128C7E);border-radius:12px;padding:25px;margin-bottom:20px;">
<tr><td style="text-align:center;color:white;">
<div style="font-size:0.85rem;opacity:0.85;text-transform:uppercase;letter-spacing:2px;">Order Total</div>
<div style="font-size:2.2rem;font-weight:700;margin:5px 0;">₹' . number_format($data['total_amount'], 2) . '</div>
<div style="font-size:0.88rem;opacity:0.9;">Order #' . $data['order_id'] . ' from ' . htmlspecialchars($data['store_name']) . '</div>
</td></tr>
</table>

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;border-radius:12px;padding:20px;margin-bottom:20px;">
<tr><td>
<div style="margin-bottom:15px"><strong style="color:#25D366;font-size:0.78rem;text-transform:uppercase;letter-spacing:1px;">Customer</strong>
<div style="color:#1a2035;font-size:1.05rem;font-weight:600;margin-top:3px;">' . htmlspecialchars($data['customer_name']) . '</div></div>
<div style="margin-bottom:15px"><strong style="color:#25D366;font-size:0.78rem;text-transform:uppercase;letter-spacing:1px;">Phone</strong>
<div style="margin-top:3px;"><a href="tel:' . htmlspecialchars($data['customer_phone']) . '" style="color:#25D366;text-decoration:none;font-weight:600;">' . htmlspecialchars($data['customer_phone']) . '</a></div></div>';

        if (!empty($data['customer_address'])) {
            $content .= '<div style="margin-bottom:15px"><strong style="color:#25D366;font-size:0.78rem;text-transform:uppercase;letter-spacing:1px;">Delivery Address</strong>
<div style="color:#1a2035;margin-top:3px;">' . nl2br(htmlspecialchars($data['customer_address'])) . '</div></div>';
        }

        $content .= '</td></tr></table>';

        if (!empty($itemsHtml)) {
            $content .= '
<table width="100%" cellpadding="0" cellspacing="0" style="background:white;border:1px solid #e5e7eb;border-radius:12px;padding:20px;margin-bottom:20px;">
<tr><td>
<strong style="color:#25D366;font-size:0.78rem;text-transform:uppercase;letter-spacing:1px;">Items Ordered</strong>
<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:12px;">
' . $itemsHtml . '
<tr><td style="padding:12px 0 0;font-weight:700;color:#1a2035;">Total</td>
<td style="padding:12px 0 0;text-align:right;font-weight:700;color:#25D366;font-size:1.2rem;">₹' . number_format($data['total_amount'], 2) . '</td></tr>
</table>
</td></tr>
</table>';
        }

        $phoneClean = preg_replace('/\D/', '', $data['customer_phone']);
        $content .= '
<div style="text-align:center;margin-top:30px;">
<a href="https://wa.me/' . $phoneClean . '" style="display:inline-block;background:#25D366;color:white;padding:12px 24px;border-radius:50px;text-decoration:none;font-weight:600;margin:5px;">📱 WhatsApp Customer</a>
<a href="tel:' . htmlspecialchars($data['customer_phone']) . '" style="display:inline-block;background:#3b82f6;color:white;padding:12px 24px;border-radius:50px;text-decoration:none;font-weight:600;margin:5px;">📞 Call</a>
</div>
<p style="color:#6b7280;font-size:0.85rem;text-align:center;margin-top:25px;">Manage orders in your <a href="' . SITE_URL . '/whatsapp-orders.html" style="color:#25D366;font-weight:600;">Orders Dashboard</a></p>';

        return [
            'subject' => '🛍️ New Order #' . $data['order_id'] . ' - ₹' . number_format($data['total_amount'], 2),
            'html' => self::baseTemplate('New Order', $content, '#25D366')
        ];
    }

    /**
     * TEST EMAIL
     */
    public static function testEmail() {
        $content = '
<h2 style="color:#1a2035;margin:0 0 8px;font-size:1.4rem;">✓ Test Email Successful!</h2>
<p style="color:#6b7280;margin:0 0 25px;">Your Tapify email notification system is working correctly.</p>

<div style="background:#d1fae5;border-left:4px solid #10b981;border-radius:8px;padding:18px;margin-bottom:25px;">
<p style="color:#065f46;margin:0;font-weight:600;">🎉 Email configuration is correct!</p>
<p style="color:#065f46;margin:8px 0 0;font-size:0.92rem;">You will now receive notifications for new inquiries, appointments, and orders.</p>
</div>

<p style="color:#6b7280;font-size:0.92rem;">If you received this email, your SMTP settings are configured correctly. You can now expect to receive automated notifications from Tapify.</p>

<p style="color:#9ca3af;font-size:0.85rem;margin-top:20px;">Sent on ' . date('d M Y, h:i A') . '</p>';

        return [
            'subject' => '✓ Tapify Email Test - Configuration Working',
            'html' => self::baseTemplate('Test Email', $content, '#10b981')
        ];
    }
}
