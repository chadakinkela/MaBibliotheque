-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 17 nov. 2025 à 03:36
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `bibliotheque_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `lecteurs`
--

CREATE TABLE `lecteurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `lecteurs`
--

INSERT INTO `lecteurs` (`id`, `nom`, `prenom`, `email`) VALUES
(2, 'etiwa', 'Imaculee', 'tribunaletiwa@gmail.com'),
(3, 'Kinkela', 'Jonathan', 'kinkelajonathan1@gmail.com'),
(4, 'Kinkela', 'Chada', 'chadakinkela@gmail.com');

-- --------------------------------------------------------

--
-- Structure de la table `liste_lecture`
--

CREATE TABLE `liste_lecture` (
  `id_livre` int(11) DEFAULT NULL,
  `id_lecteur` int(11) DEFAULT NULL,
  `date_emprunt` date DEFAULT NULL,
  `date_retour` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `liste_lecture`
--

INSERT INTO `liste_lecture` (`id_livre`, `id_lecteur`, `date_emprunt`, `date_retour`) VALUES
(5, 4, NULL, NULL),
(4, 4, NULL, NULL),
(3, 4, NULL, NULL),
(5, 3, NULL, NULL),
(4, 3, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `livres`
--

CREATE TABLE `livres` (
  `id` int(11) NOT NULL,
  `titre` varchar(100) NOT NULL,
  `auteur` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `maison_edition` varchar(100) NOT NULL,
  `nombre_exemplaire` int(11) DEFAULT NULL,
  `image` varchar(100) DEFAULT NULL,
  `categorie` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `livres`
--

INSERT INTO `livres` (`id`, `titre`, `auteur`, `description`, `maison_edition`, `nombre_exemplaire`, `image`, `categorie`) VALUES
(1, 'L`\'Enfant noir', 'Camara Laye', 'Un jeune écrivain décide de raconter son enfance africaine, en Haute-Guinée. Mais, au-delà d\'un récit autobiographique, il nous restitue, dans toute sa vérité, la vie quotidienne, les traditions et les coutumes de tout un peuple. Avec cet Enfant noir, Camara Laye nous offre un livre intemporel plein de finesse et de talent.', 'Pocket', 30, 'L_Enfant_Noir.png', 'Roman autobiographique'),
(2, 'Le Manuscrit retrouvé', 'Paulo Coelho', '14 juillet 1099. Alors que les croisés sont aux portes de la ville, les habitants de Jérusalem se pressent autour d’un homme mystérieux connu sous le nom du Copte pour entendre ses derniers enseignements. La foule, composée de chrétiens, de juifs et de musulmans qui vivaient jusqu’alors en parfaite harmonie, s’apprête à livrer combat et la défaite semble imminente. Mais loin de toute stratégie guerrière, c’est une véritable leçon de vie qui leur est dispensée. Le Manuscrit retrouvé est une invitation à repenser notre humanité qui pose une question d’une brûlante actualité : quelles valeurs subsistent lorsque tout a été détruit ?', 'Flammarion', 60, 'Manuscrit_retrouvé.png', 'Roman historique'),
(3, 'Vingt mille lieues sous les mers', 'Jules Verne', 'Un monstre mystérieux sème la terreur en mer. Lancé à sa poursuite, le professeur Aronnax va vivre une aventure exaltante et dangereuse, qui l’amènera à découvrir les merveilles cachées au fond des océans, mais aussi à frôler la mort…\r\nÉpopée moderne où la technologie rapproche de la nature, ce voyage extraordinaire sonde les ténèbres des abysses et les profondeurs, non moins énigmatiques, du cœur de l’homme.', 'Flammarion', 25, 'Vingt_mille_lieues_sous_les_mers.png', 'Science fiction'),
(4, 'Quand Dieu était femme', 'Merlin Stone', 'À’aube des religions, Dieu était une femme, Créatrice de la Vie, Reine du Ciel. Elle a été adorée par plusieurs peuples depuis le début de la période néolithique jusqu’à la fermeture de ses derniers temples, environ 500 ans après J.C. Son culte s’est éteint, mais pas de lui-même: il a été victime de siècles de répression de la part des adeptes des nouvelles religions chrétiennes, judaïques et islamiques qui imposèrent la suprématie des divinités mâles. C’est de ces nouvelles religions que nous viennent le mythe de la création d’Adam et Ève et de la fable du Paradis perdu.\r\nLe pouvoir des mythes est tel qu’ils orientent notre perception du monde, conditionnent notre pensée et même notre sensibilité. Quelles pouvaient être alors les légendes issues d’une religion où l’on vénérait des divinités féminines pour leur courage, leur force et leur sens de la justice? Quelle pouvait être la vie des femmes et des hommes dans des sociétés où régnaient de telles idées? Et, pourquoi les adeptes des nouvelles religions se sont-ils battus si férocement pour effacer jusqu’au souvenir de ce premier culte, et pour imposer l’image de la femme éternelle servante ou séductrice?\r\nLes réponses à ces questions et à bien d’autres forment le contenu de cet ouvrage étonnant.', 'L\'Etincelle ', 15, 'Quand_Dieu_était_Femme.png', 'Religion'),
(5, 'Kocoumbo l\'etudiat noir', 'Aké Loba', 'Pas facile de quitter l\'Afrique quand on a tout pour être heureux : le soleil, les chants, les danses, les parents et l\'ébauche d\'un premier amour.\r\nPourtant, un beau jour Kocoumbo se décide. C\'est dit : il ira étudier : Et pas n\'importe où : à Paris... Ah ! Paris ! On en dit des choses sur la \"vile lumière\", sur les Blancs - très grands - qui marchent avec d\'horribles instruments qu\'on appelle souliers.\r\nMais, plus angoissant, comment va-t-on l\'accueillir, lui, l\'africain à la peau brune, dans ce pays étrange où des machines transportent les lettres d\'un quartier à l\'autre en quelques secondes ?\r\nQuel monde va-t-il découvrir par-delà les mers ?\r\nJungle ou paradis ?', 'Flammation', 18, 'Kocoumbo.png', 'Roman'),
(6, 'Celles qui attendent', 'Fatou Diome', 'Arame et Bougna, mères de Lamine et Issa, clandestins partis pour l’Europe, ne comptaient plus leurs printemps ; chacune était la sentinelle vouée et dévouée à la sauvegarde des siens, le pilier qui tenait la demeure sur les galeries creusées par l’absence.\r\nCoumba et Daba, jeunes épouses des deux émigrés, humaient leurs premières roses : assoiffées d’amour, d’avenir et de modernité, elles s’étaient lancées, sans réserve, sur une piste du bonheur devenue peu à peu leur chemin de croix.\r\nLa vie n’attend pas les absents : les amours varient, les secrets de famille affleurent, les petites et grandes trahisons alimentent la chronique sociale et déterminent la nature des retrouvailles. Le visage qu’on retrouve n’est pas forcément celui qu’on attendait…', 'Flammation', 12, 'Celles_qui_attendent.png', 'Roman'),
(7, 'Apprentissage artificiel', 'Antoint Cornuéjols, Yves Kodratoff, Laurent Miclet', 'Les programmes d\'intelligence artificielle sont aujourd\'hui capables de reconnaître des commandes vocales, d\'analyser automatiquement des photos satellites, d\'assister des experts pour prendre des décisions dans des environnements complexes et évolutifs (analyse de marchés financiers, diagnostics médicaux...), de fouiller d\'immenses bases de données hétérogènes, telles les innombrables pages du Web... Pour réaliser ces tâches, ils sont dotés de modules d\'apprentissage leur permettant d\'adapter leur comportement à des situations jamais rencontrées, ou d\'extraire des lois à partir de bases de données d\'exemples. Ce livre présente les concepts qui sous-tendent l\'apprentissage artificiel, les algorithmes qui en découlent et certaines de leurs applications. Son objectif est de décrire un ensemble d\'algorithmes utiles en tentant d\'établir un cadre théorique unique pour l\'ensemble des techniques regroupées sous ce terme \" d\'apprentissage artificiel \". A qui s\'adresse ce livre ? * Aux décideurs et aux ingénieurs qui souhaitent comprendre l\'apprentissage automatique et en acquérir des connaissances solides ; * Aux étudiants de niveau maîtrise, DEA ou école d\'ingénieurs qui souhaitent un ouvrage de référence en intelligence artificielle et en reconnaissance des formes.', 'Eyrolles', 11, 'Apprentissage_artificiel.png', 'Science informatique'),
(8, 'Programmer avec MySQL', 'Christian Soutou', 'Particulièrement destiné aux débutants, cet ouvrage permet de découvrir toutes les aspects de la programmation SQL par le biais du système de gestion de bases de données MySQL. Tous les concepts du langage procédural de MySQL sont décrits avec précision : variables, structure de contrôle, interactions avec la base, sous-programmes, curseurs, transactions, gestion des exceptions, déclencheurs, SQL dynamique. Couleur explique en outre comment exploiter une base MySQL (connexion et transactions) en programmant avec Java (JDBC) ou PHP 5. Chaque notion importante du livre est introduite à l\'aide d\'exemples simples et chaque chapitre se clôt par une série d\'exercices (avec corrigés disponibles en ligne) qui permettront au lecteur de tester ses connaissances. La seconde édition de cet ouvrage traite de la programmation avec la version de production 5.5 de MySQL : gestion du XML, signaux, événements. L\'optimisation des requêtes est également détaillée, notamment le fonctionnement de l\'optimiseur, l\'utilisation des statistiques et les plans d\'exécution. Enfin, différents techniques d\'optimisation sont présentées, telles que l\'indexation, les tables temporaires, le partitionnement et la dénormalisation.', 'Eyrolles', 5, 'Programmer_en_My_SQL.png', 'Science informatique'),
(9, 'Complexités : Aux limites des mathématiques et de l\'informatique', 'Jean Paul Delahaye', 'On doit aux mathématiques et à l\'informatique la maîtrise des complexités rencontrées dans toutes les sciences, car elles fabriquent les outils pour les penser et en créer de nouvelles, à notre service : les ordinateurs sont les objets artificiels les plus complexes jamais créés par l\'homme. Cependant mathématiques et informatique flirtent avec les limites de l\'intelligence.\r\nQuelle est la mémoire totale de l\'humanité actuelle et comment évolue-t-elle ?', 'Belin', 9, 'Complexité.png', 'Sciences Mathématiques et Informatique'),
(10, 'Les émotions cachées des plantes', 'Didier Van Cauwelaert', 'Aussi incroyable que cela paraisse, les plantes sont capables d\'éprouver toute la gamme des émotions. Et comme on l\'a récemment démontré, elles savent aussi, par les moyens les plus extraordinaires comme les plus simples, transmettre ce qu\'elles ressentent. La nature ne cesse de nous parler, preuves à l\'appui. A nous d\'arrêter d\'être sourds.\r\nElles se défendent, elles attaquent, elles nouent des alliances, elles chassent, elles rusent, elles draguent, elles communiquent à distance leurs peurs, leurs souffrances et leur joie. C\'est aujourd\'hui prouvé : elles nous perçoivent, nous reconnaissent, nous calculent, elles réagissent à nos émotions comme elles expriment les leurs. Est-il possible qu\'elles nous envoient des informations thérapeutiques, des messages de gratitude, des appels au secours ?\r\nOui, les plantes sont dotées d\'intelligence, de sensibilité, voire d\'une forme de télépathie qu\'ont détectée nos instruments de mesure. Aussi merveilleuses soient-elles, toutes les révélations contenues dans ce livre sont le fruit d\'observations et d\'expériences scientifiques.\r\nLa nature ne cesse de nous parler. A nous d\'arrêter d\'être sourds.', 'J\'ai lu', 13, 'Les_émotions_cachées_des_plantes.png', 'Science botanique'),
(11, 'Relations efficaces', 'Thomas Gordon', 'Lorsque vous entendez le mot relation, à quoi pensez-vous ? Pour la majorité d\'entre nous, ce mot évoque la relation étroite, intime, que nous entretenons avec un partenaire ou un conjoint. Nous pensons alors aux relations les plus importantes de notre vie.\r\nMais réfléchissez un peu à ceci : beaucoup de personnes autres que nos proches sont en mesure d\'exercer une influence considérable, tant positive que négative, sur notre existence.', 'Marabout', 12, 'Relations_efficaces.png', 'Psychologie'),
(12, 'De quoi aimer vivre', 'Fatou Diome', 'Pour qui ne craint pas la noyade, la lune n\'est jamais loin. Elle se reflète dans toutes les eaux, flotte entre toutes les paupières. N\'est-ce pas son éclat qui fait briller les yeux des amants et leur donne le pouvoir ensorceleur ? \"\r\nA partir de simples instants de vie, Fatou Diome scrute les comportements et sonde les cœurs d\'une galerie de personnages rêvés ou croisés : qu\'ils aient le cœur en berne ou comblé, tous savent, au fond, que l\'amour est la grande affaire de nos vies.\r\nVingt ans après \"La Préférence nationale\", Fatou Diome renoue avec la nouvelle, genre dans lequel elle excelle, et nous démontre, avec brio et malice, que \"chercher le bonheur c\'est oser le vertige', 'Albin Michel', 9, 'De_quoi_aimer_vivre.png', 'Roman'),
(13, 'Onze minutes', 'Paulo Coello', 'Toute jeune Brésilienne du Nordeste, Maria n’aspire qu’à l’Aventure, au grand Amour. Au cours d\'une semaine de vacances à Rio de Janeiro, sur la plage de Copacabana, un Suisse lui propose de devenir danseuse de cabaret à Genève. Elle voit là le début d’un conte de fées... Mais la réalité sera bien différente.\r\nMaria en vient à se prostituer - sans honte, puisqu’elle apprend à son âme à ne pas se plaindre de ce que fait son corps, et qu’elle s’interdit de tomber amoureuse. Après tout, la prostitution est un métier comme un autre, avec ses règles, ses horaires et ses jours de repos… Mais le sexe - comme l\'amour reste pour elle une énigme.\r\nPour découvrir le sens sacré de la sexualité, Maria devra trouver le chemin de la réconciliation avec elle-même.', 'Poche', 19, 'Onze_minutes.png', 'Roman'),
(14, 'Une terre promise', 'Barack Obama', 'Dans le premier volume de ses mémoires présidentiels, Barack Obama raconte l\'histoire passionnante de son improbable odyssée, celle d\'un jeune homme en quête d\'identité devenu dirigeant du monde libre, retraçant de manière personnelle son éducation politique et les moments emblématiques du premier mandat de sa présidence - une période de transformations et de bouleversements profonds.\r\nBarack Obama nous invite à le suivre dans un incroyable voyage, de ses premiers pas sur la scène politique à sa victoire décisive aux primaires de l\'Iowa, et jusqu\'à la soirée historique du 4 novembre 2008, lorsqu\'il fut élu 44e président des États-Unis, devenant ainsi le premier Afro-Américain à accéder à la fonction suprême.', 'Poche', 7, 'Une_terre_promise.png', 'Memoire');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `lecteurs`
--
ALTER TABLE `lecteurs`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `liste_lecture`
--
ALTER TABLE `liste_lecture`
  ADD UNIQUE KEY `unique_livre_lecteur` (`id_livre`,`id_lecteur`),
  ADD KEY `id_lecteur` (`id_lecteur`);

--
-- Index pour la table `livres`
--
ALTER TABLE `livres`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `lecteurs`
--
ALTER TABLE `lecteurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `livres`
--
ALTER TABLE `livres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `liste_lecture`
--
ALTER TABLE `liste_lecture`
  ADD CONSTRAINT `liste_lecture_ibfk_1` FOREIGN KEY (`id_livre`) REFERENCES `livres` (`id`),
  ADD CONSTRAINT `liste_lecture_ibfk_2` FOREIGN KEY (`id_lecteur`) REFERENCES `lecteurs` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
