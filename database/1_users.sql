CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL,
    password TEXT NOT NULL,
    name TEXT,
    role TEXT DEFAULT 'user'
);

INSERT INTO users (id, username, password, name, role) VALUES
   (1, 'gandalf', '', 'Gandalf the Grey', 'admin'),
    (2, 'sam', '', 'Samwise Gamgee', 'user'),
    (3, 'merry', '', 'Meriadoc Brandybuck', 'user'),
    (4, 'pippin', '', 'Peregrin Took', 'user');