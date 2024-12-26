-- @block
CREATE DATABASE TaskFlow
USE TaskFlow;

-- @block
CREATE TABLE users (
    full_name VARCHAR(255) PRIMARY KEY
);
-- @block
CREATE TABLE tasks (
    task_ID INT AUTO_INCREMENT PRIMARY KEY,   
    assigned_to VARCHAR(255),                 
    type VARCHAR(50),                          
    title VARCHAR(255) NOT NULL,               
    description TEXT,                          
    status VARCHAR(50),                        
    FOREIGN KEY (assigned_to) REFERENCES users(full_name)
);
-- @block
Insert into users(full_name) VALUES ('You');
-- @block
SELECT * FROM tasks;
