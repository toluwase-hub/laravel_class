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
        Schema::table('carts', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->dropColumn('product_name');
            $table->dropColumn('image');
            $table->dropColumn('price');
            $table->dropColumn('size');
            $table->unsignedBigInteger('size_id')->nullable();

            $table->foreign('size_id')->references('id')->on('sizes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('carts', function (Blueprint $table) {
            $table->dropForeign(['size_id']);
            $table->dropColumn('size_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('product_name');
            $table->string('image')->nullable();
            $table->string('size');
            $table->decimal('price', 15, 2)->default(0);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');


        });
    }
};
