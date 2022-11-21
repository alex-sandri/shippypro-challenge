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
function list_fligths(): array {
  return [
    new Flight(code_departure: 'MXP', code_arrival: 'VCE', price: 10000),
    new Flight(code_departure: 'VCE', code_arrival: 'MXP', price: 10000),
    new Flight(code_departure: 'MXP', code_arrival: 'LAX', price: 65000),
    new Flight(code_departure: 'LAX', code_arrival: 'JFK', price: 30000),
    new Flight(code_departure: 'JFK', code_arrival: 'VCE', price: 48000),
    new Flight(code_departure: 'ORD', code_arrival: 'FRA', price: 40000),
    new Flight(code_departure: 'VCE', code_arrival: 'FRA', price: 10500),
    new Flight(code_departure: 'CDG', code_arrival: 'MXP', price: 13000),
    new Flight(code_departure: 'MXP', code_arrival: 'CDG', price: 15000),
    new Flight(code_departure: 'CDG', code_arrival: 'FRA', price: 12000),
    new Flight(code_departure: 'FRA', code_arrival: 'JFK', price: 52000),
    new Flight(code_departure: 'FRA', code_arrival: 'CDG', price: 12500),
    new Flight(code_departure: 'FRA', code_arrival: 'SIN', price: 85000),
    new Flight(code_departure: 'SIN', code_arrival: 'LAX', price: 63000),
    new Flight(code_departure: 'LAX', code_arrival: 'ORD', price: 22000),
  ];
}

/**
 * Find all trips that start at `$from` and end at `$to` with at most 2
 * stopovers.
 */
function find_trips(string $from, string $to): array {
  $trips = array_merge(
    find_direct_trips($from, $to),
    find_indirect_trips($from, $to),
  );

  return $trips;
}

/**
 * Find all trips that connect `$from` and `$to` directly.
 */
function find_direct_trips(string $from, string $to): array {
  $flights = array_values(
    array_filter(
      list_fligths(),
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
function find_indirect_trips(string $from, string $to): array {
  $trips = [];

  $flights = list_fligths();

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

      $possibleDepartureTrips = find_direct_trips($from, $connectingFlight->code_departure);
      $possibleArrivalTrips = find_direct_trips($connectingFlight->code_arrival, $to);

      if (empty($possibleDepartureTrips) || empty($possibleArrivalTrips)) {
        continue;
      }

      for ($j = 0; $j < count($possibleDepartureTrips); $j++) {
        for ($k = 0; $k < count($possibleArrivalTrips); $k++) {
          $departure = $possibleDepartureTrips[$j]->flights[0];
          $arrival = $possibleArrivalTrips[$k]->flights[0];

          // Skip useless flights.
          //
          // Example: MXP -> VCE, VCE -> MXP, MXP -> VCE
          if ($departure->__toString() === $arrival->__toString()) {
            continue;
          }

          $trips[] = new Trip(flights: [$departure, $connectingFlight, $arrival]);
        }
      }
    }
  }

  return $trips;
}
