# TaskMe Documentation

## Overview
TaskMe is a simple task management system that allows users to create, read, update, and delete tasks. It is built using Laravel and MySQL.

## Tech Stack
- **Backend**: Laravel (PHP Framework)
- **Database**: MySQL
- **Containerization**: Docker

## Installation
1. Clone the repository:
   ```sh
   git clone git@github.com:SamandarMadaliev/taskme.git
   cd TaskMe
   ```
2. Copy the environment file:
   ```sh
   cp .env.example .env
   ```
3. Start the application using Docker:
   ```sh
   docker compose up -d
   ```


## Usage
- After successfully launching docker containers, requests could be sent to `localhost:9090` 
- The application provides full CRUD operations for managing tasks.
- API endpoints are documented in the `docs/openapi.yml` file.
- Configure environment variables in the `.env` file.

## API Reference
All available API endpoints can be found in the `openapi.yml` file. Use tools like Postman, Swagger UI, or Redoc to explore them.
There is a container that should be running on `localhost:8080` with Swagger.
## Environment Variables
Refer to `.env.example` for all configurable environment variables required to run the application.

## Deployment
To be determined. (Let me know if you need help with this!)

## Contributing
This is a simple project. Contributions and improvements are welcome!

## License
This project is released under the Creative Commons Zero (CC0) license. You are free to use, modify, and distribute it without restriction, while still acknowledging the original authorship if desired.

