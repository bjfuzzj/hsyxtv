(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-39b6dd8e"],{"471f":function(e,t,r){},6724:function(e,t,r){"use strict";r("8d41");var a="@@wavesContext";function s(e,t){function r(r){var a=Object.assign({},t.value),s=Object.assign({ele:e,type:"hit",color:"rgba(0, 0, 0, 0.15)"},a),i=s.ele;if(i){i.style.position="relative",i.style.overflow="hidden";var l=i.getBoundingClientRect(),o=i.querySelector(".waves-ripple");switch(o?o.className="waves-ripple":(o=document.createElement("span"),o.className="waves-ripple",o.style.height=o.style.width=Math.max(l.width,l.height)+"px",i.appendChild(o)),s.type){case"center":o.style.top=l.height/2-o.offsetHeight/2+"px",o.style.left=l.width/2-o.offsetWidth/2+"px";break;default:o.style.top=(r.pageY-l.top-o.offsetHeight/2-document.documentElement.scrollTop||document.body.scrollTop)+"px",o.style.left=(r.pageX-l.left-o.offsetWidth/2-document.documentElement.scrollLeft||document.body.scrollLeft)+"px"}return o.style.backgroundColor=s.color,o.className="waves-ripple z-active",!1}}return e[a]?e[a].removeHandle=r:e[a]={removeHandle:r},r}var i={bind:function(e,t){e.addEventListener("click",s(e,t),!1)},update:function(e,t){e.removeEventListener("click",e[a].removeHandle,!1),e.addEventListener("click",s(e,t),!1)},unbind:function(e){e.removeEventListener("click",e[a].removeHandle,!1),e[a]=null,delete e[a]}},l=function(e){e.directive("waves",i)};window.Vue&&(window.waves=i,Vue.use(l)),i.install=l;t["a"]=i},"8d41":function(e,t,r){},"9d7b":function(e,t,r){"use strict";var a=r("471f"),s=r.n(a);s.a},"9dd3":function(e,t,r){"use strict";r.r(t);var a=function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("el-form",{ref:"orderFrom",staticClass:"box",attrs:{model:e.order,"label-width":"150px"}},[r("div",{staticClass:"box-title"},[e._v("基本信息")]),e._v(" "),r("div",{staticClass:"box-center"},[r("el-form-item",{attrs:{label:"订单id",prop:"oid"}},[r("el-input",{staticClass:"w500",attrs:{type:"text",disabled:""},model:{value:e.order.id,callback:function(t){e.$set(e.order,"id",t)},expression:"order.id"}})],1),e._v(" "),e.order.product?r("el-form-item",{attrs:{label:"产品名",prop:"title"}},[r("el-input",{staticClass:"w500",attrs:{type:"text",disabled:""},model:{value:e.order.product.title,callback:function(t){e.$set(e.order.product,"title",t)},expression:"order.product.title"}})],1):e._e(),e._v(" "),r("el-form-item",{attrs:{label:"购买数量",prop:"buy_num"}},[r("el-input",{staticClass:"w500",attrs:{type:"text",disabled:""},model:{value:e.order.buy_num,callback:function(t){e.$set(e.order,"buy_num",t)},expression:"order.buy_num"}})],1),e._v(" "),r("el-form-item",{attrs:{label:"总价格(含优惠)",prop:"amount_total"}},[r("el-input",{staticClass:"w500",attrs:{type:"text",disabled:""},model:{value:e.order.amount_total,callback:function(t){e.$set(e.order,"amount_total",t)},expression:"order.amount_total"}})],1),e._v(" "),r("el-form-item",{attrs:{label:"应付金额(不含优惠)",prop:"amount_payable"}},[r("el-input",{staticClass:"w500",attrs:{type:"text",disabled:""},model:{value:e.order.amount_payable,callback:function(t){e.$set(e.order,"amount_payable",t)},expression:"order.amount_payable"}})],1),e._v(" "),r("el-form-item",{attrs:{label:"已付金额",prop:"amount_paid"}},[r("el-input",{staticClass:"w500",attrs:{type:"text",disabled:""},model:{value:e.order.amount_paid,callback:function(t){e.$set(e.order,"amount_paid",t)},expression:"order.amount_paid"}})],1),e._v(" "),r("el-form-item",{attrs:{label:"应退金额",prop:"amount_refundable"}},[r("el-input",{staticClass:"w500",attrs:{type:"text",disabled:""},model:{value:e.order.amount_refundable,callback:function(t){e.$set(e.order,"amount_refundable",t)},expression:"order.amount_refundable"}})],1),e._v(" "),r("el-form-item",{attrs:{label:"已退金额",prop:"amount_refunded"}},[r("el-input",{staticClass:"w500",attrs:{type:"text",disabled:""},model:{value:e.order.amount_refunded,callback:function(t){e.$set(e.order,"amount_refunded",t)},expression:"order.amount_refunded"}})],1),e._v(" "),r("el-form-item",{attrs:{label:"订单状态",prop:"status_desc"}},[r("el-input",{staticClass:"w500",attrs:{type:"text",disabled:""},model:{value:e.order.status_desc,callback:function(t){e.$set(e.order,"status_desc",t)},expression:"order.status_desc"}})],1),e._v(" "),r("el-form-item",{attrs:{label:"创建时间",prop:"create_time"}},[r("el-input",{staticClass:"w500",attrs:{type:"text",disabled:""},model:{value:e.order.created_at,callback:function(t){e.$set(e.order,"created_at",t)},expression:"order.created_at"}})],1),e._v(" "),r("el-form-item",{attrs:{label:"支付时间",prop:"paid_time"}},[r("el-input",{staticClass:"w500",attrs:{type:"text",disabled:""},model:{value:e.order.paid_time,callback:function(t){e.$set(e.order,"paid_time",t)},expression:"order.paid_time"}})],1)],1),e._v(" "),e.order.user?r("div",{staticClass:"box-title"},[e._v("用户信息")]):e._e(),e._v(" "),e.order.user?r("div",{staticClass:"box-center"},[r("el-form-item",{attrs:{label:"用户昵称",prop:"nick_name"}},[r("el-input",{staticClass:"w500",attrs:{type:"text",disabled:""},model:{value:e.order.user.nick_name,callback:function(t){e.$set(e.order.user,"nick_name",t)},expression:"order.user.nick_name"}})],1),e._v(" "),r("el-form-item",{attrs:{label:"真实姓名",prop:"real_name"}},[r("el-input",{staticClass:"w500",attrs:{type:"text",disabled:""},model:{value:e.order.user.real_name,callback:function(t){e.$set(e.order.user,"real_name",t)},expression:"order.user.real_name"}})],1),e._v(" "),r("el-form-item",{attrs:{label:"联系手机",prop:"mobile"}},[r("el-input",{staticClass:"w500",attrs:{type:"text",disabled:""},model:{value:e.order.user.mobile,callback:function(t){e.$set(e.order.user,"mobile",t)},expression:"order.user.mobile"}})],1)],1):e._e(),e._v(" "),r("div",{staticClass:"box-title"},[e._v("物流信息")]),e._v(" "),r("div",{staticClass:"box-center"},e._l(e.order.logistics,(function(t,a){return r("div",{key:a},[r("el-form-item",{attrs:{label:"快递状态",prop:"status_desc"}},[r("el-input",{staticClass:"w500",attrs:{disabled:""},model:{value:t.status_desc,callback:function(r){e.$set(t,"status_desc",r)},expression:"logistic.status_desc"}})],1),e._v(" "),r("el-form-item",{attrs:{label:"快递公司",prop:"express_name"}},[r("el-input",{staticClass:"w500",model:{value:t.express_name,callback:function(r){e.$set(t,"express_name",r)},expression:"logistic.express_name"}})],1),e._v(" "),r("el-form-item",{attrs:{label:"快递单号",prop:"express_no"}},[r("el-input",{attrs:{type:"textarea"},model:{value:t.express_no,callback:function(r){e.$set(t,"express_no",r)},expression:"logistic.express_no"}})],1),e._v(" "),r("el-form-item",{attrs:{label:"需发货数量",prop:"quantity"}},[r("el-input",{staticClass:"w500",model:{value:t.quantity,callback:function(r){e.$set(t,"quantity",r)},expression:"logistic.quantity"}})],1),e._v(" "),r("el-form-item",{attrs:{label:"寄送省份",prop:"receiver_province"}},[r("el-input",{staticClass:"w500",model:{value:t.receiver_province,callback:function(r){e.$set(t,"receiver_province",r)},expression:"logistic.receiver_province"}})],1),e._v(" "),r("el-form-item",{attrs:{label:"寄送城市",prop:"receiver_city"}},[r("el-input",{staticClass:"w500",model:{value:t.receiver_city,callback:function(r){e.$set(t,"receiver_city",r)},expression:"logistic.receiver_city"}})],1),e._v(" "),r("el-form-item",{attrs:{label:"寄送地区",prop:"receiver_district"}},[r("el-input",{staticClass:"w500",model:{value:t.receiver_district,callback:function(r){e.$set(t,"receiver_district",r)},expression:"logistic.receiver_district"}})],1),e._v(" "),r("el-form-item",{attrs:{label:"寄送地址",prop:"address"}},[r("el-input",{staticClass:"w500",model:{value:t.receiver_address,callback:function(r){e.$set(t,"receiver_address",r)},expression:"logistic.receiver_address"}})],1),e._v(" "),r("el-form-item",{attrs:{label:"收件人",prop:"receiver_name"}},[r("el-input",{staticClass:"w500",model:{value:t.receiver_name,callback:function(r){e.$set(t,"receiver_name",r)},expression:"logistic.receiver_name"}})],1),e._v(" "),r("el-form-item",{attrs:{label:"收件电话",prop:"phone"}},[r("el-input",{staticClass:"w500",model:{value:t.receiver_mobile,callback:function(r){e.$set(t,"receiver_mobile",r)},expression:"logistic.receiver_mobile"}})],1),e._v(" "),e.order.logistics&&a!=e.order.logistics.length-1?r("el-divider"):e._e()],1)})),0),e._v(" "),r("div",{staticClass:"box-title"},[e._v("其他")]),e._v(" "),r("div",{staticClass:"box-center"},[r("el-form-item",{attrs:{label:"管理员备注",prop:"adminnote"}},[r("el-input",{attrs:{type:"textarea"},model:{value:e.order.adminnote,callback:function(t){e.$set(e.order,"adminnote",t)},expression:"order.adminnote"}})],1)],1),e._v(" "),r("el-form-item",[r("el-button",{attrs:{type:"primary"},on:{click:function(t){return e.doSaveNote()}}},[e._v("保存")])],1)],1)},s=[],i=r("f8b7"),l=r("6724"),o={name:"OrderDetail",directives:{waves:l["a"]},data:function(){return{order:{}}},created:function(){this.$route.query&&this.$route.query.id&&(this.order.id=this.$route.query.id),this.getData()},methods:{getData:function(){var e=this,t={};e.order.id&&(t.id=e.order.id),Object(i["b"])(t).then((function(t){t&&t.order&&(e.order=t.order)}))},doSaveNote:function(){var e=this;Object(i["a"])(e.order).then((function(t){e.$message.success("操作成功"),window.location.reload()}))}}},n=o,c=(r("9d7b"),r("4e82")),d=Object(c["a"])(n,a,s,!1,null,"de4644aa",null);t["default"]=d.exports},f8b7:function(e,t,r){"use strict";r.d(t,"c",(function(){return s})),r.d(t,"b",(function(){return i})),r.d(t,"e",(function(){return l})),r.d(t,"a",(function(){return o})),r.d(t,"d",(function(){return n}));var a=r("b775");function s(e){return Object(a["a"])({url:"/api/order/index",method:"get",params:e})}function i(e){return Object(a["a"])({url:"/api/order/detail",method:"post",params:e})}function l(e){return Object(a["a"])({url:"/api/order/markSend",method:"post",params:e})}function o(e){return Object(a["a"])({url:"/api/order/doSave",method:"post",params:e})}function n(e){return Object(a["a"])({url:"/api/order/markNoSend",method:"post",params:e})}}}]);