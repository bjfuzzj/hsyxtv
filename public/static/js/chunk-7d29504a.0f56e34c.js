(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-7d29504a"],{"05d4":function(e,t,i){"use strict";var n=i("ed30"),a=i.n(n);a.a},"333d":function(e,t,i){"use strict";var n=function(){var e=this,t=e.$createElement,i=e._self._c||t;return i("div",{staticClass:"pagination-container",class:{hidden:e.hidden}},[i("el-pagination",e._b({attrs:{background:e.background,"current-page":e.currentPage,"page-size":e.pageSize,layout:e.layout,"page-sizes":e.pageSizes,total:e.total},on:{"update:currentPage":function(t){e.currentPage=t},"update:current-page":function(t){e.currentPage=t},"update:pageSize":function(t){e.pageSize=t},"update:page-size":function(t){e.pageSize=t},"size-change":e.handleSizeChange,"current-change":e.handleCurrentChange}},"el-pagination",e.$attrs,!1))],1)},a=[];i("c5f6");Math.easeInOutQuad=function(e,t,i,n){return e/=n/2,e<1?i/2*e*e+t:(e--,-i/2*(e*(e-2)-1)+t)};var l=function(){return window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame||function(e){window.setTimeout(e,1e3/60)}}();function r(e){document.documentElement.scrollTop=e,document.body.parentNode.scrollTop=e,document.body.scrollTop=e}function s(){return document.documentElement.scrollTop||document.body.parentNode.scrollTop||document.body.scrollTop}function o(e,t,i){var n=s(),a=e-n,o=20,u=0;t="undefined"===typeof t?500:t;var c=function e(){u+=o;var s=Math.easeInOutQuad(u,n,a,t);r(s),u<t?l(e):i&&"function"===typeof i&&i()};c()}var u={name:"Pagination",props:{total:{required:!0,type:Number},page:{type:Number,default:1},limit:{type:Number,default:50},pageSizes:{type:Array,default:function(){return[20,50,100]}},layout:{type:String,default:"total, sizes, prev, pager, next, jumper"},background:{type:Boolean,default:!0},autoScroll:{type:Boolean,default:!0},hidden:{type:Boolean,default:!1}},computed:{currentPage:{get:function(){return this.page},set:function(e){this.$emit("update:page",e)}},pageSize:{get:function(){return this.limit},set:function(e){this.$emit("update:limit",e)}}},methods:{handleSizeChange:function(e){this.$emit("pagination",{page:this.currentPage,limit:e}),this.autoScroll&&o(0,800)},handleCurrentChange:function(e){this.$emit("pagination",{page:e,limit:this.pageSize}),this.autoScroll&&o(0,800)}}},c=u,d=(i("7ca8"),i("2877")),p=Object(d["a"])(c,n,a,!1,null,"101a657e",null);t["a"]=p.exports},"634a":function(e,t,i){"use strict";i.r(t);var n=function(){var e=this,t=e.$createElement,i=e._self._c||t;return i("div",{staticClass:"app-container"},[i("div",{staticClass:"filter-container"},[i("el-form",{attrs:{inline:!0}},[i("el-form-item",{attrs:{label:"订单号"}},[i("el-input",{staticClass:"filter-item",staticStyle:{width:"200px"},attrs:{clearable:"",placeholder:"订单号"},nativeOn:{keyup:function(t){return!t.type.indexOf("key")&&e._k(t.keyCode,"enter",13,t.key,"Enter")?null:e.handleFilter(t)}},model:{value:e.listQuery.oid_like,callback:function(t){e.$set(e.listQuery,"oid_like",t)},expression:"listQuery.oid_like"}})],1),e._v(" "),i("el-form-item",{attrs:{label:"商品名称"}},[i("el-input",{staticClass:"filter-item",staticStyle:{width:"200px"},attrs:{clearable:"",placeholder:"商品名称"},nativeOn:{keyup:function(t){return!t.type.indexOf("key")&&e._k(t.keyCode,"enter",13,t.key,"Enter")?null:e.handleFilter(t)}},model:{value:e.listQuery.product_title,callback:function(t){e.$set(e.listQuery,"product_title",t)},expression:"listQuery.product_title"}})],1),e._v(" "),i("el-form-item",{attrs:{label:"用户昵称"}},[i("el-input",{staticClass:"filter-item",staticStyle:{width:"200px"},attrs:{clearable:"",placeholder:"用户昵称"},nativeOn:{keyup:function(t){return!t.type.indexOf("key")&&e._k(t.keyCode,"enter",13,t.key,"Enter")?null:e.handleFilter(t)}},model:{value:e.listQuery.nick_name,callback:function(t){e.$set(e.listQuery,"nick_name",t)},expression:"listQuery.nick_name"}})],1),e._v(" "),i("el-form-item",{attrs:{label:"订单状态"}},[i("el-select",{staticClass:"filter-item",staticStyle:{width:"200px"},attrs:{placeholder:"订单状态",clearable:""},model:{value:e.listQuery.status,callback:function(t){e.$set(e.listQuery,"status",t)},expression:"listQuery.status"}},e._l(e.orderOptions,(function(e,t){return i("el-option",{key:t,attrs:{label:e.label,value:e.value}})})),1)],1),e._v(" "),i("el-form-item",{attrs:{label:"下单时间"}},[i("el-date-picker",{attrs:{type:"datetimerange","range-separator":"至","start-placeholder":"开始时间","end-placeholder":"结束时间","value-format":"yyyy-MM-dd HH:mm:ss","default-time":["00:00:00","23:59:59"]},on:{change:e.onChangeBuyDate},model:{value:e.buyDates,callback:function(t){e.buyDates=t},expression:"buyDates"}})],1),e._v(" "),i("el-form-item",{attrs:{label:"付款时间"}},[i("el-date-picker",{attrs:{type:"datetimerange","range-separator":"至","start-placeholder":"开始时间","end-placeholder":"结束时间","value-format":"yyyy-MM-dd HH:mm:ss","default-time":["00:00:00","23:59:59"]},on:{change:e.onChangePaymentDate},model:{value:e.paymentTimes,callback:function(t){e.paymentTimes=t},expression:"paymentTimes"}})],1),e._v(" "),i("el-form-item",[i("el-button",{staticClass:"filter-item",attrs:{icon:"el-icon-search",type:"primary"},on:{click:e.handleFilter}},[e._v("搜索")]),e._v(" "),i("el-button",{staticClass:"filter-item",on:{click:e.restore}},[e._v("重置")])],1),e._v(" "),i("el-row",[i("el-button",{attrs:{loading:e.downloadLoading,type:"primary",icon:"document"},on:{click:e.handleDownload}},[e._v("导出物流Excel")]),e._v(" "),i("el-button",{attrs:{loading:e.downloadLoading,type:"success",icon:"document"},on:{click:e.handleOrderDownload}},[e._v("导出订单Excel")]),e._v(" "),i("el-upload",{ref:"uploadExcel",staticClass:"filter-item",attrs:{action:"/api/upload_excel",limit:1,"auto-upload":!0,accept:".xlsx","before-upload":e.beforeUploadFile,"on-change":e.fileChange,"on-exceed":e.exceedFile,"on-success":e.handleSuccess,"on-error":e.handleError,"file-list":e.fileList}},[i("div",{staticClass:"update"},[i("el-button",{attrs:{size:"",plain:""}},[e._v("导入订单物流号")]),e._v(" "),i("div",{staticClass:"el-upload__tip",attrs:{slot:"tip"},slot:"tip"},[e._v("(只能上传xlsx(Excel2007)文件，且不超过10M)")])],1)])],1)],1)],1),e._v(" "),i("el-table",{directives:[{name:"loading",rawName:"v-loading",value:e.listLoading,expression:"listLoading"}],key:e.tableKey,staticStyle:{width:"100%"},attrs:{data:e.list,border:"",fit:"","highlight-current-row":""}},[i("el-table-column",{attrs:{label:"订单号",align:"center",prop:"oid"}}),e._v(" "),i("el-table-column",{attrs:{label:"商品名称",align:"center",prop:"title"}}),e._v(" "),i("el-table-column",{attrs:{label:"购买数量",align:"center",prop:"buy_num"}}),e._v(" "),i("el-table-column",{attrs:{label:"订单状态",align:"center",prop:"status_desc"}}),e._v(" "),i("el-table-column",{attrs:{label:"应付/已付 || 应退/已退",align:"center"},scopedSlots:e._u([{key:"default",fn:function(t){return[i("span",[e._v("应付："+e._s(t.row.amount_total))]),e._v(" "),i("br"),e._v(" "),i("span",[e._v("已付："+e._s(t.row.amount_paid))]),e._v(" "),t.row.amount_refundable>0||t.row.amount_refunded>0?i("div",[i("span",[e._v("应退："+e._s(t.row.amount_refundable))]),e._v(" "),i("br"),e._v(" "),i("span",[e._v("已退："+e._s(t.row.amount_refunded))])]):e._e()]}}])}),e._v(" "),i("el-table-column",{attrs:{label:"昵称",align:"center",prop:"nick_name"}}),e._v(" "),i("el-table-column",{attrs:{label:"联系人",align:"center"},scopedSlots:e._u([{key:"default",fn:function(t){return[i("span",[e._v(e._s(t.row.name))]),e._v(" "),i("br"),e._v(" "),i("span",[e._v(e._s(t.row.phone))])]}}])}),e._v(" "),i("el-table-column",{attrs:{label:"收件地址",align:"center",prop:"address"}}),e._v(" "),i("el-table-column",{attrs:{label:"下单时间",align:"center",prop:"create_time"}}),e._v(" "),i("el-table-column",{attrs:{label:"支付时间",align:"center",prop:"paid_time"}}),e._v(" "),i("el-table-column",{attrs:{label:"操作",align:"center"},scopedSlots:e._u([{key:"default",fn:function(t){return[i("el-button",{attrs:{type:"text",size:"mini"},on:{click:function(i){return e.goOrderDetail(t.row)}}},[e._v("查看详情")]),e._v(" "),"20"==t.row.status?i("el-button",{attrs:{type:"primary",size:"mini"},on:{click:function(i){return e.markSend(t.row)}}},[e._v("标记已发货")]):e._e(),e._v(" "),"30"==t.row.status?i("el-button",{attrs:{type:"primary"},on:{click:function(i){return e.markNoSend(t.row)}}},[e._v("取消发货")]):e._e()]}}])})],1),e._v(" "),i("pagination",{directives:[{name:"show",rawName:"v-show",value:e.total>0,expression:"total>0"}],attrs:{total:e.total,page:e.listQuery.page,limit:e.listQuery.size},on:{"update:page":function(t){return e.$set(e.listQuery,"page",t)},"update:limit":function(t){return e.$set(e.listQuery,"size",t)},pagination:e.getList}})],1)},a=[],l=(i("7f7f"),i("f8b7")),r=i("6724"),s=i("333d"),o={name:"OrderList",components:{Pagination:s["a"]},directives:{waves:r["a"]},data:function(){return{tableKey:0,list:null,total:0,listLoading:!0,downloadLoading:!1,fileList:[],listQuery:{oid_like:"",product_title:"",nick_name:"",start_buytime:"",end_buytime:"",payment_begin_time:"",payment_end_time:"",status:"",size:50,page:1},buyDates:[],paymentTimes:[],orderOptions:[{value:10,label:"未支付"},{value:20,label:"已付款"},{value:30,label:"已发货"},{value:40,label:"确认收货"},{value:80,label:"已退款"},{value:100,label:"已取消"},{value:101,label:"已过期"}],pickerOptions:{disabledDate:function(e){return e.getTime()>Date.now()},shortcuts:[{text:"今天",onClick:function(e){e.$emit("pick",new Date)}},{text:"昨天",onClick:function(e){var t=new Date;t.setTime(t.getTime()-864e5),e.$emit("pick",t)}},{text:"一周前",onClick:function(e){var t=new Date;t.setTime(t.getTime()-6048e5),e.$emit("pick",t)}}]}}},watch:{},created:function(){var e=localStorage.getItem("orderInfo");if(e){var t=JSON.parse(e);this.listQuery=t,this.buyDates[0]=t.start_buytime||"",this.buyDates[1]=t.end_buytime||"",this.paymentTimes[0]=t.payment_begin_time||"",this.paymentTimes[1]=t.payment_end_time||""}this.getList()},methods:{getList:function(){var e=this;Object(l["c"])(this.listQuery).then((function(t){e.list=t.records,e.total=t.total,e.listLoading=!1}))},restore:function(){localStorage.removeItem("orderInfo"),this.listQuery.oid_like="",this.listQuery.product_title="",this.listQuery.start_buytime="",this.listQuery.end_buytime="",this.listQuery.payment_begin_time="",this.listQuery.payment_end_time="",this.listQuery.status="",this.listQuery.page=1,this.listQuery.size=50,this.buyDates=[],this.paymentTimes=[],this.getList()},handleDownload:function(){var e="/api/exportexcel?";this.listQuery.oid_like&&(e+="oid_like="+this.listQuery.oid_like),this.listQuery.product_title&&(e+="&product_title="+this.listQuery.product_title),this.listQuery.start_buytime&&(e+="&start_buytime="+this.listQuery.start_buytime),this.listQuery.end_buytime&&(e+="&end_buytime="+this.listQuery.end_buytime),this.listQuery.status&&(e+="&status="+this.listQuery.status),this.listQuery.payment_begin_time&&(e+="&payment_begin_time="+this.listQuery.payment_begin_time),this.listQuery.payment_end_time&&(e+="&payment_end_time="+this.listQuery.payment_end_time),location.href=e},handleOrderDownload:function(){var e="/api/exportorderexcel?";this.listQuery.oid_like&&(e+="oid_like="+this.listQuery.oid_like),this.listQuery.product_title&&(e+="&product_title="+this.listQuery.product_title),this.listQuery.start_buytime&&(e+="&start_buytime="+this.listQuery.start_buytime),this.listQuery.end_buytime&&(e+="&end_buytime="+this.listQuery.end_buytime),this.listQuery.status&&(e+="&status="+this.listQuery.status),this.listQuery.payment_begin_time&&(e+="&payment_begin_time="+this.listQuery.payment_begin_time),this.listQuery.payment_end_time&&(e+="&payment_end_time="+this.listQuery.payment_end_time),location.href=e},handleFilter:function(){var e=JSON.stringify(this.listQuery);localStorage.setItem("orderInfo",e),this.listQuery.page=1,this.getList()},onChangeBuyDate:function(e){e&&2===e.length&&(this.listQuery.start_buytime=e[0],this.listQuery.end_buytime=e[1])},onChangePaymentDate:function(e){e&&2===e.length&&(this.listQuery.payment_begin_time=e[0],this.listQuery.payment_end_time=e[1])},beforeUploadFile:function(e){var t=e.name.substring(e.name.lastIndexOf(".")+1),i=e.size/1024/1024;"xlsx"!==t&&this.$notify.warning({title:"警告",message:"只能上传Excel（即后缀是.xlsx）的文件"}),i>10&&this.$notify.warning({title:"警告",message:"文件大小不得超过10M"})},fileChange:function(e,t){},exceedFile:function(e,t){this.$notify.warning({title:"警告",message:"只能选择 ".concat(this.limitNum," 个文件，当前共选择了 ").concat(e.length+t.length," 个")})},handleSuccess:function(e,t,i){this.$message({message:"文件上传成功",type:"success"}),this.$refs.uploadExcel.clearFiles()},handleError:function(e,t,i){this.$message.error(e.msg)},goOrderDetail:function(e){var t=JSON.stringify(this.listQuery);localStorage.setItem("orderInfo",t),this.$router.push({path:"/order/detail",query:{id:e.oid}})},markSend:function(e){var t=this;Object(l["e"])(e).then((function(i){e.oid===i.id&&(t.$message({message:"操作成功",type:"success"}),t.getList())}))},markNoSend:function(e){var t=this;Object(l["d"])(e).then((function(i){e.oid===i.id&&(t.$message({message:"操作成功",type:"success"}),t.getList())}))}}},u=o,c=(i("05d4"),i("2877")),d=Object(c["a"])(u,n,a,!1,null,"4a8b6800",null);t["default"]=d.exports},6724:function(e,t,i){"use strict";i("8d41");var n="@@wavesContext";function a(e,t){function i(i){var n=Object.assign({},t.value),a=Object.assign({ele:e,type:"hit",color:"rgba(0, 0, 0, 0.15)"},n),l=a.ele;if(l){l.style.position="relative",l.style.overflow="hidden";var r=l.getBoundingClientRect(),s=l.querySelector(".waves-ripple");switch(s?s.className="waves-ripple":(s=document.createElement("span"),s.className="waves-ripple",s.style.height=s.style.width=Math.max(r.width,r.height)+"px",l.appendChild(s)),a.type){case"center":s.style.top=r.height/2-s.offsetHeight/2+"px",s.style.left=r.width/2-s.offsetWidth/2+"px";break;default:s.style.top=(i.pageY-r.top-s.offsetHeight/2-document.documentElement.scrollTop||document.body.scrollTop)+"px",s.style.left=(i.pageX-r.left-s.offsetWidth/2-document.documentElement.scrollLeft||document.body.scrollLeft)+"px"}return s.style.backgroundColor=a.color,s.className="waves-ripple z-active",!1}}return e[n]?e[n].removeHandle=i:e[n]={removeHandle:i},i}var l={bind:function(e,t){e.addEventListener("click",a(e,t),!1)},update:function(e,t){e.removeEventListener("click",e[n].removeHandle,!1),e.addEventListener("click",a(e,t),!1)},unbind:function(e){e.removeEventListener("click",e[n].removeHandle,!1),e[n]=null,delete e[n]}},r=function(e){e.directive("waves",l)};window.Vue&&(window.waves=l,Vue.use(r)),l.install=r;t["a"]=l},"7ca8":function(e,t,i){"use strict";var n=i("b0be"),a=i.n(n);a.a},"8d41":function(e,t,i){},b0be:function(e,t,i){},ed30:function(e,t,i){},f8b7:function(e,t,i){"use strict";i.d(t,"c",(function(){return a})),i.d(t,"b",(function(){return l})),i.d(t,"e",(function(){return r})),i.d(t,"a",(function(){return s})),i.d(t,"d",(function(){return o}));var n=i("b775");function a(e){return Object(n["a"])({url:"/api/order/index",method:"get",params:e})}function l(e){return Object(n["a"])({url:"/api/order/detail",method:"post",params:e})}function r(e){return Object(n["a"])({url:"/api/order/markSend",method:"post",params:e})}function s(e){return Object(n["a"])({url:"/api/order/doSave",method:"post",params:e})}function o(e){return Object(n["a"])({url:"/api/order/markNoSend",method:"post",params:e})}}}]);