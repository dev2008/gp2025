#!/bin/sh

# Generate timestamp for the filename
timestamp=$(date +"%Y-%m-%d_%H-%M-%S")

# Define the output file with the timestamp
output_file_web="wdev_${timestamp}.sql"
output_file_analysis="adev_${timestamp}.sql"



# Export tables with a prefix and append to the output file
/opt/lampp/bin/mysql  dev-gp -uroot -pasm69SMM!# -N -e 'show tables like "b%"' | xargs /opt/lampp/bin/mysqldump dev-gp -uroot -pasm69SMM!# --add-drop-table >> "$output_file_web"
/opt/lampp/bin/mysql  dev-gp -uroot -pasm69SMM!# -N -e 'show tables like "f%"' | xargs /opt/lampp/bin/mysqldump dev-gp -uroot -pasm69SMM!# --add-drop-table >> "$output_file_web"
/opt/lampp/bin/mysql  dev-gp -uroot -pasm69SMM!# -N -e 'show tables like "g%"' | xargs /opt/lampp/bin/mysqldump dev-gp -uroot -pasm69SMM!# --add-drop-table >> "$output_file_web"
/opt/lampp/bin/mysql  dev-gp -uroot -pasm69SMM!# -N -e 'show tables like "i%"' | xargs /opt/lampp/bin/mysqldump dev-gp -uroot -pasm69SMM!# --add-drop-table >> "$output_file_web"
/opt/lampp/bin/mysql  dev-gp -uroot -pasm69SMM!# -N -e 'show tables like "z%"' | xargs /opt/lampp/bin/mysqldump dev-gp -uroot -pasm69SMM!# --add-drop-table >> "$output_file_web"
/opt/lampp/bin/mysql  dev-gp -uroot -pasm69SMM!# -N -e 'show tables like "n%"' | xargs /opt/lampp/bin/mysqldump dev-gp -uroot -pasm69SMM!# --add-drop-table >> "$output_file_analysis"
/opt/lampp/bin/mysql  dev-gp -uroot -pasm69SMM!# -N -e 'show tables like "y%"' | xargs /opt/lampp/bin/mysqldump dev-gp -uroot -pasm69SMM!# --add-drop-table >> "$output_file_analysis"

# Confirm completion
echo "Backup completed. Output files: $output_file_web $output_file_analysis"
