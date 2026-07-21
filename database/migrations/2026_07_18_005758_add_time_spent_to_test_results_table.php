<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimeSpentToTestResultsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('test_results', 'time_spent')) {
            Schema::table('test_results', function (Blueprint $table) {
                $table->integer('time_spent')->nullable()->after('certificate_level');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('test_results', 'time_spent')) {
            Schema::table('test_results', function (Blueprint $table) {
                $table->dropColumn('time_spent');
            });
        }
    }
}