USE movie_booking;

-- Drop existing tables if they have foreign key constraints
DROP TABLE IF EXISTS movie_genres;
DROP TABLE IF EXISTS languages;
DROP TABLE IF EXISTS genres;

-- Create languages table
CREATE TABLE languages (
    language_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- Insert default languages
INSERT INTO languages (name) VALUES 
('English'), ('Hindi'), ('Tamil'), ('Telugu'), ('Malayalam');

-- Create genres table
CREATE TABLE genres (
    genre_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE
);

-- Insert default genres
INSERT INTO genres (name) VALUES 
('Action'), ('Comedy'), ('Drama'), ('Horror'), ('Romance'), 
('Sci-Fi'), ('Thriller'), ('Adventure'), ('Animation');

-- Modify movies table first
ALTER TABLE movies
ADD COLUMN language_id INT,
ADD COLUMN movie_type ENUM('2D', '3D') DEFAULT '2D';

-- Add foreign key to movies table
ALTER TABLE movies
ADD CONSTRAINT fk_movie_language
FOREIGN KEY (language_id) REFERENCES languages(language_id);

-- Create movie_genres junction table
CREATE TABLE movie_genres (
    movie_id INT,
    genre_id INT,
    PRIMARY KEY (movie_id, genre_id),
    FOREIGN KEY (movie_id) REFERENCES movies(movie_id) ON DELETE CASCADE,
    FOREIGN KEY (genre_id) REFERENCES genres(genre_id) ON DELETE CASCADE
);