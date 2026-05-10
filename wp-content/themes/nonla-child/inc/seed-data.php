<?php
// One-time data seed — creates team members and projects as WP posts.
// Runs once per install, guarded by options. Safe to re-run (checks for existing posts).

add_action('init', function () {

    // ── TEAM MEMBERS ────────────────────────────────────────────────
    if (!get_option('nonla_team_seeded')) {
        $members = [
            [
                'title'         => 'Alex Ivan',
                'role_en'       => 'Founder · Cultural Ambassador',
                'role_ro'       => 'Fondator · Ambasador Cultural',
                'city'          => 'Ho Chi Minh City',
                'photo_url'     => 'https://nonlaescape.com/wp-content/uploads/2024/12/alex-profile-1-e1733645445625.jpg',
                'display_order' => 1,
            ],
            [
                'title'         => 'Trần Thị Thanh Thuỳ',
                'role_en'       => 'Cultural Ambassador · Operations Coordinator',
                'role_ro'       => 'Ambasador Cultural · Coordonator Operațional',
                'city'          => 'Ho Chi Minh City',
                'photo_url'     => 'https://nonlaescape.com/wp-content/uploads/2024/12/thuy-profile-1-e1733636971467.jpg',
                'display_order' => 2,
            ],
            [
                'title'         => 'Hiếu Phạm',
                'role_en'       => 'Cultural Ambassador',
                'role_ro'       => 'Ambasador Cultural',
                'city'          => 'Hanoi',
                'photo_url'     => 'https://nonlaescape.com/wp-content/uploads/2024/12/WhatsApp-Image-2024-12-07-at-14.59.20-1.jpeg',
                'display_order' => 3,
            ],
            [
                'title'         => 'Nguyễn Duy Uyên',
                'role_en'       => 'Cultural Ambassador',
                'role_ro'       => 'Ambasador Cultural',
                'city'          => 'Hanoi',
                'photo_url'     => 'https://nonlaescape.com/wp-content/uploads/2024/12/uyen-profile-1-e1733641741146.jpg',
                'display_order' => 4,
            ],
            [
                'title'         => 'Nguyễn Ngọc Hà Phương',
                'role_en'       => 'Cultural Ambassador',
                'role_ro'       => 'Ambasador Cultural',
                'city'          => 'Hanoi',
                'photo_url'     => 'https://nonlaescape.com/wp-content/uploads/2024/12/haphuong-profile-1-e1733643856255.jpg',
                'display_order' => 5,
            ],
            [
                'title'         => 'Nguyễn Đoàn Yến Nhi',
                'role_en'       => 'Cultural Ambassador',
                'role_ro'       => 'Ambasador Cultural',
                'city'          => 'Ho Chi Minh City',
                'photo_url'     => 'https://nonlaescape.com/wp-content/uploads/2024/12/nhi-profile-1-e1733669989583.jpg',
                'display_order' => 6,
            ],
            [
                'title'         => 'Trịnh Thu Phương',
                'role_en'       => 'Cultural Ambassador',
                'role_ro'       => 'Ambasador Cultural',
                'city'          => 'Ho Chi Minh City',
                'photo_url'     => 'https://nonlaescape.com/wp-content/uploads/2024/12/WhatsApp-Image-2024-12-07-at-13.53.44-2-1-scaled-e1733636020764.jpeg',
                'display_order' => 7,
            ],
            [
                'title'         => 'Aura Amironesei',
                'role_en'       => 'Legal Advisor',
                'role_ro'       => 'Consilier Juridic',
                'city'          => 'Iași',
                'photo_url'     => 'https://nonlaescape.com/wp-content/uploads/2024/12/aura-profile-1-1-2.jpg',
                'display_order' => 8,
            ],
            [
                'title'         => 'Constantin Anghel',
                'role_en'       => 'Accountant',
                'role_ro'       => 'Contabil',
                'city'          => 'Buzău',
                'photo_url'     => 'https://nonlaescape.com/wp-content/uploads/2024/12/WhatsApp-Image-2024-12-07-at-21.47.31-1-1-e1733635842883.jpeg',
                'display_order' => 9,
            ],
        ];

        foreach ($members as $m) {
            $existing = get_posts(['post_type' => 'nl_team_member', 'title' => $m['title'], 'post_status' => 'any', 'numberposts' => 1]);
            if ($existing) continue;

            $id = wp_insert_post([
                'post_title'  => $m['title'],
                'post_status' => 'publish',
                'post_type'   => 'nl_team_member',
            ]);
            if (!$id || is_wp_error($id)) continue;

            foreach (['role_en', 'role_ro', 'city', 'photo_url', 'display_order'] as $key) {
                if (!empty($m[$key])) update_post_meta($id, $key, $m[$key]);
            }
        }

        update_option('nonla_team_seeded', true);
    }

    // ── PROJECTS ────────────────────────────────────────────────────
    if (!get_option('nonla_projects_seeded')) {
        $projects = [
            [
                'title'          => 'Vietnam Escape Vol. 6',
                'series'         => 'vietnam-escape',
                'edition'        => 'Vol. 6',
                'year'           => '2026',
                'status'         => 'upcoming',
                'dates_en'       => 'Oct 27 – Nov 11, 2026',
                'dates_ro'       => '27 Oct – 11 Nov 2026',
                'duration_en'    => '15 days',
                'duration_ro'    => '15 zile',
                'price'          => 'TBA',
                'price_note_en'  => '15 spots available',
                'price_note_ro'  => '15 locuri disponibile',
                'destinations'   => 'Hanoi, Ha Long Bay, Ninh Binh, Phu Quoc, Ho Chi Minh City',
                'cover_url'      => 'halong.jpg',
                'desc_en'        => "From the peaceful mountain formations of the north to the lively beaches of the south — a two-week journey through Vietnam's most diverse landscapes and cultures. 15 spots available.",
                'desc_ro'        => 'De la formațiunile montane liniștite din nord până la plajele animate din sud — o călătorie de două săptămâni prin peisajele și culturile cele mai diverse ale Vietnamului. 15 locuri disponibile.',
            ],
            [
                'title'          => 'Vietnam Escape Vol. 5',
                'series'         => 'vietnam-escape',
                'edition'        => 'Vol. 5',
                'year'           => '2026',
                'status'         => 'completed',
                'dates_en'       => 'Apr 14 – 29, 2026',
                'dates_ro'       => '14 Apr – 29 Apr 2026',
                'duration_en'    => '15 days',
                'duration_ro'    => '15 zile',
                'price'          => '€1,895',
                'price_note_en'  => 'all-inclusive · flights · hotels',
                'price_note_ro'  => 'all-inclusive · zboruri · hoteluri',
                'destinations'   => 'Hanoi, Lan Ha Bay, Ninh Binh, Phu Quoc, Ho Chi Minh City',
                'cover_url'      => 'hero.jpg',
                'desc_en'        => "15 days across Vietnam's most iconic destinations. Hanoi's ancient streets, Ha Long Bay's limestone karsts, Ninh Binh's river valleys, Phu Quoc's tropical island, and Ho Chi Minh City. Turkish Airlines, 8.5+ rated hotels, expert coordinators — all-inclusive.",
                'desc_ro'        => "15 zile prin cele mai iconice destinații din Vietnam. Străzile antice ale Hanoiului, stâncile calcaroase din Ha Long Bay, văile fluviale din Ninh Binh, insula tropicală Phu Quoc și Ho Chi Minh City. Turkish Airlines, hoteluri evaluate 8.5+, coordonatori experți — all-inclusive.",
            ],
        ];

        foreach ($projects as $p) {
            $existing = get_posts(['post_type' => 'nl_project', 'title' => $p['title'], 'post_status' => 'any', 'numberposts' => 1]);
            if ($existing) continue;

            $id = wp_insert_post([
                'post_title'  => $p['title'],
                'post_status' => 'publish',
                'post_type'   => 'nl_project',
            ]);
            if (!$id || is_wp_error($id)) continue;

            $fields = ['series', 'edition', 'year', 'status', 'dates_en', 'dates_ro', 'duration_en', 'duration_ro', 'price', 'price_note_en', 'price_note_ro', 'destinations', 'cover_url', 'desc_en', 'desc_ro'];
            foreach ($fields as $key) {
                if (isset($p[$key])) update_post_meta($id, $key, $p[$key]);
            }
        }

        update_option('nonla_projects_seeded', true);
    }

}, 20);
