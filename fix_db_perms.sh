#!/bin/bash
# Direct SQL update in recovery mode for MariaDB 10.6
docker exec steman_db mariadb -u root -e "
FLUSH PRIVILEGES;
SET PASSWORD FOR 'root'@'localhost' = PASSWORD('Ch4v4run3@');
GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' WITH GRANT OPTION;
CREATE USER IF NOT EXISTS 'app_user'@'%' IDENTIFIED BY 'Ch4v4run3@';
ALTER USER 'app_user'@'%' IDENTIFIED BY 'Ch4v4run3@';
GRANT ALL PRIVILEGES ON steman_alumni.* TO 'app_user'@'%';
FLUSH PRIVILEGES;
"
