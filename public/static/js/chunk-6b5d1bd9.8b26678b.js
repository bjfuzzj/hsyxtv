(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-6b5d1bd9"],{"333d":function(t,e,i){"use strict";var n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{staticClass:"pagination-container",class:{hidden:t.hidden}},[i("el-pagination",t._b({attrs:{background:t.background,"current-page":t.currentPage,"page-size":t.pageSize,layout:t.layout,"page-sizes":t.pageSizes,total:t.total},on:{"update:currentPage":function(e){t.currentPage=e},"update:current-page":function(e){t.currentPage=e},"update:pageSize":function(e){t.pageSize=e},"update:page-size":function(e){t.pageSize=e},"size-change":t.handleSizeChange,"current-change":t.handleCurrentChange}},"el-pagination",t.$attrs,!1))],1)},a=[];i("e680");Math.easeInOutQuad=function(t,e,i,n){return t/=n/2,t<1?i/2*t*t+e:(t--,-i/2*(t*(t-2)-1)+e)};var l=function(){return window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame||function(t){window.setTimeout(t,1e3/60)}}();function o(t){document.documentElement.scrollTop=t,document.body.parentNode.scrollTop=t,document.body.scrollTop=t}function s(){return document.documentElement.scrollTop||document.body.parentNode.scrollTop||document.body.scrollTop}function r(t,e,i){var n=s(),a=t-n,r=20,u=0;e="undefined"===typeof e?500:e;var c=function t(){u+=r;var s=Math.easeInOutQuad(u,n,a,e);o(s),u<e?l(t):i&&"function"===typeof i&&i()};c()}var u={name:"Pagination",props:{total:{required:!0,type:Number},page:{type:Number,default:1},limit:{type:Number,default:50},pageSizes:{type:Array,default:function(){return[20,50,100]}},layout:{type:String,default:"total, sizes, prev, pager, next, jumper"},background:{type:Boolean,default:!0},autoScroll:{type:Boolean,default:!0},hidden:{type:Boolean,default:!1}},computed:{currentPage:{get:function(){return this.page},set:function(t){this.$emit("update:page",t)}},pageSize:{get:function(){return this.limit},set:function(t){this.$emit("update:limit",t)}}},methods:{handleSizeChange:function(t){this.$emit("pagination",{page:this.currentPage,limit:t}),this.autoScroll&&r(0,800)},handleCurrentChange:function(t){this.$emit("pagination",{page:t,limit:this.pageSize}),this.autoScroll&&r(0,800)}}},c=u,d=(i("7ca8"),i("c701")),p=Object(d["a"])(c,n,a,!1,null,"101a657e",null);e["a"]=p.exports},6724:function(t,e,i){"use strict";i("8d41");var n="@@wavesContext";function a(t,e){function i(i){var n=Object.assign({},e.value),a=Object.assign({ele:t,type:"hit",color:"rgba(0, 0, 0, 0.15)"},n),l=a.ele;if(l){l.style.position="relative",l.style.overflow="hidden";var o=l.getBoundingClientRect(),s=l.querySelector(".waves-ripple");switch(s?s.className="waves-ripple":(s=document.createElement("span"),s.className="waves-ripple",s.style.height=s.style.width=Math.max(o.width,o.height)+"px",l.appendChild(s)),a.type){case"center":s.style.top=o.height/2-s.offsetHeight/2+"px",s.style.left=o.width/2-s.offsetWidth/2+"px";break;default:s.style.top=(i.pageY-o.top-s.offsetHeight/2-document.documentElement.scrollTop||document.body.scrollTop)+"px",s.style.left=(i.pageX-o.left-s.offsetWidth/2-document.documentElement.scrollLeft||document.body.scrollLeft)+"px"}return s.style.backgroundColor=a.color,s.className="waves-ripple z-active",!1}}return t[n]?t[n].removeHandle=i:t[n]={removeHandle:i},i}var l={bind:function(t,e){t.addEventListener("click",a(t,e),!1)},update:function(t,e){t.removeEventListener("click",t[n].removeHandle,!1),t.addEventListener("click",a(t,e),!1)},unbind:function(t){t.removeEventListener("click",t[n].removeHandle,!1),t[n]=null,delete t[n]}},o=function(t){t.directive("waves",l)};window.Vue&&(window.waves=l,Vue.use(o)),l.install=o;e["a"]=l},"7ca8":function(t,e,i){"use strict";i("9d9a")},"8d41":function(t,e,i){},"9d9a":function(t,e,i){},bb31:function(t,e,i){"use strict";i.r(e);var n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{staticClass:"app-container"},[i("div",{staticClass:"filter-container"},[i("div",{staticClass:"el-row"},[i("el-input",{staticClass:"filter-item",staticStyle:{width:"200px"},attrs:{placeholder:"商品ID",clearable:""},nativeOn:{keyup:function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"enter",13,e.key,"Enter")?null:t.handleFilter(e)}},model:{value:t.listQuery.id,callback:function(e){t.$set(t.listQuery,"id",e)},expression:"listQuery.id"}}),t._v(" "),i("el-input",{staticClass:"filter-item",staticStyle:{width:"200px"},attrs:{placeholder:"商品名",clearable:""},nativeOn:{keyup:function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"enter",13,e.key,"Enter")?null:t.handleFilter(e)}},model:{value:t.listQuery.title,callback:function(e){t.$set(t.listQuery,"title",e)},expression:"listQuery.title"}}),t._v(" "),i("el-select",{staticClass:"filter-item",staticStyle:{width:"200px"},attrs:{placeholder:"商品状态",clearable:""},model:{value:t.listQuery.status,callback:function(e){t.$set(t.listQuery,"status",e)},expression:"listQuery.status"}},t._l(t.statusOptions,(function(t,e){return i("el-option",{key:e,attrs:{label:t.desc,value:t.status}})})),1),t._v(" "),i("el-select",{staticClass:"filter-item",staticStyle:{width:"200px"},attrs:{placeholder:"商品类型",clearable:""},model:{value:t.listQuery.type,callback:function(e){t.$set(t.listQuery,"type",e)},expression:"listQuery.type"}},t._l(t.typeOptions,(function(t,e){return i("el-option",{key:e,attrs:{label:t.desc,value:t.type}})})),1)],1),t._v(" "),i("div",{staticClass:"el-row"},[i("el-button",{directives:[{name:"waves",rawName:"v-waves"}],staticClass:"filter-item",attrs:{type:"primary",icon:"el-icon-search"},on:{click:t.handleFilter}},[t._v("搜索")]),t._v(" "),i("el-button",{staticClass:"filter-item",on:{click:t.restore}},[t._v("重置")]),t._v(" "),i("el-button",{staticClass:"filter-item",attrs:{type:"primary"},on:{click:t.goCreateProduct}},[t._v("创建商品")])],1)]),t._v(" "),i("el-table",{directives:[{name:"loading",rawName:"v-loading",value:t.listLoading,expression:"listLoading"}],key:t.tableKey,staticStyle:{width:"100%"},attrs:{data:t.list,border:"",fit:"","highlight-current-row":""}},[i("el-table-column",{attrs:{label:"商品ID",align:"center",prop:"id"}}),t._v(" "),i("el-table-column",{attrs:{label:"商品名",align:"center",prop:"title"}}),t._v(" "),i("el-table-column",{attrs:{label:"产品类型",align:"center",prop:"type_desc"}}),t._v(" "),i("el-table-column",{attrs:{label:"库存",align:"center",prop:"stock"}}),t._v(" "),i("el-table-column",{attrs:{label:"总销量",align:"center",prop:"sold_num"}}),t._v(" "),i("el-table-column",{attrs:{label:"商品状态",align:"center",prop:"status_desc"}}),t._v(" "),i("el-table-column",{attrs:{label:"创建时间",align:"center",prop:"create_time"}}),t._v(" "),i("el-table-column",{attrs:{label:"操作",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[i("el-button",{attrs:{type:"text",size:"mini"},on:{click:function(i){return t.goEditProduct(e.row)}}},[t._v("编辑")]),t._v(" "),1===e.row.status||3===e.row.status?i("el-button",{attrs:{type:"success",size:"mini"},on:{click:function(i){return t.goOnline(e.row)}}},[t._v("发布上线")]):i("el-button",{attrs:{type:"danger",size:"mini"},on:{click:function(i){return t.goOffline(e.row)}}},[t._v("下线")]),t._v(" "),i("el-button",{staticStyle:{"margin-bottom":"5px"},attrs:{type:"success",size:"mini"},on:{click:function(i){return t.showImg(e.row.id)}}},[t._v("查看小程序码")])]}}])})],1),t._v(" "),i("el-dialog",{attrs:{title:"小程序码查看",visible:t.dialogVisible,width:"50%"},on:{"update:visible":function(e){t.dialogVisible=e}}},[i("el-image",{attrs:{src:t.src}}),t._v(" "),i("span",{staticClass:"dialog-footer",attrs:{slot:"footer",width:"50%"},slot:"footer"},[i("el-button",{on:{click:function(e){t.dialogVisible=!1}}},[t._v("关闭")])],1)],1),t._v(" "),i("pagination",{directives:[{name:"show",rawName:"v-show",value:t.total>0,expression:"total>0"}],attrs:{total:t.total,page:t.listQuery.page},on:{"update:page":function(e){return t.$set(t.listQuery,"page",e)},pagination:t.getList}})],1)},a=[],l=i("c4c8"),o=i("6724"),s=i("333d"),r={name:"ProductList",components:{Pagination:s["a"]},directives:{waves:o["a"]},data:function(){return{dialogVisible:!1,statusOptions:[],typeOptions:[],tableKey:0,list:null,total:0,listLoading:!0,listQuery:{id:"",title:"",status:"",type:"",page:1},src:""}},created:function(){var t=localStorage.getItem("productInfo");if(t){var e=JSON.parse(t);this.listQuery=e}this.getList()},methods:{getList:function(){var t=this;Object(l["c"])(this.listQuery).then((function(e){t.list=e.list,t.total=e.total,t.statusOptions=e.status_options,t.typeOptions=e.type_options,t.listLoading=!1}))},showImg:function(t){var e=this;this.src="",this.listLoading=!0;var i={id:t};Object(l["a"])(i).then((function(t){e.src=t.url,e.dialogVisible=!0,e.listLoading=!1}))},restore:function(){localStorage.removeItem("productInfo"),this.listQuery.id="",this.listQuery.title="",this.listQuery.status="",this.listQuery.type="",this.listQuery.page=1,this.listQuery.size=50,this.getList()},goEditProduct:function(t){this.$router.push({path:"/product/edit",query:{id:t.id}})},goCreateProduct:function(){this.$router.push({path:"/product/edit"})},goOffline:function(t){var e=this;Object(l["d"])(t).then((function(i){t.id===i.id&&(e.$message({message:"商品已经下线",type:"warning"}),e.getList())}))},goOnline:function(t){var e=this;Object(l["e"])(t).then((function(i){t.id===i.id&&(e.$message({message:"商品发布成功",type:"success"}),e.getList())}))},handleFilter:function(){var t=JSON.stringify(this.listQuery);localStorage.setItem("productInfo",t),this.listQuery.page=1,this.getList()}}},u=r,c=i("c701"),d=Object(c["a"])(u,n,a,!1,null,null,null);e["default"]=d.exports},c4c8:function(t,e,i){"use strict";i.d(e,"c",(function(){return a})),i.d(e,"b",(function(){return l})),i.d(e,"f",(function(){return o})),i.d(e,"d",(function(){return s})),i.d(e,"e",(function(){return r})),i.d(e,"a",(function(){return u}));var n=i("b775");function a(t){return Object(n["a"])({url:"/product/index",method:"get",params:t})}function l(t){return Object(n["a"])({url:"/product/edit_detail",method:"get",params:t})}function o(t){return Object(n["a"])({url:"/product/save",method:"post",params:t})}function s(t){return Object(n["a"])({url:"/product/offline",method:"post",params:t})}function r(t){return Object(n["a"])({url:"/product/online",method:"post",params:t})}function u(t){return Object(n["a"])({url:"/product/get_img",method:"post",data:t})}}}]);