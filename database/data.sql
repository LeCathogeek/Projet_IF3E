-- Données de démonstration.
-- Les mots de passe doivent être remplacés par des valeurs hachées réelles avec password_hash().
-- Exemple : $2y$10$... (généré en PHP).

INSERT INTO users (name, firstname, email, password, phone_number, birthdate, account_creation_date, account_status, profile_picture)
VALUES
    ('Admin', 'Sophie', 'admin@example.com', '$2y$10$demoHashAdminReplaceMe', '0123456789', '1990-02-14', '2026-09-01', 1, ''),
    ('Dupont', 'Paul', 'paul@example.com', '$2y$10$demoHashUserReplaceMe', '0612345678', '1995-05-19', '2026-09-05', 0, ''),
    ('Martin', 'Claire', 'claire@example.com', '$2y$10$demoHashUser2ReplaceMe', '0678901234', '1992-11-03', '2026-09-12', 0, '');

INSERT INTO vehicles (owner_id, brand, model, registration_number, seat_count, color, year)
VALUES
    (1, 'Peugeot', '308', 'AA-123-AA', 5, 'Noir', 2021),
    (2, 'Renault', 'Clio', 'BB-456-BB', 4, 'Bleu', 2019),
    (3, 'Citroën', 'C3', 'CC-789-CC', 5, 'Blanc', 2020);

INSERT INTO trips (driver_id, vehicle_id, departure_city, destination_city, departure_date, departure_time, seats_available, price_per_seat, status)
VALUES
    (1, 1, 'Montbéliard', 'Strasbourg', '2026-11-12', '08:00', 3, 15.00, 'Open'),
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
    (3, 1, 3, 5, 'Très bon conducteur, trajet agréable et ponctuel.'),
    (1, 2, 1, 4, 'Bonne route, voiture propre.');
