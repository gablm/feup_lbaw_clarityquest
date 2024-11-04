--PERFORMANCE INDEXES --

-- Index connecting Post with user_id

CREATE INDEX user_post ON Post USING hash (user_id);

-- Index connecting Vote and post_id

CREATE INDEX post_vote ON VOTE USING btree (post_id);

-- Index connecting Answer and question_id

CREATE INDEX answer_question ON ANSWER USING btree (question_id);