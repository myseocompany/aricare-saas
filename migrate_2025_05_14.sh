#!/bin/bash
set -x
php artisan migrate --path=database/migrations/2025_05_14_223234_create_rips_gender_types_table.php
php artisan migrate --path=database/migrations/2025_05_14_232012_create_rips_territorial_zone_types_table.php

php artisan db:seed --class=RipsGenderTypesSeeder
php artisan db:seed --class=RipsTerritorialZoneTypesSeeder
echo "Migración ejecutada ./migrate_2025_05_14.sh"

#chmod +x migrate_2025_05_14.sh
