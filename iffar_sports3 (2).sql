-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 01/10/2026 às 16:07
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(12, 'a', 'a', 'a', 'Teste', 'Competições', 'img/noticias/1789759917_88b804ef-b1c8-443a-b6f0-2adf0d46fd38.jfif', '2026-09-18 19:31:57');

-- --------------------------------------------------------

--
-- Estrutura para tabela `recuperar_senha`
--

DROP TABLE IF EXISTS `recuperar_senha`;
CREATE TABLE IF NOT EXISTS `recuperar_senha` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(100) DEFAULT NULL,
  `token` varchar(100) DEFAULT NULL,
  `usado` int DEFAULT NULL,
  `data` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `recuperar_senha`
--

INSERT INTO `recuperar_senha` (`id`, `email`, `token`, `usado`, `data`) VALUES
(1, 'admin@iffar.com', '0ce9c45c44f892c2e66fa4c672afa374c430fc319e631afccc3ff922e9ad2524', NULL, NULL),
(2, 'admin@iffar.com', '1eaa75bcaec85114bdd7dced7b048fdafc9d2a72c301b564f2737010df756a97', NULL, NULL),
(3, 'guilherme.06120@aluno.iffar.edu.br', 'eab6f797278f318f23762592fef2d24826a9c5f2eb82bedc2b8029a393c9773d', NULL, NULL),
(4, 'guilherme.06120@aluno.iffar.edu.br', '59235c6fc54cb398d0434578a14d68b9e88894eaa282e1da1460abe703e3894c', NULL, NULL),
(5, 'guilherme.06120@aluno.iffar.edu.br', 'a05b4bfe6fd9a80b312c855c671761bb2be7cdc3fe8273a614cfb2cadbeeea97', NULL, NULL),
(6, 'guilherme.06120@aluno.iffar.edu.br', '68155cbb28a4ceb39eeb548f6f9944eed378656e33874950b8447ee6471b0fe4', NULL, NULL);

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
(4, 'Guilherme ', 'guilherme.06120@aluno.iffar.edu.br', '$2y$10$KTR76uVWP92K/NfF03OO1OCF2xDQEB1LuuspEcTWZAfb9R2DiVdvq', 'administrador');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
