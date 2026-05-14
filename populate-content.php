<?php
/**
 * Populate all CPT content for Nonla.
 * Run via: wp eval-file populate-content.php
 */

// ── HELPERS ──────────────────────────────────────────────────────────────────

function nl_import_photo( $filename, $title ) {
    $source = 'C:/Users/ionut/Desktop/Photos/modified/' . $filename;

    if ( ! file_exists( $source ) ) {
        WP_CLI::warning( "Photo not found: $filename" );
        return 0;
    }

    // Skip if already imported with this title
    $existing = get_posts( [
        'post_type'   => 'attachment',
        'post_status' => 'any',
        'title'       => $title,
        'numberposts' => 1,
    ] );
    if ( $existing ) {
        WP_CLI::log( "  Already imported: $title → ID {$existing[0]->ID}" );
        return $existing[0]->ID;
    }

    $upload_dir = wp_upload_dir();
    $dest       = $upload_dir['path'] . '/' . $filename;

    if ( ! copy( $source, $dest ) ) {
        WP_CLI::warning( "Failed to copy: $filename" );
        return 0;
    }

    $filetype   = wp_check_filetype( $filename );
    $attach_id  = wp_insert_attachment( [
        'post_mime_type' => $filetype['type'],
        'post_title'     => $title,
        'post_content'   => '',
        'post_status'    => 'inherit',
    ], $dest );

    require_once ABSPATH . 'wp-admin/includes/image.php';
    wp_update_attachment_metadata( $attach_id, wp_generate_attachment_metadata( $attach_id, $dest ) );

    WP_CLI::log( "  Imported: $title → ID $attach_id" );
    return $attach_id;
}

function nl_upsert( $post_type, $title, $fields, $order ) {
    $existing = get_posts( [
        'post_type'   => $post_type,
        'post_status' => 'publish',
        'title'       => $title,
        'numberposts' => 1,
    ] );

    if ( $existing ) {
        $id = $existing[0]->ID;
        WP_CLI::log( "  Updating: $title (ID $id)" );
    } else {
        $id = wp_insert_post( [
            'post_type'   => $post_type,
            'post_title'  => $title,
            'post_status' => 'publish',
        ] );
        WP_CLI::log( "  Created:  $title (ID $id)" );
    }

    foreach ( $fields as $key => $value ) {
        update_field( $key, $value, $id );
    }
    update_field( 'display_order', $order, $id );

    return $id;
}

// ── STEP 1 — IMPORT PHOTOS ───────────────────────────────────────────────────

WP_CLI::log( '' );
WP_CLI::log( '── Importing photos ──' );

$raw = [
    'hanoi_cathedral'   => [ 'PXL_20260415_142859273.jpg',                 'Hanoi — St. Joseph\'s Cathedral' ],
    'hanoi_antique'     => [ 'PXL_20260416_061605795.RAW-02.ORIGINAL.jpg', 'Hanoi — Old Quarter Antique Shop' ],
    'lanhabay_arch'     => [ 'PXL_20260417_074059752.jpg',                 'Lan Ha Bay — Limestone Arch' ],
    'ninhbinh_aerial'   => [ 'PXL_20260418_101555769.jpg',                 'Ninh Binh — Mua Cave Aerial' ],
    'tamcoc_boats'      => [ 'PXL_20260419_044436477.jpg',                 'Tam Coc — River Boats' ],
    'train_dragonfly'   => [ 'PXL_20260421_022535116.jpg',                 'Train — Dragonfly Craft' ],
    'phuquoc_snorkel1'  => [ 'PXL_20260422_083051470.jpg',                 'Phu Quoc — Snorkeling from Boat' ],
    'phuquoc_jungle'    => [ 'PXL_20260423_024533982.jpg',                 'Phu Quoc — Jungle Trail' ],
    'hcmc_postoffice'   => [ 'PXL_20260425_103323484.jpg',                 'Ho Chi Minh City — Central Post Office' ],
    'coffee_workshop'   => [ 'PXL_20260426_025825550.jpg',                 'Vietnamese Coffee Workshop' ],
    'phuquoc_resort'    => [ 'IMG-20260421-WA0018.jpg',                    'Phu Quoc — Coral Bay Resort' ],
    'phuquoc_sunset'    => [ 'IMG-20260424-WA0044.jpg',                    'Phu Quoc — Beach Sunset' ],
    'phuquoc_snorkel2'  => [ 'IMG-20260427-WA0051.jpg',                    'Phu Quoc — Snorkeling Group' ],
    'phuquoc_beach'     => [ 'IMG-20260427-WA0059.jpg',                    'Phu Quoc — White Sand Beach' ],
    'group_beach'       => [ 'batch_GOPR9243.JPG',                         'Vietnam Escape Vol. 5 — Group Photo' ],
];

$ids = [];
foreach ( $raw as $key => [ $file, $title ] ) {
    $ids[ $key ] = nl_import_photo( $file, $title );
}

// ── STEP 2 — PLACES ──────────────────────────────────────────────────────────

WP_CLI::log( '' );
WP_CLI::log( '── Places ──' );

$places = [
    [ 'Hanoi',            'Old Quarter',  'Ancient streets, Temple of Literature, street food & Vietnamese coffee',    'hanoi_cathedral',  1 ],
    [ 'Lan Ha Bay',       'Ha Long',      'UNESCO limestone karsts, emerald waters, boat trips & kayaking',            'lanhabay_arch',    2 ],
    [ 'Ninh Binh',        'Trang An',     'River boats, Mua Cave, bicycle rides through endless rice paddies',        'ninhbinh_aerial',  3 ],
    [ 'Phu Quoc',         'Island Life',  'Tropical island, snorkeling, boat tours, hiking & pure beach days',        'phuquoc_beach',    4 ],
    [ 'Ho Chi Minh City', 'Saigon',       'Colonial architecture, vibrant street life, rooftop bars & local cuisine', 'hcmc_postoffice',  5 ],
];

foreach ( $places as [ $title, $tag, $desc, $photo_key, $order ] ) {
    nl_upsert( 'nl_place', $title, [
        'photo'       => $ids[ $photo_key ],
        'tag'         => $tag,
        'description' => $desc,
    ], $order );
}

// ── STEP 3 — SERIES ──────────────────────────────────────────────────────────

WP_CLI::log( '' );
WP_CLI::log( '── Series ──' );

nl_upsert( 'nl_series', 'Vietnam Escape', [
    'label'       => 'Annual Group Escape',
    'description' => '<p>Our flagship annual escape — 15 days through Vietnam\'s iconic destinations. From Hanoi\'s ancient streets to Phu Quoc\'s tropical waters, everything is included: flights, hotels, transfers, and daily guided experiences.</p>',
    'cover_image' => $ids['group_beach'],
    'link_url'    => home_url( '/projects' ),
], 1 );

nl_upsert( 'nl_series', 'Vietnam Undiscovered', [
    'label'       => 'Off The Beaten Path',
    'description' => '<p>For those who want to go deeper. Remote villages, hidden valleys, and lesser-known corners of Vietnam that most travelers never reach. More adventurous, more intimate.</p>',
    'cover_image' => $ids['phuquoc_jungle'],
    'link_url'    => home_url( '/projects' ),
], 2 );

// ── STEP 4 — WHY ITEMS ───────────────────────────────────────────────────────

WP_CLI::log( '' );
WP_CLI::log( '── Why Items ──' );

$why = [
    [ 'Local Expertise',   '🧭', '2–4 coordinators speak Vietnamese, Romanian & English. No language barrier, no wasted days.',                            1 ],
    [ 'Vetted Stays',      '🏨', 'Every property personally tested and rated 8.5+ on Booking.com. Comfort you can count on.',                              2 ],
    [ 'All-Inclusive',     '✈️', 'Flights, transfers, accommodation, daily breakfast and curated experiences. You just show up.',                           3 ],
    [ 'NGO Organized',     '🌿', 'We\'re a certified NGO. Sustainable travel, community partnerships, and cultural respect are in our DNA.',                4 ],
    [ 'Authentic Vietnam', '🍜', 'Phở for €1.50, coffee workshops, market visits, local restaurants. Real Vietnam, not the tourist version.',               5 ],
    [ 'Small Group',       '🤝', 'Intimate group travel means real friendships. We travel, eat, and explore together. Community that lasts.',               6 ],
];

foreach ( $why as [ $title, $icon, $desc, $order ] ) {
    nl_upsert( 'nl_why_item', $title, [
        'icon'        => $icon,
        'description' => $desc,
    ], $order );
}

// ── STEP 5 — GALLERY ─────────────────────────────────────────────────────────

WP_CLI::log( '' );
WP_CLI::log( '── Gallery ──' );

$gallery = [
    [ 'Group on Phu Quoc Beach', 'Vietnam Escape Vol. 5 — Phu Quoc sunset', 'group_beach',      1  ],
    [ 'Lan Ha Bay',              'Limestone karsts, emerald waters',         'lanhabay_arch',    2  ],
    [ 'Mua Cave Pagoda',         'Ninh Binh from above',                     'ninhbinh_aerial',  3  ],
    [ 'Tam Coc River Boats',     'Ninh Binh river valley',                   'tamcoc_boats',     4  ],
    [ 'Hanoi Cathedral',         'St. Joseph\'s Cathedral by night',         'hanoi_cathedral',  5  ],
    [ 'Old Quarter Antiques',    'Hanoi, Old Quarter',                       'hanoi_antique',    6  ],
    [ 'Snorkeling',              'Phu Quoc boat tour',                       'phuquoc_snorkel1', 7  ],
    [ 'Train Travel',            'Hanoi to Ninh Binh',                       'train_dragonfly',  8  ],
    [ 'Coffee Workshop',         'Vietnamese coffee brewing',                'coffee_workshop',  9  ],
    [ 'Coral Bay Resort',        'Phu Quoc accommodation',                   'phuquoc_resort',   10 ],
    [ 'Phu Quoc Sunset',         'Beach moments',                            'phuquoc_sunset',   11 ],
    [ 'Snorkeling Group',        'Phu Quoc crystal waters',                  'phuquoc_snorkel2', 12 ],
];

foreach ( $gallery as [ $title, $caption, $photo_key, $order ] ) {
    nl_upsert( 'nl_gallery_item', $title, [
        'photo'   => $ids[ $photo_key ],
        'caption' => $caption,
    ], $order );
}

// ── STEP 6 — TESTIMONIALS (lorem ipsum) ──────────────────────────────────────

WP_CLI::log( '' );
WP_CLI::log( '── Testimonials ──' );

$testimonials = [
    [
        'Andreea M.',
        '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris. A fost mai mult decât o vacanță — a fost o experiență care mi-a schimbat perspectiva.</p>',
        'Andreea M.', 'Cluj-Napoca, 2026', 1,
    ],
    [
        'Radu C.',
        '<p>Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam. Nisi ut aliquid ex ea commodi consequatur? Coordonatorii au fost excepționali — au făcut totul să pară simplu și natural.</p>',
        'Radu C.', 'București, 2026', 2,
    ],
    [
        'Maria T.',
        '<p>Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat. M-am întors acasă cu prieteni pe viață și amintiri pe care nu le voi uita niciodată.</p>',
        'Maria T.', 'Timișoara, 2026', 3,
    ],
];

foreach ( $testimonials as [ $title, $quote, $author_name, $author_role, $order ] ) {
    nl_upsert( 'nl_testimonial', $title, [
        'quote'       => $quote,
        'author_name' => $author_name,
        'author_role' => $author_role,
    ], $order );
}

// ── STEP 7 — TEAM MEMBERS ────────────────────────────────────────────────────

WP_CLI::log( '' );
WP_CLI::log( '── Team Members ──' );

$lorem = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi.';

$team = [
    [ 'Alex Ivan',               'Founder & Cultural Ambassador',                  'Ho Chi Minh City', 1 ],
    [ 'Trần Thị Thanh Thuỳ',     'Cultural Ambassador & Operations Coordinator',   'Ho Chi Minh City', 2 ],
    [ 'Hiếu Phạm',               'Cultural Ambassador',                            'Hanoi',            3 ],
    [ 'Nguyễn Đoàn Yến Nhi',     'Cultural Ambassador',                            'Ho Chi Minh City', 4 ],
    [ 'Nguyễn Ngọc Hà Phương',   'Cultural Ambassador',                            'Hanoi',            5 ],
    [ 'Trịnh Thu Phương',         'Cultural Ambassador',                            'Ho Chi Minh City', 6 ],
    [ 'Nguyễn Duy Uyên',         'Cultural Ambassador',                            'Hanoi',            7 ],
    [ 'Aura Amironesei',         'Legal Advisor',                                  'Iași',             8 ],
    [ 'Constantin Anghel',       'Accountant',                                     'Buzău',            9 ],
];

foreach ( $team as [ $name, $role, $city, $order ] ) {
    nl_upsert( 'nl_team_member', $name, [
        'role_en' => $role,
        'city'    => $city,
        'bio_en'  => $lorem,
        'bio_ro'  => $lorem,
    ], $order );
}

// ── STEP 8 — PROJECT: VIETNAM ESCAPE VOL. 5 ──────────────────────────────────

WP_CLI::log( '' );
WP_CLI::log( '── Project: Vietnam Escape Vol. 5 ──' );

$vol5 = null;
foreach ( get_posts( [ 'post_type' => 'nl_project', 'numberposts' => -1 ] ) as $p ) {
    if ( get_field( 'edition', $p->ID ) === 'Vol. 5' ) {
        $vol5 = $p->ID;
        break;
    }
}

if ( ! $vol5 ) {
    $vol5 = wp_insert_post( [
        'post_type'   => 'nl_project',
        'post_title'  => 'Vietnam Escape Vol. 5',
        'post_status' => 'publish',
    ] );
    WP_CLI::log( "  Created: Vietnam Escape Vol. 5 (ID $vol5)" );
} else {
    WP_CLI::log( "  Updating: Vietnam Escape Vol. 5 (ID $vol5)" );
}

$vol5_fields = [
    'series'          => 'vietnam-escape',
    'edition'         => 'Vol. 5',
    'year'            => '2026',
    'status'          => 'completed',
    'dates_en'        => 'April 14 – 29, 2026',
    'dates_ro'        => '14 – 29 Aprilie 2026',
    'duration_en'     => '15 days',
    'duration_ro'     => '15 zile',
    'price'           => '€1,895',
    'price_note_en'   => 'Flights included · 13 nights accommodation · Daily breakfast',
    'price_note_ro'   => 'Zboruri incluse · 13 nopți cazare · Mic dejun zilnic',
    'destinations'    => 'Hanoi, Lan Ha Bay, Ninh Binh, Phu Quoc, Ho Chi Minh City',
    'cover_image'     => $ids['ninhbinh_aerial'],
    'desc_en'         => '15 days through Vietnam\'s most iconic destinations — ancient streets of Hanoi, limestone karsts of Lan Ha Bay, river valleys of Ninh Binh, tropical beaches of Phu Quoc, and the vibrant energy of Ho Chi Minh City. Everything included: Turkish Airlines flights, 13 nights accommodation, daily breakfast, all transfers, and 2–4 coordinators who speak Romanian, English, and Vietnamese.',
    'desc_ro'         => '15 zile prin cele mai iconice destinații din Vietnam — străzile antice ale Hanoiului, formațiunile calcaroase din Lan Ha Bay, văile din Ninh Binh, plajele tropicale din Phu Quoc și energia vibrantă a Ho Chi Minh City. Totul inclus: zboruri Turkish Airlines, 13 nopți cazare, mic dejun zilnic, toate transferurile și 2–4 coordonatori care vorbesc română, engleză și vietnameză.',
];

foreach ( $vol5_fields as $key => $value ) {
    update_field( $key, $value, $vol5 );
}

// ── DONE ─────────────────────────────────────────────────────────────────────

WP_CLI::log( '' );
WP_CLI::success( 'All content populated.' );
WP_CLI::log( 'Next: wp elementor flush-css' );
