# ShippyPro challenge

See [`challenge.pdf`](./challenge.pdf) for the description of the challenge.

## Requirements

- `Docker`
- `npm`

## Setup

Run `docker compose up` to create and start the necessary containers.

> I decided to use Docker as it facilitates the creation and reproduction
> of the development environment.

### Create and seed the database

1. Go to `localhost:8080` to access `adminer`
2. Enter this values:
    - `System`: `PostgreSQL`
    - `Server`: `db`
    - `Username`: `postgres`
    - `Password`: `password`
3. Click on `Login`
4. Click on `Create database`
5. Enter `shippypro` into the text field
6. Click on `Save`
7. Click on `SQL command`
8. Copy and paste the contents of [`db.sql`](./backend/db.sql) into the text field
9. Click on `Execute`

The setup should have been completed successfully.

## Use the app

1. Go into the `frontend` folder from the terminal
2. Run `npm i` to install the required dependencies
3. Run `npm run dev` to start serving the app locally
4. Go to `localhost:8000` from your browser

### Search flights

1. Select a departure airport from the `FROM` select element
2. Select an arrival airport from the `TO` select element
3. Click on `Find`

## Screenshots

### Initial page

![Initial](./assets/initial.png)

### Trip overview

![Initial](./assets/trip-overview.png)

### No flights found

![Initial](./assets/no-flights.png)
