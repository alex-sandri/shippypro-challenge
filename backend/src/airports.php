<?php

class Airport {
  public int $id;
  public string $name;
  public string $code;
  public float $lat;
  public float $lng;

  public function __construct(
    int $id,
    string $name,
    string $code,
    float $lat,
    float $lng,
  ) {
    $this->id = $id;
    $this->name = $name;
    $this->code = $code;
    $this->lat = $lat;
    $this->lng = $lng;
  }
}

function list_airports(): array {
  return [
    new Airport(
      id: 0,
      name: 'Milano Malpensa',
      code: 'MXP',
      lat: 45.630606,
      lng: 8.728111,
    ),
    new Airport(
      id: 1,
      name: 'Venezia Marco Polo',
      code: 'VCE',
      lat: 45.505278,
      lng: 12.351944,
    ),
    new Airport(
      id: 2,
      name: 'Los Angeles International Airport',
      code: 'LAX',
      lat: 33.942536,
      lng: -118.408075,
    ),
    new Airport(
      id: 3,
      name: 'John F. Kennedy International Airport',
      code: 'JFK',
      lat: 40.639751,
      lng: -73.778925,
    ),
    new Airport(
      id: 4,
      name: "O'Hare International Airport",
      code: 'ORD',
      lat: 41.978603,
      lng: -87.904842,
    ),
    new Airport(
      id: 5,
      name: 'Frankfurt Airport',
      code: 'FRA',
      lat: 50.026421,
      lng: 8.543125,
    ),
    new Airport(
      id: 6,
      name: 'Paris Charles de Gaulle Airport',
      code: 'CDG',
      lat: 49.012779,
      lng: 2.548925,
    ),
    new Airport(
      id: 7,
      name: 'Singapore Changi Airport',
      code: 'SIN',
      lat: 1.350189,
      lng: 103.994433,
    ),
  ];
}
