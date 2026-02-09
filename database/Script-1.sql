USE small_project;

CREATE TABLE users(
	id INT AUTO_INCREMENT PRIMARY KEY,
	userFirstName VARCHAR(50),
	userLastName VARCHAR(50),
	username VARCHAR(50),
	password VARCHAR(50),
	creationDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP()
);

CREATE TABLE contacts(
	id INT AUTO_INCREMENT PRIMARY KEY,
	firstName VARCHAR(50),
	lastName VARCHAR(50),
	phoneNumber VARCHAR(15),
	email VARCHAR(50),
	userID INT,
	FOREIGN KEY (userID) REFERENCES users(id)
);

