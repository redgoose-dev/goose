var TopNavigation = React.createClass({
	index : [1, 2, 3],
	render : function(){
		return (
			<nav className="lay-top-navigation">
				<ul>
					<li><a href="#">aaaa</a></li>
					<li><a href="#">bbb</a></li>
					<li><a href="#">cccc</a></li>
				</ul>
			</nav>
		);
	}
});

React.render(
	<TopNavigation />,
	document.getElementById('comp-top-navigation')
);
