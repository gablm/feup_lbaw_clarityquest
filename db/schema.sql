--------------------
-- Clean Database --
--------------------

DROP INDEX IF EXISTS user_post;
DROP INDEX IF EXISTS answer_question;

DROP INDEX IF EXISTS title_search;
DROP TRIGGER IF EXISTS questions_search_update ON Question;
DROP FUNCTION IF EXISTS questions_search_update;

ALTER TABLE Question
DROP COLUMN IF EXISTS tsvectors;

DROP INDEX IF EXISTS username_search;
DROP TRIGGER IF EXISTS users_search_update ON Users;
DROP FUNCTION IF EXISTS users_search_update;

ALTER TABLE Users
DROP COLUMN IF EXISTS tsvectors;

DROP TRIGGER IF EXISTS trigger_update_post_votes ON Vote;
DROP FUNCTION IF EXISTS update_post_votes;

DROP TRIGGER IF EXISTS trigger_update_medals_posts_upvotes ON Vote;
DROP FUNCTION IF EXISTS update_medals_posts_upvotes;

DROP TRIGGER IF EXISTS trigger_update_medals_posts_created ON Post;
DROP FUNCTION IF EXISTS update_medals_posts_created;

DROP TRIGGER IF EXISTS trigger_update_medals_questions_created ON Question;
DROP FUNCTION IF EXISTS update_medals_questions_created;

DROP TRIGGER IF EXISTS trigger_update_medals_answers_posted ON Answer;
DROP FUNCTION IF EXISTS update_medals_answers_posted;

DROP TABLE IF EXISTS Medals;
DROP TABLE IF EXISTS Vote;
DROP TABLE IF EXISTS Edition;
DROP TABLE IF EXISTS NotificationPost;
DROP TABLE IF EXISTS NotificationUser;
DROP TABLE IF EXISTS Notification;
DROP TABLE IF EXISTS FollowQuestion;
DROP TABLE IF EXISTS FollowTag;
DROP TABLE IF EXISTS PostTag;
DROP TABLE IF EXISTS Tag;
DROP TABLE IF EXISTS Report;
DROP TABLE IF EXISTS Comment;
DROP TABLE IF EXISTS Answer;
DROP TABLE IF EXISTS Question;
DROP TABLE IF EXISTS Post;
DROP TABLE IF EXISTS Users;

DROP TYPE IF EXISTS Permission;
DROP TYPE IF EXISTS NotificationType;

-----------
-- Types --
-----------

CREATE TYPE Permission 
AS ENUM('BLOCKED', 'REGULAR', 'MODERATOR', 'ADMIN');

CREATE TYPE NotificationType
AS ENUM('RESPONSE', 'REPORT', 'FOLLOW', 'MENTION', 'OTHER');

------------
-- Tables --
------------

CREATE TABLE Users(
	id SERIAL PRIMARY KEY,
	username TEXT UNIQUE NOT NULL,
	email TEXT UNIQUE NOT NULL,
	name TEXT NOT NULL,
	hashed_pw TEXT NOT NULL,
	profile_pic TEXT,
	bio TEXT,
	created_at TIMESTAMP NOT NULL DEFAULT NOW() CHECK (created_at >= NOW()),
	role PERMISSION NOT NULL DEFAULT 'REGULAR'
);

CREATE TABLE Post(
    id SERIAL PRIMARY KEY,
    text TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW() CHECK (created_at >= NOW()),
    votes INTEGER DEFAULT 0,
    user_id INTEGER,
	FOREIGN KEY (user_id) REFERENCES Users(id) ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE Question(
    id INTEGER PRIMARY KEY,
    title TEXT NOT NULL,
	FOREIGN KEY (id) REFERENCES Post(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE Answer(
    id INTEGER PRIMARY KEY, 
    question_id BIGINT NOT NULL,
	correct BOOLEAN DEFAULT FALSE,
	FOREIGN KEY (id) REFERENCES Post(id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (question_id) REFERENCES Question(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE Comment(
    id INTEGER PRIMARY KEY,
	post_id INTEGER NOT NULL,
	FOREIGN KEY (id) REFERENCES Post(id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (post_id) REFERENCES Post(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE Vote(
	user_id INTEGER NOT NULL,
	post_id INTEGER NOT NULL,
	positive BOOLEAN,
	PRIMARY KEY (user_id, post_id),
	FOREIGN KEY (user_id) REFERENCES Users(id) ON UPDATE CASCADE ON DELETE SET NULL,
	FOREIGN KEY (post_id) REFERENCES Post(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE Medals(
	user_id INTEGER PRIMARY KEY,
	posts_upvotes BIGINT DEFAULT 0 CHECK (posts_upvotes >= 0),
	posts_created BIGINT DEFAULT 0 CHECK (posts_created >= 0),
	questions_created BIGINT DEFAULT 0 CHECK (questions_created >= 0),
	answers_posted BIGINT DEFAULT 0 CHECK (answers_posted >= 0),
	FOREIGN KEY (user_id) REFERENCES Users(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE Report(
    id SERIAL PRIMARY KEY,
    reason TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW() CHECK (created_at >= NOW()),
    user_id INTEGER NOT NULL,
    post_id INTEGER NOT NULL,
	FOREIGN KEY (user_id) REFERENCES Users(id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (post_id) REFERENCES Post(id) ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE Tag(
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW() CHECK (created_at >= NOW())
);

CREATE TABLE PostTag(
    post_id INTEGER NOT NULL, 
    tag_id INTEGER NOT NULL,
    PRIMARY KEY (post_id, tag_id),
	FOREIGN KEY (post_id) REFERENCES Post(id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (tag_id) REFERENCES Tag(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE FollowTag(
    user_id INTEGER NOT NULL, 
    tag_id INTEGER NOT NULL,
    PRIMARY KEY (user_id, tag_id),
	FOREIGN KEY (user_id) REFERENCES Users(id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (tag_id) REFERENCES Tag(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE FollowQuestion(
    user_id INTEGER NOT NULL,
    question_id INTEGER NOT NULL,
    PRIMARY KEY (user_id, question_id),
	FOREIGN KEY (user_id) REFERENCES Users(id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (question_id) REFERENCES Question(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE Notification(
    id SERIAL PRIMARY KEY,
    receiver INTEGER NOT NULL,
    description TEXT,
    type NotificationType NOT NULL DEFAULT 'OTHER',
    sent_at TIMESTAMP DEFAULT NOW() CHECK (sent_at >= NOW()),
	FOREIGN KEY (receiver) REFERENCES Users(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE NotificationPost(
    notification_id INTEGER NOT NULL,
    post_id INTEGER NOT NULL,
    PRIMARY KEY (notification_id, post_id),
	FOREIGN KEY (notification_id) REFERENCES Post(id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (post_id) REFERENCES Notification(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE NotificationUser(
    notification_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    PRIMARY KEY (notification_id, user_id),
	FOREIGN KEY (notification_id) REFERENCES Notification(id) ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (user_id) REFERENCES Users(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE Edition(
	id SERIAL PRIMARY KEY,
	post_id INTEGER NOT NULL,
	old_title TEXT,
	new_title TEXT,
	old TEXT NOT NULL,
	new TEXT NOT NULL,
	made_at TIMESTAMP NOT NULL DEFAULT NOW() CHECK (made_at >= NOW()),
	FOREIGN KEY (post_id) REFERENCES Post(id) ON UPDATE CASCADE ON DELETE CASCADE
);

-------------
-- Indexes --
-------------

--                            --
-- Performance Search Indexes --
--                            --

CREATE INDEX user_post ON Post USING hash (user_id);

CREATE INDEX answer_question ON ANSWER USING hash (question_id);

--                          --
-- Full-text Search Indexes --
--                          --

-- Username Search --

-- Add column to Users to store computed ts_vector.
ALTER TABLE Users
ADD COLUMN tsvectors TSVECTOR;

-- Create a function to automatically update ts_vectors for Users.
CREATE FUNCTION users_search_update() RETURNS TRIGGER AS $$
BEGIN
 IF TG_OP = 'INSERT' THEN
        NEW.tsvectors = setweight(to_tsvector('english', NEW.username), 'A');
 END IF;
 IF TG_OP = 'UPDATE' THEN
         IF (NEW.username <> OLD.username) THEN
           NEW.tsvectors = setweight(to_tsvector('english', NEW.username), 'A');
         END IF;
 END IF;
 RETURN NEW;
END $$ LANGUAGE plpgsql;

-- Create a trigger before insert or update on Users.
CREATE TRIGGER users_search_update
 BEFORE INSERT OR UPDATE ON Users
 FOR EACH ROW
 EXECUTE PROCEDURE users_search_update();

-- Create a GIN index for ts_vectors in Users.
CREATE INDEX username_search ON Users USING GIN (tsvectors);

--
-- Question search --
--

-- Add column to Question to store computed ts_vector.
ALTER TABLE Question
ADD COLUMN tsvectors TSVECTOR;

-- Create a function to automatically update ts_vectors for Questions.
CREATE FUNCTION questions_search_update() RETURNS TRIGGER AS $$
BEGIN
 IF TG_OP = 'INSERT' THEN
        NEW.tsvectors = setweight(to_tsvector('english', NEW.title), 'A');
 END IF;
 IF TG_OP = 'UPDATE' THEN
         IF (NEW.title <> OLD.title) THEN
           NEW.tsvectors = setweight(to_tsvector('english', NEW.title), 'A');
         END IF;
 END IF;
 RETURN NEW;
END $$ LANGUAGE plpgsql;

-- Create a trigger before insert or update on Question.
CREATE TRIGGER questions_search_update
 BEFORE INSERT OR UPDATE ON Question
 FOR EACH ROW
 EXECUTE PROCEDURE questions_search_update();

CREATE INDEX title_search ON Question USING GIN (tsvectors);

-----------------------
-- Triggers and UDFs --
-----------------------

--Post(votes)
CREATE OR REPLACE FUNCTION update_post_votes()
RETURNS TRIGGER AS $$
BEGIN
    UPDATE Post
    SET votes = (SELECT COUNT(*) FROM Vote WHERE post_id = NEW.post_id AND positive = TRUE) -
                (SELECT COUNT(*) FROM Vote WHERE post_id = NEW.post_id AND positive = FALSE)
    WHERE id = NEW.post_id;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_update_post_votes
AFTER INSERT OR UPDATE ON Vote
FOR EACH ROW
EXECUTE FUNCTION update_post_votes();

--Medals(post_upvotes)
CREATE OR REPLACE FUNCTION update_medals_posts_upvotes()
RETURNS TRIGGER AS $$
BEGIN
    UPDATE Medals
    SET posts_upvotes = (SELECT COUNT(*) FROM Vote WHERE user_id = NEW.user_id AND positive = TRUE)
    WHERE user_id = NEW.user_id;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_update_medals_posts_upvotes
AFTER INSERT OR UPDATE ON Vote
FOR EACH ROW
EXECUTE FUNCTION update_medals_posts_upvotes();

-- Medals(posts_created)
CREATE OR REPLACE FUNCTION update_medals_posts_created()
RETURNS TRIGGER AS $$
BEGIN
    UPDATE Medals
    SET posts_created = (SELECT COUNT(*) FROM Post WHERE user_id = NEW.user_id)
    WHERE user_id = NEW.user_id;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_update_medals_posts_created
AFTER INSERT ON Post
FOR EACH ROW
EXECUTE FUNCTION update_medals_posts_created();

-- Medals(questions_created)
CREATE OR REPLACE FUNCTION update_medals_questions_created()
RETURNS TRIGGER AS $$
BEGIN
    UPDATE Medals
    SET questions_created = (SELECT COUNT(*) FROM Question WHERE post_id IN (SELECT id FROM Post WHERE user_id = NEW.user_id))
    WHERE user_id = NEW.user_id;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_update_medals_questions_created
AFTER INSERT ON Question
FOR EACH ROW
EXECUTE FUNCTION update_medals_questions_created();

-- Medals(answers_posted)
CREATE OR REPLACE FUNCTION update_medals_answers_posted()
RETURNS TRIGGER AS $$
BEGIN
    UPDATE Medals
    SET answers_posted = (SELECT COUNT(*) FROM Answer WHERE post_id IN (SELECT id FROM Post WHERE user_id = NEW.user_id))
    WHERE user_id = NEW.user_id;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_update_medals_answers_posted
AFTER INSERT ON Answer
FOR EACH ROW
EXECUTE FUNCTION update_medals_answers_posted();
