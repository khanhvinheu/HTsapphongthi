<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DanhSachThiSinh extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('danhSachThiSinhs', function (Blueprint $table) {
            $table->id();
            $table->string('maThiSinh')->unique();
            $table->string('tenThiSinh');
            $table->string('ngaySinh')->nullable();// Trắc nghiệm, tự luận
            $table->string('gioiTinh')->nullable();                  
            $table->string('hsLop')->nullable();                    
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
        //
        Schema::dropIfExists('danhSachThiSinhs');
    }
}
