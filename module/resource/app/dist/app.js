"use strict";var App=React.createClass({displayName:"App",getInitialState:function(){return{}},getPath:function(e){return e=e.replace(/^\/|\/$/g,""),e.split("/")},render:function(){return React.createElement("div",{className:"mod-resource"},React.createElement(Header,{apiUrl:userData.apiUrls.nav,path:this.getPath(this.props.location.pathname),ref:"header"}),React.cloneElement(this.props.children,{ref:"child",parent:this,userData:userData}))}});
"use strict";var Header=React.createClass({displayName:"Header",currentTitle:"",getInitialState:function(){return{nav:[]}},componentDidMount:function(){var t=this.props.apiUrl;this.getNavigations(t)},getNavigations:function(t){var e=this;jQuery.get(t,function(t){if(t)try{t=JSON.parse(t),t.result&&e.setState({nav:t.result})}catch(a){}})},getTitle:function(){return this.currentTitle=$(ReactDOM.findDOMNode(this)).find("li.active > a").text(),this.currentTitle},render:function(){var t=this,e=this.state.nav.map(function(e,a){var r="";return e.url=e.id?"/nest/"+e.id+"/":"/",("nest"==t.props.path[0]||"article"==t.props.path[0]||0==a&&!t.props.path[0])&&(r=e.id==t.props.path[1]?"active":"",e.id==t.props.path[1]&&(t.currentTitle=e.name)),React.createElement("li",{key:"category-"+a,className:r},React.createElement(Link,{to:e.url},e.name))});return React.createElement("nav",{className:"category"},React.createElement("ul",null,e,React.createElement("li",{key:"category-setting",className:"setting"==this.props.path[0]?"active":""},React.createElement(Link,{to:"/setting/"},"SETTING"))))}});
"use strict";var Index=React.createClass({displayName:"Index",currentNest:"",currentPage:1,count:10,getInitialState:function(){return{loading:!1,items:[],navigation:null,title:""}},componentDidMount:function(){this.currentNest=this.props.params.nest_id,this.currentPage=this.props.location.query.page?this.props.location.query.page:1,this.setState({loading:!0}),this.getItems(this.currentNest,this.currentPage)},componentWillReceiveProps:function(e){var t=e.location.query.page?e.location.query.page:1,a=this.currentNest==e.params.nest_id;(!a||a&&this.currentPage!=t)&&(this.currentNest=e.params.nest_id,this.currentPage=t,this.setState({loading:!0}),this.getItems(this.currentNest,this.currentPage))},getItems:function(e,t){var a=this,n=this.props.userData.apiUrls.articles;n+="?format=json",n+="&field=srl,category_srl,nest_srl,title,hit,json,regdate,modate",n+="&count="+this.count,n+="&page="+t,n+=e?"&nest_id="+e:"",jQuery.get(n,function(e){try{e=JSON.parse(e),a.setState({loading:!1,items:e.result,navigation:e.navigation,title:a.props.parent.refs.header.getTitle()})}catch(t){}})},render:function(){var e=this,t=void 0,a=void 0,n=React.createElement("li",{className:"loading-page"},React.createElement("span",{className:"inner-circles-loader"},"loading symbol"),React.createElement("span",{className:"message"},"loading.."));return this.state.loading?t=n:this.state.items.length?(t=this.state.items.map(function(t,a){var n={backgroundImage:"url('"+e.props.userData.url_gooseAdmin+"/"+t.json.thumnail.url+"')"};return React.createElement("li",{key:a},React.createElement(Link,{to:"/article/"+(e.currentNest?e.currentNest:"new")+"/"+t.srl+"/"},React.createElement("figure",{style:n},"image"),React.createElement("div",{className:"bd"},React.createElement("span",{className:"category-name"},t.category_name),React.createElement("strong",{className:"title"},t.title),React.createElement("div",{className:"inf"},React.createElement("span",null,React.createElement("b",null,"Ver : "+t.json.version)),React.createElement("span",null,"Update : "+t.modate),React.createElement("span",null,"Hit : "+t.hit),React.createElement("span",null,"Like : "+t.json.like)))))}),this.state.navigation&&!function(){var t=e.state.navigation,n=e.currentNest?"/nest/"+e.currentNest+"/":"/",s=e.props.location.query.keyword?"keyword="+e.props.location.query.keyword+"&":"";a=React.createElement("nav",{className:"paginate"},t.prev?React.createElement(Link,{to:n+"?"+s+"page="+t.prev.id,title:t.prev.name,className:"dir"},t.prev.name):null,t.body.map(function(e,t){var a=n+"?"+s+"page="+e.id;return e.active?React.createElement("strong",{key:"pageNav-"+t},e.name):React.createElement(Link,{to:a,key:"pageNav-"+t},e.name)}),t.next?React.createElement(Link,{to:n+"?"+s+"page="+t.next.id,title:t.next.name,className:"dir"},t.next.name):null)}()):t=React.createElement("li",{className:"noitem"},React.createElement("span",{className:"icon-close blades thick"},"loading icon"),React.createElement("span",{className:"message"},"not found item")),React.createElement("section",null,React.createElement("h1",null,this.state.title),React.createElement("ul",{className:"index"},t),a)}});
"use strict";var Setting=React.createClass({displayName:"Setting",$form:null,getInitialState:function(){return{title:"",readyFTP:!1}},componentDidMount:function(){this.$form=$(ReactDOM.findDOMNode(this.refs.settingForm)),this.setState({title:this.props.parent.refs.header.getTitle()})},checkFTP:function(){var e=this;$.post(this.props.userData.url+"/testFTP/",this.$form.serialize(),function(t){t=JSON.parse(t),"success"==t.state?(e.setState({readyFTP:!0}),alert(t.message)):(e.setState({readyFTP:!1}),alert(t.message))})},render:function(){return React.createElement("section",null,React.createElement("h1",null,this.state.title),React.createElement("form",{action:this.props.userData.url+"/updateFTP/",method:"post",name:"ftpSetting",className:"setting",ref:"settingForm"},React.createElement("input",{type:"hidden",name:"redir",defaultValue:this.props.userData.url+"/#/setting/"}),React.createElement("fieldset",null,React.createElement("legend",{className:"blind"},"ftp 설정 폼"),React.createElement("dl",{className:"first"},React.createElement("dt",null,React.createElement("label",{htmlFor:"host_name"},"Host name")),React.createElement("dd",null,React.createElement("input",{type:"text",name:"host_name",id:"host_name",maxLength:"40",size:"24",defaultValue:this.props.userData.ftp.host?this.props.userData.ftp.host:"",placeholder:"hostname.com"}))),React.createElement("dl",null,React.createElement("dt",null,React.createElement("label",{htmlFor:"host_id"},"ID")),React.createElement("dd",null,React.createElement("input",{type:"text",name:"host_id",id:"host_id",size:"15",maxLength:"20",defaultValue:this.props.userData.ftp.id?this.props.userData.ftp.id:"",placeholder:"FTP ID"}))),React.createElement("dl",null,React.createElement("dt",null,React.createElement("label",{htmlFor:"host_pw"},"Password")),React.createElement("dd",null,React.createElement("input",{type:"password",name:"host_pw",id:"host_pw",size:"15",maxLength:"20",defaultValue:"",placeholder:"FTP Password"}))),React.createElement("dl",null,React.createElement("dt",null,React.createElement("label",{htmlFor:"host_pwd"},"Location")),React.createElement("dd",null,React.createElement("input",{type:"text",name:"host_pwd",id:"host_pwd",size:"40",maxLength:"100",className:"block",defaultValue:this.props.userData.ftp.pwd,placeholder:"ftp Password"}),React.createElement("p",null,"goose가 설치된 ftp경로를 지정합니다.")))),React.createElement("nav",{className:"btn-group"},React.createElement(Link,{to:"/",className:"ui-button size-large"},"목록"),React.createElement("button",{type:"button",className:"ui-button size-large color-key",onClick:this.checkFTP},"FTP 테스트"),React.createElement("button",{type:"submit",className:"ui-button size-large color-key","data-action":"update_ftp",disabled:this.state.readyFTP?"":"disabled"},"업데이트"))))}});
"use strict";var View=React.createClass({displayName:"View",srl:null,$popup:null,getInitialState:function(){return{title:"",loading:!0,item:null,countLike:0,countHit:0,enableLike:!this.getCookie("like-"+this.props.params.srl),mode_install:!1}},componentDidMount:function(){this.srl=this.props.params.srl,this.getItem(this.srl)},setCookie:function(e,t,a){var s=new Date,n=e+"="+t+"; path=/ ";s.setDate(s.getDate()+a),n+="undefined"!=typeof a?";expires="+s.toGMTString()+";":"",document.cookie=n},getCookie:function(e){e+="=";var t=document.cookie,a=t.indexOf(e),s="";if(-1!=a){a+=e.length;var n=t.indexOf(";",a);-1==n&&(n=t.length),s=t.substring(a,n)}return s},getItem:function(e){var t=this,a=this.props.userData.apiUrls.article;a+="?format=json",a+="&srl="+e,jQuery.get(a,function(a){try{a=JSON.parse(a),t.updateHit(e),t.setState({loading:!1,item:a.result,title:t.props.parent.refs.header.getTitle()}),t.makeInstallPopup(a.result)}catch(s){}})},makeInstallPopup:function(e){var t=this,a=this.props.userData.url_gooseAPI+"/install/",s=e.json.install_loc,n=e.json.install_src.location,l=e.title;this.$popup=$('<article id="mod_resource_popup" class="mod-resource-popup on"><div class="bg"></div><form action="'+a+'" method="post"><h1>Install</h1><input type="hidden" name="install_file" value="'+n+'"><fieldset><legend class="blind">Install form</legend><p class="guide"><strong>'+l+'</strong>은 설치경로 항목의 경로에 설치됩니다.<br/>경로를 변경할 수 있지만 작동이 안될 수 있습니다.</p><dl><dt><label for="frm_pwd">설치경로</label></dt><dd><input type="text" name="pwd" id="frm_pwd" value="'+s+'" /></dd></dl></fieldset><div class="loading">loading...</div><nav><span><button type="button" class="ui-button color-danger block close">Close</button></span><span><button type="submit" class="ui-button color-install block">Install</button></span></nav></form></article>'),this.$popup.find("div.bg, button.close").on("click",function(){$("html").removeClass("mode-mod-resource-popup"),t.$popup.removeClass("on")}),this.$popup.find("form").on("submit",function(){var e=$(this).find(".loading");return e.addClass("on"),jQuery.ajax({url:this.action,method:"post",data:$(this).serialize(),headers:{Accept:"application=goose;"}}).done(function(t){log("done"),log(t),e.removeClass("on")}).fail(function(e){log("fail"),log(e)}),!1});var o=function(){$("html").removeClass("mode-mod-resource-popup"),t.$popup.find("div.bg, button.close").off(),t.$popup.find("form").off(),t.$popup.remove()};window.onhashchange=function(){o(),window.onhashchange=null},$("body").append(this.$popup)},updateHit:function(e){var t=this;if(!this.getCookie("hit-"+this.srl)){var a=this.props.userData.url_gooseAPI;a+="/article/updateHit/",a+="?format=json&srl="+this.srl,jQuery.ajax({url:a,headers:{Accept:"application=goose;"}}).done(function(e){try{if(e=JSON.parse(e),"success"!=e.state)throw"error update hit";t.setCookie("hit-"+t.srl,"1",1),t.setState({countHit:e.result.hit})}catch(a){alert(a)}})}},upLike:function(){var e=this;if(!this.getCookie("like-"+this.srl)){var t=this.props.userData.url_gooseAPI;t+="/article/updateLike/",t+="?format=json&srl="+this.srl,jQuery.ajax({url:t,headers:{Accept:"application=goose;"}}).done(function(t){try{if(t=JSON.parse(t),"success"!=t.state)throw"error update like";e.setCookie("like-"+e.srl,"1",1),e.setState({countLike:t.result.like,enableLike:!1})}catch(a){alert(a)}})}},install:function(){$("html").addClass("mode-mod-resource-popup"),this.$popup.addClass("on")},render:function(){var e=null,t=React.createElement("div",{className:"loading-page"},React.createElement("span",{className:"inner-circles-loader"},"loading symbol"),React.createElement("span",{className:"message"},"loading.."));if(this.state.loading)e=t;else if(this.state.item){var a=this.state.item,s=this.state.countHit?this.state.countHit:a.hit,n=this.state.countLike?this.state.countLike:a.json.like;e=React.createElement("section",{className:"view"},React.createElement("header",null,React.createElement("h1",null,React.createElement("span",null,a.title),React.createElement("em",{className:"version"},a.json.version)),React.createElement("div",{className:"metas"},React.createElement("span",null,"Uploader : "+a.json.user.name),React.createElement("span",null,"Regdate : "+a.regdate),React.createElement("span",null,"Updated : "+a.modate),React.createElement("span",null,"Hit : ",React.createElement("em",{"data-target":"hit"},s)),React.createElement("span",null,"Like : ",React.createElement("em",{"data-target":"like"},n)))),React.createElement("div",{className:"con-body",dangerouslySetInnerHTML:{__html:a.content}}),React.createElement("nav",{className:"nav-bottom"},React.createElement("button",{type:"button",className:"ui-button size-large color-install",onClick:this.install},"Install"),React.createElement("button",{type:"button",className:"ui-button size-large color-key",onClick:this.upLike,disabled:this.state.enableLike?"":"disabled"},React.createElement("span",null,"Like"),React.createElement("em",null,n)),React.createElement(Link,{to:"/nest/"+this.props.params.nest_id+"/",className:"ui-button size-large color-danger"},"Close")))}else e=React.createElement("div",{className:"noitem"},React.createElement("span",{className:"icon-close blades thick"},"loading icon"),React.createElement("span",{className:"message"},"not found item"));return e}});
"use strict";var Router=window.ReactRouter.Router,Link=window.ReactRouter.Link,Route=window.ReactRouter.Route,IndexRoute=window.ReactRouter.IndexRoute,createHashHistory=window.History.createHashHistory;ReactDOM.render(React.createElement(Router,{history:createHashHistory({queryKey:!1})},React.createElement(Route,{path:"/",component:App},React.createElement(IndexRoute,{component:Index}),React.createElement(Route,{path:"nest/:nest_id/",component:Index}),React.createElement(Route,{path:"article/:nest_id/:srl/",component:View}),React.createElement(Route,{path:"setting/",component:Setting}))),document.getElementById("resourceApp"));