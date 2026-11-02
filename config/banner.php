<?php

/*
 | Single source of truth for banner / brand limits.
 | Used by the server-side validation, the admin form hints and the browser-side checks.
 */
return [
    'image' => [
        'recommended' => '1920 x 1080 px (16:9, landscape)',
        'min_width' => 1280,
        'min_height' => 720,
        'max_width' => 4000,
        'max_height' => 3000,
        'max_kb' => 5120, // 5 MB
    ],

    'video' => [
        'recommended' => '1920 x 1080 px (16:9), MP4 (H.264), 10-30 seconds, no audio needed',
        'max_kb' => 51200, // 50 MB
    ],

    'logo' => [
        'recommended' => '400 x 120 px, transparent PNG',
        'min_width' => 100,
        'min_height' => 30,
        'max_width' => 1200,
        'max_height' => 400,
        'max_kb' => 1024, // 1 MB
    ],

    'favicon' => [
        'recommended' => '64 x 64 px (square), PNG or ICO',
        'max_kb' => 512, // 512 KB
    ],

    // Heading lines never wrap on the home page, so their length is capped to always fit.
    'text' => [
        'heading_min' => 2,
        'heading_max' => 18,
        'accent_min' => 2,
        'accent_max' => 24,
        'description_min' => 10,
        'description_max' => 200,
        'button_min' => 2,
        'button_max' => 20,
        'url_max' => 255,
        'order_max' => 999,
    ],

    'brand' => [
        'name_min' => 2,
        'name_max' => 30,
        'tagline_max' => 40,
    ],

    // Homepage About section
    'about_image' => [
        'recommended' => '1200 x 900 px (4:3 landscape or 1:1 square)',
        'min_width' => 400,
        'min_height' => 300,
        'max_width' => 4000,
        'max_height' => 4000,
        'max_kb' => 5120, // 5 MB
    ],
    'about' => [
        'eyebrow_max' => 40,
        'heading_min' => 5,
        'heading_max' => 120,
        'description_min' => 10,
        'description_max' => 400,
        'button_max' => 20,
        'alt_max' => 125,
    ],

    // Homepage text section (scroll-reveal heading). Plain text only: letters, digits, spaces and & , . ' -
    'home_text' => [
        'min' => 2,
        'max' => 30,      // characters per line
        'word_max' => 16, // longest single word, so a word can never overflow its container
    ],

    // Homepage "The Problem" section. Plain text only: letters, digits, spaces and . , ' - & ! ?
    'problem' => [
        'eyebrow_min' => 2,
        'eyebrow_max' => 30,
        'heading_min' => 5,
        'heading_max' => 60,
        'accent_min' => 5,
        'accent_max' => 60,
        'description_min' => 20,
        'description_max' => 300,
        'caption_min' => 2,
        'caption_max' => 30,
        'word_max' => 18,             // longest single word, so a word can never overflow its box
        'description_word_max' => 24,
        'image' => [
            'recommended' => '800 x 1000 px (4:5 portrait) or 1000 x 1000 px (square), JPG, PNG or WEBP',
            'min_width' => 400,
            'min_height' => 400,
            'max_width' => 4000,
            'max_height' => 4000,
            'max_kb' => 5120, // 5 MB
        ],
    ],

    // Homepage "Our Programs" section (text: letters, digits, spaces and . , ' - & ! ?)
    'program' => [
        'eyebrow_min' => 2,
        'eyebrow_max' => 30,
        'accent_min' => 5,
        'accent_max' => 40,
        'heading_min' => 5,
        'heading_max' => 40,
        'description_min' => 20,
        'description_max' => 200,
        'word_max' => 18,
    ],
    'program_card' => [
        'title_min' => 5,
        'title_max' => 60,
        'feature_min' => 5,
        'feature_max' => 50,
        'features_max_items' => 12,
        'duration_min' => 2,
        'duration_max' => 20,
        'mode_min' => 2,
        'mode_max' => 20,
        'order_max' => 999,
        'word_max' => 18,
        'image' => [
            'recommended' => '1200 x 750 px (16:10 landscape), JPG, PNG or WEBP',
            'min_width' => 600,
            'min_height' => 375,
            'max_width' => 4000,
            'max_height' => 4000,
            'max_kb' => 5120, // 5 MB
        ],
    ],

    // Program "Tools & Digital Topics" and "Learning Format" (detail page)
    'program_extras' => [
        'tool_min' => 2,
        'tool_max' => 40,
        'tools_max' => 12,
        'format_max' => 6,
        'format_title_min' => 3,
        'format_title_max' => 40,
        'format_desc_min' => 10,
        'format_desc_max' => 120,
        'word_max' => 24,
        'icon' => [
            'recommended' => '128 x 128 px (square), PNG, JPG or WEBP, transparent PNG works best',
            'min_width' => 32,
            'min_height' => 32,
            'max_width' => 1000,
            'max_height' => 1000,
            'max_kb' => 1024, // 1 MB
        ],
    ],

    // Program curriculum levels (detail page)
    'program_levels' => [
        'max_levels' => 10,
        'title_min' => 3,
        'title_max' => 50,
        'period_max' => 30,
        'point_min' => 5,
        'point_max' => 80,
        'points_max' => 12,
        'word_max' => 24,
    ],

    // Program detail page banner (text: letters, digits, spaces and . , ' - & ! ?)
    'program_banner' => [
        'title_min' => 5,
        'title_max' => 60,
        'description_min' => 10,
        'description_max' => 160,
        'levels_min' => 2,
        'levels_max' => 30,
        'timeline_min' => 2,
        'timeline_max' => 50,
        'word_max' => 24,
        'image' => [
            'recommended' => '1920 x 600 px (wide landscape), JPG, PNG or WEBP',
            'min_width' => 1280,
            'min_height' => 400,
            'max_width' => 4000,
            'max_height' => 3000,
            'max_kb' => 5120, // 5 MB
        ],
    ],

    // Program detail page "Have any questions?" side card (the Contact Us button is static)
    'program_help' => [
        'heading_min' => 5,
        'heading_max' => 40,
        'description_min' => 10,
        'description_max' => 120,
        'word_max' => 24,
        'image' => [
            'recommended' => '1000 x 600 px (landscape), JPG, PNG or WEBP',
            'min_width' => 600,
            'min_height' => 300,
            'max_width' => 4000,
            'max_height' => 3000,
            'max_kb' => 5120, // 5 MB
        ],
    ],

    // Program detail page "About This Program" text
    'program_about' => [
        'min' => 20,
        'max' => 400,
        'word_max' => 30,
    ],

    // Homepage "Meet Our Team" section (text: letters, digits, spaces and . , ' - & ! ?)
    'team' => [
        'eyebrow_min' => 2,
        'eyebrow_max' => 30,
        'heading_min' => 3,
        'heading_max' => 30,
        'accent_min' => 2,
        'accent_max' => 30,
        'description_min' => 20,
        'description_max' => 250,
        'word_max' => 18,
    ],
    'team_member' => [
        'name_min' => 3,
        'name_max' => 40,
        'designation_min' => 3,
        'designation_max' => 50,
        'order_max' => 999,
        'word_max' => 18,
        'image' => [
            'recommended' => '800 x 1000 px (4:5 portrait), JPG, PNG or WEBP, subject centred',
            'min_width' => 400,
            'min_height' => 400,
            'max_width' => 4000,
            'max_height' => 4000,
            'max_kb' => 5120, // 5 MB
        ],
    ],

    // Homepage "Insights" section (text: letters, digits, spaces and . , ' - & ! ?)
    'insight' => [
        'eyebrow_min' => 2,
        'eyebrow_max' => 30,
        'heading_min' => 3,
        'heading_max' => 30,
        'accent_min' => 3,
        'accent_max' => 40,
        'description_min' => 20,
        'description_max' => 250,
        'word_max' => 18,
    ],
    'insight_card' => [
        'title_min' => 3,
        'title_max' => 40,
        'description_min' => 10,
        'description_max' => 120,
        'order_max' => 999,
        'word_max' => 18,
        'image' => [
            'recommended' => '600 x 400 px (3:2 landscape), JPG, PNG or WEBP',
            'min_width' => 300,
            'min_height' => 200,
            'max_width' => 4000,
            'max_height' => 4000,
            'max_kb' => 5120, // 5 MB
        ],
    ],

    // Homepage "Insights in Motion" video section (text: letters, digits, spaces and . , ' - & ! ?)
    'reel' => [
        'eyebrow_min' => 2,
        'eyebrow_max' => 30,
        'heading_min' => 3,
        'heading_max' => 30,
        'accent_min' => 3,
        'accent_max' => 30,
        'description_min' => 20,
        'description_max' => 200,
        'word_max' => 18,
    ],
    'reel_card' => [
        'tag_min' => 2,
        'tag_max' => 16,
        'title_min' => 3,
        'title_max' => 40,
        'url_max' => 255,
        'order_max' => 999,
        'word_max' => 18,
        'video' => [
            'recommended' => 'Only needed for links that cannot be embedded (not YouTube or Vimeo). Vertical 9:16 (e.g. 720 x 1280 px), MP4 (H.264) or WebM, 5-15 seconds',
            'max_kb' => 15360, // 15 MB
        ],
    ],

    // About page banner (text: letters, digits, spaces and . , ' - & ! ?)
    'about_banner' => [
        'heading_min' => 3,
        'heading_max' => 24,
        'description_min' => 20,
        'description_max' => 200,
        'word_max' => 18,
        'recommended' => '1920 x 1080 px (16:9, landscape), JPG, PNG or WEBP',
        'min_width' => 1280,
        'min_height' => 600,
        'max_width' => 4000,
        'max_height' => 3000,
        'max_kb' => 5120, // 5 MB
    ],

    // About page "Our Story" section (text: letters, digits, spaces and . , ' - & ! ?)
    'about_story' => [
        'eyebrow_min' => 2,
        'eyebrow_max' => 30,
        'heading_min' => 5,
        'heading_max' => 40,
        'accent_min' => 5,
        'accent_max' => 30,
        'para1_min' => 40,
        'para1_max' => 400,
        'para2_min' => 40,
        'para2_max' => 300,
        'quote_min' => 20,
        'quote_max' => 260,
        'name_min' => 2,
        'name_max' => 30,
        'role_min' => 2,
        'role_max' => 24,
        'word_max' => 18,
        'long_word_max' => 24,
        'image' => [
            'recommended' => '800 x 1000 px (4:5 portrait), JPG, PNG or WEBP, subject centred',
            'min_width' => 400,
            'min_height' => 400,
            'max_width' => 4000,
            'max_height' => 4000,
            'max_kb' => 5120, // 5 MB
        ],
    ],

    // About page "Guiding Principles" section (text: letters, digits, spaces and . , ' - & ! ?)
    'principles' => [
        'eyebrow_min' => 2,
        'eyebrow_max' => 40,
        'word_max' => 18,
        'image' => [
            'recommended' => '1200 x 1400 px (portrait or square), JPG, PNG or WEBP',
            'min_width' => 600,
            'min_height' => 600,
            'max_width' => 4000,
            'max_height' => 4000,
            'max_kb' => 5120, // 5 MB
        ],
    ],
    'principle' => [
        'label_min' => 3,
        'label_max' => 24,
        'heading_min' => 5,
        'heading_max' => 40,
        'accent_min' => 5,
        'accent_max' => 40,
        'description_min' => 30,
        'description_max' => 300,
        'order_max' => 999,
        'word_max' => 18,
        'long_word_max' => 24,
        'icons' => ['target' => 'Target', 'eye' => 'Eye', 'compass' => 'Compass', 'bulb' => 'Light bulb', 'users' => 'People', 'shield' => 'Shield', 'star' => 'Star', 'heart' => 'Heart'],
    ],

    // About page "Our Team" heading (members come from the home page Team Section)
    'about_team' => [
        'eyebrow_min' => 2,
        'eyebrow_max' => 30,
        'heading_min' => 5,
        'heading_max' => 40,
        'accent_min' => 5,
        'accent_max' => 30,
        'word_max' => 18,
    ],

    // About page closing call-to-action "Ready for what's next?" (text: letters, digits, spaces and . , ' - & ! ?)
    'about_cta' => [
        'eyebrow_min' => 2,
        'eyebrow_max' => 40,
        'line_min' => 3,
        'line_max' => 24,
        'accent_min' => 2,
        'accent_max' => 20,
        'description_min' => 30,
        'description_max' => 220,
        'caption_min' => 2,
        'caption_max' => 24,
        'word_max' => 18,
        'long_word_max' => 24,
        'image' => [
            'recommended' => 'Large image 1200 x 800 px (3:2 landscape); small image 800 x 600 px (4:3). JPG, PNG or WEBP',
            'min_width' => 500,
            'min_height' => 330,
            'max_width' => 4000,
            'max_height' => 4000,
            'max_kb' => 5120, // 5 MB
        ],
    ],

    // Contact page banner (text: letters, digits, spaces and . , ' - & ! ?)
    'contact_banner' => [
        'heading_min' => 3,
        'heading_max' => 24,
        'description_min' => 20,
        'description_max' => 160,
        'word_max' => 18,
        'recommended' => '1920 x 1080 px (16:9, landscape), JPG, PNG or WEBP',
        'min_width' => 1280,
        'min_height' => 600,
        'max_width' => 4000,
        'max_height' => 3000,
        'max_kb' => 5120, // 5 MB
    ],

    // Contact page "Get in touch" section (company contact details, edited in the admin)
    'contact_info' => [
        'heading_min' => 3,
        'heading_max' => 30,
        'accent_min' => 3,
        'accent_max' => 30,
        'description_min' => 30,
        'description_max' => 220,
        'email_max' => 100,
        'phone_min' => 7,
        'phone_max' => 20,
        'address_min' => 10,
        'address_max' => 160,
        'word_max' => 18,
        'long_word_max' => 24,
    ],
    // Public contact form limits (shared by the browser checks and the server)
    'contact_form' => [
        'name_min' => 2,
        'name_max' => 60,
        'email_max' => 254,
        'phone_min' => 7,
        'phone_max' => 20,
        'message_min' => 10,
        'message_max' => 1000,
        'other_course' => 'General enquiry',
    ],

    // Insight detail page card (text: letters, digits, spaces and . , ' - & ! ?)
    'insight_cta' => [
        'heading_min' => 3,
        'heading_max' => 30,
        'description_min' => 20,
        'description_max' => 200,
        'word_max' => 18,
    ],

    // Insights page banner (text: letters, digits, spaces and . , ' - & ! ?)
    'insight_banner' => [
        'heading_min' => 3,
        'heading_max' => 24,
        'description_min' => 20,
        'description_max' => 200,
        'word_max' => 18,
        'recommended' => '1920 x 1080 px (16:9, landscape), JPG, PNG or WEBP',
        'min_width' => 1280,
        'min_height' => 600,
        'max_width' => 4000,
        'max_height' => 3000,
        'max_kb' => 5120, // 5 MB
    ],

    // Insights page "Featured Insights" scrolling cards (text: letters, digits, spaces and . , ' - & ! ?)
    'featured' => [
        'eyebrow_min' => 2,
        'eyebrow_max' => 40,
        'word_max' => 18,
    ],
    'featured_card' => [
        'category_min' => 2,
        'category_max' => 20,
        'title_min' => 5,
        'title_max' => 80,
        'order_max' => 999,
        'word_max' => 18,
        'date_min' => '2000-01-01',
        'image' => [
            'recommended' => '1200 x 800 px (3:2 landscape), JPG, PNG or WEBP',
            'min_width' => 600,
            'min_height' => 400,
            'max_width' => 4000,
            'max_height' => 4000,
            'max_kb' => 5120, // 5 MB
        ],
    ],

    // Insights page article grid (text: letters, digits, spaces and . , ' - & ! ?)
    'articles' => [
        'eyebrow_min' => 2,
        'eyebrow_max' => 40,
        'heading_min' => 5,
        'heading_max' => 60,
        'word_max' => 18,
        'per_page' => 9, // 3 cards per row, 3 rows per page
    ],
    'article' => [
        'category_min' => 2,
        'category_max' => 20,
        'title_min' => 5,
        'title_max' => 80,
        'description_min' => 30,
        'description_max' => 200,
        'url_max' => 255,
        'order_max' => 999,
        'word_max' => 18,
        'long_word_max' => 24,
        'date_min' => '2000-01-01',
        'image' => [
            'recommended' => '1200 x 800 px (3:2 landscape), JPG, PNG or WEBP',
            'min_width' => 600,
            'min_height' => 400,
            'max_width' => 4000,
            'max_height' => 4000,
            'max_kb' => 5120, // 5 MB
        ],
    ],

    // Homepage Target Audience section
    'audience' => [
        'eyebrow_max' => 60,
        'heading_min' => 3,
        'heading_max' => 120,
        'highlight_max' => 60,
        'description_min' => 5,
        'description_max' => 500,
        'interval_min' => 2,
        'interval_max' => 30,
        'padding_min' => 0,
        'padding_max' => 240,
    ],
    'audience_card' => [
        'title_min' => 2,
        'title_max' => 60,
        'description_min' => 5,
        'description_max' => 250,
        'alt_max' => 125,
        'order_max' => 999,
        'image' => [
            'recommended' => '800 x 600 px (4:3 landscape), JPG, PNG or WEBP',
            'min_width' => 240,
            'min_height' => 180,
            'max_width' => 4000,
            'max_height' => 4000,
            'max_kb' => 5120, // 5 MB
        ],
    ],
];