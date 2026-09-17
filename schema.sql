CREATE DATABASE mlatech;

USE mlatech;

CREATE TABLE IF NOT EXISTS users (
    id         CHAR(36)     NOT NULL DEFAULT (UUID()),
    name       VARCHAR(50)  NOT NULL,
    email      VARCHAR(150) NOT NULL,
    password   VARCHAR(255) NOT NULL,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email)
);

CREATE TABLE IF NOT EXISTS posts (
    id         CHAR(36)     NOT NULL DEFAULT (UUID()),
    title      VARCHAR(255) NOT NULL,
    content    TEXT         NOT NULL,    
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    user_id    CHAR(36)     NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE DATABASE mlatech CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE mlatech;

CREATE TABLE IF NOT EXISTS users (
    id         CHAR(36)     NOT NULL DEFAULT (UUID()),
    name       VARCHAR(50)  NOT NULL,
    email      VARCHAR(150) NOT NULL,
    password   VARCHAR(255) NOT NULL,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email)
);



CREATE TABLE IF NOT EXISTS categories (
    id         CHAR(36)     NOT NULL DEFAULT (UUID()),
    name       VARCHAR(50)  NOT NULL,
    icon       VARCHAR(10)  NULL, -- emoji que se muestra en la card (ej: 🧠)
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY uq_categories_name (name)
);

CREATE TABLE IF NOT EXISTS products (
    id          CHAR(36)      NOT NULL DEFAULT (UUID()),
    category_id CHAR(36)      NOT NULL,
    name        VARCHAR(150)  NOT NULL,
    description TEXT          NULL,
    price       DECIMAL(10,2) NOT NULL,
    stock       INT UNSIGNED  NOT NULL DEFAULT 0,
    image_url   VARCHAR(255)  NULL,
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    KEY idx_products_category (category_id),
    CONSTRAINT fk_products_category
        FOREIGN KEY (category_id) REFERENCES categories(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);