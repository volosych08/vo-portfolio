CREATE TABLE profile_sections (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    section_key TEXT NOT NULL UNIQUE,
    title TEXT NOT NULL,
    content TEXT NOT NULL,
    updated_at TEXT NOT NULL
);

INSERT INTO profile_sections (section_key, title, content, updated_at) VALUES
                                                                           (
                                                                               'about_me',
                                                                               'About me',
                                                                               'My name is Volodymyr, I am 17 years old and originally from Ukraine. I became curious about programming when I was around 9 years old. I started with Python by following tutorials and building a simple calculator, later exploring C++ where I even created a small Snake game. After a few breaks and restarts, I returned to coding through web development with HTML, CSS, and JavaScript, and since then I have been learning independently through online courses and projects.

                                                                           Beyond technology, I am also a car enthusiast who loves driving and learning about vehicles. My favorite movie series is Fast & Furious, and I enjoy the show Silicon Valley for its humor and insights into the tech world. I chose to study ICT because it combines my love for creativity, problem-solving, and technology, and I am excited to continue building my skills at HZ University of Applied Sciences.',
                                                                               datetime('now')
                                                                           ),
                                                                           (
                                                                               'programming_skills',
                                                                               'Programming',
                                                                               'HTML (Good)
                                                                           CSS (Good)
                                                                           C# (Beginner)
                                                                           C++ (Pre Beginner)',
                                                                               datetime('now')
                                                                           ),
                                                                           (
                                                                               'languages',
                                                                               'Languages',
                                                                               'Ukrainian (Native)
                                                                           Russian (C2)
                                                                           Polish (C1)
                                                                           English (B2)',
                                                                               datetime('now')
                                                                           );