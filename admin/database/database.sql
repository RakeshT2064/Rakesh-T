CREATE DATABASE movie_booking;
USE movie_booking;

CREATE TABLE movies (
    movie_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    duration INT,
    release_date DATE,
    poster_url VARCHAR(255),
    status ENUM('now_showing', 'coming_soon') DEFAULT 'coming_soon'
);

CREATE TABLE theaters (
    theater_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    seats_capacity INT NOT NULL
);

CREATE TABLE showtimes (
    showtime_id INT PRIMARY KEY AUTO_INCREMENT,
    movie_id INT,
    theater_id INT,
    show_date DATE,
    show_time TIME,
    price DECIMAL(10,2),
    FOREIGN KEY (movie_id) REFERENCES movies(movie_id),
    FOREIGN KEY (theater_id) REFERENCES theaters(theater_id)
);

CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    full_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE bookings (
    booking_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    showtime_id INT,
    seats_booked INT,
    total_amount DECIMAL(10,2),
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (showtime_id) REFERENCES showtimes(showtime_id)
);

ALTER TABLE bookings ADD COLUMN seat_numbers VARCHAR(255) AFTER seats_booked;

ALTER TABLE bookings 
ADD COLUMN payment_method VARCHAR(50) AFTER total_amount,
ADD COLUMN booking_date DATETIME DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE bookings ADD COLUMN payment_method VARCHAR(50) AFTER total_amount;

ALTER TABLE bookings 
ADD COLUMN transaction_id VARCHAR(50) AFTER payment_status,
ADD COLUMN payment_method VARCHAR(20) NOT NULL DEFAULT 'upi',
ADD COLUMN payment_status VARCHAR(20) NOT NULL DEFAULT 'pending';

ALTER TABLE bookings 
ADD COLUMN payment_status VARCHAR(20) NOT NULL DEFAULT 'pending',
ADD COLUMN payment_method VARCHAR(20) NOT NULL,
ADD COLUMN transaction_id VARCHAR(50) NULL,
ADD COLUMN booking_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE theaters
ADD COLUMN seats_per_row INT NOT NULL DEFAULT 12,
ADD COLUMN number_of_rows INT NOT NULL DEFAULT 10;

ALTER TABLE movies
ADD COLUMN duration INT,
ADD COLUMN genre VARCHAR(100),
ADD COLUMN language VARCHAR(50),
ADD COLUMN rating VARCHAR(10);

ALTER TABLE theaters
ADD COLUMN seats_per_row INT DEFAULT 12,
ADD COLUMN number_of_rows INT DEFAULT 10;

CREATE TABLE IF NOT EXISTS movies (
    movie_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    duration INT NOT NULL,
    release_date DATE NOT NULL,
    poster VARCHAR(255) NOT NULL,
    genre VARCHAR(50) NOT NULL,
    language VARCHAR(50) NOT NULL,
    rating VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE movies
ADD COLUMN poster VARCHAR(255) NOT NULL,
ADD COLUMN genre VARCHAR(50) NOT NULL,
ADD COLUMN language VARCHAR(50) NOT NULL,
ADD COLUMN rating VARCHAR(10) NOT NULL,
ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

CREATE TABLE video_banner (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    video_url VARCHAR(255) NOT NULL,
    active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE bookings 
ADD COLUMN payment_id VARCHAR(255) AFTER total_amount,
ADD COLUMN payment_status VARCHAR(50) DEFAULT 'pending';

ALTER TABLE bookings 
ADD COLUMN transaction_id VARCHAR(50),
ADD COLUMN payment_status VARCHAR(20) DEFAULT 'pending',
ADD COLUMN num_tickets INT NOT NULL;

ALTER TABLE bookings ADD COLUMN seat_numbers VARCHAR(255) NOT NULL;

CREATE TABLE IF NOT EXISTS bookings (
    booking_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    showtime_id INT NOT NULL,
    seat_numbers VARCHAR(255) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    transaction_id VARCHAR(50),
    payment_status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (showtime_id) REFERENCES showtimes(showtime_id)
);

CREATE TABLE IF NOT EXISTS genres (
    genre_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS movie_genres (
    movie_id INT,
    genre_id INT,
    PRIMARY KEY (movie_id, genre_id),
    FOREIGN KEY (movie_id) REFERENCES movies(movie_id),
    FOREIGN KEY (genre_id) REFERENCES genres(genre_id)
);

ALTER TABLE movies
ADD COLUMN language VARCHAR(50) AFTER description,
ADD COLUMN movie_type ENUM('2D', '3D') DEFAULT '2D' AFTER language;

CREATE TABLE contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('unread', 'read') DEFAULT 'unread'
);

ALTER TABLE theaters ADD COLUMN city VARCHAR(100) AFTER name;

CREATE TABLE IF NOT EXISTS languages (
    language_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE
);

INSERT INTO languages (name) VALUES 
('English'),
('Hindi'),
('Tamil'),
('Telugu'),
('Malayalam'),
('Kannada');

CREATE TABLE genres (
    genre_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE
);

INSERT INTO genres (name) VALUES 
('Action'),
('Adventure'),
('Animation'),
('Comedy'),
('Crime'),
('Documentary'),
('Drama'),
('Family'),
('Fantasy'),
('Horror'),
('Mystery'),
('Romance'),
('Sci-Fi'),
('Thriller'),
('War'),
('Western'),
('Musical'),
('Biography'),
('Sport'),
('Superhero');

ALTER TABLE movies
ADD COLUMN language_id INT NOT NULL DEFAULT 1,
ADD COLUMN movie_type VARCHAR(10) NOT NULL DEFAULT '2D',
ADD FOREIGN KEY (language_id) REFERENCES languages(language_id);

CREATE TABLE languages (
    language_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE
);

INSERT INTO languages (name) VALUES 
('English'),
('Hindi'),
('Tamil'),
('Telugu'),
('Malayalam'),
('Kannada'),
('Chinese'),
('Japanese'),
('Korean'),
('Spanish');

-- First, drop existing foreign key constraints if they exist
ALTER TABLE bookings
DROP FOREIGN KEY IF EXISTS bookings_ibfk_1;

ALTER TABLE showtimes
DROP FOREIGN KEY IF EXISTS showtimes_ibfk_1;

ALTER TABLE movie_genres
DROP FOREIGN KEY IF EXISTS movie_genres_ibfk_1;

-- Add movie_id column to tables if missing
ALTER TABLE bookings
ADD COLUMN IF NOT EXISTS movie_id INT,
ADD FOREIGN KEY (movie_id) REFERENCES movies(movie_id) ON DELETE CASCADE;

ALTER TABLE showtimes
ADD COLUMN IF NOT EXISTS movie_id INT,
ADD FOREIGN KEY (movie_id) REFERENCES movies(movie_id) ON DELETE CASCADE;

ALTER TABLE movie_genres
ADD COLUMN IF NOT EXISTS movie_id INT,
ADD FOREIGN KEY (movie_id) REFERENCES movies(movie_id) ON DELETE CASCADE;

-- Create genres table
CREATE TABLE IF NOT EXISTS genres (
    genre_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- Create movie_genres junction table
CREATE TABLE IF NOT EXISTS movie_genres (
    movie_id INT,
    genre_id INT,
    PRIMARY KEY (movie_id, genre_id),
    FOREIGN KEY (movie_id) REFERENCES movies(movie_id) ON DELETE CASCADE,
    FOREIGN KEY (genre_id) REFERENCES genres(genre_id) ON DELETE CASCADE
);

-- Insert some default genres
INSERT INTO genres (name) VALUES 
('Action'),
('Comedy'),
('Drama'),
('Horror'),
('Romance'),
('Sci-Fi'),
('Thriller'),
('Adventure'),
('Animation'),
('Documentary');


-- Create database if not exists
CREATE TABLE IF NOT EXISTS movies (
    movie_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    duration INT,
    release_date DATE,
    poster_url VARCHAR(255),
    status ENUM('now_showing', 'coming_soon') DEFAULT 'coming_soon'
);

INSERT INTO movies (title, description, duration, release_date, poster_url, status) VALUES 
('Inception', 'A mind-bending thriller about dreams within dreams', 148, '2024-01-01', 'inception.jpg', 'now_showing'),
('The Matrix', 'A sci-fi masterpiece about reality versus simulation', 136, '2024-01-15', 'matrix.jpg', 'now_showing'),
('Avatar 3', 'Return to Pandora in this epic adventure', 162, '2024-06-01', 'avatar3.jpg', 'coming_soon');

-- Add cancellation status to bookings table
ALTER TABLE bookings 
ADD COLUMN status ENUM('active', 'cancelled') DEFAULT 'active';

CREATE TABLE sliderimages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Drop the existing table if needed and recreate with all required columns
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

/*forgot password*/
ALTER TABLE users
ADD COLUMN reset_token VARCHAR(64) DEFAULT NULL,
ADD COLUMN reset_expiry DATETIME DEFAULT NULL;