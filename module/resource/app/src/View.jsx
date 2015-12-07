const View = React.createClass({

	displayName : 'View',

	getInitialState()
	{
		return {
			title : '',
			loading : true,
			item : null
		};
	},

	componentDidMount()
	{
		this.getItem(this.props.params.srl);
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
				self.setState({
					loading : false,
					item : response.result,
					title : self.props.parent.refs.header.getTitle()
				});
			} catch(err) {}
		});
	},

	// up like
	upLike()
	{
		log('update like')
	},

	render() {
		let body = null;
		let loading = <div className="loading-page">
			<span className="inner-circles-loader">loading symbol</span>
			<span className="message">loading..</span>
		</div>;

		if (this.state.loading)
		{
			body = loading;
		}
		else
		{
			if (this.state.item)
			{
				let item = this.state.item;
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
							<span>Hit : <em data-target="hit">{item.hit}</em></span>
							<span>Like : <em data-target="like">{item.json.like}</em></span>
						</div>
					</header>

					<div className="con-body" dangerouslySetInnerHTML={{__html: item.content}}></div>

					<nav className="nav-bottom">
						<button type="button" className="col-blue disabled" onClick={this.upLike}>
							<span>Like:</span>
							<em>{item.json.like}</em>
						</button>
						<Link to={'/nest/' + this.props.params.nest_id + '/'} className="col-red">
							<span>Close</span>
						</Link>
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