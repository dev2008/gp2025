#!/bin/sh

# Generate timestamp for the filename
timestamp=$(date +"%Y-%m-%d_%H-%M-%S")

# Define the output file with the timestamp
output_file_web="prew_${timestamp}.sql"
output_file_analysis="prea_${timestamp}.sql"

# Use specific my.cnf file for credentials
my_cnf_path="/home/alan/.my.cnf"

# Export tables with a prefix and append to the output file
mysql --defaults-extra-file="$my_cnf_path" pre-gp -N -e 'show tables like "b%"' | xargs mysqldump --defaults-extra-file="$my_cnf_path" pre-gp --add-drop-table >> "$output_file_web"
mysql --defaults-extra-file="$my_cnf_path" pre-gp -N -e 'show tables like "f%"' | xargs mysqldump --defaults-extra-file="$my_cnf_path" pre-gp --add-drop-table >> "$output_file_web"
mysql --defaults-extra-file="$my_cnf_path" pre-gp -N -e 'show tables like "g%"' | xargs mysqldump --defaults-extra-file="$my_cnf_path" pre-gp --add-drop-table >> "$output_file_web"
mysql --defaults-extra-file="$my_cnf_path" pre-gp -N -e 'show tables like "i%"' | xargs mysqldump --defaults-extra-file="$my_cnf_path" pre-gp --add-drop-table >> "$output_file_web"
mysql --defaults-extra-file="$my_cnf_path" pre-gp -N -e 'show tables like "z%"' | xargs mysqldump --defaults-extra-file="$my_cnf_path" pre-gp --add-drop-table >> "$output_file_web"
mysql --defaults-extra-file="$my_cnf_path" pre-gp -N -e 'show tables like "n%"' | xargs mysqldump --defaults-extra-file="$my_cnf_path" pre-gp --add-drop-table >> "$output_file_analysis"
mysql --defaults-extra-file="$my_cnf_path" pre-gp -N -e 'show tables like "y%"' | xargs mysqldump --defaults-extra-file="$my_cnf_path" pre-gp --add-drop-table >> "$output_file_analysis"

# Confirm completion
echo "Backup completed. Output files: $output_file_web $output_file_analysis"
