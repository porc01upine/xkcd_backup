#!/bin/bash
# This script should set up a CRON job to run cron.php every 24 hours.
# You need to implement the CRON setup logic here.

# Absolute path to PHP
PHP_PATH=$(which php)

# Absolute path to cron.php
CRON_FILE="$(pwd)/cron.php"

# Ensure cron.php is executable
chmod +x "$CRON_FILE"

# Define cron job (runs every 24 hours at 9:00 AM)
CRON_JOB="0 9 * * * $PHP_PATH $CRON_FILE"

# Check if the cron job is already installed
(crontab -l 2>/dev/null | grep -F "$CRON_FILE") >/dev/null
if [ $? -eq 0 ]; then
    echo "CRON job already exists."
else
    # Add new CRON job
    (crontab -l 2>/dev/null; echo "$CRON_JOB") | crontab -
    echo "CRON job added to run daily at 9:00 AM."
fi
