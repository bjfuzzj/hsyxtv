(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-35dda210"],{"23ba":function(t,e,i){"use strict";i.d(e,"c",(function(){return a})),i.d(e,"b",(function(){return s})),i.d(e,"f",(function(){return r})),i.d(e,"e",(function(){return l})),i.d(e,"d",(function(){return o})),i.d(e,"a",(function(){return u}));var n=i("b775");function a(t){return Object(n["a"])({url:"/activity/index",method:"get",params:t})}function s(t){return Object(n["a"])({url:"/activity/edit",method:"get",params:t})}function r(t){return Object(n["a"])({url:"/activity/save",method:"post",data:t})}function l(t){return Object(n["a"])({url:"/group/list",method:"post",data:t})}function o(t){return Object(n["a"])({url:"/group/team",method:"post",data:t})}function u(t){return Object(n["a"])({url:"/activity/change_status",method:"post",data:t})}},6724:function(t,e,i){"use strict";i("8d41");var n="@@wavesContext";function a(t,e){function i(i){var n=Object.assign({},e.value),a=Object.assign({ele:t,type:"hit",color:"rgba(0, 0, 0, 0.15)"},n),s=a.ele;if(s){s.style.position="relative",s.style.overflow="hidden";var r=s.getBoundingClientRect(),l=s.querySelector(".waves-ripple");switch(l?l.className="waves-ripple":(l=document.createElement("span"),l.className="waves-ripple",l.style.height=l.style.width=Math.max(r.width,r.height)+"px",s.appendChild(l)),a.type){case"center":l.style.top=r.height/2-l.offsetHeight/2+"px",l.style.left=r.width/2-l.offsetWidth/2+"px";break;default:l.style.top=(i.pageY-r.top-l.offsetHeight/2-document.documentElement.scrollTop||document.body.scrollTop)+"px",l.style.left=(i.pageX-r.left-l.offsetWidth/2-document.documentElement.scrollLeft||document.body.scrollLeft)+"px"}return l.style.backgroundColor=a.color,l.className="waves-ripple z-active",!1}}return t[n]?t[n].removeHandle=i:t[n]={removeHandle:i},i}var s={bind:function(t,e){t.addEventListener("click",a(t,e),!1)},update:function(t,e){t.removeEventListener("click",t[n].removeHandle,!1),t.addEventListener("click",a(t,e),!1)},unbind:function(t){t.removeEventListener("click",t[n].removeHandle,!1),t[n]=null,delete t[n]}},r=function(t){t.directive("waves",s)};window.Vue&&(window.waves=s,Vue.use(r)),s.install=r;e["a"]=s},"84f2":function(t,e,i){"use strict";i.r(e);var n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{staticClass:"app-container"},[i("div",{staticClass:"filter-container"},[i("el-form",{attrs:{inline:!0}},[i("el-form-item",{attrs:{label:"活动ID"}},[i("el-input",{staticClass:"filter-item",staticStyle:{width:"250px"},attrs:{placeholder:"活动ID",clearable:""},model:{value:t.listQuery.id,callback:function(e){t.$set(t.listQuery,"id",e)},expression:"listQuery.id"}})],1),t._v(" "),i("el-form-item",{attrs:{label:"活动名称"}},[i("el-input",{staticClass:"filter-item",staticStyle:{width:"250px"},attrs:{placeholder:"活动名称",clearable:""},model:{value:t.listQuery.name,callback:function(e){t.$set(t.listQuery,"name",e)},expression:"listQuery.name"}})],1),t._v(" "),t.statusOptions.length>0?i("el-form-item",{attrs:{label:"状态"}},[i("el-select",{staticClass:"filter-item",staticStyle:{width:"200px"},model:{value:t.listQuery.status,callback:function(e){t.$set(t.listQuery,"status",e)},expression:"listQuery.status"}},t._l(t.statusOptions,(function(t,e){return i("el-option",{key:e,attrs:{label:t.label,value:t.value}})})),1)],1):t._e(),t._v(" "),i("el-form-item",{attrs:{label:"有效时间"}},[i("el-date-picker",{attrs:{type:"datetimerange","range-separator":"至","start-placeholder":"开始时间","end-placeholder":"结束时间","value-format":"yyyy-MM-dd HH:mm:ss","default-time":["00:00:00","23:59:59"]},on:{change:t.onChangePaymentDate},model:{value:t.paymentTimes,callback:function(e){t.paymentTimes=e},expression:"paymentTimes"}})],1),t._v(" "),i("el-form-item",[i("el-button",{staticClass:"filter-item",attrs:{type:"primary"},on:{click:t.handleFilter}},[t._v("筛选")]),t._v(" "),i("el-button",{staticClass:"filter-item",on:{click:t.restore}},[t._v("重置")]),t._v(" "),i("el-button",{staticClass:"filter-item",attrs:{type:"primary"},on:{click:t.add}},[t._v("增加活动")])],1)],1)],1),t._v(" "),i("el-table",{directives:[{name:"loading",rawName:"v-loading",value:t.listLoading,expression:"listLoading"}],staticStyle:{width:"100%"},attrs:{data:t.recordList,border:"",fit:"","highlight-current-row":""}},[i("el-table-column",{attrs:{label:"id",align:"center",prop:"id"}}),t._v(" "),i("el-table-column",{attrs:{label:"活动名字",align:"center",prop:"name"}}),t._v(" "),i("el-table-column",{attrs:{label:"商品ID/产品名称",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v("\n        "+t._s(e.row.product_id)+"/"+t._s(e.row.product_name)+"\n      ")]}}])}),t._v(" "),i("el-table-column",{attrs:{label:"状态",align:"center",prop:"status_desc"}}),t._v(" "),i("el-table-column",{attrs:{label:"成团人数",align:"center",prop:"full_amount"}}),t._v(" "),i("el-table-column",{attrs:{label:"原价/拼团价",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v("\n        "+t._s(e.row.original_price)+" 元 / "+t._s(e.row.price)+" 元\n      ")]}}])}),t._v(" "),i("el-table-column",{attrs:{label:"活动开始时间/截止时间",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v("\n        开始："+t._s(e.row.buy_start_time)+" "),i("br"),t._v("\n        截止："+t._s(e.row.buy_end_time)+"\n      ")]}}])}),t._v(" "),i("el-table-column",{attrs:{label:"开团有效期天数",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v("\n        "+t._s(e.row.valid_day)+" 天\n      ")]}}])}),t._v(" "),i("el-table-column",{attrs:{label:"操作",align:"center",width:"250"},scopedSlots:t._u([{key:"default",fn:function(e){return[i("el-button",{staticStyle:{"margin-bottom":"5px"},attrs:{type:"primary",size:"mini"},on:{click:function(i){return t.toEdit(e.row.id)}}},[t._v("编辑")]),t._v(" "),0==e.row.status?i("el-button",{staticStyle:{"margin-bottom":"5px"},attrs:{type:"primary",size:"mini"},on:{click:function(i){return t.changeStatus(e.row.id,1)}}},[t._v("上线")]):t._e(),t._v(" "),1==e.row.status?i("el-button",{staticStyle:{"margin-bottom":"5px"},attrs:{type:"danger",size:"mini"},on:{click:function(i){return t.changeStatus(e.row.id,0)}}},[t._v("下线")]):t._e(),t._v(" "),i("el-button",{staticStyle:{"margin-bottom":"5px"},attrs:{type:"primary",size:"mini"},on:{click:function(i){return t.toDetail(e.row.id)}}},[t._v("查看拼团")])]}}])})],1),t._v(" "),i("el-pagination",{directives:[{name:"show",rawName:"v-show",value:t.total>0,expression:"total > 0"}],staticStyle:{"margin-top":"20px"},attrs:{background:"",total:t.total,"current-page":t.listQuery.page,"page-size":t.listQuery.size,"page-sizes":[10,20,50,100],layout:"sizes, prev, pager, next"},on:{"update:currentPage":function(e){return t.$set(t.listQuery,"page",e)},"update:current-page":function(e){return t.$set(t.listQuery,"page",e)},"size-change":t.handleSizeChange,"current-change":t.handleCurrentChange}})],1)},a=[],s=i("23ba"),r=i("6724"),l={name:"ActivityList",components:{},directives:{waves:r["a"]},data:function(){return{downloadLoading:!1,recordList:null,total:0,listLoading:!1,listQuery:{id:"",name:"",status:"",buy_start_time:"",buy_end_time:"",page:1,size:50},paymentTimes:[],filterStatus:[],statusOptions:""}},computed:{},created:function(){this.$route.query&&this.$route.query.id&&(this.listQuery.id=this.$route.query.id),this.getList()},methods:{handleSizeChange:function(){},handleCurrentChange:function(){},changeStatus:function(t,e){var i=this,n={id:t,status:e};Object(s["a"])(n).then((function(e){e.id===t?(i.$message.success("操作成功"),i.getList()):(i.$message.success("操作失败，请重试"),i.getList())}))},getList:function(){var t=this;Object(s["c"])(this.listQuery).then((function(e){t.recordList=e.records,t.total=e.total,t.statusOptions=e.statusOptions,t.listLoading=!1}))},onChangePaymentDate:function(t){t&&2===t.length&&(this.listQuery.buy_start_time=t[0],this.listQuery.buy_end_time=t[1])},handleFilter:function(){this.listQuery.page=1,this.getList()},toEdit:function(t){this.$router.push({path:"/group/edit/"+t})},toDetail:function(t){this.$router.push({path:"/group/detail",query:{id:t}})},restore:function(){this.listQuery.id="",this.listQuery.buy_start_time="",this.listQuery.buy_end_time="",this.listQuery.status="",this.listQuery.page=1,this.listQuery.size=50,this.paymentTimes=[],this.getList()},add:function(){this.$router.push({path:"/group/edit/0"})}}},o=l,u=i("c701"),c=Object(u["a"])(o,n,a,!1,null,null,null);e["default"]=c.exports},"8d41":function(t,e,i){}}]);