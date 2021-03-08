(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-fcd1e636"],{"2c6d":function(e,t,a){"use strict";var i=a("e977"),s=a.n(i);s.a},"367d":function(e,t,a){"use strict";a.r(t);var i=function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("el-form",{ref:"productFrom",staticClass:"box",attrs:{model:e.product,rules:e.rules,"label-width":"150px"}},[a("div",{staticClass:"box-title"},[e._v("商品类型")]),e._v(" "),a("div",{staticClass:"box-center"},[a("el-form-item",{attrs:{prop:"type","label-width":"20px"}},[a("el-radio-group",{staticStyle:{display:"flex"},model:{value:e.product.type,callback:function(t){e.$set(e.product,"type",t)},expression:"product.type"}},e._l(e.productTypes,(function(t,i){return a("el-radio",{key:"pro_"+i,staticClass:"pro-type",attrs:{label:t.id,border:""}},[a("div",{staticClass:"pro-type-item"},[a("span",[e._v(e._s(t.name))]),e._v(" "),a("span",{staticClass:"pro-type-tip"},[e._v(e._s(t.tip))])])])})),1)],1)],1),e._v(" "),a("div",{staticClass:"box-title"},[e._v("基本信息")]),e._v(" "),a("div",{staticClass:"box-center"},[a("el-form-item",{attrs:{label:"商品名",prop:"title"}},[a("el-input",{staticClass:"w500",attrs:{type:"text",placeholder:"长度5-60个字"},model:{value:e.product.title,callback:function(t){e.$set(e.product,"title",t)},expression:"product.title"}})],1),e._v(" "),a("el-form-item",{attrs:{label:"商品描述标签",prop:"tags"}},[a("el-input",{staticClass:"w500",attrs:{type:"text",placeholder:"以,号分隔"},model:{value:e.product.tags,callback:function(t){e.$set(e.product,"tags",t)},expression:"product.tags"}})],1),e._v(" "),a("el-form-item",{attrs:{label:"商品图",prop:"images"}},[a("el-upload",{staticClass:"updateImg",attrs:{action:"/api/upload_image","list-type":"picture-card",accept:"image/png, image/gif, image/jpeg",multiple:"",data:{style:"!w800h800"},"file-list":e.imageList,limit:e.maxImageNum,"on-success":e.uploadImageSuccess,"on-error":e.uploadImageError,"on-preview":e.handlePictureCardPreview,"on-remove":e.handleRemove,"on-exceed":e.uploadImageExceed}},[a("i",{staticClass:"el-icon-plus"})]),e._v(" "),a("div",{staticClass:"plusText"},[e._v("建议尺寸：800*800像素，最多上传9张")])],1),e._v(" "),a("el-form-item",{attrs:{label:"详情图片",prop:"detail_images"}},[a("el-upload",{staticClass:"updateImg",attrs:{action:"/api/upload_image","list-type":"picture-card",accept:"image/png, image/gif, image/jpeg",multiple:"",data:{style:"!w800"},"file-list":e.detailImageList,limit:e.maxDetailNum,"on-success":e.uploadDetailImageSuccess,"on-error":e.uploadImageError,"on-preview":e.handlePictureCardPreview,"on-remove":e.handleDetailRemove,"on-exceed":e.uploadDetailImageExceed}},[a("i",{staticClass:"el-icon-plus"})]),e._v(" "),a("div",{staticClass:"plusText"},[e._v("建议宽度800像素，每张大小不超过5M，最多上传15张")])],1)],1),e._v(" "),a("div",{staticClass:"box-title"},[e._v("价格库存")]),e._v(" "),a("div",{staticClass:"box-center"},[a("el-form-item",{attrs:{label:"售卖价格",prop:"price"}},[a("el-input",{staticClass:"w150",on:{blur:function(t){e.product.price=e.formatFloat(e.product.price)}},model:{value:e.product.price,callback:function(t){e.$set(e.product,"price",t)},expression:"product.price"}},[a("template",{slot:"prefix"},[e._v("￥")])],2)],1),e._v(" "),a("el-form-item",{attrs:{label:"原价",prop:"original_price"}},[a("el-input",{staticClass:"w150",on:{blur:function(t){e.product.original_price=e.formatFloat(e.product.original_price)}},model:{value:e.product.original_price,callback:function(t){e.$set(e.product,"original_price",t)},expression:"product.original_price"}},[a("template",{slot:"prefix"},[e._v("￥")])],2)],1),e._v(" "),a("el-form-item",{attrs:{label:"成本",prop:"cost"}},[a("el-input",{staticClass:"w150",on:{blur:function(t){e.product.cost=e.formatFloat(e.product.cost)}},model:{value:e.product.cost,callback:function(t){e.$set(e.product,"cost",t)},expression:"product.cost"}},[a("template",{slot:"prefix"},[e._v("￥")])],2)],1),e._v(" "),a("el-form-item",{attrs:{label:"库存",prop:"stock"}},[a("el-input",{staticClass:"w150",model:{value:e.product.stock,callback:function(t){e.$set(e.product,"stock",t)},expression:"product.stock"}})],1)],1),e._v(" "),a("div",{staticClass:"box-title"},[e._v("分享推广")]),e._v(" "),a("div",{staticClass:"box-center"},[a("el-form-item",{attrs:{label:"分享标题",prop:"share_title"}},[a("el-input",{staticClass:"w500",attrs:{placeholder:"建议28个字以内"},model:{value:e.product.share_title,callback:function(t){e.$set(e.product,"share_title",t)},expression:"product.share_title"}})],1),e._v(" "),a("el-form-item",{attrs:{label:"小程序分享图",prop:"share_image"}},[e.product.share_image?a("div",{staticClass:"el-upload-list--picture-card"},[a("div",{staticClass:"el-upload-list__item is-success"},[a("img",{staticClass:"el-upload-list__item-thumbnail",attrs:{src:e.thumbnail(e.product.share_image),alt:""}}),e._v(" "),a("label",{staticClass:"el-upload-list__item-status-label"},[a("i",{staticClass:"el-icon-upload-success el-icon-check"})]),e._v(" "),a("span",{staticClass:"el-upload-list__item-actions"},[a("span",{staticClass:"el-upload-list__item-preview",on:{click:function(t){return e.imagePreview(e.product.share_image)}}},[a("i",{staticClass:"el-icon-zoom-in"})]),e._v(" "),a("span",{staticClass:"el-upload-list__item-delete",on:{click:function(t){e.product.share_image=""}}},[a("i",{staticClass:"el-icon-delete"})])])])]):a("el-upload",{attrs:{action:"/api/upload_image","list-type":"picture-card",accept:"image/png, image/gif, image/jpeg","show-file-list":!1,"on-success":e.uploadWxAppShareImageSuccess,"on-error":e.uploadImageError}},[a("i",{staticClass:"el-icon-plus"})]),e._v(" "),a("div",{staticClass:"plusText"},[e._v("建议尺寸：500*400像素")])],1),e._v(" "),a("el-form-item",{attrs:{label:"分享海报图",prop:"share_poster"}},[e.product.share_poster?a("div",{staticClass:"el-upload-list--picture-card"},[a("div",{staticClass:"el-upload-list__item is-success"},[a("img",{staticClass:"el-upload-list__item-thumbnail",attrs:{src:e.thumbnail(e.product.share_poster),alt:""}}),e._v(" "),a("label",{staticClass:"el-upload-list__item-status-label"},[a("i",{staticClass:"el-icon-upload-success el-icon-check"})]),e._v(" "),a("span",{staticClass:"el-upload-list__item-actions"},[a("span",{staticClass:"el-upload-list__item-preview",on:{click:function(t){return e.imagePreview(e.product.share_poster)}}},[a("i",{staticClass:"el-icon-zoom-in"})]),e._v(" "),a("span",{staticClass:"el-upload-list__item-delete",on:{click:function(t){e.product.share_poster=""}}},[a("i",{staticClass:"el-icon-delete"})])])])]):a("el-upload",{attrs:{action:"/api/upload_image","list-type":"picture-card",accept:"image/png, image/gif, image/jpeg","show-file-list":!1,"on-success":e.uploadSharePosterSuccess,"on-error":e.uploadImageError}},[a("i",{staticClass:"el-icon-plus"})]),e._v(" "),a("div",{staticClass:"plusText"},[e._v("建议尺寸：800*1200像素，大小不超过2M")])],1)],1),e._v(" "),a("div",{staticClass:"box-title"},[e._v("其他信息")]),e._v(" "),a("div",{staticClass:"box-center"},[a("el-form-item",{attrs:{label:"单次购买最少数量",prop:"buy_min_num"}},[a("el-input",{staticClass:"w150",attrs:{placeholder:"默认为1"},model:{value:e.product.buy_min_num,callback:function(t){e.$set(e.product,"buy_min_num",t)},expression:"product.buy_min_num"}})],1),e._v(" "),a("el-form-item",{attrs:{label:"单次购买最大数量",prop:"buy_max_num"}},[a("el-input",{staticClass:"w150",attrs:{placeholder:"默认为99"},model:{value:e.product.buy_max_num,callback:function(t){e.$set(e.product,"buy_max_num",t)},expression:"product.buy_max_num"}})],1),e._v(" "),a("el-form-item",{attrs:{label:"每次加减步数",prop:"buy_step"}},[a("el-input",{staticClass:"w150",attrs:{placeholder:"默认为1"},model:{value:e.product.buy_step,callback:function(t){e.$set(e.product,"buy_step",t)},expression:"product.buy_step"}})],1),e._v(" "),a("el-form-item",{attrs:{label:"每人限购数量",prop:"buy_limit_num"}},[a("el-input",{staticClass:"w150",attrs:{placeholder:"默认无限制"},model:{value:e.product.buy_limit_num,callback:function(t){e.$set(e.product,"buy_limit_num",t)},expression:"product.buy_limit_num"}})],1)],1),e._v(" "),a("el-dialog",{attrs:{visible:e.dialogVisibleImg},on:{"update:visible":function(t){e.dialogVisibleImg=t}}},[a("img",{attrs:{width:"100%",src:e.dialogImageUrl,alt:""}})]),e._v(" "),a("el-form-item",[a("el-button",{attrs:{type:"primary"},on:{click:function(t){return e.doSave()}}},[e._v("立即创建")])],1)],1)},s=[],r=(a("cc1d"),a("d479"),a("c041"),a("ccc4"),a("4634"),a("c4c8")),l=a("6724"),o={name:"ProductCreate",directives:{waves:l["a"]},data:function(){return{productTypes:[{id:1,name:"预售商品",tip:"(预售不发货)"},{id:2,name:"实物商品",tip:"(物流发货)"}],maxImageNum:9,maxDetailNum:15,dialogVisibleImg:!1,dialogImageUrl:"",imageList:[],detailImageList:[],product:{type:"",title:"",tags:"",images:[],detail_images:[],price:"",original_price:"",cost:"",stock:"",share_title:"",share_image:"",share_poster:"",buy_min_num:"",buy_max_num:"",buy_step:"",buy_limit_num:"",status:""},rules:{type:[{required:!0,message:"请选择商品类型",trigger:"blur"}],title:[{required:!0,message:"请输入商品标题",trigger:"blur"},{min:5,max:60,message:"商品标题长度在 5 到 60 个字",trigger:"blur"}],images:[{required:!0,message:"请上传商品图片",trigger:"blur"}],detail_images:[{required:!0,message:"请上传详情图片",trigger:"blur"}],price:[{required:!0,message:"请输入价格",trigger:"blur"}],stock:[{required:!0,message:"请输入库存",trigger:"blur"}],share_title:[{required:!0,message:"请输入分享标题",trigger:"blur"}],share_image:[{required:!0,message:"请上传小程序分享图",trigger:"blur"}],share_poster:[{required:!0,message:"请上传分享海报",trigger:"blur"}]}}},created:function(){this.$route.query&&this.$route.query.id&&(this.product.id=this.$route.query.id),this.getData()},methods:{getData:function(){var e=this,t={};e.product.id&&(t.id=e.product.id),Object(r["a"])(t).then((function(t){t&&t.product&&(e.product=t.product,e.product.images.forEach((function(t,a){e.imageList.push({name:"img_"+a,url:t+"!w800h800"})})),e.product.detail_images.forEach((function(t,a){e.detailImageList.push({name:"detailimg_"+a,url:t+"!w800"})})))}))},doSave:function(){var e=this,t=!0;e.$refs.productFrom.validate((function(a,i){a||(e.$message.error(Object.values(i)[0][0].message),t=!1)})),t&&Object(r["e"])(e.product).then((function(t){e.$message.success("创建商品成功"),e.$router.push({path:"/product/list"})}))},uploadDetailImageSuccess:function(e,t,a){e.data.url?this.product.detail_images.push(e.data.url):(a.forEach((function(e,a){t.uid===e.uid&&this.fileList.splice(a,1)})),this.$message({type:"error",message:"上传图片失败!"}))},uploadImageSuccess:function(e,t,a){e.data.url?this.product.images.push(e.data.url):(a.forEach((function(e,a){t.uid===e.uid&&this.fileList.splice(a,1)})),this.$message({type:"error",message:"上传图片失败!"}))},uploadImageError:function(){this.$message({type:"error",message:"上传图片失败!"})},handleRemove:function(e,t){var a=this,i="!w800h800",s="";s=e.url.replace(new RegExp(i),""),a.product.images.forEach((function(e,t){s===e&&a.product.images.splice(t,1)})),a.$message({type:"success",message:"删除成功!"})},handleDetailRemove:function(e,t){var a=this,i="!w800",s="";s=e.url.replace(new RegExp(i),""),a.product.detail_images.forEach((function(e,t){s===e&&a.product.detail_images.splice(t,1)})),a.$message({type:"success",message:"删除成功!"})},handlePictureCardPreview:function(e){this.dialogImageUrl=e.url,this.dialogVisibleImg=!0},uploadImageExceed:function(){this.$message({type:"warning",message:"最多上传"+this.maxImageNum+"张图片"})},uploadDetailImageExceed:function(){this.$message({type:"warning",message:"最多上传"+this.maxDetailNum+"张图片"})},formatFloat:function(e){var t=parseFloat(e);if(isNaN(t))return"";t=Math.round(100*t)/100;var a=t.toString(),i=a.indexOf(".");i<0&&(i=a.length,a+=".");while(a.length<=i+2)a+="0";return a},imagePreview:function(e){this.dialogImageUrl=e,this.dialogVisibleImg=!0},uploadWxAppShareImageSuccess:function(e,t,a){e.data.url||this.$message({type:"error",message:"上传图片失败!"}),this.product.share_image=e.data.url},uploadSharePosterSuccess:function(e,t,a){e.data.url||this.$message({type:"error",message:"上传图片失败!"}),this.product.share_poster=e.data.url},thumbnail:function(e){return e?e.replace(/!.*$/,"")+"!w500h400":""}}},c=o,u=(a("2c6d"),a("4e82")),n=Object(u["a"])(c,i,s,!1,null,"04ebb989",null);t["default"]=n.exports},"37bd":function(e,t,a){var i=a("f9a5"),s=a("7d56"),r=a("6117"),l=a("c864").f;e.exports=function(e){return function(t){var a,o=r(t),c=s(o),u=c.length,n=0,p=[];while(u>n)a=c[n++],i&&!l.call(o,a)||p.push(e?[a,o[a]]:o[a]);return p}}},6724:function(e,t,a){"use strict";a("8d41");var i="@@wavesContext";function s(e,t){function a(a){var i=Object.assign({},t.value),s=Object.assign({ele:e,type:"hit",color:"rgba(0, 0, 0, 0.15)"},i),r=s.ele;if(r){r.style.position="relative",r.style.overflow="hidden";var l=r.getBoundingClientRect(),o=r.querySelector(".waves-ripple");switch(o?o.className="waves-ripple":(o=document.createElement("span"),o.className="waves-ripple",o.style.height=o.style.width=Math.max(l.width,l.height)+"px",r.appendChild(o)),s.type){case"center":o.style.top=l.height/2-o.offsetHeight/2+"px",o.style.left=l.width/2-o.offsetWidth/2+"px";break;default:o.style.top=(a.pageY-l.top-o.offsetHeight/2-document.documentElement.scrollTop||document.body.scrollTop)+"px",o.style.left=(a.pageX-l.left-o.offsetWidth/2-document.documentElement.scrollLeft||document.body.scrollLeft)+"px"}return o.style.backgroundColor=s.color,o.className="waves-ripple z-active",!1}}return e[i]?e[i].removeHandle=a:e[i]={removeHandle:a},a}var r={bind:function(e,t){e.addEventListener("click",s(e,t),!1)},update:function(e,t){e.removeEventListener("click",e[i].removeHandle,!1),e.addEventListener("click",s(e,t),!1)},unbind:function(e){e.removeEventListener("click",e[i].removeHandle,!1),e[i]=null,delete e[i]}},l=function(e){e.directive("waves",r)};window.Vue&&(window.waves=r,Vue.use(l)),r.install=l;t["a"]=r},"8d41":function(e,t,a){},c4c8:function(e,t,a){"use strict";a.d(t,"b",(function(){return s})),a.d(t,"a",(function(){return r})),a.d(t,"e",(function(){return l})),a.d(t,"c",(function(){return o})),a.d(t,"d",(function(){return c}));var i=a("b775");function s(e){return Object(i["a"])({url:"/api/product/index",method:"get",params:e})}function r(e){return Object(i["a"])({url:"/api/product/edit_detail",method:"get",params:e})}function l(e){return Object(i["a"])({url:"/api/product/save",method:"post",params:e})}function o(e){return Object(i["a"])({url:"/api/product/offline",method:"post",params:e})}function c(e){return Object(i["a"])({url:"/api/product/online",method:"post",params:e})}},ccc4:function(e,t,a){var i=a("2498"),s=a("37bd")(!1);i(i.S,"Object",{values:function(e){return s(e)}})},e977:function(e,t,a){}}]);