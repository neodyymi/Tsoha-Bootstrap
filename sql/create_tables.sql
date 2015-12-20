CREATE TABLE Player(
  id SERIAL PRIMARY KEY,
  name varchar(50),
  course_id INTEGER REFERENCES Course(id),
  username varchar(15) NOT NULL,
  password varchar(100) NOT NULL,
  admin boolean DEFAULT FALSE
);

CREATE TABLE Buddylist(
  player INTEGER REFERENCES Player(id),
  friend INTEGER REFERENCES Player(id)
  accepted boolean DEFAULT FALSE
);

CREATE TABLE Course_moderators(
  player INTEGER REFERENCES Player(id),
  course INTEGER REFERENCES Course(id)
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
  course_id INTEGER REFERENCES Course(id),
  name varchar(100),
  par INTEGER NOT NULL DEFAULT 3,
  hole_number INTEGER NOT NULL,
  in_use boolean DEFAULT TRUE
);

CREATE TABLE Round(
  id SERIAL PRIMARY KEY,
  course_id INTEGER REFERENCES Course(id),
  played DATE
  added_by INTEGER REFERENCES Player(id)
);

CREATE TABLE Score(
  id SERIAL PRIMARY KEY,
  round INTEGER REFERENCES Round(id),
  player INTEGER REFERENCES Player(id),
  name varchar(50)
);

CREATE TABLE Score_Hole(
  hole INTEGER REFERENCES Hole(id),
  score INTEGER REFERENCES Score(id),
  throws INTEGER NOT NULL
);
