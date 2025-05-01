-- Notifications table
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `post_id` int(11) DEFAULT NULL,
  `comment_id` int(11) DEFAULT NULL,
  `message` text,
  `link` varchar(255) DEFAULT NULL,
  `read_status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `sender_id` (`sender_id`),
  KEY `post_id` (`post_id`),
  KEY `comment_id` (`comment_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `notifications_ibfk_3` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notifications_ibfk_4` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Post subscriptions table (for users who want to be notified about post activity)
CREATE TABLE IF NOT EXISTS `post_subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_post_unique` (`user_id`,`post_id`),
  KEY `post_id` (`post_id`),
  CONSTRAINT `post_subscriptions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `post_subscriptions_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Auto-subscribe post authors to their own posts
DELIMITER //
CREATE TRIGGER IF NOT EXISTS `auto_subscribe_author` AFTER INSERT ON `posts`
FOR EACH ROW
BEGIN
    INSERT IGNORE INTO `post_subscriptions` (`user_id`, `post_id`, `created_at`)
    VALUES (NEW.author_id, NEW.id, NOW());
END//
DELIMITER ; 