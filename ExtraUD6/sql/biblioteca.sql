--Copiar este código en phpmyadmin

CREATE TABLE libros (

    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    autor VARCHAR(150) NOT NULL,
    año_publicacion INT,
    genero ENUM('Ficción', 'No Ficción', 'Ciencia', 'Historia', 'Tecnología', 'Arte', 'Otros') NOT NULL,
    paginas INT,
    leido BOOLEAN DEFAULT FALSE,
    valoracion INT CHECK (valoracion >= 1 AND valoracion <= 5),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP

);

INSERT INTO libros (titulo, autor, año_publicacion, genero, paginas, leido, valoracion) VALUES
('Cien años de soledad', 'Gabriel García Márquez', 1967, 'Ficción', 471, TRUE, 5),
('Sapiens', 'Yuval Noah Harari', 2011, 'Historia', 443, TRUE, 4),
('1984', 'George Orwell', 1949, 'Ficción', 328, TRUE, 5),
('El Principito', 'Antoine de Saint-Exupéry', 1943, 'Ficción', 96, FALSE, NULL),
('Clean Code', 'Robert C. Martin', 2008, 'Tecnología', 464, FALSE, NULL);