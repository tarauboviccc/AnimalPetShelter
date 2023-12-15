-- drop table statements
DROP TABLE ItemPrice;
DROP TABLE AdopterPC;
DROP TABLE PostDateAndType;
DROP TABLE AnimalCaretakerPC;
DROP TABLE Vet;
DROP TABLE ItemPurchase;
DROP TABLE Item;
DROP TABLE Donation;
DROP TABLE Appointment;
DROP TABLE PetAdopter;
DROP TABLE AdoptionDetails;
DROP TABLE Post;
DROP TABLE Volunteer;
DROP TABLE Worker;
DROP TABLE VetAppointment;
DROP TABLE Animal;
DROP TABLE Adopter;
DROP TABLE Customer;
DROP TABLE AnimalCaretaker;
DROP TABLE FundraiserEvent;

-- create table statements
CREATE TABLE FundraiserEvent (eventID INTEGER GENERATED ALWAYS AS IDENTITY CONSTRAINT fe_pk PRIMARY KEY, eventType VARCHAR2(50), eventDayTime DATE CONSTRAINT fe_u UNIQUE, donationGoal NUMBER);

CREATE TABLE AnimalCaretaker (caretakerID INTEGER GENERATED ALWAYS AS IDENTITY CONSTRAINT ac_pk PRIMARY KEY, caretakerName VARCHAR2(100), fundEventID INTEGER CONSTRAINT ac_fk_fe REFERENCES FundraiserEvent(eventID), caretakerAddress VARCHAR2(50), caretakerPostalCode VARCHAR2(8));

CREATE TABLE Customer (customerID INTEGER GENERATED ALWAYS AS IDENTITY CONSTRAINT c_pk PRIMARY KEY, customerName VARCHAR2(100));

CREATE TABLE Adopter (adopterID INTEGER CONSTRAINT a_pk PRIMARY KEY REFERENCES Customer(customerID) ON DELETE CASCADE, numOfAdoptions INTEGER, safeOwnerRating INTEGER, adopterPostalCode VARCHAR2(8), adopterAddress VARCHAR2(50));

CREATE TABLE Animal (petID INTEGER GENERATED ALWAYS AS IDENTITY CONSTRAINT animal_pk PRIMARY KEY, animalName VARCHAR2(50), type VARCHAR2(25), age INTEGER, favouriteCaretaker INTEGER CONSTRAINT a_fk_fc REFERENCES AnimalCaretaker(caretakerID), previousOwner INTEGER CONSTRAINT a_fk_po NOT NULL REFERENCES Customer(customerID), arrivalDate DATE CONSTRAINT animal_nn NOT NULL, adopterID INTEGER CONSTRAINT a_fk_aid REFERENCES Adopter(adopterID));

CREATE TABLE VetAppointment (vetDayTime DATE CONSTRAINT va_pk PRIMARY KEY, vetLicenseID INTEGER, reason VARCHAR2(250), petID INTEGER CONSTRAINT va_fk REFERENCES Animal(petID) ON DELETE CASCADE);

CREATE TABLE Worker (workerID INTEGER CONSTRAINT w_pk PRIMARY KEY REFERENCES AnimalCaretaker(caretakerID) ON DELETE CASCADE, hourlyPay INTEGER);

CREATE TABLE Volunteer (volunteerID INTEGER CONSTRAINT v_pk PRIMARY KEY REFERENCES AnimalCaretaker(caretakerID) ON DELETE CASCADE, hoursVolunteered INTEGER);

CREATE TABLE Post (postID INTEGER GENERATED ALWAYS AS IDENTITY CONSTRAINT p_pk PRIMARY KEY, postType VARCHAR2(25), description VARCHAR2(100), postingDate DATE CONSTRAINT p_u UNIQUE NOT NULL, caretakerID INTEGER CONSTRAINT p_fk_ac REFERENCES AnimalCaretaker(caretakerID));

CREATE TABLE AdoptionDetails (adoptionID INTEGER GENERATED ALWAYS AS IDENTITY CONSTRAINT ad_pk PRIMARY KEY, petID INTEGER CONSTRAINT ad_fk_u UNIQUE REFERENCES Animal(petID) ON DELETE CASCADE, adopterID INTEGER CONSTRAINT ad_fk_aid REFERENCES Adopter(adopterID), caretakerID INTEGER CONSTRAINT ad_fk_ac REFERENCES AnimalCaretaker(caretakerID), adoptionDate DATE, notes VARCHAR2(200));

CREATE TABLE PetAdopter (petID INTEGER CONSTRAINT pa_pk PRIMARY KEY REFERENCES Animal(petID) ON DELETE CASCADE, adopterID INTEGER CONSTRAINT pa_fk_aid REFERENCES Adopter(adopterID));

CREATE TABLE Appointment (petID INTEGER CONSTRAINT appt_fk_p REFERENCES Animal(petID) ON DELETE CASCADE, caretakerID INTEGER CONSTRAINT appt_fk_ac REFERENCES AnimalCaretaker(caretakerID), customerID INTEGER CONSTRAINT appt_fk_c REFERENCES Customer(customerID) ON DELETE CASCADE, apptDayTime DATE CONSTRAINT appt_nn NOT NULL, CONSTRAINT appt_pk PRIMARY KEY (petID, caretakerID, customerID));

CREATE TABLE Donation (customerID INTEGER CONSTRAINT d_fk_c REFERENCES Customer(customerID), caretakerID INTEGER CONSTRAINT d_fk_ac REFERENCES AnimalCaretaker(caretakerID), amount INTEGER, CONSTRAINT d_pk PRIMARY KEY (customerID, caretakerID));

CREATE TABLE Item (itemID INTEGER GENERATED ALWAYS AS IDENTITY CONSTRAINT i_pk PRIMARY KEY, itemName VARCHAR2(25), quantity INTEGER);

CREATE TABLE ItemPurchase (customerID INTEGER CONSTRAINT ip_fk_c REFERENCES Customer(customerID), caretakerID INTEGER CONSTRAINT ip_fk_ac REFERENCES AnimalCaretaker(caretakerID), itemID INTEGER CONSTRAINT ip_fk_i REFERENcES Item(itemID), CONSTRAINT ip_pk PRIMARY KEY (customerID, caretakerID, itemID));

CREATE TABLE Vet (vetLicenseID INTEGER GENERATED ALWAYS AS IDENTITY CONSTRAINT vet_pk PRIMARY KEY, vetName VARCHAR2(50));

CREATE TABLE AnimalCaretakerPC (caretakerPostalCode VARCHAR2(8) CONSTRAINT acpc_pk PRIMARY KEY, caretakerCity VARCHAR2(25));

CREATE TABLE PostDateAndType (postingDate DATE CONSTRAINT pdat_pk PRIMARY KEY, postType VARCHAR2(50));

CREATE TABLE AdopterPC (adopterPostalCode VARCHAR2(8) CONSTRAINT apc_pk PRIMARY KEY, adopterCity VARCHAR2(25));

CREATE TABLE ItemPrice (itemID INTEGER CONSTRAINT itp_pk PRIMARY KEY REFERENCES Item(itemID), total INTEGER);

-- insert statements
INSERT INTO FundraiserEvent (eventType, eventDayTime, donationGoal) VALUES ('Charity Auction', to_date('2023/11/01 18:00', 'YYYY/MM/DD HH24:MI'), 5000);
INSERT INTO FundraiserEvent (eventType, eventDayTime, donationGoal) VALUES ('Pet Walkathon', to_date('2023/11/15 10:00', 'YYYY/MM/DD HH24:MI'), 3000);
INSERT INTO FundraiserEvent (eventType, eventDayTime, donationGoal) VALUES ('Adoption Fair', to_date('2023/11/30 14:30', 'YYYY/MM/DD HH24:MI'), 2000);
INSERT INTO FundraiserEvent (eventType, eventDayTime, donationGoal) VALUES ('Pet Costume Contest', to_date('2023/12/10 15:00', 'YYYY/MM/DD HH24:MI'), 2500);
INSERT INTO FundraiserEvent (eventType, eventDayTime, donationGoal) VALUES ('Animal Rescue Gala', to_date('2023/12/25 19:00', 'YYYY/MM/DD HH24:MI'), 7000);

INSERT INTO AnimalCaretaker (caretakerName, fundEventID, caretakerAddress, caretakerPostalCode) VALUES ('John Peters', 1, '123 Main St', '12345');
INSERT INTO AnimalCaretaker (caretakerName, fundEventID, caretakerAddress, caretakerPostalCode) VALUES ('Mary Johnson', 2, '456 Elm St', '67890');
INSERT INTO AnimalCaretaker (caretakerName, fundEventID, caretakerAddress, caretakerPostalCode) VALUES ('David Perks', 3, '789 Oak St', '34567');
INSERT INTO AnimalCaretaker (caretakerName, fundEventID, caretakerAddress, caretakerPostalCode) VALUES ('Elaine Brown', 4, '101 Pine St', '87654');
INSERT INTO AnimalCaretaker (caretakerName, fundEventID, caretakerAddress, caretakerPostalCode) VALUES ('Michael Wilson', 5, '234 Maple St', '43210');
INSERT INTO AnimalCaretaker (caretakerName, fundEventID, caretakerAddress, caretakerPostalCode) VALUES ('Emily Anderson', NULL, '123 Oak Lane', '12345');
INSERT INTO AnimalCaretaker (caretakerName, fundEventID, caretakerAddress, caretakerPostalCode) VALUES ('Chris Martinez', NULL, '789 Pine St', '43210');
INSERT INTO AnimalCaretaker (caretakerName, fundEventID, caretakerAddress, caretakerPostalCode) VALUES ('Jasmine Walker', NULL, '101 Maple St', '34567');
INSERT INTO AnimalCaretaker (caretakerName, fundEventID, caretakerAddress, caretakerPostalCode) VALUES ('Ryan Turner', NULL, '201 Cedar Drive', '43210');
INSERT INTO AnimalCaretaker (caretakerName, fundEventID, caretakerAddress, caretakerPostalCode) VALUES ('Morgan Foster', NULL, '234 Birch Blvd', '67890');

INSERT INTO Customer (customerName) VALUES ('Alice Johnson');
INSERT INTO Customer (customerName) VALUES ('Bob Smith');
INSERT INTO Customer (customerName) VALUES ('Carol Davis');
INSERT INTO Customer (customerName) VALUES ('David Wilson');
INSERT INTO Customer (customerName) VALUES ('Eve Brown'); 

INSERT INTO Adopter (adopterID, numOfAdoptions, safeOwnerRating, adopterPostalCode, adopterAddress) VALUES (1, 2, 4, '12345', '123 Elm St');
INSERT INTO Adopter (adopterID, numOfAdoptions, safeOwnerRating, adopterPostalCode, adopterAddress) VALUES (2, 0, 3, '23456', '456 Oak St'); 
INSERT INTO Adopter (adopterID, numOfAdoptions, safeOwnerRating, adopterPostalCode, adopterAddress) VALUES (3, 1, 5, '34567', '789 Pine St');
INSERT INTO Adopter (adopterID, numOfAdoptions, safeOwnerRating, adopterPostalCode, adopterAddress) VALUES (4, 3, 4, '45678', '101 Maple St');
INSERT INTO Adopter (adopterID, numOfAdoptions, safeOwnerRating, adopterPostalCode, adopterAddress) VALUES (5, 2, 4, '56789', '234 Birch St');

INSERT INTO Animal (animalName, type, age, favouriteCaretaker, previousOwner, arrivalDate, adopterID) VALUES ('Fluffy', 'Cat', 2, 3, 4, to_date('2023/11/01', 'YYYY/MM/DD'), 1);
INSERT INTO Animal (animalName, type, age, favouriteCaretaker, previousOwner, arrivalDate, adopterID) VALUES ('Rex', 'Dog', 3, 1, 4, to_date('2023/03/11', 'YYYY/MM/DD'), 2);
INSERT INTO Animal (animalName, type, age, favouriteCaretaker, previousOwner, arrivalDate, adopterID) VALUES ('Whiskers', 'Cat', 5, 2, 5, to_date('2022/01/02', 'YYYY/MM/DD'), 3);
INSERT INTO Animal (animalName, type, age, favouriteCaretaker, previousOwner, arrivalDate, adopterID) VALUES ('Buddy', 'Bunny', 4, 4, 3, to_date('2020/10/14', 'YYYY/MM/DD'), 4);
INSERT INTO Animal (animalName, type, age, favouriteCaretaker, previousOwner, arrivalDate, adopterID) VALUES ('Luna', 'Dog', 1, 3, 1, to_date('2023/06/06', 'YYYY/MM/DD'), 5);
INSERT INTO Animal (animalName, type, age, favouriteCaretaker, previousOwner, arrivalDate, adopterID) VALUES ('Domino', 'Hamster', 1, 3, 1, to_date('2022/02/23', 'YYYY/MM/DD'), NULL);
INSERT INTO Animal (animalName, type, age, favouriteCaretaker, previousOwner, arrivalDate, adopterID) VALUES ('Patch', 'Dog', 2, 5, 4, to_date('2023/04/07', 'YYYY/MM/DD'), NULL);
INSERT INTO Animal (animalName, type, age, favouriteCaretaker, previousOwner, arrivalDate, adopterID) VALUES ('Pirate', 'Cat', 2, 4, 4, to_date('2021/05/11', 'YYYY/MM/DD'), NULL);
INSERT INTO Animal (animalName, type, age, favouriteCaretaker, previousOwner, arrivalDate, adopterID) VALUES ('Cloudy', 'Bunny', 3, 2, 2, to_date('2023/11/11', 'YYYY/MM/DD'), NULL);
INSERT INTO Animal (animalName, type, age, favouriteCaretaker, previousOwner, arrivalDate, adopterID) VALUES ('Smoothie', 'Bunny', 3, 2, 2, to_date('2023/10/31', 'YYYY/MM/DD'), NULL);

INSERT INTO VetAppointment (vetDayTime, vetLicenseID, reason, petID) VALUES (to_date('2023/10/20 14:00', 'YYYY/MM/DD HH24:MI'), 1, 'Checkup', 1);
INSERT INTO VetAppointment (vetDayTime, vetLicenseID, reason, petID) VALUES (to_date('2023/10/21 11:00', 'YYYY/MM/DD HH24:MI'), 2, 'Vaccination', 2);
INSERT INTO VetAppointment (vetDayTime, vetLicenseID, reason, petID) VALUES (to_date('2023/10/22 09:00', 'YYYY/MM/DD HH24:MI'), 3, 'Dental cleaning', 3);
INSERT INTO VetAppointment (vetDayTime, vetLicenseID, reason, petID) VALUES (to_date('2023/10/23 17:00', 'YYYY/MM/DD HH24:MI'), 4, 'Spaying', 4);
INSERT INTO VetAppointment (vetDayTime, vetLicenseID, reason, petID) VALUES (to_date('2023/10/24 14:00', 'YYYY/MM/DD HH24:MI'), 5, 'Checkup', 5);

INSERT INTO Worker (workerID, hourlyPay) VALUES (1, 15);
INSERT INTO Worker (workerID, hourlyPay) VALUES (2, 17);
INSERT INTO Worker (workerID, hourlyPay) VALUES (3, 14); 
INSERT INTO Worker (workerID, hourlyPay) VALUES (4, 18); 
INSERT INTO Worker (workerID, hourlyPay) VALUES (5, 16);

INSERT INTO Volunteer (volunteerID, hoursVolunteered) VALUES (6, 20);
INSERT INTO Volunteer (volunteerID, hoursVolunteered) VALUES (7, 25); 
INSERT INTO Volunteer (volunteerID, hoursVolunteered) VALUES (8, 18);
INSERT INTO Volunteer (volunteerID, hoursVolunteered) VALUES (9, 30); 
INSERT INTO Volunteer (volunteerID, hoursVolunteered) VALUES (10, 22); 

INSERT INTO Post (postType, description, postingDate, caretakerID) VALUES ('Announcement', 'Adoption event this weekend!', to_date('2023/10/25', 'YYYY/MM/DD'), 1);
INSERT INTO Post (postType, description, postingDate, caretakerID) VALUES ('News', 'New arrivals in the shelter', to_date('2023/10/26', 'YYYY/MM/DD'), 2);
INSERT INTO Post (postType, description, postingDate, caretakerID) VALUES ('Update', 'Vet check-ups for all animals', to_date('2023/10/27', 'YYYY/MM/DD'), 3); 
INSERT INTO Post (postType, description, postingDate, caretakerID) VALUES ('Event', 'Volunteer appreciation day', to_date('2023/10/28', 'YYYY/MM/DD'), 3);
INSERT INTO Post (postType, description, postingDate, caretakerID) VALUES ('Adoption', 'Adopt a furry friend today', to_date('2023/10/29', 'YYYY/MM/DD'), 1);

INSERT INTO AdoptionDetails (petID, adopterID, caretakerID, adoptionDate, notes) VALUES (1, 1, 2, to_date('2023/10/20', 'YYYY/MM/DD'), 'friendly cat');
INSERT INTO AdoptionDetails (petID, adopterID, caretakerID, adoptionDate, notes) VALUES (2, 2, 2, to_date('2023/10/21', 'YYYY/MM/DD'), 'playful dog');
INSERT INTO AdoptionDetails (petID, adopterID, caretakerID, adoptionDate, notes) VALUES (3, 3, 3, to_date('2023/10/22', 'YYYY/MM/DD'), 'loud cat');
INSERT INTO AdoptionDetails (petID, adopterID, caretakerID, adoptionDate, notes) VALUES (4, 4, 2, to_date('2023/10/23', 'YYYY/MM/DD'), 'really soft bunny'); 
INSERT INTO AdoptionDetails (petID, adopterID, caretakerID, adoptionDate, notes) VALUES (5, 5, 5, to_date('2023/10/24', 'YYYY/MM/DD'), 'quiet dog');
INSERT INTO AdoptionDetails (petID, adopterID, caretakerID, adoptionDate, notes) VALUES (6, 5, 2, to_date('2023/10/25', 'YYYY/MM/DD'), 'interesting hamster');

INSERT INTO PetAdopter (petID, adopterID) VALUES (1, 1);
INSERT INTO PetAdopter (petID, adopterID) VALUES (2, 2); 
INSERT INTO PetAdopter (petID, adopterID) VALUES (3, 3); 
INSERT INTO PetAdopter (petID, adopterID) VALUES (4, 4); 
INSERT INTO PetAdopter (petID, adopterID) VALUES (5, 5);
INSERT INTO PetAdopter (petID, adopterID) VALUES (6, 5);

INSERT INTO Appointment (petID, caretakerID, customerID, apptDayTime) VALUES (6, 2, 1, to_date('2023/01/15 10:00', 'YYYY/MM/DD HH24:MI'));
INSERT INTO Appointment (petID, caretakerID, customerID, apptDayTime) VALUES (7, 2, 2, to_date('2023/02/20 14:30', 'YYYY/MM/DD HH24:MI'));
INSERT INTO Appointment (petID, caretakerID, customerID, apptDayTime) VALUES (8, 3, 3, to_date('2023/03/10 11:45', 'YYYY/MM/DD HH24:MI'));
INSERT INTO Appointment (petID, caretakerID, customerID, apptDayTime) VALUES (9, 4, 4, to_date('2023/04/07 16:15', 'YYYY/MM/DD HH24:MI'));
INSERT INTO Appointment (petID, caretakerID, customerID, apptDayTime) VALUES (10, 5, 5, to_date('2023/05/12 09:30', 'YYYY/MM/DD HH24:MI'));

INSERT INTO Donation (customerID, caretakerID, amount) VALUES (1, 1, 100);
INSERT INTO Donation (customerID, caretakerID, amount) VALUES (2, 2, 150);
INSERT INTO Donation (customerID, caretakerID, amount) VALUES (3, 3, 200); 
INSERT INTO Donation (customerID, caretakerID, amount) VALUES (4, 4, 50);
INSERT INTO Donation (customerID, caretakerID, amount) VALUES (5, 5, 75);

INSERT INTO Item (itemName, quantity) VALUES ('Pet Food', 100);
INSERT INTO Item (itemName, quantity) VALUES ('Blankets', 50);
INSERT INTO Item (itemName, quantity) VALUES ('Toys', 75);
INSERT INTO Item (itemName, quantity) VALUES ('Medicine', 25);
INSERT INTO Item (itemName, quantity) VALUES ('Leashes', 30);

INSERT INTO ItemPurchase (customerID, caretakerID, itemID) VALUES (1, 1, 3);
INSERT INTO ItemPurchase (customerID, caretakerID, itemID) VALUES (2, 1, 5); 
INSERT INTO ItemPurchase (customerID, caretakerID, itemID) VALUES (3, 3, 1); 
INSERT INTO ItemPurchase (customerID, caretakerID, itemID) VALUES (4, 5, 4); 
INSERT INTO ItemPurchase (customerID, caretakerID, itemID) VALUES (5, 3, 5);
INSERT INTO ItemPurchase (customerID, caretakerID, itemID) VALUES (2, 3, 5);
INSERT INTO ItemPurchase (customerID, caretakerID, itemID) VALUES (3, 2, 2);
INSERT INTO ItemPurchase (customerID, caretakerID, itemID) VALUES (4, 1, 4);
INSERT INTO ItemPurchase (customerID, caretakerID, itemID) VALUES (1, 3, 5);

INSERT INTO Vet (vetName) VALUES ('Dr. Allan');
INSERT INTO Vet (vetName) VALUES ('Dr. Papper');
INSERT INTO Vet (vetName) VALUES ('Dr. Lorde');
INSERT INTO Vet (vetName) VALUES ('Dr. Levette'); 
INSERT INTO Vet (vetName) VALUES ('Dr. Michaels');

INSERT INTO AnimalCaretakerPC (caretakerPostalCode, caretakerCity) VALUES ('12345', 'Narnia');
INSERT INTO AnimalCaretakerPC (caretakerPostalCode, caretakerCity) VALUES ('67890', 'Atlantis');
INSERT INTO AnimalCaretakerPC (caretakerPostalCode, caretakerCity) VALUES ('34567', 'Brokeburn');
INSERT INTO AnimalCaretakerPC (caretakerPostalCode, caretakerCity) VALUES ('87654', 'Lancaster');
INSERT INTO AnimalCaretakerPC (caretakerPostalCode, caretakerCity) VALUES ('43210', 'Columbus');

INSERT INTO PostDateAndType (postingDate, postType) VALUES (to_date('2023/10/25', 'YYYY/MM/DD'), 'Announcement');
INSERT INTO PostDateAndType (postingDate, postType) VALUES (to_date('2023/10/26', 'YYYY/MM/DD'), 'News');
INSERT INTO PostDateAndType (postingDate, postType) VALUES (to_date('2023/10/27', 'YYYY/MM/DD'), 'Update'); 
INSERT INTO PostDateAndType (postingDate, postType) VALUES (to_date('2023/10/28', 'YYYY/MM/DD'), 'Event');
INSERT INTO PostDateAndType (postingDate, postType) VALUES (to_date('2023/10/29', 'YYYY/MM/DD'), 'Adoption');

INSERT INTO AdopterPC (adopterPostalCode, adopterCity) VALUES ('12345', 'Narnia');
INSERT INTO AdopterPC (adopterPostalCode, adopterCity) VALUES ('23456', 'Atlantis'); 
INSERT INTO AdopterPC (adopterPostalCode, adopterCity) VALUES ('34567', 'Brokeburn'); 
INSERT INTO AdopterPC (adopterPostalCode, adopterCity) VALUES ('45678', 'Lancaster');
INSERT INTO AdopterPC (adopterPostalCode, adopterCity) VALUES ('56789', 'Columbus');

INSERT INTO ItemPrice (itemID, total) VALUES (5, 200);
INSERT INTO ItemPrice (itemID, total) VALUES (2, 100);
INSERT INTO ItemPrice (itemID, total) VALUES (1, 150);
INSERT INTO ItemPrice (itemID, total) VALUES (4, 75);
INSERT INTO ItemPrice (itemID, total) VALUES (3, 90);

commit;
