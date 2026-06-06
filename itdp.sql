-- MySQL dump 10.13  Distrib 8.4.9, for Linux (aarch64)
--
-- Host: localhost    Database: itdp
-- ------------------------------------------------------
-- Server version	8.4.9

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
-- Table structure for table `blog_posts`
--

DROP TABLE IF EXISTS `blog_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blog_posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text NOT NULL,
  `content` text NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'published',
  `card_image` varchar(255) NOT NULL,
  `hero_image` varchar(255) NOT NULL,
  `published_at` varchar(255) NOT NULL,
  `created_at` varchar(255) NOT NULL,
  `updated_at` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_posts`
--

LOCK TABLES `blog_posts` WRITE;
/*!40000 ALTER TABLE `blog_posts` DISABLE KEYS */;
INSERT INTO `blog_posts` VALUES (1,'Study Choice','study-choice','Why I chose ICT at HZ, the activities that influenced me, and what I hope to achieve after completing my studies.','<p>When I was considering my future studies, I wanted a program that would let me combine creativity, problem-solving, and technology. ICT stood out to me because it is practical, future-oriented, and full of opportunities worldwide. I enjoy turning ideas into real projects, and ICT gives me the tools to do exactly that. HZ University of Applied Sciences attracted me because of its focus on hands-on learning and personal growth. After completing my studies, I want to continue developing as a software engineer and possibly start my own projects in areas like AI, web development, or even SaaS businesses.</p>','published','/img/scl.jpg','/img/studyChoice.png','2025-05-09','2026-06-06 08:36:01','2026-06-06 08:36:01'),(2,'Personal SWOT Analysis','personal-swot-analysis','My strengths, weaknesses, opportunities, and challenges as a future ICT student, and how I plan to grow from them.','<h3>Strengths (S)</h3>\n      <ul>\n          <li>Started programming young (Python, C++, web dev) and built small projects like Snake and websites.</li>\n          <li>Self-motivated learner, comfortable with YouTube, online courses, and documentation.</li>\n          <li>Creative and good at problem-solving, able to turn ideas into working code.</li>\n          <li>Strong interest in cars and technology, which keeps me inspired.</li>\n          <li>Hyperfocus moments (ADHD) allow me to code and learn intensively for many hours.</li>\n      </ul>\n\n      <h3>Weaknesses (W)</h3>\n      <ul>\n          <li>Sometimes lose focus or motivation when tasks feel repetitive or boring.</li>\n          <li>Still developing teamwork and communication skills in an academic/professional setting.</li>\n          <li>Limited formal experience with large projects or advanced programming concepts.</li>\n          <li>Can overwork during hyperfocus and forget about balance.</li>\n      </ul>\n\n      <h3>Opportunities (O)</h3>\n      <ul>\n          <li>Studying ICT at HZ provides hands-on practice and real-world projects.</li>\n          <li>The Netherlands offers strong job opportunities and a great tech ecosystem.</li>\n          <li>Access to international peers and teachers to learn from.</li>\n          <li>Chance to expand skills in areas like AI, software engineering, and SaaS business ideas.</li>\n          <li>GitHub portfolio and projects can grow into a career showcase.</li>\n      </ul>\n\n      <h3>Threats (T)</h3>\n      <ul>\n          <li>High competition in the ICT field, need to keep improving to stand out.</li>\n          <li>Fast-changing technology requires constant learning and adaptation.</li>\n          <li>Risk of procrastination or burnout if time management is not handled well.</li>\n          <li>Language and cultural adaptation as an international student.</li>\n      </ul>','published','/img/swotl.jpg','/img/swot.png','2025-05-09','2026-06-06 08:36:01','2026-06-06 08:36:01'),(3,'Programming Experience','programming-experience','A look at how I started coding, the languages I explored, and the lessons I learned from self-study and projects.','<p>My programming journey started at around 9 years old. I first tried Python and created a small calculator by following YouTube tutorials. Later, I studied C++ with a tutor and even managed to create a small Snake game. After a break, I returned to coding through web development with HTML, CSS, and JavaScript, which I still practice today. More recently, I began exploring React, Firebase, and C#, and I plan to keep building projects that push me further as a developer.</p>','published','/img/pel.webp','/img/programingxp.png','2025-05-09','2026-06-06 08:36:01','2026-06-06 08:36:01'),(4,'First Feedback','first-feedback','The first feedback I received at HZ, how I reacted to it, and how it shaped my approach to learning ICT.','<h3>First feedback</h3>\n\n      <table class=\"blog-feedback-table\">\n          <thead>\n              <tr>\n                  <th>Feedback</th>\n                  <th>Type</th>\n                  <th>Positive</th>\n                  <th>Negative</th>\n              </tr>\n          </thead>\n          <tbody>\n              <tr>\n                  <td>1</td>\n                  <td>Sticky note from TA</td>\n                  <td>Style consistency, comments, CSS, fun fade.</td>\n                  <td>Blog pictures and posts to blog pages.</td>\n              </tr>\n              <tr>\n                  <td>2</td>\n                  <td>Sticky note from TA</td>\n                  <td>Scrolling animation, indexing, folder structure.</td>\n                  <td>Navbar in posts pages.</td>\n              </tr>\n              <tr>\n                  <td>3</td>\n                  <td>Evaluation of pitch and website</td>\n                  <td>Code well organized, fun animation, looks professional.</td>\n                  <td>No major negative feedback.</td>\n              </tr>\n              <tr>\n                  <td>4</td>\n                  <td>Evaluation of pitch and website</td>\n                  <td>Positive evaluation points received for the website and pitch.</td>\n                  <td>No major negative feedback.</td>\n              </tr>\n              <tr>\n                  <td>5</td>\n                  <td>Evaluation of pitch and website</td>\n                  <td>Positive evaluation points received for the website and pitch.</td>\n                  <td>No major negative feedback.</td>\n              </tr>\n          </tbody>\n      </table>\n\n      <h2>Progress</h2>\n            <h3>Evaluation of pitch and website recived - 3/3</h3>\n            <div class=\"progress-container\">\n                <div class=\"progress-bar\" style=\"width: 100%;\">3</div>\n            </div>\n            <h3>Evaluation of pitch and website given - 3/3</h3>\n            <div class=\"progress-container\">\n                <div class=\"progress-bar\" style=\"width: 100%;\">3</div>\n            </div>\n            </div>','published','/img/feedback.jpg','/img/feedback.webp','2025-05-09','2026-06-06 08:36:01','2026-06-06 08:36:01'),(5,'ICT Field of Work','ict-field-of-work','An overview of today’s ICT field, the exciting opportunities it offers, and why I see my future career in it.','<p>The ICT field is one of the fastest-growing in the world. With AI, cloud computing, and cybersecurity shaping the future, ICT professionals are in demand more than ever. According to recent reports, roles such as software engineers, AI specialists, and cloud architects are among the most sought-after. For students like me, this means that learning ICT is not only exciting but also a safe investment in the future. ICT professionals don’t just code; they solve global problems, innovate in industries, and shape how society uses technology.</p>','published','/img/ictl.jpg','/img/ictwork.jpg','2025-05-09','2026-06-06 08:36:01','2026-06-06 08:36:01'),(7,'Test mysql','test-post','test for mysql to check if everything works as suposed to','<p>here you can some pictures of planes </br> on like thumbnail its a SU-57 very cool and agile </br> and on post image its fastes plane as i know in category of ultra light planes which is JMB VL3 with rotax 916i engine. </p>','published','/uploads/blog/blog_6a23e9e4176802.82349965.jpg','/uploads/blog/blog_6a23e9e4178945.78207858.png','2026-06-06','2026-06-06 09:35:32','2026-06-06 09:35:32');
/*!40000 ALTER TABLE `blog_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profile_sections`
--

DROP TABLE IF EXISTS `profile_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profile_sections` (
  `id` int NOT NULL AUTO_INCREMENT,
  `section_key` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `updated_at` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `section_key` (`section_key`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profile_sections`
--

LOCK TABLES `profile_sections` WRITE;
/*!40000 ALTER TABLE `profile_sections` DISABLE KEYS */;
INSERT INTO `profile_sections` VALUES (1,'about_me','About me','My name is Volodymyr, I am 17 years old and originally from Ukraine. I became curious about programming when I was around 9 years old. I started with Python by following tutorials and building a simple calculator, later exploring C++ where I even created a small Snake game. After a few breaks and restarts, I returned to coding through web development with HTML, CSS, and JavaScript, and since then I have been learning independently through online courses and projects.\n\n                                                                           Beyond technology, I am also a car enthusiast who loves driving and learning about vehicles. My favorite movie series is Fast & Furious, and I enjoy the show Silicon Valley for its humor and insights into the tech world. I chose to study ICT because it combines my love for creativity, problem-solving, and technology, and I am excited to continue building my skills at HZ University of Applied Sciences.','2026-06-06 08:36:01'),(2,'programming_skills','Programming','HTML (Good)\n                                                                           CSS (Good)\n                                                                           C# (Beginner)\n                                                                           C++ (Pre Beginner)','2026-06-06 08:36:01'),(3,'languages','Languages','Ukrainian (Native)\n                                                                           Russian (C2)\n                                                                           Polish (C1)\n                                                                           English (B2)','2026-06-06 08:36:01');
/*!40000 ALTER TABLE `profile_sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `projects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `long_description` text NOT NULL,
  `tech_json` text NOT NULL,
  `status` varchar(255) NOT NULL,
  `completed_at` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `sort_order` int NOT NULL,
  `created_at` varchar(255) NOT NULL,
  `updated_at` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES (1,'Portfolio Website','A responsive personal portfolio website showcasing my projects, skills, blog, profile, and study progress.','A responsive personal portfolio website with dynamic content management, admin-only editing, blog publishing workflow, profile sections, and a study dashboard.','[\"PHP\", \"Twig\", \"SQLite\", \"JavaScript\"]','Completed','2025-04-18','Web Development','/uploads/blog/blog_6a1d5d359a3779.75266393.png','/profile',1,'2026-06-06 08:36:01','2026-06-06 08:36:01'),(2,'Blog System','A blog publishing workflow with create, edit, delete, draft and published status, and image uploads.','A complete blog management system where an admin can create posts, upload images, edit content, switch between draft and published status, and delete posts safely.','[\"PHP\", \"Twig\", \"SQLite\", \"CSRF\", \"File Uploads\"]','Completed','2025-05-02','CMS Feature','/uploads/blog/blog_6a1d5d359a3779.75266393.png','/blog',2,'2026-06-06 08:36:01','2026-06-06 08:36:01'),(3,'Study Dashboard','A dashboard that stores assessments in the database and calculates earned credits based on grades.','A study progress dashboard that stores assessment data in SQLite, allows grade editing, and automatically calculates earned study credits and NBSA progress.','[\"PHP\", \"Twig\", \"SQLite\", \"PHPUnit\", \"JavaScript\"]','Completed','2025-05-24','Study Progress','/uploads/blog/blog_6a1d5d359a3779.75266393.png','/dashboard',3,'2026-06-06 08:36:01','2026-06-06 08:36:01'),(4,'Authentication System','A secure login system with password hashing, authorization middleware, and CSRF protection.','A secure authentication and authorization system that supports login, logout, password hashing, role-based access control, admin-only routes, and CSRF protection for POST requests.','[\"PHP\", \"SQLite\", \"Password Hashing\", \"Middleware\", \"CSRF\"]','Completed','2025-05-26','Security','/uploads/blog/blog_6a1d5d359a3779.75266393.png','/login',4,'2026-06-06 08:36:01','2026-06-06 08:36:01'),(5,'Editable Profile','A profile page where professional biography, programming skills, and languages can be managed.','A database-driven profile page that allows an administrator to update professional biographical information, programming skills, and language knowledge through a protected edit form.','[\"PHP\", \"Twig\", \"SQLite\", \"Admin Authorization\"]','Completed','2025-05-28','Content Management','/uploads/blog/blog_6a1d5d359a3779.75266393.png','/profile',5,'2026-06-06 08:36:01','2026-06-06 08:36:01'),(6,'3D Laptop Project Showcase','An interactive Three.js laptop that allows visitors to explore projects through a virtual laptop screen.','An innovative interactive project showcase built with Three.js. Visitors can rotate the laptop, enter fullscreen mode, open and close the screen, and browse project data loaded dynamically from a RESTful API.','[\"Three.js\", \"JavaScript\", \"WebGL\", \"REST API\", \"Canvas Texture\"]','Completed','2026-06-02','Innovation','/uploads/blog/blog_6a1d5d359a3779.75266393.png','/projects-3d',6,'2026-06-06 08:36:01','2026-06-06 08:36:01');
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schema_migrations`
--

DROP TABLE IF EXISTS `schema_migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `schema_migrations` (
  `filename` varchar(255) NOT NULL,
  `executed_at` varchar(255) NOT NULL,
  PRIMARY KEY (`filename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schema_migrations`
--

LOCK TABLES `schema_migrations` WRITE;
/*!40000 ALTER TABLE `schema_migrations` DISABLE KEYS */;
INSERT INTO `schema_migrations` VALUES ('001_initial.sql','2026-06-06 08:53:27');
/*!40000 ALTER TABLE `schema_migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `study_assessments`
--

DROP TABLE IF EXISTS `study_assessments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `study_assessments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `quartile` varchar(255) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `course_code` varchar(255) NOT NULL,
  `exam_name` varchar(255) NOT NULL,
  `exam_date` varchar(255) NOT NULL,
  `earnable_credits` decimal(5,2) NOT NULL,
  `grade` decimal(5,2) DEFAULT NULL,
  `sort_order` int NOT NULL,
  `created_at` varchar(255) NOT NULL,
  `updated_at` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `study_assessments`
--

LOCK TABLES `study_assessments` WRITE;
/*!40000 ALTER TABLE `study_assessments` DISABLE KEYS */;
INSERT INTO `study_assessments` VALUES (1,'S1 Q1','PCO','CU75001V3','First Exam Opportunity','03-10-2025',2.50,9.30,1,'2026-06-06 08:36:01','2026-06-06 09:47:20'),(2,'S1 Q1','PBA','CU75003V1','Casustoets PBA','30-10-2025',5.00,9.70,2,'2026-06-06 08:36:01','2026-06-06 09:47:20'),(3,'S1 Q1','CSB','CU75002V1','Schriftelijke Kennistoets','31-10-2025',5.00,7.30,3,'2026-06-06 08:36:01','2026-06-06 09:47:20'),(4,'S1 Q2','OOP','CU75004V1','Presentation','Week S1.15',5.00,7.50,4,'2026-06-06 08:36:01','2026-06-06 09:47:20'),(5,'S1 Q2','OOP','CU75004V1','Written Knowledge Test','Week S1.20',5.00,9.60,5,'2026-06-06 08:36:01','2026-06-06 09:47:20'),(6,'S2 Q3','Framework Project 1','CU75080V3','On-Site Case Study Exam','Week S2.8',5.00,9.20,6,'2026-06-06 08:36:01','2026-06-06 09:47:20'),(7,'S2 Q3','Framework Project 1','CU75080V3','Presentation (Group)','Week S2.8',5.00,5.50,7,'2026-06-06 08:36:01','2026-06-06 09:47:20'),(8,'S2 Q4','Framework Project 2','CU75011V4','Presentation (Group)','Week S2.18',5.00,NULL,9,'2026-06-06 08:36:01','2026-06-06 09:47:20'),(9,'S2 Q4','Framework Project 2','CU75011V4','Portfolio (Individual Project Assessment)','Week S2.15 - S2.20',5.00,NULL,10,'2026-06-06 08:36:01','2026-06-06 09:47:20'),(10,'S2 Q4','ITP','CU75011V4','IT Personality','Week S2.18',2.50,NULL,11,'2026-06-06 08:36:01','2026-06-06 09:47:20'),(11,'S2 Q4','BUB','CU75081V1','Video (Individual Assignment)','Week S2.19',2.50,NULL,12,'2026-06-06 08:36:01','2026-06-06 09:47:20'),(12,'S2 Q4','PPD-E','CU75068V3','Criterium Focused Interview','Week S2.11 - S1.8 (Y2)',12.50,6.00,13,'2026-06-06 08:36:01','2026-06-06 09:47:20');
/*!40000 ALTER TABLE `study_assessments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `role` varchar(255) DEFAULT 'user',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'vova','$2a$12$GyZ821AVxFY9.eXgDUl7YO4SunTQPFBj.4Zl2j.FtPtB.rGc6TNKq','Volodymyr','admin');
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

-- Dump completed on 2026-06-06 16:21:05
