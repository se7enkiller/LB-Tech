CREATE TABLE orders (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        reference INT UNSIGNED UNIQUE NOT NULL,
                        status_id INT UNSIGNED DEFAULT 12,
                        tracker_id VARCHAR(255)
);
