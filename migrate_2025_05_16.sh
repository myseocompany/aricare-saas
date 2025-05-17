#!/bin/bash
set -x
#php artisan migrate --path=database/migrations/2025_05_15_100501_create_rips_services_table.php
#php artisan migrate --path=database/migrations/2025_05_15_103326_create_rips_technology_purposes_table.php
php artisan migrate --path=database/migrations/2025_05_15_110219_create_rips_collection_concept_table.php
php artisan migrate --path=database/migrations/2025_05_15_110806_create_rips_service_reasons_table.php
#php artisan db:seed --class=RipsServiceSeeder
#php artisan db:seed --class=RipsTechnologyPurposesSeeder
php artisan db:seed --class=RipsUserTypeSeeder
php artisan db:seed --class=RipsDepartmentMunicipalitySeeder
echo "Migración ejecutada"
