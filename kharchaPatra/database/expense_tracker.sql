CREATE DATABASE kharchapatra;

USE kharchapatra;

CREATE TABLE users(
user_id INT AUTO_INCREMENT PRIMARY KEY,
first_name VARCHAR(50),
last_name VARCHAR(50),
username VARCHAR(50) UNIQUE,
email VARCHAR(100) UNIQUE,
password VARCHAR(255),
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_name VARCHAR(50) NOT NULL,
    category_type ENUM('Income','Expense') NOT NULL,

    FOREIGN KEY (user_id)
    REFERENCES users(user_id)
    ON DELETE CASCADE
);

CREATE TABLE income (
    income_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,

    source VARCHAR(100) NOT NULL,

    amount DECIMAL(10,2) NOT NULL,

    description TEXT,

    income_date DATE NOT NULL,

    FOREIGN KEY (user_id)
    REFERENCES users(user_id)
    ON DELETE CASCADE,

    FOREIGN KEY (category_id)
    REFERENCES categories(category_id)
    ON DELETE CASCADE
);

SELECT * FROM income;

CREATE TABLE expenses (
    expense_id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NOT NULL,

    category_id INT NOT NULL,

    amount DECIMAL(10,2) NOT NULL,

    description TEXT,

    expense_date DATE NOT NULL,

    FOREIGN KEY (user_id)
    REFERENCES users(user_id)
    ON DELETE CASCADE,

    FOREIGN KEY (category_id)
    REFERENCES categories(category_id)
    ON DELETE CASCADE
);

SELECT * FROM expenses;

CREATE TABLE reports (
    report_id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NOT NULL,

    report_month VARCHAR(20) NOT NULL,

    report_year YEAR NOT NULL,

    total_income DECIMAL(10,2) DEFAULT 0,

    total_expense DECIMAL(10,2) DEFAULT 0,

    total_savings DECIMAL(10,2) DEFAULT 0,

    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)
    REFERENCES users(user_id)
    ON DELETE CASCADE
);

SELECT * FROM reports;

CREATE TABLE savings (
    savings_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    description TEXT,
    saving_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)
    REFERENCES users(user_id)
    ON DELETE CASCADE
);

-- Run this in phpMyAdmin's SQL tab, with your kharchapatra database selected.
-- Adds a 'type' column so categories can be either 'income' or 'expense'.
-- Existing categories are all marked 'expense' by default (safe for your current data).

ALTER TABLE categories
ADD COLUMN type ENUM('income', 'expense') NOT NULL DEFAULT 'expense' AFTER name;

-- Run this in phpMyAdmin's SQL tab, with your kharchapatra database selected.
-- Adds a column to store the uploaded profile picture's filename.

ALTER TABLE users
ADD COLUMN avatar VARCHAR(255) NULL AFTER password;