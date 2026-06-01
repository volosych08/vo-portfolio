CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL,
    password TEXT NOT NULL,
    name TEXT,
    role TEXT DEFAULT 'user'
);

INSERT INTO users (id, username, password, name, role) VALUES
   (1, 'vova', '$2a$12$GyZ821AVxFY9.eXgDUl7YO4SunTQPFBj.4Zl2j.FtPtB.rGc6TNKq', 'Volodymyr', 'admin');