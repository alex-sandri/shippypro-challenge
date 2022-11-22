# ShippyPro challenge

## Requirements

- `Docker`
- `npm`

## Setup

Run `docker compose up` to create and start the necessary containers.

> I decided to use Docker as it facilitates the creation and reproduction
> of the development environment.

### Create and seed database

1. Go to `localhost:8080` to access `adminer`
2. Enter this values:
  - `System`: `PostgreSQL`
  - `Server`: `db`
  - `Username`: `postgres`
  - `Password`: `password`
3. Click on `Create database`
4. Enter `shippypro` into the text field
5. Click on `Save`
6. Click on `SQL command`
7. Copy and paste the contents of [`db.sql`](./backend/db.sql) into the text field
8. Click on `Execute`

The setup should have been completed successfully.

## Use the app

1. Go into the `frontend` folder from the terminal
2. Run `npm i` to install the required dependencies
3. Run `npm run dev` to start serving the app locally
4. Go to `localhost:8000` from your browser
