var TopNavigationItems = React.createClass({
	render : function()
	{
		if (!this.props.data) return false;
		var items = this.props.data.map(function(item){
			return <li><a href={item.url}>{item.name}</a></li>;
		});
		return <ul>{items}</ul>;
	}
});

var TopNavigation = React.createClass({
	getInitialState : function()
	{
		return {
			navigationData : {}
		};
	},

	componentDidMount : function()
	{
		$.get(this.props.source, function(result){
			if (this.isMounted())
			{
				this.setState({
					navigationData : result
				});
			}
		}.bind(this));
	},

	render : function(){
		return (
			<nav className="lay-top-navigation">
				<TopNavigationItems data={this.state.navigationData.top}/>
			</nav>
		);
	}
});

// render top navigation
React.render(
	<TopNavigation type="top" source="./data/navigation.json" />,
	document.getElementById('comp-top-navigation')
);
