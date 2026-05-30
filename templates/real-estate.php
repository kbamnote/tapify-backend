<?php
/**
 * REAL ESTATE TEMPLATE — Luxury Warm (matches tapifyworld.com/real-estate)
 */
$primaryColor = !empty($vcard['primary_color']) ? $vcard['primary_color'] : '#3d1f00';
$bgColor      = !empty($vcard['bg_color'])      ? $vcard['bg_color']      : '#f5e6cc';
$accentColor  = !empty($vcard['secondary_color']) ? $vcard['secondary_color'] : '#b8860b';
$VCDN = 'https://tapifyworld.com/assets/img/vcard35/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($fullName) ?></title>
    <meta name="description" content="<?= htmlspecialchars(strip_tags($vcard['description'] ?? '')) ?>">
    <link rel="icon" type="image/png" href="<?= $vcard['favicon_image'] ? imgUrl($vcard['favicon_image']) : '/images/tapify-logo-green.png' ?>">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: <?= $primaryColor ?>;
            --accent:  <?= $accentColor ?>;
            --bg:      <?= $bgColor ?>;
            --warm:    #f5e6cc;
            --card:    #ffffff;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--warm);
            min-height: 100vh;
            color: var(--primary);
            position: relative;
            overflow-x: hidden;
        }

        /* ── Decorative background vectors ── */
        .re-vec {
            position: fixed;
            pointer-events: none;
            z-index: 0;
            opacity: 0.92;
        }
        .re-vec-tl  { top: 0;    left: -10px; width: 200px; }
        .re-vec-bl  { bottom: 0; left: -10px; width: 220px; }
        .re-vec-tr  { top: 0;    right: -10px; width: 180px; }
        .re-vec-br  { bottom: 0; right: -10px; width: 200px; }
        .re-vec-ml  { top: 35%;  left: -15px;  width: 160px; }
        .re-vec-mr  { top: 30%;  right: -15px; width: 160px; }
        .re-bg-mid  { top: 50%;  left: 50%;
                      transform: translate(-50%, -50%);
                      width: 100vw; min-height: 100vh;
                      z-index: -1; opacity: 0.07; object-fit: cover; }

        @media (max-width: 600px) { .re-vec { display: none; } }

        /* ── Card container ── */
        .vcard-container {
            max-width: 480px;
            margin: 0 auto;
            background: var(--card);
            min-height: 100vh;
            box-shadow: 0 0 60px rgba(61,31,0,0.18);
            position: relative;
            z-index: 1;
        }

        /* ── Cover ── */
        .cover-section {
            height: 260px;
            background: linear-gradient(135deg, var(--primary), #5c3010);
            position: relative;
            overflow: hidden;
        }
        .cover-section img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .cover-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(180deg, rgba(61,31,0,0.15) 0%, rgba(61,31,0,0.5) 100%);
        }
        .cover-badge {
            position: absolute; top: 18px; left: 18px; right: 18px;
            display: flex; justify-content: space-between; z-index: 5;
        }
        .badge-pill {
            padding: 5px 14px;
            font-size: 0.65rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 2px;
        }
        .badge-pill.agent { background: var(--accent); color: #fff; }
        .badge-pill.luxury { background: rgba(255,255,255,0.92); color: var(--primary); }

        /* ── Profile ── */
        .profile-section {
            text-align: center;
            padding: 0 24px 28px;
            background: #fff;
            position: relative; z-index: 5;
        }
        .profile-photo {
            width: 120px; height: 120px;
            border-radius: 50%;
            border: 5px solid #fff;
            box-shadow: 0 8px 30px rgba(61,31,0,0.2);
            margin: -60px auto 16px;
            overflow: hidden;
            background: var(--warm);
        }
        .profile-photo img { width: 100%; height: 100%; object-fit: cover; }
        .profile-photo .placeholder {
            width: 100%; height: 100%;
            background: var(--primary); color: var(--accent);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Playfair Display'; font-size: 2.8rem; font-weight: 700;
        }
        .profile-name {
            font-family: 'Playfair Display';
            font-size: 1.7rem; font-weight: 700;
            color: var(--primary); margin-bottom: 4px;
        }
        .profile-title {
            color: var(--accent); font-size: 0.78rem;
            text-transform: uppercase; letter-spacing: 3px;
            font-weight: 600; margin-bottom: 10px;
        }
        .profile-divider {
            width: 70px; height: 2px;
            background: var(--accent);
            margin: 14px auto;
            position: relative;
        }
        .profile-divider::before {
            content: '◆';
            position: absolute; top: 50%; left: 50%;
            transform: translate(-50%,-50%);
            background: #fff; color: var(--accent);
            padding: 0 6px; font-size: 0.65rem;
        }
        .profile-company {
            color: #6b4c2a; font-size: 0.85rem; margin-bottom: 12px;
            display: flex; align-items: center; justify-content: center; gap: 6px;
        }
        .profile-desc {
            font-family: 'Playfair Display';
            font-size: 1rem; font-style: italic;
            color: #5a3e28; line-height: 1.7;
            margin-bottom: 22px;
        }
        .save-contact-btn {
            display: inline-flex; align-items: center; gap: 10px;
            background: var(--primary); color: #fff;
            padding: 13px 32px;
            border: 2px solid var(--accent);
            font-size: 0.75rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 2.5px;
            text-decoration: none; cursor: pointer;
            transition: all 0.3s;
        }
        .save-contact-btn:hover { background: var(--accent); }

        /* ── Quick actions ── */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            background: var(--warm);
            gap: 1px;
        }
        .quick-action {
            background: #fff;
            padding: 16px 8px; text-align: center;
            text-decoration: none; color: inherit;
            display: flex; flex-direction: column;
            align-items: center; gap: 7px;
            transition: all 0.25s;
        }
        .quick-action:hover { background: var(--primary); }
        .quick-action:hover i { color: var(--accent); }
        .quick-action:hover span { color: #fff; }
        .quick-action i { font-size: 1.2rem; color: var(--accent); }
        .quick-action span {
            font-size: 0.6rem; font-weight: 600;
            color: #7a5c3a; text-transform: uppercase; letter-spacing: 0.8px;
        }

        /* ── Sections ── */
        .section { padding: 28px 22px; border-top: 1px solid #e8d5b7; }
        .section-title {
            font-family: 'Playfair Display';
            font-size: 1.5rem; font-weight: 600;
            color: var(--primary); margin-bottom: 20px;
            text-align: center; position: relative; padding-bottom: 12px;
        }
        .section-title::after {
            content: ''; position: absolute;
            bottom: 0; left: 50%; transform: translateX(-50%);
            width: 44px; height: 2px; background: var(--accent);
        }
        .section-title i { display: none; }

        /* ── Contact grid ── */
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .contact-box {
            background: var(--warm);
            border: 1px solid #ddc89a;
            border-radius: 8px;
            padding: 14px 12px;
            display: flex; align-items: center; gap: 12px;
            text-decoration: none; color: inherit;
            transition: all 0.25s;
            word-break: break-all;
        }
        .contact-box:hover { background: var(--accent); color: #fff; border-color: var(--accent); }
        .contact-box:hover .cb-icon { background: #fff; color: var(--accent); }
        .contact-box:hover .cb-label,
        .contact-box:hover .cb-val  { color: #fff; }
        .cb-icon {
            width: 36px; height: 36px; flex-shrink: 0;
            border-radius: 50%;
            background: var(--accent); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.9rem;
        }
        .cb-label { font-size: 0.6rem; font-weight: 700; color: var(--accent); text-transform: uppercase; letter-spacing: 1px; }
        .cb-val   { font-size: 0.78rem; font-weight: 500; color: var(--primary); margin-top: 2px; }

        /* ── Gallery ── */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
        }
        .gallery-img {
            aspect-ratio: 1; overflow: hidden;
            background: var(--warm); cursor: pointer;
        }
        .gallery-img img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
        .gallery-img:hover img { transform: scale(1.08); }

        /* ── Services ── */
        .services-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .service-card {
            background: var(--warm);
            border-radius: 10px;
            overflow: hidden;
            text-decoration: none; color: inherit;
            transition: all 0.25s;
            border: 1px solid #ddc89a;
        }
        .service-card:hover { box-shadow: 0 6px 20px rgba(61,31,0,0.14); transform: translateY(-3px); }
        .service-card-img {
            width: 100%; height: 110px;
            background: #d4b896; overflow: hidden;
        }
        .service-card-img img { width: 100%; height: 100%; object-fit: cover; }
        .service-card-img .si-placeholder {
            width: 100%; height: 100%;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Playfair Display'; font-size: 2rem;
            color: var(--accent); font-weight: 700;
        }
        .service-card-body { padding: 12px 10px; }
        .service-card-name { font-weight: 700; font-size: 0.82rem; color: var(--primary); margin-bottom: 4px; }
        .service-card-desc { font-size: 0.72rem; color: #7a5c3a; line-height: 1.5; }

        /* ── Appointment ── */
        .appt-form { display: flex; flex-direction: column; gap: 12px; }
        .appt-input {
            width: 100%; padding: 12px 16px;
            border: 1px solid #ddc89a; background: var(--warm);
            font-family: inherit; font-size: 0.88rem; color: var(--primary);
            outline: none; border-radius: 6px;
        }
        .appt-input:focus { border-color: var(--accent); }
        .appt-btn {
            background: var(--primary); color: #fff;
            padding: 13px 24px; border: 2px solid var(--accent);
            font-weight: 700; cursor: pointer; font-size: 0.78rem;
            text-transform: uppercase; letter-spacing: 2px;
            transition: all 0.25s; width: 100%;
        }
        .appt-btn:hover { background: var(--accent); }

        /* ── Products ── */
        .products-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .product-card {
            background: var(--warm); overflow: hidden;
            text-decoration: none; color: inherit;
            border: 1px solid #ddc89a; border-radius: 10px;
            transition: all 0.25s;
        }
        .product-card:hover { box-shadow: 0 6px 20px rgba(61,31,0,0.14); }
        .product-image { width: 100%; aspect-ratio: 4/3; overflow: hidden; background: #d4b896; }
        .product-image img { width: 100%; height: 100%; object-fit: cover; }
        .product-image .pi-placeholder {
            width: 100%; height: 100%;
            display: flex; align-items: center; justify-content: center;
            color: #b8966c; font-size: 2rem;
        }
        .product-info { padding: 12px; }
        .product-name { font-weight: 600; font-size: 0.85rem; color: var(--primary); margin-bottom: 4px; }
        .product-price { color: var(--accent); font-family: 'Playfair Display'; font-size: 1.1rem; font-weight: 700; }
        .view-more-btn {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            margin-top: 14px; padding: 11px 18px;
            background: var(--warm); border: 1.5px solid #ddc89a;
            color: var(--primary); text-decoration: none;
            font-size: 0.8rem; font-weight: 600;
            border-radius: 6px; transition: all 0.25s;
        }
        .view-more-btn:hover { background: var(--accent); color: #fff; border-color: var(--accent); }

        /* ── Testimonials ── */
        .testimonial {
            background: var(--warm); padding: 24px 20px;
            border: 1px solid #ddc89a; border-radius: 10px;
            position: relative; margin-bottom: 12px;
        }
        .testimonial::before {
            content: '\201C';
            position: absolute; top: -16px; left: 18px;
            font-family: 'Playfair Display'; font-size: 4.5rem;
            color: var(--accent); line-height: 1;
        }
        .tst-stars { color: var(--accent); margin-bottom: 8px; font-size: 0.85rem; }
        .tst-msg {
            font-family: 'Playfair Display'; font-size: 1rem;
            line-height: 1.65; color: #4a3020; font-style: italic; margin-bottom: 14px;
        }
        .tst-author-row { display: flex; align-items: center; gap: 12px; }
        .tst-avatar {
            width: 42px; height: 42px; border-radius: 50%;
            overflow: hidden; background: var(--primary);
            flex-shrink: 0; display: flex; align-items: center;
            justify-content: center; color: var(--accent); font-weight: 700; font-size: 1.1rem;
        }
        .tst-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .tst-name { font-weight: 700; font-size: 0.85rem; color: var(--primary); text-transform: uppercase; letter-spacing: 0.5px; }
        .tst-meta { font-size: 0.72rem; color: #8a6848; margin-top: 2px; }

        /* ── Instagram ── */
        .insta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
        .insta-cell {
            aspect-ratio: 1; background: var(--warm);
            border: 1px solid #ddc89a; border-radius: 8px;
            overflow: hidden; display: flex; align-items: center;
            justify-content: center; color: #b8966c; font-size: 2rem;
        }
        .insta-cell img { width: 100%; height: 100%; object-fit: cover; }

        /* ── Business Hours ── */
        .hours-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .hours-box {
            background: var(--warm);
            border: 1px solid #ddc89a;
            border-radius: 8px;
            padding: 12px 14px;
            display: flex; align-items: center; gap: 10px;
        }
        .hours-box.closed { opacity: 0.55; }
        .hours-box-icon {
            width: 34px; height: 34px; flex-shrink: 0;
            border-radius: 50%;
            background: var(--accent); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.8rem;
        }
        .hours-day { font-size: 0.72rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.8px; }
        .hours-time { font-size: 0.72rem; color: #7a5c3a; margin-top: 2px; }

        /* ── Blog ── */
        .blog-card {
            background: var(--warm); border-radius: 10px;
            overflow: hidden; margin-bottom: 14px;
            border: 1px solid #ddc89a; text-decoration: none; color: inherit;
            display: block; transition: all 0.25s;
        }
        .blog-card:hover { box-shadow: 0 6px 20px rgba(61,31,0,0.12); }
        .blog-img { width: 100%; height: 175px; overflow: hidden; background: #d4b896; }
        .blog-img img { width: 100%; height: 100%; object-fit: cover; }
        .blog-body { padding: 16px; }
        .blog-date { font-size: 0.7rem; color: var(--accent); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; }
        .blog-title {
            font-family: 'Playfair Display'; font-size: 1.15rem;
            font-weight: 600; color: var(--primary); margin-bottom: 6px;
        }
        .blog-excerpt { font-size: 0.8rem; color: #7a5c3a; line-height: 1.6; margin-bottom: 12px; }
        .blog-read-more {
            font-size: 0.75rem; font-weight: 700;
            color: var(--accent); text-transform: uppercase; letter-spacing: 1.5px;
            display: inline-flex; align-items: center; gap: 5px;
        }

        /* ── QR Code ── */
        .qr-wrap {
            display: flex; align-items: center; gap: 18px;
            background: var(--warm); border: 1px solid #ddc89a;
            border-radius: 10px; padding: 18px;
        }
        .qr-wrap img { width: 90px; height: 90px; flex-shrink: 0; border-radius: 6px; }
        .qr-title { font-weight: 700; font-size: 0.9rem; color: var(--primary); margin-bottom: 4px; }
        .qr-sub { font-size: 0.78rem; color: #7a5c3a; line-height: 1.5; }

        /* ── Custom links ── */
        .custom-link {
            background: var(--warm); padding: 14px 18px;
            display: flex; align-items: center; gap: 14px;
            text-decoration: none; color: inherit; margin-bottom: 8px;
            transition: all 0.25s; border: 1px solid #ddc89a; border-radius: 8px;
        }
        .custom-link:hover { background: var(--accent); color: #fff; }
        .custom-link i { color: var(--accent); font-size: 1.1rem; }
        .custom-link:hover i { color: #fff; }
        .custom-link span { font-weight: 600; font-size: 0.88rem; }

        /* ── Social icons ── */
        .social-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 1px; background: #ddc89a; border-radius: 8px; overflow: hidden; }
        .social-icon { aspect-ratio: 1; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.2rem; text-decoration: none; transition: opacity 0.25s; }
        .social-icon:hover { opacity: 0.85; }
        .social-Facebook  { background: #1877F2; } .social-Instagram { background: linear-gradient(45deg,#f09433,#dc2743); }
        .social-Twitter   { background: #1DA1F2; } .social-LinkedIn  { background: #0077B5; }
        .social-WhatsApp  { background: #25D366; } .social-YouTube   { background: #FF0000; }
        .social-Pinterest { background: #E60023; } .social-TikTok    { background: #000; }
        .social-Snapchat  { background: #FFFC00; color: #000; }

        /* ── Inquiry form ── */
        .inquiry-form { display: flex; flex-direction: column; gap: 12px; }
        .form-group { display: flex; flex-direction: column; gap: 5px; }
        .form-group label { font-size: 0.68rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 1.5px; }
        .form-group input,
        .form-group textarea,
        .form-group select {
            padding: 11px 14px; border: 1px solid #ddc89a;
            background: var(--warm); font-family: inherit; font-size: 0.88rem;
            color: var(--primary); border-radius: 6px; outline: none;
        }
        .form-group input:focus,
        .form-group textarea:focus { border-color: var(--accent); }
        .submit-btn {
            background: var(--primary); color: #fff;
            padding: 13px 24px; border: 2px solid var(--accent);
            font-weight: 700; cursor: pointer; font-size: 0.78rem;
            text-transform: uppercase; letter-spacing: 2px;
            transition: all 0.25s; border-radius: 0;
        }
        .submit-btn:hover { background: var(--accent); }

        /* ── Footer ── */
        .vcard-footer {
            text-align: center; padding: 26px 20px;
            color: #9a7a5a; font-size: 0.76rem;
            border-top: 1px solid #e8d5b7;
            background: var(--warm);
        }
        .vcard-footer a { color: var(--accent); text-decoration: none; font-weight: 600; }
        .view-counter {
            display: inline-flex; align-items: center; gap: 6px;
            color: var(--accent); margin-bottom: 10px;
            font-weight: 700; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1.5px;
        }
    </style>
</head>
<body>

<!-- ── Decorative background vectors ── -->
<img src="<?= $VCDN ?>vector-bg-1.png"  class="re-vec re-vec-tl"  alt="">
<img src="<?= $VCDN ?>vector-bg-3.png"  class="re-vec re-vec-bl"  alt="">
<img src="<?= $VCDN ?>vector-bg-5.png"  class="re-vec re-vec-tr"  alt="">
<img src="<?= $VCDN ?>vector-bg-7.png"  class="re-vec re-vec-br"  alt="">
<img src="<?= $VCDN ?>vector-bg-9.png"  class="re-vec re-vec-ml"  alt="">
<img src="<?= $VCDN ?>vector-bg-11.png" class="re-vec re-vec-mr"  alt="">

<div class="vcard-container">

    <!-- Cover -->
    <div class="cover-section">
        <?php if (!empty($vcard['cover_image'])): ?>
            <img src="<?= imgUrl($vcard['cover_image']) ?>" alt="Cover">
        <?php endif; ?>
        <div class="cover-overlay"></div>
        <div class="cover-badge">
            <span class="badge-pill agent">Realtor</span>
            <span class="badge-pill luxury">Luxury</span>
        </div>
    </div>

    <!-- Profile -->
    <div class="profile-section">
        <div class="profile-photo">
            <?php if (!empty($vcard['profile_image'])): ?>
                <img src="<?= imgUrl($vcard['profile_image']) ?>" alt="<?= htmlspecialchars($fullName) ?>">
            <?php else: ?>
                <div class="placeholder"><?= strtoupper(substr($fullName, 0, 1)) ?></div>
            <?php endif; ?>
        </div>
        <h1 class="profile-name"><?= htmlspecialchars($fullName) ?></h1>
        <?php if (!empty($vcard['occupation'])): ?>
            <p class="profile-title"><?= htmlspecialchars($vcard['occupation']) ?></p>
        <?php endif; ?>
        <div class="profile-divider"></div>
        <?php if (!empty($vcard['company'])): ?>
            <p class="profile-company"><i class="fas fa-building"></i> <?= htmlspecialchars($vcard['company']) ?></p>
        <?php endif; ?>
        <?php if (!empty($vcard['description'])): ?>
            <div class="profile-desc"><?= $vcard['description'] ?></div>
        <?php endif; ?>
        <a href="javascript:saveContact()" class="save-contact-btn">
            <i class="fas fa-bookmark"></i> Add to Contact
        </a>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <?php if (!empty($vcard['phone'])): ?>
            <a href="tel:<?= htmlspecialchars($vcard['phone']) ?>" class="quick-action"><i class="fas fa-phone"></i><span>Call</span></a>
            <a href="https://wa.me/<?= preg_replace('/\D/', '', $vcard['phone']) ?>" target="_blank" class="quick-action"><i class="fab fa-whatsapp"></i><span>Chat</span></a>
        <?php endif; ?>
        <?php if (!empty($vcard['email'])): ?>
            <a href="mailto:<?= htmlspecialchars($vcard['email']) ?>" class="quick-action"><i class="fas fa-envelope"></i><span>Email</span></a>
        <?php endif; ?>
        <?php if (!empty($vcard['location_url']) || !empty($vcard['location'])): ?>
            <a href="<?= !empty($vcard['location_url']) ? htmlspecialchars($vcard['location_url']) : 'https://maps.google.com/?q='.urlencode($vcard['location']) ?>" target="_blank" class="quick-action"><i class="fas fa-map-marker-alt"></i><span>Office</span></a>
        <?php endif; ?>
        <a href="javascript:shareCard()" class="quick-action"><i class="fas fa-share-alt"></i><span>Share</span></a>
    </div>

    <!-- Contact Section -->
    <?php
    $contactItems = [];
    if (!empty($vcard['email']))   $contactItems[] = ['icon'=>'fa-envelope','label'=>'Email','val'=>$vcard['email'],'href'=>'mailto:'.$vcard['email']];
    if (!empty($vcard['email2']))  $contactItems[] = ['icon'=>'fa-envelope','label'=>'Email 2','val'=>$vcard['email2'],'href'=>'mailto:'.$vcard['email2']];
    if (!empty($vcard['phone']))   $contactItems[] = ['icon'=>'fa-phone','label'=>'Phone','val'=>$vcard['phone'],'href'=>'tel:'.$vcard['phone']];
    if (!empty($vcard['phone2']))  $contactItems[] = ['icon'=>'fa-phone','label'=>'Phone 2','val'=>$vcard['phone2'],'href'=>'tel:'.$vcard['phone2']];
    if (!empty($vcard['location']))$contactItems[] = ['icon'=>'fa-map-marker-alt','label'=>'Location','val'=>$vcard['location'],'href'=>!empty($vcard['location_url'])?$vcard['location_url']:'https://maps.google.com/?q='.urlencode($vcard['location'])];
    if (!empty($vcard['dob']))     $contactItems[] = ['icon'=>'fa-calendar','label'=>'Date','val'=>$vcard['dob'],'href'=>'#'];
    ?>
    <?php if (!empty($contactItems)): ?>
    <div class="section">
        <h3 class="section-title">Contact</h3>
        <div class="contact-grid">
            <?php foreach ($contactItems as $ci): ?>
            <a href="<?= htmlspecialchars($ci['href']) ?>" class="contact-box">
                <div class="cb-icon"><i class="fas <?= $ci['icon'] ?>"></i></div>
                <div>
                    <div class="cb-label"><?= $ci['label'] ?></div>
                    <div class="cb-val"><?= htmlspecialchars($ci['val']) ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Gallery -->
    <?php if (!empty($galleries)): ?>
    <?php foreach ($galleries as $gallery): ?>
    <div class="section">
        <h3 class="section-title"><?= htmlspecialchars($gallery['name'] ?? 'Gallery') ?></h3>
        <div class="gallery-grid">
            <?php foreach (($gallery['images'] ?? []) as $img): ?>
            <div class="gallery-img" onclick="openImage('<?= imgUrl($img['image_url']) ?>')">
                <img src="<?= imgUrl($img['image_url']) ?>" alt="Gallery" loading="lazy">
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>

    <!-- Services -->
    <?php if (!empty($services)): ?>
    <div class="section">
        <h3 class="section-title">Our Services</h3>
        <div class="services-grid">
            <?php foreach ($services as $s): ?>
            <a href="<?= htmlspecialchars($s['service_url'] ?: '#') ?>" <?= $s['service_url'] ? 'target="_blank"' : '' ?> class="service-card">
                <div class="service-card-img">
                    <?php if (!empty($s['image'])): ?>
                        <img src="<?= imgUrl($s['image']) ?>" alt="<?= htmlspecialchars($s['name']) ?>">
                    <?php else: ?>
                        <div class="si-placeholder"><?= strtoupper(substr($s['name'],0,1)) ?></div>
                    <?php endif; ?>
                </div>
                <div class="service-card-body">
                    <div class="service-card-name"><?= htmlspecialchars($s['name']) ?></div>
                    <?php if (!empty($s['description'])): ?>
                        <div class="service-card-desc"><?= htmlspecialchars($s['description']) ?></div>
                    <?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Appointment -->
    <?php if (!empty($vcard['appointment_enabled'])): ?>
    <div class="section">
        <h3 class="section-title">Make an Appointment</h3>
        <div class="appt-form">
            <input type="date" class="appt-input" placeholder="Pick a Date" id="apptDate">
            <button class="appt-btn" onclick="bookAppointment()"><i class="fas fa-calendar-check"></i> Book Appointment</button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Products -->
    <?php if (!empty($products)): ?>
    <div class="section">
        <h3 class="section-title">Products</h3>
        <div class="products-grid">
            <?php foreach ($products as $p): ?>
            <a href="<?= htmlspecialchars($p['product_url'] ?: '#') ?>" <?= $p['product_url'] ? 'target="_blank"' : '' ?> class="product-card">
                <div class="product-image">
                    <?php if (!empty($p['image'])): ?>
                        <img src="<?= imgUrl($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                    <?php else: ?>
                        <div class="pi-placeholder"><i class="fas fa-home"></i></div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
                    <?php if ($p['price'] !== null): ?>
                        <div class="product-price">₹ <?= number_format((float)$p['price'], 2) ?></div>
                    <?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php if (!empty($storeUrl)): ?>
        <a href="<?= htmlspecialchars($storeUrl) ?>" target="_blank" class="view-more-btn">
            <i class="fas fa-store"></i> View More Products
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Testimonials -->
    <?php if (!empty($testimonials)): ?>
    <div class="section">
        <h3 class="section-title">Testimonials</h3>
        <?php foreach ($testimonials as $t): ?>
        <div class="testimonial">
            <?php $stars = (int)($t['rating'] ?? 5); ?>
            <div class="tst-stars"><?= str_repeat('★', $stars) ?><?= str_repeat('☆', 5-$stars) ?></div>
            <div class="tst-msg"><?= htmlspecialchars($t['message'] ?? '') ?></div>
            <div class="tst-author-row">
                <div class="tst-avatar">
                    <?php if (!empty($t['photo'])): ?>
                        <img src="<?= imgUrl($t['photo']) ?>" alt="">
                    <?php else: ?>
                        <?= strtoupper(substr($t['name'] ?? 'U', 0, 1)) ?>
                    <?php endif; ?>
                </div>
                <div>
                    <div class="tst-name"><?= htmlspecialchars($t['name'] ?? '') ?></div>
                    <?php if (!empty($t['designation'])): ?>
                        <div class="tst-meta"><?= htmlspecialchars($t['designation']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Instagram Feed -->
    <?php if (!empty($vcard['instagram_url'])): ?>
    <div class="section">
        <h3 class="section-title">Instagram Feed</h3>
        <div class="insta-grid">
            <div class="insta-cell"><i class="fab fa-instagram"></i></div>
            <div class="insta-cell"><i class="fab fa-instagram"></i></div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Business Hours -->
    <?php if (!empty($businessHours)): ?>
    <div class="section">
        <h3 class="section-title">Business Hours</h3>
        <div class="hours-grid">
            <?php foreach ($businessHours as $bh): ?>
            <div class="hours-box <?= $bh['is_open'] ? '' : 'closed' ?>">
                <div class="hours-box-icon"><i class="fas fa-clock"></i></div>
                <div>
                    <div class="hours-day"><?= ucfirst(strtolower($bh['day_name'])) ?></div>
                    <div class="hours-time"><?= $bh['is_open'] ? htmlspecialchars($bh['open_time'].' - '.$bh['close_time']) : 'Closed' ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Blog -->
    <?php if (!empty($blogs)): ?>
    <div class="section">
        <h3 class="section-title">Blog</h3>
        <?php foreach ($blogs as $b): ?>
        <a href="<?= htmlspecialchars($b['url'] ?? '#') ?>" target="_blank" class="blog-card">
            <?php if (!empty($b['image'])): ?>
            <div class="blog-img"><img src="<?= imgUrl($b['image']) ?>" alt="<?= htmlspecialchars($b['title']) ?>" loading="lazy"></div>
            <?php endif; ?>
            <div class="blog-body">
                <?php if (!empty($b['date'])): ?><div class="blog-date"><i class="fas fa-calendar"></i> <?= htmlspecialchars($b['date']) ?></div><?php endif; ?>
                <div class="blog-title"><?= htmlspecialchars($b['title']) ?></div>
                <?php if (!empty($b['excerpt'])): ?><div class="blog-excerpt"><?= htmlspecialchars($b['excerpt']) ?></div><?php endif; ?>
                <span class="blog-read-more">Read More <i class="fas fa-arrow-right"></i></span>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Custom Links -->
    <?php if (!empty($customLinks)): ?>
    <div class="section">
        <?php foreach ($customLinks as $cl): ?>
        <a href="<?= htmlspecialchars($cl['url']) ?>" target="_blank" class="custom-link">
            <i class="<?= htmlspecialchars($cl['icon'] ?? 'fas fa-link') ?>"></i>
            <span><?= htmlspecialchars($cl['label']) ?></span>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Social -->
    <?php if (!empty($socialLinks)): ?>
    <div class="social-grid">
        <?php foreach ($socialLinks as $sl): ?>
        <a href="<?= htmlspecialchars($sl['url']) ?>" target="_blank" class="social-icon social-<?= htmlspecialchars($sl['platform']) ?>">
            <i class="fab fa-<?= strtolower(htmlspecialchars($sl['platform'])) ?>"></i>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- QR Code -->
    <?php if (!empty($vcard['qr_code'])): ?>
    <div class="section">
        <h3 class="section-title">QR Code</h3>
        <div class="qr-wrap">
            <img src="<?= imgUrl($vcard['qr_code']) ?>" alt="QR Code">
            <div>
                <div class="qr-title">Scan to Contact</div>
                <div class="qr-sub">Scan the QR code to instantly add contact details and save to your device.</div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Inquiries -->
    <div class="section">
        <h3 class="section-title">Inquiries</h3>
        <form class="inquiry-form" onsubmit="submitInquiry(event)">
            <div class="form-group">
                <label>Your Name</label>
                <input type="text" name="name" placeholder="Enter your full name" required>
            </div>
            <div class="form-group">
                <label>Mobile Number</label>
                <input type="tel" name="phone" placeholder="Enter mobile number">
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="Enter email address">
            </div>
            <div class="form-group">
                <label>Type of Enquiry</label>
                <input type="text" name="subject" placeholder="e.g. Property Purchase">
            </div>
            <div class="form-group">
                <label>Message</label>
                <textarea name="message" rows="4" placeholder="Write your message…"></textarea>
            </div>
            <button type="submit" class="submit-btn"><i class="fas fa-paper-plane"></i> Send Message</button>
        </form>
    </div>

    <!-- Footer -->
    <div class="vcard-footer">
        <div class="view-counter"><i class="fas fa-eye"></i> <?= number_format((int)$vcard['view_count']) ?> Views</div>
        <p>Powered by <a href="/">Tapify</a></p>
        <p style="margin-top:3px;opacity:0.6;">A unit of <strong>Mr Print World</strong></p>
        <p style="margin-top:5px;font-size:0.68rem;">© <?= date('Y') ?> | All Rights Reserved</p>
    </div>

</div><!-- /vcard-container -->

<?php include __DIR__ . '/_shared-scripts.php'; ?>

<script>
function openImage(src) {
    const ov = document.createElement('div');
    ov.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.88);z-index:9999;display:flex;align-items:center;justify-content:center;cursor:pointer;';
    ov.innerHTML = '<img src="'+src+'" style="max-width:95vw;max-height:90vh;border-radius:8px;">';
    ov.onclick = () => ov.remove();
    document.body.appendChild(ov);
}
function bookAppointment() {
    const d = document.getElementById('apptDate')?.value;
    if (!d) { alert('Please select a date.'); return; }
    alert('Appointment requested for ' + d + '. We will contact you shortly.');
}
</script>

</body>
</html>
