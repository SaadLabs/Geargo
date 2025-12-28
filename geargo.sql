-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 24, 2025 at 01:28 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `geargo`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
CREATE TABLE IF NOT EXISTS `cart` (
  `cart_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cart_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cartitem`
--

DROP TABLE IF EXISTS `cartitem`;
CREATE TABLE IF NOT EXISTS `cartitem` (
  `cart_item_id` int NOT NULL AUTO_INCREMENT,
  `cart_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  PRIMARY KEY (`cart_item_id`),
  KEY `cart_id` (`cart_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `category_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `name`) VALUES
(1, 'Headphones'),
(2, 'Smartwatches'),
(3, 'Laptops'),
(4, 'Gaming Accessories'),
(5, 'Mobile and Tablets');

-- --------------------------------------------------------

--
-- Table structure for table `orderitem`
--

DROP TABLE IF EXISTS `orderitem`;
CREATE TABLE IF NOT EXISTS `orderitem` (
  `order_item_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `price_at_purchase` decimal(10,0) NOT NULL,
  `quantity` int NOT NULL,
  PRIMARY KEY (`order_item_id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `total_amount` decimal(10,0) NOT NULL,
  `status` varchar(15) NOT NULL,
  `delievery_address` text NOT NULL,
  `city` varchar(30) NOT NULL,
  `postal_code` varchar(8) NOT NULL,
  `country` varchar(30) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

DROP TABLE IF EXISTS `payment`;
CREATE TABLE IF NOT EXISTS `payment` (
  `payment_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `card_id` int NOT NULL,
  `amount` decimal(10,0) NOT NULL,
  `payment_status` varchar(25) NOT NULL,
  `paid_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`payment_id`),
  KEY `order_id` (`order_id`),
  KEY `card_id` (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE IF NOT EXISTS `product` (
  `product_id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `brand` varchar(50) NOT NULL,
  `color` varchar(30) NOT NULL,
  `price` decimal(10,0) NOT NULL,
  `stock_quantity` int NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `image` varchar(255) NOT NULL,
  `category_id` int NOT NULL,
  `created_by` int NOT NULL,
  PRIMARY KEY (`product_id`),
  KEY `category_id` (`category_id`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `title`, `description`, `brand`, `color`, `price`, `stock_quantity`, `is_active`, `image`, `category_id`, `created_by`) VALUES
(1, 'Midnight Navy Wireless Over-Ear Headphones', 'Step out in style with these sophisticated midnight navy wireless headphones. The deep blue matte finish is understated yet elegant. Under the hood, they pack a punch with signature bass-heavy sound that brings energy to your playlists. The memory foam ear cups isolate passive noise effectively, while the Bluetooth 5.2 connection ensures stable streaming. Includes a carrying case and backup audio cable for wired listening.', 'Beats', 'Navy Blue', 7500, 20, 1, 'assets/products/headphone collection/01.png', 1, 1),
(2, 'Pure Snow White Noise Cancelling Headphones', 'Experience the purity of sound with these minimalist snow-white headphones. Featuring advanced hybrid active noise cancellation, they effectively block out ambient noise for an uninterrupted audio experience. The high-resolution audio drivers deliver deep, resonant bass and sparkling highs. With a sleek, foldable design and intuitive touch controls on the ear cup, these headphones blend modern aesthetics with powerful performance.', 'Sennheiser', 'White', 8800, 20, 1, 'assets/products/headphone collection/02.png', 1, 1),
(3, 'Blush Pink Lightweight On-Ear Headphones', 'Add a touch of softness to your tech collection with these blush pink on-ear headphones. Their compact, foldable design makes them incredibly portable, fitting easily into small bags. Despite their size, they deliver surprisingly big sound with clear vocals and defined instrumentals. The soft ear cushions rest gently on the ears, making them perfect for casual listening, podcasts, or video calls on the go.', 'Philips', 'Pink', 8100, 20, 1, 'assets/products/headphone collection/03.png', 1, 1),
(4, 'Lilac Purple Bass Boost Headphones', 'Express your personality with these trendy lilac purple headphones. Designed for bass lovers, they feature a dedicated bass boost button that instantly deepens the low-end frequencies. The lightweight frame and soft padded headband make them comfortable for all-day wear. With 40 hours of battery life and a built-in microphone for voice assistant support, they are as functional as they are fashionable.', 'Skullcandy', 'Purple', 8100, 20, 1, 'assets/products/headphone collection/04.png', 1, 1),
(5, 'Studio Black Professional Monitor Headphones', 'Achieve audio precision with these classic black studio monitor headphones. Built for creators and audiophiles, they deliver a flat frequency response for accurate sound reproduction. The circumaural design contours around the ears for excellent sound isolation in loud environments. Constructed with high-quality materials, they are robust enough for daily studio use while remaining comfortable for long mixing and mastering sessions.', 'Audio-Technica', 'Black', 5500, 20, 1, 'assets/products/headphone collection/05.png', 1, 1),
(6, 'Sandstone Beige Premium Wireless Headphones', 'Immerse yourself in luxury with these sandstone beige over-ear headphones. Designed for the discerning listener, they feature plush protein leather ear cushions that provide cloud-like comfort during extended listening sessions. The active noise-canceling technology silences the outside world, allowing the rich, warm audio profile to shine through. With a 30-hour battery life and quick-charge capability, they are the perfect travel companion for long flights or commutes.', 'Bose', 'Beige', 6600, 20, 1, 'assets/products/headphone collection/06.png', 1, 1),
(7, 'Olive Green Retro Style Headphones', 'Stand out from the crowd with these unique olive green headphones featuring a retro-inspired design. The grey ear pads contrast beautifully with the matte green finish. Sound-wise, they offer a balanced, natural audio profile suitable for all genres, from jazz to pop. The lightweight plastic construction is durable yet easy to carry, and the simple button interface allows for easy control of volume and tracks without reaching for your phone.', 'Panasonic', 'Green', 7700, 20, 1, 'assets/products/headphone collection/07.png', 1, 1),
(8, 'Heroic Red & Blue Children\'s Headphones', 'Spark your child\'s imagination with these vibrant red and blue headphones. Designed with safety first, they include an automatic volume limiter to ensure safe listening levels. The adjustable headband grows with your child, and the sturdy, tangle-free cable adds durability. Perfect for school, travel, or home use, these headphones deliver clear sound for educational apps and music without compromising on comfort or safety.', 'Sony', 'Red/Blue', 9300, 20, 1, 'assets/products/headphone collection/08.png', 1, 1),
(9, 'Kids Pastel Blue & Pink Safe-Sound Headphones', 'These delightful pastel blue and pink headphones are specially crafted for younger ears. They feature built-in volume-limiting technology capped at 85dB to prevent hearing damage. The construction is ultra-durable and flexible, able to withstand being twisted and bent by energetic kids. The soft, hypoallergenic ear pads ensure comfort during online classes or while watching cartoons, making them a safe and fun choice for children.', 'JBL', 'Blue/Pink', 7900, 20, 1, 'assets/products/headphone collection/09.png', 1, 1),
(10, 'Arctic White & Orange Gaming Headset', 'Dominate your game with this stylish arctic white headset featuring striking orange accents. Engineered for gamers, it boasts a lightweight suspension headband that self-adjusts for a perfect fit. The ear cups are lined with breathable sports-mesh fabric to keep you cool under pressure. Equipped with precision 40mm drivers and a crystal-clear boom microphone, your team communication will be flawless. Compatible with PC, consoles, and mobile devices.', 'Logitech', 'White/Orange', 9200, 20, 1, 'assets/products/headphone collection/10.png', 1, 1),
(11, 'Electric Blue & Lime Sport Headphones', 'Get pumped for your workout with these electric blue headphones featuring energetic lime green details. Designed for an active lifestyle, they are sweat-resistant and feature a secure clamping force that keeps them in place during movement. The punchy sound profile motivates you to push harder, while the durable materials can handle the rigors of the gym. Includes easy-to-locate tactile buttons for mid-workout control.', 'JLab', 'Blue/Green', 8400, 20, 1, 'assets/products/headphone collection/11.png', 1, 1),
(12, 'Crimson Red Minimalist Wireless Headphones', 'Make a bold statement with these deep crimson red wireless headphones. The seamless, minimalist design features a smooth matte finish and hidden joints. They offer superior sound quality with custom-tuned drivers for a rich, immersive soundstage. Features include proximity sensors that pause music when you remove them, aptX adaptive support for high-quality streaming, and superb call quality with dual beamforming microphones.', 'Urbanista', 'Red', 6300, 20, 1, 'assets/products/headphone collection/12.png', 1, 1),
(13, 'Space Grey Compact Student Laptop', 'Ideally sized for students and mobile professionals, this space grey compact laptop offers the perfect balance of performance and portability. The crisp Retina display is easy on the eyes during long study sessions, while the M1 chip ensures snappy performance for all your applications. With a battery that lasts up to 18 hours, you can leave the charger at home. The intuitive macOS makes integration with your iPhone seamless.', 'Apple', 'Space Grey', 228700, 20, 1, 'assets/products/laptop collection/01.png', 3, 1),
(14, 'Rose Gold Ultra-Portable Laptop', 'Make a statement with this elegant rose gold ultra-portable laptop. The luxurious finish is matched by a brilliant display that spans edge-to-edge. It features a whisper-quiet fanless design and a large Force Touch trackpad for superior control. Secure access via Touch ID adds a layer of convenience and safety to your digital life, making it the perfect choice for lifestyle bloggers and creatives.', 'Apple', 'Rose Gold', 247200, 20, 1, 'assets/products/laptop collection/02.png', 3, 1),
(15, 'Silver Air-Light Travel Laptop', 'Redefine portability with this silver air-light laptop. Weighing next to nothing, it disappears into your bag, ready for travel. Despite its size, it packs a punch with a speedy processor and instant-on capabilities. The True Tone display technology adjusts the screen warmth to your environment for a natural viewing experience. Perfect for writing, browsing, and media consumption on the go.', 'Apple', 'Silver', 210100, 20, 1, 'assets/products/laptop collection/03.png', 3, 1),
(16, 'Space Grey Pro Creative Laptop', 'Unleash your creativity with this space grey laptop designed for power users. The Liquid Retina XDR display delivers extreme dynamic range and stunning contrast, essential for color-critical work. The powerful M2 Pro chip allows for effortless multitasking between heavy applications like Final Cut Pro and Logic Pro. Its slim profile and long-lasting battery make it the ultimate mobile workstation.', 'Apple', 'Space Grey', 217100, 20, 1, 'assets/products/laptop collection/04.png', 3, 1),
(17, 'Silver High-Performance Pro Laptop', 'Experience unbridled power with this silver high-performance laptop. It features a studio-quality three-mic array and a six-speaker sound system with Spatial Audio for an immersive media experience. The high-refresh-rate ProMotion display makes scrolling and gaming incredibly smooth. Equipped with a wide array of ports including HDMI and SD card slot, connectivity is never an issue.', 'Apple', 'Silver', 314400, 20, 1, 'assets/products/laptop collection/05.png', 3, 1),
(18, 'Silver Multimedia Ultra-Book', 'Immerse yourself in entertainment with this sleek silver ultra-book. The vivid AMOLED display showcases billions of colors, making movies and games pop with realism. The ultra-slim bezel design provides a more immersive viewing experience. Under the hood, an Intel Core i7 processor ensures smooth performance for casual gaming and media consumption. A perfect blend of style and substance.', 'Samsung', 'Silver', 171100, 20, 1, 'assets/products/laptop collection/06.png', 3, 1),
(19, 'Sky Blue Slim Productivity Laptop', 'A marvel of engineering, this sky blue slim laptop combines performance with a unique aesthetic. The sleek aluminum chassis houses a powerful processor capable of handling demanding tasks with ease. Its vibrant display brings visuals to life with stunning color accuracy, making it perfect for creative professionals. With all-day battery life and a responsive backlit keyboard, it is designed for productivity anywhere.', 'Samsung', 'Sky Blue', 230700, 20, 1, 'assets/products/laptop collection/07.png', 3, 1),
(20, 'Pro Silver Business Workstation', 'Built for heavy-duty tasks, this silver performance workstation is a powerhouse. The large, immersive 15.6-inch display provides ample screen real estate for complex spreadsheets and multitasking. It features a full-sized keyboard with a numeric keypad for data entry efficiency. The robust military-grade build quality ensures it can withstand the rigors of daily travel and intensive corporate use.', 'HP', 'Silver', 328600, 20, 1, 'assets/products/laptop collection/08.png', 3, 1),
(21, 'Pro Grey Enterprise Laptop', 'Designed for the modern professional, this grey business laptop offers robust security features including a privacy shutter and fingerprint reader. The high-resolution anti-glare screen delivers crisp text and images, while the spacious trackpad ensures precise navigation. Equipped with a fast NVMe SSD and ample RAM, multitasking becomes seamless. Its understated design fits perfectly in any meeting room.', 'HP', 'Grey', 163900, 20, 1, 'assets/products/laptop collection/09.png', 3, 1),
(22, 'Silver Premium Graphic Laptop', 'Witness clarity like never before on this silver high-resolution laptop. The display boasts incredibly high pixel density, rendering text and images with print-quality sharpness. Ideal for photo editing and watching 4K content, it reveals details you\'ve never seen. The premium metal construction feels solid and cool to the touch, exuding quality and craftsmanship, powered by the latest graphics technology.', 'Samsung', 'Silver', 217500, 20, 1, 'assets/products/laptop collection/10.png', 3, 1),
(23, 'Space Grey Developer Laptop', 'The ultimate tool for developers, this space grey laptop offers sustained performance for compiling code and running virtual machines. The Magic Keyboard provides a comfortable and quiet typing experience, crucial for long coding sessions. With advanced thermal management, it stays cool under pressure. The Thunderbolt 4 ports enable high-speed data transfer and connection to multiple external displays.', 'Apple', 'Space Grey', 281300, 20, 1, 'assets/products/laptop collection/11.png', 3, 1),
(24, 'Space Grey Everyday Air Laptop', 'Your perfect daily companion, this space grey laptop balances efficiency with style. The fanless design ensures completely silent operation, making it ideal for quiet environments like libraries or classrooms. The battery life is phenomenal, easily lasting through a full day of work and play. With a brilliant Retina display and powerful neural engine, it handles everything from video calls to light gaming with ease.', 'Apple', 'Space Grey', 193400, 20, 1, 'assets/products/laptop collection/12.png', 3, 1),
(25, 'Titanium Gold Pro Max Smartphone', 'Discover the pinnacle of mobile technology with this Titanium Gold Pro smartphone. Crafted from aerospace-grade titanium, it is lighter and stronger than ever. The A17 Pro chip brings console-level gaming to your pocket. It features a pro camera system with a 5x telephoto lens, capturing stunning photos from a distance. The all-day battery life and USB-C connectivity ensure you stay powered and connected wherever you go.', 'Apple', 'Titanium Gold', 62100, 20, 1, 'assets/products/mobile collection/01.png', 5, 1),
(26, 'Pastel Pink Modern Smartphone', 'Embrace modern elegance with this pastel pink smartphone. It features a ceramic shield front that is tougher than any smartphone glass. The advanced dual-camera system lets you capture beautiful photos in low light and cinematic 4K video. With a Super Retina XDR display, everything looks incredible. It also supports MagSafe accessories for easy attachment and faster wireless charging.', 'Apple', 'Pink', 51200, 20, 1, 'assets/products/mobile collection/02.png', 5, 1),
(27, 'Sky Blue Dual-Camera Smartphone', 'Capture your world in style with this sky blue smartphone. The durable aluminum design with a glass back feels premium in the hand. It boasts an advanced dual-camera system for impressive photos and videos. The A15 Bionic chip delivers lightning-fast performance for apps and games. Enjoy peace of mind with industry-leading water resistance and a battery that lasts all day.', 'Apple', 'Sky Blue', 79300, 20, 1, 'assets/products/mobile collection/03.png', 5, 1),
(28, 'Lavender Purple Compact Smartphone', 'This lavender purple smartphone is designed to stand out. It features a bright and colorful OLED display that makes movies and photos pop. The powerful cinematic mode adds shallow depth of field to your videos automatically. With 5G capability, you can download movies on the fly and stream high-quality video. Its compact size makes it easy to use with one hand.', 'Apple', 'Purple', 84100, 20, 1, 'assets/products/mobile collection/04.png', 5, 1),
(29, 'Mint Green AI Portrait Smartphone', 'Refresh your mobile experience with this mint green smartphone. It specializes in AI-enhanced portrait photography, ensuring you always look your best. The 50MP main camera captures intricate details, while the large battery with SuperVOOC charging keeps you going for days. The 90Hz sunlight-readable display ensures smooth scrolling and clear visibility even under bright outdoor light.', 'Oppo', 'Mint Green', 82900, 20, 1, 'assets/products/mobile collection/05.png', 5, 1),
(30, 'Spectrum Glow Holographic Smartphone', 'Shine bright with this spectrum glow smartphone featuring a unique holographic finish. The Reno Glow design creates a shimmering effect that changes with the light. It is equipped with a professional portrait camera system for DSLR-like bokeh effects. The ultra-slim body is lightweight and comfortable to hold. Powered by a fast processor, it handles multitasking and gaming with ease.', 'Oppo', 'Holographic/Gold', 41500, 20, 1, 'assets/products/mobile collection/06.png', 5, 1),
(31, 'Aurora Blue Gradient Smartphone', 'Experience the beauty of the aurora with this blue gradient smartphone. The back panel shifts colors beautifully, resisting fingerprints and scratches. It features a large, immersive display perfect for streaming and gaming. The high-capacity battery supports fast charging, so you spend less time tethered to a wall. The AI triple camera setup adapts to any lighting condition for the perfect shot.', 'Oppo', 'Blue Gradient', 86300, 20, 1, 'assets/products/mobile collection/07.png', 5, 1),
(32, 'Midnight Blue Matte Smartphone', 'Sleek, stealthy, and powerful, this midnight blue smartphone features a premium matte finish. The 64MP AI camera captures ultra-clear images, day or night. It runs on a high-performance chipset optimized for smooth gaming and app usage. The display features a high refresh rate for fluid visual experiences. With generous storage options, you have plenty of room for all your photos and apps.', 'Oppo', 'Midnight Blue', 86900, 20, 1, 'assets/products/mobile collection/08.png', 5, 1),
(33, 'Prism White Galaxy Smartphone', 'Simple yet sophisticated, this prism white smartphone fits perfectly into your lifestyle. The seamless design features a triple-camera layout for versatile photography, from wide-angle landscapes to macro details. The vivid AMOLED display offers rich colors and deep blacks. With a long-lasting battery and expandable storage, it is a reliable daily driver for all your communication and entertainment needs.', 'Samsung', 'White', 94600, 20, 1, 'assets/products/mobile collection/09.png', 5, 1),
(34, 'Lemon Yellow Vibrant Smartphone', 'Add a pop of color to your life with this vibrant lemon yellow smartphone. The playful design is matched by a fun and intuitive user interface. It features a versatile multi-lens camera system to capture all your favorite moments. The battery is designed to last up to two days on a single charge. With a sharp display and Dolby Atmos sound, it\'s a pocket-sized entertainment hub.', 'Samsung', 'Yellow', 93800, 20, 1, 'assets/products/mobile collection/10.png', 5, 1),
(35, 'Pearl White Gradient 5G Smartphone', 'Step into the future with this pearl white 5G-ready smartphone. The back panel features an iridescent gradient that mimics the sheen of a pearl. It boasts a massive edge-to-edge infinity display for an immersive viewing experience. The quad-camera setup includes a macro lens for close-ups and an ultra-wide lens for landscapes. Security is integrated seamlessly with a side-mounted fingerprint scanner.', 'Samsung', 'Pearl White', 114300, 20, 1, 'assets/products/mobile collection/11.png', 5, 1),
(36, 'Deep Blue Galaxy Smartphone', 'Reliability meets style in this deep blue smartphone. The textured back ensures a solid grip and resists smudges. It packs a robust battery that easily powers through intense usage. The intuitive One UI software provides a smooth and clutter-free experience. Whether you are video calling or browsing social media, the crisp HD+ display and clear front-facing camera deliver excellent quality.', 'Samsung', 'Deep Blue', 82400, 20, 1, 'assets/products/mobile collection/12.png', 5, 1),
(37, 'Soft Pink Aluminum Series Smartwatch', 'Stay connected and stylish with this soft pink aluminum smartwatch. The bright, always-on Retina display ensures you never miss a notification or fitness metric. It features advanced health monitoring sensors, including heart rate and blood oxygen tracking. The comfortable silicone sport band is perfect for workouts and all-day wear. With water resistance up to 50 meters, it is swim-proof and ready for any adventure.', 'Apple', 'Pink', 402400, 20, 1, 'assets/products/smartwatch collection/01.png', 2, 1),
(38, 'Silver Aluminum Smartwatch with Sport Loop', 'Experience the perfect blend of form and function with this silver aluminum smartwatch. Paired with a breathable blue sport loop, it offers a secure and comfortable fit for active lifestyles. The powerful dual-core processor delivers smooth performance for apps and Siri interactions. Track your sleep stages, mindfulness, and daily activity rings to maintain a healthy balance.', 'Apple', 'Silver/Blue', 414700, 20, 1, 'assets/products/smartwatch collection/02.png', 2, 1),
(39, 'Starlight Aluminum Series Smartwatch', 'Elegant and versatile, this starlight aluminum smartwatch complements any outfit. The neutral beige tone is sophisticated, while the durable construction withstands daily wear and tear. It comes equipped with crash detection and fall detection safety features for peace of mind. The all-day battery life keeps you powered from morning workouts to evening notifications.', 'Apple', 'Beige', 199100, 20, 1, 'assets/products/smartwatch collection/03.png', 2, 1),
(40, 'Titanium Ultra Smartwatch with Trail Loop', 'Push your limits with this rugged titanium ultra smartwatch. Designed for endurance athletes, it features a lightweight aerospace-grade titanium case and a specialized purple/black trail loop for a secure fit. The precision dual-frequency GPS provides accurate location data in challenging environments. With a battery life of up to 36 hours, it is built to go the distance.', 'Apple', 'Purple', 224000, 20, 1, 'assets/products/smartwatch collection/04.png', 2, 1),
(41, 'Titanium Ultra Smartwatch with Alpine Loop', 'Adventure awaits with this robust titanium ultra smartwatch featuring an olive green alpine loop. The high-contrast display remains visible even in direct sunlight, and the customizable Action button allows for quick access to key functions. It includes a depth gauge and water temperature sensor for divers. The rugged design is tested to military standards for shock and temperature resistance.', 'Apple', 'Green', 389500, 20, 1, 'assets/products/smartwatch collection/05.png', 2, 1),
(42, 'Cream Gold Smartwatch', 'Add a touch of luxury to your wrist with this cream gold smartwatch. The smooth finish and minimalist design make it a timeless accessory. It seamlessly integrates with your smartphone to deliver messages, calls, and app alerts directly to your wrist. The comprehensive fitness tracking suite covers everything from yoga to swimming, helping you stay motivated and reach your goals.', 'Apple', 'Cream', 445000, 20, 1, 'assets/products/smartwatch collection/06.png', 2, 1),
(43, 'Blue Horizon Smartwatch', 'Navigate your day with this sleek blue horizon smartwatch. The navy blue band offers a classic look that works for both the office and the gym. It features a large, crack-resistant front crystal and IP6X dust resistance. Stay on top of your health with irregular rhythm notifications and ECG app capability. The fast charging feature gets you back up and running in no time.', 'Apple', 'Navy Blue', 143800, 20, 1, 'assets/products/smartwatch collection/07.png', 2, 1),
(44, 'Midnight Aluminum Smartwatch', 'Sleek and stealthy, this midnight aluminum smartwatch is the ultimate modern accessory. The deep black finish is paired with a matching sport band for a unified look. It offers a wide range of customizable watch faces to suit your style and needs. With built-in cellular capability options, you can stream music and make calls even without your phone nearby.', 'Apple', 'Black', 258100, 20, 1, 'assets/products/smartwatch collection/08.png', 2, 1),
(45, 'White Fitness Tracker Smartwatch', 'Keep your fitness goals on track with this pristine white fitness smartwatch. The vivid rectangular AMOLED display provides clear visibility of your stats. It supports over 90 workout modes and automatic workout detection. The lightweight design ensures comfort during sleep tracking, providing insights into your rest quality. With a battery life of up to 10 days, you can focus more on moving and less on charging.', 'Huawei', 'White', 286700, 20, 1, 'assets/products/smartwatch collection/09.png', 2, 1),
(46, 'Pink Fitness Tracker Smartwatch', 'Brighten up your workout routine with this cheerful pink fitness smartwatch. Designed for energy and movement, it features a soft, skin-friendly strap. It monitors your heart rate 24/7 and tracks stress levels to help you maintain wellness. The intuitive interface makes it easy to control music playback and check weather updates on the go. Perfect for those starting their fitness journey.', 'Xiaomi', 'Pink', 135100, 20, 1, 'assets/products/smartwatch collection/10.png', 2, 1),
(47, 'Midnight Black Fitness Smartwatch', 'Track your progress with precision using this midnight black fitness smartwatch. The large, easy-to-read rectangular screen shows your steps, calories, and notifications at a glance. It features a durable silicone strap designed for intense sweat sessions. The built-in SpO2 sensor helps you understand your physical state better, making it an essential tool for health-conscious individuals.', 'Xiaomi', 'Black', 339000, 20, 1, 'assets/products/smartwatch collection/11.png', 2, 1),
(48, 'Emerald Green Sport Smartwatch', 'Make a bold statement with this emerald green sport smartwatch. The unique square face design is framed by a premium metallic bezel, giving it a modern and rugged look. The textured green silicone strap ensures a secure grip on your wrist during runs or hikes. Packed with sports modes and health tracking features, it combines style with serious performance.', 'Xcell', 'Green', 337000, 20, 1, 'assets/products/smartwatch collection/12.png', 2, 1),
(49, 'Graphite Grey Professional Tablet', 'Maximize your productivity with this graphite grey professional tablet. The large, immersive display is perfect for multitasking, allowing you to run multiple apps side-by-side. Included with the S-Pen, it offers low latency for natural writing and drawing. The all-day battery life and powerful processor make it an ideal choice for business professionals and students alike.', 'Samsung', 'Grey', 238500, 20, 1, 'assets/products/tablet collection/01.png', 5, 1),
(50, 'Glacier White Family Tablet', 'Enjoy entertainment for the whole family with this glacier white tablet. Its bright and clear display brings movies and games to life. The durable design is built to withstand everyday use, making it safe for kids. With expandable storage, you can keep all your favorite photos and videos offline. The dual speakers provide rich audio for an immersive viewing experience.', 'Samsung', 'White', 241600, 20, 1, 'assets/products/tablet collection/02.png', 5, 1),
(51, 'Lavender Purple Creative Tablet', 'Unleash your artistic side with this lavender purple creative tablet. The vibrant screen reproduces colors with stunning accuracy, perfect for digital art and photo editing. It features a water-resistant design, so you can take it anywhere without worry. The bundled stylus snaps magnetically to the back for charging, ensuring you are always ready to create when inspiration strikes.', 'Samsung', 'Purple', 195600, 20, 1, 'assets/products/tablet collection/03.png', 5, 1),
(52, 'Mint Green Student Tablet', 'Study smarter with this fresh mint green tablet. Its lightweight and slim profile fits easily into any backpack. The long-lasting battery ensures you get through a full day of classes without needing a charge. It supports seamless file sharing with other devices, making group projects a breeze. The high-resolution front camera is perfect for attending online lectures clearly.', 'Samsung', 'Green', 229800, 20, 1, 'assets/products/tablet collection/04.png', 5, 1),
(53, 'Navy Blue Entertainment Tablet', 'Dive into your favorite shows with this navy blue entertainment tablet. It boasts a cinematic wide display that offers an expansive viewing area. The fast processor ensures smooth gaming performance and quick app launches. With Samsung Kids mode, you can create a safe digital environment for your children. The sleek metal body feels premium and is comfortable to hold for hours.', 'Samsung', 'Navy Blue', 181100, 20, 1, 'assets/products/tablet collection/05.png', 5, 1),
(54, 'Space Grey Educational Tablet', 'Empower learning with this reliable space grey educational tablet. The 2K resolution display delivers sharp text and images, reducing eye strain during reading. It features a specialized reading mode that mimics the appearance of paper. The high-fidelity speakers with Dolby Atmos support provide an engaging audio experience for educational videos. Sturdy and portable, it is built for the classroom.', 'Lenovo', 'Grey', 236300, 20, 1, 'assets/products/tablet collection/06.png', 5, 1),
(55, 'Rose Pink Multimedia Tablet', 'Add a splash of color to your tech with this rose pink multimedia tablet. The ultra-slim design is both stylish and easy to handle. It features a high-performance battery that supports fast charging, keeping you connected longer. The sharp 8MP front and rear cameras are great for video calls and capturing documents. Ideal for streaming, browsing, and social media on the go.', 'Realme', 'Pink', 180100, 20, 1, 'assets/products/tablet collection/07.png', 5, 1),
(56, 'Sky Blue Ultra-Slim Tablet', 'Experience true portability with this sky blue ultra-slim tablet. The unibody aluminum design is incredibly lightweight yet durable. The 90Hz refresh rate display ensures buttery smooth scrolling and animations. It is equipped with quad speakers that deliver surround sound, transforming your tablet into a portable theater. Perfect for gaming and watching movies while traveling.', 'Xiaomi', 'Sky Blue', 177400, 20, 1, 'assets/products/tablet collection/08.png', 5, 1),
(57, 'Silver Horizon Pro Tablet', 'Expand your horizons with this silver pro tablet featuring a massive edge-to-edge display. The high screen-to-body ratio provides an immersive visual experience for work and play. It supports multi-screen collaboration, allowing you to control your phone from your tablet. The TÜV Rheinland certification ensures low blue light emission, protecting your eyes during late-night study sessions.', 'Huawei', 'Silver', 166000, 20, 1, 'assets/products/tablet collection/09.png', 5, 1),
(58, 'Iron Grey Gaming Tablet', 'Dominate the leaderboard with this iron grey gaming tablet. Powered by a high-end chipset, it handles graphically intensive games with ease. The large battery ensures you can game for hours without interruption. It features a specialized game mode that optimizes performance and blocks notifications. The crisp, high-resolution screen reveals every detail in the game world.', 'Lenovo', 'Grey', 156200, 20, 1, 'assets/products/tablet collection/10.png', 5, 1),
(59, 'Starlight Gold Elegant Tablet', 'Elegance meets performance in this starlight gold tablet. The shimmering finish resists fingerprints and adds a touch of sophistication. It runs on a unified operating system that works seamlessly with your other devices. The massive battery is supported by ultra-fast charging technology, getting you back to 100% in minutes. Ideal for executives and style-conscious users.', 'Honor', 'Gold', 213300, 20, 1, 'assets/products/tablet collection/11.png', 5, 1),
(60, 'Ocean Blue Connectivity Tablet', 'Stay connected everywhere with this ocean blue tablet featuring built-in LTE support. The vibrant blue finish is inspired by the deep sea. It offers a desktop-like experience with windowed apps and keyboard support. The crystal-clear microphone system ensures your voice is heard perfectly during conference calls. A reliable companion for remote work and travel.', 'Honor', 'Blue', 225800, 20, 1, 'assets/products/tablet collection/12.png', 5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `email` varchar(40) NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `phone` varchar(11) NOT NULL,
  `role` enum('admin','customer','manager') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `name`, `email`, `password`, `phone`, `role`, `created_at`) VALUES
(1, 'Khizar Nadeem', 'khizarnadeem324@gmail.com', '$2y$10$9ofFDJ1SPmH/b6UgSpNh/.EH3LCsGgMr7sVlYz79HI.iFIZqyyKea', '03320777167', 'admin', '2025-12-17 12:22:10');

-- --------------------------------------------------------

--
-- Table structure for table `usercards`
--

DROP TABLE IF EXISTS `usercards`;
CREATE TABLE IF NOT EXISTS `usercards` (
  `card_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `card_number` varchar(20) NOT NULL,
  `cvv` int NOT NULL,
  `exp_month` tinyint NOT NULL,
  `exp_year` smallint NOT NULL,
  `card_holder_name` varchar(50) NOT NULL,
  PRIMARY KEY (`card_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `cartitem`
--
ALTER TABLE `cartitem`
  ADD CONSTRAINT `cartitem_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`cart_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `cartitem_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `orderitem`
--
ALTER TABLE `orderitem`
  ADD CONSTRAINT `orderitem_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `orderitem_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `payment_ibfk_2` FOREIGN KEY (`card_id`) REFERENCES `usercards` (`card_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `product_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `user` (`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `usercards`
--
ALTER TABLE `usercards`
  ADD CONSTRAINT `usercards_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
