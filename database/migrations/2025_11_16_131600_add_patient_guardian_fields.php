<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            if (! Schema::hasColumn('patients', 'contact_email')) {
                $table->string('contact_email')->nullable()->after('document_number');
            }
            if (! Schema::hasColumn('patients', 'marital_status_id')) {
                $table->unsignedBigInteger('marital_status_id')->nullable()->after('contact_email');
            }
            if (! Schema::hasColumn('patients', 'birth_place')) {
                $table->string('birth_place')->nullable()->after('marital_status_id');
            }
            if (! Schema::hasColumn('patients', 'residence_address')) {
                $table->string('residence_address')->nullable()->after('birth_place');
            }
            if (! Schema::hasColumn('patients', 'occupation')) {
                $table->string('occupation')->nullable()->after('residence_address');
            }
            if (! Schema::hasColumn('patients', 'ethnicity')) {
                $table->string('ethnicity')->nullable()->after('occupation');
            }
            if (! Schema::hasColumn('patients', 'education_level')) {
                $table->string('education_level')->nullable()->after('ethnicity');
            }
            if (! Schema::hasColumn('patients', 'phone_secondary')) {
                $table->string('phone_secondary')->nullable()->after('education_level');
            }
            if (! Schema::hasColumn('patients', 'responsible_name')) {
                $table->string('responsible_name')->nullable()->after('phone_secondary');
            }
            if (! Schema::hasColumn('patients', 'responsible_phone')) {
                $table->string('responsible_phone')->nullable()->after('responsible_name');
            }
            if (! Schema::hasColumn('patients', 'responsible_relationship')) {
                $table->string('responsible_relationship')->nullable()->after('responsible_phone');
            }
            if (! Schema::hasColumn('patients', 'emergency_contact_name')) {
                $table->string('emergency_contact_name')->nullable()->after('responsible_relationship');
            }
            if (! Schema::hasColumn('patients', 'emergency_contact_phone')) {
                $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $columns = [
                'contact_email',
                'marital_status_id',
                'birth_place',
                'residence_address',
                'occupation',
                'ethnicity',
                'education_level',
                'phone_secondary',
                'responsible_name',
                'responsible_phone',
                'responsible_relationship',
                'emergency_contact_name',
                'emergency_contact_phone',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('patients', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
