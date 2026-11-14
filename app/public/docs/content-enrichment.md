# Content enrichment — places copy & photos (2026-11-14)

The local database is a **mock**. On 2026-11-14 it was enriched with accurate destination copy and freely licensed photos so the local site looks and reads like the real thing. **The local database is never deployed.** This file holds everything needed to apply the same content to production by hand in WP Admin.

## What changed locally

- **Places (5):** new tag, short description and hover text in EN + RO, and a new photo for each. Text below.
- **Gallery (9):** each item has a matching photo and an accurate caption. "Snorkeling", "Snorkeling Group" and "Coral Bay Resort" were renamed, because no real photos of those exist yet.
- **Series covers:** Vietnam Escape and Vietnam Undiscovered.
- **Projects:**
  - Vol. 5 has a new cover photo.
  - Vol. 6 was test data (Dest1/Dest2, `goku.jpeg`, a google.com register link). It is now a *mock* upcoming edition: 10–25 March 2027, with price and description copied from Vol. 5. **These details are not real.**
- **Removed:** the orphaned test itinerary day and test accommodation (they pointed at a project that no longer exists).
- **Still placeholder:** testimonials and team bios are still lorem ipsum. They describe real people, so only the NGO can write them.

## Applying to production (optional)

1. Upload the photos you want from `wp-content/uploads/2026/11/nonla-*.jpg` (already resized to 1920 px, 0.3–0.8 MB each) in **Media → Add New**. Copy the caption from the credits table below.
2. **Places → edit each place:** paste the tag, description and hover texts from below, and pick the new photo.
3. **Before publishing any Commons photo, add a "Photo credits" section** (on the Legal page, for example) listing author, license and source for each one. CC BY and CC BY-SA require visible attribution. CC0 photos don't need it, but crediting them is good practice.
4. Prefer the NGO's own trip photos where they exist. Commons photos are a good fallback for places the group hasn't photographed well.

## Place copy (EN / RO)

### Hanoi
- **Tag:** Old Quarter
- **Description:** Ancient streets, Temple of Literature, street food & Vietnamese coffee
- **Hover EN:** Vietnam's thousand-year-old capital. Get lost in the 36 guild streets of the Old Quarter, walk around Hoàn Kiếm Lake at dawn, and try phở and egg coffee in the city where they were born.
- **Hover RO:** Capitala Vietnamului, veche de o mie de ani. Pierde-te pe cele 36 de străzi ale breslelor din Old Quarter, plimbă-te în jurul lacului Hoàn Kiếm în zori și gustă phở și cafea cu ou chiar în orașul în care s-au născut.

### Lan Ha Bay
- **Tag:** Cát Bà Archipelago
- **Description:** Limestone karsts, kayaking, quiet lagoons & floating fishing villages
- **Hover EN:** The quieter neighbour of Hạ Long Bay. Hundreds of limestone islands rise from emerald water around Cát Bà; we kayak between them, swim in hidden lagoons and pass floating fishing villages.
- **Hover RO:** Vecinul mai liniștit al golfului Hạ Long. Sute de insule calcaroase se ridică din apa smarald din jurul insulei Cát Bà; vâslim cu caiacul printre ele, înotăm în lagune ascunse și trecem pe lângă sate de pescari plutitoare.

### Ninh Binh
- **Tag:** Tràng An · UNESCO
- **Description:** Karst valleys, rowing boats through caves & rice fields
- **Hover EN:** “Hạ Long Bay on land.” Local rowers take us by boat through caves and rice fields in the UNESCO-listed Tràng An landscape, then we climb the 500 steps of Hang Múa for the view over Tam Cốc.
- **Hover RO:** „Golful Hạ Long de pe uscat.” Barcagii localnici ne poartă prin peșteri și orezării în peisajul Tràng An, aflat în patrimoniul UNESCO, apoi urcăm cele 500 de trepte ale Hang Múa pentru priveliștea asupra Tam Cốc.

### Phu Quoc
- **Tag:** Island Life
- **Description:** White-sand beaches, island hopping & Gulf of Thailand sunsets
- **Hover EN:** Vietnam's largest island, in the Gulf of Thailand. White-sand beaches like Bãi Sao, boat trips to the An Thới islands, pepper farms, a night market full of seafood, and sunsets over the sea every evening.
- **Hover RO:** Cea mai mare insulă a Vietnamului, în Golful Thailandei. Plaje cu nisip alb precum Bãi Sao, ture cu barca printre insulele An Thới, plantații de piper, o piață de noapte plină de fructe de mare și apusuri deasupra mării în fiecare seară.

### Ho Chi Minh City
- **Tag:** Saigon
- **Description:** Markets, history, rooftop views & non-stop street life
- **Hover EN:** Vietnam's biggest city, still called Saigon by most locals. Bến Thành Market, the War Remnants Museum, the French-era Central Post Office and Nguyễn Huệ walking street — a fast, loud and very friendly finale.
- **Hover RO:** Cel mai mare oraș al Vietnamului, pe care majoritatea localnicilor îl numesc încă Saigon. Piața Bến Thành, Muzeul Rămășițelor Războiului, Oficiul Poștal Central din epoca franceză și strada pietonală Nguyễn Huệ — un final rapid, zgomotos și foarte prietenos.

> Check the activities against the real itinerary before publishing: kayaking in Lan Ha, Hang Múa, An Thới boat trips.

## Photo credits

All photos are from Wikimedia Commons. Each image's caption in the Media Library also holds its credit line, source URL and license URL.

| Local file (uploads/2026/11/) | Used for | Author | License | Source |
|---|---|---|---|---|
| `nonla-place-hanoi.jpg` | Place card | Gary Cycles | CC BY 2.0 | [Commons](https://commons.wikimedia.org/wiki/File:Hanoi_architecture_-_H%C3%A0ng_B%E1%BA%A1c_Street.jpg) |
| `nonla-place-lanha.jpg` | Place card | Christophe95 | CC BY-SA 4.0 | [Commons](https://commons.wikimedia.org/wiki/File:Lan_Ha_Bay_21.jpg) |
| `nonla-place-ninhbinh.jpg` | Place card | Jakub Hałun | CC BY 4.0 | [Commons](https://commons.wikimedia.org/wiki/File:Trang_An_Landscape_Complex,_Ninh_Binh_Province,_Vietnam,_20240202_1447_5310.jpg) |
| `nonla-place-phuquoc.jpg` | Place card | Trantuonglam | CC BY-SA 4.0 | [Commons](https://commons.wikimedia.org/wiki/File:Bai-sao-phu-quoc-tuonglamphotos.jpg) |
| `nonla-place-hcmc.jpg` | Place card | Steffen Schmitz | CC BY-SA 4.0 | [Commons](https://commons.wikimedia.org/wiki/File:Ho_Chi_Minh_City,_Nguyen_Hue_Street,_2020-01_CN-01.jpg) |
| `nonla-gal-phuquoc-beach.jpg` | Gallery item | Elmschrat | CC BY-SA 4.0 | [Commons](https://commons.wikimedia.org/wiki/File:2_Phu_Quoc_beach.jpg) |
| `nonla-gal-lanha-junks.jpg` | Gallery item | Velvet | CC BY-SA 4.0 | [Commons](https://commons.wikimedia.org/wiki/File:Lan_Ha_bay_jonques.jpg) |
| `nonla-gal-tamcoc-boats.jpg` | Gallery item | Ji-Elle | CC BY-SA 4.0 | [Commons](https://commons.wikimedia.org/wiki/File:Barques_%C3%A0_Tam_Coc_(2).jpg) |
| `nonla-gal-cathedral.jpg` | Gallery item | Linhcandng | CC BY-SA 3.0 | [Commons](https://commons.wikimedia.org/wiki/File:Nh%C3%A0_th%E1%BB%9D_L%E1%BB%9Bn_H%C3%A0_N%E1%BB%99i.jpeg) |
| `nonla-gal-lanha-karsts.jpg` | Gallery item | Christophe95 | CC BY-SA 4.0 | [Commons](https://commons.wikimedia.org/wiki/File:Lan_Ha_Bay_20.jpg) |
| `nonla-gal-airplane.jpg` | Gallery item | Elmschrat | CC BY-SA 4.0 | [Commons](https://commons.wikimedia.org/wiki/File:3_Phu_Quoc_beach_and_airplane.jpg) |
| `nonla-gal-resort.jpg` | Gallery item | Elmschrat | CC0 | [Commons](https://commons.wikimedia.org/wiki/File:1_Phu_Quoc_beach_Saigon_Phu_Quoc_Resort_and_Spa.jpg) |
| `nonla-gal-sunset.jpg` | Gallery item | Elmschrat | CC0 | [Commons](https://commons.wikimedia.org/wiki/File:1_Phu_Quoc_sunset.jpg) |
| `nonla-gal-phuquoc-shore.jpg` | Gallery item | Elmschrat | CC BY-SA 4.0 | [Commons](https://commons.wikimedia.org/wiki/File:Phu_Quoc_beach_01.jpg) |
| `nonla-proj-vol5.jpg` | Project cover | Steffen Schmitz | CC BY-SA 4.0 | [Commons](https://commons.wikimedia.org/wiki/File:H%E1%BA%A1_Long_Bay,_Ti_T%E1%BB%91p_Island,_2020-01_CN-01.jpg) |
| `nonla-proj-vol6.jpg` | Project cover | Jakub Hałun | CC BY 4.0 | [Commons](https://commons.wikimedia.org/wiki/File:View_of_sea_from_Titov_Island,_Ha_Long_Bay,_Vietnam,_20240128_1337_3732.jpg) |
| `nonla-series-escape.jpg` | Series cover | Jakub Hałun | CC BY 4.0 | [Commons](https://commons.wikimedia.org/wiki/File:Trang_An_Landscape_Complex,_Ninh_Binh_Province,_Vietnam,_20240202_1456_5313.jpg) |
| `nonla-series-undiscovered.jpg` | Series cover | Velvet | CC BY-SA 4.0 | [Commons](https://commons.wikimedia.org/wiki/File:Ha_Giang_rizieres_bis.jpg) |
