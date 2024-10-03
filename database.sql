-- MySQL dump 10.13  Distrib 8.0.39, for Linux (x86_64)
--
-- Host: localhost    Database: mini_social
-- ------------------------------------------------------
-- Server version	8.0.39-0ubuntu0.22.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`),
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES (2,1,1,'Et ceci est son commentaire pour le test XoXo ...','2024-10-02 06:08:29'),(3,1,4,'test de commentaire avec un autre compte utilisateur ;)','2024-10-02 06:09:21'),(4,1,1,'Un autre compte pour mettre plus de commentaire','2024-10-02 06:10:40'),(5,1,2,'commentaire de tahirihasina','2024-10-02 07:24:04'),(6,1,1,'2 ème commentaire','2024-10-02 14:02:47'),(7,1,1,'commentaire test sur l\'affichage dynamique avec ajax','2024-10-02 20:05:28'),(8,2,2,'commentaire d\'une autre compte sur le test de l\'affichage dynamique...','2024-10-02 20:13:07'),(9,3,4,'waouuuuh XD :)','2024-10-03 03:39:13');
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (1,1,'Ceci est la publication créer par Sahyan :)','2024-10-02 06:07:47'),(2,1,'publication 3','2024-10-02 20:07:29'),(3,2,'Publication de tahirihasina ','2024-10-02 20:14:54');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reactions`
--

DROP TABLE IF EXISTS `reactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reactions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `type` enum('like','love','wow','sad','angry') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `comment_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `reactions_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`),
  CONSTRAINT `reactions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reactions`
--

LOCK TABLES `reactions` WRITE;
/*!40000 ALTER TABLE `reactions` DISABLE KEYS */;
INSERT INTO `reactions` VALUES (1,1,1,'wow','2024-10-02 06:07:52',NULL),(2,NULL,1,'love','2024-10-02 06:08:33',2),(3,1,4,'love','2024-10-02 06:09:26',NULL),(4,NULL,4,'like','2024-10-02 06:09:39',3),(5,NULL,1,'like','2024-10-02 06:10:12',3),(6,NULL,1,'like','2024-10-02 06:10:44',4),(7,1,2,'like','2024-10-02 07:22:02',NULL),(8,NULL,2,'love','2024-10-02 12:07:54',3),(9,NULL,2,'love','2024-10-02 12:09:54',5),(10,NULL,1,'sad','2024-10-02 14:01:40',5),(11,NULL,1,'love','2024-10-02 20:08:20',7),(12,NULL,1,'like','2024-10-02 20:08:34',6),(13,2,1,'like','2024-10-02 20:09:12',NULL),(14,2,2,'like','2024-10-02 20:12:32',NULL),(15,NULL,2,'sad','2024-10-02 20:13:48',2),(16,NULL,2,'like','2024-10-02 20:28:02',8),(17,3,4,'like','2024-10-03 03:38:27',NULL),(18,2,4,'love','2024-10-03 03:39:30',NULL),(19,NULL,4,'wow','2024-10-03 03:39:38',8),(20,3,1,'wow','2024-10-03 05:12:05',NULL),(21,NULL,1,'wow','2024-10-03 05:12:16',9);
/*!40000 ALTER TABLE `reactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'sahyan@gmail.com','$2y$10$.V2Ahc/1G1Nha2LBVeor/.OA4fsp1uBQFOVgQTlx44dRwx6nQpoGm','2024-10-02 06:05:47'),(2,'tahirihasinarakotomanga@gmail.com','$2y$10$UrrSRpRGLhvnK.7J3AnoBOEEkgIH8OAnXQonQfi8YlchVkquvUQy.','2024-10-02 06:06:02'),(3,'toandro@gmail.com','$2y$10$n7pNb2r9nGy1A9TJW7V.d.xzH9bx9nux1gyAsnWpx1whlMjAotMym','2024-10-02 06:06:17'),(4,'hrakoto@gmail.com','$2y$10$3BMBrSFVrLIsyrBmxbM9cui9ud1ZzO2PAErQGWZIvNSKOoJVrGKVy','2024-10-02 06:06:49');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-10-03 15:16:28
