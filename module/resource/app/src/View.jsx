const View = React.createClass({

	displayName : 'View',
	srl : null,
	$popup : null,

	getInitialState()
	{
		return {
			title : '',
			loading : true,
			item : null,
			countLike : 0,
			countHit : 0,
			enableLike : (!this.getCookie('like-' + this.props.params.srl)),
			mode_install : false
		};
	},

	componentDidMount()
	{
		this.srl = this.props.params.srl;
		this.getItem(this.srl);
	},

	setCookie(name, value, cDay)
	{
		let expire = new Date();
		let cookies = name + '=' + value + '; path=/ ';
		expire.setDate(expire.getDate() + cDay);
		cookies += (typeof cDay != 'undefined') ? ';expires=' + expire.toGMTString() + ';' : '';
		document.cookie = cookies;
	},

	getCookie(name)
	{
		name += '=';
		let cookieData = document.cookie;
		let start = cookieData.indexOf(name);
		let value = '';

		if (start != -1)
		{
			start += name.length;
			var end = cookieData.indexOf(';', start);
			if(end == -1)end = cookieData.length;
			value = cookieData.substring(start, end);
		}
		return value;
	},

	getItem(srl)
	{
		let self = this;
		let url = this.props.userData.apiUrls.article;

		url += '?format=json';
		url += '&srl=' + srl;

		jQuery.get(url, function(response){
			try {
				response = JSON.parse(response);
				self.updateHit(srl);
				self.setState({
					loading : false,
					item : response.result,
					title : self.props.parent.refs.header.getTitle()
				});
				self.makeInstallPopup(response.result);
			} catch(err) {}
		});
	},

	makeInstallPopup(res)
	{
		// TODO : 팝업 부분을 컴포넌트로 변경하기;; 할 수 있을듯...
		// TODO : 설치는 이 goose에서 설치해야함!!

		let self = this;
		let url = this.props.userData.url_gooseAPI + '/install/'; // TODO : 여기 경로로 인스톨 경로 변경해야함.
		let install_loc = res.json.install_loc;
		let install_src = res.json.install_src.location;
		let title = res.title;
		this.$popup = $('<article id="mod_resource_popup" class="mod-resource-popup on">' +
			'<div class="bg"></div>' +
			'<form action="' + url + '" method="post">' +
				'<h1>Install</h1>' +
				'<input type="hidden" name="install_file" value="' + install_src + '">' +
				'<fieldset>' +
					'<legend class="blind">Install form</legend>' +
					'<p class="guide">' +
						'<strong>' + title + '</strong>은 설치경로 항목의 경로에 설치됩니다.<br/>' +
						'경로를 변경할 수 있지만 작동이 안될 수 있습니다.' +
					'</p>' +
					'<dl>' +
						'<dt><label for="frm_pwd">설치경로</label></dt>' +
						'<dd><input type="text" name="pwd" id="frm_pwd" value="' + install_loc + '" /></dd>' +
					'</dl>' +
				'</fieldset>' +
				'<div class="loading">' +
					'loading...' +
				'</div>' +
				'<nav>' +
					'<span><button type="button" class="ui-button color-danger block close">Close</button></span>' +
					'<span><button type="submit" class="ui-button color-install block">Install</button></span>' +
				'</nav>' +
			'</form>' +
		'</article>');

		this.$popup.find('div.bg, button.close').on('click', function(){
			$('html').removeClass('mode-mod-resource-popup');
			self.$popup.removeClass('on');
		});

		this.$popup.find('form').on('submit', function(){
			let $loading = $(this).find('.loading');
			$loading.addClass('on');
			jQuery.ajax({
				url : this.action,
				method : 'post',
				data : $(this).serialize(),
				headers : { 'Accept' : 'application=goose;' }
			}).done(function(response){
				log('done');
				log(response);
				$loading.removeClass('on');
			}).fail(function(res){
				log('fail');
				log(res);
			});
			return false;
		});

		let destroyPopup = function()
		{
			$('html').removeClass('mode-mod-resource-popup');
			self.$popup.find('div.bg, button.close').off();
			self.$popup.find('form').off();
			self.$popup.remove();
		};

		window.onhashchange = function() {
			destroyPopup();
			window.onhashchange = null;
		};

		$('body').append(this.$popup);
	},

	updateHit(srl)
	{
		let self = this;

		if (!this.getCookie('hit-' + this.srl))
		{
			let url = this.props.userData.url_gooseAPI;
			url += '/article/updateHit/';
			url += '?format=json&srl=' + this.srl;

			jQuery.ajax({
				url : url,
				headers : { 'Accept' : 'application=goose;' }
			}).done(function(response) {
				try {
					response = JSON.parse(response);
					if (response.state == 'success')
					{
						self.setCookie('hit-' + self.srl, '1', 1);
						self.setState({
							countHit : response.result.hit
						});
					}
					else
					{
						throw 'error update hit';
					}
				} catch(err) {
					alert(err);
				}
			});
		}
	},

	// up like
	upLike()
	{
		let self = this;

		if (!this.getCookie('like-' + this.srl))
		{
			let url = this.props.userData.url_gooseAPI;
			url += '/article/updateLike/';
			url += '?format=json&srl=' + this.srl;

			jQuery.ajax({
				url : url,
				headers : { 'Accept' : 'application=goose;' }
			}).done(function(response) {
				try {
					response = JSON.parse(response);
					if (response.state == 'success')
					{
						self.setCookie('like-' + self.srl, '1', 1);
						self.setState({
							countLike : response.result.like,
							enableLike : false
						});
					}
					else
					{
						throw 'error update like';
					}
				} catch(err) {
					alert(err);
				}
			});
		}
	},

	// install
	install()
	{
		$('html').addClass('mode-mod-resource-popup');
		this.$popup.addClass('on');
	},

	render() {
		let body = null;
		let loading = <div className="loading-page">
			<span className="inner-circles-loader">loading symbol</span>
			<span className="message">loading..</span>
		</div>;

		// TODO : nest_id가 app이라면 install 버튼이 나오지 않게하기

		if (this.state.loading)
		{
			body = loading;
		}
		else
		{
			if (this.state.item)
			{
				let item = this.state.item;
				let countHit = (this.state.countHit) ? this.state.countHit : item.hit;
				let countLike = (this.state.countLike) ? this.state.countLike : item.json.like;

				body = <section className="view">
					<header>
						<h1>
							<span>{item.title}</span>
							<em className="version">{item.json.version}</em>
						</h1>
						<div className="metas">
							<span>{'Uploader : ' + item.json.user.name}</span>
							<span>{'Regdate : ' + item.regdate}</span>
							<span>{'Updated : ' + item.modate}</span>
							<span>Hit : <em data-target="hit">{countHit}</em></span>
							<span>Like : <em data-target="like">{countLike}</em></span>
						</div>
					</header>

					<div className="con-body" dangerouslySetInnerHTML={{__html: item.content}}></div>

					<nav className="nav-bottom">
						<button type="button" className="ui-button size-large color-install" onClick={this.install}>Install</button>
						<button type="button" className="ui-button size-large color-key" onClick={this.upLike} disabled={(!this.state.enableLike) ? 'disabled' : ''}>
							<span>Like</span>
							<em>{countLike}</em>
						</button>
						<Link to={'/nest/' + this.props.params.nest_id + '/'} className="ui-button size-large color-danger">Close</Link>
					</nav>
				</section>;
			}
			else
			{
				body = <div className="noitem">
					<span className="icon-close blades thick">loading icon</span>
					<span className="message">not found item</span>
				</div>;
			}
		}

		return body;
	}
});