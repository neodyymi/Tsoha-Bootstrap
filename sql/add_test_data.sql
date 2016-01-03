INSERT INTO Course (name, address, maplink, url, description) VALUES ('Siltamäki', 'Pallomäentie, 00740 Helsinki', 'https://www.google.com/maps?q=60.2730,24.9830', 'http://frisbeegolfradat.fi/rata/siltamaen_frisbeegolfrata_helsinki/', 'Kumpuileva, jonkin verran korkeuseroja');
INSERT INTO Course (name, address, maplink, url, description) VALUES ('Talin frisbeegolfpuisto', 'Talinhuippu (Takkatie), 00360 Helsinki', 'http://maps.google.com/?q=60.2131,24.8468', 'http://frisbeegolfradat.fi/rata/talin_frisbeegolfpuisto_helsinki/', 'Talin Tallaajat ry:n talkoovoimin ylläpitämä 18-väyläinen frisbeegolfrata.');
INSERT INTO Course (name, address, url) VALUES ('Kivikon frisbeegolfrata', 'Savikiekontie 8 00940 Helsinki', 'http://frisbeegolfradat.fi/rata/kivikon_frisbeegolfrata/');

INSERT INTO Player (name, courseId, username, password, admin) VALUES ('Jack Bauer', 1, 'jbauer', 'jackisbest', TRUE);
INSERT INTO Player (name, courseId, username, password) VALUES ('Barry Basic', 2, 'barry', 'barry');
INSERT INTO Player (name, username, password) VALUES ('Annie Average', 'average', 'average');

-- INSERT INTO Buddylist (playerId, friendId) VALUES (2, 1);
-- INSERT INTO Buddylist (playerId, friendId, accepted) VALUES (3, 1, TRUE);

INSERT INTO Course_moderators (playerId, courseId) VALUES (1, 1);
INSERT INTO Course_moderators (playerId, courseId) VALUES (1, 2);
INSERT INTO Course_moderators (playerId, courseId) VALUES (2, 2);

INSERT INTO Hole (courseId, par, holeNumber) VALUES (1, 5, 1);
INSERT INTO Hole (courseId, par, holeNumber) VALUES (1, 3, 2);
INSERT INTO Hole (courseId, par, holeNumber) VALUES (1, 3, 3);
INSERT INTO Hole (courseId, par, holeNumber) VALUES (1, 3, 4);
INSERT INTO Hole (courseId, par, holeNumber) VALUES (1, 3, 5);
INSERT INTO Hole (courseId, par, holeNumber) VALUES (1, 3, 6);
INSERT INTO Hole (courseId, par, holeNumber) VALUES (1, 3, 7);
INSERT INTO Hole (courseId, par, holeNumber) VALUES (1, 3, 8);
INSERT INTO Hole (courseId, par, holeNumber) VALUES (1, 3, 9);
INSERT INTO Hole (courseId, par, holeNumber) VALUES (1, 3, 10);
INSERT INTO Hole (courseId, par, holeNumber) VALUES (1, 4, 11);
INSERT INTO Hole (courseId, par, holeNumber) VALUES (1, 3, 12);
INSERT INTO Hole (courseId, par, holeNumber) VALUES (1, 3, 13);
INSERT INTO Hole (courseId, par, holeNumber) VALUES (1, 3, 14);
INSERT INTO Hole (courseId, par, holeNumber) VALUES (1, 3, 15);
INSERT INTO Hole (courseId, par, holeNumber) VALUES (1, 3, 16);
INSERT INTO Hole (courseId, par, holeNumber) VALUES (1, 3, 17);
INSERT INTO Hole (courseId, name, par, holeNumber) VALUES (1, 'The Saari', 4, 18);

INSERT INTO Round (courseId, played, addedBy) VALUES (1, NOW(), 1);

INSERT INTO Score (roundId, playerId) VALUES (1, 1);
INSERT INTO Score (roundId, playerId) VALUES (1, 3);

INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (1, 1, 4);
INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (2, 1, 3);
INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (3, 1, 3);
INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (4, 1, 3);
INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (5, 1, 3);
INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (6, 1, 5);
INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (7, 1, 3);
INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (8, 1, 3);
INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (9, 1, 1);
INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (10, 1, 3);
INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (11, 1, 3);
INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (12, 1, 2);
INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (13, 1, 3);
INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (14, 1, 4);
INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (15, 1, 3);
INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (16, 1, 3);
INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (17, 1, 2);
INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (18, 1, 5);

INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (1, 2, 6);
INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (2, 2, 4);
INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (3, 2, 5);
INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (4, 2, 4);
INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (5, 2, 3);
INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (6, 2, 3);
INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (7, 2, 4);
INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (8, 2, 6);
INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (9, 2, 2);
INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (10, 2, 4);
INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (11, 2, 4);
INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (12, 2, 3);
INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (13, 2, 3);
INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (14, 2, 4);
INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (15, 2, 3);
INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (16, 2, 3);
INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (17, 2, 3);
INSERT INTO Score_Hole (holeId, scoreId, throws) VALUES (18, 2, 5);
