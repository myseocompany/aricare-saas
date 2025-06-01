#!/bin/bash
set -x
php artisan migrate --path=database/migrations/2025_05_14_223234_create_rips_gender_types_table.php
php artisan migrate --path=database/migrations/2025_05_14_232012_create_rips_territorial_zone_types_table.php

php artisan db:seed --class=RipsGenderTypesSeeder
php artisan db:seed --class=RipsTerritorialZoneTypesSeeder
echo "Migración ejecutada ./migrate_2025_05_14.sh"

#chmod +x migrate_2025_05_14.sh

php artisan migrate --path=database/migrations/2025_05_31_101315_create_rips_billing_documents_table.php
php artisan migrate --path=database/migrations/2025_05_31_102013_create_rips_billing_document_types_table.php
php artisan migrate --path=database/migrations/2025_05_31_104112_add_xml_path_to_rips_billing_documents_table.php
