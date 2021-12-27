-- Users --
CREATE TABLE users (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	name TEXT NOT NULL,
	username TEXT NOT NULL UNIQUE,
	password TEXT NOT NULL
);

INSERT INTO users (id, name, username, password) VALUES (1, 'Manu Herksovits', 'manu', '$2y$10$QtCybkpkzh7x5VN11APHned4J8fu78.eFXlyAMmahuAaNcbwZ7FH.');
-- password: monkey --


--- Sessions ---

CREATE TABLE sessions (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	user_id INTEGER NOT NULL,
	session TEXT NOT NULL UNIQUE,
  last_login   TEXT NOT NULL,

  FOREIGN KEY(user_id) REFERENCES users(id)
);

--- Groups ----

CREATE TABLE groups (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	name TEXT NOT NULL UNIQUE
);

INSERT INTO groups (id, name) VALUES (1, 'admin');


--- Group Membership

CREATE TABLE memberships (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
  group_id INTEGER NOT NULL,
  user_id INTEGER NOT NULL,

  FOREIGN KEY(group_id) REFERENCES groups(id),
  FOREIGN KEY(user_id) REFERENCES users(id)
);

INSERT INTO memberships (group_id, user_id) VALUES (1, 1);

-- User 'manu' is a member of the 'admin' group.

-- Create Movies --
CREATE TABLE movies (
  id	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
  user_id INTEGER NOT NULL,
  movie_name	TEXT NOT NULL,
  img_url TEXT,
  rating INT NOT NULL,
  summary TEXT NOT NULL,
  genre TEXT NOT NULL,
  loc TEXT NOT NULL,
  file_name TEXT NOT NULL,
  file_ext TEXT NOT NULL,
  source TEXT NOT NULL,

  FOREIGN KEY(user_id) REFERENCES users(id)
);

INSERT INTO movies (id, user_id, movie_name, img_url, rating, summary, genre, loc, file_name, file_ext, source) VALUES (1, 1, "King Kong", "https://www.rottentomatoes.com/m/king_kong", 4, "A large ape makes things interesting in NYC. Starring Jack Black, this movie is one heck of an adventure.", "Adventure", "Fairport", "King Kong", "jpeg", "rotten tomatoes");

INSERT INTO movies (id, user_id, movie_name, img_url, rating, summary, genre, loc, file_name, file_ext, source) VALUES (2, 1, "Shawshank Redemption", "https://www.google.com/search?q=shawshank+redemption+movie&tbm=isch&ved=2ahUKEwiXvurr-I_wAhVxdzABHVLhCikQ2-cCegQIABAA&oq=shawshank+redemption+movie&gs_lcp=CgNpbWcQAzICCAAyAggAMgIIADICCAAyAggAMgIIADICCAAyAggAMgIIADICCAA6BggAEAcQHlCklooBWNG-igFgwsGKAWgBcAB4AIABwwKIAfcckgEHNy41LjguMpgBAKABAaoBC2d3cy13aXotaW1nwAEB&sclient=img&ei=1muAYJf7IfHuwbkP0sKryAI&bih=914&biw=1680&rlz=1C5CHFA_enUS901US902&hl=en#imgrc=dGJ4hP7-6EgbUM", 5, "One man gets a raw deal when he is caught in the wrong place at the wrong time. See how human nature copes with the unimaginable.", "Drama", "Churchville", "Shawshank Redemption", "jpeg", "google images");

INSERT INTO movies (id, user_id, movie_name, img_url, rating, summary, genre, loc, file_name, file_ext, source) VALUES (3, 1, "Shutter Island", "https://www.imdb.com/title/tt1130884/", 4, "In the mindboggling movie, the main character deals with a lot. Is he going crazy or is this place trying to kill him?", "Thriller", "Brighton", "Shutter Island", "jpeg", "imdb");

INSERT INTO movies (id, user_id,  movie_name, img_url, rating, summary, genre, loc,file_name, file_ext, source) VALUES (4, 1, "Finding Nemo", "https://www.fandangonow.com/details/movie/finding-nemo-2003/MMV1F713A2708DAFAE9779155691EA64A9E4", 4, "A great story of friendship and loyalty. This shows how far a father will go for his son.", "Adventure", "Fairport", "Finding Nemo", "jpeg", "fandango");

INSERT INTO movies (id, user_id, movie_name, img_url, rating, summary, genre, loc, file_name, file_ext, source) VALUES (5, 1, "Avatar", "https://medium.com/the-strategic-whimsy-experiment/avatar-2009-eb810b80b939", 3, "An insane journey into a world full of the unexplored. Meet a new species and explore what role we as humans have when going into new frontiers.", "Science Fiction", "Fairport", "Avatar", "jpeg", "medium");

INSERT INTO movies (id, user_id, movie_name, img_url, rating, summary, genre, loc, file_name, file_ext, source) VALUES (6, 1, "Good Will Hunting", "https://www.goodreads.com/book/show/190224.Good_Will_Hunting", 4, "Some people are just to smart for their own good. The main character in this movie battles with the desire to do better but the feeling of guilt if he was to leave those he loves most.", "Drama", "Brighton", "Good Will Hunting", "jpeg", "goodreads");

INSERT INTO movies (id, user_id, movie_name, img_url, rating, summary, genre, loc, file_name, file_ext, source) VALUES (7, 1, "Transformers", "https://www.imdb.com/title/tt0418279/", 4, "A wild ride full of excitement and action when one man discovers that there is life beyond just Earth, and not all that other life is on our side.", "Science Fiction", "Fairport", "Transformers", "jpeg", "imdb");

INSERT INTO movies (id, user_id, movie_name, img_url, rating, summary, genre, loc, file_name, file_ext, source) VALUES (8, 1, "Insidious", "https://www.pinterest.com/pin/240238961347403529/", 3, "One lost soul wreaks absolute havoc on a poor family. A parent's worst nightmare.", "Horror", "Churchville", "Insidious", "jpeg", "pinterest");

INSERT INTO movies (id, user_id, movie_name, img_url, rating, summary, genre, loc, file_name, file_ext, source) VALUES (9, 1, "Fantastic Four", "https://www.rottentomatoes.com/m/fantastic_four", 4, "Great superhero movie that shows how far friendships can actually stretch.", "Super Hero", "Churchville", "Fantastic Four", "jpeg", "rotten tomatoes");

INSERT INTO movies (id, user_id,  movie_name, img_url, rating, summary, genre, loc, file_name, file_ext, source) VALUES (10, 1, "Hereditary", "https://feminisminindia.com/2018/07/05/hereditary-review/", 5, "A shocking look at what would happen if a family's deepest darkest secret was unravelled before unassuming eyes. Watch this family descend into madness when something that should never have been revealed, is.", "Thriller", "Brighton", "Hereditary", "jpeg", "feminisminindia");

-- Tags --

-- Tag Table --
CREATE TABLE tags (
  id	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
  tag TEXT NOT NULL UNIQUE
);

INSERT INTO tags (id, tag) VALUES (1, "Super-Hero");
INSERT INTO tags (id, tag) VALUES (2, "Horror");
INSERT INTO tags (id, tag) VALUES (3, "Thriller");
INSERT INTO tags (id, tag) VALUES (4, "Drama");
INSERT INTO tags (id, tag) VALUES (5, "Adventure");
INSERT INTO tags (id, tag) VALUES (6, "Kid's");
INSERT INTO tags (id, tag) VALUES (7, "Sci-Fi");
INSERT INTO tags (id, tag) VALUES (8, "Brighton");
INSERT INTO tags (id, tag) VALUES (9, "Churchville");
INSERT INTO tags (id, tag) VALUES (10, "Fairport");

-- Movie Tags --

CREATE TABLE movie_tags (
  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
  movie_id INT NOT NULL,
  tag_id INT NOT NULL,

  FOREIGN KEY(movie_id) REFERENCES movies(id),
  FOREIGN KEY(tag_id) REFERENCES tags(id)
);

INSERT INTO movie_tags (id, movie_id, tag_id) VALUES (1, 1, 5);
INSERT INTO movie_tags (id, movie_id, tag_id) VALUES (2, 2, 4);
INSERT INTO movie_tags (id, movie_id, tag_id) VALUES (3, 3, 3);
INSERT INTO movie_tags (id, movie_id, tag_id) VALUES (4, 4, 6);
INSERT INTO movie_tags (id, movie_id, tag_id) VALUES (5, 5, 5);
INSERT INTO movie_tags (id, movie_id, tag_id) VALUES (6, 6, 4);
INSERT INTO movie_tags (id, movie_id, tag_id) VALUES (7, 7, 7);
INSERT INTO movie_tags (id, movie_id, tag_id) VALUES (8, 8, 2);
INSERT INTO movie_tags (id, movie_id, tag_id) VALUES (9, 9, 1);
INSERT INTO movie_tags (id, movie_id, tag_id) VALUES (10, 10, 3);
INSERT INTO movie_tags (id, movie_id, tag_id) VALUES (11, 4, 10);
INSERT INTO movie_tags (id, movie_id, tag_id) VALUES (12, 1, 10);
INSERT INTO movie_tags (id, movie_id, tag_id) VALUES (13, 2, 9);
INSERT INTO movie_tags (id, movie_id, tag_id) VALUES (14, 3, 8);
INSERT INTO movie_tags (id, movie_id, tag_id) VALUES (16, 5, 10);
INSERT INTO movie_tags (id, movie_id, tag_id) VALUES (17, 6, 8);
INSERT INTO movie_tags (id, movie_id, tag_id) VALUES (18, 7, 10);
INSERT INTO movie_tags (id, movie_id, tag_id) VALUES (19, 8, 9);
INSERT INTO movie_tags (id, movie_id, tag_id) VALUES (20, 9, 9);
INSERT INTO movie_tags (id, movie_id, tag_id) VALUES (21, 10, 8);
