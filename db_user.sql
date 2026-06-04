CREATE USER IF NOT EXISTS 'app_user'@'%' IDENTIFIED BY 'strongpassword';
GRANT ALL PRIVILEGES ON steman_alumni.* TO 'app_user'@'%';
FLUSH PRIVILEGES;
