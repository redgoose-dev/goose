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
		let items = this.state.nav.map((o, k) => {
			let active = '';
			o.url = (o.id) ? '/nest/' + o.id + '/' : '/';

			if (this.props.path[0] == 'nest' || (k == 0 && !this.props.path[0]))
			{
				active = (o.id == this.props.path[1]) ? 'active' : '';
				if (o.id == this.props.path[1])
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
					<li key="category-setting" className={(this.props.path[0] == 'setting') ? 'active' : ''}>
						<Link to="/setting/">SETTING</Link>
					</li>
					<li><a href={window.userData.url + '/setting/'}>SETTING ww</a></li>
				</ul>
			</nav>
		);
	}
});