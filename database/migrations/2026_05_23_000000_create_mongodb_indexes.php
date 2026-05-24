<?php

use Illuminate\Database\Migrations\Migration;
use MongoDB\Laravel\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mongodb';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('mongodb')->table('users', function (Blueprint $collection) {
            $collection->unique('email');
        });

        Schema::connection('mongodb')->table('doctors', function (Blueprint $collection) {
            $collection->unique('license_number');
        });

        Schema::connection('mongodb')->table('emergency_cards', function (Blueprint $collection) {
            $collection->unique('qr_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mongodb')->table('users', function (Blueprint $collection) {
            $collection->dropIndex(['email']);
        });

        Schema::connection('mongodb')->table('doctors', function (Blueprint $collection) {
            $collection->dropIndex(['license_number']);
        });

        Schema::connection('mongodb')->table('emergency_cards', function (Blueprint $collection) {
            $collection->dropIndex(['qr_token']);
        });
    }
};
