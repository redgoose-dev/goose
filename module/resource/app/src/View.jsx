const View = React.createClass({

	displayName : 'View',
	srl : null,
	popup_id : 'modResourcePopup',

	getInitialState()
	{
		return {
			title : '',
			loading : true,
			item : null,
			countLike : 0,
			countHit : 0,
			enableLike : (!this.getCookie('like-' + this.props.params.srl)),
			is_install : false,
			overlap_install : false
		};
	},

	componentDidMount()
	{
		this.srl = this.props.params.srl;
		this.getItem(this.srl);
		this.checkInstall();
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

	// get item from api.goose-dev.com/article/item
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
				self.checkInstallLocation(response.result.json.install_loc);
				self.setState({
					loading : false,
					item : response.result,
					title : self.props.parent.refs.header.getTitle()
				});
			} catch(err) {}
		});
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
						self.setCookie('hit-' + self.srl, '1', 30);
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

	checkInstall()
	{
		let self = this;
		jQuery.get(this.props.userData.url + '/checkInstall/', function(response){
			try {
				response = JSON.parse(response);
				if (response.state == 'success')
				{
					self.setState({ is_install : true });
				}
				else
				{
					self.setState({ is_install : false });
				}
			} catch(e) {}
		});

	},

	checkInstallLocation(loc)
	{
		let self = this;
		jQuery.post(this.props.userData.url + '/checkInstallLocation/', { location: loc }, function(response){
			try {
				response = JSON.parse(response);
				self.setState({ overlap_install : response.overlap });
			} catch(e) {}
		});
	},

	// install
	install()
	{
		if ($('#' + this.popup_id).length > 0) return false;

		let $popup = $('<div id="' + this.popup_id + '"></div>');
		$('body').append($popup);

		ReactDOM.render(
			<Install
				parentID={this.popup_id}
				action={this.props.userData.url + '/install/'}
				location={this.state.item.json.install_loc}
				file={this.props.userData.url_gooseAdmin + '/' + this.state.item.json.install_src.location}
				title={this.state.item.title}
				/>,
			document.getElementById(this.popup_id));
	},

	render() {
		let body = null;
		let loading = (
			<div className="loading-page">
				<span className="mod-resource-loader">loading symbol</span>
				<span className="message">loading..</span>
			</div>
		);
		let installButton = <button
			type="button"
			className="ui-button size-large color-install"
			disabled={this.state.overlap_install}
			onClick={this.install}>Install</button>;
		let installInfo = null;

		//log(this.state.is_install);
		if (this.state.item && !this.state.is_install && this.state.item.json.install_loc)
		{
			let file = this.props.userData.url_gooseAdmin + '/' + this.state.item.json.install_src.location;
			installInfo = (
				<div className="install-info">
					<dl>
						<dt>설치경로</dt>
						<dd>{this.state.item.json.install_loc}</dd>
					</dl>
					<dl>
						<dt>설치파일</dt>
						<dd><a href={file}>{file}</a></dd>
					</dl>
				</div>
			);
		}

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

					{installInfo}

					<div className="con-body" dangerouslySetInnerHTML={{__html: item.content}}></div>

					<nav className="nav-bottom">
						{ (this.state.item.json.install_loc && this.state.is_install) ? installButton : null }
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
					<span className="mod-resource-closed blades thick">loading icon</span>
					<span className="message">not found item</span>
				</div>;
			}
		}

		return body;
	}
});