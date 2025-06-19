-- Add isAdmin column to customerlogon table
ALTER TABLE customerlogon
ADD COLUMN isAdmin BOOLEAN DEFAULT FALSE;

-- Set all existing users to non-admin
UPDATE customerlogon SET isAdmin = FALSE;

-- Create an admin user
-- First, create the login credentials to get an auto-generated CustomerID
INSERT INTO customerlogon (UserName, Pass, Salt, State, Type, DateJoined, DateLastModified, isAdmin)
VALUES (
    'admin',
    '$2y$10$qpTSgUnhROIBPLnFTNU3OexvQ/fMmtEVZpaB1TJaAXP3CPVgl9XHO', -- AdminPassword123!
    'qpTSgUnhROIBPLnFTNU3Oe',
    1,
    1,
    NOW(),
    NOW(),
    TRUE
);

-- Get the auto-generated CustomerID and insert customer data
INSERT INTO customers (CustomerID, FirstName, LastName, Address, City, Region, Country, Postal, Phone, Email)
VALUES (
    LAST_INSERT_ID(),
    'Admin', 
    'User', 
    '123 Admin Street', 
    'Admin City', 
    'Admin Region', 
    'Admin Country', 
    '12345', 
    '+492555012355', 
    'admin@artgallery.com'
);
