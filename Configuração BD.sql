CREATE DATABASE  IF NOT EXISTS `backlog` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `backlog`;
-- MySQL dump 10.13  Distrib 8.0.38, for Win64 (x86_64)
--
-- Host: localhost    Database: backlog
-- ------------------------------------------------------
-- Server version	8.3.0

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
-- Table structure for table `solicitacoes`
--

DROP TABLE IF EXISTS `solicitacoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `solicitacoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `categoria` enum('Melhoria','Erro','Suporte','Ajuste') NOT NULL,
  `descricao` text NOT NULL,
  `beneficios` text NOT NULL,
  `subticket` int NOT NULL,
  `status` enum('Em análise','Aceito','Em aprovação','Em desenvolvimento','Entregue','Recusado') DEFAULT 'Em análise',
  `anexos` text,
  `historico` text,
  `agencia` enum('1','10','11','15','16','20','25','30','35','40','45','50','55','60','65','70','71','75','80','85','90','95') NOT NULL,
  `complexidade` enum('1','2','3','4','5') DEFAULT '3',
  `relevancia` enum('1','2','3','4','5') DEFAULT '3',
  `impacto` enum('1','2','3','4','5') DEFAULT '3',
  `prazo_execucao` date DEFAULT NULL,
  `criado_por` varchar(100) NOT NULL,
  `criado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `solicitacoes`
--

LOCK TABLES `solicitacoes` WRITE;
/*!40000 ALTER TABLE `solicitacoes` DISABLE KEYS */;
INSERT INTO `solicitacoes` VALUES (1,'Erro','Erro ao salvar dados','Correção do salvamento',1,'Em análise',NULL,NULL,'10','3','4','5',NULL,'admin','2024-10-07 00:39:57'),(2,'Erro','Falha na autenticação','Acesso restaurado',2,'Em análise',NULL,NULL,'10','2','5','5',NULL,'admin','2024-10-07 00:39:57'),(3,'Erro','Sistema não responde','Disponibilidade do sistema',3,'Em análise',NULL,NULL,'10','4','5','5',NULL,'admin','2024-10-07 00:39:57'),(4,'Erro','Erro ao gerar relatório','Relatórios corretos',4,'Em análise',NULL,NULL,'10','3','3','4',NULL,'admin','2024-10-07 00:39:57'),(5,'Erro','Problema na conexão com o servidor','Estabilidade da conexão',5,'Em análise',NULL,NULL,'10','2','4','5',NULL,'admin','2024-10-07 00:39:57'),(6,'Erro','Erro ao carregar página','Página carregada corretamente',6,'Em análise',NULL,NULL,'10','3','3','3',NULL,'admin','2024-10-07 00:39:57'),(7,'Erro','Dados inconsistentes','Dados corretos',7,'Em análise',NULL,NULL,'10','2','4','4',NULL,'admin','2024-10-07 00:39:57'),(8,'Erro','Aplicativo fecha inesperadamente','Estabilidade do aplicativo',8,'Em análise',NULL,NULL,'10','4','5','5',NULL,'admin','2024-10-07 00:39:57'),(9,'Erro','Erro ao imprimir documentos','Impressão normalizada',9,'Em análise',NULL,NULL,'10','3','3','4',NULL,'admin','2024-10-07 00:39:57'),(10,'Erro','Página não encontrada','Navegação funcional',10,'Em análise',NULL,NULL,'10','2','2','3',NULL,'admin','2024-10-07 00:39:57'),(11,'Erro','Falha ao enviar e-mails','Notificações restabelecidas',11,'Em análise',NULL,NULL,'10','3','4','5',NULL,'admin','2024-10-07 00:39:57'),(12,'Erro','Erro de permissão de acesso','Permissões corrigidas',12,'Em análise',NULL,NULL,'10','2','3','4',NULL,'admin','2024-10-07 00:39:57'),(13,'Erro','Sistema lento','Desempenho otimizado',13,'Em análise',NULL,NULL,'10','4','4','5',NULL,'admin','2024-10-07 00:39:57'),(14,'Erro','Erro ao exportar dados','Exportação funcional',14,'Em análise',NULL,NULL,'10','3','3','4',NULL,'admin','2024-10-07 00:39:57'),(15,'Erro','Falha na integração com API','Integração restabelecida',15,'Em análise',NULL,NULL,'10','5','5','5',NULL,'admin','2024-10-07 00:39:57'),(16,'Melhoria','Adicionar filtro avançado','Busca mais precisa',16,'Aceito',NULL,NULL,'20','3','4','3',NULL,'tiago','2024-10-07 00:39:57'),(17,'Melhoria','Implementar dashboard','Visualização de dados',17,'Aceito',NULL,NULL,'20','4','5','4',NULL,'tiago','2024-10-07 00:39:57'),(18,'Melhoria','Melhorar desempenho do sistema','Sistema mais rápido',18,'Aceito',NULL,NULL,'20','3','5','5',NULL,'tiago','2024-10-07 00:39:57'),(19,'Melhoria','Atualizar interface gráfica','Melhor experiência do usuário',19,'Aceito',NULL,NULL,'20','2','4','3',NULL,'tiago','2024-10-07 00:39:57'),(20,'Melhoria','Implementar notificações push','Comunicação em tempo real',20,'Aceito',NULL,NULL,'20','4','5','4',NULL,'tiago','2024-10-07 00:39:57'),(21,'Melhoria','Adicionar suporte a múltiplos idiomas','Acessibilidade internacional',21,'Aceito',NULL,NULL,'20','5','5','5',NULL,'tiago','2024-10-07 00:39:57'),(22,'Melhoria','Integrar com API externa','Novas funcionalidades',22,'Aceito',NULL,NULL,'20','3','4','4',NULL,'tiago','2024-10-07 00:39:57'),(23,'Melhoria','Otimizar código fonte','Manutenção facilitada',23,'Aceito',NULL,NULL,'20','2','3','3',NULL,'tiago','2024-10-07 00:39:57'),(24,'Suporte','Ajuda com configuração inicial','Usuário orientado',24,'Em desenvolvimento',NULL,NULL,'30','1','2','1',NULL,'fulano','2024-10-07 00:39:57'),(25,'Suporte','Dúvida sobre funcionalidade X','Usuário esclarecido',25,'Em desenvolvimento',NULL,NULL,'30','1','2','1',NULL,'fulano','2024-10-07 00:39:57'),(26,'Suporte','Solicitação de treinamento','Equipe capacitada',26,'Em desenvolvimento',NULL,NULL,'30','2','3','2',NULL,'fulano','2024-10-07 00:39:57'),(27,'Suporte','Assistência na migração de dados','Transição suave',27,'Em desenvolvimento',NULL,NULL,'30','3','4','3',NULL,'fulano','2024-10-07 00:39:57'),(28,'Suporte','Dúvida sobre funcionalidade Y','Usuário esclarecido',28,'Em desenvolvimento',NULL,NULL,'30','2','2','2',NULL,'fulano','2024-10-07 00:39:57'),(29,'Ajuste','Ajuste no cálculo de impostos','Valores corretos',29,'Entregue',NULL,NULL,'40','2','3','2',NULL,'beltrano','2024-10-07 00:39:57'),(30,'Ajuste','Correção de textos em emails','Comunicação clara',30,'Entregue',NULL,NULL,'40','1','2','1',NULL,'beltrano','2024-10-07 00:39:57');
/*!40000 ALTER TABLE `solicitacoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario` varchar(50) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `agencia` enum('1','10','11','15','16','20','25','30','35','40','45','50','55','60','65','70','71','75','80','85','90','95') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'admin','$2y$10$GAMF35dvWP1jtutTvAJtUOK6LhqNjZVxvw4.m9Mb4/nWsQsEdA9W6','Administrador','10'),(2,'tiago','$2y$10$eVk.Srq1WnF6plZo8VQUauJVUivrLxzVokyPYEhbULS3FMukwGOOi','Tiago Buchi Cappellaro','20'),(3,'fulano','$2y$10$WMHqW7tZFIveqBmlZJVs5Ok23ffiHb6kZ0qEECnGCifOWMSpmkhMW','Fulano da Silva','30'),(4,'beltrano','$2y$10$7vv5kWv.ewp2BNiK3rGK8uvgSDp885on1.VTTxbwu00/tXxX17fw2','Beltrano de Oliveira','40');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'backlog'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-10-06 21:41:34
