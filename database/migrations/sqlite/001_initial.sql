CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL,
    password TEXT NOT NULL,
    name TEXT,
    role TEXT DEFAULT 'user'
);

CREATE TABLE IF NOT EXISTS blog_posts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    slug TEXT NOT NULL UNIQUE,
    excerpt TEXT NOT NULL,
    content TEXT NOT NULL,
    status TEXT NOT NULL DEFAULT 'published',
    card_image TEXT NOT NULL,
    hero_image TEXT NOT NULL,
    published_at TEXT NOT NULL,
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS profile_sections (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    section_key TEXT NOT NULL UNIQUE,
    title TEXT NOT NULL,
    content TEXT NOT NULL,
    updated_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS study_assessments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    quartile TEXT NOT NULL,
    course_name TEXT NOT NULL,
    course_code TEXT NOT NULL,
    exam_name TEXT NOT NULL,
    exam_date TEXT NOT NULL,
    earnable_credits REAL NOT NULL,
    grade REAL,
    sort_order INTEGER NOT NULL,
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS projects (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    description TEXT NOT NULL,
    long_description TEXT NOT NULL,
    tech_json TEXT NOT NULL,
    status TEXT NOT NULL,
    completed_at TEXT NOT NULL,
    category TEXT NOT NULL,
    image TEXT NOT NULL,
    url TEXT NOT NULL,
    sort_order INTEGER NOT NULL,
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL
);

INSERT OR IGNORE INTO users (id, username, password, name, role) VALUES
    (1, 'vova', '$2a$12$GyZ821AVxFY9.eXgDUl7YO4SunTQPFBj.4Zl2j.FtPtB.rGc6TNKq', 'Volodymyr', 'admin');

INSERT OR IGNORE INTO profile_sections (id, section_key, title, content, updated_at) VALUES
    (1, 'about_me', 'About me', 'My name is Volodymyr, I am 17 years old and originally from Ukraine. I became curious about programming when I was around 9 years old. I chose to study ICT because it combines creativity, problem-solving, and technology.', datetime('now')),
    (2, 'programming_skills', 'Programming', 'HTML (Good)
CSS (Good)
C# (Beginner)
C++ (Pre Beginner)', datetime('now')),
    (3, 'languages', 'Languages', 'Ukrainian (Native)
Russian (C2)
Polish (C1)
English (B2)', datetime('now'));

INSERT OR IGNORE INTO projects (
    id, title, description, long_description, tech_json, status, completed_at, category, image, url, sort_order, created_at, updated_at
) VALUES
    (1, 'Portfolio Website', 'A responsive personal portfolio website showcasing my projects, skills, blog, profile, and study progress.', 'A responsive personal portfolio website with dynamic content management, admin-only editing, blog publishing workflow, profile sections, and a study dashboard.', '["PHP", "Twig", "SQLite", "JavaScript"]', 'Completed', '2025-04-18', 'Web Development', '/uploads/projects/projects_6a22cc89b843c2.14563818.png', '/profile', 1, datetime('now'), datetime('now')),
    (2, 'Study Dashboard', 'A dashboard that stores assessments in the database and calculates earned credits based on grades.', 'A study progress dashboard that stores assessment data, allows grade editing, and automatically calculates earned study credits and NBSA progress.', '["PHP", "Twig", "SQLite", "PHPUnit", "JavaScript"]', 'Completed', '2025-05-24', 'Study Progress', '/uploads/projects/projects_6a22cc89b843c2.14563818.png', '/dashboard', 2, datetime('now'), datetime('now')),
    (3, '3D Laptop Project Showcase', 'An interactive Three.js laptop that allows visitors to explore projects through a virtual laptop screen.', 'An interactive project showcase built with Three.js. Visitors can rotate the laptop, enter fullscreen mode, open and close the screen, and browse project data loaded dynamically from a RESTful API.', '["Three.js", "JavaScript", "WebGL", "REST API", "Canvas Texture"]', 'Completed', '2026-06-02', 'Innovation', '/uploads/projects/projects_6a22cc89b843c2.14563818.png', '/projects-3d', 3, datetime('now'), datetime('now'));

INSERT OR IGNORE INTO study_assessments (
    id, quartile, course_name, course_code, exam_name, exam_date, earnable_credits, grade, sort_order, created_at, updated_at
) VALUES
    (1, 'S1 Q1', 'PCO', 'CU75001V3', 'First Exam Opportunity', '03-10-2025', 2.5, 9.3, 1, datetime('now'), datetime('now')),
    (2, 'S1 Q1', 'PBA', 'CU75003V1', 'Casustoets PBA', '30-10-2025', 5, 9.7, 2, datetime('now'), datetime('now')),
    (3, 'S1 Q1', 'CSB', 'CU75002V1', 'Schriftelijke Kennistoets', '31-10-2025', 5, 7.3, 3, datetime('now'), datetime('now')),
    (4, 'S1 Q2', 'OOP', 'CU75004V1', 'Presentation', 'Week S1.15', 5, 7.5, 4, datetime('now'), datetime('now')),
    (5, 'S2 Q4', 'Framework Project 2', 'CU75011V4', 'Portfolio (IT Development Portfolio)', 'Week S2.18', 5, NULL, 5, datetime('now'), datetime('now'));
