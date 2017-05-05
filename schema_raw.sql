drop table userAccounts cascade;
create table userAccounts (
        username varchar(25) primary key,
        password varchar(255),
	fName varchar(20),
	lName varchar(20),
	profilePic text
);

drop table wordSearch cascade;
create table wordSearch(
	id SERIAL PRIMARY KEY,
	searchedWord varchar(20),
	username varchar(25) references userAccounts

);

drop table searchResults cascade;
create table searchResults(
	searchId INTEGER references wordSearch(id),
	word_cloud text,
	likes INTEGER DEFAULT 0
);
