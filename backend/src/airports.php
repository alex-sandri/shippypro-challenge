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

function list_airports($connection): array {
  $result = pg_query($connection, 'select * from "airport"');

  if (!$result) {
    exit('could not retrieve airports');
  }

  $airports = [];

  while ($row = pg_fetch_assoc($result)) {
    $airports[] = new Airport(
      id: $row['id'],
      name: $row['name'],
      code: $row['code'],
      lat: $row['lat'],
      lng: $row['lng'],
    );
  }

  pg_free_result($result);

  return $airports;
}
