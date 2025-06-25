CREATE TABLE IF NOT EXISTS links (
                                     code       VARCHAR(12) PRIMARY KEY,
    url        TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL,
    clicks     INTEGER NOT NULL DEFAULT 0,
    expire_at  TIMESTAMP
    );