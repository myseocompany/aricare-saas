{
    "accountants": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "user_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "user_id"
                ],
                "referred_table": "users",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "accounts": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "name",
                "type": "VARCHAR(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "type",
                "type": "TINYINT"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "status",
                "type": "TINYINT(1)"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "addresses": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "owner_id",
                "type": "INTEGER"
            },
            {
                "name": "owner_type",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "address1",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "address2",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "city",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "zip",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "admin_testimonials": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "position",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": []
    },
    "advanced_payments": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "patient_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "receipt_no",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "amount",
                "type": "DOUBLE"
            },
            {
                "name": "date",
                "type": "DATE"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "patient_id"
                ],
                "referred_table": "users",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "ambulance_calls": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "patient_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "ambulance_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "driver_name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "date",
                "type": "DATE"
            },
            {
                "name": "amount",
                "type": "DOUBLE"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "ambulance_id"
                ],
                "referred_table": "ambulances",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "patient_id"
                ],
                "referred_table": "patients",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "ambulances": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "vehicle_number",
                "type": "VARCHAR(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "vehicle_model",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "year_made",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "driver_name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "driver_license",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "driver_contact",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "note",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "is_available",
                "type": "TINYINT(1)"
            },
            {
                "name": "vehicle_type",
                "type": "INTEGER"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "appointment_transactions": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "appointment_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "transaction_type",
                "type": "INTEGER"
            },
            {
                "name": "transaction_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "appointment_id"
                ],
                "referred_table": "appointments",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "appointments": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "patient_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "doctor_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "department_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "opd_date",
                "type": "DATETIME"
            },
            {
                "name": "problem",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "is_completed",
                "type": "TINYINT(1)"
            },
            {
                "name": "payment_status",
                "type": "TINYINT(1)"
            },
            {
                "name": "payment_type",
                "type": "INTEGER"
            },
            {
                "name": "custom_field",
                "type": "LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "department_id"
                ],
                "referred_table": "doctor_departments",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "doctor_id"
                ],
                "referred_table": "doctors",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "patient_id"
                ],
                "referred_table": "patients",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "bed_assigns": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "bed_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "ipd_patient_department_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "patient_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "case_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "assign_date",
                "type": "DATETIME"
            },
            {
                "name": "discharge_date",
                "type": "DATETIME"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "status",
                "type": "TINYINT(1)"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "bed_id"
                ],
                "referred_table": "beds",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "ipd_patient_department_id"
                ],
                "referred_table": "ipd_patient_departments",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "patient_id"
                ],
                "referred_table": "patients",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "bed_types": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "title",
                "type": "VARCHAR(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "beds": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "bed_type",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "bed_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "charge",
                "type": "DOUBLE"
            },
            {
                "name": "is_available",
                "type": "TINYINT(1)"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "bed_type"
                ],
                "referred_table": "bed_types",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "bill_items": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "item_name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "bill_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "qty",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "price",
                "type": "DECIMAL(16, 2)"
            },
            {
                "name": "amount",
                "type": "DECIMAL(16, 2)"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "bill_id"
                ],
                "referred_table": "bills",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "bill_transactions": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "transaction_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "payment_type",
                "type": "INTEGER"
            },
            {
                "name": "amount",
                "type": "DECIMAL(16, 2)"
            },
            {
                "name": "bill_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "status",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "meta",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "is_manual_payment",
                "type": "INTEGER"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "bill_id"
                ],
                "referred_table": "bills",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "bills": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "bill_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "patient_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "bill_date",
                "type": "DATETIME"
            },
            {
                "name": "amount",
                "type": "DECIMAL(16, 2)"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "patient_admission_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "status",
                "type": "TINYINT(1)"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "patient_id"
                ],
                "referred_table": "patients",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "birth_reports": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "patient_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "case_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "doctor_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "date",
                "type": "DATETIME"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "doctor_id"
                ],
                "referred_table": "doctors",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "patient_id"
                ],
                "referred_table": "patients",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "blood_banks": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "blood_group",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "remained_bags",
                "type": "BIGINT"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "blood_donations": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "blood_donor_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "bags",
                "type": "INTEGER"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "blood_donor_id"
                ],
                "referred_table": "blood_donors",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "blood_donors": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "age",
                "type": "INTEGER"
            },
            {
                "name": "gender",
                "type": "INTEGER"
            },
            {
                "name": "blood_group",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "last_donate_date",
                "type": "DATETIME"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "blood_issues": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "issue_date",
                "type": "DATETIME"
            },
            {
                "name": "doctor_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "donor_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "patient_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "amount",
                "type": "DECIMAL(10, 2)"
            },
            {
                "name": "remarks",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "doctor_id"
                ],
                "referred_table": "doctors",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "donor_id"
                ],
                "referred_table": "blood_donors",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "patient_id"
                ],
                "referred_table": "patients",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "brands": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "name",
                "type": "VARCHAR(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "email",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "phone",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "cache": {
        "columns": [
            {
                "name": "key",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "value",
                "type": "MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "expiration",
                "type": "INTEGER"
            }
        ],
        "primary_keys": [
            "key"
        ],
        "foreign_keys": []
    },
    "cache_locks": {
        "columns": [
            {
                "name": "key",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "owner",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "expiration",
                "type": "INTEGER"
            }
        ],
        "primary_keys": [
            "key"
        ],
        "foreign_keys": []
    },
    "call_logs": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "phone",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "date",
                "type": "DATE"
            },
            {
                "name": "follow_up_date",
                "type": "DATE"
            },
            {
                "name": "note",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "call_type",
                "type": "INTEGER"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "case_handlers": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "user_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "user_id"
                ],
                "referred_table": "users",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "categories": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "name",
                "type": "VARCHAR(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "is_active",
                "type": "TINYINT(1)"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "charge_categories": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "name",
                "type": "VARCHAR(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "charge_type",
                "type": "INTEGER"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "charges": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "charge_type",
                "type": "INTEGER"
            },
            {
                "name": "charge_category_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "code",
                "type": "VARCHAR(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "standard_charge",
                "type": "DOUBLE"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "charge_category_id"
                ],
                "referred_table": "charge_categories",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "currency_settings": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "currency_name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "currency_code",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "currency_icon",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "deleted_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "custom_fields": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "module_name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "field_type",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "field_name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "is_required",
                "type": "TINYINT(1)"
            },
            {
                "name": "values",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "grid",
                "type": "INTEGER"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "death_reports": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "patient_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "case_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "doctor_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "date",
                "type": "DATETIME"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "doctor_id"
                ],
                "referred_table": "doctors",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "patient_id"
                ],
                "referred_table": "patients",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "departments": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "is_active",
                "type": "TINYINT(1)"
            },
            {
                "name": "guard_name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": []
    },
    "diagnosis_categories": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "name",
                "type": "VARCHAR(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "doctor_departments": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "title",
                "type": "VARCHAR(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "doctor_holidays": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "doctor_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "date",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "doctor_id"
                ],
                "referred_table": "doctors",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "doctor_opd_charges": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "doctor_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "standard_charge",
                "type": "DOUBLE"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "doctor_id"
                ],
                "referred_table": "doctors",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "doctors": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "user_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "doctor_department_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "specialist",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "appointment_charge",
                "type": "DOUBLE"
            },
            {
                "name": "google_json_file_path",
                "type": "LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "doctor_department_id"
                ],
                "referred_table": "doctor_departments",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "user_id"
                ],
                "referred_table": "users",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "document_types": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "name",
                "type": "VARCHAR(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "documents": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "title",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "document_type_id",
                "type": "INTEGER"
            },
            {
                "name": "patient_id",
                "type": "INTEGER"
            },
            {
                "name": "uploaded_by",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "notes",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "uploaded_by"
                ],
                "referred_table": "users",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "domains": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "domain",
                "type": "VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "employee_payrolls": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "sr_no",
                "type": "BIGINT"
            },
            {
                "name": "payroll_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "type",
                "type": "INTEGER"
            },
            {
                "name": "owner_id",
                "type": "INTEGER"
            },
            {
                "name": "owner_type",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "month",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "year",
                "type": "INTEGER"
            },
            {
                "name": "net_salary",
                "type": "DOUBLE"
            },
            {
                "name": "status",
                "type": "INTEGER"
            },
            {
                "name": "basic_salary",
                "type": "DOUBLE"
            },
            {
                "name": "allowance",
                "type": "DOUBLE"
            },
            {
                "name": "deductions",
                "type": "DOUBLE"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "enquiries": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "full_name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "email",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "contact_no",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "type",
                "type": "TINYINT"
            },
            {
                "name": "message",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "viewed_by",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "status",
                "type": "TINYINT(1)"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "viewed_by"
                ],
                "referred_table": "users",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "event_google_calendars": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "user_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "google_calendar_list_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "google_calendar_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "google_calendar_list_id"
                ],
                "referred_table": "google_calendar_lists",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "user_id"
                ],
                "referred_table": "users",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "expenses": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "expense_head",
                "type": "INTEGER"
            },
            {
                "name": "name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "invoice_number",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "date",
                "type": "DATETIME"
            },
            {
                "name": "amount",
                "type": "DOUBLE"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "exports": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "completed_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "file_disk",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "file_name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "exporter",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "processed_rows",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "total_rows",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "successful_rows",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "user_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "user_id"
                ],
                "referred_table": "users",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "failed_jobs": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "uuid",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "connection",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "queue",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "payload",
                "type": "LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "exception",
                "type": "LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "failed_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": []
    },
    "faqs": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "question",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "answer",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": []
    },
    "feature_subscriptionplan": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "feature_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "subscription_plan_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "feature_id"
                ],
                "referred_table": "features",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "subscription_plan_id"
                ],
                "referred_table": "subscription_plans",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "features": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "name",
                "type": "VARCHAR(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "submenu",
                "type": "INTEGER"
            },
            {
                "name": "route",
                "type": "LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "has_parent",
                "type": "INTEGER"
            },
            {
                "name": "is_default",
                "type": "INTEGER"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": []
    },
    "front_services": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "short_description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "front_settings": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "key",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "value",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "type",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "google_calendar_integrations": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "user_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "access_token",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "meta",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "last_used_at",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "user_id"
                ],
                "referred_table": "users",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "google_calendar_lists": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "user_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "calendar_name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "google_calendar_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "meta",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "user_id"
                ],
                "referred_table": "users",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "hospital_schedules": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "day_of_week",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "start_time",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "end_time",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "hospital_type": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": []
    },
    "incomes": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "income_head",
                "type": "INTEGER"
            },
            {
                "name": "name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "invoice_number",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "date",
                "type": "DATETIME"
            },
            {
                "name": "amount",
                "type": "DOUBLE"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "insurance_diseases": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "insurance_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "disease_name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "disease_charge",
                "type": "DOUBLE"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "insurance_id"
                ],
                "referred_table": "insurances",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "insurances": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "name",
                "type": "VARCHAR(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "service_tax",
                "type": "DOUBLE"
            },
            {
                "name": "discount",
                "type": "DOUBLE"
            },
            {
                "name": "remark",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "insurance_no",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "insurance_code",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "hospital_rate",
                "type": "DOUBLE"
            },
            {
                "name": "total",
                "type": "DOUBLE"
            },
            {
                "name": "status",
                "type": "TINYINT(1)"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "investigation_reports": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "patient_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "date",
                "type": "DATETIME"
            },
            {
                "name": "title",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "doctor_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "status",
                "type": "INTEGER"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "doctor_id"
                ],
                "referred_table": "doctors",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "patient_id"
                ],
                "referred_table": "patients",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "invoice_items": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "account_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "invoice_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "quantity",
                "type": "INTEGER"
            },
            {
                "name": "price",
                "type": "DOUBLE"
            },
            {
                "name": "total",
                "type": "DOUBLE"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "account_id"
                ],
                "referred_table": "accounts",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "invoice_id"
                ],
                "referred_table": "invoices",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "invoices": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "invoice_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "patient_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "invoice_date",
                "type": "DATE"
            },
            {
                "name": "amount",
                "type": "DOUBLE"
            },
            {
                "name": "discount",
                "type": "DOUBLE"
            },
            {
                "name": "status",
                "type": "TINYINT(1)"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "patient_id"
                ],
                "referred_table": "patients",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "ipd_bills": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "ipd_patient_department_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "total_charges",
                "type": "DOUBLE"
            },
            {
                "name": "total_payments",
                "type": "DOUBLE"
            },
            {
                "name": "gross_total",
                "type": "DOUBLE"
            },
            {
                "name": "discount_in_percentage",
                "type": "INTEGER"
            },
            {
                "name": "tax_in_percentage",
                "type": "INTEGER"
            },
            {
                "name": "other_charges",
                "type": "DOUBLE"
            },
            {
                "name": "net_payable_amount",
                "type": "DOUBLE"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "ipd_patient_department_id"
                ],
                "referred_table": "ipd_patient_departments",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "ipd_charges": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "ipd_patient_department_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "date",
                "type": "DATE"
            },
            {
                "name": "charge_type_id",
                "type": "INTEGER"
            },
            {
                "name": "charge_category_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "charge_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "standard_charge",
                "type": "DOUBLE"
            },
            {
                "name": "applied_charge",
                "type": "DOUBLE"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "charge_category_id"
                ],
                "referred_table": "charge_categories",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "charge_id"
                ],
                "referred_table": "charges",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "ipd_patient_department_id"
                ],
                "referred_table": "ipd_patient_departments",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "ipd_consultant_registers": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "ipd_patient_department_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "applied_date",
                "type": "DATETIME"
            },
            {
                "name": "doctor_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "instruction",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "instruction_date",
                "type": "DATE"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "doctor_id"
                ],
                "referred_table": "doctors",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "ipd_patient_department_id"
                ],
                "referred_table": "ipd_patient_departments",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "ipd_diagnoses": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "ipd_patient_department_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "report_type",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "report_date",
                "type": "DATETIME"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "ipd_patient_department_id"
                ],
                "referred_table": "ipd_patient_departments",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "ipd_patient_departments": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "patient_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "ipd_number",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "height",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "weight",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "bp",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "symptoms",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "notes",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "admission_date",
                "type": "DATETIME"
            },
            {
                "name": "is_discharge",
                "type": "TINYINT(1)"
            },
            {
                "name": "case_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "is_old_patient",
                "type": "TINYINT(1)"
            },
            {
                "name": "doctor_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "bed_type_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "bed_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "custom_field",
                "type": "LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "bill_status",
                "type": "TINYINT(1)"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "bed_id"
                ],
                "referred_table": "beds",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "bed_type_id"
                ],
                "referred_table": "bed_types",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "case_id"
                ],
                "referred_table": "patient_cases",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "doctor_id"
                ],
                "referred_table": "doctors",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "patient_id"
                ],
                "referred_table": "patients",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "ipd_payments": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "ipd_patient_department_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "amount",
                "type": "DOUBLE"
            },
            {
                "name": "date",
                "type": "DATE"
            },
            {
                "name": "payment_mode",
                "type": "TINYINT"
            },
            {
                "name": "notes",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "transaction_id",
                "type": "INTEGER"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "ipd_patient_department_id"
                ],
                "referred_table": "ipd_patient_departments",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "ipd_prescription_items": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "ipd_prescription_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "category_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "medicine_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "dosage",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "dose_interval",
                "type": "INTEGER"
            },
            {
                "name": "day",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "time",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "instruction",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "category_id"
                ],
                "referred_table": "categories",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "ipd_prescription_id"
                ],
                "referred_table": "ipd_prescriptions",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "medicine_id"
                ],
                "referred_table": "medicines",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "ipd_prescriptions": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "ipd_patient_department_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "header_note",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "footer_note",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "ipd_patient_department_id"
                ],
                "referred_table": "ipd_patient_departments",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "ipd_timelines": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "ipd_patient_department_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "title",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "date",
                "type": "DATE"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "visible_to_person",
                "type": "TINYINT(1)"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "ipd_patient_department_id"
                ],
                "referred_table": "ipd_patient_departments",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "issued_items": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "department_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "user_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "issued_by",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "issued_date",
                "type": "DATE"
            },
            {
                "name": "return_date",
                "type": "DATE"
            },
            {
                "name": "item_category_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "item_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "quantity",
                "type": "INTEGER"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "status",
                "type": "TINYINT(1)"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "department_id"
                ],
                "referred_table": "departments",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "item_category_id"
                ],
                "referred_table": "item_categories",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "item_id"
                ],
                "referred_table": "items",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "user_id"
                ],
                "referred_table": "users",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "item_categories": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "item_stocks": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "item_category_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "item_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "supplier_name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "store_name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "quantity",
                "type": "INTEGER"
            },
            {
                "name": "purchase_price",
                "type": "DOUBLE"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "item_category_id"
                ],
                "referred_table": "item_categories",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "item_id"
                ],
                "referred_table": "items",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "items": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "item_category_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "unit",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "available_quantity",
                "type": "INTEGER"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "item_category_id"
                ],
                "referred_table": "item_categories",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "job_batches": {
        "columns": [
            {
                "name": "id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "total_jobs",
                "type": "INTEGER"
            },
            {
                "name": "pending_jobs",
                "type": "INTEGER"
            },
            {
                "name": "failed_jobs",
                "type": "INTEGER"
            },
            {
                "name": "failed_job_ids",
                "type": "LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "options",
                "type": "MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "cancelled_at",
                "type": "INTEGER"
            },
            {
                "name": "created_at",
                "type": "INTEGER"
            },
            {
                "name": "finished_at",
                "type": "INTEGER"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": []
    },
    "jobs": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "queue",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "payload",
                "type": "LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "attempts",
                "type": "TINYINT UNSIGNED"
            },
            {
                "name": "reserved_at",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "available_at",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "created_at",
                "type": "INTEGER UNSIGNED"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": []
    },
    "lab_technicians": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "user_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "user_id"
                ],
                "referred_table": "users",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "landing_about_us": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "text_main",
                "type": "VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_img_one",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_img_two",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_img_three",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "main_img_one",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "main_img_two",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_one_text",
                "type": "VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_two_text",
                "type": "VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_three_text",
                "type": "VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_one_text_secondary",
                "type": "VARCHAR(135) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_two_text_secondary",
                "type": "VARCHAR(135) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_three_text_secondary",
                "type": "VARCHAR(135) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": []
    },
    "live_consultations": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "doctor_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "patient_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "consultation_title",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "consultation_date",
                "type": "DATETIME"
            },
            {
                "name": "host_video",
                "type": "TINYINT(1)"
            },
            {
                "name": "participant_video",
                "type": "TINYINT(1)"
            },
            {
                "name": "consultation_duration_minutes",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "type",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "type_number",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_by",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "status",
                "type": "INTEGER"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "meeting_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "meta",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "time_zone",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "password",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "platform_type",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "doctor_id"
                ],
                "referred_table": "doctors",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "patient_id"
                ],
                "referred_table": "patients",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "live_meetings": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "consultation_title",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "consultation_date",
                "type": "DATETIME"
            },
            {
                "name": "consultation_duration_minutes",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "host_video",
                "type": "TINYINT(1)"
            },
            {
                "name": "participant_video",
                "type": "TINYINT(1)"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_by",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "meta",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "meeting_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "time_zone",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "password",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "status",
                "type": "INTEGER"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "live_meetings_candidates": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "user_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "live_meeting_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": []
    },
    "lunch_breaks": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "doctor_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "break_from",
                "type": "TIME"
            },
            {
                "name": "break_to",
                "type": "TIME"
            },
            {
                "name": "every_day",
                "type": "TINYINT(1)"
            },
            {
                "name": "date",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "doctor_id"
                ],
                "referred_table": "doctors",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "mails": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "to",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "subject",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "message",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "attachments",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "user_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "user_id"
                ],
                "referred_table": "users",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "media": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "model_type",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "model_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "collection_name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "file_name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "mime_type",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "disk",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "size",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "manipulations",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "custom_properties",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "responsive_images",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "order_column",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "conversions_disk",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "uuid",
                "type": "CHAR(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "generated_conversions",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": []
    },
    "medicine_bills": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "bill_number",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "patient_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "doctor_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "model_type",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "model_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "discount",
                "type": "DOUBLE"
            },
            {
                "name": "net_amount",
                "type": "DOUBLE"
            },
            {
                "name": "total",
                "type": "DOUBLE"
            },
            {
                "name": "tax_amount",
                "type": "DOUBLE"
            },
            {
                "name": "payment_status",
                "type": "INTEGER"
            },
            {
                "name": "payment_type",
                "type": "INTEGER"
            },
            {
                "name": "note",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "bill_date",
                "type": "DATETIME"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "medicines": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "category_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "brand_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "name",
                "type": "VARCHAR(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "selling_price",
                "type": "DOUBLE"
            },
            {
                "name": "buying_price",
                "type": "DOUBLE"
            },
            {
                "name": "quantity",
                "type": "INTEGER"
            },
            {
                "name": "available_quantity",
                "type": "INTEGER"
            },
            {
                "name": "salt_composition",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "side_effects",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "brand_id"
                ],
                "referred_table": "brands",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "category_id"
                ],
                "referred_table": "categories",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "migrations": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "migration",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "batch",
                "type": "INTEGER"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": []
    },
    "model_has_permissions": {
        "columns": [
            {
                "name": "permission_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "model_type",
                "type": "VARCHAR(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "model_id",
                "type": "BIGINT UNSIGNED"
            }
        ],
        "primary_keys": [
            "permission_id",
            "model_id",
            "model_type"
        ],
        "foreign_keys": [
            {
                "column": [
                    "permission_id"
                ],
                "referred_table": "permissions",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "model_has_roles": {
        "columns": [
            {
                "name": "role_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "model_type",
                "type": "VARCHAR(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "model_id",
                "type": "BIGINT UNSIGNED"
            }
        ],
        "primary_keys": [
            "role_id",
            "model_id",
            "model_type"
        ],
        "foreign_keys": [
            {
                "column": [
                    "role_id"
                ],
                "referred_table": "departments",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "modules": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "is_active",
                "type": "TINYINT(1)"
            },
            {
                "name": "is_hidden",
                "type": "TINYINT(1)"
            },
            {
                "name": "route",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "notice_boards": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "title",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "notifications": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "type",
                "type": "INTEGER"
            },
            {
                "name": "notification_for",
                "type": "INTEGER"
            },
            {
                "name": "user_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "title",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "text",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "meta",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "read_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "user_id"
                ],
                "referred_table": "users",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "nurses": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "user_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "user_id"
                ],
                "referred_table": "users",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "opd_diagnoses": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "opd_patient_department_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "report_type",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "report_date",
                "type": "DATETIME"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "report_generated",
                "type": "TINYINT(1)"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "opd_patient_department_id"
                ],
                "referred_table": "opd_patient_departments",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "opd_patient_departments": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "patient_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "opd_number",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "height",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "weight",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "bp",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "symptoms",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "notes",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "appointment_date",
                "type": "DATETIME"
            },
            {
                "name": "case_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "is_old_patient",
                "type": "TINYINT(1)"
            },
            {
                "name": "doctor_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "standard_charge",
                "type": "DOUBLE"
            },
            {
                "name": "payment_mode",
                "type": "TINYINT"
            },
            {
                "name": "custom_field",
                "type": "LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "case_id"
                ],
                "referred_table": "patient_cases",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "doctor_id"
                ],
                "referred_table": "doctors",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "patient_id"
                ],
                "referred_table": "patients",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "opd_prescription_items": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "opd_prescription_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "category_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "medicine_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "dosage",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "dose_interval",
                "type": "INTEGER"
            },
            {
                "name": "day",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "time",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "instruction",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "category_id"
                ],
                "referred_table": "categories",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "medicine_id"
                ],
                "referred_table": "medicines",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "opd_prescription_id"
                ],
                "referred_table": "opd_prescriptions",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "opd_prescriptions": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "opd_patient_department_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "header_note",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "footer_note",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "opd_patient_department_id"
                ],
                "referred_table": "opd_patient_departments",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "opd_timelines": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "opd_patient_department_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "title",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "date",
                "type": "DATE"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "visible_to_person",
                "type": "TINYINT(1)"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "opd_patient_department_id"
                ],
                "referred_table": "opd_patient_departments",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "operation_reports": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "patient_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "case_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "doctor_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "date",
                "type": "DATETIME"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "doctor_id"
                ],
                "referred_table": "doctors",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "patient_id"
                ],
                "referred_table": "patients",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "package_services": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "package_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "service_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "quantity",
                "type": "DOUBLE"
            },
            {
                "name": "rate",
                "type": "DOUBLE"
            },
            {
                "name": "amount",
                "type": "DOUBLE"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "package_id"
                ],
                "referred_table": "packages",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "service_id"
                ],
                "referred_table": "services",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "packages": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "name",
                "type": "VARCHAR(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "discount",
                "type": "DOUBLE"
            },
            {
                "name": "total_amount",
                "type": "DOUBLE"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "password_reset_tokens": {
        "columns": [
            {
                "name": "email",
                "type": "VARCHAR(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "token",
                "type": "LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [],
        "foreign_keys": []
    },
    "pathology_categories": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "name",
                "type": "VARCHAR(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "pathology_parameter_items": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "pathology_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "patient_result",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "parameter_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "parameter_id"
                ],
                "referred_table": "pathology_parameters",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "pathology_id"
                ],
                "referred_table": "pathology_tests",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "pathology_parameters": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "parameter_name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "reference_range",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "unit_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "unit_id"
                ],
                "referred_table": "pathology_units",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "pathology_tests": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "test_name",
                "type": "VARCHAR(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "short_name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "test_type",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "category_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "unit",
                "type": "INTEGER"
            },
            {
                "name": "subcategory",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "method",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "report_days",
                "type": "INTEGER"
            },
            {
                "name": "charge_category_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "standard_charge",
                "type": "DOUBLE"
            },
            {
                "name": "patient_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "category_id"
                ],
                "referred_table": "pathology_categories",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "charge_category_id"
                ],
                "referred_table": "charge_categories",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "patient_id"
                ],
                "referred_table": "patients",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "pathology_units": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "patient_admissions": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "patient_admission_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "patient_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "doctor_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "admission_date",
                "type": "DATETIME"
            },
            {
                "name": "discharge_date",
                "type": "DATETIME"
            },
            {
                "name": "package_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "insurance_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "bed_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "policy_no",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "agent_name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "guardian_name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "guardian_relation",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "guardian_contact",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "guardian_address",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "status",
                "type": "TINYINT(1)"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "bed_id"
                ],
                "referred_table": "beds",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "doctor_id"
                ],
                "referred_table": "doctors",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "insurance_id"
                ],
                "referred_table": "insurances",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "package_id"
                ],
                "referred_table": "packages",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "patient_id"
                ],
                "referred_table": "patients",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "patient_cases": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "case_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "patient_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "phone",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "doctor_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "date",
                "type": "DATETIME"
            },
            {
                "name": "fee",
                "type": "DOUBLE"
            },
            {
                "name": "status",
                "type": "TINYINT(1)"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "doctor_id"
                ],
                "referred_table": "doctors",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "patient_id"
                ],
                "referred_table": "patients",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "patient_diagnosis_properties": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "patient_diagnosis_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "property_name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "property_value",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "patient_diagnosis_id"
                ],
                "referred_table": "patient_diagnosis_tests",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "patient_diagnosis_tests": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "patient_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "doctor_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "category_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "report_number",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "category_id"
                ],
                "referred_table": "diagnosis_categories",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "doctor_id"
                ],
                "referred_table": "doctors",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "patient_id"
                ],
                "referred_table": "patients",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "patients": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "user_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "template_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "custom_field",
                "type": "LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "patient_unique_id",
                "type": "VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "template_id"
                ],
                "referred_table": "smart_patient_cards",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "user_id"
                ],
                "referred_table": "users",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "payments": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "payment_date",
                "type": "DATE"
            },
            {
                "name": "account_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "pay_to",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "amount",
                "type": "DOUBLE"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "account_id"
                ],
                "referred_table": "accounts",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "permissions": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "name",
                "type": "VARCHAR(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "guard_name",
                "type": "VARCHAR(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": []
    },
    "pharmacists": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "user_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "user_id"
                ],
                "referred_table": "users",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "postals": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "from_title",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "to_title",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "reference_no",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "date",
                "type": "DATE"
            },
            {
                "name": "address",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "type",
                "type": "INTEGER"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "prescriptions": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "patient_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "doctor_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "food_allergies",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tendency_bleed",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "heart_disease",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "high_blood_pressure",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "diabetic",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "surgery",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "accident",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "others",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "medical_history",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "current_medication",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "female_pregnancy",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "breast_feeding",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "health_insurance",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "low_income",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "reference",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "status",
                "type": "TINYINT(1)"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "plus_rate",
                "type": "VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "temperature",
                "type": "VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "problem_description",
                "type": "VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "test",
                "type": "VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "advice",
                "type": "VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "next_visit_qty",
                "type": "VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "next_visit_time",
                "type": "VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "doctor_id"
                ],
                "referred_table": "doctors",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "patient_id"
                ],
                "referred_table": "patients",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "prescriptions_medicines": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "prescription_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "medicine",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "dosage",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "day",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "time",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "dose_interval",
                "type": "INTEGER"
            },
            {
                "name": "comment",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "medicine"
                ],
                "referred_table": "medicines",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "prescription_id"
                ],
                "referred_table": "prescriptions",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "purchase_medicines": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "purchase_no",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tax",
                "type": "DOUBLE"
            },
            {
                "name": "total",
                "type": "DOUBLE"
            },
            {
                "name": "net_amount",
                "type": "DOUBLE"
            },
            {
                "name": "payment_type",
                "type": "INTEGER"
            },
            {
                "name": "payment_status",
                "type": "TINYINT(1)"
            },
            {
                "name": "discount",
                "type": "DOUBLE"
            },
            {
                "name": "note",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "payment_note",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "purchased_medicines": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "purchase_medicines_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "medicine_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "expiry_date",
                "type": "DATETIME"
            },
            {
                "name": "lot_no",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tax",
                "type": "DOUBLE"
            },
            {
                "name": "quantity",
                "type": "INTEGER"
            },
            {
                "name": "amount",
                "type": "DOUBLE"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "medicine_id"
                ],
                "referred_table": "medicines",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "purchase_medicines_id"
                ],
                "referred_table": "purchase_medicines",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "radiology_categories": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "name",
                "type": "VARCHAR(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "radiology_tests": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "test_name",
                "type": "VARCHAR(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "short_name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "test_type",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "category_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "subcategory",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "report_days",
                "type": "INTEGER"
            },
            {
                "name": "charge_category_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "standard_charge",
                "type": "DOUBLE"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "charge_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "patient_id",
                "type": "BIGINT UNSIGNED"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "category_id"
                ],
                "referred_table": "radiology_categories",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "charge_category_id"
                ],
                "referred_table": "charge_categories",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "charge_id"
                ],
                "referred_table": "charges",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "receptionists": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "user_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "user_id"
                ],
                "referred_table": "users",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "role_has_permissions": {
        "columns": [
            {
                "name": "permission_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "role_id",
                "type": "BIGINT UNSIGNED"
            }
        ],
        "primary_keys": [
            "permission_id",
            "role_id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "permission_id"
                ],
                "referred_table": "permissions",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "role_id"
                ],
                "referred_table": "departments",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "sale_medicines": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "medicine_bill_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "medicine_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "sale_quantity",
                "type": "INTEGER"
            },
            {
                "name": "sale_price",
                "type": "DOUBLE"
            },
            {
                "name": "tax",
                "type": "DOUBLE"
            },
            {
                "name": "expiry_date",
                "type": "DATE"
            },
            {
                "name": "amount",
                "type": "DOUBLE"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": []
    },
    "schedule_days": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "doctor_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "schedule_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "available_on",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "available_from",
                "type": "TIME"
            },
            {
                "name": "available_to",
                "type": "TIME"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "doctor_id"
                ],
                "referred_table": "doctors",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "schedule_id"
                ],
                "referred_table": "schedules",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "schedules": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "doctor_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "per_patient_time",
                "type": "TIME"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "doctor_id"
                ],
                "referred_table": "doctors",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "section_fives": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "main_img_url",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_img_url_one",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_img_url_two",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_img_url_three",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_img_url_four",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_one_number",
                "type": "INTEGER"
            },
            {
                "name": "card_two_number",
                "type": "INTEGER"
            },
            {
                "name": "card_three_number",
                "type": "INTEGER"
            },
            {
                "name": "card_four_number",
                "type": "INTEGER"
            },
            {
                "name": "card_one_text",
                "type": "VARCHAR(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_two_text",
                "type": "VARCHAR(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_three_text",
                "type": "VARCHAR(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_four_text",
                "type": "VARCHAR(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": []
    },
    "section_fours": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "text_main",
                "type": "VARCHAR(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "text_secondary",
                "type": "VARCHAR(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "img_url_one",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "img_url_two",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "img_url_three",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "img_url_four",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "img_url_five",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "img_url_six",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_text_one",
                "type": "VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_text_two",
                "type": "VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_text_three",
                "type": "VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_text_four",
                "type": "VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_text_five",
                "type": "VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_text_six",
                "type": "VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_text_one_secondary",
                "type": "VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_text_two_secondary",
                "type": "VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_text_three_secondary",
                "type": "VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_text_four_secondary",
                "type": "VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_text_five_secondary",
                "type": "VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_text_six_secondary",
                "type": "VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": []
    },
    "section_ones": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "text_main",
                "type": "VARCHAR(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "text_secondary",
                "type": "VARCHAR(135) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "img_url",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": []
    },
    "section_threes": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "text_main",
                "type": "VARCHAR(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "text_secondary",
                "type": "VARCHAR(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "img_url",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "text_one",
                "type": "VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "text_two",
                "type": "VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "text_three",
                "type": "VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "text_four",
                "type": "VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "text_five",
                "type": "VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": []
    },
    "section_twos": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "text_main",
                "type": "VARCHAR(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "text_secondary",
                "type": "VARCHAR(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_one_image",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_one_text",
                "type": "VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_one_text_secondary",
                "type": "VARCHAR(90) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_two_image",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_two_text",
                "type": "VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_two_text_secondary",
                "type": "VARCHAR(90) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_third_image",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_third_text",
                "type": "VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "card_third_text_secondary",
                "type": "VARCHAR(90) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": []
    },
    "service_sliders": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": []
    },
    "services": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "name",
                "type": "VARCHAR(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "quantity",
                "type": "INTEGER"
            },
            {
                "name": "rate",
                "type": "DOUBLE"
            },
            {
                "name": "status",
                "type": "INTEGER"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "sessions": {
        "columns": [
            {
                "name": "id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "user_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "ip_address",
                "type": "VARCHAR(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "user_agent",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "payload",
                "type": "LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "last_activity",
                "type": "INTEGER"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": []
    },
    "settings": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "key",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "value",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "smart_patient_cards": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "template_name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "header_color",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "show_email",
                "type": "TINYINT(1)"
            },
            {
                "name": "show_phone",
                "type": "TINYINT(1)"
            },
            {
                "name": "show_dob",
                "type": "TINYINT(1)"
            },
            {
                "name": "show_blood_group",
                "type": "TINYINT(1)"
            },
            {
                "name": "show_address",
                "type": "TINYINT(1)"
            },
            {
                "name": "show_patient_unique_id",
                "type": "TINYINT(1)"
            },
            {
                "name": "show_insurance",
                "type": "TINYINT(1)"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "sms": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "send_to",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "region_code",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "phone_number",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "message",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "send_by",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "send_by"
                ],
                "referred_table": "users",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "send_to"
                ],
                "referred_table": "users",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "subscribes": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "email",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "subscribe",
                "type": "TINYINT(1)"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": []
    },
    "subscription_plans": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "currency",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "price",
                "type": "DOUBLE"
            },
            {
                "name": "frequency",
                "type": "INTEGER"
            },
            {
                "name": "is_default",
                "type": "INTEGER"
            },
            {
                "name": "trial_days",
                "type": "INTEGER"
            },
            {
                "name": "sms_limit",
                "type": "BIGINT"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": []
    },
    "subscriptions": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "user_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "subscription_plan_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "transaction_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "plan_amount",
                "type": "DOUBLE"
            },
            {
                "name": "plan_frequency",
                "type": "INTEGER"
            },
            {
                "name": "starts_at",
                "type": "DATETIME"
            },
            {
                "name": "ends_at",
                "type": "DATETIME"
            },
            {
                "name": "trial_ends_at",
                "type": "DATETIME"
            },
            {
                "name": "sms_limit",
                "type": "BIGINT"
            },
            {
                "name": "status",
                "type": "TINYINT(1)"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "subscription_plan_id"
                ],
                "referred_table": "subscription_plans",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "transaction_id"
                ],
                "referred_table": "transactions",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "user_id"
                ],
                "referred_table": "users",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "super_admin_currency_settings": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "currency_name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "currency_code",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "currency_icon",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "deleted_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": []
    },
    "super_admin_enquiries": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "first_name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "last_name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "email",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "phone",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "message",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "status",
                "type": "TINYINT(1)"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": []
    },
    "super_admin_settings": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "key",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "value",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": []
    },
    "tenants": {
        "columns": [
            {
                "name": "id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_username",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "hospital_name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "data",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": []
    },
    "testimonials": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "transactions": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "transaction_id",
                "type": "VARCHAR(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "payment_type",
                "type": "INTEGER"
            },
            {
                "name": "amount",
                "type": "DOUBLE"
            },
            {
                "name": "user_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "status",
                "type": "VARCHAR(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "is_manual_payment",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "meta",
                "type": "LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "notes",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "user_id"
                ],
                "referred_table": "users",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "used_medicines": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "stock_used",
                "type": "INTEGER"
            },
            {
                "name": "medicine_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "model_id",
                "type": "INTEGER"
            },
            {
                "name": "model_type",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "medicine_id"
                ],
                "referred_table": "medicines",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "user_google_event_schedules": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "user_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "google_live_consultation_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "google_calendar_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "google_event_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "google_meet_link",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "user_id"
                ],
                "referred_table": "users",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "user_tenants": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "user_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "last_login_at",
                "type": "DATETIME"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "user_id"
                ],
                "referred_table": "users",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "user_zoom_credential": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "user_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "zoom_api_key",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "zoom_api_secret",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "user_id"
                ],
                "referred_table": "users",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "users": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "department_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "first_name",
                "type": "VARCHAR(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "last_name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "email",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "city",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "password",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "designation",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "phone",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "region_code",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "is_super_admin_default",
                "type": "TINYINT(1)"
            },
            {
                "name": "gender",
                "type": "INTEGER"
            },
            {
                "name": "is_admin_default",
                "type": "TINYINT(1)"
            },
            {
                "name": "qualification",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "blood_group",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "dob",
                "type": "DATE"
            },
            {
                "name": "email_verified_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "owner_id",
                "type": "INTEGER"
            },
            {
                "name": "owner_type",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "status",
                "type": "TINYINT(1)"
            },
            {
                "name": "language",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "username",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "hospital_name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "remember_token",
                "type": "VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "facebook_url",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "twitter_url",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "instagram_url",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "linkedIn_url",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "theme_mode",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "hospital_type_id",
                "type": "BIGINT UNSIGNED"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "hospital_type_id"
                ],
                "referred_table": "hospital_type",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "vaccinated_patients": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "patient_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "vaccination_id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "vaccination_serial_number",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "dose_number",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "dose_given_date",
                "type": "DATETIME"
            },
            {
                "name": "description",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "patient_id"
                ],
                "referred_table": "patients",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            },
            {
                "column": [
                    "vaccination_id"
                ],
                "referred_table": "vaccinations",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "vaccinations": {
        "columns": [
            {
                "name": "id",
                "type": "INTEGER UNSIGNED"
            },
            {
                "name": "name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "manufactured_by",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "brand",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "visitors": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "purpose",
                "type": "INTEGER"
            },
            {
                "name": "name",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "phone",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "id_card",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "no_of_person",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "date",
                "type": "DATE"
            },
            {
                "name": "in_time",
                "type": "TIME"
            },
            {
                "name": "out_time",
                "type": "TIME"
            },
            {
                "name": "note",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "tenant_id",
                "type": "VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "tenant_id"
                ],
                "referred_table": "tenants",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    },
    "zoom_o_auth_credentials": {
        "columns": [
            {
                "name": "id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "user_id",
                "type": "BIGINT UNSIGNED"
            },
            {
                "name": "access_token",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "refresh_token",
                "type": "TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            },
            {
                "name": "created_at",
                "type": "TIMESTAMP"
            },
            {
                "name": "updated_at",
                "type": "TIMESTAMP"
            }
        ],
        "primary_keys": [
            "id"
        ],
        "foreign_keys": [
            {
                "column": [
                    "user_id"
                ],
                "referred_table": "users",
                "referred_columns": [
                    "id"
                ]
            }
        ]
    }
}
