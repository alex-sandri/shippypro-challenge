<?php

require_once './src/airports.php';
require_once './src/trips.php';

$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

if (empty($from) && empty($to)) {
  $result = list_airports();
} else {
  $trips = find_trips(from: $from, to: $to);

  if (empty($trips)) {
    $result = [];
  } else {
    uasort($trips, fn(Trip $a, Trip $b) => $b->price() - $a->price());

    $result = [$trips[0]];
  }
}

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

echo json_encode($result);
