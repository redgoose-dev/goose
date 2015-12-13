var Index = React.createClass({

	displayName : 'Index',
	currentNest : '',
	currentPage : 1,
	currentCategory : 0,
	count : 10,

	getInitialState()
	{
		return {
			loading: false,
			items: [],
			navigation: null,
			categories: [],
			title : ''
		};
	},

	componentDidMount()
	{
		this.currentNest = this.props.params.nest_id;
		this.currentPage = (this.props.location.query.page) ? this.props.location.query.page : 1;
		this.currentCategory = (this.props.params.category_srl) ? parseInt(this.props.params.category_srl) : 0;
		this.setState({ loading : true });
		this.getItems(this.currentNest, this.currentCategory, this.currentPage);
	},

	componentWillReceiveProps(nextProps)
	{
		let nextPage = (nextProps.location.query.page) ? nextProps.location.query.page : 1;
		let sameNest = this.currentNest == nextProps.params.nest_id;
		let samePage = this.currentPage == nextPage;
		let sameCategory = this.currentCategory == nextProps.params.category_srl;

		if (!sameNest || (sameNest && !sameCategory) || (sameNest && sameCategory && !samePage))
		{
			this.currentNest = nextProps.params.nest_id;
			this.currentCategory = (nextProps.params.category_srl) ? nextProps.params.category_srl : 0;
			this.currentPage = nextPage;
			this.setState({ loading : true });
			this.getItems(this.currentNest, this.currentCategory, this.currentPage);
		}
	},

	getItems(nest_id, category_srl, page)
	{
		let self = this;
		let url = this.props.userData.apiUrls.articles;

		url += '?format=json';
		url += '&field=srl,category_srl,nest_srl,title,hit,json,regdate,modate';
		url += '&count=' + this.count;
		url += '&page=' + page;
		url += (nest_id && (nest_id != 'new')) ? '&nest_id=' + nest_id : '';
		url += (category_srl) ? '&category_srl=' + category_srl : '';

		jQuery.getJSON(url, function(response){
			if (response.result)
			{
				self.setState({
					loading : false,
					items : response.result,
					navigation : response.navigation,
					categories : response.categories,
					title : self.props.parent.refs.header.getTitle()
				});
			}
		});
	},

	render()
	{
		let index, pageNavigation, categories;
		let loading = <li className="loading-page">
			<span className="mod-resource-loader">loading symbol</span>
			<span className="message">loading..</span>
		</li>;

		if (this.state.loading)
		{
			index = loading;
		}
		else
		{
			if (this.state.items.length)
			{
				// make index
				index = this.state.items.map((o, k) => {
					let css_figure = {
						backgroundImage : 'url(\'' + this.props.userData.url_gooseAdmin + '/' + o.json.thumnail.url + '\')'
					};

					return (
						<li key={k}>
							<Link to={'/article/' + ((this.currentNest) ? this.currentNest : 'new') + '/' + o.srl + '/'}>
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
				index = (
					<li className="noitem">
						<span className="mod-resource-closed blades thick">loading icon</span>
						<span className="message">not found item</span>
					</li>
				);
			}
		}

		if (this.state.categories.length)
		{
			let categoryItem = this.state.categories.map((o, k) =>{
				let url = '/nest/' + this.props.params.nest_id + '/' + ((o.srl) ? o.srl + '/' : '');
				let active = ( this.currentCategory == o.srl || ((this.currentCategory != o.srl) && o.srl < 0) ) ? 'active' : '';
				return (
					<li key={'categoryItem-' + k} className={active}>
						<Link to={url}>
							<span>{o.name}</span>
							<em>{o.count}</em>
						</Link>
					</li>
				);
			});
			categories = (
				<nav className="article-categories">
					<ul>{categoryItem}</ul>
				</nav>
			);
		}

		return (
			<section>
				<h1>{this.state.title}</h1>
				{categories}
				<ul className="index">{index}</ul>
				{pageNavigation}
			</section>
		);
	}
});