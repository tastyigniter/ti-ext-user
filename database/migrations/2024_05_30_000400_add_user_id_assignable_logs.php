<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('assignable_logs', function(Blueprint $table) {
            $table->unsignedInteger('user_id')->nullable()->after('assignee_group_id');
        });
    }
};
