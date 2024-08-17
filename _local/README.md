Step build:
- go to folder docker 
- Run cmd : docker-compose build --no-cache
- Run cmd : docker-compose up -d
- Run cmd : docker-compose exec php bash
- Run cmd : composer install
- Run cmd : cp .env.example .env
- Run cmd : php artisan key:generate
- Run cmd : chmod 0777 storage storage/logs storage/logs/laravel.log
- Run cmd : chmod 0777 framework/*
- Update infomation in .env file 

// Copy and import data.sal
docker cp /path/to/yourfile.sql <container_name>:/path/in/container/yourfile.sql
mysql -u root -p
USE access_log;
source ...

Can them bang attestation_options, co 2 cot origin và challenge
C:/Users/ducht/Downloads

php artisan config:cache
