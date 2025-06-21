-- Create database
CREATE DATABASE IF NOT EXISTS movie_booking;
USE movie_booking;

-- Create movies table
CREATE TABLE IF NOT EXISTS movies (
    movie_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    duration INT,
    release_date DATE,
    poster_url VARCHAR(255),
    status ENUM('now_showing', 'coming_soon') DEFAULT 'coming_soon'
);

-- Create video_banner table
CREATE TABLE IF NOT EXISTS video_banner (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    video_url VARCHAR(255) NOT NULL,
    active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample movies
INSERT INTO movies (title, description, duration, release_date, poster_url, status) VALUES 
('Inception', 'A mind-bending thriller about dreams within dreams', 148, '2024-01-01', 'inception.jpg', 'now_showing'),
('The Matrix', 'A sci-fi masterpiece about reality versus simulation', 136, '2024-01-15', 'matrix.jpg', 'now_showing'),
('Avatar 3', 'Return to Pandora in this epic adventure', 162, '2024-06-01', 'avatar3.jpg', 'coming_soon');