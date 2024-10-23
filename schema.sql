DROP TABLE IF EXISTS User;

CREATE TYPE Permission 
AS ENUM('BLOCKED', 'REGULAR', 'MODERATOR', 'ADMIN');

CREATE TABLE User(
	id BIGSERIAL PRIMARY KEY,
	username TEXT UNIQUE NOT NULL,
	name TEXT NOT NULL,
	hashed_password TEXT NOT NULL,
	profile_pic TEXT NOT NULL,
	bio TEXT NOT NULL,
	role PERMISSION DEFAULT 'REGULAR'
);

CREATE TABLE Post(
    id BIGSERIAL PRIMARY KEY,
    text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT NOW(),
    votes INT DEFAULT 0,
    user_id BIGINT,
	FOREIGN KEY (user_id) REFERENCES User(id)
);

CREATE TABLE Question(
    id BIGSERIAL PRIMARY KEY,
    title TEXT NOT NULL,
    post_id BIGINT NOT NULL, 
	FOREIGN KEY (post_id) REFERENCES Post(id)
);

CREATE TABLE Answer(
    id BIGSERIAL PRIMARY KEY,
    post_id BIGINT NOT NULL, 
    question_id BIGINT NOT NULL,
	FOREIGN KEY (post_id) REFERENCES Post(id),
	FOREIGN KEY (question_id) REFERENCES Question(id)
);

CREATE TABLE Comment(
    id BIGSERIAL PRIMARY KEY,
    post_id BIGINT NOT NULL,
	FOREIGN KEY (post_id) REFERENCES Post(id)
);

CREATE TABLE Report(
    id BIGSERIAL PRIMARY KEY,
    reason VARCHAR NOT NULL,
    created_at TIMESTAMP DEFAULT NOW(),
    user_id BIGINT,
    post_id BIGINT,
	FOREIGN KEY (user_id) REFERENCES User(id),
	FOREIGN KEY (post_id) REFERENCES Post(id)
);

CREATE TABLE Tag(
    id BIGSERIAL PRIMARY KEY,
    name TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT NOW()
);

CREATE TABLE PostTag(
    post_id BIGINT NOT NULL, 
    tag_id BIGINT NOT NULL,
    PRIMARY KEY (post_id, tag_id),
	FOREIGN KEY (post_id) REFERENCES Post(id) ON DELETE CASCADE,
	FOREIGN KEY (tag_id) REFERENCES Tag(id) ON DELETE CASCADE,
);

CREATE TABLE FollowTag(
    user_id BIGINT NOT NULL, 
    tag_id BIGINT NOT NULL,
    PRIMARY KEY (user_id, tag_id)
	FOREIGN KEY (user_id) REFERENCES User(id) ON DELETE CASCADE,
	FOREIGN KEY (tag_id) REFERENCES Tag(id) ON DELETE CASCADE,
);

CREATE TABLE FollowQuestion(
    user_id BIGINT NOT NULL,
    question_id BIGINT NOT NULL,
    PRIMARY KEY (user_id, question_id),
	FOREIGN KEY (user_id) REFERENCES User(id) ON DELETE CASCADE,
	FOREIGN KEY (question_id) REFERENCES Question(id) ON DELETE CASCADE,
);

CREATE TYPE NotificationType
AS ENUM('RESPONSE', 'REPORT', 'FOLLOW', 'MENTION', 'OTHER');

CREATE TABLE Notification(
    id BIGSERIAL PRIMARY KEY,
    receiver BIGINT NOT NULL,
    description TEXT,
    type NotificationType NOT NULL DEFAULT 'OTHER',
    sent_at TIMESTAMP DEFAULT NOW(),
	FOREIGN KEY (receiver) REFERENCES User(id),
);

CREATE TABLE NotificationPost(
    notification_id BIGINT NOT NULL,
    post_id BIGINT NOT NULL,
    PRIMARY KEY (notification_id, post_id)
	FOREIGN KEY REFERENCES Post(id) ON DELETE CASCADE,
	FOREIGN KEY REFERENCES Notification(id) ON DELETE CASCADE,
);

CREATE TABLE NotificationUser(
    notification_id BIGINT NOT NULL REFERENCES Notification(id) ON DELETE CASCADE,
    user_id BIGINT NOT NULL REFERENCES Users(id) ON DELETE CASCADE,
    PRIMARY KEY (notification_id, user_id)
);

CREATE TABLE Edition(
	id BIGSERIAL PRIMARY KEY,
	old TEXT,
	new TEXT,
	made_at TIMESTAMP DEFAULT NOW()
);

CREATE TABLE Vote(
	user_id BIGINT REFERENCES User(id),
	post_id BIGINT REFERENCES Post(id),
	positive BOOLEAN NOT NULL
);

CREATE TABLE Medals(
	user_id BIGINT REFERENCES User(id),
	posts_upvotes BIGINT DEFAULT 0 CHECK (posts_upvotes >= 0),
	posts_created BIGINT DEFAULT 0 CHECK (posts_created >= 0),
	questions_created BIGINT DEFAULT 0 CHECK (questions_created >= 0),
	answers_posted BIGINT DEFAULT 0 CHECK (answers_posted >= 0)
);