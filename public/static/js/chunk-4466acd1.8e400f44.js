(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-4466acd1"],{6724:function(e,t,l){"use strict";l("8d41");var i="@@wavesContext";function a(e,t){function l(l){var i=Object.assign({},t.value),a=Object.assign({ele:e,type:"hit",color:"rgba(0, 0, 0, 0.15)"},i),n=a.ele;if(n){n.style.position="relative",n.style.overflow="hidden";var s=n.getBoundingClientRect(),r=n.querySelector(".waves-ripple");switch(r?r.className="waves-ripple":(r=document.createElement("span"),r.className="waves-ripple",r.style.height=r.style.width=Math.max(s.width,s.height)+"px",n.appendChild(r)),a.type){case"center":r.style.top=s.height/2-r.offsetHeight/2+"px",r.style.left=s.width/2-r.offsetWidth/2+"px";break;default:r.style.top=(l.pageY-s.top-r.offsetHeight/2-document.documentElement.scrollTop||document.body.scrollTop)+"px",r.style.left=(l.pageX-s.left-r.offsetWidth/2-document.documentElement.scrollLeft||document.body.scrollLeft)+"px"}return r.style.backgroundColor=a.color,r.className="waves-ripple z-active",!1}}return e[i]?e[i].removeHandle=l:e[i]={removeHandle:l},l}var n={bind:function(e,t){e.addEventListener("click",a(e,t),!1)},update:function(e,t){e.removeEventListener("click",e[i].removeHandle,!1),e.addEventListener("click",a(e,t),!1)},unbind:function(e){e.removeEventListener("click",e[i].removeHandle,!1),e[i]=null,delete e[i]}},s=function(e){e.directive("waves",n)};window.Vue&&(window.waves=n,Vue.use(s)),n.install=s;t["a"]=n},"6c3e":function(e,t,l){"use strict";l.r(t);var i=function(){var e=this,t=e.$createElement,l=e._self._c||t;return l("div",{staticClass:"app-container"},[l("div",{staticClass:"filter-container"},[l("el-form",{attrs:{inline:!0}},[l("el-form-item",{attrs:{label:"用户ID"}},[l("el-input",{staticClass:"filter-item",staticStyle:{width:"250px"},attrs:{placeholder:"用户ID",clearable:""},model:{value:e.listQuery.user_id,callback:function(t){e.$set(e.listQuery,"user_id",t)},expression:"listQuery.user_id"}})],1),e._v(" "),l("el-form-item",{attrs:{label:"订单号"}},[l("el-input",{staticClass:"filter-item",staticStyle:{width:"250px"},attrs:{placeholder:"订单号",clearable:""},model:{value:e.listQuery.oid_like,callback:function(t){e.$set(e.listQuery,"oid_like",t)},expression:"listQuery.oid_like"}})],1),e._v(" "),l("el-form-item",{attrs:{label:"优惠券模块ID"}},[l("el-input",{staticClass:"filter-item",staticStyle:{width:"250px"},attrs:{placeholder:"优惠券模块ID",clearable:""},model:{value:e.listQuery.template_id,callback:function(t){e.$set(e.listQuery,"template_id",t)},expression:"listQuery.template_id"}})],1),e._v(" "),e.statusOptions.length>0?l("el-form-item",{attrs:{label:"状态"}},[l("el-select",{staticClass:"filter-item",staticStyle:{width:"200px"},model:{value:e.listQuery.status,callback:function(t){e.$set(e.listQuery,"status",t)},expression:"listQuery.status"}},e._l(e.statusOptions,(function(e,t){return l("el-option",{key:t,attrs:{label:e.label,value:e.value}})})),1)],1):e._e(),e._v(" "),l("el-form-item",{attrs:{label:"下单时间"}},[l("el-date-picker",{attrs:{type:"datetimerange","range-separator":"至","start-placeholder":"开始时间","end-placeholder":"结束时间","value-format":"yyyy-MM-dd HH:mm:ss","default-time":["00:00:00","23:59:59"]},on:{change:e.onChangePaymentDate},model:{value:e.paymentTimes,callback:function(t){e.paymentTimes=t},expression:"paymentTimes"}})],1),e._v(" "),l("el-form-item",[l("el-button",{staticClass:"filter-item",attrs:{type:"primary"},on:{click:e.handleFilter}},[e._v("筛选")]),e._v(" "),l("el-button",{staticClass:"filter-item",on:{click:e.restore}},[e._v("重置")])],1)],1)],1),e._v(" "),l("el-table",{directives:[{name:"loading",rawName:"v-loading",value:e.listLoading,expression:"listLoading"}],staticStyle:{width:"100%"},attrs:{data:e.recordList,border:"",fit:"","highlight-current-row":""}},[l("el-table-column",{attrs:{label:"id",align:"center",prop:"id"}}),e._v(" "),l("el-table-column",{attrs:{label:"用户ID",align:"center",prop:"user_id"}}),e._v(" "),l("el-table-column",{attrs:{label:"订单ID",align:"center",prop:"order_id"}}),e._v(" "),l("el-table-column",{attrs:{label:"优惠券模板ID",align:"center",prop:"template_id"}}),e._v(" "),l("el-table-column",{attrs:{label:"优惠券名字",align:"center",prop:"name"}}),e._v(" "),l("el-table-column",{attrs:{label:"优惠券类型",align:"center",prop:"type_desc"}}),e._v(" "),l("el-table-column",{attrs:{label:"状态",align:"center",prop:"status_desc"}}),e._v(" "),l("el-table-column",{attrs:{label:"减少金额",align:"center",prop:"cut_value"}}),e._v(" "),l("el-table-column",{attrs:{label:"消费多少金额",align:"center",prop:"cost"}}),e._v(" "),l("el-table-column",{attrs:{label:"有效截止时间",align:"center",prop:"valid_end_time"}}),e._v(" "),l("el-table-column",{attrs:{label:"失效原因",align:"center",prop:"invalid_cause"}}),e._v(" "),l("el-table-column",{attrs:{label:"使用时间",align:"center",prop:"use_time"}})],1),e._v(" "),l("el-pagination",{directives:[{name:"show",rawName:"v-show",value:e.total>0,expression:"total > 0"}],staticStyle:{"margin-top":"20px"},attrs:{background:"",total:e.total,"current-page":e.listQuery.page,"page-size":e.listQuery.size,"page-sizes":[10,20,50,100],layout:"sizes, prev, pager, next"},on:{"update:currentPage":function(t){return e.$set(e.listQuery,"page",t)},"update:current-page":function(t){return e.$set(e.listQuery,"page",t)},"size-change":e.handleSizeChange,"current-change":e.handleCurrentChange}})],1)},a=[],n=l("cbfe"),s=l("6724"),r={name:"CouponList",components:{},directives:{waves:s["a"]},data:function(){return{downloadLoading:!1,recordList:null,total:0,listLoading:!1,listQuery:{template_id:"",user_id:"",type:"",status:"",oid_like:"",payment_begin_time:"",payment_end_time:"",page:1,size:50},paymentTimes:[],filterType:[],filterStatus:[],statusOptions:"",typeOptions:""}},computed:{},created:function(){this.$route.query&&this.$route.query.id&&(this.listQuery.template_id=this.$route.query.id),this.getList()},methods:{handleSizeChange:function(){},handleCurrentChange:function(){},getList:function(){var e=this;Object(n["c"])(this.listQuery).then((function(t){e.recordList=t.records,e.total=t.total,e.filterType=t.filterType,e.filterStatus=t.filterStatus,e.statusOptions=t.statusOptions,e.typeOptions=t.typeOptions,e.listLoading=!1}))},onChangePaymentDate:function(e){e&&2===e.length&&(this.listQuery.payment_begin_time=e[0],this.listQuery.payment_end_time=e[1])},handleFilter:function(){this.listQuery.page=1,this.getList()},restore:function(){this.listQuery.template_id="",this.listQuery.oid_like="",this.listQuery.payment_begin_time="",this.listQuery.payment_end_time="",this.listQuery.status="",this.listQuery.type="",this.listQuery.page=1,this.listQuery.size=50,this.listQuery.user_id="",this.paymentTimes=[],this.getList()}}},o=r,u=l("c701"),c=Object(u["a"])(o,i,a,!1,null,null,null);t["default"]=c.exports},"8d41":function(e,t,l){},cbfe:function(e,t,l){"use strict";l.d(t,"d",(function(){return a})),l.d(t,"b",(function(){return n})),l.d(t,"e",(function(){return s})),l.d(t,"c",(function(){return r})),l.d(t,"a",(function(){return o}));var i=l("b775");function a(e){return Object(i["a"])({url:"/coupon/index",method:"get",params:e})}function n(e){return Object(i["a"])({url:"/coupon/edit",method:"get",params:e})}function s(e){return Object(i["a"])({url:"/coupon/save",method:"post",data:e})}function r(e){return Object(i["a"])({url:"/coupon/detail",method:"get",data:e})}function o(e){return Object(i["a"])({url:"/coupon/change_status",method:"post",data:e})}}}]);