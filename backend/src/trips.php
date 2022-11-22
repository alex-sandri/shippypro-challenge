<?php

class Flight {
  public string $code_departure;
  public string $code_arrival;
  public int $price;

  public function __construct(
    string $code_departure,
    string $code_arrival,
    int $price,
  ) {
    $this->code_departure = $code_departure;
    $this->code_arrival = $code_arrival;
    $this->price = $price;
  }

  public function __toString(): string {
    return "$this->code_departure;$this->code_arrival;$this->price";
  }
}

class Trip implements JsonSerializable {
  public array $flights;

  public function __construct(array $flights) {
    $this->flights = $flights;
  }

  public function price(): int {
    return array_reduce(
      $this->flights,
      fn(int $amount, Flight $flight) => $amount + $flight->price,
      initial: 0,
    );
  }

  public function jsonSerialize(): mixed {
    return [
      'flights' => $this->flights,
      'price' => $this->price(),
    ];
  }
}

/**
 * List all available flights.
 */
function list_fligths($connection): array {
  $result = pg_query($connection, 'select * from "flight"');

  if (!$result) {
    exit('could not retrieve flights');
  }

  $flights = [];

  while ($row = pg_fetch_assoc($result)) {
    $flights[] = new Flight(
      code_departure: $row['code_departure'],
      code_arrival: $row['code_arrival'],
      price: $row['price'],
    );
  }

  pg_free_result($result);

  return $flights;
}

/**
 * Find all trips that start at `$from` and end at `$to` with at most 2
 * stopovers.
 */
function find_trips($connection, string $from, string $to): array {
  $trips = array_merge(
    find_direct_trips($connection, $from, $to),
    find_indirect_trips($connection, $from, $to),
  );

  return $trips;
}

/**
 * Find all trips that connect `$from` and `$to` directly.
 */
function find_direct_trips($connection, string $from, string $to): array {
  $flights = array_values(
    array_filter(
      list_fligths($connection),
      fn(Flight $flight) =>
        $flight->code_departure === $from && $flight->code_arrival === $to,
    ),
  );

  return array_map(
    fn(Flight $flight) => new Trip([$flight]),
    $flights,
  );
}

/**
 * Find all trips that connect `$from` and `$to` with at most 2
 * stopovers.
 */
function find_indirect_trips($connection, string $from, string $to): array {
  $trips = [];

  $flights = list_fligths($connection);

  // Find all flights that would depart from the desired airport
  $possibleDepartures = array_values(
    array_filter(
      $flights,
      fn(Flight $flight) => $flight->code_departure === $from,
    ),
  );

  // Find all flights that would arrive in the desired airport
  $possibleArrivals = array_values(
    array_filter(
      $flights,
      fn(Flight $flight) => $flight->code_arrival === $to,
    ),
  );

  // Add all trips with one stopover by finding all flights where
  // the arrival of the first is equal to the departure of the last.
  //
  // Example: MXP -> VCE, VCE -> LAX
  for ($i = 0; $i < count($possibleDepartures); $i++) {
    for ($j = 0; $j < count($possibleArrivals); $j++) {
      $departure = $possibleDepartures[$i];
      $arrival = $possibleArrivals[$j];

      if ($departure->code_arrival === $arrival->code_departure) {
        $trips[] = new Trip(flights: [$departure, $arrival]);
      }
    }
  }

  {
    // Remove all flights that are possible departures and possible arrivals as
    // those flights do not qualify as "connecting".
    $possibleConnectingFlights = array_values(
      array_filter(
        $flights,
        fn(Flight $flight) =>
          !in_array($flight, $possibleDepartures) && !in_array($flight, $possibleArrivals),
      ),
    );

    // Add all trips with two stopovers by finding all flights that
    // can connect a flight in $possibleDepartures with one in $possibleArrivals.
    //
    // Example: MXP -> VCE, VCE -> JFK, JFK -> LAX
    for ($i = 0; $i < count($possibleConnectingFlights); $i++) {
      $connectingFlight = $possibleConnectingFlights[$i];

      $possibleDepartureTrips = find_direct_trips($connection, $from, $connectingFlight->code_departure);
      $possibleArrivalTrips = find_direct_trips($connection, $connectingFlight->code_arrival, $to);

      if (empty($possibleDepartureTrips) || empty($possibleArrivalTrips)) {
        continue;
      }

      for ($j = 0; $j < count($possibleDepartureTrips); $j++) {
        for ($k = 0; $k < count($possibleArrivalTrips); $k++) {
          $departure = $possibleDepartureTrips[$j]->flights[0];
          $arrival = $possibleArrivalTrips[$k]->flights[0];

          // Skip useless flights.
          //
          // Examples:
          // - MXP -> VCE, VCE -> MXP, MXP -> VCE
          // - MXP -> CDG, CDG -> MXP, MXP -> FRA
          if ($departure->code_departure === $arrival->code_departure) {
            continue;
          }

          $trips[] = new Trip(flights: [$departure, $connectingFlight, $arrival]);
        }
      }
    }
  }

  return $trips;
}
