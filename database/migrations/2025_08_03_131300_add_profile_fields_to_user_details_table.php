<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            // Drop columns if they exist from a previous failed migration attempt
            if (Schema::hasColumn('user_details', 'company')) {
                $table->dropColumn('company');
            }
            if (Schema::hasColumn('user_details', 'position')) {
                $table->dropColumn('position');
            }

            // Add the new columns
            $table->string('company_name_ar')->nullable()->after('company_name');
            $table->string('company_name_en')->nullable()->after('company_name_ar');
            $table->string('region')->nullable()->after('company_name_en');
            $table->string('employee_name_ar')->nullable()->after('city');
            $table->string('employee_name_en')->nullable()->after('employee_name_ar');
            $table->integer('employee_type')->nullable()->after('employee_name_en');
            $table->text('notes')->nullable()->after('industry');
            $table->boolean('profile_completed')->default(false)->after('newsletter_subscription');

            // Rename the original 'company_name' to avoid confusion if needed
            // Or ensure the data from the registration form goes to the right place.
            // For now, we'll assume new columns are sufficient.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->dropColumn([
                'company_name_ar',
                'company_name_en',
                'region',
                'employee_name_ar',
                'employee_name_en',
                'employee_type',
                'notes',
                'how_did_you_hear',
                'profile_completed',
            ]);

            // If you dropped 'company' and 'position', you might want to add them back here
            // $table->string('company')->nullable();
            // $table->string('position')->nullable();
        });
    }
};
