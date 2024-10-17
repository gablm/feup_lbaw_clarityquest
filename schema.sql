CREATE TABLE Post (
    id BIGSERIAL PRIMARY KEY,
    text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT NOW(),
    votes INT DEFAULT 0,
    user_id INT REFERENCES Users(id)
);

CREATE TABLE Question (
    id BIGSERIAL PRIMARY KEY REFERENCES Post(id),
    title TEXT NOT NULL
);

CREATE TABLE Answer (
    id BIGSERIAL PRIMARY KEY REFERENCES Post(id),
    question_id BIGSERIAL NOT NULL REFERENCES Question(id)
);

CREATE TABLE CommentToAnswer (
    id BIGSERIAL PRIMARY KEY REFERENCES Post(id),
    answer_id BIGSERIAL NOT NULL REFERENCES Answer(id)
);

CREATE TABLE CommentToQuestion (
    id BIGSERIAL PRIMARY KEY REFERENCES Post(id),
    question_id BIGSERIAL NOT NULL REFERENCES Question(id)
);