<template>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header" style="background-color: rgb(0,0,0,0.1);">
                    <h3 class="card-title">DANH SÁCH THÍ SINH</h3>
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
                            <div class="row"
                                 style="display: flex; flex-wrap: nowrap; padding: 8px; justify-content: space-between">
                                <el-input
                                    style="width: 500px"
                                    v-model="textSearch"
                                    placeholder="Nhập kí tự cần tìm kiếm"
                                    @keyup.enter.native="getList()"
                                >
                                    <template v-slot:append>
                                        <el-button type="primary" @click="getList()"><i class="el-icon-search"></i> Tìm
                                            kiếm
                                        </el-button>

                                    </template>
                                </el-input>
                                <div>        
                                    <el-button @click="$router.push({name:'DanhSachThiSinhCreate'})" class="ml-2"
                                               type="primary"><i
                                        class="el-icon-plus"></i> Thêm mới
                                    </el-button>
                                </div>

                            </div>
                            <el-table
                                empty-text="Chưa có dữ liệu !"
                                :data="tableData"
                                style="width: 100%"
                                border
                                :resizable="true"
                                v-loading="loadingTable"
                                :row-class-name="tableRowClassName">

                                <el-table-column
                                    prop="maThiSinh"
                                    label="MÃ THÍ SINH"
                                    sortable
                                >
                                    <template slot-scope="scope">CNTN{{ scope.row.maThiSinh}}
                                    </template>
                                </el-table-column>
                                <el-table-column
                                    prop="tenThiSinh"
                                    label="HỌ TÊN"
                                    sortable
                                >
                                </el-table-column>      
                                <el-table-column
                                    prop="gioiTinh"
                                    label="GIỚI TÍNH"
                                    sortable
                                >
                                  
                                </el-table-column>                         
                                <el-table-column
                                    prop="ngaySinh"
                                    label="NGÀY SINH"
                                    sortable
                                >
                                    <template slot-scope="scope"> {{ scope.row.namSinh | formatDate_Default }}
                                    </template>
                                </el-table-column>                               
                                <el-table-column
                                    prop="created_at"
                                    label="NGÀY TẠO"
                                    width="150"
                                >
                                    <template slot-scope="scope">
                                        {{ scope.row.created_at | formatDate }}
                                    </template>
                                </el-table-column>
                                <el-table-column
                                    label="THAO TÁC"
                                    width="180"
                                >
                                    <template slot-scope="scope">                                    
                                      
                                        <el-button                                          
                                            type="primary"
                                            size="mini"
                                            @click="update(scope.row)"><i class="el-icon-edit"></i>
                                        </el-button>
                     
                                        <el-popconfirm                                            
                                            confirm-button-text='Xóa'
                                            cancel-button-text='Không'
                                            :title="'Bạn có chắc chắn muốn xóa ?'"
                                            @confirm="()=>deleteBanner(scope.row.id)"
                                        >
                                            <el-button slot="reference" type="danger"
                                                       size="mini"><i class="el-icon-delete"></i>
                                            </el-button>
                                        </el-popconfirm>
                                    </template>
                                </el-table-column>
                                <template slot="empty">
                                    <el-empty description="No data"></el-empty>
                                </template>
                            </el-table>
                        </div>
                        <div class="block" style="margin-left: 0px;margin-right: 8px;padding: 10px;width: 100%">
                            <el-pagination
                                @size-change="handleSizeChange"
                                @current-change="handleCurrentChange"
                                :current-page.sync="currentPage"
                                :page-sizes="[10, 20, 50, 100]"
                                :page-size="options.PageLimit"
                                layout="total, sizes, prev, pager, next, jumper"
                                :total="options.Total">
                            </el-pagination>
                        </div>
                        <!-- /.col -->

                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.card-body -->
                <div class="card-footer">

                </div>
            </div>
        </div>
        <el-dialog :visible.sync="outerVisible">
            <formData :resID="idUpdate" @success="success"/>
        </el-dialog>
        <el-dialog :visible.sync="viewPdf" width="80vw">
            <div style="margin-top: -30px">
                <span
                    style="font-size: 13px; font-weight: bold; text-transform: uppercase">QUÉT MÃ ĐỂ TẢI CHỨNG CHỈ</span>
                <el-divider></el-divider>
            </div>
            <embed style="width: 100%; height: 60vh" :src="pdfSrc" title="Embedded PDF Viewer" type="application/pdf">
            </embed>
            <div style="display: flex; justify-content: center; align-items: center;flex-direction: column;">
                <!-- <VueQRCodeComponent :text="this.qrValue"></VueQRCodeComponent>
                <a style="text-align: center; width: 100%;" :href="this.qrValue" target="_brank">Nhấn vào để xem</a> -->
            </div>
        </el-dialog>
        <el-dialog :visible.sync="validDialog" width="50vw">
            <div style="margin-top: -30px">
                <span style="font-size: 13px; font-weight: bold; text-transform: uppercase">KIỂM TRA TÍNH HỢP LỆ CỦA CHỨNG CHỈ</span>
                <el-divider></el-divider>
            </div>
            <div>
                <el-upload                    
                    accept=".pdf"
                    class="upload-demo"
                    drag
                    action=""
                    :limit="1"
                    :on-change="readPdf"
                    :auto-upload="false"
                    :on-remove="handleRemove"
                    :before-upload="beforeUpload"
                    :file-list="fileList">
                    <div @click="clearFile" style="height: 100%; width: 100%;">
                        <i class="el-icon-upload"></i>
                        <div class="el-upload__text"><em>Click to upload</em></div>
                    </div>
                   
                    <!-- <div class="el-upload__tip" slot="tip">pdf files with a size less than 500kb</div> -->
                </el-upload>
            </div>
            <div v-if="publicKey=='' || signature==''">
                <el-alert v-if="fileList.length>0"
                          title="File tải lên chưa được ký duyệt hoặc không đúng định dạng, Vui lòng kiểm tra lại"
                          type="error">
                </el-alert>
            </div>
            <div v-else>
                <el-alert
                    :closable=false
                    title="File tải lên đã hợp lệ"
                    type="success">
                </el-alert>
            </div>
            <el-divider v-if="publicKey && signature" content-position="left">
                <el-button v-show="publicKey && signature" style="margin-left: 10px;" size="small" type="success"
                           @click="validKey()">Kiểm tra tính hợp lệ chữ ký
                </el-button>
            </el-divider>
            <el-progress v-if="percentage>0 && percentage<100" :percentage="percentage" color="success"></el-progress>
            <div v-if="percentage==100">
                <el-alert v-if="statusValid===true"
                          :closable=false
                          title="Kết quả"
                          type="success"
                          description="Khóa hợp lệ"
                          show-icon>
                </el-alert>
                <el-alert v-if="statusValid===false"
                          :closable=false
                          title="Kết quả"
                          type="error"
                          description="Khóa không hợp lệ"
                          show-icon>
                </el-alert>
            </div>

        </el-dialog>
        <el-dialog :visible.sync="validDialogUpload" width="50vw">
            <div style="margin-top: -30px">
                <span style="font-size: 13px; font-weight: bold; text-transform: uppercase">Import file excel</span>
                <el-divider></el-divider>
            </div>
            <div>
                <el-upload
                    v-model:file-list="fileList"
                    class="upload-demo"     
                    action="/api/admin/capchungchi/import"       
                    :on-success="handleSuccess"    
                >
                    <el-button type="primary">Click to upload</el-button>
                    <template #tip>
                    <div class="el-upload__tip">
                        xls/xlsx files with a size less than 500KB.
                    </div>
                    </template>
                </el-upload>
            </div>
        </el-dialog>
    </div>

</template>

<script>
import ApiService from '../../common/api.service';
import formData from "./form";
import VueQRCodeComponent from 'vue-qrcode-component'
import {PDFDocument, rgb} from 'pdf-lib';
import genPdfFunction from '../../common/genPdfFunction';
import commonFn from '../../common/commonFn';

export default {
    components: {formData, VueQRCodeComponent},
    mixins:[genPdfFunction, commonFn],
    data() {
        return {
            fileList: [],
            idUpdate: '',
            outerVisible: false,
            viewPdf: false,
            loadingTable: false,
            tableData: [],
            slideData: [],
            textSearch: '',
            currentPage: 1,
            options: {
                Total: 10,
                Page: 1,
                PageLimit: 10
            },
            pdfSrc: '',
            qrValue: 'https://example.com',
            publicKey: '',
            signature: '',
            validDialog: false,
            validDialogUpload: false,
            statusValid: '',
            percentage: 0
        }
    },
    mounted() {  
        this.getList()        
    },

    methods: {
        // Handle file upload success
        handleSuccess(response, file, fileList) {
            console.log('File uploaded successfully!', response);
            // You can access the response data here, which will typically be the server response.
            if (response && response.success === true) {
                this.$notify({
                                title: 'Success',
                                message: response.data['mess'],
                                type: 'success'
                            });
                this.getList()
            } else {
                this.$notify({
                    title: 'Error',
                    message: 'Import thất bại, Vui lòng kiểm tra nội dung file',
                    type: 'error'
                });
            }
        },

        beforeUpload(file) {
            if (this.fileList.length >= 1) {
                this.fileList.splice(0, 1); // Remove existing file
            }
            return true; // Allow upload
        },
        clearFile(){
            this.fileList = [];   
            this.publicKey=''
            this.signature=''         
            this.statusValid=''         
        },
        fakeLoading() {
            this.percentage = 0
            setInterval(() => {
                if (this.percentage < 100) {
                    this.percentage += 10;
                }
            }, 100);
        },
        handleRemove(el) {
            this.fileList = this.fileList.filter(e => e.uid != el.uid)
            this.publicKey = this.signature = this.statusValid = ''
        },
        success() {
            this.outerVisible = false
            this.getList()
        },
        update(e) {
            this.idUpdate = e.id
            // this.outerVisible=true
            this.$router.push({name: 'ThongTinThiSinhUpdate', params: {id: e.id}})
        },
        handleSizeChange(val) {
            this.options.PageLimit = val
            this.getList()
        },
        handleCurrentChange(val) {
            this.options.Page = val
            this.getList()
        },
      
        deleteBanner(id) {
            let _this = this
            axios({
                method: 'post',
                url: '/api/admin/danhsachthisinh/delete/' + id,
            })
                .then(function (response) {
                    if (response.data['success']) {
                        _this.$notify({
                            title: 'Success',
                            message: response.data['mess'],
                            type: 'success'
                        });
                        _this.getList()
                    } else {
                        _this.$notify({
                            title: 'Error',
                            message: response.data['mess'],
                            type: 'error'
                        });
                    }
                });
        },
        getList() {
            let _this = this
            _this.loadingTable = true
            let param = {}
            this.options.Page && (param.Page = this.options.Page)
            this.options.PageLimit && (param.PageLimit = this.options.PageLimit)
            this.textSearch && (param.TextSearch = this.textSearch)
            axios({
                method: 'get',
                url: '/api/admin/danhsachthisinh',
                params: param
            })
                .then(function ({data}) {
                    if (data['success']) {
                        _this.tableData = data['data']
                        _this.options.Total = data['total']
                        _this.slideData = data['data'].filter(e => e.hidden == 1)
                    }
                    _this.loadingTable = false
                });
        }, changeStatus(id) {
            alert(id)
        },
        tableRowClassName({row, rowIndex}) {
            if (rowIndex === 1) {
                return 'warning-row';
            } else if (rowIndex === 3) {
                return 'success-row';
            }
            return '';
        }
    }
};
</script>

<style lang="scss">
.el-table .warning-row {
    background: oldlace;
}

.el-table .success-row {
    background: #f0f9eb;
}

.dialog-pdf-viewer {
    .el-dialog__body {
        padding: 0 !important
    }

    .el-dialog__header {
        display: none;
    }
}

</style>
