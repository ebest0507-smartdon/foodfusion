-- FoodFusion database
-- Creates foodfusion_db and loads sample rows that match includes/data.php.
-- Passwords are password_hash() values (PASSWORD_DEFAULT / bcrypt).
-- Classroom passwords:
--   amara@foodfusion.test  Paprika#2026
--   leo@foodfusion.test    Risotto#2026
--   priya@foodfusion.test  Harvest#2026
-- failed_attempts and lockout_time support the sign-in rule in login.php:
-- lock the account for 3 minutes after 3 failed checks, then reset the counter.
-- Sample accounts start unlocked. The PHP demo stores the same facts in the
-- session so the site can run before MySQL is connected.

CREATE DATABASE IF NOT EXISTS foodfusion_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE foodfusion_db;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS contacts;
DROP TABLE IF EXISTS community_posts;
DROP TABLE IF EXISTS recipes;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  first_name VARCHAR(80) NOT NULL,
  last_name VARCHAR(80) NOT NULL,
  email VARCHAR(190) NOT NULL,
  password VARCHAR(255) NOT NULL,
  failed_attempts INT NOT NULL DEFAULT 0,
  lockout_time DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE categories (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(80) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_categories_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE recipes (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT UNSIGNED NOT NULL,
  category_id INT UNSIGNED NOT NULL,
  title VARCHAR(160) NOT NULL,
  description TEXT NOT NULL,
  ingredients TEXT NOT NULL,
  instructions TEXT NOT NULL,
  cuisine_type VARCHAR(80) NOT NULL,
  dietary_pref VARCHAR(80) NOT NULL,
  difficulty ENUM('Easy', 'Medium', 'Hard') NOT NULL,
  image_url VARCHAR(500) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_recipes_user (user_id),
  KEY idx_recipes_category (category_id),
  KEY idx_recipes_cuisine (cuisine_type),
  KEY idx_recipes_difficulty (difficulty),
  CONSTRAINT fk_recipes_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
  CONSTRAINT fk_recipes_category FOREIGN KEY (category_id) REFERENCES categories (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE community_posts (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT UNSIGNED NOT NULL,
  title VARCHAR(160) NOT NULL,
  content TEXT NOT NULL,
  tip_type VARCHAR(80) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_posts_user (user_id),
  CONSTRAINT fk_posts_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE contacts (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL,
  subject VARCHAR(160) NOT NULL,
  message TEXT NOT NULL,
  submitted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (id, first_name, last_name, email, password, failed_attempts, lockout_time, created_at) VALUES
(1, 'Amara', 'Okonkwo', 'amara@foodfusion.test', '$2y$10$4I.Y1Ntpe5kjKJbXfZ9uluCdbIaCCSgF6BDu/ZFMw5Mq1tLoR6T8G', 0, NULL, '2024-11-02 09:30:00'),
(2, 'Leo', 'Martin', 'leo@foodfusion.test', '$2y$10$Zlk9Wc/UWQr8NSY52OCWAOwJYJaAJd9z2sYhga89r73RM36OzwMqK', 0, NULL, '2025-01-18 14:10:00'),
(3, 'Priya', 'Shah', 'priya@foodfusion.test', '$2y$10$g5JiFEsCqN7xZj/BvOi18uFAxy5g0ujIJ3hq37Z1HVc5tSO1p1cZS', 0, NULL, '2025-03-09 11:05:00');

INSERT INTO categories (id, name) VALUES
(1, 'Weeknight'),
(2, 'Celebration'),
(3, 'Baking'),
(4, 'Broths and Stews'),
(5, 'Street Food'),
(6, 'Preserves and Sides');

INSERT INTO recipes (
  id, user_id, category_id, title, description, ingredients, instructions,
  cuisine_type, dietary_pref, difficulty, image_url, created_at
) VALUES
(
  1, 1, 2,
  'Jollof rice',
  'One pot of long-grain rice cooked in a tomato, onion, and thyme base until the grains are separate and the colour runs deep orange.',
  '400g long-grain rice; 4 tbsp vegetable oil; 1 large onion; 3 tbsp tomato paste; 400g tin chopped tomatoes; 1 red pepper; 2 garlic cloves; 1 tsp thyme; 1 tsp curry powder; 1 tsp ground ginger; 1 bay leaf; 600ml hot stock; salt',
  'Fry onion and tomato paste until the oil turns red. Simmer blended tomatoes, pepper, and spices until thick. Add rice and stock, cover, and cook on low heat for 20 to 25 minutes. Rest and fluff.',
  'Ghanaian', 'Vegan', 'Medium',
  'https://images.unsplash.com/photo-1665332195309-9d75071138f0?auto=format&fit=crop&w=1200&q=80',
  '2026-08-02 18:00:00'
),
(
  2, 2, 4,
  'Banku and groundnut soup',
  'Fermented corn and cassava dough formed into soft banku, served with a rich peanut soup.',
  '300g banku mix; warm water; 1 cup peanut butter; 1 onion; 2 tbsp tomato paste; 2 tomatoes; 1 tsp ground ginger; 1 scotch bonnet; 500ml stock; smoked fish or chicken, optional; salt',
  'Steam banku until firm. Fry onion, paste, and tomatoes, then simmer with loosened peanut butter and stock for 20 minutes. Season and serve with the banku.',
  'Ghanaian', 'Omnivore', 'Hard',
  'images/banku-groundnut-soup.jpg',
  '2026-07-19 12:30:00'
),
(
  3, 1, 1,
  'Beans stew (red-red)',
  'Black-eyed beans simmered in palm oil with tomato and onion, served with fried ripe plantain and gari.',
  '400g black-eyed beans; 4 tbsp red palm oil; 1 large onion; 2 tbsp tomato paste; 3 tomatoes; 1 tsp ground ginger; 1 scotch bonnet, optional; salt; 2 ripe plantains; gari',
  'Simmer the beans until soft. Fry onion and paste in palm oil, add tomatoes and ginger, then the beans. Serve with fried plantain and gari.',
  'Ghanaian', 'Vegan', 'Easy',
  'images/beans-red-red.jpg',
  '2026-06-28 16:20:00'
),
(
  4, 3, 1,
  'Waakye',
  'Rice and beans cooked together with dried millet leaves for colour, then plated with shito, egg, spaghetti, and salad.',
  '300g long-grain rice; 200g black-eyed beans; dried millet leaves; 1 tsp baking soda, optional; 1 tsp salt; water; shito, egg, spaghetti, and salad to serve',
  'Simmer beans with millet leaves until almost soft. Add rice, salt, and water. Cover and cook until tender. Serve with the usual street sides.',
  'Ghanaian', 'Vegetarian', 'Medium',
  'images/waakye.jpg',
  '2026-09-01 19:10:00'
),
(
  5, 2, 4,
  'Fufu and light soup',
  'A peppery tomato and ginger soup with goat or chicken, eaten with soft fufu.',
  '500g goat or chicken; 1 onion; 3 tomatoes; 2 tbsp tomato paste; 1 tbsp grated ginger; 2 garlic cloves; 1 scotch bonnet; 1 tsp salt; 1 litre water; prepared fufu',
  'Steam the meat with onion, ginger, and garlic. Add tomatoes, paste, pepper, and water. Simmer until tender. Serve with fufu.',
  'Ghanaian', 'Omnivore', 'Hard',
  'images/fufu-light-soup.jpg',
  '2026-05-14 13:00:00'
),
(
  6, 3, 5,
  'Kelewele',
  'Ripe plantain cubes tossed in ginger, hot pepper, and cloves, then fried until the edges caramelise.',
  '3 ripe plantains; 1 tbsp grated ginger; 1 tsp ground hot pepper; 1/4 tsp ground cloves; 1/4 tsp nutmeg; 1/2 tsp salt; oil for frying',
  'Toss plantain with the spices and rest for 10 minutes. Fry in batches until deep gold. Serve at once.',
  'Ghanaian', 'Vegan', 'Easy',
  'images/kelewele.jpg',
  '2026-09-12 17:40:00'
),
(
  7, 1, 2,
  'Grilled tilapia',
  'Whole tilapia scored, rubbed with pepper and ginger, then grilled until the skin blisters. Serve with banku or kenkey and shito.',
  '2 whole tilapia; 1 tbsp grated ginger; 2 garlic cloves; 1 tsp ground hot pepper; 1 tsp salt; 2 tbsp oil; 1 onion; shito; banku or kenkey',
  'Rub the fish with ginger, garlic, pepper, salt, and oil. Grill 6 to 8 minutes a side until the flesh flakes. Serve with banku or kenkey and shito.',
  'Ghanaian', 'Pescatarian', 'Medium',
  'https://images.unsplash.com/photo-1665401015549-712c0dc5ef85?auto=format&fit=crop&w=1200&q=80',
  '2026-04-22 18:15:00'
),
(
  8, 2, 4,
  'Kontomire stew',
  'Cocoyam leaves cooked soft with palm oil, onion, and smoked fish or egg. Eat with boiled yam, plantain, or rice.',
  '500g kontomire; 3 tbsp red palm oil; 1 onion; 2 tbsp tomato paste; 2 tomatoes; 1 tsp ground ginger; 100g smoked fish or 2 eggs; 1 scotch bonnet, optional; salt; boiled yam or plantain',
  'Blanch and chop the leaves. Fry onion, paste, and tomatoes in palm oil. Add kontomire and cook until soft. Stir in fish or eggs and serve.',
  'Ghanaian', 'Omnivore', 'Medium',
  'images/kontomire-stew.jpg',
  '2026-03-08 15:00:00'
),
(
  9, 3, 6,
  'Shito',
  'Ghana hot black pepper sauce, cooked low until the oil is dark and fragrant. Keep a jar beside kenkey, rice, grilled fish, and eggs.',
  '120ml vegetable oil; 1 large onion; 4 tbsp tomato paste; 1 tbsp ground ginger; 2 tbsp dried shrimp powder; 2 tbsp ground hot pepper; 1 tsp aniseed, optional; 1 tsp salt',
  'Fry onion, then tomato paste, then the spices on low heat for 15 to 20 minutes until thick and dark. Cool, jar, and refrigerate.',
  'Ghanaian', 'Pescatarian', 'Easy',
  'images/shito.jpg',
  '2026-09-20 10:00:00'
);

INSERT INTO community_posts (id, user_id, title, content, tip_type, created_at) VALUES
(1, 2, 'Salt the red-red pot, not the soak', 'Salting the cooking water, once the black-eyed beans are simmering, seasons them through. Start modestly and taste near the end.', 'Technique', '2026-09-12 09:00:00'),
(2, 3, 'Finish jollof with the lid on and the heat off', 'After the stock is absorbed, turn the hob off and leave the lid on for 10 minutes. Fluff the rice after, not before.', 'Zero-waste', '2026-09-18 08:20:00'),
(3, 1, 'Jollof colour without a burnt base', 'Fry tomato paste in oil first, then keep the simmer gentle and stir the base often. Colour does not require a burnt pot.', 'Culture', '2026-08-30 19:40:00'),
(4, 3, 'Keep shito in a clean jar', 'Cool shito completely before you lid the jar. Keep it in the fridge and use a dry spoon every time.', 'Storage', '2026-09-05 11:15:00'),
(5, 2, 'When the groundnut tin is empty', 'Blend roasted peanuts with a little warm stock until smooth. Taste as you go; homemade paste can be oilier than the jar.', 'Substitution', '2026-09-22 16:05:00');

INSERT INTO contacts (id, name, email, subject, message, submitted_at) VALUES
(1, 'Helena Brooks', 'helena.brooks@example.com', 'Seat at the October fermentation lab', 'I can come on 4 October and I am happy to bring a clean jar. Please tell me what size.', '2026-09-21 10:12:00'),
(2, 'Samir Rahman', 'samir.rahman@example.com', 'Banku dough timing', 'My banku stayed sticky after 20 minutes of steaming. Should I knead longer, or leave it on the steam for another 10 minutes?', '2026-09-15 15:44:00'),
(3, 'June Adeyemi', 'june.adeyemi@example.com', 'School visit', 'Our year 10 food class is looking at energy use in home kitchens. Could someone talk to them in November?', '2026-09-08 09:05:00');
