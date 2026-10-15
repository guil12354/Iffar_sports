-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 15/10/2026 às 18:38
-- Versão do servidor: 9.1.0
-- Versão do PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `iffar_sports3`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `atletas_equipe`
--

DROP TABLE IF EXISTS `atletas_equipe`;
CREATE TABLE IF NOT EXISTS `atletas_equipe` (
  `id_atleta` int NOT NULL AUTO_INCREMENT,
  `id_equipe` int NOT NULL,
  `nome` varchar(150) NOT NULL,
  `numero` smallint UNSIGNED DEFAULT NULL,
  `posicao` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`id_atleta`),
  KEY `fk_atletas_equipe` (`id_equipe`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `atletas_equipe`
--

INSERT INTO `atletas_equipe` (`id_atleta`, `id_equipe`, `nome`, `numero`, `posicao`) VALUES
(1, 1, 'sla', 10, 'a');

-- --------------------------------------------------------

--
-- Estrutura para tabela `equipes`
--

DROP TABLE IF EXISTS `equipes`;
CREATE TABLE IF NOT EXISTS `equipes` (
  `id_equipe` int NOT NULL AUTO_INCREMENT,
  `id_esporte` int NOT NULL,
  `nome` varchar(150) NOT NULL,
  `ano` smallint UNSIGNED NOT NULL,
  `categoria` enum('masculino','feminino','misto') NOT NULL,
  `tecnico` varchar(150) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `historico` text,
  `status` enum('atual','historica') NOT NULL DEFAULT 'historica',
  PRIMARY KEY (`id_equipe`),
  KEY `fk_equipes_esporte` (`id_esporte`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `equipes`
--

INSERT INTO `equipes` (`id_equipe`, `id_esporte`, `nome`, `ano`, `categoria`, `tecnico`, `foto`, `historico`, `status`) VALUES
(1, 4, 'equipe 1', 2026, 'masculino', 'eu', NULL, '<p><br></p>', 'historica');

-- --------------------------------------------------------

--
-- Estrutura para tabela `esportes`
--

DROP TABLE IF EXISTS `esportes`;
CREATE TABLE IF NOT EXISTS `esportes` (
  `id_esporte` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `descricao` text,
  `feminino_dias` varchar(100) DEFAULT NULL,
  `feminino_horario` varchar(50) DEFAULT NULL,
  `feminino_local` varchar(100) DEFAULT NULL,
  `feminino_professor` varchar(100) DEFAULT NULL,
  `masculino_dias` varchar(100) DEFAULT NULL,
  `masculino_horario` varchar(50) DEFAULT NULL,
  `masculino_local` varchar(100) DEFAULT NULL,
  `masculino_professor` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_esporte`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `esportes`
--

INSERT INTO `esportes` (`id_esporte`, `nome`, `imagem`, `descricao`, `feminino_dias`, `feminino_horario`, `feminino_local`, `feminino_professor`, `masculino_dias`, `masculino_horario`, `masculino_local`, `masculino_professor`) VALUES
(1, 'Futsal', '', 'Treinamentos de Futsal focados no desenvolvimento tático e preparação para campeonatos.', 'Terças e Quintas', '18:00 - 19:31', 'Quadra Poliesportiva', 'Prof. Roberto Silva', 'Segundas e Quartas', '18:00 - 19:30', 'Quadra Poliesportiva', 'Prof. Roberto Silva'),
(2, 'Basquete', '', 'Prática de basquetebol para integração e desenvolvimento esportivo.', 'Quartas e Sextas', '17:00 - 18:31', 'Ginásio Principal', 'Profª. Juliana Costa', 'Terças e Quintas', '17:00 - 18:30', 'Ginásio Principal', 'Profª. Juliana Costa'),
(4, 'a', 'img/esportes/1789760454_88b804ef-b1c8-443a-b6f0-2adf0d46fd38.jfif', 'a', 'a', '', 'a', 'a', 'a', 'a', 'a', 'a'),
(5, 'a', '88b804ef-b1c8-443a-b6f0-2adf0d46fd38.jfif', 'a', '1', '1', '1', '1', '1', '1', '1', '1');

-- --------------------------------------------------------

--
-- Estrutura para tabela `noticias`
--

DROP TABLE IF EXISTS `noticias`;
CREATE TABLE IF NOT EXISTS `noticias` (
  `id_noticia` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(200) NOT NULL,
  `resumo` text,
  `descricao` text NOT NULL,
  `autor` varchar(100) DEFAULT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `data_noticia` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_noticia`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `noticias`
--

INSERT INTO `noticias` (`id_noticia`, `titulo`, `resumo`, `descricao`, `autor`, `categoria`, `imagem`, `data_noticia`) VALUES
(5, 'a', 'a', 'a', NULL, 'Esportes', '', '2026-09-18 19:06:27'),
(6, 'a', 'a', 'a', NULL, 'Esportes', '', '2026-09-18 19:07:22'),
(7, 'a', 'a', 'a', NULL, 'Esportes', '', '2026-09-18 19:07:23'),
(8, 'a', 'a', 'a', 'Teste', 'Esportes', '', '2026-09-18 19:23:16'),
(9, 'a', 'a', 'a', 'Teste', 'Esportes', '', '2026-09-18 19:24:06'),
(10, 'a', 'a', 'a', 'Teste', 'Eventos', '', '2026-09-18 19:24:18'),
(11, 'b', 'b', 'b', 'Teste', 'Esportes', '', '2026-09-18 19:28:56'),
(12, 'a', 'a', 'a', 'Teste', 'Competições', 'img/noticias/1789759917_88b804ef-b1c8-443a-b6f0-2adf0d46fd38.jfif', '2026-09-18 19:31:57'),
(13, 'a', 'a', 'a', 'Administrador', 'Eventos', '', '2026-10-02 19:37:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `recuperar_senha`
--

DROP TABLE IF EXISTS `recuperar_senha`;
CREATE TABLE IF NOT EXISTS `recuperar_senha` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(100) DEFAULT NULL,
  `token` varchar(100) DEFAULT NULL,
  `usado` tinyint(1) NOT NULL DEFAULT '0',
  `data` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `recuperar_senha`
--

INSERT INTO `recuperar_senha` (`id`, `email`, `token`, `usado`, `data`) VALUES
(1, 'admin@iffar.com', '0ce9c45c44f892c2e66fa4c672afa374c430fc319e631afccc3ff922e9ad2524', 0, '2026-10-09 16:05:15'),
(2, 'admin@iffar.com', '1eaa75bcaec85114bdd7dced7b048fdafc9d2a72c301b564f2737010df756a97', 0, '2026-10-09 16:05:15'),
(3, 'guilherme.06120@aluno.iffar.edu.br', 'eab6f797278f318f23762592fef2d24826a9c5f2eb82bedc2b8029a393c9773d', 1, '2026-10-09 16:05:15'),
(4, 'guilherme.06120@aluno.iffar.edu.br', '59235c6fc54cb398d0434578a14d68b9e88894eaa282e1da1460abe703e3894c', 1, '2026-10-09 16:05:15'),
(5, 'guilherme.06120@aluno.iffar.edu.br', 'a05b4bfe6fd9a80b312c855c671761bb2be7cdc3fe8273a614cfb2cadbeeea97', 1, '2026-10-09 16:05:15'),
(6, 'guilherme.06120@aluno.iffar.edu.br', '68155cbb28a4ceb39eeb548f6f9944eed378656e33874950b8447ee6471b0fe4', 1, '2026-10-09 16:05:15'),
(7, 'guilherme.06120@aluno.iffar.edu.br', '88223af9e4fcb1c8ff0eb1c5b31bf0a4dc84c4165ad292360f7838b762fbcb35', 1, '2026-10-09 16:05:15'),
(8, 'guilherme.06120@aluno.iffar.edu.br', 'ac773d076cd1dc10fcc09b9c9922fec656db31d0a1059d866e8298097f9dba07', 1, '2026-10-09 16:05:21'),
(9, 'guilherme.06120@aluno.iffar.edu.br', '8da3f6d78eaac684257328377fcf9b76759af8b49486624a2e890aa737e6a062', 1, '2026-10-09 16:05:24'),
(10, 'guilherme.06120@aluno.iffar.edu.br', '16a90d515e348d07d3323dfd237a83611704ccd4d6170aa2980c6ea150ffa328', 1, '2026-10-09 16:20:09'),
(11, 'guilherme.06120@aluno.iffar.edu.br', '03ba48fcb667f55347d8e943f7e56956e3c7319ebe05e37490c6bad29ec0dcc8', 1, '2026-10-09 16:20:50'),
(12, 'guilherme.06120@aluno.iffar.edu.br', 'a5faba4368bfde8e615d66991bd0840628b6ae11af39c187aa6317c30bf8744f', 1, '2026-10-09 16:22:15'),
(13, 'guilherme.06120@aluno.iffar.edu.br', '4d8c1af445c5ed4d36429fbe0237534c6a6b6a0a341cc1439894a76234a4d63c', 1, '2026-10-09 16:24:25'),
(14, 'guilherme.06120@aluno.iffar.edu.br', '4d8c1af445c5ed4d36429fbe0237534c6a6b6a0a341cc1439894a76234a4d63c', 1, '2026-10-09 16:24:25'),
(15, 'guilherme.06120@aluno.iffar.edu.br', 'e4364de10d1514f415a141d7365a29c0daf6aa3aaae01cdc9dd447abf88e089d', 0, '2026-10-09 16:25:19'),
(16, 'guilherme.06120@aluno.iffar.edu.br', 'e4364de10d1514f415a141d7365a29c0daf6aa3aaae01cdc9dd447abf88e089d', 0, '2026-10-09 16:25:19');

-- --------------------------------------------------------

--
-- Estrutura para tabela `treinos`
--

DROP TABLE IF EXISTS `treinos`;
CREATE TABLE IF NOT EXISTS `treinos` (
  `id_treino` int NOT NULL AUTO_INCREMENT,
  `id_esporte` int NOT NULL,
  `dia_semana` varchar(30) DEFAULT NULL,
  `horario_inicio` time DEFAULT NULL,
  `horario_termino` time DEFAULT NULL,
  `local` varchar(100) DEFAULT NULL,
  `categoria` varchar(30) DEFAULT NULL,
  `responsavel` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_treino`),
  KEY `id_esporte` (`id_esporte`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `nivel` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_usuario`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nome`, `email`, `senha`, `nivel`) VALUES
(1, 'Administrador', 'admin@iffar.com', '$2y$10$RpvcaR5t9Cuy6DP7bhk01.pct4E0bbHD86aLXVNiTKH28lFyhCsVe', 'administrador'),
(3, 'Teste', 'teste@iffar.com', '$2y$10$lOemKxuRNzRPdmIxa8rsOusra8hwNvnlDJhx8b/U.1vsoo1WOnqXi', 'administrador'),
(4, 'Guilherme ', 'guilherme.06120@aluno.iffar.edu.br', '$2y$10$AkTGkdzh32i62/ERAcibo.SUvUxPn5oa8YKcOQKLX4kk07I.0pyd6', 'administrador');

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `atletas_equipe`
--
ALTER TABLE `atletas_equipe`
  ADD CONSTRAINT `fk_atletas_equipe` FOREIGN KEY (`id_equipe`) REFERENCES `equipes` (`id_equipe`) ON DELETE CASCADE;

--
-- Restrições para tabelas `equipes`
--
ALTER TABLE `equipes`
  ADD CONSTRAINT `fk_equipes_esporte` FOREIGN KEY (`id_esporte`) REFERENCES `esportes` (`id_esporte`) ON DELETE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
