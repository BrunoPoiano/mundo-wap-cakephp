SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for addresses
-- ----------------------------
DROP TABLE IF EXISTS `addresses`;
CREATE TABLE `addresses`
(
    `id`            int          NOT NULL AUTO_INCREMENT,
    `foreign_table` varchar(100) NOT NULL,
    `foreign_id`    int          NOT NULL,
    `postal_code`   varchar(8)   NOT NULL,
    `state`         varchar(2)   NOT NULL,
    `city`          varchar(200) NOT NULL,
    `sublocality`   varchar(200) NOT NULL,
    `street`        varchar(200) NOT NULL,
    `street_number` varchar(200) NOT NULL,
    `complement`    varchar(200) NOT NULL DEFAULT '',
    PRIMARY KEY (`id`),
    KEY             `foreign_table__foreign_id` (`foreign_table`,`foreign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Table structure for visits
-- ----------------------------
DROP TABLE IF EXISTS `visits`;
CREATE TABLE `visits`
(
    `id`        int  NOT NULL AUTO_INCREMENT,
    `date`      date NOT NULL,
    `completed` int  NOT NULL DEFAULT '0',
    `forms`     int  NOT NULL,
    `products`  int  NOT NULL,
    `duration`  int  NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY         `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Table structure for workdays
-- ----------------------------
DROP TABLE IF EXISTS `workdays`;
CREATE TABLE `workdays`
(
    `id`        int  NOT NULL AUTO_INCREMENT,
    `date`      date NOT NULL,
    `visits`    int  NOT NULL DEFAULT '0',
    `completed` int  NOT NULL DEFAULT '0',
    `duration`  int  NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY         `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

SET FOREIGN_KEY_CHECKS = 1;
