DROP SCHEMA IF EXISTS lbaw24125 CASCADE;
CREATE SCHEMA IF NOT EXISTS lbaw24125;
SET search_path TO lbaw24125;

--------------------
-- Clean Database --
--------------------

DROP INDEX IF EXISTS user_post;
DROP INDEX IF EXISTS answer_question;

DROP INDEX IF EXISTS title_search;
DROP TRIGGER IF EXISTS questions_search_update ON "questions";
DROP FUNCTION IF EXISTS questions_search_update;

ALTER TABLE IF EXISTS "questions"
DROP COLUMN IF EXISTS tsvectors;

DROP INDEX IF EXISTS name_search;
DROP TRIGGER IF EXISTS users_search_update ON "users";
DROP FUNCTION IF EXISTS users_search_update;

ALTER TABLE IF EXISTS "users"
DROP COLUMN IF EXISTS tsvectors;

DROP TRIGGER IF EXISTS trigger_update_post_votes ON Vote;
DROP FUNCTION IF EXISTS update_post_votes;

DROP TRIGGER IF EXISTS trigger_update_medals_posts_upvoted ON Vote;
DROP FUNCTION IF EXISTS update_medals_posts_upvoted;

DROP TRIGGER IF EXISTS trigger_update_medals_posts_created ON "posts";
DROP FUNCTION IF EXISTS update_medals_posts_created;

DROP TRIGGER IF EXISTS trigger_update_medals_questions_created ON "questions";
DROP FUNCTION IF EXISTS update_medals_questions_created;

DROP TRIGGER IF EXISTS trigger_update_medals_answers_posted ON "answers";
DROP FUNCTION IF EXISTS update_medals_answers_posted;

DROP TABLE IF EXISTS "medals";
DROP TABLE IF EXISTS "votes";
DROP TABLE IF EXISTS "editions";
DROP TABLE IF EXISTS NotificationPost;
DROP TABLE IF EXISTS NotificationUser;
DROP TABLE IF EXISTS "notifications";
DROP TABLE IF EXISTS FollowQuestion;
DROP TABLE IF EXISTS FollowTag;
DROP TABLE IF EXISTS PostTag;
DROP TABLE IF EXISTS "tags";
DROP TABLE IF EXISTS "report";
DROP TABLE IF EXISTS "comments";
DROP TABLE IF EXISTS "answers";
DROP TABLE IF EXISTS "questions";
DROP TABLE IF EXISTS "posts";
DROP TABLE IF EXISTS "users";

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

CREATE TABLE "users"(
    id SERIAL PRIMARY KEY,
    username TEXT UNIQUE NOT NULL,
    email TEXT UNIQUE NOT NULL,
    name TEXT NOT NULL,
    password TEXT,
	google_token TEXT,
	x_token TEXT,
	remember_token TEXT,
    profile_pic TEXT,
    bio TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    role PERMISSION NOT NULL DEFAULT 'REGULAR'
);

CREATE TABLE "posts"(
    id SERIAL PRIMARY KEY,
    text TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    votes INTEGER DEFAULT 0,
    user_id INTEGER,
    FOREIGN KEY (user_id) REFERENCES "users"(id) ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE "questions"(
    id INTEGER PRIMARY KEY,
    title TEXT NOT NULL,
    FOREIGN KEY (id) REFERENCES "posts"(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE "answers"(
    id INTEGER PRIMARY KEY, 
    question_id INT NOT NULL,
    correct BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id) REFERENCES "posts"(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES "questions"(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE "comments"(
    id INTEGER PRIMARY KEY,
    post_id INTEGER NOT NULL,
    FOREIGN KEY (id) REFERENCES "posts"(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES "posts"(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE "votes"(
    user_id INTEGER NOT NULL,
    post_id INTEGER NOT NULL,
    positive BOOLEAN,
    PRIMARY KEY (user_id, post_id),
    FOREIGN KEY (user_id) REFERENCES "users"(id) ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (post_id) REFERENCES "posts"(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE "medals"(
    user_id INTEGER PRIMARY KEY,
    posts_upvoted BIGINT DEFAULT 0 CHECK (posts_upvoted >= 0),
    posts_created BIGINT DEFAULT 0 CHECK (posts_created >= 0),
    questions_created BIGINT DEFAULT 0 CHECK (questions_created >= 0),
    answers_posted BIGINT DEFAULT 0 CHECK (answers_posted >= 0),
    FOREIGN KEY (user_id) REFERENCES "users"(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE "reports"(
    id SERIAL PRIMARY KEY,
    reason TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    user_id INTEGER NOT NULL,
    post_id INTEGER NOT NULL,
    FOREIGN KEY (user_id) REFERENCES "users"(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES "posts"(id) ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE "tags"(
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE PostTag(
    post_id INTEGER NOT NULL, 
    tag_id INTEGER NOT NULL,
    PRIMARY KEY (post_id, tag_id),
    FOREIGN KEY (post_id) REFERENCES "posts"(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES "tags"(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE FollowTag(
    user_id INTEGER NOT NULL, 
    tag_id INTEGER NOT NULL,
    PRIMARY KEY (user_id, tag_id),
    FOREIGN KEY (user_id) REFERENCES "users"(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES "tags"(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE FollowQuestion(
    user_id INTEGER NOT NULL,
    question_id INTEGER NOT NULL,
    PRIMARY KEY (user_id, question_id),
    FOREIGN KEY (user_id) REFERENCES "users"(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES "questions"(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE "notifications"(
    id SERIAL PRIMARY KEY,
    receiver INTEGER NOT NULL,
    description TEXT,
    type NotificationType NOT NULL DEFAULT 'OTHER',
    sent_at TIMESTAMP NOT NULL DEFAULT NOW(),
    FOREIGN KEY (receiver) REFERENCES "users"(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE NotificationPost(
    notification_id INTEGER NOT NULL,
    post_id INTEGER NOT NULL,
    PRIMARY KEY (notification_id, post_id),
    FOREIGN KEY (notification_id) REFERENCES "posts"(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES "notifications"(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE NotificationUser(
    notification_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    PRIMARY KEY (notification_id, user_id),
    FOREIGN KEY (notification_id) REFERENCES "notifications"(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES "users"(id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE "editions"(
    id SERIAL PRIMARY KEY,
    post_id INTEGER NOT NULL,
    old_title TEXT,
    new_title TEXT,
    old TEXT NOT NULL,
    new TEXT NOT NULL,
    made_at TIMESTAMP NOT NULL DEFAULT NOW(),
    FOREIGN KEY (post_id) REFERENCES "posts"(id) ON UPDATE CASCADE ON DELETE CASCADE
);

-------------
-- Indexes --
-------------

--                            --
-- Performance Search Indexes --
--                            --

CREATE INDEX user_post ON "posts" USING hash (user_id);

CREATE INDEX answer_question ON "answers" USING hash (question_id);

--                          --
-- Full-text Search Indexes --
--                          --

-- Name Search --

-- Add column to "users" to store computed ts_vector.
ALTER TABLE "users"
ADD COLUMN tsvectors TSVECTOR;

-- Create a function to automatically update ts_vectors for "users".
CREATE FUNCTION users_search_update()
RETURNS TRIGGER AS $$
BEGIN
    IF TG_OP = 'INSERT' THEN
        NEW.tsvectors = setweight(to_tsvector('english', NEW.username), 'A') 
			|| setweight(to_tsvector('english', NEW.name), 'B');
    END IF;
    IF TG_OP = 'UPDATE' THEN
        IF (NEW.username <> OLD.username) THEN
            NEW.tsvectors = setweight(to_tsvector('english', NEW.username), 'A');
				|| setweight(to_tsvector('english', NEW.name), 'B');
        END IF;
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Create a trigger before insert or update on "users".
CREATE TRIGGER users_search_update
BEFORE INSERT OR UPDATE ON "users"
FOR EACH ROW
EXECUTE PROCEDURE users_search_update();

-- Create a GIN index for ts_vectors in "users".
CREATE INDEX name_search ON "users" USING GIN (tsvectors);

--
-- "questions" search --
--

-- Add column to "questions" to store computed ts_vector.
ALTER TABLE "questions"
ADD COLUMN tsvectors TSVECTOR;

-- Create a function to automatically update ts_vectors for Questions.
CREATE FUNCTION questions_search_update()
RETURNS TRIGGER AS $$
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
END;
$$ LANGUAGE plpgsql;

-- Create a trigger before insert or update on "questions".
CREATE TRIGGER questions_search_update
BEFORE INSERT OR UPDATE ON "questions"
FOR EACH ROW
EXECUTE PROCEDURE questions_search_update();

CREATE INDEX title_search ON "questions" USING GIN (tsvectors);

-----------------------
-- Triggers and UDFs --
-----------------------

-- Posts(votes)
CREATE OR REPLACE FUNCTION update_post_votes()
RETURNS TRIGGER AS $$
BEGIN
    UPDATE "posts"
    SET votes = (SELECT COUNT(*) FROM "votes" WHERE post_id = NEW.post_id AND positive = TRUE) -
                (SELECT COUNT(*) FROM "votes" WHERE post_id = NEW.post_id AND positive = FALSE)
    WHERE id = NEW.post_id;
    RETURN NULL;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_update_post_votes
AFTER INSERT OR UPDATE ON "votes"
FOR EACH ROW
EXECUTE FUNCTION update_post_votes();

--Medals(posts_upvoted)
CREATE OR REPLACE FUNCTION update_medals_posts_upvoted()
RETURNS TRIGGER AS $$
BEGIN
    UPDATE "medals"
    SET posts_upvoted = (SELECT COUNT(*) FROM "votes" WHERE user_id = NEW.user_id AND positive = TRUE)
    WHERE user_id = NEW.user_id;
    RETURN NULL;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_update_medals_posts_upvoted
AFTER INSERT OR UPDATE ON "votes"
FOR EACH ROW
EXECUTE FUNCTION update_medals_posts_upvoted();

-- Medals(posts_created)
CREATE OR REPLACE FUNCTION update_medals_posts_created()
RETURNS TRIGGER AS $$
BEGIN
    UPDATE "medals"
    SET posts_created = (SELECT COUNT(*) FROM "posts" WHERE user_id = NEW.user_id)
    WHERE user_id = NEW.user_id;
    RETURN NULL;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_update_medals_posts_created
AFTER INSERT ON "posts"
FOR EACH ROW
EXECUTE FUNCTION update_medals_posts_created();

-- Medals(questions_created)
CREATE OR REPLACE FUNCTION update_medals_questions_created()
RETURNS TRIGGER AS $$
DECLARE
	user_id2 INTEGER;
BEGIN
	SELECT user_id INTO user_id2
	FROM "posts"
	WHERE id = NEW.id;

    UPDATE Medals
    SET questions_created = (SELECT COUNT(*) FROM "questions" WHERE id IN (SELECT id FROM "posts" WHERE user_id = user_id2))
    WHERE user_id = user_id2;
    RETURN NULL;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_update_medals_questions_created
AFTER INSERT ON "questions"
FOR EACH ROW
EXECUTE FUNCTION update_medals_questions_created();

-- Medals(answers_posted)
CREATE OR REPLACE FUNCTION update_medals_answers_posted()
RETURNS TRIGGER AS $$
DECLARE
	user_id2 INTEGER;
BEGIN
	SELECT user_id INTO user_id2
	FROM "posts"
	WHERE id = NEW.id;

    UPDATE "medals"
    SET answers_posted = (SELECT COUNT(*) FROM "answers" WHERE id IN (SELECT id FROM "posts" WHERE user_id = user_id2))
    WHERE user_id = user_id2;
    RETURN NULL;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_update_medals_answers_posted
AFTER INSERT ON "answers"
FOR EACH ROW
EXECUTE FUNCTION update_medals_answers_posted();
