-- the usage of placeholders for table manipulation requires a web-server context. 
-- that is why transactions are in a separate file.

-- Add question
BEGIN TRANSACTION;

SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

INSERT INTO Post (text, user_id)
VALUES ($text, $user_id)
RETURNING id INTO new_post_id;

INSERT INTO Question (title, post_id)
VALUES ($title, $new_post_id)
RETURNING id INTO new_question_id;

COMMIT;

-- Add answer
BEGIN TRANSACTION;

SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

INSERT INTO Post (text, user_id)
VALUES ($text, $user_id)
RETURNING id INTO new_post_id;

INSERT INTO Answer (post_id, question_id)
VALUES (new_post_id, $question_id); 

COMMIT;

-- Add comment
BEGIN TRANSACTION;

SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

INSERT INTO Post (text, user_id)
VALUES ($text, $user_id)
RETURNING id INTO new_post_id;

INSERT INTO Comment(post_id, question_id)
VALUES (new_post_id, $question_id); 

COMMIT;

-- Vote on answer / Vote on question
BEGIN TRANSACTION;

SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

INSERT INTO Vote (user_id, post_id, positive)
VALUES ($user_id, $post_id, $positive)
ON CONFLICT (user_id, post_id) 
DO UPDATE SET positive = EXCLUDED.positive;

UPDATE Post
SET votes = (SELECT COUNT(*) FROM Vote WHERE post_id = $post_id AND positive = TRUE) -
            (SELECT COUNT(*) FROM Vote WHERE post_id = $post_id AND positive = FALSE)
WHERE id = $post_id;

COMMIT;

-- Edit answer / edit comment
BEGIN TRANSACTION;

SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

UPDATE Post
SET text = $new_text, updated_at = NOW()
WHERE id = $post_id AND user_id = $user_id;

COMMIT;

-- Edit question
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

COMMIT;

-- Delete answer
BEGIN TRANSACTION;

SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

DELETE FROM Answer
WHERE post_id = $post_id AND EXISTS (
    SELECT 1 FROM Post WHERE id = $post_id AND user_id = $user_id
);

DELETE FROM Post
WHERE id = $post_id AND user_id = $user_id;

COMMIT;

-- Delete comment
BEGIN TRANSACTION;

SET TRANSACTION ISOLATION LEVEL REPEATABLE READ;

DELETE FROM Comment
WHERE post_id = $post_id AND EXISTS (
    SELECT 1 FROM Post WHERE id = $post_id AND user_id = $user_id
);

DELETE FROM Post
WHERE id = $post_id AND user_id = $user_id;

COMMIT;

-- Edit user profile
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

COMMIT;