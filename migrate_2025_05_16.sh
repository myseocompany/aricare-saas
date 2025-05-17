#!/bin/bash
set -x
php artisan migrate --path=database/migrations/2025_05_09_195952_create_rips_service_group_table.php
php artisan migrate --path=database/migrations/2025_05_09_183904_create_rips_service_group_mode_table.php
#php artisan migrate --path=database/migrations/2025_05_15_100501_create_rips_services_table.php
#php artisan migrate --path=database/migrations/2025_05_15_103326_create_rips_technology_purposes_table.php
#php artisan migrate --path=database/migrations/2025_05_15_110219_create_rips_collection_concept_table.php
#php artisan migrate --path=database/migrations/2025_05_15_110806_create_rips_service_reasons_table.php
#php artisan migrate --path=database/migrations/2025_05_17_010755_create_rips_admission_routes_table.php

php artisan db:seed --class=RipsServiceGroupSeeder
php artisan db:seed --class=RipsServiceGroupModeSeeder

#php artisan db:seed --class=RipsServiceSeeder
#php artisan db:seed --class=RipsTechnologyPurposesSeeder
#php artisan db:seed --class=RipsServiceReasonSeeder
#php artisan db:seed --class=RipsCollectionConceptSeeder
#php artisan db:seed --class=RipsAdmissionRouteSeeder
echo "Migración ejecutada ./migrate_2025_05_16.sh"

# chmod +x migrate_2025_05_14.sh
