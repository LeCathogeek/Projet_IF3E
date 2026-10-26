<?php
function echoMenu(): void {
    echo '<div id="navigation">';
    echo '<ul>';
    echo '<li><a href="dashboard.php">Dashboard</a></li>';
    echo '<li><a href="search_trips.php">Find a trip</a></li>';
    echo '<li><a href="add_vehicle.php">Add a car</a></li>';
    echo '<li><a href="create_trip.php">Publish a trip</a></li>';
    echo '<li><a href="my_trips.php">My trips</a></li>';
    echo '</ul>';
    echo '</div>';
}