CREATE TABLE blog_posts (
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

INSERT INTO blog_posts (
    title,
    slug,
    excerpt,
    content,
    status,
    card_image,
    hero_image,
    published_at,
    created_at,
    updated_at
) VALUES
      (
          'Study Choice',
          'study-choice',
          'Why I chose ICT at HZ, the activities that influenced me, and what I hope to achieve after completing my studies.',
          '<p>When I was considering my future studies, I wanted a program that would let me combine creativity, problem-solving, and technology. ICT stood out to me because it is practical, future-oriented, and full of opportunities worldwide. I enjoy turning ideas into real projects, and ICT gives me the tools to do exactly that. HZ University of Applied Sciences attracted me because of its focus on hands-on learning and personal growth. After completing my studies, I want to continue developing as a software engineer and possibly start my own projects in areas like AI, web development, or even SaaS businesses.</p>',
          'published',
          '/img/scl.jpg',
          '/img/studyChoice.png',
          '2025-05-09',
          datetime('now'),
          datetime('now')
      ),
      (
          'Personal SWOT Analysis',
          'personal-swot-analysis',
          'My strengths, weaknesses, opportunities, and challenges as a future ICT student, and how I plan to grow from them.',
          '<h3>Strengths (S)</h3>
      <ul>
          <li>Started programming young (Python, C++, web dev) and built small projects like Snake and websites.</li>
          <li>Self-motivated learner, comfortable with YouTube, online courses, and documentation.</li>
          <li>Creative and good at problem-solving, able to turn ideas into working code.</li>
          <li>Strong interest in cars and technology, which keeps me inspired.</li>
          <li>Hyperfocus moments (ADHD) allow me to code and learn intensively for many hours.</li>
      </ul>

      <h3>Weaknesses (W)</h3>
      <ul>
          <li>Sometimes lose focus or motivation when tasks feel repetitive or boring.</li>
          <li>Still developing teamwork and communication skills in an academic/professional setting.</li>
          <li>Limited formal experience with large projects or advanced programming concepts.</li>
          <li>Can overwork during hyperfocus and forget about balance.</li>
      </ul>

      <h3>Opportunities (O)</h3>
      <ul>
          <li>Studying ICT at HZ provides hands-on practice and real-world projects.</li>
          <li>The Netherlands offers strong job opportunities and a great tech ecosystem.</li>
          <li>Access to international peers and teachers to learn from.</li>
          <li>Chance to expand skills in areas like AI, software engineering, and SaaS business ideas.</li>
          <li>GitHub portfolio and projects can grow into a career showcase.</li>
      </ul>

      <h3>Threats (T)</h3>
      <ul>
          <li>High competition in the ICT field, need to keep improving to stand out.</li>
          <li>Fast-changing technology requires constant learning and adaptation.</li>
          <li>Risk of procrastination or burnout if time management is not handled well.</li>
          <li>Language and cultural adaptation as an international student.</li>
      </ul>',
          'published',
          '/img/swotl.jpg',
          '/img/swot.png',
          '2025-05-09',
          datetime('now'),
          datetime('now')
      ),
      (
          'Programming Experience',
          'programming-experience',
          'A look at how I started coding, the languages I explored, and the lessons I learned from self-study and projects.',
          '<p>My programming journey started at around 9 years old. I first tried Python and created a small calculator by following YouTube tutorials. Later, I studied C++ with a tutor and even managed to create a small Snake game. After a break, I returned to coding through web development with HTML, CSS, and JavaScript, which I still practice today. More recently, I began exploring React, Firebase, and C#, and I plan to keep building projects that push me further as a developer.</p>',
          'published',
          '/img/pel.webp',
          '/img/programingxp.png',
          '2025-05-09',
          datetime('now'),
          datetime('now')
      ),
      (
          'First Feedback',
          'first-feedback',
          'The first feedback I received at HZ, how I reacted to it, and how it shaped my approach to learning ICT.',
          '<h3>First feedback</h3>

      <table class="blog-feedback-table">
          <thead>
              <tr>
                  <th>Feedback</th>
                  <th>Type</th>
                  <th>Positive</th>
                  <th>Negative</th>
              </tr>
          </thead>
          <tbody>
              <tr>
                  <td>1</td>
                  <td>Sticky note from TA</td>
                  <td>Style consistency, comments, CSS, fun fade.</td>
                  <td>Blog pictures and posts to blog pages.</td>
              </tr>
              <tr>
                  <td>2</td>
                  <td>Sticky note from TA</td>
                  <td>Scrolling animation, indexing, folder structure.</td>
                  <td>Navbar in posts pages.</td>
              </tr>
              <tr>
                  <td>3</td>
                  <td>Evaluation of pitch and website</td>
                  <td>Code well organized, fun animation, looks professional.</td>
                  <td>No major negative feedback.</td>
              </tr>
              <tr>
                  <td>4</td>
                  <td>Evaluation of pitch and website</td>
                  <td>Positive evaluation points received for the website and pitch.</td>
                  <td>No major negative feedback.</td>
              </tr>
              <tr>
                  <td>5</td>
                  <td>Evaluation of pitch and website</td>
                  <td>Positive evaluation points received for the website and pitch.</td>
                  <td>No major negative feedback.</td>
              </tr>
          </tbody>
      </table>

      <h3>Progress</h3>
      <ul>
          <li>Evaluation of pitch and website received - 3/3</li>
          <li>Evaluation of pitch and website given - 3/3</li>
      </ul>',
          'published',
          '/img/feedback.jpg',
          '/img/feedback.webp',
          '2025-05-09',
          datetime('now'),
          datetime('now')
      ),
      (
          'ICT Field of Work',
          'ict-field-of-work',
          'An overview of today’s ICT field, the exciting opportunities it offers, and why I see my future career in it.',
          '<p>The ICT field is one of the fastest-growing in the world. With AI, cloud computing, and cybersecurity shaping the future, ICT professionals are in demand more than ever. According to recent reports, roles such as software engineers, AI specialists, and cloud architects are among the most sought-after. For students like me, this means that learning ICT is not only exciting but also a safe investment in the future. ICT professionals don’t just code; they solve global problems, innovate in industries, and shape how society uses technology.</p>',
          'published',
          '/img/ictl.jpg',
          '/img/ictwork.jpg',
          '2025-05-09',
          datetime('now'),
          datetime('now')
      );