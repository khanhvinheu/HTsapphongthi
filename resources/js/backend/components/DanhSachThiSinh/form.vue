<template>
    <div class="row">    
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header" style="background-color: rgb(0,0,0,0.1);">
                    <h3 class="card-title">{{ title }}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>              
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <el-form label-position="right" :model="formData" :rules="rules" ref="formData"
                                     label-width="220px" class="demo-ruleForm">   
                                <span class="title-divider">Thông tin thí sinh</span>     
                                <el-divider></el-divider>                                
                                <el-form-item label="Mã chứng chỉ" prop="maThiSinh">
                                    <div class="form-group">
                                        <el-input disabled validate-event placeholder="Mã chứng chỉ"
                                                    v-model="formData.maThiSinh"></el-input>
                                    </div>
                                </el-form-item>                         
                                <el-form-item label="Tên học viên" prop="tenThiSinh">
                                    <div class="form-group">
                                        <el-input validate-event placeholder="Nhập tên học viên"
                                                    v-model="formData.tenThiSinh"></el-input>
                                    </div>
                                </el-form-item>                         
                                <el-form-item label="Ngày sinh" prop="ngaySinh">
                                    <div class="form-group">
                                        <!-- <el-input validate-event placeholder="Nhập ngày tháng năm sinh: dd/mm/yyyy"
                                                    v-model="formData.ngaySinh"></el-input> -->
                                        <el-date-picker
                                            style="width: 100%;"
                                            format="dd/MM/yyyy"
                                            v-model="formData.ngaySinh"
                                            type="date"
                                            placeholder="Nhập ngày tháng năm sinh: dd/mm/yyyy">
                                        </el-date-picker>
                                    </div>
                                </el-form-item>     
                                <el-form-item label="Giới tính" prop="gioiTinh">
                                    <div class="form-group">
                                        <!-- <el-input validate-event placeholder="Nhập giới tính"
                                                    v-model="formData.gioiTinh"></el-input> -->
                                        <el-select style="width: 100%;" v-model="formData.gioiTinh" placeholder="Nhập giới tính">
                                            <el-option label="Nam" value="Nam"></el-option>
                                            <el-option label="Nữ" value="Nữ"></el-option>
                                        </el-select>
                                    </div>
                                </el-form-item>                       
                                                            
                                <el-form-item label="Lớp" prop="hsLop">
                                    <div class="form-group">
                                        <el-input validate-event placeholder="Lớp"
                                                  v-model="formData.hsLop"></el-input>
                                    </div>
                                </el-form-item> 
                                
                                <span class="title-divider">Thông tin năm học</span>     
                                <!-- <el-divider></el-divider>                                  -->
                                <el-form-item label="Khối thi" prop="maKhoiThi">
                                    <div class="form-group">
                                        <el-select style="width: 100%" v-model="formData.maKhoiThi" size="large"
                                                   filterable 
                                                   placeholder="Chọn khối thi">
                                            <el-option
                                                v-for="item in listKhoiThi"
                                                :key="item.id"
                                                :label="item.maKhoiThi + ' | ' + item.maKhoiThi"
                                                :value="item.maKhoiThi"
                                            >                                            
                                            </el-option>
                                        </el-select>
                                    </div>
                                </el-form-item>                                                           
                                 
                                <!-- <span class="title-divider">Thông tin xét tốt nghiệp và cấp chứng chỉ</span>     -->
                                <el-divider></el-divider>
                                <el-form-item label="Năm học" prop="maNamHoc">
                                    <div class="form-group">
                                        <el-select style="width: 100%" v-model="formData.maNamHoc" size="large"
                                                   filterable 
                                                   placeholder="Chọn năm học">
                                            <el-option
                                                v-for="item in listNamHoc"
                                                :key="item.id"
                                                :label="item.maNamHoc + ' | ' + item.maNamHoc"
                                                :value="item.maNamHoc"
                                            >                                            
                                            </el-option>
                                        </el-select>
                                    </div>
                                </el-form-item>     
                                <el-form-item label="Ghi chú" prop="ghiChu">
                                    <div class="form-group">
                                        <el-input type="textarea" rows="6" validate-event placeholder="Nhập ghi chú"
                                                    v-model="formData.ghiChu"></el-input>
                                    </div>
                                </el-form-item>                                                   
                            </el-form>

                            <!-- /.form-group -->
                        </div>
                        <!-- /.col -->

                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.card-body -->
                <div class="card-footer" style="display: flex; justify-content: end;">
                    <el-button v-show="!idUpdate" type="success" @click="create"><i class="el-icon-plus"></i> Lưu lại
                    </el-button>
                    <el-button v-show="idUpdate" type="success" @click="update"><i class="el-icon-edit"></i> Cập nhật
                    </el-button>
                    <el-button @click="$router.push({name:'DanhSachThiSinh'})">Đóng</el-button>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
import ApiService from '../../common/api.service';
import VueUploadMultipleImage from 'vue-upload-multiple-image'



export default {
    watch: {
        filterDataCategorys(val) {
            this.$refs.tree.filter(val);
        }
    },
    components: {
        VueUploadMultipleImage,
    },
    data() {
       
        return {
            title:'THÊM MỚI THÍ SINH',           
            idUpdate: '',
            rules: {
                tenThiSinh: [
                    {required: true, message: 'Vui lòng không bỏ trống', trigger: 'blur'},
                ],
                ngaySinh: [
                    {required: true, message: 'Vui lòng không bỏ trống', trigger: 'blur'},
                ],
                gioiTinh: [
                    {required: true, message: 'Vui lòng không bỏ trống', trigger: 'blur'},
                ],
                maKhoiThi: [
                    {required: true, message: 'Vui lòng không bỏ trống', trigger: 'blur'},
                ],
                maNamHoc: [
                    {required: true, message: 'Vui lòng không bỏ trống', trigger: 'blur'},
                ],
                email: [
                    {required: true, message: 'Vui lòng không bỏ trống', trigger: 'blur'},
                    {
                        type: 'email',
                        message: 'Vui lòng nhập đúng định dạng: abc@gmail.com',
                        trigger: ['blur', 'change'],
                    },
                ],                              
            },
            data: [],
            filterDataCategorys: '',
            listNamHoc: [],
            listKhoiThi: [],             
            form:new FormData(),
            formData: {
                maThiSinh:'',
                tenThiSinh:'',
                ngaySinh:'',
                gioiTinh:'',
                hsLop:'',
                ketQua:'',
                ghiChu:'',
                maKhoiThi:'',
                maNamHoc:'',               
            },
            files: [],
            fileListOption: [],
            limitImg:1,
            fileList:[],
            dialogVisible:false,
            dialogImageUrl:false

        }
    },
    async mounted() {
        this.title=this.$route.name=='UserUpdate'?'CẬP NHẬT THÔNG TIN THÍ SINH':'THÊM MỚI THÍ SINH'
        await this.getListNamHoc()
        await this.getListKhoiThi()
        if(this.$route.params.id){
            this.idUpdate=this.$route.params.id
            await this.getDetail(this.$route.params.id)
        }else{
            this.genCode()     
        }
    },
    methods: {
        async genCode() {
            let _this = this
            ApiService.query('/api/admin/danhsachthisinh/gen_code').then(({data}) => {
                _this.formData.maThiSinh = data
            })
        },
         //Custorm upload images
        handlePictureCardPreview(file) {
            this.dialogImageUrl = file.url;
            this.dialogVisible = true;
        },
        handleUploadChange(file) {
            if (this.fileList.findIndex(e => e.uid == file.uid) == -1) {
                this.fileList.push(file)
            }
        },
        messLimit() {
            this.$notify({
                title: 'Error',
                message: 'Không upload số lượng ảnh vượt quá mức cho phép',
                type: 'error'
            });
        },
        uploadFile(i) {
            // this.$refs['uploadFile'+i]
            this.$refs['uploadFile' + i][0].click();
        },
        clearImage(i){      
            this.fileListOption.splice(i, 1,null); 
        },
        handleFileUpload(event, index) {
            // Get the uploaded file
            const file = event.target.files[0]
            // Update the files array with the new file
            this.$set(this.fileListOption, index, file)           
          
        },  
        removeImg(el) {           
            if (el.url) {
                this.formData.delete_image=(el.url)
            }
            this.fileList = this.fileList.filter(e => e.uid != el.uid)
            // ApiService.post('/api/admin/removeFile', { path: el.url })
        },

        fileUrl(index) {
            // Get the URL for the uploaded file
            if (this.fileListOption[index] && this.fileListOption[index] instanceof File) {
                return URL.createObjectURL(this.fileListOption[index])
            }else{
                return this.fileListOption[index]
            }
        },

        handleSubmit(event) {
            event.preventDefault()
            // Handle form submission here
        },
        resetForm(formName) {            
            this.$refs[formName].resetFields();
        },
       
        async getListNamHoc(id, update) {
            let _this = this
            ApiService.query('/api/admin/danhsachnamhoc', {params: {type: 'data'}}).then(({data}) => {
                _this.listNamHoc = data['data']              
            })

        },
        async getListKhoiThi(id, update) {
            let _this = this
            ApiService.query('/api/admin/danhsachkhoithi', {params: {type: 'data'}}).then(({data}) => {
                _this.listKhoiThi = data['data']              
            })

        },       
        async getDetail(id) {
            let _this = this
            _this.idUpdate = id
            await axios({
                method: 'get',
                url: '/api/admin/danhsachthisinh/detail/' + id,
            })
                .then(({data}) => {                  
                    if (data['success']) {
                        let res = data['data']
                        _this.formData.maThiSinh=res['maThiSinh'],
                        _this.formData.tenThiSinh=res['tenThiSinh'],
                        _this.formData.ngaySinh=res['ngaySinh'],
                        _this.formData.gioiTinh=res['gioiTinh'],
                        _this.formData.hsLop=res['hsLop'],
                        _this.formData.ghiChu=res['ghiChu'],
                        _this.formData.maKhoiThi=res['maKhoiThi'],
                        _this.formData.maNamHoc=res['maNamHoc']                     
                    }
                 
                });
           
        },
      
        appendToFormData() {
            let _this = this
            _this.form =new FormData()
            Object.keys(this.formData).forEach(key => {   
                if(this.formData[key]){
                     _this.form.set(key, this.formData[key])
                }             
               
            });          
        },
        appendFileToFormData() {
            let index = 0
            this.fileList.map((e) => {
                if (e && e.status == "ready") {
                    this.form.set('file' + index, e.raw)
                    index++
                }
            })
            if (this.fileList.length==0) {
                this.form.set('image', null)
            }

        },
        create() {
            let _this = this
            _this.appendToFormData()
            _this.appendFileToFormData()         
            this.$refs['formData'].validate((valid) => {
                if (valid) {
                    axios({
                        method: 'post',
                        url: '/api/admin/danhsachthisinh/create',
                        data: _this.form
                    })
                        .then(function (response) {
                            if (response.data['success']) {
                                _this.$notify({
                                    title: 'Success',
                                    message: response.data['mess'],
                                    type: 'success'
                                });
                                _this.resetForm('formData')
                                _this.$router.push({name:"DanhSachThiSinh"})
                            } else {
                                _this.$notify({
                                    title: 'Error',
                                    message: response.data['mess'],
                                    type: 'error'
                                });
                            }

                        });
                } else {
                    return false;
                }
            });
        },
        update() {
            let _this = this
            _this.appendToFormData()
            _this.appendFileToFormData()           
            this.$refs['formData'].validate((valid) => {
                if (valid) {
                    axios({
                        method: 'post',
                        url: '/api/admin/danhsachthisinh/update/' + _this.idUpdate,
                        data: _this.form
                    })
                        .then(function (response) {
                            if (response.data['success']) {
                                _this.$notify({
                                    title: 'Success',
                                    message: response.data['mess'],
                                    type: 'success'
                                });
                                _this.resetForm('formData')                               
                                _this.idUpdate = ''
                                _this.$router.push({name:"DanhSachThiSinh"})
                            } else {
                                _this.$notify({
                                    title: 'Error',
                                    message: response.data['mess'],
                                    type: 'error'
                                });
                            }

                        });
                } else {
                    return false;
                }
            });
        }
    }
};
</script>

<style>
.custom-tree-node {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 14px;
    padding-right: 8px;
}

.label__form {
    font-size: 13px;
}

/* .label__form::before{
  content: '+ ';
} */
.title-divider{
    font-weight: bold;
    color: rgb(0,0,0,0.6);
}
.title-divider::before{
    content: "+";
}
.custorm-upload .el-upload--picture-card {
    width: 120px;
    height: unset;
    line-height: 10px;
    padding: 25px;
}

.custorm-upload .el-upload-list--picture-card .el-upload-list__item {
    width: 120px;
    height: 120px;
}
.box-image-option:hover .overlay-remove{
    opacity:1 !important;
    background-color:rgb(0,0,0,0.3)!important;
    color:#fff;
    transition: all 0.9ms linear;
}
</style>
