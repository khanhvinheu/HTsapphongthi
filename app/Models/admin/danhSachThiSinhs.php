<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class danhSachThiSinhs extends Model
{
    use HasFactory;
    protected $table = 'danhSachThiSinhs';
    protected $fillable = [
        'maThiSinh',
        'tenThiSinh',
        'ngaySinh',
        'gioiTinh',
        'hsLop',
        'ketQua',
        'ghiChu',
        'maKhoiThi',
        'maNamHoc',
    ];
}
