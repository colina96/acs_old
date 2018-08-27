-- MySQL dump 10.13  Distrib 5.7.21, for macos10.13 (x86_64)
--
-- Host: localhost    Database: acs
-- ------------------------------------------------------
-- Server version	5.7.21

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `COMPONENT`
--

DROP TABLE IF EXISTS `COMPONENT`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `COMPONENT` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(50) DEFAULT NULL,
  `prep_type_id` int(11) DEFAULT NULL,
  `M1_check_id` int(11) DEFAULT NULL,
  `M1_temp` int(11) DEFAULT NULL,
  `M1_time` datetime DEFAULT NULL,
  `M1_chef_id` int(11) DEFAULT NULL,
  `M1_action_code` int(11) DEFAULT NULL,
  `M2_check_id` int(11) DEFAULT NULL,
  `M2_temp` int(11) DEFAULT NULL,
  `M2_time` datetime DEFAULT NULL,
  `M2_chef_id` int(11) DEFAULT NULL,
  `M2_action_code` int(11) DEFAULT NULL,
  `M3_check_id` int(11) DEFAULT NULL,
  `M3_temp` int(11) DEFAULT NULL,
  `M3_time` datetime DEFAULT NULL,
  `M3_chef_id` int(11) DEFAULT NULL,
  `M3_action_code` int(11) DEFAULT NULL,
  `finished` datetime DEFAULT NULL,
  `shelf_life_days` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `COMPONENT`
--

LOCK TABLES `COMPONENT` WRITE;
/*!40000 ALTER TABLE `COMPONENT` DISABLE KEYS */;
INSERT INTO `COMPONENT` VALUES (2,'CHASSEUR SAUCE',2,1,88,'2018-08-02 17:09:29',3,NULL,1,18,'2018-08-03 16:08:53',3,NULL,1,18,'2018-08-06 17:57:01',0,NULL,NULL,NULL),(3,'RICH TOMATO SAUCE',2,1,77,'2018-08-03 16:42:16',3,NULL,1,18,'2018-08-06 17:39:43',0,NULL,1,18,'2018-08-06 17:39:51',0,NULL,NULL,NULL),(4,'CHASSEUR SAUCE',2,1,89,'2018-08-04 08:44:40',3,NULL,1,20,'2018-08-06 17:37:05',0,NULL,1,2,'2018-08-06 17:39:30',0,NULL,NULL,NULL),(5,'BEEF MASSAMAN CURRY (240G) WHICH INCLUDES:-',2,1,88,'2018-08-06 16:44:27',3,NULL,1,12,'2018-08-06 17:28:35',0,NULL,1,2,'2018-08-06 17:29:59',0,NULL,NULL,NULL),(6,'RICH TOMATO SAUCE',2,1,85,'2018-08-06 17:58:22',3,NULL,1,20,'2018-08-06 17:58:43',0,NULL,1,2,'2018-08-06 17:59:08',0,NULL,NULL,NULL),(7,'RICH TOMATO SAUCE',2,1,88,'2018-08-07 16:37:29',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(8,'RICH TOMATO SAUCE',2,1,88,'2018-08-08 18:13:45',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(9,'RICH TOMATO SAUCE',2,1,88,'2018-08-08 18:18:43',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(10,'KOREAN BEEF TARTARE MIX',1,1,77,'2018-08-08 18:21:36',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `COMPONENT` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `CORRECTIVE_ACTIONS`
--

DROP TABLE IF EXISTS `CORRECTIVE_ACTIONS`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CORRECTIVE_ACTIONS` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `prep_type` smallint(6) NOT NULL,
  `action_text` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `CORRECTIVE_ACTIONS`
--

LOCK TABLES `CORRECTIVE_ACTIONS` WRITE;
/*!40000 ALTER TABLE `CORRECTIVE_ACTIONS` DISABLE KEYS */;
INSERT INTO `CORRECTIVE_ACTIONS` VALUES (1,1,'Evacuate Blast Chiller, Hard Chill'),(2,1,'Decant Product into Shallow Metal - Continue Chilling'),(3,2,'Add more ice to ice bath'),(4,3,'Evacuate Blast Chiller, Hard Chill'),(5,3,'Decant Product into Shallow Metal - Continue Chilling'),(6,5,'Refrigerate Product'),(7,5,'Discard Product'),(8,5,'Retrain Staff'),(9,5,'Low Risk Item, QA Sign-Off');
/*!40000 ALTER TABLE `CORRECTIVE_ACTIONS` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `MENUS`
--

DROP TABLE IF EXISTS `MENUS`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MENUS` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  `comment` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `MENUS`
--

LOCK TABLES `MENUS` WRITE;
/*!40000 ALTER TABLE `MENUS` DISABLE KEYS */;
INSERT INTO `MENUS` VALUES (1,'2018-08-01 09:12:27','2018-08-01 09:12:27','INTERNATIONAL','35A','N/A'),(2,'2018-08-01 00:00:00','2018-08-31 00:00:00','Wednesday','Comment','35A');
/*!40000 ALTER TABLE `MENUS` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `MENU_ITEMS`
--

DROP TABLE IF EXISTS `MENU_ITEMS`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MENU_ITEMS` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL,
  `code` varchar(20) DEFAULT NULL,
  `dish_name` varchar(100) DEFAULT NULL,
  `plating_team` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `MENU_ITEMS`
--

LOCK TABLES `MENU_ITEMS` WRITE;
/*!40000 ALTER TABLE `MENU_ITEMS` DISABLE KEYS */;
INSERT INTO `MENU_ITEMS` VALUES (1,1,'F0601408','Duck Rillettes, Apple Beetroot Jelly',NULL),(2,1,'F4489315','Vanilla Sauce',NULL),(3,2,'F0700159','CHEESE CALENDAR ROLL & ARNOTTS BISCUIT',1),(4,2,'F0700393','CHEESE CALENDAR ROLL & ARNOTTS BISCUIT',1),(5,2,'F0300633','MARINATED OLIVES',1),(6,2,'F0402689','JAPANESE RICE PARCEL',1),(7,2,'F0400147','Scrambled Egg Mixture 400ml',NULL),(8,2,'F0400533','Organic Scrambled Egg Mixture 400ml',NULL),(9,2,'F1200988','Bread Toast Granary 2P81',NULL),(10,2,'F1201086','Bread Brioche Loaf Slice for Eggs Tstd',NULL),(11,2,'F1400205','Lemon wedges 2P72/2P76',NULL),(12,2,'F1400825','Lemon Cheeks supplied in 2P32',NULL),(13,2,'F1400740','Dressing Palm Sugar Halal 100ml 2P81',NULL),(14,2,'F1400995','Palm Sugar 400ml',NULL),(15,2,'F0402983','BIRCHER MUESLI NEIL PERRY',NULL),(16,2,'F0403237','BIRCHER MUESLI NEIL PERRY 2U846 787-900',NULL),(17,2,'F1400001','LEMON HALVED SLICED X 8 2P72',NULL),(18,2,'F0100385','VOL AUVENT CREME FRAICHE FOR CAVIAR',NULL),(19,2,'F0100386','KOREAN BEEF TARTARE CHIVES SESAME DRESSING',2),(20,2,'F0300637','SALAD OF BABY COS, RADICCHIO & FRISEE 100G BAG',2),(21,2,'F1401992','CORIANDER YOGHURT DUKKAH CROUTON FOR F0200634',2),(22,2,'F0303902','BAKED RIGATONI EGGPLANT RICH TOMATO SAUCE',NULL),(23,2,'F0303922','BAKED RIGATONI EGGPLANT RICH TOMATO SAUCE',NULL),(24,2,'F1401990','MARINATED ZUCCHINI SALAD FOR F0303902',3),(25,2,'F0303900','BURRATA ROAST PEPPER RADICCHIO SOURDOUGH',3),(26,2,'F0200633','ROAST CHICKEN BROTH FARRO ASPARAGUS ENOKI',6),(27,2,'F1401987','PUMPKIN TORTELLINI GARNISH FOR F0200633',NULL),(28,2,'F1401988','PARSLEY CHIFFONADE GARNISH FOR F0200633',NULL),(29,2,'F0303903','TUNA POKE CORIANDER SESAME DRESSING',NULL),(30,2,'F0303920','TUNA POKE CORIANDER 2U801',NULL),(31,2,'F0303921','TUNA POKE CORIANDER 2U840',NULL),(32,2,'F1401991','DRESSING SESAME AND RICE VINEGAR FOR F0303920 / F0303921',NULL),(33,2,'F0505925','BEEF MASSAMAN CURRY JASMINE RICE',NULL),(34,2,'F1401997','BLANCHED GAI LAN FOR F0505925',NULL),(35,2,'F1402023','CRISPY ESCHALOTS THAI BASIL FOR F0505925',NULL),(36,2,'F0601176','ROASTED EGGPLANT EGGPLANT NOODLES',NULL),(37,2,'F1401397','HOKKIEN NOODLES 90G 2P32',3),(38,2,'F0505923','ROAST CHICKEN CHASSEUR RISONI BEANS PEAS',4),(39,2,'F1401995','BRAISED SILVERBEET FOR F0505923',NULL),(40,2,'F0505926','RPBG LAMB RACK BEANS GREEN',NULL),(41,2,'F1401999','MAC AND CHEESE 2U846 FOR F0505926',NULL),(42,2,'F1402000','GARNISH HARISSA FOR F0505926',NULL),(43,2,'F0505924','SEAR CONE BAY BARRAMUNDI HERB POTATO BROCCOLINI',NULL),(44,2,'F0505956','SEAR CONE BAY BARRAMUNDI STEAMED HERB POTATO',NULL),(45,2,'F1402020','ALMOND SALSA FOR F0505956 F0505924',NULL),(46,2,'F1402019','LEMON CHEEK FOR F0505924',NULL),(47,2,'F1401980','BLANCHED BROCCOLINI',NULL),(48,2,'F0505922','STEAMED BLUE EYE BROTH GINGER SOY BROWN RICE',NULL),(49,2,'F0506065','SEARED FLATHEAD FILLET GINGER SOY JASMINE RICE',NULL),(50,2,'F1402001','BOK CHOY BLANCHED FOR F0505922 F0506065',NULL),(51,2,'F0505957','SEARED CHICKEN BREAST GINGER SOY JASMINE RICE',NULL),(52,2,'F1402008','GARNISH GINGER SHALLOT RELISH FOR F0505957',NULL),(53,2,'F1401768','GARNISH BOK CHOY 2P20 FOR F0505957',NULL),(54,2,'F0303904','SALAD ANCIENT GRAIN HERB BROCCOLINI 2U802 - QF9',NULL),(55,2,'F1402011','HARISSA DRESSING FOR CHICK ANCIENT GRAINS',NULL),(56,2,'F0600302','STEAK SANDWICH',NULL),(57,2,'F0601725','MILK BUNS WITH MUSTARD FOR SMOKED SALMON ROLL',NULL),(58,2,'F1401996','FENNEL SLAW SMOKE SALMON FOR F0601725',NULL),(59,2,'F0600873','CHICKEN SCHNITZEL',NULL),(60,2,'F1101484','CHOCOLATE OLIVE OIL CAKE COCOA CREAM RASPBERRIES',NULL),(61,2,'F1101635','BAKED VANILLA CUSTARD 2U846',NULL),(62,2,'F1101419','BAKED VANILLA CUSTARD 2U828',NULL),(63,2,'F1401277','GARNISH RHUBARB PEAR COMPOTE FLAKED ALMONDS',NULL),(64,2,'F0700386','CHEESE PLATE 2U841 QF 25',NULL),(65,2,'F0403234','BRIOCHE BACON EGG ROAST TOMATO BBQ SAUCE',NULL),(66,2,'F0403229','POACHED EGG HALOUMI KALE QUINOA SALAD',NULL),(67,2,'F1402012','DRESSING GREEN TAHINI FOR F0403229',NULL),(68,2,'F1402092','PEPITAS SUNFLOWER SESAME PISTACHIO FOR F0403229',NULL),(69,2,'F0403153','PORK SAUSAGE SPINACH BRAISED BEANS',NULL),(70,2,'F1402015','PORK SAUSAGE ROAST TOM FOR CAFƒ BREAKFAST',NULL),(71,2,'F1401608','SAUTƒED SPINACH',NULL),(72,2,'F1401609','SLOW ROAST TOMATOES',NULL),(73,2,'F1401739','GRILL BACON BLACKFOREST FOR CAFƒ BREAKFAST 2P21',NULL),(74,2,'F1401610','BRAISED BEANS',NULL),(75,2,'F1402006','HASH BROWNS FOR CAFƒ BREAKFAST',NULL),(76,2,'F0403295','CRUMPET HONEY BUTTER FOR CAFE BREAKFAST',NULL),(77,2,'F1401576','BANANA RIPE 2NO. FOR CAFƒ BREAKFAST',NULL),(78,2,'F1401574','CHOPPED HAZELNUT',NULL),(79,2,'F1401575','STRAWBERRY HALVES',NULL),(80,2,'F1401577','MAPLE SYRUP',NULL),(81,2,'F1401620','SLICED APPLE SYRUP',NULL),(82,2,'F1401573','BLUEBERRY FRESH 2P32 FOR CAFE BREAKFAST',NULL),(83,2,'F0601726','BEEF UDON NOODLE SOUP',NULL),(84,2,'F1401998','SHALLOT TOGRASHI FOR F0601726',NULL),(85,2,'F0601727','EGG WHITE FRIED RICE WITH FISH LAP CHEONG SNAKE BEANS',NULL),(86,2,'F1402002','BOK CHOY STEAMED FOR F0601727',NULL),(87,2,'F0601730','EGG WHITE FRIED RICE LAP CHEONG, SNAKE BEANS  PTA',NULL),(88,2,'F1401800','GARNISH SHALLOT ROUNDS FOR F0601730 F0601727',NULL),(89,2,'F0601661','LAMB PILAF',NULL),(90,2,'F0601752','LAMB PILAF QF43',NULL),(91,2,'F1401993','TABBOULEH FOR F0601661 F0601752',NULL),(92,2,'F1402017','LEMON YOGHURT FOR F0601661 F0601752',NULL),(93,2,'F0601785','LAMB PILAF 160g',NULL),(94,2,'F1402134','TABBOULEH FOR F0601785',NULL),(95,2,'F1401904','GARNISH LEMON YOGHURT FOR F0601785',NULL),(96,2,'F0601728','LAMB AND WHITE BEAN CASSOULET POT PIE',NULL),(97,2,'F0601751','LAMB AND WHITE BEAN CASSOULET POT PIE 2U846',NULL),(98,2,'F1402003','HERB BREADCRUMB FOR F0601728 F0601751',NULL),(99,2,'F0601519','SPINACH, RICOTTA AND ONION PASTIZZI WITH TOMATO RELISH',NULL),(100,2,'F0601805','SAUSAGE ROLL MESCLUN TOMATO RELISH',NULL),(101,2,'F0601791','CROISSANT PUDDING SALSA CORN',NULL),(102,2,'F0304025','MORTON B  Y BUG MUSHROOM LEEK H  ZELNUT B  LS',NULL),(103,2,'F1402218','GARNISH LEMON MINT FOR WELCOME WATER',NULL);
/*!40000 ALTER TABLE `MENU_ITEMS` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `MENU_ITEM_COMPONENTS`
--

DROP TABLE IF EXISTS `MENU_ITEM_COMPONENTS`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MENU_ITEM_COMPONENTS` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(100) DEFAULT NULL,
  `prep_type` int(11) DEFAULT NULL,
  `probe_type` int(11) DEFAULT NULL,
  `location` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=185 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `MENU_ITEM_COMPONENTS`
--

LOCK TABLES `MENU_ITEM_COMPONENTS` WRITE;
/*!40000 ALTER TABLE `MENU_ITEM_COMPONENTS` DISABLE KEYS */;
INSERT INTO `MENU_ITEM_COMPONENTS` VALUES (1,'Duck Rillettes',1,1,NULL),(2,'Apple Beetroot Jelly',1,2,NULL),(3,'Vanilla Sauce',2,0,NULL),(4,'ITEM DESCRIPTION',NULL,NULL,NULL),(5,'CHEESE CHEDDAR (BOTTEGA ROTOLO)',5,NULL,NULL),(6,'BREAD ROLL',NULL,NULL,NULL),(7,'WATERCRACKER BISCUIT (2PC.)',NULL,NULL,NULL),(8,'THYME AND ROSEMARY',NULL,NULL,NULL),(9,'JAPANESE RICE PARCEL WITH BLACK SESAME SEED, 120g ea',5,NULL,NULL),(10,'EA VOL AU VENT PASTRY SHELLS APPROX.3.5CM',NULL,NULL,NULL),(11,'CRéME FRAICHE',NULL,NULL,NULL),(12,'PLASTIC TEASPOONS',NULL,NULL,NULL),(13,'KOREAN BEEF TARTARE MIX',1,NULL,NULL),(14,'CHIVE BATONS',NULL,NULL,NULL),(15,'SESAME RADISH DRESSING',NULL,NULL,NULL),(16,'BABY COS LETTUCE LEAVEAS (TORN)',NULL,NULL,NULL),(17,'BABY RADICCHIO LEAVES (TORN)',NULL,NULL,NULL),(18,'FRISEE LEAVES',NULL,NULL,NULL),(19,'CORIANDER YOGHURT',NULL,NULL,NULL),(20,'DUKKAH CROUTONS 1.5CM x 1.5CM rough cut',NULL,NULL,NULL),(21,'BAKED RIGATONI(1EA = 110G)',NULL,NULL,NULL),(22,'RICH TOMATO SAUCE',2,NULL,NULL),(23,'RICH TOMATO SAUCE (30g/2P35)',2,NULL,NULL),(24,'MARINATED ZUCCHINI',NULL,NULL,NULL),(25,'BURRATA',NULL,NULL,NULL),(26,'ROASTED PEPPER MIX',NULL,NULL,NULL),(27,'LIGURIAN OLIVE',NULL,NULL,NULL),(28,'GRILLED SHALLOT',NULL,NULL,NULL),(29,'RADICHHIO LEAF TORN',NULL,NULL,NULL),(30,'SALAD HERB TARRAGON DILL FRISEE',NULL,NULL,NULL),(31,'CHARRED GRILLED SOURDOUGH CUT IN HALF',NULL,NULL,NULL),(32,'ROAST CHICKEN BROTH',NULL,NULL,NULL),(33,'COOKED FARRO',NULL,NULL,NULL),(34,'BLANCH ASPARAGUS (1CM SLICE, TIPS SPLIT LENGTHWAYS)',NULL,NULL,NULL),(35,'STEAMED ENOKI MUSHROOMS',NULL,NULL,NULL),(36,'PUMPKIN TORTELLINI (1 EA = 20G)',NULL,NULL,NULL),(37,'CABBAGE LEAF',NULL,NULL,NULL),(38,'PARSLEY CHIFFONADE',NULL,NULL,NULL),(39,'SEARED TUNA (10MM X 10MM CUBE)',NULL,NULL,NULL),(40,'SALAD CABBAGE DAIKON CARROT MIX',NULL,NULL,NULL),(41,'WAKAME SEAWEED SALAD',NULL,NULL,NULL),(42,'CORIANDER LEAVES',NULL,NULL,NULL),(43,'SESAME AND RICE VINEGAR DRESSING',NULL,NULL,NULL),(44,'SEARED TUNA CUBE',1,NULL,NULL),(45,'SALAD CABBAGE DIAKON CARROT MIX',NULL,NULL,NULL),(46,'WAKAME SEAWEED',NULL,NULL,NULL),(47,'DRESSING SESAME AND RICE VINEGAR',NULL,NULL,NULL),(48,'BEEF MASSAMAN CURRY (240G) WHICH INCLUDES:-',2,NULL,NULL),(49,'BEEF',NULL,NULL,NULL),(50,'POTATOES',NULL,NULL,NULL),(51,'MASSAMAN CURRY SAUCE',NULL,NULL,NULL),(52,'JASMINE RICE',NULL,NULL,NULL),(53,'BLANCHED CABBAGE LEAF',NULL,NULL,NULL),(54,'GAILAN',NULL,NULL,NULL),(55,'CRISPY ESCHALOTS',NULL,NULL,NULL),(56,'THAI BASIL',NULL,NULL,NULL),(57,'ROASTED EGGPLANT 1.5cmx 1.5xm x7cm',NULL,NULL,NULL),(58,'NOODLE SAUCE',NULL,NULL,NULL),(59,'CORIANDER FRESH PICKED',NULL,NULL,NULL),(60,'CHILLI RED JULIENNE',NULL,NULL,NULL),(61,'HOKKIEN NOODLES',NULL,NULL,NULL),(62,'BOK CHOY',NULL,NULL,NULL),(63,'ROAST CHICKEN BREAST SK/ON (1 EA = 120G CW)',NULL,NULL,NULL),(64,'CHASSEUR SAUCE',2,NULL,NULL),(65,'COOKED BUTTON MUSHROOMS',NULL,NULL,NULL),(66,'SLOW ROASTED CHERRY TOMATOES',NULL,NULL,NULL),(67,'TARRAGON LEAVES',NULL,NULL,NULL),(68,'RISONI AND GREEN BEAN MIX',NULL,NULL,NULL),(69,'CABBAGE LEAF BLANCHED',NULL,NULL,NULL),(70,'BRAISED SILVERBEET',NULL,NULL,NULL),(71,'LAMB RACK ROAST 2 BONES',NULL,NULL,NULL),(72,'GREEN BEANS',NULL,NULL,NULL),(73,'MAC AND CHEESE (1EA = 165G)',NULL,NULL,NULL),(74,'HARISSA PASTE',NULL,NULL,NULL),(75,'SEARED CONE BAY BARRAMUNDI FILLET - CW',NULL,NULL,NULL),(76,'STEAMED HERB CHAT POTATOES',NULL,NULL,NULL),(77,'BROCCOLINI',NULL,NULL,NULL),(78,'PARSLEY, OLIVE AND ALMOND SALSA',NULL,NULL,NULL),(79,'LEMON CHEEK',NULL,NULL,NULL),(80,'BLANCHED BROCCOLINI',NULL,NULL,NULL),(81,'STEAMED BLUE EYE CW',NULL,NULL,NULL),(82,'GINGER AND SOY BROTH',NULL,NULL,NULL),(83,'GINGER JULIENNE',NULL,NULL,NULL),(84,'SHALLOT JULIENNE',NULL,NULL,NULL),(85,'BROWN RICE',NULL,NULL,NULL),(86,'SEARED FLATHEAD FISH',NULL,NULL,NULL),(87,'STEAMED JASMINE RICE',NULL,NULL,NULL),(88,'BLANCHED BOK CHOY',NULL,NULL,NULL),(89,'SEARED GINGER AND SHALLOT CHICKEN SKIN ON, BONELESS',NULL,NULL,NULL),(90,'BLANCHED CABBAGE',NULL,NULL,NULL),(91,'GINGER AND SHALLOT RELISH',NULL,NULL,NULL),(92,'BLANCH BOK CHOY QUARTER',NULL,NULL,NULL),(93,'POACHED CHICKEN BREAST (APPROX. 5MM)',NULL,NULL,NULL),(94,'ANCIENT GRAIN, EDAMAME AND HERB SALAD',NULL,NULL,NULL),(95,'BROCOLLINI BLANCH',NULL,NULL,NULL),(96,'HARISSA DRESSING',NULL,NULL,NULL),(97,'1 X 15CM LENGTH SOURDOUGH BAGUETTE',NULL,NULL,NULL),(98,'40GM ea RW GRILL MARKED MINUTE STEAK',NULL,NULL,NULL),(99,'FRESH BABY ROCKET LEAVES',NULL,NULL,NULL),(100,'TOMATO AND CHILLI RELISH',NULL,NULL,NULL),(101,'MILK BUN 10CM CUT IN HALF FILLED WITH',NULL,NULL,NULL),(102,'MUSTARD SPREAD',NULL,NULL,NULL),(103,'SMOKED SALMON',NULL,NULL,NULL),(104,'WHITE SLAW',NULL,NULL,NULL),(105,'13CM X 8CM LENGTH SCHIACCIATA/FOCCACCIA SLICED HORIZONTALLY',NULL,NULL,NULL),(106,'17G SLICES EMMENTHAL CHEESE',NULL,NULL,NULL),(107,'CHICKEN THIGH SCHNITZEL 120G CW',NULL,NULL,NULL),(108,'SALAD COLESLAW',NULL,NULL,NULL),(109,'CHOCOLATE CAKE SLICE 10 x 4CM(APPROX. 50G/SLICE)',NULL,NULL,NULL),(110,'COCOA CRéME',NULL,NULL,NULL),(111,'CRUNCHY MIX',NULL,NULL,NULL),(112,'RASPBERRIES',NULL,NULL,NULL),(113,'BAKED VANILLA CUSTARD',NULL,NULL,NULL),(114,'HONEY PEAR AND RHUBARB MIX',NULL,NULL,NULL),(115,'HALAL ROASTED ALMOND FLAKES',NULL,NULL,NULL),(116,'SOFT CHEESE',NULL,NULL,NULL),(117,'HARD CHEESE',NULL,NULL,NULL),(118,'BLUE/GOATS CHEESE',NULL,NULL,NULL),(119,'QUINCE PASTE',NULL,NULL,NULL),(120,'MEDJOOL DATE',NULL,NULL,NULL),(121,'BUNCH RED GRAPES',NULL,NULL,NULL),(122,'BRIOCHE ROLL CUT IN HALF',NULL,NULL,NULL),(123,'SEALED BACON RASHERS',NULL,NULL,NULL),(124,'FRIED EGG',NULL,NULL,NULL),(125,'CHOPPED SLOW ROASTED TOMATOES, 2CM CHUNKS',NULL,NULL,NULL),(126,'BARBECUE SAUCE',NULL,NULL,NULL),(127,'QUINOA AND KALE MIXTURE',NULL,NULL,NULL),(128,'CABBAGE LEAVES',NULL,NULL,NULL),(129,'HALOUMI',NULL,NULL,NULL),(130,'POACHED EGG',NULL,NULL,NULL),(131,'WATER',NULL,NULL,NULL),(132,'GREEN TAHINI',NULL,NULL,NULL),(133,'SEEDS AND PISTACHIO',NULL,NULL,NULL),(134,'WHITE SESAME SEEDS',NULL,NULL,NULL),(135,'BLACK  SESAME SEEDS',NULL,NULL,NULL),(136,'PORK CHIPOLATA SAUSAGES, PAN FRIED 30g/CW',NULL,NULL,NULL),(137,'BRAISED BEANS',NULL,NULL,NULL),(138,'SAUTEED ENGLISH SPINACH',NULL,NULL,NULL),(139,'PAN FRIED PORK  SAUSAGE (30G/EA x 2)',NULL,NULL,NULL),(140,'SAUTƒED SPINACH',NULL,NULL,NULL),(141,'SLOW ROAST TOMATOES (25G/EA)',NULL,NULL,NULL),(142,'BACON SEARED RINDLESS BLACKFOREST',NULL,NULL,NULL),(143,'HASH BROWNS (40G/EA)',NULL,NULL,NULL),(144,'TOASTED CRUMPET(approx. 40g)',NULL,NULL,NULL),(145,'HONEY BUTTER DISC (10g/EA)',NULL,NULL,NULL),(146,'RIPE BANANA',NULL,NULL,NULL),(147,'CHOPPED HAZELNUT',NULL,NULL,NULL),(148,'STRWBERRY HALF CUT STEM OFF',NULL,NULL,NULL),(149,'MAPLE SYRUP',NULL,NULL,NULL),(150,'SLICED GREEN APPLES',NULL,NULL,NULL),(151,'SYRUP',NULL,NULL,NULL),(152,'Fresh Blueberries',NULL,NULL,NULL),(153,'UDON NOODLE',NULL,NULL,NULL),(154,'DASHI BROTH',NULL,NULL,NULL),(155,'SOY SEARED BEEF',NULL,NULL,NULL),(156,'SHALLOTS ROUND',NULL,NULL,NULL),(157,'SHICHIMI TOGARASHI',NULL,NULL,NULL),(158,'FRIED RICE',NULL,NULL,NULL),(159,'STIR FRY BEANS LAPCHEONG',NULL,NULL,NULL),(160,'PACKING SAUCE',NULL,NULL,NULL),(161,'LAP CHEONG AND BEANS',NULL,NULL,NULL),(162,'RICE LAMB PILAF',NULL,NULL,NULL),(163,'LAMB PILAF',NULL,NULL,NULL),(164,'TABBOULEH',NULL,NULL,NULL),(165,'LEMON YOGHURT',NULL,NULL,NULL),(166,'LAMB CW',NULL,NULL,NULL),(167,'SAUCE',NULL,NULL,NULL),(168,'WHITE BEAN CASSOULET',NULL,NULL,NULL),(169,'BREAD CRUMB PARSLEY MIXTURE',NULL,NULL,NULL),(170,'PASTIZZI (APPROX. 35G/EA)',NULL,NULL,NULL),(171,'SAUSAGE ROLL (APPROX.155G/EA)',NULL,NULL,NULL),(172,'MESCLUN LETTUCE',NULL,NULL,NULL),(173,'TOMATO RELISH',NULL,NULL,NULL),(174,'WHOLE CHERRY TOMATO',NULL,NULL,NULL),(175,'CROISSANT PUDDING (APPROX. 140g/EA)',NULL,NULL,NULL),(176,'CORN SALSA JALAPENO',NULL,NULL,NULL),(177,'POACHED MORETON BUGS',NULL,NULL,NULL),(178,'BROWN BUTTER AND BALSAMIC DRESSING',NULL,NULL,NULL),(179,'SAUTƒED MUSHROOMS AND LEEKS',NULL,NULL,NULL),(180,'PEELED ROAST CHOP HAZELNUTS',NULL,NULL,NULL),(181,'PARSNIP PUREE',NULL,NULL,NULL),(182,'FINE CHOPPED CHIVE',NULL,NULL,NULL),(183,'SALAD LEAVES WITLOF FRISEE PARSLEY CHERVIL',NULL,NULL,NULL),(184,'LEMON HALF SLICES 5MM',NULL,NULL,NULL);
/*!40000 ALTER TABLE `MENU_ITEM_COMPONENTS` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `MENU_ITEM_LINK`
--

DROP TABLE IF EXISTS `MENU_ITEM_LINK`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MENU_ITEM_LINK` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `menu_item_id` int(11) DEFAULT NULL,
  `component_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=227 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `MENU_ITEM_LINK`
--

LOCK TABLES `MENU_ITEM_LINK` WRITE;
/*!40000 ALTER TABLE `MENU_ITEM_LINK` DISABLE KEYS */;
INSERT INTO `MENU_ITEM_LINK` VALUES (1,1,1),(2,1,2),(3,2,3),(4,-1,4),(5,3,5),(6,3,6),(7,3,7),(8,4,5),(9,4,6),(10,4,7),(11,5,8),(12,6,9),(13,18,10),(14,18,11),(15,18,12),(16,19,13),(17,19,14),(18,19,15),(19,19,4),(20,20,16),(21,20,17),(22,20,18),(23,21,19),(24,21,20),(25,22,21),(26,22,22),(27,23,21),(28,23,23),(29,24,24),(30,25,25),(31,25,26),(32,25,27),(33,25,28),(34,25,29),(35,25,30),(36,25,31),(37,26,32),(38,26,33),(39,26,34),(40,26,35),(41,27,36),(42,27,37),(43,28,38),(44,29,39),(45,29,40),(46,29,41),(47,29,42),(48,29,43),(49,30,44),(50,30,45),(51,30,46),(52,31,44),(53,31,45),(54,31,46),(55,32,47),(56,33,48),(57,33,49),(58,33,50),(59,33,51),(60,33,52),(61,33,53),(62,34,54),(63,35,55),(64,35,56),(65,36,57),(66,36,58),(67,36,59),(68,36,60),(69,37,61),(70,37,62),(71,37,37),(72,38,63),(73,38,64),(74,38,65),(75,38,66),(76,38,67),(77,38,68),(78,38,69),(79,39,70),(80,39,4),(81,40,71),(82,40,72),(83,41,73),(84,42,74),(85,43,75),(86,43,76),(87,43,77),(88,44,75),(89,44,76),(90,45,78),(91,46,79),(92,47,80),(93,48,81),(94,48,82),(95,48,83),(96,48,84),(97,48,85),(98,48,53),(99,49,86),(100,49,82),(101,49,87),(102,49,53),(103,50,88),(104,51,89),(105,51,82),(106,51,52),(107,51,90),(108,52,91),(109,53,92),(110,54,93),(111,54,94),(112,54,95),(113,55,96),(114,55,4),(115,56,97),(116,56,98),(117,56,99),(118,56,100),(119,57,101),(120,57,102),(121,58,103),(122,58,104),(123,59,105),(124,59,106),(125,59,107),(126,59,108),(127,60,109),(128,60,110),(129,60,111),(130,60,112),(131,61,113),(132,62,113),(133,63,114),(134,63,115),(135,63,4),(136,64,116),(137,64,117),(138,64,118),(139,64,119),(140,64,120),(141,64,121),(142,65,122),(143,65,123),(144,65,124),(145,65,125),(146,65,126),(147,66,127),(148,66,128),(149,66,129),(150,66,130),(151,66,131),(152,67,132),(153,68,133),(154,68,134),(155,68,135),(156,69,136),(157,69,137),(158,69,138),(159,70,139),(160,71,140),(161,72,141),(162,73,142),(163,74,137),(164,75,143),(165,76,144),(166,76,145),(167,77,146),(168,78,147),(169,79,148),(170,79,4),(171,80,149),(172,81,150),(173,81,151),(174,82,152),(175,83,153),(176,83,62),(177,83,53),(178,83,154),(179,83,155),(180,84,156),(181,84,157),(182,85,158),(183,85,53),(184,85,159),(185,85,160),(186,86,62),(187,87,158),(188,87,53),(189,87,161),(190,87,160),(191,87,4),(192,88,156),(193,88,160),(194,89,162),(195,89,53),(196,90,163),(197,90,37),(198,91,164),(199,92,165),(200,93,163),(201,93,37),(202,94,164),(203,95,165),(204,96,166),(205,96,167),(206,96,168),(207,97,166),(208,97,167),(209,97,168),(210,98,169),(211,99,170),(212,99,100),(213,100,171),(214,100,172),(215,100,173),(216,100,174),(217,101,175),(218,101,176),(219,102,177),(220,102,178),(221,102,179),(222,102,180),(223,102,181),(224,102,182),(225,102,183),(226,103,184);
/*!40000 ALTER TABLE `MENU_ITEM_LINK` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `PREP_TYPES`
--

DROP TABLE IF EXISTS `PREP_TYPES`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PREP_TYPES` (
  `id` smallint(5) unsigned NOT NULL,
  `code` varchar(4) NOT NULL,
  `days_offset` int(11) DEFAULT NULL,
  `M1_temp` int(11) DEFAULT NULL,
  `M1_temp_above` tinyint(4) DEFAULT NULL,
  `M2_time_minutes` int(11) DEFAULT NULL,
  `M2_alarm_min` int(11) DEFAULT NULL,
  `M2_temp` int(11) DEFAULT NULL,
  `M2_temp_above` tinyint(4) DEFAULT NULL,
  `M3_time_minutes` int(11) DEFAULT NULL,
  `M3_alarm_min` int(11) DEFAULT NULL,
  `M3_temp` int(11) DEFAULT NULL,
  `M3_temp_above` tinyint(4) DEFAULT NULL,
  `shelf_life_days` int(11) DEFAULT NULL,
  `probe_type` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `PREP_TYPES`
--

LOCK TABLES `PREP_TYPES` WRITE;
/*!40000 ALTER TABLE `PREP_TYPES` DISABLE KEYS */;
INSERT INTO `PREP_TYPES` VALUES (1,'CC',3,75,1,120,20,21,0,360,60,5,0,6,0),(2,'HF',21,80,1,120,20,21,0,360,60,5,0,28,0),(3,'ESL',21,75,1,120,20,21,0,360,60,5,0,90,0),(4,'LR',3,NULL,1,NULL,0,0,0,NULL,NULL,NULL,0,6,0),(5,'AHR',3,5,0,45,20,15,0,NULL,NULL,NULL,0,6,0);
/*!40000 ALTER TABLE `PREP_TYPES` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `USERS`
--

DROP TABLE IF EXISTS `USERS`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `USERS` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `firstname` varchar(20) NOT NULL,
  `lastname` varchar(20) NOT NULL,
  `function` varchar(20) DEFAULT NULL,
  `admin` int(11) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `USERS`
--

LOCK TABLES `USERS` WRITE;
/*!40000 ALTER TABLE `USERS` DISABLE KEYS */;
INSERT INTO `USERS` VALUES (1,'col','acs','Colin','Atkinson','admin',1,'2018-08-08 18:26:44'),(2,'david@qamc.co','acs','David','Cox','admin',1,NULL),(3,'chef1@gmail.com','acs','Chef','One','chef',0,NULL),(4,'chef2@gmail.com','acs','Chef','Two','chef',0,NULL),(5,'chef3@gmail.com','acs','Bob the','Chef','chef',0,NULL),(6,'chef4@gmail.com','acs','Chef','Four','chef',0,NULL),(7,'chef5@gmail.com','acs','Chef','Five','chef',0,NULL),(8,'chef6@gmail.com','acs','Chef','Six','chef',0,NULL),(9,'chef7@gmail.com','acs','Chef','Seven','chef',0,NULL),(10,'chef8@gmail.com','acs','Chef','Eight','chef',0,NULL),(11,'chef9@gmail.com','acs','Chef','Nine','chef',0,NULL),(12,'chef10@gmail.com','acs','Chef','10','chef',0,NULL);
/*!40000 ALTER TABLE `USERS` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plating_team_member`
--

DROP TABLE IF EXISTS `plating_team_member`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plating_team_member` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` smallint(5) unsigned NOT NULL,
  `team_id` smallint(5) unsigned NOT NULL,
  `time_added` datetime DEFAULT NULL,
  `time_removed` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2395 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plating_team_member`
--

LOCK TABLES `plating_team_member` WRITE;
/*!40000 ALTER TABLE `plating_team_member` DISABLE KEYS */;
INSERT INTO `plating_team_member` VALUES (2392,1,1,'2018-08-09 19:21:31',NULL),(2393,5,1,'2018-08-09 19:22:53',NULL),(2394,2,2,'2018-08-09 19:23:11',NULL);
/*!40000 ALTER TABLE `plating_team_member` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-08-10  9:21:46
