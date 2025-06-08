-- Fix foreign key constraint in customerlogon
ALTER TABLE customerlogon
DROP FOREIGN KEY fk_customerlogon;

ALTER TABLE customerlogon
ADD CONSTRAINT fk_customerlogon
FOREIGN KEY (CustomerId) REFERENCES customers(CustomerId)
ON DELETE CASCADE ON UPDATE CASCADE;

-- Remove obsolete Salt column (handled by password_hash)
ALTER TABLE customerlogon
DROP COLUMN Salt;
