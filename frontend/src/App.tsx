import { useEffect, useState } from 'react';
import './App.css';
import SelectAirport from './SelectAirport';
import TripOverview from './TripOverview';

interface Trip {
  flights: Flight[];
  price: number;
}

interface Flight {
  code_departure: string;
  code_arrival: string;
  price: number;
}

function App() {
  const [airports, setAirports] = useState(null);

  const [departureAirport, setDepartureAirport] = useState<string | null>(null);
  const [arrivalAirport, setArrivalAirport] = useState<string | null>(null);

  const [trip, setTrip] = useState<[Trip] | [] | null>(null);
  const [isLoadingTrip, setIsLoadingTrip] = useState(false);

  useEffect(() => {
    if (airports === null) {
      fetch('http://localhost:80')
        .then((response) => response.json())
        .then(setAirports)
        .catch(() => alert('could not fetch airports'));
    }
  });

  const findTrip = async (): Promise<void> => {
    if (departureAirport === null || arrivalAirport === null) {
      return;
    }

    setIsLoadingTrip(true);

    try {
      const response = await fetch(`http://localhost:80?from=${departureAirport}&to=${arrivalAirport}`);
      const json = await response.json();

      setTrip(json);
    } catch {
      alert('could not fetch flights')
    } finally {
      setIsLoadingTrip(false);
    }
  };

  return (
    <div>
      <header>
        <h1>&#9992;&#65039; Flight finder</h1>
      </header>
      <main>
        {
          airports != null &&
            <div id='finder'>
              <SelectAirport
                label='From'
                airports={airports}
                onChange={({ code }) => setDepartureAirport(code)}
              />
              <SelectAirport
                label='To'
                airports={airports}
                onChange={({ code }) => setArrivalAirport(code)}
              />
              <button onClick={findTrip}>Find</button>
            </div>
        }
        {
          isLoadingTrip
            ? <i>Loading...</i>
            : (
              trip != null
                ? trip[0] != null
                  ? <TripOverview trip={trip[0]} />
                  : <span>&#128683; <i>No flights found.</i></span>
                : null
            )
        }
      </main>
    </div>
  );
}

export default App;
