

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `ZaverecnaOdbornaPrace_PhTT7`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` mediumtext DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Vypisuji data pro tabulku `category`
--

INSERT INTO `category` (`id`, `name`, `description`, `created_at`) VALUES
(1, '🏋️‍♂️ Zdravě & Fit', 'Zdravé a výživné recepty pro aktivní životní styl', '2025-05-05 16:51:54'),
(2, '🍕 Cheat Day', 'Sladká a tučná odměna – recepty pro dny bez výčitek', '2025-05-05 16:51:54'),
(3, '⚡ Rychlovky', 'Bleskové recepty do 30 minut', '2025-05-05 16:51:54'),
(5, '⭐ Speciality', 'Unikátní, sváteční a netradiční jídla', '2025-05-05 14:52:22');

-- --------------------------------------------------------

--
-- Struktura tabulky `comment`
--

CREATE TABLE `comment` (
  `id` int(10) UNSIGNED NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `author_id` int(10) UNSIGNED NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Vypisuji data pro tabulku `comment`
--

INSERT INTO `comment` (`id`, `recipe_id`, `author_id`, `content`, `created_at`) VALUES
(3, 9, 2, 'Super recept!', '2025-05-05 20:14:35'),
(5, 9, 1, 'Moc mi chutná!', '2025-05-06 09:26:40');

-- --------------------------------------------------------

--
-- Struktura tabulky `forum_comment`
--

CREATE TABLE `forum_comment` (
  `id` int(10) UNSIGNED NOT NULL,
  `post_id` int(10) UNSIGNED NOT NULL,
  `author_id` int(10) UNSIGNED NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Vypisuji data pro tabulku `forum_comment`
--

INSERT INTO `forum_comment` (`id`, `post_id`, `author_id`, `content`, `created_at`) VALUES
(1, 6, 2, 'Játrový knedlíček', '2025-05-05 20:40:51'),
(2, 7, 1, 'Těstoviny se sýrem!', '2025-05-06 09:27:08');

-- --------------------------------------------------------

--
-- Struktura tabulky `forum_post`
--

CREATE TABLE `forum_post` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `author_id` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Vypisuji data pro tabulku `forum_post`
--

INSERT INTO `forum_post` (`id`, `title`, `content`, `author_id`, `created_at`, `updated_at`) VALUES
(6, 'Jaký je váš oblíbený nedělní oběd?', 'Já miluju svíčkovou s domácím knedlíkem. A co vy?', 1, '2025-05-05 20:26:52', NULL),
(7, 'Tipy na rychlé večeře?', 'Hledám inspiraci na rychlé recepty do 20 minut. Máte nějaké osvědčené?', 2, '2025-05-05 20:26:52', '2025-05-05 18:33:34'),
(8, 'Vegetariánské recepty doporučení', 'Přecházím na bezmasou stravu. Co byste doporučili začátečníkovi?', 3, '2025-05-05 20:26:52', NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `recipe`
--

CREATE TABLE `recipe` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `author_id` int(10) UNSIGNED NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `duration` int(11) NOT NULL,
  `difficulty` enum('lehký','střední','těžký') NOT NULL,
  `ingredients` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`ingredients`)),
  `comments_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `rating_sum` int(11) DEFAULT 0,
  `rating_count` int(11) DEFAULT 0,
  `views_count` int(11) DEFAULT 0,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Vypisuji data pro tabulku `recipe`
--

INSERT INTO `recipe` (`id`, `title`, `content`, `image`, `author_id`, `category_id`, `duration`, `difficulty`, `ingredients`, `comments_enabled`, `rating_sum`, `rating_count`, `views_count`, `is_published`, `created_at`, `updated_at`) VALUES
(4, 'Avokádový toast s vejcem', 'Rychlá a výživná snídaně nebo svačina, která tě nakopne. Avokádo, citron a vejce vytvářejí dokonalou kombinaci.\n\n1. Vlož plátek žitného chleba do toustovače nebo na pánev a opeč ho dozlatova.  \n2. Mezitím rozpůl avokádo a vydlabej dužinu do misky. Rozmačkej ho vidličkou.  \n3. Přidej několik kapek citronové šťávy, osol, opepři a můžeš přidat i špetku chilli vloček.  \n4. Na pánvi připrav volské oko nebo sázené vejce – stačí pár minut, aby žloutek zůstal tekutý.  \n5. Namaž avokádovou směs na toast, navrch polož vejce a můžeš podávat.  \n6. Pro stylové servírování posyp ještě trochou bylinek nebo semínek.\n', '6818e3560b49b.webp', 1, 1, 10, 'lehký', '[\"1 pl\\u00e1tek \\u017eitn\\u00e9ho chleba\",\"\\u00bd zral\\u00e9ho avok\\u00e1da\",\"1 vejce\",\"Citronov\\u00e1 \\u0161\\u0165\\u00e1va\",\"S\\u016fl\",\"pep\\u0159\",\"Chilli vlo\\u010dky (voliteln\\u011b)\"]', 1, 7, 2, 4, 1, '2025-05-05 16:12:06', NULL),
(6, 'Rýžová miska s tuňákem', ' Expresní miska s rýží, zeleninou a kvalitním tuňákem. Perfektní oběd do krabičky.\n\n1. Pokud nemáš hotovou rýži, uvař si ji podle návodu. Ideální je jasmínová nebo basmati.  \n2. Okurku nakrájej na tenké plátky, mrkev nastrouhej na nudličky nebo plátky.  \n3. Do misky dej rýži jako základ, navrch naaranžuj zeleninu a tuňáka (po scezení).  \n4. Posyp sezamovými semínky a zakápni sójovou omáčkou.  \n5. Můžeš přidat i trochu jarní cibulky, chilli omáčky nebo avokáda.  \n6. Servíruj v hluboké misce s lžící nebo hůlkami.\n', '6818e40a4866a.jpeg', 1, 3, 15, 'lehký', '[\"1 miska va\\u0159en\\u00e9 r\\u00fd\\u017ee\",\"1 plechovka tu\\u0148\\u00e1ka ve vlastn\\u00ed \\u0161\\u0165\\u00e1v\\u011b\",\"\\u00bd okurky\",\"1 mrkev\",\"L\\u017ei\\u010dka sezamov\\u00fdch sem\\u00ednek\",\"S\\u00f3jov\\u00e1 om\\u00e1\\u010dka\"]', 1, 5, 1, 3, 1, '2025-05-05 16:15:06', NULL),
(7, 'Dýňová polévka s kokosovým mlékem', ' Hustá, voňavá a jemně sladká polévka, která zahřeje. Ideální pro podzimní večery.\n1. Dýni omyj, vydlabej semínka a nakrájej ji na menší kostky (slupku můžeš ponechat).  \n2. Na olivovém oleji osmaž na hrubo nakrájenou cibuli a nastrouhaný zázvor.  \n3. Přidej dýni, krátce orestuj a zalij vývarem tak, aby byla dýně ponořená.  \n4. Vař přibližně 20 minut, dokud dýně nezměkne.  \n5. Pomocí tyčového mixéru vše rozmixuj do hladkého krému.  \n6. Přilij kokosové mléko, dochuť solí, pepřem a případně chilli. Prohřej, ale už nevař.  \n7. Servíruj s pečivem a trochou dýňového oleje nebo semínek navrch.\n', '6818e4608a4ec.jpeg', 1, 5, 40, 'lehký', '[\"1 men\\u0161\\u00ed d\\u00fdn\\u011b Hokkaido\",\"1 cibule\",\"1 plechovka kokosov\\u00e9ho ml\\u00e9ka\",\"Z\\u00e1zvor (\\u010derstv\\u00fd)\",\"V\\u00fdvar nebo voda\",\"S\\u016fl\",\"pep\\u0159\",\"chilli\",\"\"]', 1, 0, 0, 0, 1, '2025-05-05 16:16:32', NULL),
(9, 'Krémové těstoviny se slaninou', '1. Dejte vařit osolenou vodu a uvař těstoviny dle návodu na obalu.  \n2. Mezitím nakrájej slaninu na kostičky a opeč ji na pánvi dokřupava.  \n3. Přidej prolisovaný česnek a nech ho krátce rozvonět (pozor, aby nezhořkl).  \n4. Do pánve přilij smetanu na vaření a vmíchej nastrouhaný parmazán. Promíchej, dokud se sýr nerozpustí.  \n5. Sceď těstoviny (trochu vody si nech stranou) a přidej je do omáčky. Pokud je omáčka příliš hustá, přidej trochu vody z těstovin.  \n6. Dochut solí a pepřem a můžeš servírovat. Nahoru můžeš přidat ještě extra sýr.\n\n\nTahle bomba zasytí i hladového vlka. Dokonalá kombinace smetany, sýra a křupavé slaniny na oblíbených těstovinách.', '6818e63c6213a.jpeg', 1, 2, 25, 'střední', '[\"200 g t\\u011bstovin\",\"100 g slaniny\",\"150 ml smetany na va\\u0159en\\u00ed\",\"50 g strouhan\\u00e9ho parmaz\\u00e1nu\",\"1 strou\\u017eek \\u010desneku\",\"Pep\\u0159\",\"s\\u016fl\"]', 1, 35, 9, 202, 1, '2025-05-05 16:24:28', NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `user`
--

CREATE TABLE `user` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Vypisuji data pro tabulku `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Administrator', 'admin@receptaria.cz', '$2y$12$uciWLtyuWcz7NWJxsoPIkeC18q/gEPRwQ8KzYgh1BvxB6OAwPqM7.', 'admin', '2025-04-10 21:12:18'),
(2, 'User', 'user@receptaria.cz', '$2y$12$2dKtqUEVGor4dKxrJ4x.6uzaStMTnz55QuFZgkQ55yRLNVU6kIT52', 'user', '2025-04-10 21:33:03'),
(3, 'User2', 'userr@receptaria.cz', '$2y$12$vQ3SLAhr5IgzFd74UEL59e4u1q2ufGvPA4fQf4YETQYlI.1wasT9y', 'user', '2025-04-23 13:40:57');

--
-- Indexy pro exportované tabulky
--

--
-- Indexy pro tabulku `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pro tabulku `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipe_id` (`recipe_id`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexy pro tabulku `forum_comment`
--
ALTER TABLE `forum_comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexy pro tabulku `forum_post`
--
ALTER TABLE `forum_post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexy pro tabulku `recipe`
--
ALTER TABLE `recipe`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_recipe_author` (`author_id`),
  ADD KEY `fk_recipe_category` (`category_id`);

--
-- Indexy pro tabulku `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pro tabulku `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pro tabulku `forum_comment`
--
ALTER TABLE `forum_comment`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pro tabulku `forum_post`
--
ALTER TABLE `forum_post`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pro tabulku `recipe`
--
ALTER TABLE `recipe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pro tabulku `user`
--
ALTER TABLE `user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipe` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Omezení pro tabulku `forum_comment`
--
ALTER TABLE `forum_comment`
  ADD CONSTRAINT `forum_comment_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `forum_post` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_comment_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Omezení pro tabulku `forum_post`
--
ALTER TABLE `forum_post`
  ADD CONSTRAINT `forum_post_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Omezení pro tabulku `recipe`
--
ALTER TABLE `recipe`
  ADD CONSTRAINT `fk_recipe_author` FOREIGN KEY (`author_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_recipe_category` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
