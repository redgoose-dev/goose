var TopNavigation = React.createClass({
	getInitialState : function()
	{
		return {
			navigationData : {}
		};
	},
	componentDidMount : function() {},
	render : function(){
		return (
			<nav className="lay-top-navigation">
				<ul>
					{this.props.source.map(function(item){
						return <li key={item.name}><a href={item.url} target={item.target}>{item.name}</a></li>;
					})}
				</ul>
			</nav>
		);
	}
});

var SideNavigation = React.createClass({
	getInitialState : function()
	{
		return {
			navigationData : {}
		};
	},
	componentDidMount : function() {},
	render : function(){
		return (
			<nav className="lay-side-navigation">
				<ul>
					<li>aaaa</li>
				</ul>
			</nav>
		);
	}
});

// load navigation data
$.get('./data/navigation.json', function(data){
	// render top navigation
	React.render(
		<TopNavigation type="top" source={data} />,
		document.getElementById('comp-top-navigation')
	);

	// render side navigation
	React.render(
		<SideNavigation source={data} />,
		document.getElementById('comp-side-navigation')
	);
});
