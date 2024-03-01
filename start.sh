#!/bin/sh

# Start the Hyperf application in the background
php bin/hyperf.php start &

# Capture pid process from start Hyperf server appplication
php_pid=$!

# Wait for the application to finish starting
wait_for_postgres() {
    until nc -zv $DB_HOST $DB_PORT &> /dev/null
    do
        echo "Awaiting the connection with the Postgres on $DB_HOST:$DB_PORT..."
        sleep 4
    done
}
wait_for_postgres

# Run the migrations
php bin/hyperf.php migrate

# Bring the Hyperf application to the foreground
echo "Attach HTTP Coroutine Server listening at 0.0.0.0:9501"
wait $php_pid