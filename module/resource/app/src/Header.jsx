/**
 * Header component
 */
const Header = React.createClass({

	displayName : 'Header',
	currentTitle : '',

	getInitialState()
	{
		return {
			nav : []
		};
	},

	componentDidMount()
	{
		let url = this.props.apiUrl;
		this.getNavigations(url);
	},

	getNavigations(url)
	{
		let self = this;

		jQuery.get(url, function(response){
			if (!response) return;
			try {
				response = JSON.parse(response);
				if (response.result)
				{
					self.setState({
						nav : response.result
					});
				}
			} catch(error) {}
		});
	},

	getTitle()
	{
		this.currentTitle = $(ReactDOM.findDOMNode(this)).find('li.active > a').text();
		return this.currentTitle;
	},

	render() {
		let path = (this.props.path[0]) ? this.props.path : ['nest', 'new'];

		let items = this.state.nav.map((o, k) => {
			let active = '';
			o.url = (o.id) ? '/nest/' + o.id + '/' : '/';

			if (path[0] == 'nest' || path[0] == 'article' || (k == 0 && !path[0]))
			{
				active = (o.id == path[1]) ? 'active' : '';
				if (o.id == path[1])
				{
					this.currentTitle = o.name;
				}
			}

			return (
				<li key={'category-' + k} className={active}>
					<Link to={o.url}>{o.name}</Link>
				</li>
			);
		});

		return (
			<nav className="category">
				<ul>
					{items}
					<li key="category-setting" className={(path[0] == 'setting') ? 'active' : ''}>
						<Link to="/setting/">SETTING</Link>
					</li>
				</ul>
			</nav>
		);
	}
});