-- Requêtes SQL courantes pour la plateforme de covoiturage.

-- 1. Trouver les trajets disponibles depuis une ville vers une autre.
SELECT *
FROM trips
WHERE departure_city = 'Montbéliard'
  AND destination_city = 'Strasbourg'
  AND departure_date = '2026-11-12'
  AND status = 'Open';

-- 2. Trouver les trajets avec au moins N places disponibles.
SELECT *
FROM trips
WHERE seats_available >= 2
ORDER BY departure_date, departure_time;

-- 3. Voir les passagers réservés pour un trajet donné.
SELECT u.firstname, u.name, b.seats_booked, b.status
FROM bookings b
JOIN users u ON u.id = b.passenger_id
WHERE b.trip_id = 1;

-- 4. Voir les trajets publiés par un conducteur.
SELECT t.*, v.brand, v.model
FROM trips t
JOIN vehicles v ON v.id = t.vehicle_id
WHERE t.driver_id = 1;

-- 5. Historique des réservations d'un passager.
SELECT t.departure_city, t.destination_city, t.departure_date, b.total_price, b.status
FROM bookings b
JOIN trips t ON t.id = b.trip_id
WHERE b.passenger_id = 2;

-- 6. Note moyenne d'un conducteur.
SELECT driver_id, AVG(rating) AS moyenne_note
FROM reviews
GROUP BY driver_id;

-- 7. Total des montants de paiements sur une période.
SELECT SUM(amount) AS total_payments
FROM payments
WHERE payment_date BETWEEN '2026-10-01' AND '2026-10-31';
