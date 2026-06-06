INSERT INTO bd_catalog_products (id, name, price_usd, category, description, is_enquiry) VALUES
-- Snorkeling
(1, 'Padang Bai', 25.00, 'Snorkeling', 'Calm bay with rich marine life and colorful corals', 0),
(2, 'Tulamben', 30.00, 'Snorkeling', 'Snorkel above the famous USAT Liberty shipwreck in shallow waters', 0),
(3, 'Amed', 22.00, 'Snorkeling', 'Beautiful coral gardens perfect for snorkeling and underwater photos', 0),
(4, 'Nusa Penida', 35.00, 'Snorkeling', 'Crystal clear waters with turtles and vibrant tropical fish', 0),
(5, 'Manta Point', 40.00, 'Snorkeling', 'Swim with majestic manta rays in their natural habitat', 0),

-- Try Diving
(6, 'Tulamben', 85.00, 'Try Diving', 'Guided shallow dives at the USAT Liberty shipwreck for first-time divers.', 0),
(7, 'Padang Bai', 75.00, 'Try Diving', 'Calm, clear conditions for your very first underwater experience.', 0),
(8, 'Amed', 70.00, 'Try Diving', 'Safe introduction to scuba diving with beautiful coral reefs.', 0),

-- Fun Diving
(9, 'Tulamben', 55.00, 'Fun Diving', 'Explore the legendary USAT Liberty wreck teeming with marine life.', 0),
(10, 'Padang Bai', 48.00, 'Fun Diving', 'Multiple dive sites with diverse underwater landscapes and creatures.', 0),
(11, 'Amed', 50.00, 'Fun Diving', 'Japanese wreck and vibrant coral gardens for certified divers.', 0),
(12, 'Manta Point', 75.00, 'Fun Diving', 'Unforgettable encounters with graceful manta rays and pelagic fish.', 0),
(13, 'Tepekong', 65.00, 'Fun Diving', 'Advanced drift dive with sharks and strong currents.', 0),
(14, 'Kubu', 58.00, 'Fun Diving', 'Dramatic underwater scenery with macro photography opportunities and fish.', 0),

-- Diving Safari (Multi-day)
(15, 'Menjangan', 0.00, 'Diving Safari (Multi-day)', 'Pristine marine park with stunning wall dives variety.', 1),
(16, 'East Coast (2D1N)', 0.00, 'Diving Safari (Multi-day)', 'Two day diving adventure exploring Bali''s best eastern dive sites.', 1),
(17, 'Banyuwangi', 0.00, 'Diving Safari (Multi-day)', 'Volcanic underwater landscapes with unique marine biodiversity and pelagics.', 1),
(18, 'Sumbawa', 0.00, 'Diving Safari (Multi-day)', 'Remote pristine reefs with world class diving experiences untouched.', 1),
(19, 'Other', 0.00, 'Diving Safari (Multi-day)', 'Custom multi day diving safari tailored to your preferences.', 1),

-- Learn Diving (PADI)
(20, 'PADI Discover Scuba Diving', 95.00, 'Learn Diving', 'First PADI experience: pool session + guided ocean introduction.', 0),
(21, 'PADI Open Water Diver', 450.00, 'Learn Diving', 'Full entry level PADI certification to dive independently with a buddy.', 0),
(22, 'PADI Advanced Open Water', 380.00, 'Learn Diving', 'Level up your skills with deep, navigation, and other adventure dives.', 0),
(23, 'PADI Specialty Programs', 280.00, 'Learn Diving', 'Focused PADI specialties such as Nitrox, Wreck, or Peak Performance Buoyancy.', 0),
(24, 'PADI Divemaster Program', 950.00, 'Learn Diving', 'First professional level in the PADI system, train as a dive leader.', 0);

INSERT INTO bd_catalog_product_images (product_id, image_url, sort_order) VALUES
(1,  'https://balidiving.com/images/thumbnails/10-bali-diving-underwater.jpg', 1),
(2,  'https://balidiving.com/images/thumbnails/11-bali-diving-underwater.jpg', 1),
(3,  'https://balidiving.com/images/thumbnails/12-bali-diving-underwater.jpg', 1),
(4,  'https://balidiving.com/images/thumbnails/13-bali-diving-underwater.jpg', 1),
(5,  'https://balidiving.com/images/thumbnails/2-bali-diving-underwater.jpg', 1),
(6,  'https://balidiving.com/images/thumbnails/3-bali-diving-underwater.jpg', 1),
(7,  'https://balidiving.com/images/thumbnails/4-bali-diving-underwater.jpg', 1),
(8,  'https://balidiving.com/images/thumbnails/5-bali-diving-underwater.jpg', 1),
(9,  'https://balidiving.com/images/thumbnails/6-bali-diving-underwater.jpg', 1),
(10, 'https://balidiving.com/images/thumbnails/7-bali-diving-underwater.jpg', 1),
(11, 'https://balidiving.com/images/thumbnails/8-bali-diving-underwater.jpg', 1),
(12, 'https://balidiving.com/images/thumbnails/9-bali-diving-underwater.jpg', 1),
(13, 'https://balidiving.com/images/thumbnails/10-bali-diving-underwater.jpg', 1),
(14, 'https://balidiving.com/images/thumbnails/11-bali-diving-underwater.jpg', 1),
(15, 'https://balidiving.com/images/thumbnails/12-bali-diving-underwater.jpg', 1),
(16, 'https://balidiving.com/images/thumbnails/13-bali-diving-underwater.jpg', 1),
(17, 'https://balidiving.com/images/thumbnails/2-bali-diving-underwater.jpg', 1),
(18, 'https://balidiving.com/images/thumbnails/3-bali-diving-underwater.jpg', 1),
(19, 'https://balidiving.com/images/thumbnails/4-bali-diving-underwater.jpg', 1),
(20, 'https://balidiving.com/images/thumbnails/5-bali-diving-underwater.jpg', 1),
(21, 'https://balidiving.com/images/thumbnails/6-bali-diving-underwater.jpg', 1),
(22, 'https://balidiving.com/images/thumbnails/7-bali-diving-underwater.jpg', 1),
(23, 'https://balidiving.com/images/thumbnails/8-bali-diving-underwater.jpg', 1),
(24, 'https://balidiving.com/images/thumbnails/9-bali-diving-underwater.jpg', 1);

INSERT INTO bd_catalog_variants (product_id, variant_key, label, description, price_usd) VALUES
-- 20: PADI DSD
(20, '20_padi_dsd_standard', 'PADI Discover Scuba Diving – Standard', 'Confident first PADI experience: pool practice + 1 easy ocean dive with instructor.', 95.00),
(20, '20_padi_dsd_plus', 'PADI Discover Scuba Diving – Extra Ocean Dive', 'Same PADI intro plus 2nd ocean dive for more time underwater and photos.', 130.00),

-- 21: OWD
(21, '21_padi_ow_standard', 'PADI Open Water Diver – Standard', 'Classic 3-day PADI Open Water course: theory, pool, and 4 open water dives.', 450.00),
(21, '21_padi_ow_premium', 'PADI Open Water Diver – Premium', 'More personal time with instructor, smaller group and flexible training pace.', 520.00),

-- 22: AOW
(22, '22_padi_aow_standard', 'PADI Advanced Open Water – 5 Adventure Dives', 'Deep, navigation + 3 more PADI adventure dives tailored to Bali conditions.', 380.00),
(22, '22_padi_aow_deep_drift', 'PADI Advanced – Deep & Drift Focus', 'Advanced course with extra coaching for deep dives and Bali-style currents.', 410.00),

-- 23: Specialty
(23, '23_padi_nitrox', 'PADI Enriched Air (Nitrox)', 'Official PADI Nitrox specialty to extend no-stop limits on repetitive dives.', 280.00),
(23, '23_padi_wreck_combo', 'PADI Wreck + Photo Coaching', 'Wreck-focused PADI diving with extra tips for better Tulamben photo sessions.', 320.00),

-- 24: DM
(24, '24_padi_dm_standard', 'PADI Divemaster – Standard Internship', 'Start your professional path in the PADI system, assisting courses & guiding.', 950.00),
(24, '24_padi_dm_extended', 'PADI Divemaster – Extended Pro', 'Longer internship with more dives, mentoring, and leadership experience.', 1200.00);

INSERT INTO bd_catalog_addons (addon_key, name, price_usd) VALUES
('gopro', 'GoPro Camera Rental', 15.00),
('computer', 'Dive Computer Rental', 10.00);
