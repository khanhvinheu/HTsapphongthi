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
    public function namHoc(): \Illuminate\Database\Eloquent\Relations\belongsTo
    {
        return $this->belongsTo(namHocs::class, 'maNamHoc', 'maNamHoc');
    }
    public function khoiThi(): \Illuminate\Database\Eloquent\Relations\belongsTo
    {
        return $this->belongsTo(khoiThis::class, 'maKhoiThi', 'maKhoiThi');
    }
}
