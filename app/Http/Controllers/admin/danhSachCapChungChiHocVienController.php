<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\danhSachCapChungChiHocViens;
use App\Models\admin\hoSoKyDuyets;
use App\Models\admin\thongTinKhoaHocs;
use App\Models\admin\dotCaps;
use App\Services\QueryService;
use Illuminate\Support\Str;
use File;
use Carbon\Carbon;
use DateTime;
use Rap2hpoutre\FastExcel\FastExcel;

class danhSachCapChungChiHocVienController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        try {
            $filter=[];
            $request->input('pack_status')&& array_push($filter,['pack_status','=',$request->input('pack_status')]);
            $limit = $request->get('PageLimit', 25);
            $page = $request->get('Page', 1);           
            $ascending = (int) $request->get('ascending', 0);
            $orderBy = $request->get('orderBy', '');
            $search = $request->get('TextSearch', '');
            $searchWith = $request->get('TextSearchWith', '');
            $with = $request->get('with', '');
            $itemWith = $request->get('ItemSearchWith', '');
            $columnSearch = $request->get('columnSearch', ['hoTen','maChungChi']);
            $betweenDate = $request->get('updated_at', []);
            $queryService = new QueryService(new danhSachCapChungChiHocViens());
            $queryService->select = [];
            $queryService->filter = $filter;
            $queryService->columnSearch =$columnSearch;
            $queryService->withRelationship = ['dotCap','khoaHoc','hoSoDuyet','hoSoDuyet.nguoiKyDuyet'];
            $queryService->searchRelationship = $searchWith;
            $queryService->itemRelationship = $itemWith;
            $queryService->with = $with;
            $queryService->search = $search;
            $queryService->betweenDate = $betweenDate;
            $queryService->limit = $limit;
            $queryService->ascending = $ascending;
            $queryService->orderBy = $orderBy;
            $query = $queryService->queryTable();
            $query = $query->paginate($limit,['*'],'page',$page);
            
            $product = $query->toArray();
            return $this->jsonTable($product);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        try{
            $formData = $request->post();    
            $file = $request->file('file0');
            if($file){
                $formData['image']= $this->upload($file);
            }     
            $res = danhSachCapChungChiHocViens::create($formData);
            if($res){
                return response()->json(['success'=>true, 'mess'=>'Thêm mới thành công!']);
            }else{
                return response()->json(['success'=>false, 'mess'=>'Thêm mới thất bại!']);
            }
        }catch(\Exception $e){
            return response()->json(['success'=>false, 'mess'=>$e]);
        }
    }
    public function import(Request $request){
        // Validate that the file is an Excel or CSV file
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        // Use FastExcel to read the file and return the rows as an array
        $data = (new FastExcel)->import($request->file('file'));
        $success=[];
        $error = [];
        if($data && count($data)>0){
            foreach($data as $index=>$item){
                $formData = $item;  
                $formData['maChungChi']= self::genCode();   
                $formData['image']='/images/no_img.jpg';
                if(@$formData['namSinh'] instanceof DateTime){
                    $formData['namSinh']=  $formData['namSinh']->getTimestamp(); 
                    $formData['namSinh']=  Carbon::createFromTimestamp($formData['namSinh'])->format('d/m/Y');                
                }else{
                    $formData['namSinh']=  Carbon::createFromFormat('d/m/Y',$formData['namSinh'])->format('m/d/Y');        
                }
                
                //Khóa học
                $khoaHoc = thongTinKhoaHocs::where('tenKhoaHoc',$formData['khoaHoc'])->first();               
                if($khoaHoc){
                    $formData['maKhoaHoc']= $khoaHoc->toArray()['maKhoaHoc'];
                }else{
                    $formDataKhoaHoc=[
                        "maKhoaHoc"=> self::genCodeKhoaHoc(),
                        "tenKhoaHoc"=> $formData['khoaHoc'],
                        "tenKhoaHocEN"=> "null",
                        "chiTietKhoaHoc"=> "null",
                        "thoiGianDaoTao"=> "90",
                        "tuNgay"=> "03-01-2023",
                        "denNgay"=> "03-01-2023",
                        "noiDaoTao"=> "Trường trung cấp nghề Tân Hiệp tỉnh Kiên Giang",
                        "noiDaoTaoEN"=> "Tan Hiep, Kien Giang",
                    ];
                    $resKhoaHoc = thongTinKhoaHocs::create($formDataKhoaHoc);                 
                    if($resKhoaHoc){
                        $formData['maKhoaHoc']= $formDataKhoaHoc['maKhoaHoc'];
                    }
                }  
                // Đợt cấp
                $dotCap = dotCaps::where('thoiGianCap',$formData['dotCap'])->first();               
                if($dotCap){
                    $formData['maDotCap']= $dotCap->toArray()['maDot'];
                }else{
                    $formDataDotCap=[
                        "maDot"=> self::genCodeDotCap(),
                        "thoiGianCap"=> $formData['dotCap'],
                        "ghiChu"=> "null"
                    ];
                    $resKhoaHoc = dotCaps::create($formDataDotCap);                 
                    if($resKhoaHoc){
                        $formData['maDotCap']= $formDataDotCap['maDot'];
                    }
                }  
                $checkExist = danhSachCapChungChiHocViens::where([['hoTen',$formData['hoTen']],['maKhoaHoc',$formData['maKhoaHoc']]])->first();  
                if($checkExist){                   
                    $res = danhSachCapChungChiHocViens::find($checkExist->toArray()['id'])->update($formData);
                }else{
                    $res = danhSachCapChungChiHocViens::create($formData);
                }                
                if(@$res){
                    array_push($success,$item['hoTen']);
                }else{
                    array_push($error,$item['hoTen']);
                }
            }
        }     
        // Return the data as JSON
        return response()->json([
            'success'=>true,
            'error'=>$error,
            'mess'=>'Import thành công!'
        ]);
    }
    public function genCodeKhoaHoc(){
        $lastCode = thongTinKhoaHocs::orderBy('maKhoaHoc', 'desc')->first(); // lấy mã cuối cùng trong database      
        if (!$lastCode) {
            $number = 1;
        } else {
            $number = intval(substr($lastCode->maKhoaHoc, -3)) + 1; // lấy số cuối cùng của mã và tăng giá trị lên 1
        }    
        $newCode = 'KH' . str_pad($number, 4, '0', STR_PAD_LEFT); // tạo mã mới dựa trên số đó và định dạng "ABCXXX"
        return $newCode;
    }

    public function genCodeDotCap(){
        $lastCode = dotCaps::orderBy('maDot', 'desc')->first(); // lấy mã cuối cùng trong database      
        if (!$lastCode) {
            $number = 1;
        } else {
            $number = intval(substr($lastCode->maDot, -3)) + 1; // lấy số cuối cùng của mã và tăng giá trị lên 1
        }    
        $newCode = 'DC' . str_pad($number, 4, '0', STR_PAD_LEFT); // tạo mã mới dựa trên số đó và định dạng "ABCXXX"
        return $newCode;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {       
        try{            
            $res = danhSachCapChungChiHocViens::where('id', $id)
            ->with('dotCap')
            ->with('khoaHoc')
            ->with('hoSoDuyet')
            ->first();            
            if($res){
                return response()->json(['success'=>true, 'data'=>$res]);
            }else{
                return response()->json(['success'=>false, 'mess'=>'Danh mục đang tìm không tồn tại!']);
            }
        }catch(\Exception $e){
            return response()->json(['success'=>false, 'mess'=>$e]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        try{
            $formData = $request->post();
            $file = $request->file('file0');
            if($file){
                $formData['image']= $this->upload($file);
            }
            if(@$formData['image']=='null'){                
                $formData['image']='';
            }       
            if (@$formData['delete_image']) {              
                if(file_exists((public_path($formData['delete_image'])))){
                    File::delete(public_path($formData['delete_image']));
                }
            }     
                    
            $res = danhSachCapChungChiHocViens::find($id)->update($formData);
            if($res){
                return response()->json(['success'=>true, 'mess'=>'Cập nhật dữ liệu thành công']);
            }else{
                return response()->json(['success'=>false, 'mess'=>'Cập nhật thất bại!']);
            }
        }catch(\Exception $e){
            return response()->json(['success'=>false, 'mess'=>$e]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        try{
            $res = danhSachCapChungChiHocViens::find($id)->delete();
            if($res){
                return response()->json(['success'=>true, 'mess'=>'Xóa dữ liệu thành công!']);
            }else{
                return response()->json(['success'=>false, 'mess'=>'Xóa dữ liệu thất bại!']);
            }
        }catch(\Exception $e){
            return response()->json(['success'=>false, 'mess'=>$e]);
        }
    }

    public function upload($file){
        $randomString = Str::random(10); 
        $fileName ='anh34'.explode('.',$file->getClientOriginalName())[0].time().$randomString.'.'.$file->extension();
        if($file->move(public_path('uploads/anh34'), $fileName)){
            return '/uploads/anh34/'.$fileName;
        }
    }

    public function genCode(){
        $lastCode = danhSachCapChungChiHocViens::orderBy('maChungChi', 'desc')->first(); // lấy mã cuối cùng trong database      
        if (!$lastCode) {
            $number = 1;
        } else {
            $number = intval(substr($lastCode->maChungChi, -3)) + 1; // lấy số cuối cùng của mã và tăng giá trị lên 1
        }    
        $newCode = '' . str_pad($number, 5, '0', STR_PAD_LEFT); // tạo mã mới dựa trên số đó và định dạng "ABCXXX"
        return $newCode;
    }

    public function genCodeSoVaoSo(){
        $lastCode = hoSoKyDuyets::orderBy('maHoSo', 'desc')->first(); // lấy mã cuối cùng trong database      
        if (!$lastCode) {
            $number = 1;
        } else {
            $number = intval(substr($lastCode->maHoSo, -3)) + 1; // lấy số cuối cùng của mã và tăng giá trị lên 1
        }  
        $yearNow =  Carbon::now()->year;  
        $newCode = $yearNow.'/ĐT' . str_pad($number, 4, '0', STR_PAD_LEFT); // tạo mã mới dựa trên số đó và định dạng "ABCXXX"
        return $newCode;
    }
    public function kyDuyet(Request $request, $id){            
        $formData=$request->input();       
        $dataFind= hoSoKyDuyets::where('maHoSo', $id)->first();      
        $formDataUpdate=[
            "maHoSoKyDuyet"=>''
        ]; 
        if(!$dataFind){
            $resHS = hoSoKyDuyets::create($formData);
            $resHS &&  $formDataUpdate['maHoSoKyDuyet']= $resHS->maHoSo;            
        }else{        
            $idHoSo = $dataFind->toArray();    
            $formDataUpdate['maHoSoKyDuyet']=$idHoSo['maHoSo'];
        }          

        $res = danhSachCapChungChiHocViens::find($id)->update($formDataUpdate);
        if($res){
            return response()->json(['success'=>true, 'mess'=>'Cập nhật dữ liệu thành công']);
        }else{
            return response()->json(['success'=>false, 'mess'=>'Cập nhật thất bại!']);
        }
    }


}
