CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(255),
    role VARCHAR(255) DEFAULT 'user'
);

CREATE TABLE IF NOT EXISTS blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    excerpt TEXT NOT NULL,
    content TEXT NOT NULL,
    status VARCHAR(255) NOT NULL DEFAULT 'published',
    card_image VARCHAR(255) NOT NULL,
    hero_image VARCHAR(255) NOT NULL,
    published_at VARCHAR(255) NOT NULL,
    created_at VARCHAR(255) NOT NULL,
    updated_at VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS profile_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_key VARCHAR(255) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    updated_at VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS study_assessments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quartile VARCHAR(255) NOT NULL,
    course_name VARCHAR(255) NOT NULL,
    course_code VARCHAR(255) NOT NULL,
    exam_name VARCHAR(255) NOT NULL,
    exam_date VARCHAR(255) NOT NULL,
    earnable_credits DECIMAL(5,2) NOT NULL,
    grade DECIMAL(5,2),
    sort_order INT NOT NULL,
    created_at VARCHAR(255) NOT NULL,
    updated_at VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    long_description TEXT NOT NULL,
    tech_json TEXT NOT NULL,
    status VARCHAR(255) NOT NULL,
    completed_at VARCHAR(255) NOT NULL,
    category VARCHAR(255) NOT NULL,
    image VARCHAR(255) NOT NULL,
    url VARCHAR(255) NOT NULL,
    sort_order INT NOT NULL,
    created_at VARCHAR(255) NOT NULL,
    updated_at VARCHAR(255) NOT NULL
);

INSERT INTO users (id, username, password, name, role) VALUES
    (1, 'vova', '$2a$12$GyZ821AVxFY9.eXgDUl7YO4SunTQPFBj.4Zl2j.FtPtB.rGc6TNKq', 'Volodymyr', 'admin')
ON DUPLICATE KEY UPDATE username = VALUES(username), password = VALUES(password), name = VALUES(name), role = VALUES(role);

INSERT INTO profile_sections (id, section_key, title, content, updated_at) VALUES
    (1, 'about_me', 'About me', 'My name is Volodymyr, I am 17 years old and originally from Ukraine. I became curious about programming when I was around 9 years old. I chose to study ICT because it combines creativity, problem-solving, and technology.', NOW()),
    (2, 'programming_skills', 'Programming', 'HTML (Good)
CSS (Good)
C# (Beginner)
C++ (Pre Beginner)', NOW()),
    (3, 'languages', 'Languages', 'Ukrainian (Native)
Russian (C2)
Polish (C1)
English (B2)', NOW())
ON DUPLICATE KEY UPDATE section_key = VALUES(section_key), title = VALUES(title), content = VALUES(content), updated_at = VALUES(updated_at);

INSERT INTO projects (
    id, title, description, long_description, tech_json, status, completed_at, category, image, url, sort_order, created_at, updated_at
) VALUES
    (1, 'Portfolio Website', 'A responsive personal portfolio website showcasing my projects, skills, blog, profile, and study progress.', 'A responsive personal portfolio website with dynamic content management, admin-only editing, blog publishing workflow, profile sections, and a study dashboard.', '["PHP", "Twig", "SQLite", "JavaScript"]', 'Completed', '2025-04-18', 'Web Development', '/uploads/projects/projects_6a22cc89b843c2.14563818.png', '/profile', 1, NOW(), NOW()),
    (2, 'Study Dashboard', 'A dashboard that stores assessments in the database and calculates earned credits based on grades.', 'A study progress dashboard that stores assessment data, allows grade editing, and automatically calculates earned study credits and NBSA progress.', '["PHP", "Twig", "SQLite", "PHPUnit", "JavaScript"]', 'Completed', '2025-05-24', 'Study Progress', '/uploads/projects/projects_6a22cc89b843c2.14563818.png', '/dashboard', 2, NOW(), NOW()),
    (3, '3D Laptop Project Showcase', 'An interactive Three.js laptop that allows visitors to explore projects through a virtual laptop screen.', 'An interactive project showcase built with Three.js. Visitors can rotate the laptop, enter fullscreen mode, open and close the screen, and browse project data loaded dynamically from a RESTful API.', '["Three.js", "JavaScript", "WebGL", "REST API", "Canvas Texture"]', 'Completed', '2026-06-02', 'Innovation', '/uploads/projects/projects_6a22cc89b843c2.14563818.png', '/projects-3d', 3, NOW(), NOW())
ON DUPLICATE KEY UPDATE title = VALUES(title), description = VALUES(description), long_description = VALUES(long_description), tech_json = VALUES(tech_json), status = VALUES(status), completed_at = VALUES(completed_at), category = VALUES(category), image = VALUES(image), url = VALUES(url), sort_order = VALUES(sort_order), updated_at = VALUES(updated_at);

INSERT INTO study_assessments (
    id, quartile, course_name, course_code, exam_name, exam_date, earnable_credits, grade, sort_order, created_at, updated_at
) VALUES
    (1, 'S1 Q1', 'PCO', 'CU75001V3', 'First Exam Opportunity', '03-10-2025', 2.5, 9.3, 1, NOW(), NOW()),
    (2, 'S1 Q1', 'PBA', 'CU75003V1', 'Casustoets PBA', '30-10-2025', 5, 9.7, 2, NOW(), NOW()),
    (3, 'S1 Q1', 'CSB', 'CU75002V1', 'Schriftelijke Kennistoets', '31-10-2025', 5, 7.3, 3, NOW(), NOW()),
    (4, 'S1 Q2', 'OOP', 'CU75004V1', 'Presentation', 'Week S1.15', 5, 7.5, 4, NOW(), NOW()),
    (5, 'S2 Q4', 'Framework Project 2', 'CU75011V4', 'Portfolio (IT Development Portfolio)', 'Week S2.18', 5, NULL, 5, NOW(), NOW())
ON DUPLICATE KEY UPDATE quartile = VALUES(quartile), course_name = VALUES(course_name), course_code = VALUES(course_code), exam_name = VALUES(exam_name), exam_date = VALUES(exam_date), earnable_credits = VALUES(earnable_credits), grade = VALUES(grade), sort_order = VALUES(sort_order), updated_at = VALUES(updated_at);
