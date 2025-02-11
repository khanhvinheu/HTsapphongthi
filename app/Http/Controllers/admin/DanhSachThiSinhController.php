<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\danhSachThiSinhs;
use Illuminate\Http\JsonResponse;
use App\Services\QueryService;
use DB;

class DanhSachThiSinhController extends Controller
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
            $limit = $request->get('limit', 25);            
            $page = $request->get('Page', 1);
            $namHocs = DB::table('danhSachThiSinhs')
            ->join('namHocs', 'danhSachThiSinhs.maNamHoc', '=', 'namHocs.maNamHoc')
            ->join('khoiThis', 'danhSachThiSinhs.maKhoiThi', '=', 'khoiThis.maKhoiThi')
            ->select(
                'namHocs.id', 'namHocs.maNamHoc', 'namHocs.tenNamHoc', 'namHocs.ghiChu',
                'khoiThis.maKhoiThi', 'khoiThis.tenKhoiThi'
            )
            ->groupBy(
                'namHocs.id', 'namHocs.maNamHoc', 'namHocs.tenNamHoc', 'namHocs.ghiChu',
                'khoiThis.maKhoiThi', 'khoiThis.tenKhoiThi'
            )
            ->paginate($limit, ['*'], 'page', $page)
            ->toArray();
          
            return $this->jsonTable([
                'data'=>$namHocs['data'],
                'total'=>count($namHocs['data'])
            ]);
        } catch (\Exception $e) {
            return $this->jsonError($e);
        }
    }

    public function listThiSinhInNamHocKhoiThi(Request $request){
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
            $columnSearch = $request->get('columnSearch', ['tenThiSinh']);
            $betweenDate = $request->get('updated_at', []);
            $queryService = new QueryService(new danhSachThiSinhs());
            $queryService->select = [];
            $queryService->filter = $filter;
            $queryService->columnSearch =$columnSearch;
            $queryService->withRelationship = ['namHoc','khoiThi'];
            $queryService->searchRelationship = $searchWith;
            $queryService->itemRelationship = $itemWith;
            $queryService->with = $with;
            $queryService->search = $search;
            $queryService->betweenDate = $betweenDate;
            $queryService->limit = $limit;
            $queryService->ascending = $ascending;
            $queryService->orderBy = $orderBy;
            $query = $queryService->queryTable();
            $maNamHoc = $request->get('maNamHoc','');
            $maKhoiThi = $request->get('maKhoiThi','');        
            $query = $query->where([['maNamHoc',$maNamHoc], ['maKhoiThi',$maKhoiThi]]);
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
        DB::beginTransaction();
        try{
            $formData = $request->post();
            $listThiSinh = json_decode($formData['ListThiSinh']);   
            $maNamHoc = $formData['maNamHoc'];        
            $maKhoiThi = $formData['maKhoiThi'];     
            $clear = danhSachThiSinhs::where([['maNamHoc',$maNamHoc],['maKhoiThi',$maKhoiThi]])->delete();   
            foreach($listThiSinh as $index=>$item){
               $item= (array)$item;
               $item['maNamHoc']=$maNamHoc;
               $item['maKhoiThi']=$maKhoiThi;
               $item['maThiSinh']= self::genCode();     
            //    $checkExist= danhSachThiSinhs::where([['tenThiSinh',$item['tenThiSinh']]])->first();
            //    if($checkExist == null){
               
               $res = danhSachThiSinhs::create($item);
            //    }else{
            //      $id = $checkExist->toArray()['id'];                
            //      $res = danhSachThiSinhs::find($id)->update($item);
            //    }                 
            }
            DB::commit();
            if($res){
                return response()->json(['success'=>true, 'mess'=>'Cập nhật danh sách thành công!']);
            }else{
                return response()->json(['success'=>false, 'mess'=>'Cập nhật danh sách thất bại!']);
            }
        }catch(\Exception $e){
            return response()->json(['success'=>false, 'mess'=>$e]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        try{
            $res = danhSachThiSinhs::find($id);
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
        try{
            $formData = $request->post();
            $file = $request->file('image');
            if($file){
                $formData['path']= $this->upoadFile($file);
            }
            $res = danhSachThiSinhs::find($id)->update($formData);
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
            $res = danhSachThiSinhs::find($id)->update($formData);
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
            $res = danhSachThiSinhs::find($id)->delete();
            if($res){
                return response()->json(['success'=>true, 'mess'=>'Xóa dữ liệu thành công!']);
            }else{
                return response()->json(['success'=>false, 'mess'=>'Xóa dữ liệu thất bại!']);
            }
        }catch(\Exception $e){
            return response()->json(['success'=>false, 'mess'=>$e]);
        }
    }
    public function genCode(){
        $lastCode = danhSachThiSinhs::orderBy('maThiSinh', 'desc')->first(); // lấy mã cuối cùng trong database      
        if (!$lastCode) {
            $number = 1;
        } else {
            $number = intval(substr($lastCode->maThiSinh, -3)) + 1; // lấy số cuối cùng của mã và tăng giá trị lên 1
        }    
        $newCode = 'SBD' . str_pad($number, 4, '0', STR_PAD_LEFT); // tạo mã mới dựa trên số đó và định dạng "ABCXXX"
        return $newCode;
    }
}