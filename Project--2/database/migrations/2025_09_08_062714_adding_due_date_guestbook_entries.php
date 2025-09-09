<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('guestbook_entries', function (Blueprint $table) {
            $table->date('due_date')
                ->default(DB::raw('CURRENT_DATE'))
                ->after('image');
        });
    }


    public function down(): void {}
};
