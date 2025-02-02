-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Feb 02, 2025 alle 22:56
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tickets_donadoni`
--

DELIMITER $$
--
-- Procedure
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `creaPostiNumerati` ()   BEGIN
    DECLARE v_idSettore INT;
    DECLARE v_postiTotali INT;
    DECLARE v_numeroPosto INT;
    DECLARE v_prefixo VARCHAR(10);
    DECLARE v_numerato BOOLEAN;
    DECLARE done INT DEFAULT 0;  -- Aggiungi questa dichiarazione

    -- Cursore per selezionare i settori numerati
    DECLARE cur CURSOR FOR
        SELECT idSettore, postiTotali, numerato, nomeSettore
        FROM tSettore
        WHERE numerato = TRUE;

    -- Handler per gestire la fine del cursore
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    OPEN cur;

    -- Ciclo attraverso i settori numerati
    read_loop: LOOP
        FETCH cur INTO v_idSettore, v_postiTotali, v_numerato, v_prefixo;
        IF done THEN
            LEAVE read_loop;
        END IF;

        SET v_numeroPosto = 1;
        
        -- Creazione dei posti numerati
        WHILE v_numeroPosto <= v_postiTotali DO
            INSERT INTO tPosto (idSettore, numeroPosto, disponibile)
            VALUES (v_idSettore, CONCAT(v_prefixo, v_numeroPosto), TRUE);
            SET v_numeroPosto = v_numeroPosto + 1;
        END WHILE;
    END LOOP;

    CLOSE cur;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Struttura della tabella `tcarrello`
--

CREATE TABLE `tcarrello` (
  `idCarrello` int(11) NOT NULL,
  `idPrenotazione` int(11) NOT NULL,
  `idUtente` int(11) NOT NULL,
  `pagata` tinyint(1) NOT NULL,
  `dataAggiunta` datetime NOT NULL,
  `idEvento` int(11) NOT NULL,
  `disponibile` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `tcarrello`
--

INSERT INTO `tcarrello` (`idCarrello`, `idPrenotazione`, `idUtente`, `pagata`, `dataAggiunta`, `idEvento`, `disponibile`) VALUES
(1, 1, 1, 1, '2025-01-30 14:57:00', 4, 0),
(2, 2, 1, 0, '2025-01-29 14:58:00', 4, 0),
(3, 3, 1, 0, '2025-01-29 15:29:00', 4, 0),
(4, 4, 1, 0, '2025-01-29 15:29:20', 4, 0),
(5, 5, 1, 0, '2025-01-30 15:49:18', 4, 0),
(6, 6, 1, 0, '2025-01-30 15:50:45', 4, 0),
(7, 7, 1, 0, '2025-01-30 15:50:57', 4, 0),
(8, 8, 1, 0, '2025-01-30 15:51:02', 4, 0),
(9, 9, 1, 0, '2025-01-30 15:51:32', 1, 0),
(10, 10, 1, 0, '2025-01-30 15:52:59', 4, 0),
(11, 11, 1, 0, '2025-01-30 15:52:59', 4, 0),
(12, 12, 1, 0, '2025-01-30 15:53:03', 4, 0),
(13, 13, 1, 0, '2025-01-30 15:53:03', 4, 0),
(14, 14, 5, 1, '2025-01-31 09:57:39', 4, 0),
(15, 15, 6, 0, '2025-01-31 13:29:11', 4, 0),
(16, 16, 1, 0, '2025-02-01 11:51:30', 1, 0),
(17, 17, 1, 0, '2025-02-01 12:52:39', 3, 0),
(18, 18, 1, 0, '2025-02-01 12:52:46', 6, 0),
(19, 19, 1, 0, '2025-02-01 12:52:46', 6, 0),
(20, 20, 1, 1, '2025-02-02 16:15:24', 1, 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `tevento`
--

CREATE TABLE `tevento` (
  `idEvento` int(11) NOT NULL,
  `nomeEvento` varchar(100) NOT NULL,
  `categoria` varchar(50) NOT NULL,
  `pathFotoLocandina` varchar(255) DEFAULT NULL,
  `dataOraEvento` datetime NOT NULL,
  `idLuogo` int(11) NOT NULL,
  `biofrafia` varchar(750) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `tevento`
--

INSERT INTO `tevento` (`idEvento`, `nomeEvento`, `categoria`, `pathFotoLocandina`, `dataOraEvento`, `idLuogo`, `biofrafia`) VALUES
(1, 'Travis Scott - ROCK IN ROME', 'concerto', 'images/travisconcert.png', '2025-07-22 21:00:00', 14, NULL),
(2, 'Concerto di Sfera Ebbasta', 'concerto', 'images/concerto-sfera.png', '2025-02-01 21:00:00', 1, NULL),
(3, 'Concerto di Coldplay', 'concerto', NULL, '2025-02-10 21:00:00', 2, NULL),
(4, 'Real Madrid vs Barcelona', 'partite', 'images/Real_Madrid_vs_Barcelona.png', '2025-02-15 21:00:00', 3, 'Una delle rivalità più epiche del calcio mondiale: Real Madrid contro Barcelona. Un match che promette emozioni.'),
(5, 'Spettacolo teatrale Romeo e Giulietta', 'teatro', NULL, '2025-02-20 21:00:00', 4, NULL),
(6, 'Tour guidato al Colosseo', 'tour', NULL, '2025-02-22 10:00:00', 5, NULL),
(7, 'Concerto di Elton John', 'concerto', NULL, '2025-03-01 21:00:00', 6, NULL),
(8, 'Juventus vs Milan', 'partite', 'images/Juventus_vs_Milan.png', '2025-03-03 21:00:00', 7, 'Juventus e Milan si sfidano in un match avvincente. Un appuntamento imperdibile per gli appassionati di calcio.'),
(9, 'Teatro La Traviata', 'teatro', NULL, '2025-03-05 21:00:00', 8, NULL),
(10, 'Concerto di Beyoncé', 'concerto', NULL, '2025-03-10 21:00:00', 9, NULL),
(11, 'Tour del Museo del Louvre', 'tour', NULL, '2025-03-12 10:00:00', 10, NULL),
(12, 'Manchester United vs Chelsea', 'partite', 'images/Manchester_United_vs_Chelsea.png', '2025-03-15 21:00:00', 11, 'Manchester United contro Chelsea: un classico del calcio inglese, ricco di adrenalina e grande spettacolo.'),
(13, 'Concerto di Andrea Bocelli', 'concerto', NULL, '2025-03-18 21:00:00', 12, NULL),
(14, 'Spettacolo teatrale La Bella e la Bestia', 'teatro', NULL, '2025-03-20 21:00:00', 13, NULL),
(15, 'Tour guidato al Camp Nou', 'tour', NULL, '2025-03-25 10:00:00', 14, NULL),
(16, 'Concerto dei Rolling Stones', 'concerto', NULL, '2025-04-01 21:00:00', 15, NULL),
(17, 'PSG vs Bayern Monaco', 'partite', 'images/PSG_vs_Bayern_Monaco.png', '2025-04-05 21:00:00', 16, 'PSG contro Bayern Monaco: due colossi del calcio europeo che si affrontano in una sfida mozzafiato.'),
(18, 'Teatro I Miserabili', 'teatro', NULL, '2025-04-07 21:00:00', 17, NULL),
(19, 'Concerto di Adele', 'concerto', NULL, '2025-04-10 21:00:00', 18, NULL),
(20, 'Tour guidato alla Torre Eiffel', 'tour', NULL, '2025-04-12 10:00:00', 19, NULL),
(21, 'Liverpool vs Arsenal', 'partite', 'images/Liverpool_vs_Arsenal.png', '2025-04-15 21:00:00', 20, 'Liverpool e Arsenal si scontrano in una partita di alta intensità nella leggendaria Premier League inglese.'),
(22, 'Concerto di Taylor Swift', 'concerto', NULL, '2025-04-18 21:00:00', 21, NULL),
(23, 'Teatro Il Lago dei Cigni', 'teatro', NULL, '2025-04-20 21:00:00', 22, NULL),
(24, 'Tour guidato al Monumento di Cristo Redentore', 'tour', NULL, '2025-04-22 10:00:00', 23, NULL),
(25, 'Concerto di Ed Sheeran', 'concerto', NULL, '2025-05-01 21:00:00', 24, NULL),
(26, 'Bayern Monaco vs Borussia Dortmund', 'partite', 'images/Bayern_Monaco_vs_Borussia_Dortmund.png', '2025-05-03 21:00:00', 25, 'Bayern Monaco contro Borussia Dortmund: una delle sfide più intense della Bundesliga, con adrenalina assicurata.'),
(27, 'Teatro La Bohème', 'teatro', NULL, '2025-05-05 21:00:00', 26, NULL),
(28, 'Concerto di Bruce Springsteen', 'concerto', NULL, '2025-05-10 21:00:00', 27, NULL),
(29, 'Tour guidato al Stadio Olimpico di Roma', 'tour', NULL, '2025-05-12 10:00:00', 28, NULL),
(30, 'Roma vs Lazio', 'partite', 'images/Roma_vs_Lazio.png', '2025-05-15 21:00:00', 14, 'Roma Lazio, il derby piu\' importante al mondo con uno dei due giocatori piu\' forti del mondo (dybala) e il piu\' grande pagliaccio della storia (guendouzi)'),
(31, 'Concerto di Sting', 'concerto', NULL, '2025-05-18 21:00:00', 30, NULL),
(32, 'Spettacolo teatrale Il Gabbiano', 'teatro', NULL, '2025-05-20 21:00:00', 31, NULL),
(33, 'Tour guidato al Palazzo della Cultura di Mosca', 'tour', NULL, '2025-05-22 10:00:00', 32, NULL),
(34, 'Concerto dei U2', 'concerto', NULL, '2025-06-01 21:00:00', 33, NULL),
(35, 'Chelsea vs Manchester City', 'partite', 'images/Chelsea_vs_Manchester_City.png', '2025-06-05 21:00:00', 34, 'Chelsea e Manchester City si affrontano in una sfida di Premier League che promette spettacolo e intensità.'),
(36, 'Teatro Il Barbiere di Siviglia', 'teatro', NULL, '2025-06-07 21:00:00', 35, NULL),
(37, 'Concerto di Rammstein', 'concerto', NULL, '2025-06-10 21:00:00', 36, NULL),
(38, 'Tour guidato al Museo del Prado', 'tour', NULL, '2025-06-12 10:00:00', 37, NULL),
(39, 'Tottenham vs Everton', 'partite', 'images/Tottenham_vs_Everton.png', '2025-06-15 21:00:00', 38, 'Tottenham contro Everton: una partita che celebra la tradizione del calcio inglese con emozioni garantite.'),
(40, 'Concerto di Pink Floyd Tribute', 'concerto', NULL, '2025-06-18 21:00:00', 39, NULL),
(41, 'Spettacolo teatrale La Tempesta', 'teatro', NULL, '2025-06-20 21:00:00', 40, NULL),
(42, 'Tour guidato a Stonehenge', 'tour', NULL, '2025-06-22 10:00:00', 41, NULL),
(43, 'Concerto di Muse', 'concerto', NULL, '2025-07-01 21:00:00', 42, NULL),
(44, 'Barcelona vs Atletico Madrid', 'partite', 'images/Barcelona_vs_Atletico_Madrid.png', '2025-07-03 21:00:00', 43, 'Barcelona contro Atletico Madrid: una partita di Liga che promette azione, gol e grande intensità.'),
(45, 'Teatro La Rondine', 'teatro', NULL, '2025-07-05 21:00:00', 44, NULL),
(46, 'Concerto di Imagine Dragons', 'concerto', NULL, '2025-07-10 21:00:00', 45, NULL),
(47, 'Tour guidato al Parco Nazionale di Yellowstone', 'tour', NULL, '2025-07-12 10:00:00', 46, NULL),
(48, 'Borussia Dortmund vs Schalke 04', 'partite', 'images/Borussia_Dortmund_vs_Schalke_04.png', '2025-07-15 21:00:00', 47, 'Borussia Dortmund contro Schalke 04: il derby della Ruhr, una delle sfide più accese del calcio tedesco.'),
(49, 'Concerto di Lady Gaga', 'concerto', NULL, '2025-07-18 21:00:00', 48, NULL),
(50, 'Teatro Il Flauto Magico', 'teatro', NULL, '2025-07-20 21:00:00', 49, NULL),
(51, 'Tour guidato a Machu Picchu', 'tour', NULL, '2025-07-22 10:00:00', 50, NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `tluogo`
--

CREATE TABLE `tluogo` (
  `idLuogo` int(11) NOT NULL,
  `citta` varchar(50) NOT NULL,
  `stato` varchar(50) NOT NULL,
  `locazione` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `tluogo`
--

INSERT INTO `tluogo` (`idLuogo`, `citta`, `stato`, `locazione`) VALUES
(1, 'Roma', 'Italia', 'Colosseo'),
(2, 'Parigi', 'Francia', 'Tour Eiffel'),
(3, 'New York', 'USA', 'Madison Square Garden'),
(4, 'Londra', 'Regno Unito', 'O2 Arena'),
(5, 'Los Angeles', 'USA', 'Staples Center'),
(6, 'Milano', 'Italia', 'Teatro alla Scala'),
(7, 'Barcellona', 'Spagna', 'Camp Nou'),
(8, 'Berlino', 'Germania', 'Olympiastadion'),
(9, 'Tokyo', 'Giappone', 'Tokyo Dome'),
(10, 'Beijing', 'Cina', 'Bird’s Nest Stadium'),
(11, 'Atene', 'Grecia', 'Stadio Panathenaic'),
(12, 'Sydney', 'Australia', 'Sydney Opera House'),
(13, 'San Pietroburgo', 'Russia', 'Mariinsky Theatre'),
(14, 'Roma', 'Italia', 'Stadio Olimpico'),
(15, 'Vienna', 'Austria', 'Wiener Stadthalle'),
(16, 'Madrid', 'Spagna', 'Santiago Bernabéu Stadium'),
(17, 'Lisbona', 'Portogallo', 'Estádio da Luz'),
(18, 'Milan', 'Italia', 'San Siro'),
(19, 'Buenos Aires', 'Argentina', 'Teatro Colón'),
(20, 'Rio de Janeiro', 'Brasile', 'Maracanã'),
(21, 'Città del Messico', 'Messico', 'Auditorio Nacional'),
(22, 'Mosca', 'Russia', 'Luzhniki Stadium'),
(23, 'Shanghai', 'Cina', 'Mercedes-Benz Arena'),
(24, 'Las Vegas', 'USA', 'T-Mobile Arena'),
(25, 'San Francisco', 'USA', 'Levi’s Stadium'),
(26, 'Dubai', 'Emirati Arabi Uniti', 'Dubai Opera'),
(27, 'Chicago', 'USA', 'United Center'),
(28, 'Los Angeles', 'USA', 'Hollywood Bowl'),
(29, 'Hong Kong', 'Cina', 'Hong Kong Coliseum'),
(30, 'Cape Town', 'Sud Africa', 'Cape Town Stadium'),
(31, 'Praga', 'Repubblica Ceca', 'O2 Arena'),
(32, 'Budapest', 'Ungheria', 'Papp László Budapest Sportaréna'),
(33, 'Toronto', 'Canada', 'Scotiabank Arena'),
(34, 'Amsterdam', 'Paesi Bassi', 'Amsterdam Arena'),
(35, 'Dubai', 'Emirati Arabi Uniti', 'Al Wasl Plaza'),
(36, 'Mumbai', 'India', 'Wankhede Stadium'),
(37, 'Singapore', 'Singapore', 'Singapore Indoor Stadium'),
(38, 'Toronto', 'Canada', 'Rogers Centre'),
(39, 'Los Angeles', 'USA', 'Dodger Stadium'),
(40, 'Buenos Aires', 'Argentina', 'Estadio Monumental'),
(41, 'Londra', 'Regno Unito', 'Wembley Stadium'),
(42, 'Dubai', 'Emirati Arabi Uniti', 'Coca-Cola Arena'),
(43, 'Roma', 'Italia', 'Teatro dell’Opera'),
(44, 'Londra', 'Regno Unito', 'Royal Albert Hall'),
(45, 'Washington D.C.', 'USA', 'The Kennedy Center'),
(46, 'Parigi', 'Francia', 'Palais Garnier'),
(47, 'Las Vegas', 'USA', 'Caesars Palace'),
(48, 'Città del Messico', 'Messico', 'Estadio Azteca'),
(49, 'Berlin', 'Germania', 'Mercedes-Benz Arena'),
(50, 'New York', 'USA', 'Barclays Center'),
(51, 'Roma', 'Italia', 'Palazzo dei Congressi'),
(52, 'Madrid', 'Spagna', 'Palacio Vistalegre'),
(53, 'Los Angeles', 'USA', 'Hollywood Palladium'),
(54, 'San Pietroburgo', 'Russia', 'St. Petersburg Philharmonic Hall'),
(55, 'Berlino', 'Germania', 'Berliner Philharmonie'),
(56, 'Sydney', 'Australia', 'Sydney Entertainment Centre'),
(57, 'Copenhagen', 'Danimarca', 'Tivoli Concert Hall'),
(58, 'Barcelona', 'Spagna', 'Palau Sant Jordi'),
(59, 'Parigi', 'Francia', 'Le Zénith'),
(60, 'Stockholm', 'Svezia', 'Friends Arena'),
(61, 'Praga', 'Repubblica Ceca', 'Forum Karlin'),
(62, 'Milan', 'Italia', 'Fiera Milano Rho'),
(63, 'Manchester', 'Regno Unito', 'Manchester Arena'),
(64, 'Helsinki', 'Finlandia', 'Hartwall Arena'),
(65, 'Madrid', 'Spagna', 'WiZink Center'),
(66, 'Rome', 'Italia', 'PalaLottomatica'),
(67, 'Chicago', 'USA', 'Riviera Theatre'),
(68, 'Londra', 'Regno Unito', 'Royal Opera House'),
(69, 'Roma', 'Italia', 'Auditorium Parco della Musica'),
(70, 'Santiago', 'Cile', 'Movistar Arena'),
(71, 'Lisbona', 'Portogallo', 'Altice Arena'),
(72, 'Seul', 'Corea del Sud', 'Gocheok Sky Dome'),
(73, 'Paris', 'Francia', 'AccorHotels Arena'),
(74, 'Istanbul', 'Turchia', 'Ataturk Olympic Stadium'),
(75, 'San Paolo', 'Brasile', 'Arena Corinthians'),
(76, 'Buenos Aires', 'Argentina', 'Teatro Nacional Cervantes'),
(77, 'Moscova', 'Russia', 'Bolshoi Theatre'),
(78, 'Lisbona', 'Portogallo', 'Campo Pequeno'),
(79, 'Dubai', 'Emirati Arabi Uniti', 'Dubai World Trade Centre'),
(80, 'Madrid', 'Spagna', 'Plaza Mayor'),
(81, 'Santiago', 'Cile', 'Teatro Municipal de Santiago'),
(82, 'Londra', 'Regno Unito', 'Barbican Centre'),
(83, 'Hong Kong', 'Cina', 'Hong Kong Convention and Exhibition Centre'),
(84, 'Zurigo', 'Svizzera', 'Hallenstadion'),
(85, 'Roma', 'Italia', 'Palazzo del Viminale'),
(86, 'Singapore', 'Singapore', 'Singapore Sports Hub'),
(87, 'Parigi', 'Francia', 'Le Grand Palais'),
(88, 'Las Vegas', 'USA', 'MGM Grand Garden Arena'),
(89, 'San Francisco', 'USA', 'Shoreline Amphitheatre'),
(90, 'Los Angeles', 'USA', 'Banc of California Stadium'),
(91, 'Berlino', 'Germania', 'Max-Schmeling-Halle'),
(92, 'Tokio', 'Giappone', 'Saitama Super Arena'),
(93, 'Madrid', 'Spagna', 'Palacio de los Deportes'),
(94, 'São Paulo', 'Brasile', 'Allianz Parque'),
(95, 'Roma', 'Italia', 'Teatro Argentina');

-- --------------------------------------------------------

--
-- Struttura della tabella `tpagamento`
--

CREATE TABLE `tpagamento` (
  `idPagamento` int(11) NOT NULL,
  `idPrenotazione` int(11) DEFAULT NULL,
  `idCarrello` int(11) DEFAULT NULL,
  `importoFinale` float DEFAULT NULL,
  `dataOraPagamento` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `tpagamento`
--

INSERT INTO `tpagamento` (`idPagamento`, `idPrenotazione`, `idCarrello`, `importoFinale`, `dataOraPagamento`) VALUES
(1, 1, 1, 132, '2025-01-30 15:28:04'),
(2, 14, 14, 132, '2025-01-31 09:58:00'),
(3, 20, 20, 143, '2025-02-02 16:15:40');

-- --------------------------------------------------------

--
-- Struttura della tabella `tposto`
--

CREATE TABLE `tposto` (
  `idPosto` int(11) NOT NULL,
  `idSettore` int(11) NOT NULL,
  `numeroPosto` varchar(20) DEFAULT NULL,
  `disponibile` tinyint(1) NOT NULL DEFAULT 1,
  `idUtente` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `tposto`
--

INSERT INTO `tposto` (`idPosto`, `idSettore`, `numeroPosto`, `disponibile`, `idUtente`) VALUES
(1, 1, 'Tribuna Ce1', 0, 1),
(2, 1, 'Tribuna Ce2', 1, NULL),
(3, 1, 'Tribuna Ce3', 1, NULL),
(4, 1, 'Tribuna Ce4', 1, NULL),
(5, 1, 'Tribuna Ce5', 1, NULL),
(6, 1, 'Tribuna Ce6', 1, NULL),
(7, 1, 'Tribuna Ce7', 1, NULL),
(8, 1, 'Tribuna Ce8', 1, NULL),
(9, 1, 'Tribuna Ce9', 1, NULL),
(10, 1, 'Tribuna Ce10', 1, NULL),
(11, 1, 'Tribuna Ce11', 1, NULL),
(12, 1, 'Tribuna Ce12', 1, NULL),
(13, 1, 'Tribuna Ce13', 1, NULL),
(14, 1, 'Tribuna Ce14', 1, NULL),
(15, 1, 'Tribuna Ce15', 1, NULL),
(16, 1, 'Tribuna Ce16', 1, NULL),
(17, 1, 'Tribuna Ce17', 1, NULL),
(18, 1, 'Tribuna Ce18', 1, NULL),
(19, 1, 'Tribuna Ce19', 1, NULL),
(20, 1, 'Tribuna Ce20', 1, NULL),
(21, 1, 'Tribuna Ce21', 1, NULL),
(22, 1, 'Tribuna Ce22', 1, NULL),
(23, 1, 'Tribuna Ce23', 1, NULL),
(24, 1, 'Tribuna Ce24', 1, NULL),
(25, 1, 'Tribuna Ce25', 1, NULL),
(26, 1, 'Tribuna Ce26', 1, NULL),
(27, 1, 'Tribuna Ce27', 1, NULL),
(28, 1, 'Tribuna Ce28', 1, NULL),
(29, 1, 'Tribuna Ce29', 1, NULL),
(30, 1, 'Tribuna Ce30', 1, NULL),
(31, 1, 'Tribuna Ce31', 1, NULL),
(32, 1, 'Tribuna Ce32', 1, NULL),
(33, 1, 'Tribuna Ce33', 1, NULL),
(34, 1, 'Tribuna Ce34', 1, NULL),
(35, 1, 'Tribuna Ce35', 1, NULL),
(36, 1, 'Tribuna Ce36', 1, NULL),
(37, 1, 'Tribuna Ce37', 1, NULL),
(38, 1, 'Tribuna Ce38', 1, NULL),
(39, 1, 'Tribuna Ce39', 1, NULL),
(40, 1, 'Tribuna Ce40', 1, NULL),
(41, 1, 'Tribuna Ce41', 1, NULL),
(42, 1, 'Tribuna Ce42', 1, NULL),
(43, 1, 'Tribuna Ce43', 1, NULL),
(44, 1, 'Tribuna Ce44', 1, NULL),
(45, 1, 'Tribuna Ce45', 1, NULL),
(46, 1, 'Tribuna Ce46', 1, NULL),
(47, 1, 'Tribuna Ce47', 1, NULL),
(48, 1, 'Tribuna Ce48', 1, NULL),
(49, 1, 'Tribuna Ce49', 1, NULL),
(50, 1, 'Tribuna Ce50', 1, NULL),
(51, 2, 'Tribuna La1', 1, NULL),
(52, 2, 'Tribuna La2', 1, NULL),
(53, 2, 'Tribuna La3', 1, NULL),
(54, 2, 'Tribuna La4', 1, NULL),
(55, 2, 'Tribuna La5', 1, NULL),
(56, 2, 'Tribuna La6', 1, NULL),
(57, 2, 'Tribuna La7', 1, NULL),
(58, 2, 'Tribuna La8', 1, NULL),
(59, 2, 'Tribuna La9', 1, NULL),
(60, 2, 'Tribuna La10', 1, NULL),
(61, 3, 'Curve1', 1, NULL),
(62, 3, 'Curve2', 1, NULL),
(63, 3, 'Curve3', 1, NULL),
(64, 3, 'Curve4', 1, NULL),
(65, 3, 'Curve5', 1, NULL),
(66, 3, 'Curve6', 1, NULL),
(67, 3, 'Curve7', 1, NULL),
(68, 3, 'Curve8', 1, NULL),
(69, 3, 'Curve9', 1, NULL),
(70, 3, 'Curve10', 1, NULL),
(71, 3, 'Curve11', 1, NULL),
(72, 3, 'Curve12', 1, NULL),
(73, 3, 'Curve13', 1, NULL),
(74, 3, 'Curve14', 1, NULL),
(75, 3, 'Curve15', 1, NULL),
(76, 3, 'Curve16', 1, NULL),
(77, 3, 'Curve17', 1, NULL),
(78, 3, 'Curve18', 1, NULL),
(79, 3, 'Curve19', 1, NULL),
(80, 3, 'Curve20', 1, NULL),
(81, 3, 'Curve21', 1, NULL),
(82, 3, 'Curve22', 1, NULL),
(83, 3, 'Curve23', 1, NULL),
(84, 3, 'Curve24', 1, NULL),
(85, 3, 'Curve25', 1, NULL),
(86, 3, 'Curve26', 1, NULL),
(87, 3, 'Curve27', 1, NULL),
(88, 3, 'Curve28', 1, NULL),
(89, 3, 'Curve29', 1, NULL),
(90, 3, 'Curve30', 1, NULL),
(91, 3, 'Curve31', 1, NULL),
(92, 3, 'Curve32', 1, NULL),
(93, 3, 'Curve33', 1, NULL),
(94, 3, 'Curve34', 1, NULL),
(95, 3, 'Curve35', 1, NULL),
(96, 3, 'Curve36', 1, NULL),
(97, 3, 'Curve37', 1, NULL),
(98, 3, 'Curve38', 1, NULL),
(99, 3, 'Curve39', 1, NULL),
(100, 3, 'Curve40', 1, NULL),
(101, 3, 'Curve41', 1, NULL),
(102, 3, 'Curve42', 1, NULL),
(103, 3, 'Curve43', 1, NULL),
(104, 3, 'Curve44', 1, NULL),
(105, 3, 'Curve45', 1, NULL),
(106, 3, 'Curve46', 1, NULL),
(107, 3, 'Curve47', 1, NULL),
(108, 3, 'Curve48', 1, NULL),
(109, 3, 'Curve49', 1, NULL),
(110, 3, 'Curve50', 1, NULL),
(111, 4, 'Settore Os1', 1, NULL),
(112, 4, 'Settore Os2', 1, NULL),
(113, 4, 'Settore Os3', 1, NULL),
(114, 4, 'Settore Os4', 1, NULL),
(115, 4, 'Settore Os5', 1, NULL),
(116, 4, 'Settore Os6', 1, NULL),
(117, 4, 'Settore Os7', 1, NULL),
(118, 4, 'Settore Os8', 1, NULL),
(119, 4, 'Settore Os9', 1, NULL),
(120, 4, 'Settore Os10', 1, NULL),
(121, 4, 'Settore Os11', 1, NULL),
(122, 4, 'Settore Os12', 1, NULL),
(123, 4, 'Settore Os13', 1, NULL),
(124, 4, 'Settore Os14', 1, NULL),
(125, 4, 'Settore Os15', 1, NULL),
(126, 4, 'Settore Os16', 1, NULL),
(127, 4, 'Settore Os17', 1, NULL),
(128, 4, 'Settore Os18', 1, NULL),
(129, 4, 'Settore Os19', 1, NULL),
(130, 4, 'Settore Os20', 1, NULL),
(131, 6, 'Tribuna Nu1', 1, NULL),
(132, 6, 'Tribuna Nu2', 1, NULL),
(133, 6, 'Tribuna Nu3', 1, NULL),
(134, 6, 'Tribuna Nu4', 1, NULL),
(135, 6, 'Tribuna Nu5', 1, NULL),
(136, 6, 'Tribuna Nu6', 1, NULL),
(137, 6, 'Tribuna Nu7', 1, NULL),
(138, 6, 'Tribuna Nu8', 1, NULL),
(139, 6, 'Tribuna Nu9', 1, NULL),
(140, 6, 'Tribuna Nu10', 1, NULL),
(141, 6, 'Tribuna Nu11', 1, NULL),
(142, 6, 'Tribuna Nu12', 1, NULL),
(143, 6, 'Tribuna Nu13', 1, NULL),
(144, 6, 'Tribuna Nu14', 1, NULL),
(145, 6, 'Tribuna Nu15', 1, NULL),
(146, 6, 'Tribuna Nu16', 1, NULL),
(147, 6, 'Tribuna Nu17', 1, NULL),
(148, 6, 'Tribuna Nu18', 1, NULL),
(149, 6, 'Tribuna Nu19', 1, NULL),
(150, 6, 'Tribuna Nu20', 1, NULL),
(151, 6, 'Tribuna Nu21', 1, NULL),
(152, 6, 'Tribuna Nu22', 1, NULL),
(153, 6, 'Tribuna Nu23', 1, NULL),
(154, 6, 'Tribuna Nu24', 1, NULL),
(155, 6, 'Tribuna Nu25', 1, NULL),
(156, 6, 'Tribuna Nu26', 1, NULL),
(157, 6, 'Tribuna Nu27', 1, NULL),
(158, 6, 'Tribuna Nu28', 1, NULL),
(159, 6, 'Tribuna Nu29', 1, NULL),
(160, 6, 'Tribuna Nu30', 1, NULL),
(161, 6, 'Tribuna Nu31', 1, NULL),
(162, 6, 'Tribuna Nu32', 1, NULL),
(163, 6, 'Tribuna Nu33', 1, NULL),
(164, 6, 'Tribuna Nu34', 1, NULL),
(165, 6, 'Tribuna Nu35', 1, NULL),
(166, 6, 'Tribuna Nu36', 1, NULL),
(167, 6, 'Tribuna Nu37', 1, NULL),
(168, 6, 'Tribuna Nu38', 1, NULL),
(169, 6, 'Tribuna Nu39', 1, NULL),
(170, 6, 'Tribuna Nu40', 1, NULL),
(171, 6, 'Tribuna Nu41', 1, NULL),
(172, 6, 'Tribuna Nu42', 1, NULL),
(173, 6, 'Tribuna Nu43', 1, NULL),
(174, 6, 'Tribuna Nu44', 1, NULL),
(175, 6, 'Tribuna Nu45', 1, NULL),
(176, 6, 'Tribuna Nu46', 1, NULL),
(177, 6, 'Tribuna Nu47', 1, NULL),
(178, 6, 'Tribuna Nu48', 1, NULL),
(179, 6, 'Tribuna Nu49', 1, NULL),
(180, 6, 'Tribuna Nu50', 1, NULL),
(181, 8, 'Tribuna Nu1', 0, 1),
(182, 8, 'Tribuna Nu2', 1, NULL),
(183, 8, 'Tribuna Nu3', 1, NULL),
(184, 8, 'Tribuna Nu4', 1, NULL),
(185, 8, 'Tribuna Nu5', 1, NULL),
(186, 8, 'Tribuna Nu6', 1, NULL),
(187, 8, 'Tribuna Nu7', 1, NULL),
(188, 8, 'Tribuna Nu8', 1, NULL),
(189, 8, 'Tribuna Nu9', 1, NULL),
(190, 8, 'Tribuna Nu10', 1, NULL),
(191, 8, 'Tribuna Nu11', 1, NULL),
(192, 8, 'Tribuna Nu12', 1, NULL),
(193, 8, 'Tribuna Nu13', 1, NULL),
(194, 8, 'Tribuna Nu14', 1, NULL),
(195, 8, 'Tribuna Nu15', 1, NULL),
(196, 8, 'Tribuna Nu16', 1, NULL),
(197, 8, 'Tribuna Nu17', 1, NULL),
(198, 8, 'Tribuna Nu18', 1, NULL),
(199, 8, 'Tribuna Nu19', 1, NULL),
(200, 8, 'Tribuna Nu20', 1, NULL),
(201, 8, 'Tribuna Nu21', 1, NULL),
(202, 8, 'Tribuna Nu22', 1, NULL),
(203, 8, 'Tribuna Nu23', 1, NULL),
(204, 8, 'Tribuna Nu24', 1, NULL),
(205, 8, 'Tribuna Nu25', 1, NULL),
(206, 8, 'Tribuna Nu26', 1, NULL),
(207, 8, 'Tribuna Nu27', 1, NULL),
(208, 8, 'Tribuna Nu28', 1, NULL),
(209, 8, 'Tribuna Nu29', 1, NULL),
(210, 8, 'Tribuna Nu30', 1, NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `tprenotazione`
--

CREATE TABLE `tprenotazione` (
  `idPrenotazione` int(11) NOT NULL,
  `idSettore` int(11) NOT NULL,
  `idUtente` int(11) NOT NULL,
  `prezzo` float NOT NULL,
  `idPosto` int(11) DEFAULT NULL,
  `statoPrenotazione` enum('in elaborazione','confermata','cancellata') DEFAULT NULL,
  `idEvento` int(11) DEFAULT NULL,
  `dataPrenotazione` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `tprenotazione`
--

INSERT INTO `tprenotazione` (`idPrenotazione`, `idSettore`, `idUtente`, `prezzo`, `idPosto`, `statoPrenotazione`, `idEvento`, `dataPrenotazione`) VALUES
(1, 1, 1, 120, 1, 'confermata', 4, '2025-01-30 14:57:00'),
(2, 1, 1, 120, 2, 'cancellata', 4, '2025-01-30 14:57:00'),
(3, 3, 1, 70, 61, 'cancellata', 4, '2025-01-30 14:59:35'),
(4, 4, 1, 60, 111, 'cancellata', 4, '2025-01-30 15:28:20'),
(5, 1, 1, 120, 2, 'cancellata', 4, '2025-01-30 15:49:18'),
(6, 1, 1, 120, 2, 'cancellata', 4, '2025-01-30 15:50:45'),
(7, 4, 1, 60, 111, 'cancellata', 4, '2025-01-30 15:50:57'),
(8, 1, 1, 120, 3, 'cancellata', 4, '2025-01-30 15:51:02'),
(9, 8, 1, 130, 181, 'cancellata', 1, '2025-01-30 15:51:32'),
(10, 1, 1, 120, 2, 'cancellata', 4, '2025-01-30 15:52:59'),
(11, 1, 1, 120, 4, 'cancellata', 4, '2025-01-30 15:52:59'),
(12, 1, 1, 120, 5, 'cancellata', 4, '2025-01-30 15:53:03'),
(13, 1, 1, 120, 6, 'cancellata', 4, '2025-01-30 15:53:03'),
(14, 1, 5, 120, 2, 'confermata', 4, '2025-01-31 09:57:39'),
(15, 4, 6, 60, 111, 'cancellata', 4, '2025-01-31 13:29:11'),
(16, 7, 1, 95, NULL, 'cancellata', 1, '2025-02-01 11:51:30'),
(17, 10, 1, 100, NULL, 'cancellata', 3, '2025-02-01 12:52:39'),
(18, 9, 1, 25, NULL, 'cancellata', 6, '2025-02-01 12:52:46'),
(19, 9, 1, 25, NULL, 'cancellata', 6, '2025-02-01 12:52:46'),
(20, 8, 1, 130, 181, 'confermata', 1, '2025-02-02 16:15:24');

-- --------------------------------------------------------

--
-- Struttura della tabella `tsettore`
--

CREATE TABLE `tsettore` (
  `idSettore` int(11) NOT NULL,
  `idEvento` int(11) NOT NULL,
  `nomeSettore` varchar(50) NOT NULL,
  `postiTotali` int(11) NOT NULL,
  `numerato` tinyint(1) NOT NULL,
  `prezzo` decimal(10,2) NOT NULL,
  `postiDisponibili` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `tsettore`
--

INSERT INTO `tsettore` (`idSettore`, `idEvento`, `nomeSettore`, `postiTotali`, `numerato`, `prezzo`, `postiDisponibili`) VALUES
(1, 4, 'Tribuna Centrale', 84, 1, 120.00, 50),
(2, 4, 'Tribuna Laterale', 10, 1, 90.00, 10),
(3, 4, 'Curve', 84, 1, 70.00, 50),
(4, 4, 'Settore Ospiti', 46, 1, 60.00, 20),
(5, 2, 'Prato', 20, 0, 80.00, 20),
(6, 2, 'Tribuna Numerata', 50, 1, 150.00, 50),
(7, 1, 'Prato', 10, 0, 95.00, 10),
(8, 1, 'Tribuna Numerata', 21, 1, 130.00, 22),
(9, 6, 'Tour Guidato', 30, 0, 25.00, 30),
(10, 3, 'Prato', 20, 0, 100.00, 20);

-- --------------------------------------------------------

--
-- Struttura della tabella `tutente`
--

CREATE TABLE `tutente` (
  `idUtente` int(11) NOT NULL,
  `nome` varchar(50) DEFAULT NULL,
  `cognome` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `dataNascita` date DEFAULT NULL,
  `luogoNascita` varchar(255) DEFAULT NULL,
  `codiceFiscale` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `tutente`
--

INSERT INTO `tutente` (`idUtente`, `nome`, `cognome`, `email`, `password`, `remember_token`, `dataNascita`, `luogoNascita`, `codiceFiscale`) VALUES
(1, 'Admin', 'admin', 'admin', 'admin', NULL, NULL, NULL, NULL),
(2, 'devin', 'donadoni', 'devin.donadoni@aulic.it', 'aulicEmpire', NULL, NULL, NULL, NULL),
(5, 'devin', 'donadoni', 'donadoni.devin@gmail.com', NULL, NULL, NULL, NULL, NULL),
(6, 'devin', 'donadoni', 'donadev.donadoni@gmail.com', NULL, NULL, '2006-08-14', 'roma', 'DNDDVN06M14H501E');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `tcarrello`
--
ALTER TABLE `tcarrello`
  ADD PRIMARY KEY (`idCarrello`),
  ADD KEY `idPrenotazione` (`idPrenotazione`),
  ADD KEY `idUtente` (`idUtente`),
  ADD KEY `idEvento` (`idEvento`);

--
-- Indici per le tabelle `tevento`
--
ALTER TABLE `tevento`
  ADD PRIMARY KEY (`idEvento`),
  ADD KEY `idLuogo` (`idLuogo`);

--
-- Indici per le tabelle `tluogo`
--
ALTER TABLE `tluogo`
  ADD PRIMARY KEY (`idLuogo`);

--
-- Indici per le tabelle `tpagamento`
--
ALTER TABLE `tpagamento`
  ADD PRIMARY KEY (`idPagamento`),
  ADD KEY `idPrenotazione` (`idPrenotazione`),
  ADD KEY `idCarrello` (`idCarrello`);

--
-- Indici per le tabelle `tposto`
--
ALTER TABLE `tposto`
  ADD PRIMARY KEY (`idPosto`),
  ADD KEY `idSettore` (`idSettore`),
  ADD KEY `idUtente` (`idUtente`);

--
-- Indici per le tabelle `tprenotazione`
--
ALTER TABLE `tprenotazione`
  ADD PRIMARY KEY (`idPrenotazione`),
  ADD KEY `idSettore` (`idSettore`),
  ADD KEY `idUtente` (`idUtente`),
  ADD KEY `idPosto` (`idPosto`),
  ADD KEY `idEvento` (`idEvento`);

--
-- Indici per le tabelle `tsettore`
--
ALTER TABLE `tsettore`
  ADD PRIMARY KEY (`idSettore`),
  ADD KEY `idEvento` (`idEvento`);

--
-- Indici per le tabelle `tutente`
--
ALTER TABLE `tutente`
  ADD PRIMARY KEY (`idUtente`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `tcarrello`
--
ALTER TABLE `tcarrello`
  MODIFY `idCarrello` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT per la tabella `tevento`
--
ALTER TABLE `tevento`
  MODIFY `idEvento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT per la tabella `tluogo`
--
ALTER TABLE `tluogo`
  MODIFY `idLuogo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT per la tabella `tpagamento`
--
ALTER TABLE `tpagamento`
  MODIFY `idPagamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT per la tabella `tposto`
--
ALTER TABLE `tposto`
  MODIFY `idPosto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=211;

--
-- AUTO_INCREMENT per la tabella `tprenotazione`
--
ALTER TABLE `tprenotazione`
  MODIFY `idPrenotazione` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT per la tabella `tsettore`
--
ALTER TABLE `tsettore`
  MODIFY `idSettore` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT per la tabella `tutente`
--
ALTER TABLE `tutente`
  MODIFY `idUtente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `tcarrello`
--
ALTER TABLE `tcarrello`
  ADD CONSTRAINT `tcarrello_ibfk_1` FOREIGN KEY (`idPrenotazione`) REFERENCES `tprenotazione` (`idPrenotazione`),
  ADD CONSTRAINT `tcarrello_ibfk_2` FOREIGN KEY (`idUtente`) REFERENCES `tutente` (`idUtente`),
  ADD CONSTRAINT `tcarrello_ibfk_3` FOREIGN KEY (`idEvento`) REFERENCES `tevento` (`idEvento`);

--
-- Limiti per la tabella `tevento`
--
ALTER TABLE `tevento`
  ADD CONSTRAINT `tevento_ibfk_1` FOREIGN KEY (`idLuogo`) REFERENCES `tluogo` (`idLuogo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `tpagamento`
--
ALTER TABLE `tpagamento`
  ADD CONSTRAINT `tpagamento_ibfk_1` FOREIGN KEY (`idPrenotazione`) REFERENCES `tprenotazione` (`idPrenotazione`),
  ADD CONSTRAINT `tpagamento_ibfk_2` FOREIGN KEY (`idCarrello`) REFERENCES `tcarrello` (`idCarrello`);

--
-- Limiti per la tabella `tposto`
--
ALTER TABLE `tposto`
  ADD CONSTRAINT `idUtente` FOREIGN KEY (`idUtente`) REFERENCES `tutente` (`idUtente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tposto_ibfk_1` FOREIGN KEY (`idSettore`) REFERENCES `tsettore` (`idSettore`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `tprenotazione`
--
ALTER TABLE `tprenotazione`
  ADD CONSTRAINT `tprenotazione_ibfk_1` FOREIGN KEY (`idSettore`) REFERENCES `tsettore` (`idSettore`),
  ADD CONSTRAINT `tprenotazione_ibfk_2` FOREIGN KEY (`idUtente`) REFERENCES `tutente` (`idUtente`),
  ADD CONSTRAINT `tprenotazione_ibfk_3` FOREIGN KEY (`idPosto`) REFERENCES `tposto` (`idPosto`),
  ADD CONSTRAINT `tprenotazione_ibfk_4` FOREIGN KEY (`idEvento`) REFERENCES `tevento` (`idEvento`);

--
-- Limiti per la tabella `tsettore`
--
ALTER TABLE `tsettore`
  ADD CONSTRAINT `tsettore_ibfk_1` FOREIGN KEY (`idEvento`) REFERENCES `tevento` (`idEvento`);

DELIMITER $$
--
-- Eventi
--
CREATE DEFINER=`root`@`localhost` EVENT `cancellaPrenotazioniScadute` ON SCHEDULE EVERY 1 MINUTE STARTS '2025-01-30 15:26:39' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE tCarrello c
    JOIN tPrenotazione p ON c.idPrenotazione = p.idPrenotazione
    JOIN tSettore s ON p.idSettore = s.idSettore
    LEFT JOIN tPosto po ON p.idPosto = po.idPosto
    SET 
        c.disponibile = 0,
        p.statoPrenotazione = 'cancellata',
        s.postiTotali = s.postiTotali + 1,
        po.disponibile = IF(p.idPosto IS NOT NULL, 1, po.disponibile),
        po.idUtente = IF(p.idPosto IS NOT NULL, NULL, po.idUtente)
    WHERE c.dataAggiunta < NOW() - INTERVAL 24 HOUR$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
