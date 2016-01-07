CREATE TABLE Course(
  id SERIAL PRIMARY KEY,
  name varchar(100) NOT NULL,
  address varchar(300),
  maplink varchar(300),
  url varchar(300),
  description varchar(500),
  added TIMESTAMP DEFAULT now(),
  edited TIMESTAMP DEFAULT now()
);
CREATE TABLE Player(
  id SERIAL PRIMARY KEY,
  name varchar(50),
  courseId INTEGER REFERENCES Course(id) ON DELETE CASCADE,
  username varchar(15) NOT NULL UNIQUE,
  password varchar(255) NOT NULL,
  admin boolean DEFAULT FALSE,
  joined TIMESTAMP DEFAULT now(),
  login TIMESTAMP DEFAULT now()
);
-- CREATE TABLE Buddylist(
--   playerId INTEGER REFERENCES Player(id),
--   friendId INTEGER REFERENCES Player(id),
--   accepted boolean DEFAULT FALSE
-- );
CREATE TABLE Course_moderators(
  playerId INTEGER REFERENCES Player(id) ON DELETE CASCADE,
  courseId INTEGER REFERENCES Course(id) ON DELETE CASCADE,
  PRIMARY KEY(playerId, courseId)
);
CREATE TABLE Hole(
  id SERIAL PRIMARY KEY,
  courseId INTEGER REFERENCES Course(id) ON DELETE CASCADE,
  name varchar(100),
  par INTEGER NOT NULL DEFAULT 3,
  holeNumber INTEGER NOT NULL,
  inUse boolean DEFAULT TRUE,
  UNIQUE(courseId, holeNumber)
);
CREATE TABLE Round(
  id SERIAL PRIMARY KEY,
  courseId INTEGER REFERENCES Course(id) ON DELETE CASCADE,
  played TIMESTAMP DEFAULT NULL,
  added TIMESTAMP DEFAULT now(),
  addedBy INTEGER REFERENCES Player(id) ON DELETE CASCADE
);
CREATE TABLE Score(
  id SERIAL PRIMARY KEY,
  roundId INTEGER REFERENCES Round(id) ON DELETE CASCADE,
  playerId INTEGER REFERENCES Player(id) ON DELETE CASCADE,
  name varchar(50)
);
CREATE TABLE Score_Hole(
  holeId INTEGER REFERENCES Hole(id) ON DELETE CASCADE,
  scoreId INTEGER REFERENCES Score(id) ON DELETE CASCADE,
  throws INTEGER NOT NULL
);
