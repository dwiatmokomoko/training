-- Script untuk membuat database PostgreSQL
-- Jalankan dengan: psql -U postgres -f create_database.sql

-- Buat database
CREATE DATABASE training_need_analysis
    WITH 
    OWNER = postgres
    ENCODING = 'UTF8'
    LC_COLLATE = 'Indonesian_Indonesia.1252'
    LC_CTYPE = 'Indonesian_Indonesia.1252'
    TABLESPACE = pg_default
    CONNECTION LIMIT = -1;

-- Buat user khusus untuk aplikasi (opsional)
-- CREATE USER tna_user WITH PASSWORD 'tna_password';
-- GRANT ALL PRIVILEGES ON DATABASE training_need_analysis TO tna_user;