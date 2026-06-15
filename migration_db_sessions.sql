-- Database-backed PHP sessions so logins survive Railway redeploys/restarts.
-- The container filesystem (where default file sessions live) is wiped on every
-- deploy, which is why everyone gets logged out. Storing sessions here fixes it.
-- Run once on the live database.

CREATE TABLE IF NOT EXISTS sessions (
    id            VARCHAR(128)  NOT NULL PRIMARY KEY,
    data          MEDIUMTEXT    NOT NULL,
    last_activity INT UNSIGNED  NOT NULL,
    KEY idx_last_activity (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
