--PERFORMANCE INDEXES --

-- Index connecting Post with user_id

CREATE INDEX user_post ON Post USING hash (user_id);

-- Index connecting Answer and question_id

CREATE INDEX answer_question ON ANSWER USING hash (question_id);

-- FULL TEXT SEARCH INDEXES --

-- searches users by username

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

-- searches question by title

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