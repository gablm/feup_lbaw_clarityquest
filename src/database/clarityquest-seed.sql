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
DROP TABLE IF EXISTS PasswordResets;

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
    user_id INTEGER,
    post_id INTEGER NOT NULL,
    positive BOOLEAN,
    PRIMARY KEY (user_id, post_id),
    FOREIGN KEY (user_id) REFERENCES "users"(id) ON UPDATE CASCADE ON DELETE CASCADE,
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
    user_id INTEGER,
    post_id INTEGER NOT NULL,
    FOREIGN KEY (user_id) REFERENCES "users"(id) ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (post_id) REFERENCES "posts"(id) ON UPDATE CASCADE ON DELETE CASCADE
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
    FOREIGN KEY (notification_id) REFERENCES "notifications"(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES "posts"(id) ON UPDATE CASCADE ON DELETE CASCADE
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

CREATE TABLE PasswordResets(
    id SERIAL PRIMARY KEY,
    email TEXT NOT NULL,
    token TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
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

-- Name/Username Search --

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
            NEW.tsvectors = setweight(to_tsvector('english', NEW.username), 'A')
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

--
-- "tags" search --
--

-- Add column to "tags" to store computed ts_vector.
ALTER TABLE "tags"
ADD COLUMN tsvectors TSVECTOR;

-- Create a function to automatically update ts_vectors for Tags.
CREATE FUNCTION tags_search_update()
RETURNS TRIGGER AS $$
BEGIN
    IF TG_OP = 'INSERT' THEN
        NEW.tsvectors = setweight(to_tsvector('english', NEW.name), 'A');
    END IF;
    IF TG_OP = 'UPDATE' THEN
        IF (NEW.name <> OLD.name) THEN
            NEW.tsvectors = setweight(to_tsvector('english', NEW.name), 'A');
        END IF;
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Create a trigger before insert or update on "tags".
CREATE TRIGGER tags_search_update
BEFORE INSERT OR UPDATE ON "tags"
FOR EACH ROW
EXECUTE PROCEDURE tags_search_update();

CREATE INDEX tag_search ON "tags" USING GIN (tsvectors);

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
AFTER INSERT OR UPDATE OR DELETE ON "votes"
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


-- DB MUST BE UPDATED
-- New function to handle vote removal

CREATE OR REPLACE FUNCTION update_post_votes_on_delete()
RETURNS TRIGGER AS $$
BEGIN
    UPDATE posts
    SET votes = (SELECT COUNT(*) FROM votes WHERE post_id = OLD.post_id AND positive = TRUE) -
                (SELECT COUNT(*) FROM votes WHERE post_id = OLD.post_id AND positive = FALSE)
    WHERE id = OLD.post_id;
    RETURN NULL;
END;
$$ LANGUAGE plpgsql;

-- New trigger for vote removal
CREATE TRIGGER trigger_update_post_votes_on_delete
AFTER DELETE ON votes
FOR EACH ROW
EXECUTE FUNCTION update_post_votes_on_delete();

--------------
-- Populate --
--------------

INSERT INTO "users" (username, email, name, password, profile_pic, bio, role)
VALUES 
	(
		'admin',
		'admin@clarityquest.com',
		'John Admin',
		'$2y$10$X8PeRk9GNXZjh4EtXEoxLu8j.2/7.0zA5f9/F/aNMfLfud8k.sEuu',
		'profile_pics\1732553856_1.jpg',
		'Software developer and coffee enthusiast.',
		'ADMIN'
	),
	(
		'mod',
		'mod@clarityquest.com',
		'Jane Moderator',
		'$2y$10$1hTUkpDuo.2B4cJ8kLe2P.Sd4oHogLZQCNpZQ7yiqH7QZqD9buz.W',
		'profile_pics\1732553768_2.jpg',
		'Graphic designer. Love art and traveling.',
		'MODERATOR'
	),
	(
		'androe',
		'andy.monroe@gmail.com',
		'Andy Monroe',
		'$2y$10$2lDQ1w4tLD8ZyMRmKUuO/.P9pO8TQR5cL5d76CWGc7PhZv.gG1hy.',
		NULL,
		'PhD student researching Cancer.',
		'REGULAR'
	),
	(
    	'sarah1984',
    	'sarah.connor1984@gmail.com',
    	'Sarah Connor',
    	'$2y$10$2lDQ1w4tLD8ZyMRmKUuO/.P9pO8TQR5cL5d76CWGc7PhZv.gG1hy.',
    	NULL,
    	'Cybersecurity expert and technology enthusiast.',
    	'REGULAR'
	),
	(
    	'mikejo',
    	'mike.jones@example.com',
    	'Mike Jones',
    	'$2y$10$2lDQ1w4tLD8ZyMRmKUuO/.P9pO8TQR5cL5d76CWGc7PhZv.gG1hy.',
    	NULL,
    	'Aspiring chef, food blogger, and recipe creator.',
    	'REGULAR'
	),
	(
    	'lenachez',
    	'elena.sanchez@outlook.com',
    	'Elena Sanchez',
    	'$2y$10$2lDQ1w4tLD8ZyMRmKUuO/.P9pO8TQR5cL5d76CWGc7PhZv.gG1hy.',
    	NULL,
    	'Freelance photographer specializing in landscapes.',
    	'REGULAR'
	),
	(
		'blocked',
		'blocked@clarityquest.com',
		'Rufus Blocked',
		'$2y$10$/2QAbg9J2fAOUuc3xSUYS.ozGgl2NQUj4IzLxyCq48qaJNRmL32ra',
		NULL,
		'I tend to say the truth, and some people can''t handle it.',
		'BLOCKED'
	),
	(
		'user',
		'user@clarityquest.com',
		'Gloria User',
		'$2y$10$nzwtE34LON0vfSFMehBeWOLeNXuS0HDmoEZSWkmIi2SLzVMDUjMai',
		NULL,
		'Just your average gal, nothing much to see around.',
		'REGULAR'
	);

INSERT INTO "tags" (name) VALUES 
('Technology'),
('Health'),
('Education'),
('Science'),
('Travel'),
('Food'),
('Art'),
('Music'),
('Sports'),
('Fitness'),
('Photography'),
('Business'),
('Finance'),
('Programming'),
('Lifestyle');

INSERT INTO "posts" (text, user_id, created_at) VALUES
('What are the best practices for securing a web application?', 1, '2023-08-15 14:22:00'),
('How does blockchain technology work?', 2, '2023-09-02 09:35:00'),
('What is the impact of AI on healthcare?', 3, '2023-10-10 17:50:00'),
('What are some must-visit places in Europe?', 4, '2023-07-20 11:05:00'),
('How do you prepare the perfect sourdough bread?', 5, '2023-05-25 07:30:00'),
('What are the basics of investing in stocks?', 1, '2023-11-01 16:45:00'),
('How can I improve my landscape photography skills?', 6, '2023-03-18 12:15:00'),
('What is the best programming language for beginners?', 2, '2023-06-13 08:20:00'),
('How can I stay consistent with my fitness goals?', 3, '2023-04-05 10:10:00'),
('What are the key challenges in remote work?', 4, '2023-02-28 13:55:00');

INSERT INTO "questions" (id, title) VALUES
(1, 'Best practices for web security'),
(2, 'Understanding blockchain technology'),
(3, 'AI and its impact on healthcare'),
(4, 'Must-visit places in Europe'),
(5, 'Perfecting sourdough bread'),
(6, 'Basics of stock market investing'),
(7, 'Improving landscape photography'),
(8, 'Programming languages for beginners'),
(9, 'Consistency in fitness goals'),
(10, 'Challenges in remote work');

INSERT INTO PostTag (post_id, tag_id) VALUES
(1, 1),
(2, 1),
(2, 5),
(3, 2),
(3, 1),
(4, 5),
(4, 3),
(5, 6),
(5, 4),
(6, 5),
(6, 12),
(7, 11),
(7, 3),
(8, 1),
(9, 10),
(9, 2),
(10, 5),
(10, 3);

INSERT INTO FollowQuestion (user_id, question_id) VALUES
(1, 1),
(2, 1),
(3, 2),
(4, 3),
(5, 3),
(5, 4),
(6, 4),
(1, 5),
(2, 5),
(1, 6),
(2, 6),
(4, 7),
(5, 7),
(3, 8),
(6, 8),
(1, 9),
(2, 9),
(4, 10),
(5, 10);

INSERT INTO "posts" (text, user_id, created_at) VALUES
('The best practices for securing a web application include using HTTPS, validating input, and implementing proper authentication mechanisms.', 2, '2023-08-16 09:00:00'),
('Blockchain works by decentralizing data across a network of computers, making it secure through consensus protocols.', 4, '2023-09-03 10:30:00'),
('AI has the potential to transform healthcare by improving diagnosis, personalizing treatment, and automating tasks.', 5, '2023-10-12 15:00:00'),
('Europe offers a variety of must-visit places such as Paris, Rome, and Amsterdam, each rich in history and culture.', 1, '2023-07-21 08:00:00'),
('To prepare the perfect sourdough bread, you need to carefully manage your starter and monitor the fermentation process.', 5, '2023-05-26 10:00:00'),
('Stock market investing can be approached by diversifying your portfolio, doing thorough research, and understanding risk.', 3, '2023-11-02 14:00:00'),
('Landscape photography involves capturing natural scenes using techniques like proper lighting and composition.', 5, '2023-03-19 13:00:00'),
('In my view, for beginners, Python is an excellent programming language due to its simple syntax and large community support.', 2, '2023-06-14 07:45:00'),
('Staying consistent with fitness goals requires a good routine, tracking progress, and setting achievable milestones.', 1, '2023-04-06 11:30:00'),
('Challenges in remote work include managing time effectively, dealing with isolation, and maintaining productivity.', 6, '2023-02-28 14:10:00'),
('An alternative way to secure web applications is by focusing on zero-trust architecture.', 5, '2023-08-16 14:30:00'),
('Blockchain technology is used to store transactions securely in a distributed ledger.', 6, '2023-09-04 11:20:00'),
('AI in healthcare helps with predictive diagnostics and patient care management.', 1, '2023-10-13 12:30:00'),
('Europe is known for its diverse cultures and historical landmarks that attract millions of tourists each year.', 2, '2023-07-22 09:30:00'),
('When making sourdough bread, hydration is key to getting the perfect crumb texture.', 3, '2023-05-27 13:20:00'),
('Investing in stocks requires understanding market trends and having a long-term perspective.', 4, '2023-11-03 16:00:00'),
('To improve landscape photography, focus on the golden hour for the best lighting.', 1, '2023-03-20 12:45:00'),
('The best programming language for beginners is Python because of its readability and vast support libraries.', 4, '2023-06-15 10:10:00');

INSERT INTO "answers" (id, question_id, correct) VALUES
(11, 1, TRUE),
(12, 2, FALSE),
(13, 3, FALSE),
(14, 4, FALSE),
(15, 5, FALSE),
(16, 6, FALSE),
(17, 7, FALSE),
(18, 8, TRUE),
(19, 9, FALSE),
(20, 10, FALSE),
(21, 1, FALSE),
(22, 2, FALSE),
(23, 3, TRUE),
(24, 4, FALSE),
(25, 5, FALSE),
(26, 6, FALSE),
(27, 7, TRUE),
(28, 8, FALSE);

INSERT INTO FollowTag (user_id, tag_id) VALUES
(1, 1),
(1, 4),
(2, 2),
(2, 6),
(3, 3),
(3, 5),
(4, 7),
(4, 10),
(5, 1),
(5, 8),
(6, 2),
(6, 9),
(6, 11);

INSERT INTO "posts" (text, user_id, created_at) VALUES
('I think using HTTPS is a must for any web application.', 3, '2023-08-16 10:00:00'),
('Blockchain can revolutionize many industries, not just finance.', 4, '2023-09-03 12:00:00'),
('AI could help doctors make more accurate diagnoses faster.', 2, '2023-10-13 09:30:00'),
('Europe has so much culture and history to offer, it’s a dream destination!', 5, '2023-07-22 16:30:00'),
('Sourdough bread is amazing, but it takes a lot of patience!', 6, '2023-06-25 14:45:00'),
('Investing in stocks can be risky, but it’s also very rewarding in the long run.', 2, '2023-11-03 13:15:00'),
('Landscape photography really changes when you focus on the light.', 4, '2023-03-22 18:00:00'),
('Python is very beginner-friendly, I agree with this suggestion.', 5, '2023-06-16 08:50:00'),
('Setting clear goals is so important for staying consistent with fitness.', 6, '2023-04-10 17:00:00'),
('Remote work can be great, but you need strong self-discipline to stay productive.', 1, '2023-02-28 15:30:00');

INSERT INTO "posts" (text, user_id, created_at) VALUES
('I agree that HTTPS is crucial for security, but don’t forget about input validation!', 3, '2023-08-16 11:00:00'),
('Blockchain works by decentralizing data across a network of computers, making it secure through consensus protocols.', 5, '2023-09-03 13:00:00'),
('AI is great, but it should never replace the human touch in healthcare.', 4, '2023-10-13 10:30:00'),
('Europe really does have something for every type of traveler.', 6, '2023-07-22 17:00:00'),
('Sourdough bread is delicious but takes time to perfect.', 1, '2023-06-25 15:15:00'),
('Understanding market trends is essential before investing in stocks.', 3, '2023-11-03 14:00:00'),
('Landscape photography gets so much better with natural light.', 5, '2023-03-22 19:00:00'),
('Python is definitely great for beginners.', 6, '2023-06-16 09:30:00'),
('Staying consistent with your fitness routine is key to seeing results.', 1, '2023-04-10 18:00:00'),
('Remote work isn’t for everyone, but it can be effective with the right tools.', 4, '2023-02-28 16:00:00');

INSERT INTO "comments" (id, post_id) VALUES
(29, 1),
(30, 2),
(31, 3),
(32, 4),
(33, 5),
(34, 6),
(35, 7),
(36, 8),
(37, 9),
(38, 10);

INSERT INTO "comments" (id, post_id) VALUES
(39, 11),
(40, 12),
(41, 13),
(42, 14),
(43, 15),
(44, 16),
(45, 17),
(46, 18),
(47, 19),
(48, 20);

INSERT INTO "votes" (user_id, post_id, positive) VALUES
(1, 1, TRUE),
(1, 2, FALSE),
(1, 3, TRUE),
(1, 4, TRUE),
(1, 5, FALSE),
(1, 6, TRUE),
(1, 7, TRUE),
(1, 8, FALSE),
(1, 9, TRUE),
(1, 10, TRUE),
(2, 11, FALSE),
(2, 12, TRUE),
(2, 13, TRUE),
(2, 14, FALSE),
(2, 15, TRUE),
(2, 16, FALSE),
(2, 17, TRUE),
(2, 18, TRUE),
(2, 19, FALSE),
(2, 20, TRUE),
(3, 21, TRUE),
(3, 22, FALSE),
(3, 23, TRUE),
(3, 24, TRUE),
(3, 25, FALSE),
(3, 26, TRUE),
(3, 27, TRUE),
(3, 28, FALSE),
(3, 29, TRUE),
(3, 30, TRUE),
(4, 31, FALSE),
(4, 32, TRUE),
(4, 33, TRUE),
(4, 34, FALSE),
(4, 35, TRUE),
(4, 36, TRUE),
(4, 37, FALSE),
(4, 38, TRUE),
(4, 39, TRUE),
(5, 40, TRUE),
(5, 41, FALSE),
(5, 42, TRUE),
(5, 43, TRUE),
(5, 44, FALSE),
(5, 45, TRUE),
(5, 46, TRUE),
(5, 47, FALSE),
(5, 48, TRUE),
(6, 1, FALSE),
(6, 2, TRUE),
(6, 3, TRUE),
(6, 4, FALSE),
(6, 5, TRUE),
(6, 6, FALSE),
(6, 7, TRUE),
(6, 8, TRUE),
(6, 9, FALSE),
(6, 10, TRUE);

INSERT INTO "editions" (post_id, old_title, new_title, old, new, made_at) VALUES
(1, 'What are the best practices for securing a web application?', 'How to secure a web app?', 'The best practices for securing a web application include using HTTPS, validating input, and implementing proper authentication mechanisms.', 'Securing a web app requires careful consideration of authentication, encryption, and input validation.', '2023-08-17 10:30:00'),
(3, 'What is the impact of AI on healthcare?', 'How AI is Transforming Healthcare', 'AI has the potential to transform healthcare by improving diagnosis, personalizing treatment, and automating tasks.', 'AI is improving healthcare by automating tasks, enhancing diagnostics, and providing personalized treatment plans.', '2023-10-14 09:00:00'),
(6, 'How do you prepare the perfect sourdough bread?', 'The Art of Sourdough Baking', 'To prepare the perfect sourdough bread, you need to carefully manage your starter and monitor the fermentation process.', 'The key to making perfect sourdough is nurturing your starter and maintaining the right fermentation conditions.', '2023-06-26 11:20:00'),
(10, 'What are the key challenges in remote work?', 'Overcoming Remote Work Challenges', 'Challenges in remote work include managing time effectively, dealing with isolation, and maintaining productivity.', 'Remote work presents challenges such as time management, isolation, and staying productive in an unstructured environment.', '2023-03-01 10:00:00'),
(12, NULL, NULL, 'Blockchain works by decentralizing data across a network of computers, making it secure through consensus protocols.', 'Blockchain ensures security by decentralizing data and using consensus mechanisms.', '2023-09-05 09:40:00'),
(17, NULL, NULL, 'Investing in stocks can be risky, but it’s also very rewarding in the long run.', 'Stock market investing can be risky, but it also offers long-term rewards for informed investors.', '2023-11-04 14:45:00'),
(22, 'What are some must-visit places in Europe?', 'Top European travel destinations', 'Europe offers a variety of must-visit places such as Paris, Rome, and Amsterdam, each rich in history and culture.', 'Europe is home to some amazing cities like Paris, Rome, and Amsterdam, full of rich history and culture.', '2023-07-23 13:10:00'),
(25, NULL, NULL, 'Sourdough bread is amazing, but it takes a lot of patience!', 'Sourdough bread is delicious, but it requires time and patience to perfect.', '2023-06-26 14:10:00'),
(32, NULL, NULL, 'Investing in stocks requires understanding market trends and having a long-term perspective.', 'Stock investing requires a solid understanding of market trends and a patient, long-term approach.', '2023-11-04 15:30:00'),
(39, NULL, NULL, 'Landscape photography involves capturing natural scenes using techniques like proper lighting and composition.', 'Landscape photography relies on perfecting the lighting, composition, and perspective to capture nature’s beauty.', '2023-03-23 12:30:00');

INSERT INTO "reports" (reason, user_id, post_id, created_at) VALUES
('Inappropriate content', 3, 2, '2023-09-05 14:30:00'),
('Spam content', 4, 3, '2023-10-14 12:20:00'),
('Offensive language', 5, 5, '2023-06-27 15:40:00'),
('Irrelevant answer', 6, 8, '2023-06-17 11:15:00'),
('Misleading information', 3, 7, '2023-03-23 16:00:00'),
('Duplicate content', 4, 9, '2023-02-25 13:30:00'),
('Rude comment', 5, 10, '2023-03-01 14:25:00'),
('Incorrect advice', 6, 12, '2023-08-01 10:40:00'),
('Offensive image', 3, 13, '2023-09-10 17:00:00'),
('Not relevant to the topic', 4, 14, '2023-07-22 08:45:00'),
('Personal attack', 5, 15, '2023-06-12 13:50:00'),
('Does not add value', 6, 16, '2023-08-05 19:00:00'),
('Spamming links', 3, 18, '2023-09-03 11:20:00'),
('Unclear and confusing', 4, 20, '2023-02-18 10:30:00'),
('Inappropriate language', 5, 25, '2023-07-01 16:55:00');

INSERT INTO "medals" (user_id, posts_upvoted, posts_created, questions_created, answers_posted) VALUES
(1, 5, 5, 3, 2),
(2, 7, 4, 3, 3),
(3, 6, 5, 4, 3),
(4, 4, 5, 3, 2),
(5, 3, 5, 3, 2),
(6, 8, 5, 2, 3);

INSERT INTO "notifications" (receiver, description, type, sent_at) VALUES
(1, 'A new report has been filed regarding post 3.', 'REPORT', '2023-11-15 08:30:00'),
(2, 'A new report has been filed regarding post 7.', 'REPORT', '2023-11-15 09:15:00'),
(3, 'A new report has been filed regarding post 15.', 'REPORT', '2023-11-15 10:00:00');

INSERT INTO "notifications" (receiver, description, type, sent_at) VALUES
(4, 'Your question "What are the best practices for securing a web application?" has received a new answer.', 'RESPONSE', '2023-08-16 10:15:00'),
(5, 'Your question "What is the impact of AI on healthcare?" has received a new answer.', 'RESPONSE', '2023-10-13 10:00:00'),
(6, 'Your question "What are some must-visit places in Europe?" has received a new answer.', 'RESPONSE', '2023-07-22 17:15:00');

INSERT INTO "notifications" (receiver, description, type, sent_at) VALUES
(3, 'Your answer to "How does blockchain technology work?" has received a new comment.', 'RESPONSE', '2023-09-03 12:45:00'),
(4, 'Your answer to "How do you prepare the perfect sourdough bread?" has received a new comment.', 'RESPONSE', '2023-06-25 15:30:00');

INSERT INTO "notifications" (receiver, description, type, sent_at) VALUES
(1, 'System maintenance scheduled for tonight at midnight. Expect some downtime.', 'OTHER', '2023-11-15 11:00:00'),
(2, 'System maintenance is complete. Everything is back to normal.', 'OTHER', '2023-11-15 14:00:00');

INSERT INTO NotificationUser (notification_id, user_id) VALUES
(1, 1),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(7, 3),
(8, 4),
(9, 1),
(10, 2);

INSERT INTO NotificationPost (notification_id, post_id) VALUES
(1, 3),
(2, 7),
(3, 15),
(4, 1),
(5, 3),
(6, 4),
(7, 2),
(8, 5);
