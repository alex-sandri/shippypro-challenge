import './TripOverview.css';

export interface Trip {
  flights: Flight[];
  price: number;
}

interface Flight {
  code_departure: string;
  code_arrival: string;
  price: number;
}

interface Props {
  trip: Trip;
}

function TripOverview(props: Props) {
  const { trip } = props;

  const formatPrice = (value: number) =>
    Intl.NumberFormat(undefined, { style: 'currency', currency: 'EUR' })
      .format(value / 100);

  return (
    <div className='TripOverview'>
      <h3>&#9992; Trip overview</h3>
      <p className='summary-item'>
        <i>Price: </i>
        <b>{formatPrice(trip.price)}</b>
      </p>
      {
        trip.flights.length > 1 &&
          <p className='summary-item'>
            <i>Number of connections: </i>
            <b>{trip.flights.length - 1}</b>
          </p>
      }
      <hr />
      <h4>Flights</h4>
      {
        trip.flights.map((flight, index) => {
          return <div key={index} className='flight'>
            <p>{flight.code_departure} &rarr; {flight.code_arrival}</p>
            <small>
              <b>{formatPrice(flight.price)}</b>
            </small>
          </div>;
        })
      }
    </div>
  );
}

export default TripOverview;
