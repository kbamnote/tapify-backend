<?php
/**
 * TAPIFY vCard Theme Registry
 * One entry per template_id (vcard1–vcard42). Used by _render.php.
 *
 * @return array<string, array<string, mixed>>
 */
return [
    'vcard1'  => ['name' => 'Simple Contact', 'layout' => 'legacy', 'legacy' => 'default'],
    'vcard2'  => ['name' => 'Executive Profile', 'layout' => 'executive', 'primary' => '#1e3a5f', 'secondary' => '#3b82f6', 'bg' => '#f8fafc', 'surface' => '#ffffff', 'font' => 'montserrat'],
    'vcard3'  => ['name' => 'Clean Canvas', 'layout' => 'minimal', 'primary' => '#64748b', 'secondary' => '#94a3b8', 'bg' => '#ffffff', 'surface' => '#f8fafc', 'font' => 'inter'],
    'vcard4'  => ['name' => 'Professional', 'layout' => 'classic', 'primary' => '#2563eb', 'secondary' => '#1d4ed8', 'bg' => '#ffffff', 'surface' => '#f1f5f9', 'font' => 'poppins'],
    'vcard5'  => ['name' => 'Corporate Connect', 'layout' => 'corporate', 'primary' => '#0f172a', 'secondary' => '#334155', 'bg' => '#f1f5f9', 'surface' => '#ffffff', 'font' => 'inter'],
    'vcard6'  => ['name' => 'Modern Edge', 'layout' => 'bold', 'primary' => '#7c3aed', 'secondary' => '#a855f7', 'bg' => '#faf5ff', 'surface' => '#ffffff', 'font' => 'poppins'],
    'vcard7'  => ['name' => 'Business Beacon', 'layout' => 'wave', 'primary' => '#ea580c', 'secondary' => '#f97316', 'bg' => '#fff7ed', 'surface' => '#ffffff', 'font' => 'raleway'],
    'vcard8'  => ['name' => 'Corporate Classic', 'layout' => 'sidebar', 'primary' => '#1e293b', 'secondary' => '#475569', 'bg' => '#ffffff', 'surface' => '#f8fafc', 'font' => 'merriweather'],
    'vcard9'  => ['name' => 'Corporate Identity', 'layout' => 'split', 'primary' => '#0369a1', 'secondary' => '#0284c7', 'bg' => '#f0f9ff', 'surface' => '#ffffff', 'font' => 'inter'],
    'vcard10' => ['name' => 'Pro Network', 'layout' => 'floating', 'primary' => '#059669', 'secondary' => '#10b981', 'bg' => '#ecfdf5', 'surface' => '#ffffff', 'font' => 'poppins'],
    'vcard11' => ['name' => 'Portfolio', 'layout' => 'portfolio', 'primary' => '#18181b', 'secondary' => '#71717a', 'bg' => '#fafafa', 'surface' => '#ffffff', 'font' => 'oswald'],
    'vcard12' => ['name' => 'Gym', 'layout' => 'neon', 'primary' => '#dc2626', 'secondary' => '#f97316', 'bg' => '#0a0a0a', 'surface' => '#171717', 'font' => 'oswald', 'dark' => true],
    'vcard29' => ['name' => 'Marriage', 'layout' => 'wedding', 'primary' => '#9f1239', 'secondary' => '#fda4af', 'bg' => '#fff1f2', 'surface' => '#ffffff', 'font' => 'cormorant'],
    'vcard30' => ['name' => 'Taxi Service', 'layout' => 'transport', 'primary' => '#facc15', 'secondary' => '#1f2937', 'bg' => '#111827', 'surface' => '#1f2937', 'font' => 'montserrat', 'dark' => true],
    'vcard31' => ['name' => 'Handyman Services', 'layout' => 'industrial', 'primary' => '#b45309', 'secondary' => '#78350f', 'bg' => '#fef3c7', 'surface' => '#ffffff', 'font' => 'roboto'],
    'vcard32' => ['name' => 'Interior Designer', 'layout' => 'designer', 'primary' => '#57534e', 'secondary' => '#a8a29e', 'bg' => '#fafaf9', 'surface' => '#ffffff', 'font' => 'lora'],
    'vcard33' => ['name' => 'Musician', 'layout' => 'creative', 'primary' => '#7e22ce', 'secondary' => '#c026d3', 'bg' => '#1e1b4b', 'surface' => '#312e81', 'font' => 'oswald', 'dark' => true],
    'vcard34' => ['name' => 'Photographer', 'layout' => 'photo', 'primary' => '#000000', 'secondary' => '#525252', 'bg' => '#0a0a0a', 'surface' => '#171717', 'font' => 'inter', 'dark' => true],
    'vcard35' => ['name' => 'Real Estate', 'layout' => 'legacy', 'legacy' => 'real-estate'],
    'vcard36' => ['name' => 'Travel Agency', 'layout' => 'travel', 'primary' => '#0284c7', 'secondary' => '#38bdf8', 'bg' => '#f0f9ff', 'surface' => '#ffffff', 'font' => 'raleway'],
    'vcard37' => ['name' => 'Flower Garden', 'layout' => 'nature', 'primary' => '#16a34a', 'secondary' => '#86efac', 'bg' => '#f0fdf4', 'surface' => '#ffffff', 'font' => 'lora'],
    'vcard38' => ['name' => 'Architecture', 'layout' => 'architect', 'primary' => '#334155', 'secondary' => '#64748b', 'bg' => '#f8fafc', 'surface' => '#ffffff', 'font' => 'montserrat'],
    'vcard39' => ['name' => 'Bio Black', 'layout' => 'bio-dark', 'primary' => '#fafafa', 'secondary' => '#a3a3a3', 'bg' => '#0a0a0a', 'surface' => '#171717', 'font' => 'inter', 'dark' => true],
    'vcard40' => ['name' => 'Bio White', 'layout' => 'bio-light', 'primary' => '#171717', 'secondary' => '#737373', 'bg' => '#ffffff', 'surface' => '#fafafa', 'font' => 'inter'],
    'vcard41' => ['name' => 'Social Vcard', 'layout' => 'social-grid', 'primary' => '#6366f1', 'secondary' => '#818cf8', 'bg' => '#eef2ff', 'surface' => '#ffffff', 'font' => 'poppins'],
    'vcard42' => ['name' => 'Social Vcard 2', 'layout' => 'social-stack', 'primary' => '#0ea5e9', 'secondary' => '#38bdf8', 'bg' => '#f0f9ff', 'surface' => '#ffffff', 'font' => 'poppins'],

    // ===== PREMIUM STANDALONE TEMPLATES (vcard01–vcard28) =====
    'vcard01' => ['name' => 'Corporate Executive', 'layout' => 'luxury',      'primary' => '#c9a84c', 'secondary' => '#f0c96a', 'bg' => '#0a1628', 'surface' => '#0d1e38', 'font' => 'montserrat', 'dark' => true],
    'vcard02' => ['name' => 'Medical Doctor',       'layout' => 'clinic',      'primary' => '#0d9488', 'secondary' => '#14b8a6', 'bg' => '#f8fafc', 'surface' => '#ffffff', 'font' => 'inter'],
    'vcard03' => ['name' => 'Creative Designer',    'layout' => 'creative',    'primary' => '#7c3aed', 'secondary' => '#e879f9', 'bg' => '#0c0512', 'surface' => '#1a1025', 'font' => 'poppins', 'dark' => true],
    'vcard04' => ['name' => 'Real Estate',          'layout' => 'legacy',      'legacy' => 'real-estate'],
    'vcard05' => ['name' => 'Restaurant Chef',      'layout' => 'neon',        'primary' => '#c0392b', 'secondary' => '#d4af37', 'bg' => '#1a0a0a', 'surface' => '#221010', 'font' => 'oswald', 'dark' => true],
    'vcard06' => ['name' => 'Fitness Trainer',      'layout' => 'neon',        'primary' => '#f97316', 'secondary' => '#fbbf24', 'bg' => '#0d0d0d', 'surface' => '#181818', 'font' => 'oswald', 'dark' => true],
    'vcard07' => ['name' => 'Tech Developer',       'layout' => 'tech',        'primary' => '#00e5ff', 'secondary' => '#00ff88', 'bg' => '#050d12', 'surface' => '#0a1a22', 'font' => 'inter',  'dark' => true],
    'vcard08' => ['name' => 'Lawyer & Legal',       'layout' => 'sidebar',     'primary' => '#0b1c3d', 'secondary' => '#b8952a', 'bg' => '#f4f6f9', 'surface' => '#ffffff', 'font' => 'merriweather'],
    'vcard09' => ['name' => 'Beauty Salon',         'layout' => 'soft',        'primary' => '#c9796a', 'secondary' => '#e8b898', 'bg' => '#fff8f8', 'surface' => '#fff2f2', 'font' => 'lora'],
    'vcard10' => ['name' => 'Musician & Artist',    'layout' => 'creative',    'primary' => '#7b2fff', 'secondary' => '#ff2d9e', 'bg' => '#04050e', 'surface' => '#090b1a', 'font' => 'oswald', 'dark' => true],
    'vcard11' => ['name' => 'Photographer',         'layout' => 'photo',       'primary' => '#d4a853', 'secondary' => '#f0c878', 'bg' => '#0a0a0a', 'surface' => '#111111', 'font' => 'inter',  'dark' => true],
    'vcard12' => ['name' => 'Financial Advisor',    'layout' => 'consulting',  'primary' => '#0f2b52', 'secondary' => '#2563eb', 'bg' => '#f0f4f8', 'surface' => '#ffffff', 'font' => 'inter'],
    'vcard13' => ['name' => 'Architect',            'layout' => 'architect',   'primary' => '#1c1c1c', 'secondary' => '#c45c26', 'bg' => '#f7f5f0', 'surface' => '#ffffff', 'font' => 'montserrat'],
    'vcard14' => ['name' => 'Yoga & Wellness',      'layout' => 'nature',      'primary' => '#7a9e7e', 'secondary' => '#a8c5a0', 'bg' => '#f5f0eb', 'surface' => '#ffffff', 'font' => 'lora'],
    'vcard15' => ['name' => 'Digital Marketing',    'layout' => 'bold',        'primary' => '#6c47ff', 'secondary' => '#ff47b8', 'bg' => '#060614', 'surface' => '#0d0d28', 'font' => 'poppins', 'dark' => true],
    'vcard16' => ['name' => 'Interior Designer',    'layout' => 'designer',    'primary' => '#c17f5c', 'secondary' => '#e8a07a', 'bg' => '#f9f6f1', 'surface' => '#ffffff', 'font' => 'lora'],
    'vcard17' => ['name' => 'Wedding Planner',      'layout' => 'wedding',     'primary' => '#c9607a', 'secondary' => '#e8849a', 'bg' => '#fdf8fc', 'surface' => '#ffffff', 'font' => 'cormorant'],
    'vcard18' => ['name' => 'Dentist',              'layout' => 'clinic',      'primary' => '#0891b2', 'secondary' => '#06b6d4', 'bg' => '#f0fffe', 'surface' => '#ffffff', 'font' => 'inter'],
    'vcard19' => ['name' => 'CA & Accountant',      'layout' => 'luxury',      'primary' => '#d4a017', 'secondary' => '#f0c040', 'bg' => '#0e1a2b', 'surface' => '#132035', 'font' => 'montserrat', 'dark' => true],
    'vcard20' => ['name' => 'School Teacher',       'layout' => 'education',   'primary' => '#f59e0b', 'secondary' => '#fbbf24', 'bg' => '#fffbf0', 'surface' => '#ffffff', 'font' => 'nunito'],
    'vcard21' => ['name' => 'Fashion Designer',     'layout' => 'elegant',     'primary' => '#e8b4b8', 'secondary' => '#c9a96e', 'bg' => '#0c0c0c', 'surface' => '#161616', 'font' => 'cormorant', 'dark' => true],
    'vcard22' => ['name' => 'Travel Agent',         'layout' => 'travel',      'primary' => '#0ea5e9', 'secondary' => '#38bdf8', 'bg' => '#f0f9ff', 'surface' => '#ffffff', 'font' => 'raleway'],
    'vcard23' => ['name' => 'Automobile Dealer',    'layout' => 'neon',        'primary' => '#dc2626', 'secondary' => '#c0c8d4', 'bg' => '#0a0a0a', 'surface' => '#141414', 'font' => 'montserrat', 'dark' => true],
    'vcard24' => ['name' => 'Event Planner',        'layout' => 'festive',     'primary' => '#9333ea', 'secondary' => '#ec4899', 'bg' => '#0d0010', 'surface' => '#150a1a', 'font' => 'poppins', 'dark' => true],
    'vcard25' => ['name' => 'Pharma & Medical',     'layout' => 'ngo',         'primary' => '#16a34a', 'secondary' => '#22c55e', 'bg' => '#f0fdf4', 'surface' => '#f7fef9', 'font' => 'poppins'],
    'vcard26' => ['name' => 'NGO & Social',         'layout' => 'ngo',         'primary' => '#7c3aed', 'secondary' => '#a78bfa', 'bg' => '#f5f3ff', 'surface' => '#faf5ff', 'font' => 'poppins'],
    'vcard27' => ['name' => 'Coaching Institute',   'layout' => 'consulting',  'primary' => '#2563eb', 'secondary' => '#3b82f6', 'bg' => '#eff6ff', 'surface' => '#f0f9ff', 'font' => 'poppins'],
    'vcard28' => ['name' => 'Electrician & Contractor', 'layout' => 'industrial', 'primary' => '#d97706', 'secondary' => '#f59e0b', 'bg' => '#fffbeb', 'surface' => '#fffdf5', 'font' => 'roboto'],

    // ===== PREMIUM EXACT-DESIGN TEMPLATES (from newTemps, hosted assets) =====
    'vcard43' => ['name' => 'Stylish Salon', 'layout' => 'standalone', 'primary' => '#a4866d', 'secondary' => '#c9a96e', 'bg' => '#ffffff', 'surface' => '#ffffff', 'font' => 'cormorant'],
];
