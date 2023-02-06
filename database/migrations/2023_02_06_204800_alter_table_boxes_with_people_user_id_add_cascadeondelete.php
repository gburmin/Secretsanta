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

        DB::statement('alter table boxes_with_people drop FOREIGN KEY boxes_with_people_user_id_foreign;');
        DB::statement(
            'alter table boxes_with_people add constraint boxes_with_people_user_id_foreign
                   foreign key (user_id)
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
        DB::statement('alter table boxes_with_people drop FOREIGN KEY boxes_with_people_user_id_foreign;');
        DB::statement('alter table boxes_with_people add constraint boxes_with_people_user_id_foreign
                       foreign key (user_id)
                       references users(id);');
    }
};
