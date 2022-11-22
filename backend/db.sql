create table "airport" (
  "id" serial not null,
  "name" text not null,
  "code" char(3) not null,
  "lat" decimal(8, 6) not null,
  "lng" decimal(9, 6) not null,

  primary key("id"),

  unique("code"),

  check("code" ~ '^[A-Z]{3}$'),
  check("lat" between -90 and 90),
  check("lng" between -180 and 180)
);

create table "flight" (
  "code_departure" char(3) not null,
  "code_arrival" char(3) not null,
  "price" int not null,

  primary key("code_departure", "code_arrival"),

  foreign key("code_departure") references "airport"("code"),
  foreign key("code_arrival") references "airport"("code"),

  check("price" > 0)
);

insert into "airport"
  ("name", "code", "lat", "lng")
values
  ('Milano Malpensa', 'MXP', 45.630606, 8.728111),
  ('Venezia Marco Polo', 'VCE', 45.505278, 12.351944),
  ('Los Angeles International Airport', 'LAX', 33.942536, -118.408075),
  ('John F. Kennedy International Airport', 'JFK', 40.639751, -73.778925),
  ('O''Hare International Airport', 'ORD', 41.978603, -87.904842),
  ('Frankfurt Airport', 'FRA', 50.026421, 8.543125),
  ('Paris Charles de Gaulle Airport', 'CDG', 49.012779, 2.548925),
  ('Singapore Changi Airport', 'SIN', 1.350189, 103.994433);

insert into "flight"
  ("code_departure", "code_arrival", "price")
values
  ('MXP', 'VCE', 10000),
  ('VCE', 'MXP', 10000),
  ('MXP', 'LAX', 65000),
  ('LAX', 'JFK', 30000),
  ('JFK', 'VCE', 48000),
  ('ORD', 'FRA', 40000),
  ('VCE', 'FRA', 10500),
  ('CDG', 'MXP', 13000),
  ('MXP', 'CDG', 15000),
  ('CDG', 'FRA', 12000),
  ('FRA', 'JFK', 52000),
  ('FRA', 'CDG', 12500),
  ('FRA', 'SIN', 85000),
  ('SIN', 'LAX', 63000),
  ('LAX', 'ORD', 22000);
