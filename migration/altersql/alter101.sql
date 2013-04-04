ALTER TABLE question 
ADD COLUMN inputfile_url VARCHAR(255) NULL AFTER content,
ADD COLUMN difficulty INT NOT NULL AFTER content;
