(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-0fb40702"],{"61c8":function(e,t,n){"use strict";n.r(t);var a=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{staticClass:"app-container"},[n("el-table",{directives:[{name:"loading",rawName:"v-loading",value:e.listLoading,expression:"listLoading"}],staticStyle:{width:"100%"},attrs:{data:e.operatorList,border:"",fit:"","highlight-current-row":""}},[n("el-table-column",{attrs:{label:"用户id",align:"center",prop:"uid"}}),e._v(" "),n("el-table-column",{attrs:{label:"用户昵称",align:"center",prop:"nick_name"}}),e._v(" "),n("el-table-column",{attrs:{label:"月份",align:"center",prop:"month"}}),e._v(" "),n("el-table-column",{attrs:{label:"下单时间",align:"center",prop:"order_time"}}),e._v(" "),n("el-table-column",{attrs:{label:"订单金额",align:"center",prop:"amount_paid"}}),e._v(" "),n("el-table-column",{attrs:{label:"状态",align:"center",prop:"status_desc"}})],1),e._v(" "),n("el-pagination",{directives:[{name:"show",rawName:"v-show",value:e.total>0,expression:"total > 0"}],staticStyle:{"margin-top":"20px"},attrs:{background:"",total:e.total,"current-page":e.listQuery.page,"page-size":e.listQuery.size,"page-sizes":[10,20,50,100],layout:"sizes, prev, pager, next"},on:{"update:currentPage":function(t){return e.$set(e.listQuery,"page",t)},"update:current-page":function(t){return e.$set(e.listQuery,"page",t)},"size-change":e.handleSizeChange,"current-change":e.handleCurrentChange}})],1)},r=[],i=n("b39f"),l=n("6724"),o={name:"IncomeList",components:{},directives:{waves:l["a"]},data:function(){return{operatorList:null,total:0,listLoading:!1,listQuery:{userId:"",page:1,size:50}}},computed:{},created:function(){this.$route.query&&this.$route.query.id&&(this.listQuery.userId=this.$route.query.id),this.getList()},methods:{handleSizeChange:function(){},handleCurrentChange:function(){},getList:function(){var e=this;Object(i["a"])(this.listQuery).then((function(t){e.operatorList=t.records,e.total=t.total,e.listLoading=!1}))}}},s=o,c=n("4e82"),u=Object(c["a"])(s,a,r,!1,null,null,null);t["default"]=u.exports},6724:function(e,t,n){"use strict";n("8d41");var a="@@wavesContext";function r(e,t){function n(n){var a=Object.assign({},t.value),r=Object.assign({ele:e,type:"hit",color:"rgba(0, 0, 0, 0.15)"},a),i=r.ele;if(i){i.style.position="relative",i.style.overflow="hidden";var l=i.getBoundingClientRect(),o=i.querySelector(".waves-ripple");switch(o?o.className="waves-ripple":(o=document.createElement("span"),o.className="waves-ripple",o.style.height=o.style.width=Math.max(l.width,l.height)+"px",i.appendChild(o)),r.type){case"center":o.style.top=l.height/2-o.offsetHeight/2+"px",o.style.left=l.width/2-o.offsetWidth/2+"px";break;default:o.style.top=(n.pageY-l.top-o.offsetHeight/2-document.documentElement.scrollTop||document.body.scrollTop)+"px",o.style.left=(n.pageX-l.left-o.offsetWidth/2-document.documentElement.scrollLeft||document.body.scrollLeft)+"px"}return o.style.backgroundColor=r.color,o.className="waves-ripple z-active",!1}}return e[a]?e[a].removeHandle=n:e[a]={removeHandle:n},n}var i={bind:function(e,t){e.addEventListener("click",r(e,t),!1)},update:function(e,t){e.removeEventListener("click",e[a].removeHandle,!1),e.addEventListener("click",r(e,t),!1)},unbind:function(e){e.removeEventListener("click",e[a].removeHandle,!1),e[a]=null,delete e[a]}},l=function(e){e.directive("waves",i)};window.Vue&&(window.waves=i,Vue.use(l)),i.install=l;t["a"]=i},"8d41":function(e,t,n){},b39f:function(e,t,n){"use strict";n.d(t,"b",(function(){return r})),n.d(t,"c",(function(){return i})),n.d(t,"a",(function(){return l}));var a=n("b775");function r(e){return Object(a["a"])({url:"/api/user/index",method:"get",params:e})}function i(e){return Object(a["a"])({url:"/api/user/team",method:"get",params:e})}function l(e){return Object(a["a"])({url:"/api/user/detail",method:"get",params:e})}}}]);