# PHP Mini RDBMS

A minimal relational database management system built entirely from scratch in PHP.

## Overview
This project implements the core concepts of a relational database, including schema definition, constraints, indexing, joins, and persistence, with a SQL-like interface and interactive REPL.

The goal is clarity of design and correctness rather than production completeness.

## Features
- SQL-like syntax
- CREATE TABLE with schema definition
- PRIMARY KEY and UNIQUE constraints
- INSERT, SELECT, UPDATE, DELETE
- WHERE clause filtering
- Hash-based indexing
- JOIN support (nested-loop join)
- Interactive CLI REPL
- Web-based CRUD demo
- File-based persistence (JSON)

## Supported SQL
```sql
CREATE TABLE users (id INT PRIMARY KEY, email TEXT UNIQUE, name TEXT)
INSERT INTO users VALUES (1, 'a@test.com', 'Alice')
SELECT * FROM users WHERE id = 1
UPDATE users SET name = 'Bob' WHERE id = 1
DELETE FROM users WHERE id = 1

SELECT * FROM users JOIN orders ON users.id = orders.user_id


Architecture

Tables stored as JSON files

Constraints enforced at insert time

Indexes implemented as in-memory hash maps

JOINs implemented using nested-loop joins

No external libraries or frameworks

Trade-offs

No transactions or concurrency control

No query optimizer

Indexes are rebuilt per runtime

Limited SQL grammar

Designed for learning and demonstration, not production use

Running locally
php repl/repl.php

Web demo

Deploy to any PHP-enabled web server and open:

/web/index.php

Purpose

This project was built to demonstrate understanding of database internals, systems thinking, and the ability to design and implement core infrastructure from first principles.

I have deployed it on live server