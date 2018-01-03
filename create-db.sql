SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `designacoes` (
  `designacao_id` int(10) UNSIGNED NOT NULL,
  `designacao_territorio` tinyint(3) UNSIGNED NOT NULL,
  `designacao_entrega` date NOT NULL,
  `designacao_devolucao` date DEFAULT NULL,
  `designacao_comentario` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `designacoes_irmaos` (
  `designacao_id` int(10) UNSIGNED NOT NULL,
  `irmao_id` tinyint(3) UNSIGNED NOT NULL,
  `deir_comentario` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `designacoes_saidas` (
  `designacao_id` int(10) UNSIGNED NOT NULL,
  `saida_id` tinyint(3) UNSIGNED NOT NULL,
  `desa_comentario` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `irmaos` (
  `irmao_id` int(10) UNSIGNED NOT NULL,
  `irmao_nome` tinytext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `saidas` (
  `saida_id` tinyint(3) UNSIGNED NOT NULL,
  `saida_nome` tinytext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


ALTER TABLE `designacoes`
  ADD PRIMARY KEY (`designacao_id`);

ALTER TABLE `designacoes_irmaos`
  ADD PRIMARY KEY (`designacao_id`,`irmao_id`);

ALTER TABLE `designacoes_saidas`
  ADD PRIMARY KEY (`designacao_id`,`saida_id`);

ALTER TABLE `irmaos`
  ADD PRIMARY KEY (`irmao_id`);

ALTER TABLE `saidas`
  ADD PRIMARY KEY (`saida_id`);


ALTER TABLE `designacoes`
  MODIFY `designacao_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `irmaos`
  MODIFY `irmao_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `saidas`
  MODIFY `saida_id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT;COMMIT;
