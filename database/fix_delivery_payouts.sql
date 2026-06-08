-- Fix: Create delivery_payouts table if not exists
CREATE TABLE IF NOT EXISTS `delivery_payouts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `rider_id` INT NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `status` ENUM('pending', 'processed', 'failed') DEFAULT 'pending',
    `requested_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `processed_at` TIMESTAMP NULL,
    FOREIGN KEY (`rider_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- Fix: Create delivery_reviews table if not exists (also missing from error logs)
CREATE TABLE IF NOT EXISTS `delivery_reviews` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `rider_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `rating` DECIMAL(2,1) NOT NULL DEFAULT 5.0,
    `comment` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`rider_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);
