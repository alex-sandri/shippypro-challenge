<?php

require_once './src/airports.php';
require_once './src/trips.php';

$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$connection = pg_connect('postgresql://postgres:password@db:5432/shippypro');

if (!$connection) {
  exit('could not connect to the database');
}

if (empty($from) && empty($to)) {
  $result = list_airports($connection);
} else {
  $trips = find_trips($connection, from: $from, to: $to);

  if (empty($trips)) {
    $result = [];
  } else {
    // Sort trips by price in ascending order so that the first trip
    // in the array is the cheapest one.
    usort($trips, fn(Trip $a, Trip $b) => $a->price() - $b->price());

    $result = [$trips[0]];
  }
}

pg_close($connection);

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

echo json_encode($result);
