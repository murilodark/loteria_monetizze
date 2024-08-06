-- MySQL dump 10.13  Distrib 8.0.27, for Win64 (x86_64)
--
-- Host: localhost    Database: dbloteria
-- ------------------------------------------------------
-- Server version	8.0.39

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `loteria`
--

DROP TABLE IF EXISTS `loteria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `loteria` (
  `idloteria` int NOT NULL AUTO_INCREMENT COMMENT 'id gerado automatico',
  `data_cadastro` datetime DEFAULT NULL COMMENT 'data do cadastro',
  `data_sorteio` datetime DEFAULT NULL COMMENT 'data do sorteio',
  `nome_loteria` varchar(245) DEFAULT NULL COMMENT 'nome da loteria',
  `dezenas_sorteadas` varchar(145) DEFAULT NULL COMMENT 'dez sorteadas separas por virgula',
  `status_loteria` varchar(20) DEFAULT NULL COMMENT 'a = andamento, c = concluido',
  `usuario_sistema_cadastro` int NOT NULL COMMENT 'usuario que cadastrou',
  `usuario_sistema_sorteio` int DEFAULT NULL COMMENT 'usuario que efetuou o sorteio',
  PRIMARY KEY (`idloteria`),
  KEY `fk_loteria_usuario_sistema_idx` (`usuario_sistema_cadastro`),
  KEY `fk_loteria_usuario_sistema1_idx` (`usuario_sistema_sorteio`),
  CONSTRAINT `fk_loteria_usuario_sistema` FOREIGN KEY (`usuario_sistema_cadastro`) REFERENCES `usuario_sistema` (`idusuario_sistema`) ON DELETE CASCADE,
  CONSTRAINT `fk_loteria_usuario_sistema1` FOREIGN KEY (`usuario_sistema_sorteio`) REFERENCES `usuario_sistema` (`idusuario_sistema`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3 COMMENT='registra as loterias cadastradas';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loteria`
--

LOCK TABLES `loteria` WRITE;
/*!40000 ALTER TABLE `loteria` DISABLE KEYS */;
INSERT INTO `loteria` VALUES (7,'2024-08-05 13:08:00','2024-08-05 20:08:44','Loteria dia dos Pais','19,25,27,31,40,54','Finalizada',29,30),(8,'2024-08-05 18:08:18','2024-08-05 20:08:11','nova loteria','10,23,25,32,44,54','Finalizada',30,30);
/*!40000 ALTER TABLE `loteria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario_jogos`
--

DROP TABLE IF EXISTS `usuario_jogos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario_jogos` (
  `idusuario_jogos` int NOT NULL AUTO_INCREMENT COMMENT 'id automatico',
  `quant_dezenas` int DEFAULT NULL COMMENT 'define a quantidade de dezenas do jogo',
  `dezenas_escolhidas` varchar(145) DEFAULT NULL COMMENT 'dezenas escolhidas separadas por virgula',
  `jogo_vencedor` varchar(1) DEFAULT NULL COMMENT 'se e o jogo vencendor recebe S',
  `loteria_idloteria` int NOT NULL COMMENT 'id da loteria do jogo',
  `usuario_sistema_idusuario_sistema` int NOT NULL COMMENT 'id do usuario do jogo',
  PRIMARY KEY (`idusuario_jogos`),
  KEY `fk_usuario_jogos_loteria1_idx` (`loteria_idloteria`),
  KEY `fk_usuario_jogos_usuario_sistema1_idx` (`usuario_sistema_idusuario_sistema`),
  CONSTRAINT `fk_usuario_jogos_loteria1` FOREIGN KEY (`loteria_idloteria`) REFERENCES `loteria` (`idloteria`) ON DELETE CASCADE,
  CONSTRAINT `fk_usuario_jogos_usuario_sistema1` FOREIGN KEY (`usuario_sistema_idusuario_sistema`) REFERENCES `usuario_sistema` (`idusuario_sistema`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb3 COMMENT='registra as dezenas dos usuarios';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario_jogos`
--

LOCK TABLES `usuario_jogos` WRITE;
/*!40000 ALTER TABLE `usuario_jogos` DISABLE KEYS */;
INSERT INTO `usuario_jogos` VALUES (1,8,'11,14,27,30,33,43,46,53',NULL,7,29),(2,8,'13,20,29,32,50,53,57,60',NULL,7,29),(3,8,'3,19,29,41,47,49,51,59',NULL,7,29),(4,8,'4,12,19,32,33,42,44,55',NULL,7,29),(5,8,'13,15,17,19,25,54,57,59',NULL,7,29),(6,8,'3,5,15,30,39,44,51,57',NULL,7,29),(7,8,'3,10,26,33,38,46,54,60',NULL,7,29),(8,8,'11,37,40,41,45,46,56,57',NULL,7,29),(9,8,'4,5,22,25,33,46,51,52',NULL,7,29),(10,8,'2,16,19,24,25,40,41,43',NULL,7,29),(11,8,'16,24,30,36,43,44,46,53',NULL,7,29),(12,8,'5,8,14,15,19,22,36,46',NULL,7,29),(13,8,'12,20,27,33,34,43,50,56',NULL,7,29),(14,8,'7,11,19,20,23,44,50,58',NULL,7,29),(15,8,'1,7,9,22,37,40,43,53',NULL,7,29),(16,8,'2,8,17,18,37,39,47,53',NULL,7,29),(17,8,'5,10,13,20,23,32,59,60',NULL,7,29),(18,8,'2,15,17,35,46,48,49,57',NULL,7,29),(19,8,'6,8,15,22,29,36,41,60',NULL,7,29),(20,8,'16,18,23,26,36,47,49,56',NULL,7,29),(21,10,'12,15,18,25,27,42,53,55,56,57',NULL,7,30),(22,10,'12,25,29,33,35,36,38,54,55,58',NULL,7,30),(23,10,'1,3,10,21,31,33,35,42,58,60',NULL,7,30),(24,10,'5,20,24,29,33,42,47,52,56,59',NULL,7,30),(25,10,'5,6,21,24,27,37,45,53,57,59',NULL,7,30),(26,10,'1,9,16,17,21,32,43,46,55,58',NULL,7,30),(27,10,'6,10,11,12,23,30,33,45,59,60',NULL,7,30),(28,10,'3,6,11,33,36,37,44,45,54,57',NULL,7,30),(29,10,'7,8,10,12,14,29,35,36,51,55',NULL,7,30),(30,10,'1,11,24,26,28,44,45,46,51,56',NULL,7,30),(31,10,'16,18,21,30,33,39,40,41,54,55',NULL,7,30),(32,10,'8,21,24,27,31,41,42,44,49,60',NULL,7,30),(33,10,'9,10,16,18,32,33,37,38,46,57',NULL,7,30),(34,10,'10,17,19,22,26,28,34,48,49,58',NULL,7,30),(35,10,'8,9,25,27,34,47,48,49,54,59',NULL,7,30),(36,10,'2,5,27,32,47,49,51,52,54,56',NULL,7,30),(37,10,'1,11,22,27,30,32,37,38,50,53',NULL,7,30),(38,10,'1,6,16,19,25,30,36,38,50,60',NULL,7,30),(39,10,'2,7,25,26,27,31,39,42,50,57',NULL,7,30),(40,10,'5,11,16,22,24,31,32,39,44,55',NULL,7,30),(41,10,'1,10,13,20,25,29,31,32,33,35',NULL,7,30),(42,10,'8,9,11,12,23,27,40,56,57,60',NULL,7,30),(43,10,'7,8,21,25,28,33,34,52,56,57',NULL,7,30),(44,10,'2,5,19,25,27,31,32,40,54,60','S',7,30),(45,10,'2,9,15,16,19,20,23,43,48,60',NULL,7,30),(46,10,'4,5,9,16,19,29,35,41,44,49',NULL,7,30),(47,10,'4,7,14,18,28,29,32,33,40,55',NULL,7,30),(48,10,'6,10,12,19,20,23,26,37,41,60',NULL,7,30),(49,10,'10,11,13,19,24,25,26,39,41,43',NULL,7,30),(50,10,'4,7,11,13,18,29,40,47,49,59',NULL,7,30),(51,10,'1,4,6,7,15,27,28,48,53,59',NULL,7,30),(52,6,'5,7,10,20,43,56',NULL,8,30),(53,6,'10,23,25,32,44,54','S',8,30);
/*!40000 ALTER TABLE `usuario_jogos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario_sistema`
--

DROP TABLE IF EXISTS `usuario_sistema`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario_sistema` (
  `idusuario_sistema` int NOT NULL AUTO_INCREMENT COMMENT 'id gerado automaticamente',
  `nome_usuario` varchar(245) DEFAULT NULL COMMENT 'nome do usuario na base',
  `email_usuario` varchar(245) DEFAULT NULL COMMENT 'email do usuario na base não se repete',
  `senha_usuario` varchar(245) DEFAULT NULL COMMENT 'senha criptografada',
  `tipo_usuario` varchar(1) DEFAULT NULL COMMENT 'A=adm e F=funcionario',
  PRIMARY KEY (`idusuario_sistema`),
  UNIQUE KEY `email_usuario_UNIQUE` (`email_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb3 COMMENT='registra os usuários do sistema';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario_sistema`
--

LOCK TABLES `usuario_sistema` WRITE;
/*!40000 ALTER TABLE `usuario_sistema` DISABLE KEYS */;
INSERT INTO `usuario_sistema` VALUES (29,'murilo dark','murilo.dark@olirum.com.br','25d55ad283aa400af464c76d713c07ad',''),(30,'Teste 1','teste1@olirum.com.br','25d55ad283aa400af464c76d713c07ad','');
/*!40000 ALTER TABLE `usuario_sistema` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-08-05 21:07:42
