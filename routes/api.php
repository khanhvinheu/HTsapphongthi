<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');

    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::delete('logout', 'AuthController@logout');
        Route::get('me', 'AuthController@user');
        Route::group(
            [
                'middleware' => 'permission:' . \ACL::PERMISSION_PERMISSION_MANAGE,
            ],
            function () {
                Route::apiResource('roles', 'RoleController');
                Route::apiResource('permissions', 'PermissionController');
            }
        );

    });
});
Route::prefix('admin')->group(function () {
  //block
  Route::post('/sign-pdf', 'PdfBlockchainController@signPdf');
  Route::post('/verify-pdf','PdfBlockchainController@verifyPdf');
});
Route::prefix('admin')->namespace('admin')->group(function () {
    // Upload
    Route::post('upload', 'UploadController@upload');
    Route::post('removeFile', 'UploadController@removeFile');
    // Category
    Route::get('categorys','CategoryController@index');
    Route::get('categorys/getParent/{id}','CategoryController@getParent');
    Route::get('categorys/detail/{id}','CategoryController@show');
    Route::post('categorys/update/{id}','CategoryController@update');
    Route::post('categorys/create','CategoryController@create');
    Route::post('categorys/delete/{id}','CategoryController@destroy');
    // Banner
    Route::get('banners','BannerController@index');
    Route::get('banners/detail/{id}','BannerController@show');
    Route::post('banners/delete/{id}','BannerController@destroy');
    Route::post('banners/create','BannerController@store');
    Route::post('banners/update/{id}','BannerController@update');
    //Product Color
    Route::get('product_color','ProductColorController@index');
    Route::get('product_color/detail/{id}','ProductColorController@show');
    Route::post('product_color/delete/{id}','ProductColorController@destroy');
    Route::post('product_color/create','ProductColorController@store');
    Route::post('product_color/update/{id}','ProductColorController@update');
    //Product Size
    Route::get('product_size','ProductSizeController@index');
    Route::get('product_size/detail/{id}','ProductSizeController@show');
    Route::post('product_size/delete/{id}','ProductSizeController@destroy');
    Route::post('product_size/create','ProductSizeController@store');
    Route::post('product_size/update/{id}','ProductSizeController@update');
    // Product
    Route::get('products','ProductController@index');
    Route::get('products/{id}','ProductController@show');
    Route::post('products/create','ProductController@store');
    Route::post('products/delete/{id}','ProductController@destroy');
    Route::post('products/update/{id}','ProductController@update');
    // Route::apiResource('products', 'ProductController');
    //Province
    Route::get('get-full-province','ProvinceController@getFullProvince');
    // Order
    Route::get('orders','OrderController@index');
    Route::post('orders/create','OrderController@create');
    Route::post('orders/delete/{id}','OrderController@destroy');
    Route::post('orders/update/{id}','OrderController@update');

    //Setting
    Route::post('setting/updateSetting','SettingController@UpdateSetting');
    Route::post('setting/updateAllSetting','SettingController@UpdateAllSetting');
    Route::get('setting/fetchSetting','SettingController@fetchSetting');
    //Blog
    //Banner
    Route::get('blogs','BlogController@index');
    Route::get('blogs/detail/{id}','BlogController@show');
    Route::post('blogs/delete/{id}','BlogController@destroy');
    Route::post('blogs/create','BlogController@store');
    Route::post('blogs/update/{id}','BlogController@update');
    //User
    Route::get('users','UserController@index');
    Route::post('users/create','UserController@create');
    Route::get('users/detail/{id}','UserController@show');
    Route::post('users/update/{id}','UserController@update');
    Route::post('users/create-signature/{id}','UserController@createSigature');
    Route::post('users/delete/{id}','UserController@destroy');
    //Modules
    Route::get('module','ModuleController@index');
    Route::get('module/gen_code','ModuleController@genCode');
    Route::get('module/detail/{id}','ModuleController@show');
    Route::post('module/update/{id}','ModuleController@update');
    Route::post('module/create','ModuleController@create');
    Route::post('module/delete/{id}','ModuleController@destroy');
    //Action
    Route::get('action','ActionController@index');
    Route::get('action/gen_code','ActionController@genCode');
    Route::get('action/detail/{id}','ActionController@show');
    Route::post('action/update/{id}','ActionController@update');
    Route::post('action/create','ActionController@create');
    Route::post('action/delete/{id}','ActionController@destroy');
    //Role
    Route::get('role','RoleController@index');
    Route::get('role/gen_code','RoleController@genCode');
    Route::get('role/detail/{id}','RoleController@show');
    Route::post('role/update/{id}','RoleController@update');
    Route::post('role/create','RoleController@create');
    Route::post('role/delete/{id}','RoleController@destroy');
    //TaskManager
    Route::get('list-task','TaskManagerController@index');
    Route::get('list-task/detail/{id}','TaskManagerController@show');
    Route::post('list-task/update/{id}','TaskManagerController@update');
    Route::post('list-task/create','TaskManagerController@create');
    Route::post('list-task/delete/{id}','TaskManagerController@destroy');
    //TaskType
    Route::get('list-task-type','TaskTypesController@index');
    //TaskFeedbacks
    Route::get('list-task-feedback','TaskFeedbackController@index');
    Route::get('list-task-feedback/detail/{id}','TaskFeedbackController@show');
    Route::post('list-task-feedback/update/{id}','TaskFeedbackController@update');
    Route::post('list-task-feedback/create','TaskFeedbackController@create');
    Route::post('list-task-feedback/delete/{id}','TaskFeedbackController@destroy');
    //dotCap
    Route::get('dot-cap','dotCapController@index');
    Route::get('dot-cap/detail/{id}','dotCapController@show');
    Route::post('dot-cap/update/{id}','dotCapController@update');
    Route::post('dot-cap/create','dotCapController@store');
    Route::post('dot-cap/delete/{id}','dotCapController@destroy');
    Route::get('dot-cap/gen_code','dotCapController@genCode');
    //hoSoKyDuyet
    Route::get('ho-so-ky-duyet','hoSoKyDuyetController@index');
    Route::get('ho-so-ky-duyet/detail/{id}','hoSoKyDuyetController@show');
    Route::post('ho-so-ky-duyet/update/{id}','hoSoKyDuyetController@update');
    Route::post('ho-so-ky-duyet/create','hoSoKyDuyetController@store');
    Route::post('ho-so-ky-duyet/delete/{id}','hoSoKyDuyetController@destroy');
    Route::get('ho-so-ky-duyet/gen_code','hoSoKyDuyetController@genCode');
    //khoaHoc
    Route::get('khoa-hoc','thongTinKhoaHocController@index');
    Route::get('khoa-hoc/detail/{id}','thongTinKhoaHocController@show');
    Route::post('khoa-hoc/update/{id}','thongTinKhoaHocController@update');
    Route::post('khoa-hoc/create','thongTinKhoaHocController@store');
    Route::post('khoa-hoc/delete/{id}','thongTinKhoaHocController@destroy');
    Route::get('khoa-hoc/gen_code','thongTinKhoaHocController@genCode');
    //danhSachCapChungChi
    Route::get('cap-chung-chi','danhSachCapChungChiHocVienController@index');
    Route::get('cap-chung-chi/detail/{id}','danhSachCapChungChiHocVienController@show');
    Route::post('cap-chung-chi/update/{id}','danhSachCapChungChiHocVienController@update');
    Route::post('cap-chung-chi/create','danhSachCapChungChiHocVienController@store');
    Route::post('capchungchi/import','danhSachCapChungChiHocVienController@import');
    Route::post('cap-chung-chi/delete/{id}','danhSachCapChungChiHocVienController@destroy');
    Route::post('cap-chung-chi/kyduyet/{id}','danhSachCapChungChiHocVienController@kyDuyet');
    Route::get('cap-chung-chi/gen_code','danhSachCapChungChiHocVienController@genCode');
    Route::get('cap-chung-chi/gen_so-vao-so','danhSachCapChungChiHocVienController@genCodeSoVaoSo');

    Route::post('/blocks', 'BlockController@store');
    Route::get('/blocks/get-index', 'BlockController@getIndex');
    Route::get('/blocks/get-all', 'BlockController@index');
    Route::get('/blocks/get-last', 'BlockController@getLast');

    //thongTinDonVi
    Route::get('thongtindonvi','thongTinDonViController@index');
    Route::get('thongtindonvi/detail/{id}','thongTinDonViController@show');
    Route::post('thongtindonvi/update/{id}','thongTinDonViController@update');
    Route::post('thongtindonvi/create','thongTinDonViController@store');
    Route::post('thongtindonvi/delete/{id}','thongTinDonViController@destroy');
    Route::get('thongtindonvi/gen_code','thongTinDonViController@genCode');
     //danhSachKyThi
     Route::get('danhsachkythi','danhSachKyThiController@index');
     Route::get('danhsachkythi/detail/{id}','danhSachKyThiController@show');
     Route::post('danhsachkythi/update/{id}','danhSachKyThiController@update');
     Route::post('danhsachkythi/create','danhSachKyThiController@store');
     Route::post('danhsachkythi/delete/{id}','danhSachKyThiController@destroy');
     Route::get('danhsachkythi/gen_code','danhSachKyThiController@genCode');
     //danhSachKyThi
     Route::get('danhsachkhoithi','danhSachKhoiThiController@index');
     Route::get('danhsachkhoithi/detail/{id}','danhSachKhoiThiController@show');
     Route::post('danhsachkhoithi/update/{id}','danhSachKhoiThiController@update');
     Route::post('danhsachkhoithi/create','danhSachKhoiThiController@store');
     Route::post('danhsachkhoithi/delete/{id}','danhSachKhoiThiController@destroy');
     Route::get('danhsachkhoithi/gen_code','danhSachKhoiThiController@genCode');
     //danhSachMonThi
     Route::get('danhsachmonthi','DanhSachMonThiController@index');
     Route::get('danhsachmonthi/detail/{id}','DanhSachMonThiController@show');
     Route::post('danhsachmonthi/update/{id}','DanhSachMonThiController@update');
     Route::post('danhsachmonthi/create','DanhSachMonThiController@store');
     Route::post('danhsachmonthi/delete/{id}','DanhSachMonThiController@destroy');
     Route::get('danhsachmonthi/gen_code','DanhSachMonThiController@genCode');
     //danhSachPhongThi
     Route::get('danhsachphongthi','DanhSachPhongThiController@index');
     Route::get('danhsachphongthi/detail/{id}','DanhSachPhongThiController@show');
     Route::post('danhsachphongthi/update/{id}','DanhSachPhongThiController@update');
     Route::post('danhsachphongthi/create','DanhSachPhongThiController@store');
     Route::post('danhsachphongthi/delete/{id}','DanhSachPhongThiController@destroy');
     Route::get('danhsachphongthi/gen_code','DanhSachPhongThiController@genCode');
     //danhSachThiSinh
     Route::get('danhsachthisinh','DanhSachThiSinhController@index');
     Route::get('danhsachthisinh/danhsach','DanhSachThiSinhController@listThiSinhInNamHocKhoiThi');
     Route::get('danhsachthisinh/detail/{id}','DanhSachThiSinhController@show');
     Route::post('danhsachthisinh/update/{id}','DanhSachThiSinhController@update');
     Route::post('danhsachthisinh/create','DanhSachThiSinhController@store');
     Route::post('danhsachthisinh/delete/{id}','DanhSachThiSinhController@destroy');
     Route::get('danhsachthisinh/gen_code','DanhSachThiSinhController@genCode');
     //danhSachNamHoc
     Route::get('danhsachnamhoc','DanhSachNamHocController@index');
     Route::get('danhsachnamhoc/detail/{id}','DanhSachNamHocController@show');
     Route::post('danhsachnamhoc/update/{id}','DanhSachNamHocController@update');
     Route::post('danhsachnamhoc/create','DanhSachNamHocController@store');
     Route::post('danhsachnamhoc/delete/{id}','DanhSachNamHocController@destroy');
     Route::get('danhsachnamhoc/gen_code','DanhSachNamHocController@genCode');

});



