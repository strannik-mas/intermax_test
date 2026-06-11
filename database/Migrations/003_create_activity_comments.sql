CREATE TABLE activity_comments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    activity_id BIGINT UNSIGNED NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    CONSTRAINT fk_activity_id_activity_comments_foreign
        FOREIGN KEY (activity_id)
            REFERENCES client_activities(id)
            ON DELETE CASCADE
);