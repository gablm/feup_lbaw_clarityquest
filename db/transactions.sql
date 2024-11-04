-- the usage of placeholders for table manipulation requires a web-server context. 
-- that is why transactions are in a separate file.

-- Add question
-- Using REPEATABLE READ to ensure consistency while adding a question and its associated post.
-- Higher isolation levels like SERIALIZABLE are not necessary as REPEATABLE READ prevents non-repeatable reads and phantom reads.
BEGIN TRANSACTION;
SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

INSERT INTO Post (text, user_id)
VALUES ($text, $user_id)
RETURNING id INTO new_post_id;

INSERT INTO Question (title, post_id)
VALUES ($title, $new_post_id)
RETURNING id INTO new_question_id;

END TRANSACTION;

-- Add answer
-- Using REPEATABLE READ to ensure consistency while adding an answer and its associated post.
-- Higher isolation levels like SERIALIZABLE are not necessary as REPEATABLE READ prevents non-repeatable reads and phantom reads.
BEGIN TRANSACTION;
SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

INSERT INTO Post (text, user_id)
VALUES ($text, $user_id)
RETURNING id INTO new_post_id;

INSERT INTO Answer (post_id, question_id)
VALUES (new_post_id, $question_id); 

END TRANSACTION;

-- Add comment
-- Using REPEATABLE READ to ensure consistency while adding a comment and its associated post.
-- Higher isolation levels like SERIALIZABLE are not necessary as REPEATABLE READ prevents non-repeatable reads and phantom reads.
BEGIN TRANSACTION;
SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

INSERT INTO Post (text, user_id)
VALUES ($text, $user_id)
RETURNING id INTO new_post_id;

INSERT INTO Comment(post_id, question_id)
VALUES (new_post_id, $question_id); 

END TRANSACTION;

-- Vote on answer / Vote on question
-- Using REPEATABLE READ to ensure consistency while updating votes.
-- Higher isolation levels like SERIALIZABLE are not necessary as REPEATABLE READ prevents non-repeatable reads and phantom reads.
BEGIN TRANSACTION;
SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

INSERT INTO Vote (user_id, post_id, positive)
VALUES ($user_id, $post_id, $positive)
ON CONFLICT (user_id, post_id) 
DO UPDATE SET positive = EXCLUDED.positive;

END TRANSACTION;

-- Edit answer / edit comment
-- Using REPEATABLE READ to ensure consistency while editing an answer or comment.
-- Higher isolation levels like SERIALIZABLE are not necessary as REPEATABLE READ prevents non-repeatable reads and phantom reads.
BEGIN TRANSACTION;
SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

SELECT text AS old_text FROM Post
WHERE id = $post_id;

UPDATE Post
SET text = $new_text, updated_at = NOW()
WHERE id = $post_id AND user_id = $user_id;

INSERT INTO Edition(post_id, old, new)
VALUES ($post_id, $old_text, $new_text);

END TRANSACTION;

-- Edit question
-- Using REPEATABLE READ to ensure consistency while editing a question.
-- Higher isolation levels like SERIALIZABLE are not necessary as REPEATABLE READ prevents non-repeatable reads and phantom reads.
BEGIN TRANSACTION;
SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

IF $new_email IS NOT NULL THEN
	SELECT text AS old_text FROM Post
	WHERE id = $post_id;

	UPDATE Post
	SET text = $new_text, updated_at = NOW()
	WHERE id = $post_id AND user_id = $user_id;
END IF;

IF $new_title IS NOT NULL THEN
	SELECT text AS old_title FROM Question
	WHERE id = $post_id;

	UPDATE Question
	SET  title = $new_title
	WHERE id = $post_id;
END IF;

INSERT INTO Edition(post_id, old_title, new_title, old, new)
VALUES ($post_id, $old_title, $new_title, $old_text, $new_text);

END TRANSACTION;

-- Delete answer
-- Using REPEATABLE READ to ensure consistency while deleting an answer and its associated post.
-- Higher isolation levels like SERIALIZABLE are not necessary as REPEATABLE READ prevents non-repeatable reads and phantom reads.
BEGIN TRANSACTION;
SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

DELETE FROM Answer
WHERE post_id = $post_id AND EXISTS (
    SELECT 1 FROM Post WHERE id = $post_id AND user_id = $user_id
);

END TRANSACTION;

-- Delete comment
-- Using REPEATABLE READ to ensure consistency while deleting a comment and its associated post.
-- Higher isolation levels like SERIALIZABLE are not necessary as REPEATABLE READ prevents non-repeatable reads and phantom reads.
BEGIN TRANSACTION;
SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

DELETE FROM Comment
WHERE post_id = $post_id AND EXISTS (
    SELECT 1 FROM Post WHERE id = $post_id AND user_id = $user_id
);

END TRANSACTION;

-- Edit user profile
-- Using REPEATABLE READ to ensure consistency while editing user profile details.
-- Higher isolation levels like SERIALIZABLE are not necessary as REPEATABLE READ prevents non-repeatable reads and phantom reads.
BEGIN TRANSACTION;
SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

IF $new_name IS NOT NULL THEN
    UPDATE Users
    SET name = $new_name, updated_at = NOW()
    WHERE id = $user_id;
END IF;

IF $new_email IS NOT NULL THEN
    UPDATE Users
    SET email = $new_email, updated_at = NOW()
    WHERE id = $user_id;
END IF;

IF $new_bio IS NOT NULL THEN
    UPDATE Users
    SET bio = $new_bio, updated_at = NOW()
    WHERE id = $user_id;
END IF;

IF $new_hashed_password IS NOT NULL THEN
    UPDATE Users
    SET hashed_password = $new_hashed_password, updated_at = NOW()
    WHERE id = $user_id;
END IF;

END TRANSACTION;

-- Delete account
-- Using REPEATABLE READ to ensure consistency while deleting a user account and all associated data.
-- Higher isolation levels like SERIALIZABLE are not necessary as REPEATABLE READ prevents non-repeatable reads and phantom reads.
BEGIN TRANSACTION;
SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

DELETE FROM Users WHERE id = $1;

END TRANSACTION;

-- Edit question
-- Using REPEATABLE READ to ensure consistency while editing a question and its associated post.
-- Higher isolation levels like SERIALIZABLE are not necessary as REPEATABLE READ prevents non-repeatable reads and phantom reads.
BEGIN TRANSACTION;
SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

IF $new_title IS NOT NULL THEN
    UPDATE Question
    SET title = $new_title
    WHERE id = $question_id AND user_id = $user_id;
END IF;

IF $new_text IS NOT NULL THEN
    UPDATE Post
    SET text = $new_text, updated_at = NOW()
    WHERE id = (SELECT post_id FROM Question WHERE id = $question_id) AND user_id = $user_id;
END IF;

END TRANSACTION;

-- Delete question
-- Using REPEATABLE READ to ensure consistency while deleting a question and all associated data.
-- Higher isolation levels like SERIALIZABLE are not necessary as REPEATABLE READ prevents non-repeatable reads and phantom reads.
BEGIN TRANSACTION;
SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

DELETE FROM Answer WHERE question_id = $1;

END TRANSACTION;

-- Mark the answer as correct for questioner
-- Using REPEATABLE READ to ensure consistency while marking an answer as correct and unmarking others.
-- Higher isolation levels like SERIALIZABLE are not necessary as REPEATABLE READ prevents non-repeatable reads and phantom reads.
BEGIN TRANSACTION;
SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

UPDATE Answer
SET correct = TRUE
WHERE id = $1;

UPDATE Answer
SET correct = FALSE
WHERE question_id = (SELECT question_id FROM Answer WHERE id = $1) AND id != $1;

END TRANSACTION;

-- Edit question tags
-- Using REPEATABLE READ to ensure consistency while editing tags for a question.
-- Higher isolation levels like SERIALIZABLE are not necessary as REPEATABLE READ prevents non-repeatable reads and phantom reads.
BEGIN TRANSACTION;

SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

DELETE FROM PostTag WHERE post_id = (SELECT post_id FROM Question WHERE id = $1);

FOREACH tag_id IN ARRAY $2
LOOP
    INSERT INTO PostTag (post_id, tag_id) VALUES ((SELECT post_id FROM Question WHERE id = $1), tag_id);
END LOOP;

END TRANSACTION;

-- Follow Question
-- Using REPEATABLE READ to ensure consistency while following a question.
-- Higher isolation levels like SERIALIZABLE are not necessary as REPEATABLE READ prevents non-repeatable reads and phantom reads.
BEGIN TRANSACTION;
SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

IF EXISTS (SELECT 1 FROM Question WHERE id = $1) THEN
    INSERT INTO FollowQuestion (user_id, question_id)
    VALUES ($2, $1)
    ON CONFLICT (user_id, question_id) DO NOTHING;
END IF;

END TRANSACTION;

-- Follow Tag
-- Using REPEATABLE READ to ensure consistency while following a tag.
-- Higher isolation levels like SERIALIZABLE are not necessary as REPEATABLE READ prevents non-repeatable reads and phantom reads.
BEGIN TRANSACTION;
SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

IF EXISTS (SELECT 1 FROM Tag WHERE id = $1) THEN
    INSERT INTO FollowTag (user_id, tag_id)
    VALUES ($2, $1)
    ON CONFLICT (user_id, tag_id) DO NOTHING;
END IF;

END TRANSACTION;
