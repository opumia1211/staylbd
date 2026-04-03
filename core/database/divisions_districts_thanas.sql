-- Bangladesh Divisions, Districts, Thanas
-- Run this in your database (e.g. wintersm_tt) then run: php artisan db:seed --class=BangladeshDivisionDistrictThanaSeeder
-- to fill thanas. Or use: php artisan migrate (migrations create tables) then run the seeder.

-- USE wintersm_tt;

DROP TABLE IF EXISTS thanas;
DROP TABLE IF EXISTS districts;
DROP TABLE IF EXISTS divisions;

CREATE TABLE divisions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name_en VARCHAR(100) NOT NULL,
    name_bn VARCHAR(100) NOT NULL,
    sort_order TINYINT UNSIGNED DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE districts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    division_id BIGINT UNSIGNED NOT NULL,
    name_en VARCHAR(100) NOT NULL,
    name_bn VARCHAR(100) NOT NULL,
    sort_order TINYINT UNSIGNED DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX districts_division_id_index (division_id),
    FOREIGN KEY (division_id) REFERENCES divisions(id) ON DELETE CASCADE
);

CREATE TABLE thanas (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    district_id BIGINT UNSIGNED NOT NULL,
    name_en VARCHAR(150) NOT NULL,
    name_bn VARCHAR(150) NOT NULL,
    sort_order SMALLINT UNSIGNED DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX thanas_district_id_index (district_id),
    FOREIGN KEY (district_id) REFERENCES districts(id) ON DELETE CASCADE
);

-- 8 Divisions
INSERT INTO divisions (name_en, name_bn, sort_order, created_at, updated_at) VALUES
('Barishal','বরিশাল',1,NOW(),NOW()),
('Chattogram','চট্টগ্রাম',2,NOW(),NOW()),
('Dhaka','ঢাকা',3,NOW(),NOW()),
('Khulna','খুলনা',4,NOW(),NOW()),
('Mymensingh','ময়মনসিংহ',5,NOW(),NOW()),
('Rajshahi','রাজশাহী',6,NOW(),NOW()),
('Rangpur','রংপুর',7,NOW(),NOW()),
('Sylhet','সিলেট',8,NOW(),NOW());

-- 64 Districts (division_id 1=Barishal, 2=Chattogram, 3=Dhaka, 4=Khulna, 5=Mymensingh, 6=Rajshahi, 7=Rangpur, 8=Sylhet)
INSERT INTO districts (division_id, name_en, name_bn, sort_order, created_at, updated_at) VALUES
(1,'Barguna','বরগুনা',1,NOW(),NOW()),(1,'Barisal','বরিশাল',2,NOW(),NOW()),(1,'Bhola','ভোলা',3,NOW(),NOW()),(1,'Jhalokati','ঝালকাঠি',4,NOW(),NOW()),(1,'Patuakhali','পটুয়াখালী',5,NOW(),NOW()),(1,'Pirojpur','পিরোজপুর',6,NOW(),NOW()),
(2,'Bandarban','বান্দরবান',7,NOW(),NOW()),(2,'Brahmanbaria','ব্রাহ্মণবাড়িয়া',8,NOW(),NOW()),(2,'Chandpur','চাঁদপুর',9,NOW(),NOW()),(2,'Chittagong','চট্টগ্রাম',10,NOW(),NOW()),(2,'Comilla','কুমিল্লা',11,NOW(),NOW()),(2,'Cox''s Bazar','কক্সবাজার',12,NOW(),NOW()),(2,'Feni','ফেনী',13,NOW(),NOW()),(2,'Khagrachhari','খাগড়াছড়ি',14,NOW(),NOW()),(2,'Lakshmipur','লক্ষ্মীপুর',15,NOW(),NOW()),(2,'Noakhali','নোয়াখালী',16,NOW(),NOW()),(2,'Rangamati','রাঙ্গামাটি',17,NOW(),NOW()),
(3,'Dhaka','ঢাকা',18,NOW(),NOW()),(3,'Faridpur','ফরিদপুর',19,NOW(),NOW()),(3,'Gazipur','গাজীপুর',20,NOW(),NOW()),(3,'Gopalganj','গোপালগঞ্জ',21,NOW(),NOW()),(3,'Kishoreganj','কিশোরগঞ্জ',22,NOW(),NOW()),(3,'Madaripur','মাদারীপুর',23,NOW(),NOW()),(3,'Manikganj','মানিকগঞ্জ',24,NOW(),NOW()),(3,'Munshiganj','মুন্সিগঞ্জ',25,NOW(),NOW()),(3,'Narayanganj','নারায়ণগঞ্জ',26,NOW(),NOW()),(3,'Narsingdi','নরসিংদী',27,NOW(),NOW()),(3,'Rajbari','রাজবাড়ী',28,NOW(),NOW()),(3,'Shariatpur','শরীয়তপুর',29,NOW(),NOW()),(3,'Tangail','টাঙ্গাইল',30,NOW(),NOW()),
(4,'Bagerhat','বাগেরহাট',31,NOW(),NOW()),(4,'Chuadanga','চুয়াডাঙ্গা',32,NOW(),NOW()),(4,'Jessore','যশোর',33,NOW(),NOW()),(4,'Jhenaidah','ঝিনাইদহ',34,NOW(),NOW()),(4,'Khulna','খুলনা',35,NOW(),NOW()),(4,'Kushtia','কুষ্টিয়া',36,NOW(),NOW()),(4,'Magura','মাগুরা',37,NOW(),NOW()),(4,'Meherpur','মেহেরপুর',38,NOW(),NOW()),(4,'Narail','নড়াইল',39,NOW(),NOW()),(4,'Satkhira','সাতক্ষীরা',40,NOW(),NOW()),
(5,'Jamalpur','জামালপুর',41,NOW(),NOW()),(5,'Mymensingh','ময়মনসিংহ',42,NOW(),NOW()),(5,'Netrokona','নেত্রকোণা',43,NOW(),NOW()),(5,'Sherpur','শেরপুর',44,NOW(),NOW()),
(6,'Bogra','বগুড়া',45,NOW(),NOW()),(6,'Joypurhat','জয়পুরহাট',46,NOW(),NOW()),(6,'Naogaon','নওগাঁ',47,NOW(),NOW()),(6,'Natore','নাটোর',48,NOW(),NOW()),(6,'Chapainawabganj','চাঁপাইনবাবগঞ্জ',49,NOW(),NOW()),(6,'Pabna','পাবনা',50,NOW(),NOW()),(6,'Rajshahi','রাজশাহী',51,NOW(),NOW()),(6,'Sirajganj','সিরাজগঞ্জ',52,NOW(),NOW()),
(7,'Dinajpur','দিনাজপুর',53,NOW(),NOW()),(7,'Gaibandha','গাইবান্ধা',54,NOW(),NOW()),(7,'Kurigram','কুড়িগ্রাম',55,NOW(),NOW()),(7,'Lalmonirhat','লালমনিরহাট',56,NOW(),NOW()),(7,'Nilphamari','নীলফামারী',57,NOW(),NOW()),(7,'Panchagarh','পঞ্চগড়',58,NOW(),NOW()),(7,'Rangpur','রংপুর',59,NOW(),NOW()),(7,'Thakurgaon','ঠাকুরগাঁও',60,NOW(),NOW()),
(8,'Habiganj','হবিগঞ্জ',61,NOW(),NOW()),(8,'Moulvibazar','মৌলভীবাজার',62,NOW(),NOW()),(8,'Sunamganj','সুনামগঞ্জ',63,NOW(),NOW()),(8,'Sylhet','সিলেট',64,NOW(),NOW());

-- Thanas: run Laravel seeder for full list: php artisan db:seed --class=BangladeshDivisionDistrictThanaSeeder
