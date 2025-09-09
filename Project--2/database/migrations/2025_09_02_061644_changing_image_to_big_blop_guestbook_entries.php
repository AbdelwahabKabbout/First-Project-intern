<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('guestbook_entries', function (Blueprint $table) {
            DB::statement('ALTER TABLE guestbook_entries MODIFY image LONGBLOB');
        });
    }


    public function down(): void
    {
        Schema::table('guestbook_entries', function (Blueprint $table) {});
    }
};
