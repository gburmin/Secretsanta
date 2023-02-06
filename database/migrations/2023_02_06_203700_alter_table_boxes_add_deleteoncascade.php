<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::statement('alter table boxes drop FOREIGN KEY boxes_creator_id_foreign;');
        DB::statement(
            'alter table boxes add constraint boxes_creator_id_foreign
                   foreign key (creator_id)
                   references users(id)
                   on delete cascade;'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('alter table boxes drop FOREIGN KEY boxes_creator_id_foreign;');
        DB::statement('alter table boxes add constraint boxes_creator_id_foreign
                       foreign key (creator_id)
                       references users(id);');
    }
};
