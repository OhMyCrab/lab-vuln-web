CREATE DATABASE IF NOT EXISTS ohmycrab
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE ohmycrab;
-- USERS
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100)
    NOT NULL UNIQUE,
    password VARCHAR(255)
    NOT NULL,
    role ENUM('user','admin')
    NOT NULL DEFAULT 'user',
    created_at TIMESTAMP
    DEFAULT CURRENT_TIMESTAMP
);
-- CATEGORIES
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100)
    NOT NULL,
    slug VARCHAR(100)
    NOT NULL UNIQUE
);
-- LABS
CREATE TABLE labs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    title VARCHAR(255)
    NOT NULL,
    slug VARCHAR(255)
    NOT NULL UNIQUE,
    description TEXT,
    objective TEXT,
    is_active TINYINT(1)
    DEFAULT 1,
    created_at TIMESTAMP
    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id)
    REFERENCES categories(id)
    ON DELETE CASCADE
);
-- COMMENTS
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lab_id INT NOT NULL,
    user_id INT NOT NULL,
    content LONGTEXT,
    created_at TIMESTAMP
    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lab_id)
    REFERENCES labs(id)
    ON DELETE CASCADE,
    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE CASCADE
);
-- NOTES
CREATE TABLE notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    note LONGTEXT,
    created_at TIMESTAMP
    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE CASCADE
);
-- USER PROGRESS
CREATE TABLE user_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    lab_id INT NOT NULL,
    completed TINYINT(1)
    DEFAULT 0,
    completed_at TIMESTAMP NULL,
    UNIQUE(user_id, lab_id),
    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE CASCADE,
    FOREIGN KEY (lab_id)
    REFERENCES labs(id)
    ON DELETE CASCADE
);
-- FLAGS
CREATE TABLE flags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lab_id INT NOT NULL,
    flag VARCHAR(255)
    NOT NULL UNIQUE,
    created_at TIMESTAMP
    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lab_id)
    REFERENCES labs(id)
    ON DELETE CASCADE
);
-- JWT BLACKLIST
CREATE TABLE jwt_blacklist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    token_hash VARCHAR(255)
    UNIQUE,
    expired_at TIMESTAMP NULL
);
-- ATTACK LOGS
CREATE TABLE attack_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lab_id INT NULL,
    ip_address VARCHAR(100),
    user_agent TEXT,
    request_method VARCHAR(10),
    endpoint VARCHAR(255),
    payload LONGTEXT,
    status_code INT,
    created_at TIMESTAMP
    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lab_id)
    REFERENCES labs(id)
    ON DELETE SET NULL
);
-- SETTINGS
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100)
    NOT NULL UNIQUE,
    setting_value TEXT
);
-- XSS STORED COMMENTS
CREATE TABLE xss_stored_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100),
    comment TEXT,
    created_at TIMESTAMP
    DEFAULT CURRENT_TIMESTAMP
);
-- SQLI ACCOUNTS
CREATE TABLE sqli_accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100),
    password VARCHAR(100),
    role VARCHAR(50)
);

INSERT INTO sqli_accounts
(
    username,
    password,
    role
)
VALUES
(
    'admin',
    'supersecret',
    'admin'
),

( 
    'caymai',
    'caymai123',
    'user'
),

(
    'kr4v7',
    'crabmeifucan',
    'user'
),

(
    'guest',
    'guest',
    'user'
);
-- INDEXES
CREATE INDEX idx_username
ON users(username);

CREATE INDEX idx_lab_slug
ON labs(slug);

CREATE INDEX idx_category_id
ON labs(category_id);

CREATE INDEX idx_attack_lab
ON attack_logs(lab_id);

CREATE INDEX idx_comments_lab
ON comments(lab_id);

CREATE INDEX idx_comments_user
ON comments(user_id);

CREATE INDEX idx_progress_user
ON user_progress(user_id);

CREATE INDEX idx_progress_lab
ON user_progress(lab_id);

-- DEFAULT USERS
-- password: password
INSERT INTO users
(
    username,
    password,
    role
)
VALUES
(
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/.A0L6y7A5M9a',
    'admin'
),

(
    'test',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/.A0L6y7A5M9a',
    'user'
);
-- CATEGORIES DATA
INSERT INTO categories
(
    name,
    slug
)
VALUES
('XSS', 'xss'),

('SQL Injection', 'sqli'),

('CSRF', 'csrf'),

('SSRF', 'ssrf'),

('JWT', 'jwt'),

('OAuth', 'oauth'),

('XXE', 'xxe'),

('SSTI', 'ssti'),

('Command Injection', 'command_injection'),

('Access Control', 'access_control'),

('Insecure Deserialization', 'insecure_deserialization'),

('Authentication', 'authentication');

-- LABS DATA
INSERT INTO labs
(
    category_id,
    title,
    slug,
    description,
    objective
)
VALUES
(
    1,
    'Stored XSS',
    'stored_xss',
    'Persistent cross site scripting vulnerability.',
    'Execute JavaScript payload stored in comments.'
),

(
    1,
    'Reflected XSS',
    'reflected_xss',
    'Reflected XSS through search parameter.',
    'Execute JavaScript through reflected input.'
),

(
    1,
    'DOM XSS',
    'dom_xss',
    'Client-side DOM manipulation vulnerability.',
    'Exploit unsafe JavaScript sink.'
),

(
    2,
    'In-Band SQL Injection',
    'inband_sqli',
    'Classic SQL injection returning visible results.',
    'Exploit SQL injection to bypass authentication.'
),

(
    2,
    'Blind SQL Injection',
    'blind_sqli',
    'Boolean-based blind SQL injection.',
    'Extract data without visible SQL output.'
),

(
    3,
    'CSRF Password Change',
    'csrf_password_change',
    'Password change without CSRF protection.',
    'Force victim to change password.'
),

(
    4,
    'Basic SSRF',
    'basic_ssrf',
    'Server-side request forgery vulnerability.',
    'Access internal services from server.'
),

(
    5,
    'JWT None Algorithm',
    'jwt_none_algorithm',
    'Weak JWT validation vulnerability.',
    'Forge JWT token with alg none.'
),

(
    6,
    'OAuth Redirect',
    'oauth_redirect',
    'Insecure OAuth redirect_uri validation.',
    'Hijack OAuth authorization flow.'
),

(
    7,
    'Basic XXE',
    'basic_xxe',
    'XML external entity injection.',
    'Read local files through XML parser.'
),

(
    8,
    'SSTI Injection',
    'ssti_injection',
    'Server-side template injection vulnerability.',
    'Execute template expressions on server.'
),

(
    9,
    'Command Injection Ping',
    'command_injection_ping',
    'Operating system command injection.',
    'Execute arbitrary shell commands.'
),

(
    10,
    'Broken Access Control',
    'broken_access_control',
    'Missing authorization validation.',
    'Access restricted resources without permission.'
),

(
    11,
    'PHP Object Injection',
    'php_object_injection',
    'Unsafe unserialize vulnerability.',
    'Exploit insecure deserialization.'
),

(
    12,
    'Weak Authentication',
    'weak_authentication',
    'Weak authentication implementation.',
    'Abuse insecure login logic.'
);
-- FLAGS
INSERT INTO flags
(
    lab_id,
    flag
)
VALUES
(1, 'OHMYCRAB{stored_xss_master}'),

(2, 'OHMYCRAB{reflected_xss_master}'),

(3, 'OHMYCRAB{dom_xss_master}'),

(4, 'OHMYCRAB{in_band_sqli}'),

(5, 'OHMYCRAB{blind_sqli_success}'),

(6, 'OHMYCRAB{csrf_exploited}'),

(7, 'OHMYCRAB{basic_ssrf_success}'),

(8, 'OHMYCRAB{jwt_none_pwned}'),

(9, 'OHMYCRAB{oauth_redirect_hijack}'),

(10, 'OHMYCRAB{xxe_file_read}'),

(11, 'OHMYCRAB{ssti_rce}'),

(12, 'OHMYCRAB{command_injection_success}'),

(13, 'OHMYCRAB{broken_access_control}'),

(14, 'OHMYCRAB{unsafe_deserialization}'),

(15, 'OHMYCRAB{weak_authentication_broken}');
-- SETTINGS
INSERT INTO settings
(
    setting_key,
    setting_value
)
VALUES
('site_name', 'OhMyCrab'),

('theme_color', 'orange'),

('registration_enabled', 'true');