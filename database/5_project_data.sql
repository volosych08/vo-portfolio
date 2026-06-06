CREATE TABLE projects (
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
