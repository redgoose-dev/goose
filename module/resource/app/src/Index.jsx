var Index = React.createClass({

	displayName : 'Index',
	currentNest : '',
	currentPage : 1,
	count : 20,

	getInitialState()
	{
		return {
			loading: false,
			items: [],
			navigation: null,
			title : ''
		};
	},

	getItems(nest_id, page)
	{
		let self = this;
		let url = userData.apiUrls.article;

		url += '?format=json';
		url += '&field=srl,category_srl,nest_srl,title,hit,json,regdate,modate';
		url += '&count=' + this.count;
		url += '&page=' + page;
		url += (nest_id) ? '&nest_id=' + nest_id : '';

		jQuery.get(url, function(response){
			try {
				response = JSON.parse(response);
				self.setState({
					loading : false,
					items : response.result,
					navigation : response.navigation,
					title : self.props.parent.refs.header.getTitle()
				});
			} catch(err) {}
		});
	},

	componentDidMount()
	{
		this.currentNest = this.props.params.nest_id;
		this.currentPage = (this.props.location.query.page) ? this.props.location.query.page : 1;
		this.setState({ loading : true });
		this.getItems(this.currentNest, this.currentPage);
	},

	componentWillReceiveProps(nextProps)
	{
		let nextPage = (nextProps.location.query.page) ? nextProps.location.query.page : 1;
		let sameNest = this.currentNest == nextProps.params.nest_id;

		if (!sameNest || (sameNest && (this.currentPage != nextPage)))
		{
			this.currentNest = nextProps.params.nest_id;
			this.currentPage = nextPage;
			this.setState({ loading : true });
			this.getItems(this.currentNest, this.currentPage);
		}
	},

	render()
	{
		let index, pageNavigation;

		if (this.state.loading)
		{
			index = <li className="loading-page">
				<span className="inner-circles-loader">loading symbol</span>
				<span className="message">loading..</span>
			</li>;
		}
		else
		{
			if (this.state.items.length)
			{
				// make index
				index = this.state.items.map((o, k) => {
					let css_figure = {
						backgroundImage : 'url(\'' + userData.url_gooseAdmin + '/' + o.json.thumnail.url + '\')'
					};

					return (
						<li key={k}>
							<Link to={'/article/' + o.srl + '/'}>
								<figure style={css_figure}>image</figure>
								<div className="bd">
									<span className="category-name">{o.category_name}</span>
									<strong className="title">{o.title}</strong>
									<div className="inf">
										<span><b>{'Ver : ' + o.json.version}</b></span>
										<span>{'Update : ' + o.modate}</span>
										<span>{'Hit : ' + o.hit}</span>
										<span>{'Like : ' + o.json.like}</span>
									</div>
								</div>
							</Link>
						</li>
					);
				});

				// make page navigation
				if (this.state.navigation)
				{
					let nav = this.state.navigation;
					let url = ((this.currentNest) ? '/nest/' + this.currentNest + '/' : '/');
					let params = (this.props.location.query.keyword) ? 'keyword=' + this.props.location.query.keyword + '&' : '';

					pageNavigation = (<nav className="paginate">
						{(nav.prev) ? <Link to={url + '?' + params + 'page=' + nav.prev.id} title={nav.prev.name} className="dir">{nav.prev.name}</Link> : null}
						{nav.body.map((o, k) => {
							let link = url + '?' + params + 'page=' + o.id;
							return (o.active) ? <strong key={'pageNav-'+k}>{o.name}</strong> : <Link to={link} key={'pageNav-'+k}>{o.name}</Link>;
						})}
						{(nav.next) ? <Link to={url + '?' + params + 'page=' + nav.next.id} title={nav.next.name} className="dir">{nav.next.name}</Link> : null}
					</nav>);
				}
			}
			else
			{
				index = <li className="noitem">
					<span className="icon-close blades thick">loading icon</span>
					<span className="message">not found item</span>
				</li>;
			}
		}

		return (
			<section className="index">
				<h1>{this.state.title}</h1>
				{ (this.state.loading) ? <div className="loading">loading...</div> : <ul>{index}</ul> }
				{pageNavigation}
			</section>
		);
	}
});