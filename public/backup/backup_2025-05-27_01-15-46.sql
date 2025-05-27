/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.11-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: TiendaScripts
-- ------------------------------------------------------
-- Server version	10.11.11-MariaDB-0ubuntu0.24.04.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `pagos`
--

DROP TABLE IF EXISTS `pagos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pagos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `solicitud_id` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','pagado','fallido') NOT NULL DEFAULT 'pendiente',
  `fecha_pago` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `solicitud_id` (`solicitud_id`),
  CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pagos_ibfk_2` FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagos`
--

LOCK TABLES `pagos` WRITE;
/*!40000 ALTER TABLE `pagos` DISABLE KEYS */;
INSERT INTO `pagos` VALUES
(1,6,1,150.00,'pagado','2023-01-25 13:30:00'),
(2,7,2,200.00,'pagado','2023-02-28 18:20:00'),
(3,8,3,180.50,'pagado','2023-03-20 15:15:00'),
(4,9,4,220.75,'pagado','2023-04-15 20:45:00'),
(5,10,5,300.00,'pagado','2023-05-22 14:30:00'),
(6,6,6,175.00,'pagado','2023-06-25 17:10:00'),
(7,7,7,250.00,'pagado','2023-07-30 19:20:00'),
(8,8,8,190.00,'pendiente','2025-05-11 22:32:33'),
(9,9,9,210.00,'fallido','2023-08-05 16:30:00'),
(10,10,10,160.00,'fallido','2023-08-10 18:45:00');
/*!40000 ALTER TABLE `pagos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `respuestas`
--

DROP TABLE IF EXISTS `respuestas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `respuestas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `solicitud_id` int(11) NOT NULL,
  `desarrollador_id` int(11) NOT NULL,
  `mensaje` text NOT NULL,
  `archivo_script` varchar(255) DEFAULT NULL,
  `fecha_respuesta` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `solicitud_id` (`solicitud_id`),
  KEY `desarrollador_id` (`desarrollador_id`),
  CONSTRAINT `respuestas_ibfk_1` FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `respuestas_ibfk_2` FOREIGN KEY (`desarrollador_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `respuestas`
--

LOCK TABLES `respuestas` WRITE;
/*!40000 ALTER TABLE `respuestas` DISABLE KEYS */;
/*!40000 ALTER TABLE `respuestas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `solicitudes`
--

DROP TABLE IF EXISTS `solicitudes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `solicitudes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text NOT NULL,
  `estado` enum('pendiente','en proceso','completado','rechazado') NOT NULL DEFAULT 'pendiente',
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `solicitudes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `solicitudes`
--

LOCK TABLES `solicitudes` WRITE;
/*!40000 ALTER TABLE `solicitudes` DISABLE KEYS */;
INSERT INTO `solicitudes` VALUES
(1,6,'Script para análisis de datos','Necesito un script que procese archivos CSV y genere reportes estadísticos','completado','2023-01-15 14:30:00'),
(2,7,'Automatización de backups','Script que haga backup automático de mi base de datos MySQL','completado','2023-02-20 18:45:00'),
(3,8,'Web scraper para productos','Extraer información de precios de una página de e-commerce','completado','2023-03-10 13:15:00'),
(4,9,'Conversor de formatos','Convertir archivos XML a JSON con validación','completado','2023-04-05 20:20:00'),
(5,10,'Integración con API','Script para conectar con API de PayPal y registrar transacciones','completado','2023-05-12 15:10:00'),
(6,6,'Sistema de notificaciones','Enviar emails automáticos basados en eventos','en proceso','2023-06-18 17:25:00'),
(7,7,'Monitor de servidores','Script que monitoree recursos del servidor y alerte','en proceso','2023-07-22 19:30:00'),
(8,8,'Generador de facturas','Crear facturas en PDF a partir de datos en Excel','en proceso','2025-05-06 22:32:06'),
(9,9,'Migración de datos','Mover datos de SQLite a PostgreSQL con transformación','pendiente','2025-05-09 22:32:06'),
(10,10,'Analizador de logs','Procesar logs de Apache y generar métricas','pendiente','2025-05-10 22:32:06'),
(11,6,'Buscador de archivos','Script que encuentre archivos duplicados por contenido','pendiente','2025-05-11 22:32:06'),
(12,7,'Script de minería de cripto','Quiero un script para minar Bitcoin en segundo plano','rechazado','2023-01-10 12:40:00'),
(13,8,'Herramienta de hacking','Necesito un script para probar vulnerabilidades','rechazado','2023-03-15 21:50:00'),
(14,11,'Organizador de fotos','Script que organice fotos por fecha y evento','completado','2023-08-01 14:20:00'),
(15,12,'Buscador de ofertas','Comparador de precios entre tiendas online','en proceso','2023-08-10 18:35:00'),
(16,11,'Validación de formularios','Script para validar formularios web complejos','pendiente','2025-05-11 22:32:50'),
(17,12,'Extractor de metadatos','Extraer metadatos de archivos PDF y DOCX','pendiente','2025-05-11 22:32:50');
/*!40000 ALTER TABLE `solicitudes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol` enum('cliente','desarrollador','admin') NOT NULL DEFAULT 'cliente',
  `fecha_registro` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES
(6,'juan','juan@gmail.com','$2y$10$0fIlPt8tnIRzAwJTPIVgf.AUa3Cm/Nf79Vv/upaJy567YWb6ADbj6','admin','2025-05-11 17:14:00'),
(7,'caminante','caminante@gmail.com','$2y$10$uvNlvQ6CQVD.r8UuZqIIvOmyNhn68.mlMUPR0SDf5NWTJmLQXYDFm','desarrollador','2025-05-11 17:16:27'),
(8,'Admin Principal','admin@tiendascripts.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin','2025-05-11 22:31:48'),
(9,'Soporte Técnico','soporte@tiendascripts.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin','2025-05-11 22:31:48'),
(10,'Carlos Script','carlos@dev.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','desarrollador','2025-05-11 22:31:48'),
(11,'María Coder','maria@dev.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','desarrollador','2025-05-11 22:31:48'),
(12,'Pedro Programador','pedro@dev.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','desarrollador','2025-05-11 22:31:48'),
(13,'Juan Chambi','juan@cliente.com','$2y$10$NmFe0QlLW/6yqMsnk95o/ueRZd/UBQeyKrMndw7WmjVAV8CXMYDti','cliente','2025-05-11 22:31:51'),
(14,'Ana Gómez','ana@cliente.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','cliente','2025-05-11 22:31:51'),
(15,'Luis Martínez','luis@cliente.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','cliente','2025-05-11 22:31:51'),
(16,'Sofía Rodríguez','sofia@cliente.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','cliente','2025-05-11 22:31:51'),
(17,'Miguel Sánchez','miguel@cliente.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','cliente','2025-05-11 22:31:51'),
(18,'Laura García','laura@cliente.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','cliente','2025-05-11 22:32:46'),
(19,'Roberto López','roberto@cliente.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','cliente','2025-05-11 22:32:46'),
(20,'Elena Castro','elena@dev.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','desarrollador','2025-05-11 22:32:46');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-26 21:15:46
