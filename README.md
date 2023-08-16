## About the Project

This is a simple task manager application created in Laravel
This application requires PHP ^8.1

## Installation RUN the commands below in the terminal
- cd ~/path/to/the/directory/where/you/download/the/project
- cp .env.example .env
- composer install
- docker compose build
- docker compose ps -a (To see the list of containers started) 
- docker compose up -d
- docker exec -it Insider_php /bin/sh
- Create your database
- Copy .env.example and rename it to .env
- Add the database configuration to the .env file
- Run the migrations (php artisan migrate)
- php artisan league numOfTeamToPlay (This will run the league and create the weeks based on the number of clubs you desire)
- Copy the url:8000 (see how to use the application below)


## HOW to use the application
- To access the application .........http://0.0.0.0:8000/premier/league


