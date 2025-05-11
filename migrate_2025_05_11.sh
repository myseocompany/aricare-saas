#!/bin/bash
set -x
php artisan migrate:fresh --seed
php artisan migrate --path=database/migrations/2025_05_09_185059_create_rips_identification_types_table.php
php artisan migrate --path=database/migrations/2025_05_11_063345_create_rips_countries_table.php
php artisan migrate --path=database/migrations/2025_05_10_235208_create_rips_user_types_table.php
php artisan migrate --path=database/migrations/2025_05_11_063345_create_rips_countries_table.php
php artisan migrate --path=database/migrations/2025_05_11_114349_create_rips_departments_table.php
php artisan migrate --path=database/migrations/2025_05_11_065315_create_rips_municipalities_table.php
php artisan db:seed --class=RipsIdentificationTypeSeeder
php artisan db:seed --class=RipsCountrySeeder
php artisan db:seed --class=RipsUserTypeSeeder
php artisan db:seed --class=RipsDepartmentMunicipalitySeeder
echo "Migración ejecutada"
