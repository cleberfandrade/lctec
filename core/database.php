CREATE TABLE `tb_cargos_salarios` (
	`CGS_COD` INT(11) NOT NULL AUTO_INCREMENT,
	`EMP_COD` INT(11) NOT NULL,
	`USU_COD` INT(11) NOT NULL,
	`CGS_DT_CADASTRO` DATETIME NOT NULL,
	`CGS_DT_ATUALIZACAO` DATETIME NULL DEFAULT NULL,
	`CGS_NOME` VARCHAR(155) NOT NULL COLLATE 'utf8_general_ci',
	`CGS_SALARIO` DECIMAL(10,2) NULL DEFAULT NULL,
	`CGS_NIVEL` VARCHAR(111) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`CGS_TIPO` VARCHAR(11) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`CGS_COMISSAO` VARCHAR(11) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`CGS_STATUS` INT(11) NOT NULL,
	PRIMARY KEY (`CGS_COD`) USING BTREE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=6
;

------------------------------------------------------------------------------------

CREATE TABLE `tb_categorias` (
	`CAT_COD` INT(11) NOT NULL AUTO_INCREMENT,
	`EMP_COD` INT(11) NOT NULL,
	`USU_COD` INT(11) NOT NULL,
	`SET_COD` INT(11) NULL DEFAULT NULL,
	`CAT_DT_CADASTRO` DATETIME NULL DEFAULT NULL,
	`CAT_DT_ATUALIZACAO` DATETIME NULL DEFAULT NULL,
	`CAT_DESCRICAO` VARCHAR(150) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`CAT_NIVEL` VARCHAR(150) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`CAT_STATUS` VARCHAR(150) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	PRIMARY KEY (`CAT_COD`) USING BTREE,
	INDEX `USU_COD` (`USU_COD`) USING BTREE,
	INDEX `EMP_COD` (`EMP_COD`) USING BTREE,
	INDEX `SET_COD` (`SET_COD`) USING BTREE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=6
;

CREATE TABLE `tb_classificacoes` (
	`CLA_COD` INT(11) NOT NULL AUTO_INCREMENT,
	`EMP_COD` INT(11) NOT NULL,
	`USU_COD` INT(11) NOT NULL,
	`CLA_TIPO` INT(11) NULL DEFAULT NULL,
	`CLA_DT_CADASTRO` DATETIME NULL DEFAULT NULL,
	`CLA_DT_ATUALIZACAO` DATETIME NULL DEFAULT NULL,
	`CLA_DESCRICAO` VARCHAR(150) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`CLA_STATUS` VARCHAR(150) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	PRIMARY KEY (`CLA_COD`) USING BTREE,
	INDEX `USU_COD` (`USU_COD`) USING BTREE,
	INDEX `EMP_COD` (`EMP_COD`) USING BTREE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;

CREATE TABLE `tb_clientes` (
	`CLI_COD` INT(11) NOT NULL AUTO_INCREMENT,
	`EMP_COD` INT(11) NOT NULL,
	`USU_COD` INT(11) NOT NULL,
	`CLI_TIPO` VARCHAR(155) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`CLI_DT_CADASTRO` DATETIME NOT NULL,
	`CLI_DT_ATUALIZACAO` DATETIME NULL DEFAULT NULL,
	`CLI_NOME` VARCHAR(155) NOT NULL COLLATE 'utf8_general_ci',
	`CLI_SOBRENOME` VARCHAR(255) NOT NULL COLLATE 'utf8_general_ci',
	`CLI_TEL_1` VARCHAR(155) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`CLI_TEL_2` VARCHAR(155) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`CLI_EMAIL` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`CLI_REGISTRO` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`CLI_DT_NASCIMENTO` DATE NULL DEFAULT NULL,
	`CLI_SEXO` VARCHAR(12) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`CLI_OBSERVACAO` TEXT NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`CLI_STATUS` INT(11) NOT NULL,
	PRIMARY KEY (`CLI_COD`) USING BTREE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=4
;

CREATE TABLE `tb_colaboradores` (
	`COL_COD` INT(11) NOT NULL AUTO_INCREMENT,
	`EMP_COD` INT(11) NOT NULL,
	`USU_COD` INT(11) NOT NULL,
	`CGS_COD` INT(11) NULL DEFAULT NULL,
	`SET_COD` INT(11) NULL DEFAULT NULL,
	`CLA_COD` INT(11) NULL DEFAULT NULL,
	`COL_DT_CADASTRO` DATETIME NOT NULL,
	`COL_DT_ATUALIZACAO` DATETIME NULL DEFAULT NULL,
	`COL_CODIGO` VARCHAR(14) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`COL_NOME` VARCHAR(155) NOT NULL COLLATE 'utf8_general_ci',
	`COL_SOBRENOME` VARCHAR(155) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`COL_EMAIL` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`COL_SENHA` VARCHAR(300) NOT NULL COLLATE 'utf8_general_ci',
	`COL_COMISSAO` VARCHAR(15) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`COL_SEXO` INT(11) NOT NULL,
	`COL_NIVEL` INT(11) NOT NULL,
	`COL_TEL_1` VARCHAR(155) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`COL_TEL_2` VARCHAR(155) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`COL_STATUS` INT(11) NOT NULL,
	PRIMARY KEY (`COL_COD`) USING BTREE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=5
;

CREATE TABLE `tb_contas` (
	`CTA_COD` INT(11) NOT NULL AUTO_INCREMENT,
	`EMP_COD` INT(11) NULL DEFAULT NULL,
	`CTA_TIPO` VARCHAR(11) NOT NULL COLLATE 'utf8_general_ci',
	`CTA_DT_CADASTRO` DATETIME NOT NULL,
	`CTA_DT_ATUALIZACAO` DATETIME NULL DEFAULT NULL,
	`CTA_NOME` VARCHAR(255) NOT NULL COLLATE 'utf8_general_ci',
	`CTA_DESCRICAO` TEXT NOT NULL COLLATE 'utf8_general_ci',
	`CTA_SALDO` DECIMAL(10,2) NULL DEFAULT NULL,
	`CTA_STATUS` INT(1) NOT NULL,
	PRIMARY KEY (`CTA_COD`) USING BTREE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=2
;

CREATE TABLE `tb_empresas` (
	`EMP_COD` INT(11) NOT NULL AUTO_INCREMENT,
	`EMP_TIPO` VARCHAR(255) NOT NULL COLLATE 'utf8_general_ci',
	`EMP_DT_CADASTRO` DATETIME NOT NULL,
	`EMP_DT_ATUALIZACAO` DATETIME NULL DEFAULT NULL,
	`EMP_NOME_FANTASIA` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`EMP_RAZAO_SOCIAL` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`EMP_REGULAMENTACAO` VARCHAR(255) NOT NULL COLLATE 'utf8_general_ci',
	`EMP_PORTE` VARCHAR(255) NOT NULL COLLATE 'utf8_general_ci',
	`EMP_REGISTRO` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`EMP_INSCRICAO_ESTADUAL` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`EMP_DT_FUNDACAO` DATE NULL DEFAULT NULL,
	`EMP_TEL_1` VARCHAR(155) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`EMP_TEL_2` VARCHAR(155) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`EMP_CEL_1` VARCHAR(155) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`EMP_CEL_2` VARCHAR(155) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`EMP_EMAIL_1` VARCHAR(155) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`EMP_EMAIL_2` VARCHAR(155) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`EMP_DESCRICAO` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`EMP_STATUS` INT(1) NOT NULL,
	PRIMARY KEY (`EMP_COD`) USING BTREE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=3
;
CREATE TABLE `tb_enderecos` (
	`END_COD` INT(11) NOT NULL AUTO_INCREMENT,
	`EMP_COD` INT(11) NULL DEFAULT NULL,
	`USU_COD` INT(11) NULL DEFAULT NULL,
	`FOR_COD` INT(11) NULL DEFAULT NULL,
	`CLI_COD` INT(11) NULL DEFAULT NULL,
	`COL_COD` INT(11) NULL DEFAULT NULL,
	`END_DT_CADASTRO` DATETIME NOT NULL,
	`END_DT_ATUALIZACAO` DATETIME NULL DEFAULT NULL,
	`END_LOGRADOURO` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`END_NUMERO` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`END_BAIRRO` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`END_CIDADE` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`END_ESTADO` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`END_CEP` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`END_TEL_1` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`END_TEL_2` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`END_CEL_1` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`END_CEL_2` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`END_WHATSAPP` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`END_STATUS` INT(1) NOT NULL,
	PRIMARY KEY (`END_COD`) USING BTREE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=25
;
CREATE TABLE `tb_estoques` (
	`EST_COD` INT(11) NOT NULL AUTO_INCREMENT,
	`EMP_COD` INT(11) NOT NULL,
	`USU_COD` INT(11) NOT NULL,
	`EST_DT_CADASTRO` DATETIME NOT NULL,
	`EST_DT_ATUALIZACAO` DATETIME NULL DEFAULT NULL,
	`EST_TIPO` VARCHAR(255) NOT NULL COLLATE 'utf8_general_ci',
	`EST_DESCRICAO` VARCHAR(255) NOT NULL COLLATE 'utf8_general_ci',
	`EST_STATUS` INT(11) NOT NULL,
	PRIMARY KEY (`EST_COD`) USING BTREE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=4
;
CREATE TABLE `tb_formas_pagamentos` (
	`FPG_COD` INT(11) NOT NULL AUTO_INCREMENT,
	`EMP_COD` INT(11) NOT NULL,
	`USU_COD` INT(11) NOT NULL,
	`FPG_TIPO` INT(11) NULL DEFAULT NULL,
	`FPG_DT_CADASTRO` DATETIME NULL DEFAULT NULL,
	`FPG_DT_ATUALIZACAO` DATETIME NULL DEFAULT NULL,
	`FPG_DESCRICAO` VARCHAR(150) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`FPG_PRAZO` VARCHAR(150) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`FPG_STATUS` VARCHAR(150) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	PRIMARY KEY (`FPG_COD`) USING BTREE,
	INDEX `USU_COD` (`USU_COD`) USING BTREE,
	INDEX `EMP_COD` (`EMP_COD`) USING BTREE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=9
;
CREATE TABLE `tb_fornecedores` (
	`FOR_COD` INT(11) NOT NULL AUTO_INCREMENT,
	`EMP_COD` INT(11) NOT NULL,
	`USU_COD` INT(11) NOT NULL,
	`FOR_DT_CADASTRO` DATETIME NOT NULL,
	`FOR_DT_ATUALIZACAO` DATETIME NULL DEFAULT NULL,
	`FOR_TIPO` VARCHAR(155) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`FOR_NOME_FANTASIA` VARCHAR(255) NOT NULL COLLATE 'utf8_general_ci',
	`FOR_RAZAO_SOCIAL` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`FOR_REGISTRO` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`FOR_INSCRICAO_ESTADUAL` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`FOR_TEL_1` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`FOR_TEL_2` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`FOR_CEL_1` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`FOR_CEL_2` VARCHAR(55) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`FOR_EMAIL_1` VARCHAR(155) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`FOR_EMAIL_2` VARCHAR(155) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`FOR_RESPONSAVEL` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`FOR_DESCRICAO` TEXT NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`FOR_STATUS` INT(11) NOT NULL,
	PRIMARY KEY (`FOR_COD`) USING BTREE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=2
;
CREATE TABLE `tb_modulos` (
	`MOD_COD` INT(11) NOT NULL AUTO_INCREMENT,
	`MOD_DT_CADASTRO` DATETIME NOT NULL,
	`MOD_DT_ATUALIZACAO` DATETIME NULL DEFAULT NULL,
	`MOD_NOME` VARCHAR(150) NOT NULL COLLATE 'utf8_general_ci',
	`MOD_DESCRICAO` TEXT NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`MOD_LINK` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`MOD_COR` VARCHAR(25) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`MOD_ICON` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`MOD_STATUS` INT(11) NOT NULL,
	PRIMARY KEY (`MOD_COD`) USING BTREE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=4
;
CREATE TABLE `tb_modulos_empresa` (
	`MLE_COD` INT(11) NOT NULL AUTO_INCREMENT,
	`EMP_COD` INT(11) NOT NULL,
	`MOD_COD` INT(11) NOT NULL,
	`MLE_DT_CADASTRO` DATETIME NOT NULL,
	`MLE_STATUS` INT(11) NOT NULL,
	PRIMARY KEY (`MLE_COD`) USING BTREE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=5
;
CREATE TABLE `tb_movimentacao` (
	`MOV_COD` INT(11) NOT NULL AUTO_INCREMENT,
	`EMP_COD` INT(11) NOT NULL,
	`USU_COD` INT(11) NOT NULL,
	`EST_COD` INT(11) NOT NULL,
	`PRO_COD` INT(11) NULL DEFAULT NULL,
	`SER_COD` INT(11) NULL DEFAULT NULL,
	`MOV_DT_CADASTRO` DATETIME NOT NULL,
	`MOV_DT_ATUALIZACAO` DATETIME NULL DEFAULT NULL,
	`MOV_ENTRADA` VARCHAR(155) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`MOV_SAIDA` VARCHAR(155) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`MOV_STATUS` INT(11) NOT NULL,
	`CLI_COD` INT(11) NULL DEFAULT NULL,
	PRIMARY KEY (`MOV_COD`) USING BTREE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;
CREATE TABLE `tb_produtos` (
	`PRO_COD` INT(11) NOT NULL AUTO_INCREMENT,
	`EMP_COD` INT(11) NOT NULL,
	`USU_COD` INT(11) NOT NULL,
	`EST_COD` INT(11) NOT NULL,
	`FOR_COD` INT(11) NULL DEFAULT NULL,
	`CAT_COD` INT(11) NULL DEFAULT NULL,
	`PRO_TIPO` VARCHAR(155) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`PRO_DT_CADASTRO` DATETIME NOT NULL,
	`PRO_DT_ATUALIZACAO` DATETIME NOT NULL,
	`PRO_NOME` VARCHAR(155) NOT NULL COLLATE 'utf8_general_ci',
	`PRO_DESCRICAO` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`PRO_CODIGO` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`PRO_REFERENCIA` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`PRO_GRUPO` VARCHAR(155) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`PRO_PRECO_UNITARIO` DECIMAL(10,2) NULL DEFAULT NULL,
	`PRO_PRECO_VENDA` DECIMAL(10,2) NULL DEFAULT NULL,
	`PRO_UNIDADE` VARCHAR(155) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`PRO_QUANTIDADE` INT(11) NOT NULL,
	`PRO_IMPOSTO` VARCHAR(155) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`PRO_MARGEM` VARCHAR(155) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`PRO_DESCONTO` VARCHAR(155) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`PRO_QTD_MIN` INT(11) NULL DEFAULT NULL,
	`PRO_QTD_MAX` INT(11) NULL DEFAULT NULL,
	`PRO_IMAGEM` VARCHAR(555) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`PRO_STATUS` INT(11) NOT NULL,
	PRIMARY KEY (`PRO_COD`) USING BTREE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=3
;

------------------------------------------------------------------------------------

CREATE TABLE `tb_promocoes` (
	`PRM_COD` INT(11) NOT NULL AUTO_INCREMENT,
	`EMP_COD` INT(11) NULL DEFAULT NULL,
	`USU_COD` INT(11) NULL DEFAULT NULL,
	`EST_COD` INT(11) NULL DEFAULT NULL,
	`SET_COD` INT(11) NULL DEFAULT NULL,
	`CAT_COD` INT(11) NULL DEFAULT NULL,
	`SCT_COD` INT(11) NULL DEFAULT NULL,
	`PRO_COD` INT(11) NULL DEFAULT NULL,
	`PRO_DESCRICAO` VARCHAR(150) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`PRO_DT_CADASTRO` DATETIME NULL DEFAULT NULL,
	`PRO_DT_ATUALIZACAO` DATETIME NULL DEFAULT NULL,
	`PRO_DT_INICIAL` DATETIME NULL DEFAULT NULL,
	`PRO_DT_FINAL` DATETIME NULL DEFAULT NULL,
	`PRO_TEXTO` TEXT NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`PRO_DESCONTO` VARCHAR(150) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`PRO_STATUS` VARCHAR(150) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	PRIMARY KEY (`PRM_COD`) USING BTREE,
	INDEX `USU_COD` (`USU_COD`) USING BTREE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;
CREATE TABLE `tb_recuperacoes` (
	`REC_COD` INT(11) NOT NULL AUTO_INCREMENT,
	`REC_DT_CADASTRO` DATETIME NULL DEFAULT NULL,
	`REC_DT_EXPIRACAO` DATETIME NULL DEFAULT NULL,
	`REC_EMAIL` VARCHAR(250) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`REC_TOKEN` VARCHAR(250) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	PRIMARY KEY (`REC_COD`) USING BTREE,
	UNIQUE INDEX `REC_EMAIL` (`REC_EMAIL`) USING BTREE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;
CREATE TABLE `tb_setores` (
	`SET_COD` INT(11) NOT NULL AUTO_INCREMENT,
	`EMP_COD` INT(11) NOT NULL,
	`USU_COD` INT(11) NULL DEFAULT NULL,
	`SET_DT_CADASTRO` DATETIME NOT NULL,
	`SET_DT_ATUALIZACAO` DATETIME NULL DEFAULT NULL,
	`SET_DESCRICAO` VARCHAR(150) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`SET_NIVEL` VARCHAR(150) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`SET_TIPO` INT(11) NULL DEFAULT NULL,
	`SET_STATUS` VARCHAR(150) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	PRIMARY KEY (`SET_COD`) USING BTREE,
	INDEX `USU_COD` (`USU_COD`) USING BTREE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=3
;
CREATE TABLE `tb_usuarios` (
	`USU_COD` INT(11) NOT NULL AUTO_INCREMENT,
	`EMP_COD` INT(11) NOT NULL,
	`USU_DT_CADASTRO` DATETIME NOT NULL,
	`USU_DT_ATUALIZACAO` DATETIME NULL DEFAULT NULL,
	`USU_NOME` VARCHAR(150) NOT NULL COLLATE 'utf8_general_ci',
	`USU_SOBRENOME` VARCHAR(255) NOT NULL COLLATE 'utf8_general_ci',
	`USU_SEXO` VARCHAR(1) NULL DEFAULT NULL COLLATE 'utf8_general_ci',
	`USU_EMAIL` VARCHAR(255) NOT NULL COLLATE 'utf8_general_ci',
	`USU_SENHA` VARCHAR(255) NOT NULL COLLATE 'utf8_general_ci',
	`USU_NIVEL` INT(11) NOT NULL,
	`USU_STATUS` INT(11) NOT NULL,
	PRIMARY KEY (`USU_COD`) USING BTREE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=3
;

CREATE TABLE `tb_usuarios_empresa` (
	`UMP_COD` INT(11) NOT NULL AUTO_INCREMENT,
	`USU_COD` INT(11) NOT NULL,
	`EMP_COD` INT(11) NOT NULL,
	`UMP_DT_CADASTRO` DATETIME NOT NULL,
	`UMP_STATUS` INT(11) NOT NULL,
	PRIMARY KEY (`UMP_COD`) USING BTREE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=10
;



CREATE TABLE IF NOT EXISTS `lembretes` (
  `LEM_COD` int(11) NOT NULL AUTO_INCREMENT,
  `PES_COD` int(11) NOT NULL,
  `LAN_COD` int(11) NOT NULL,
  `LEM_TEMPO` int(11) DEFAULT NULL,
  `LEM_STATUS` varchar(30) DEFAULT NULL,
  `LEM_LEMBRAR` date DEFAULT NULL,
  PRIMARY KEY (`LEM_COD`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;


CREATE TABLE IF NOT EXISTS `lancamentos` (
  `LAN_COD` int(11) NOT NULL AUTO_INCREMENT,
  `PES_COD` int(11) NOT NULL,
  `CON_COD` int(11) DEFAULT NULL,
  `FP_COD` int(11) NOT NULL,
  `CAT_COD` int(11) DEFAULT NULL,
  `LAN_VEICULO` varchar(4) DEFAULT NULL,
  `LAN_TIPO` varchar(10) NOT NULL,
  `LAN_CLASSIFICACAO` varchar(50) DEFAULT NULL,
  `LAN_NOME` varchar(45) NOT NULL,
  `LAN_VALOR` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '0.00',
  `LAN_CADASTRO` datetime NOT NULL,
  `LAN_VENCIMENTO` date NOT NULL,
  `LAN_PAGAMENTO` date DEFAULT NULL,
  `LAN_OBSERVACOES` varchar(500) DEFAULT NULL,
  `LAN_STATUS` varchar(45) NOT NULL,
  `LAN_RESULTADOS` char(50) DEFAULT NULL,
  PRIMARY KEY (`LAN_COD`),
  KEY `FK_LANCAMENTOS_FORMAS_PAGAMENTO` (`FP_COD`),
  KEY `FK_LANCAMENTOS_CATEGORIA` (`CAT_COD`),
  KEY `FK_LANCAMENTOS_PESSOA` (`PES_COD`),
  KEY `FK_LANCAMENTOS_CONTAS` (`CON_COD`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1638 ;
