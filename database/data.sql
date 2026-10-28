INSERT INTO users (name, firstname, email, password, phone_number, birthdate, account_creation_date, account_status, profile_picture)
VALUES
    ('Pacault', 'Etienne', 'etienne.pacault@protonmail.com', '$2y$10$Bq27GswrhA26ymS.UOlTfuJ2Bm4go4aQONWm5o01fMCfR3MjAdxeW', '0634667396', '2008-09-29', '2026-09-17', 1, ''),
    ('Dupont', 'Paul', 'paul.dupont@example.com', '$2y$10$c19ryAkYZb.ptm5Stf9z1.3mlX9GtBDA31cgdx3vCv2g53vX1XMxu', '0612345678', '2026-10-15', '2026-10-28', 0, ''),
    ('Dubois', 'Jeanne', 'jeanne.dubois@example.com', '$2y$10$XY9M6trduRYpuS1TK3/Kd.BGsTsFvF9kKtK4/OCrhlmjpGAQedFPm', '0678901234', '2026-10-06', '2026-10-28', 0, '');

INSERT INTO vehicles (owner_id, brand, model, registration_number, seat_count, color, year)
VALUES
    (1, 'Peugeot', '208', 'GB-111-GV', 5, 'Grise', 2022),
    (2, 'Renault', 'Clio', 'BB-456-BB', 4, 'Bleu', 2019),
    (3, 'Citroën', 'C3', 'CC-789-CC', 5, 'Blanc', 2020);

INSERT INTO trips (driver_id, vehicle_id, departure_city, destination_city, departure_date, departure_time, seats_available, price_per_seat, status)
VALUES
    (1, 1, 'Sevenans', 'Pontarlier', '2026-10-28', '08:00', 3, 15.00, 'Open'),
    (2, 2, 'Besançon', 'Mulhouse', '2026-11-13', '09:30', 2, 12.50, 'Open'),
    (3, 3, 'Belfort', 'Colmar', '2026-11-15', '07:45', 4, 14.00, 'Open');

INSERT INTO bookings (trip_id, passenger_id, seats_booked, booking_date, total_price, status)
VALUES
    (1, 2, 2, '2026-10-20', 30.00, 'Confirmed'),
    (2, 3, 1, '2026-10-22', 12.50, 'Pending'),
    (3, 1, 2, '2026-10-23', 28.00, 'Completed');

INSERT INTO payments (booking_id, amount, payment_date, payment_method, payment_status)
VALUES
    (1, 30.00, '2026-10-20', 'Card', 'Paid'),
    (2, 12.50, '2026-10-22', 'Card', 'Pending'),
    (3, 28.00, '2026-10-23', 'Bank transfer', 'Paid');

INSERT INTO reviews (trip_id, passenger_id, driver_id, rating, comment)
VALUES
    (3, 1, 3, 5, 'Excellent trajet ! Nous avons bien discuté et le conducteur était très agréable.'),
    (1, 2, 1, 1, 'Le trajet était abominable, n allez pas chez ce fou.');
