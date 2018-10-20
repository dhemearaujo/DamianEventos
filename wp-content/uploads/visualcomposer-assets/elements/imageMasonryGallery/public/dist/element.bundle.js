(window.vcvWebpackJsonp4x=window.vcvWebpackJsonp4x||[]).push([[0],{"./imageMasonryGallery/component.js":function(e,a,l){"use strict";Object.defineProperty(a,"__esModule",{value:!0});var n=u(l("./node_modules/babel-runtime/helpers/extends.js")),t=u(l("./node_modules/babel-runtime/core-js/object/get-prototype-of.js")),i=u(l("./node_modules/babel-runtime/helpers/classCallCheck.js")),s=u(l("./node_modules/babel-runtime/helpers/createClass.js")),o=u(l("./node_modules/babel-runtime/helpers/possibleConstructorReturn.js")),r=u(l("./node_modules/babel-runtime/helpers/inherits.js")),m=u(l("./node_modules/react/index.js")),c=u(l("./node_modules/vc-cake/index.js"));function u(e){return e&&e.__esModule?e:{default:e}}var g=function(e){function a(e){(0,i.default)(this,a);var l=(0,o.default)(this,(a.__proto__||(0,t.default)(a)).call(this,e));return l.currentImg=0,l.loadingIndex=0,l.data=[],l.loadImage=l.loadImage.bind(l),l}return(0,r.default)(a,e),(0,s.default)(a,[{key:"componentDidMount",value:function(){this.prepareImages(this.props.atts)}},{key:"componentDidUpdate",value:function(){c.default.env("iframe")&&c.default.env("iframe").vcv.trigger("ready")}},{key:"componentWillReceiveProps",value:function(e){this.currentImg=0,this.data=[],this.loadingIndex++,this.prepareImages(e.atts,!0)}},{key:"prepareImages",value:function(e){for(var a=arguments.length>1&&void 0!==arguments[1]&&arguments[1],l=e.image,n=this.getImageUrl(l),t=e.columns<=0?1:e.columns,i=[],s=0;s<t;s++)i.push(0),this.data.push([]);if(a){i=[];for(var o=0;o<t;o++)i.push(0)}this.loadImage(n,i)}},{key:"loadImage",value:function(e,a){if(e.length){var l=new window.Image;l.loadingIndex=this.loadingIndex,l.onload=this.imgLoadHandler.bind(this,e,a,l),l.src=e[this.currentImg]}else this.setState({columnData:this.data})}},{key:"imgLoadHandler",value:function(e,a,l){if(l.loadingIndex===this.loadingIndex){var n=this.getImageHeight(l.width,l.height),t=this.getSmallestFromArray(a);a[t]+=n,this.data[t].push(this.props.atts.image[this.currentImg]),this.currentImg++,this.currentImg<e.length?this.loadImage(e,a):this.setState({columnData:this.data})}}},{key:"getImageHeight",value:function(e,a){return a/(e/50)}},{key:"getSmallestFromArray",value:function(e){var a=0,l=e[0];return e.forEach(function(n,t){n<l&&(l=e[t],a=t)}),a}},{key:"getPublicImage",value:function(e){var a=this.props.atts.metaAssetsPath;return e?e.match("^(https?:)?\\/\\/?")?e:a+e:""}},{key:"render",value:function(){var e=this,a=this.props,l=a.id,t=a.atts,i=a.editor,s=t.image,o=t.shape,r=t.customClass,c=t.metaCustomId,u=t.clickableOptions,g=t.showCaption,d=["vce-image-masonry-gallery"],p=["vce-image-masonry-gallery-wrapper vce"],y={},h="div",v=this.state&&this.state.columnData,f=[];if(v){var b=0;v.forEach(function(a,t){var i=[];a&&a.forEach(function(a,t){var o=e.getImageUrl(a),r={},c="vce-image-masonry-gallery-item",d={alt:a&&a.alt?a.alt:""};if("url"===u&&a.link&&a.link.url){h="a";var p=a.link,v=p.url,f=p.title,x=p.targetBlank,w=p.relNofollow;r={href:v,title:f,target:x?"_blank":void 0,rel:w?"nofollow":void 0}}else"imageNewTab"===u?(h="a",r={href:o,target:"_blank"}):"lightbox"===u?(h="a",r={href:o,"data-lightbox":"lightbox-"+l}):"photoswipe"===u&&(h="a",r={href:o,"data-photoswipe-image":l,"data-photoswipe-index":b,"data-photoswipe-item":"photoswipe-"+l},g&&a&&a.caption&&(r["data-photoswipe-caption"]=a.caption),y["data-photoswipe-gallery"]=l,b++);s[t]&&s[t].filter&&"normal"!==s[t].filter&&(c+=" vce-image-filter--"+s[t].filter),i.push(m.default.createElement(h,(0,n.default)({},r,{className:c,key:"vce-image-masonry-gallery-item-"+t+"-"+l}),m.default.createElement("img",(0,n.default)({className:"vce-image-masonry-gallery-img",src:e.getImageUrl(a)},d))))}),f.push(m.default.createElement("div",{className:"vce-image-masonry-gallery-column",key:"vce-image-masonry-gallery-col-"+t+"-"+l},i))})}"string"==typeof r&&r&&d.push(r);var x=this.getMixinData("imageGalleryGap");x&&p.push("vce-image-masonry-gallery--gap-"+x.selector),(x=this.getMixinData("imageGalleryColumns"))&&p.push("vce-image-masonry-gallery--columns-"+x.selector),"rounded"===o&&d.push("vce-image-masonry-gallery--border-rounded"),c&&(y.id=c);var w=this.applyDO("all");return m.default.createElement("div",(0,n.default)({className:d.join(" ")},i,y),m.default.createElement("div",(0,n.default)({className:p.join(" "),id:"el-"+l},w),m.default.createElement("div",{className:"vce-image-masonry-gallery-list"},f)))}}]),a}(c.default.getService("api").elementComponent);a.default=g},"./imageMasonryGallery/index.js":function(e,a,l){"use strict";var n=i(l("./node_modules/vc-cake/index.js")),t=i(l("./imageMasonryGallery/component.js"));function i(e){return e&&e.__esModule?e:{default:e}}(0,n.default.getService("cook").add)(l("./imageMasonryGallery/settings.json"),function(e){e.add(t.default)},{css:l("./node_modules/raw-loader/index.js!./imageMasonryGallery/styles.css"),editorCss:l("./node_modules/raw-loader/index.js!./imageMasonryGallery/editor.css"),mixins:{imageGalleryColumns:{mixin:l("./node_modules/raw-loader/index.js!./imageMasonryGallery/cssMixins/imageGalleryColumns.pcss")},imageGalleryGap:{mixin:l("./node_modules/raw-loader/index.js!./imageMasonryGallery/cssMixins/imageGalleryGap.pcss")}}},"")},"./imageMasonryGallery/settings.json":function(e){e.exports={image:{type:"attachimage",access:"public",value:["image-1.jpg","image-2.jpg","image-3.jpg","image-4.jpg","image-5.jpg","image-6.jpg"],options:{label:"Images",multiple:!0,onChange:{rules:{clickableOptions:{rule:"value",options:{value:"url"}}},actions:[{action:"attachImageUrls"}]},url:!1,imageFilter:!0}},shape:{type:"buttonGroup",access:"public",value:"square",options:{label:"Shape",values:[{label:"Square",value:"square",icon:"vcv-ui-icon-attribute-shape-square"},{label:"Rounded",value:"rounded",icon:"vcv-ui-icon-attribute-shape-rounded"}]}},designOptions:{type:"designOptions",access:"public",value:{},options:{label:"Design Options"}},editFormTab1:{type:"group",access:"protected",value:["clickableOptions","showCaption","image","columns","gap","shape","metaCustomId","customClass"],options:{label:"General"}},metaEditFormTabs:{type:"group",access:"protected",value:["editFormTab1","designOptions"]},relatedTo:{type:"group",access:"protected",value:["General"]},customClass:{type:"string",access:"public",value:"",options:{label:"Extra class name",description:"Add an extra class name to the element and refer to it from Custom CSS option."}},clickableOptions:{type:"dropdown",access:"public",value:"lightbox",options:{label:"OnClick action",values:[{label:"None",value:""},{label:"Lightbox",value:"lightbox"},{label:"PhotoSwipe",value:"photoswipe"},{label:"Open Image in New Tab",value:"imageNewTab"},{label:"Link selector",value:"url"}]}},showCaption:{type:"toggle",access:"public",value:!1,options:{label:"Show image caption in gallery view",onChange:{rules:{clickableOptions:{rule:"value",options:{value:"photoswipe"}}},actions:[{action:"toggleVisibility"}]}}},gap:{type:"number",access:"public",value:"10",options:{label:"Gap",description:"Enter gap in pixels (Example: 5).",cssMixin:{mixin:"imageGalleryGap",property:"gap",namePattern:"[\\da-f]+"}}},columns:{type:"number",access:"public",value:"3",options:{label:"Number of Columns",cssMixin:{mixin:"imageGalleryColumns",property:"columns",namePattern:"[\\da-f]+"}}},metaCustomId:{type:"customId",access:"public",value:"",options:{label:"Element ID",description:"Apply unique ID to element to link directly to it by using #your_id (for element ID use lowercase input only)."}},tag:{access:"protected",type:"string",value:"imageMasonryGallery"},sharedAssetsLibrary:{access:"protected",type:"string",value:{libraries:[{rules:{clickableOptions:{rule:"value",options:{value:"lightbox"}}},libsNames:["lightbox"]},{rules:{clickableOptions:{rule:"value",options:{value:"photoswipe"}}},libsNames:["photoswipe"]}]}}}},"./node_modules/raw-loader/index.js!./imageMasonryGallery/cssMixins/imageGalleryColumns.pcss":function(e,a){e.exports="@media (min-width: 544px) {\n  .vce-image-masonry-gallery {\n    &--columns-$selector {\n      .vce-image-masonry-gallery-column {\n        @if $columns != false {\n          flex: 0 0 calc(100% / $columns);\n          max-width: calc(100% / $columns);\n        }\n      }\n    }\n  }\n}\n\n\n"},"./node_modules/raw-loader/index.js!./imageMasonryGallery/cssMixins/imageGalleryGap.pcss":function(e,a){e.exports=".vce-image-masonry-gallery {\n  &--gap-$selector {\n    @if $gap != false {\n      .vce-image-masonry-gallery-list {\n        margin-left: calc(-$(gap)px / 2);\n        margin-right: calc(-$(gap)px / 2);\n        margin-bottom: -$(gap)px;\n      }\n    }\n\n    @if $gap != false {\n      .vce-image-masonry-gallery-item {\n        padding-left: calc($(gap)px / 2);\n        padding-right: calc($(gap)px / 2);\n        margin-bottom: $(gap)px;\n      }\n    }\n  }\n}\n\n"},"./node_modules/raw-loader/index.js!./imageMasonryGallery/editor.css":function(e,a){e.exports=".vce-image-masonry-gallery {\n  min-height: 1em;\n}\n"},"./node_modules/raw-loader/index.js!./imageMasonryGallery/styles.css":function(e,a){e.exports=".vce-image-masonry-gallery-wrapper {\n  overflow: hidden;\n}\n\n.vce-image-masonry-gallery-list {\n  flex: 1 1 auto;\n  display: flex;\n  flex-direction: row;\n  flex-wrap: wrap;\n  justify-content: flex-start;\n  align-items: flex-start;\n  align-content: flex-start;\n}\n\n.vce-image-masonry-gallery-column {\n  flex: 0 0 100%;\n  max-width: 100%;\n  box-sizing: border-box;\n}\n\n.vce-image-masonry-gallery-item {\n  display: block;\n}\n\na.vce-image-masonry-gallery-item {\n  color: transparent;\n  border-bottom: 0;\n  text-decoration: none;\n  box-shadow: none;\n}\n\na.vce-image-masonry-gallery-item:hover,\na.vce-image-masonry-gallery-item:focus {\n  border-bottom: 0;\n  text-decoration: none;\n  box-shadow: none;\n}\n\n.vce-image-masonry-gallery-img {\n  width: 100%;\n  height: auto;\n}\n\n.vce-image-masonry-gallery--border-rounded .vce-image-masonry-gallery-img {\n  border-radius: 5px;\n}\n\n.vce-image-masonry-gallery .vce-image-masonry-gallery-item .vce-image-masonry-gallery-img {\n  box-shadow: none;\n}\n"}},[["./imageMasonryGallery/index.js"]]]);