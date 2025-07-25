#!/bin/bash
set -x

php artisan migrate --path=database/migrations/2025_05_31_235100_create_rips_payer_types_table.php
php artisan migrate --path=database/migrations/2025_05_31_235101_create_rips_payers_table.php
php artisan migrate --path=database/migrations/2025_06_01_005035_create_rips_tenant_payer_agreements_table.php

php artisan migrate --path=database/migrations/2025_06_01_005807_add_agreement_id_to_rips_billing_documents_table.php
php artisan migrate --path=database/migrations/2025_06_01_005035_create_rips_tenant_payer_agreements_table.php
php artisan migrate --path=database/migrations/2025_05_31_102013_create_rips_billing_document_types_table.php
php artisan migrate --path=database/migrations/2025_05_31_101315_create_rips_billing_documents_table.php
php artisan migrate --path=database/migrations/2025_05_31_104112_add_xml_path_to_rips_billing_documents_table.php
php artisan migrate --path=database/migrations/2025_05_31_225752_add_billing_document_id_to_rips_patient_services_table.php


php artisan migrate --path=database/migrations/2025_05_19_050234_change_id_column_type_in_patients_table.php

php artisan db:seed --class=RipsPayerTypeSeeder

echo "Migración ejecutada ./migrate_2025_05_31.sh"
# chmod +x migrate_2025_05_31.sh

php artisan db:seed --class=RipsGenderTypesSeeder

php artisan db:seed --class=RipsTerritorialZoneTypeSeeder


php artisan migrate --path=database/migrations/2025_06_01_231459_add_rips_diagnosis_type_id_to_rips_patient_service_consultation_diagnoses_table.php


php artisan migrate --path=database/migrations/2025_07_18_185043_add_requires_fev_to_rips_patient_services_table.php
php artisan migrate --path=database/migrations/2025_07_18_200438_add_rips_service_id_to_rips_patient_service_procedures_table.php


php artisan db:seed --class=RipsAdmissionRouteSeeder
php artisan db:seed --class=CupsTableSeeder
php artisan db:seed --class=Cie10Seeder
php artisan db:seed --class=RipsServiceReasonSeeder
php artisan db:seed --class=RipsServiceSeeder


php artisan migrate --path=database/migrations/2025_07_19_062005_create_rips_patient_service_templates_structure.php

php artisan migrate --path=database/migrations/2025_06_30_135806_create_rips_statuses_table.php
php artisan migrate --path=database/migrations/2025_07_06_231635_add_rips_fields_to_tenants_table.php
php artisan migrate --path=database/migrations/2025_07_09_023102_add_submission_status_to_rips_billing_documents_table.php