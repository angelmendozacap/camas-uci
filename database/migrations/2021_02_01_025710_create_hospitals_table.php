<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHospitalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospitals', function (Blueprint $table) {
            $table->id();
            $table->string('institution',254);
            $table->string('name', 254);
            $table->string('classification', 254);
            $table->string('type', 254);
            $table->string('department', 254);
            $table->string('province', 254);
            $table->string('district', 254);
            $table->string('ubigeo', 254);
            $table->string('address', 254);
            $table->string('disa', 254);
            $table->string('red', 254)->nullable();
            $table->string('micro_red', 254)->nullable();
            $table->string('executor', 254)->nullable();
            $table->string('category', 254)->nullable();
            $table->string('phone', 254)->nullable();
            $table->date('started_at', 254);
            $table->string('responsible', 254)->nullable();
            $table->boolean('status');
            $table->unsignedTinyInteger('condition', 254);
            $table->decimal('north', 19, 15);
            $table->decimal('east', 19, 15);
            $table->decimal('height', 19, 15);
            $table->unsignedInteger('beds_quantity', 254);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospitals');
    }
}