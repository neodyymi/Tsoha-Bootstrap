CREATE TABLE Player(
  id SERIAL PRIMARY KEY,
  name varchar(50),
  courseId INTEGER REFERENCES Course(id),
  username varchar(15) NOT NULL,
  password varchar(100) NOT NULL,
  admin boolean DEFAULT FALSE
);

CREATE TABLE Buddylist(
  playerId INTEGER REFERENCES Player(id),
  friendId INTEGER REFERENCES Player(id)
  accepted boolean DEFAULT FALSE
);

CREATE TABLE Course_moderators(
  playerId INTEGER REFERENCES Player(id),
  courseId INTEGER REFERENCES Course(id)
);

CREATE TABLE Course(
  id SERIAL PRIMARY KEY,
  name varchar(100) NOT NULL,
  address varchar(300),
  map_link varchar(300),
  description varchar(500)
);

CREATE TABLE Hole(
  id SERIAL PRIMARY KEY,
  courseId INTEGER REFERENCES Course(id),
  name varchar(100),
  par INTEGER NOT NULL DEFAULT 3,
  holeNumber INTEGER NOT NULL,
  inUse boolean DEFAULT TRUE
);

CREATE TABLE Round(
  id SERIAL PRIMARY KEY,
  courseId INTEGER REFERENCES Course(id),
  played DATE
  addedBy INTEGER REFERENCES Player(id)
);

CREATE TABLE Score(
  id SERIAL PRIMARY KEY,
  roundId INTEGER REFERENCES Round(id),
  playerId INTEGER REFERENCES Player(id),
  name varchar(50)
);

CREATE TABLE Score_Hole(
  holeId INTEGER REFERENCES Hole(id),
  scoreId INTEGER REFERENCES Score(id),
  throws INTEGER NOT NULL
);
