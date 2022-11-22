import './SelectAirport.css';

interface Props {
  label: string;
  airports: Airport[];
  onChange: (airport: Airport) => void;
}

interface Airport {
  id: number;
  name: string;
  code: string;
  lat: number;
  lng: number;
}

function SelectAirport(props: Props) {
  const onChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
    const selectedAirport = props.airports
      .find(({ code }) => e.target.value === code);

    props.onChange(selectedAirport!);
  }

  return (
    <div className='SelectAirport'>
      <label>
        {props.label}
        <select defaultValue='null' onChange={onChange}>
          <option value='null' disabled>Select airport</option>
          {
            props.airports.map((airport) => {
              return <option key={airport.id} value={airport.code}>
                {airport.name}
              </option>;
            })
          }
        </select>
      </label>
    </div>
  );
}

export default SelectAirport;
