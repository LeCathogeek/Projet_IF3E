-- 1. Find the trips going from one city to another.
SELECT * FROM trips WHERE departure_city = 'Montbéliard'  AND destination_city = 'Strasbourg'  AND departure_date = '2026-11-12'  AND status = 'Open';

-- 2. Find the trips with a certain number of seats.
SELECT * FROM trips WHERE seats_available >= 2 ORDER BY departure_date, departure_time;

-- 3. Get the passengers names for a given trip.
SELECT u.firstname, u.name, b.seats_booked, b.status FROM bookings b JOIN users u ON u.id = b.passenger_id WHERE b.trip_id = 1;

-- 4. See the trips published by a specific driver.
SELECT t.*, v.brand, v.model FROM trips t JOIN vehicles v ON v.id = t.vehicle_id WHERE t.driver_id = 1;

-- 5. Get the booking history of a passenger.
SELECT t.departure_city, t.destination_city, t.departure_date, b.total_price, b.status FROM bookings b JOIN trips t ON t.id = b.trip_id WHERE b.passenger_id = 2;

-- 6. Get the average rating of a driver.
SELECT driver_id, AVG(rating) AS moyenne_note FROM reviews GROUP BY driver_id;

-- 7. Check if a passenger already rated a trip.
SELECT * FROM reviews WHERE trip_id = 1  AND passenger_id = 2;

-- 8. See the reviews given to a driver.
SELECT r.rating, r.comment, u.firstname, u.name, t.departure_city, t.destination_city FROM reviews r JOIN users u ON u.id = r.passenger_id JOIN trips t ON t.id = r.trip_id WHERE r.driver_id = 1;

-- 9. All the funds collected during a certain time.
SELECT SUM(amount) AS total_payments FROM payments WHERE payment_date BETWEEN '2026-10-01' AND '2026-10-31';

