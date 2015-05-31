var navigationData = null;

/**
 * Object to Array
 * object형식의 데이더를 array형식의 데이터로 변환시킨다. (only 1depth)
 *
 * @param {Object} obj
 * @return {Array}
 */
var objectToArray = function(obj)
{
	var result = [];
	for (var o in obj)
	{
		result.push(obj[o]);
	}
	return result;
};

// top navigation items
var TopNavigationList = React.createClass({
	render : function()
	{
		if (!this.props.data) return <ul/>;
		var items = this.props.data.map(function(data){
			return <li key={data.name}><a href={data.url} target={data.target}>{data.name}</a></li>;
		});
		return <ul>{items}</ul>;
	}
});

// top navigation wrap
var TopNavigation = React.createClass({
	getInitialState : function()
	{
		return { navData : null };
	},
	componentDidMount : function()
	{
		var self = this;

		// change hash event
		$(window).on('hashchange', function() {
			//log(location.hash);
			compNavigationSide.update('Documentation');
		});

		// load navigation data
		$.get(this.props.sourceUrl, function(data){
			navigationData = data;
			self.setState({ navData : objectToArray(data) });
			$(window).trigger('hashchange');
		});
	},

	render : function()
	{
		return (
			<nav className="lay-top-navigation">
				<TopNavigationList data={this.state.navData} />
			</nav>
		);
	}
});

// side navigation
var SideNavigation = React.createClass({
	getInitialState : function()
	{
		return {
			items : null
		};
	},
	update : function(key)
	{
		//log(navigationData);
		log(navigationData[key]);

		// 페이지 호출하기
	},
	componentDidMount : function() {},
	render : function(){
		return (
			<nav className="lay-side-navigation">
				<ul>{this.state.items}</ul>
			</nav>
		);
	}
});


/*
 * Render components
 */

// render side navigation
var compNavigationSide = React.render(
	<SideNavigation key="side" foo={this.bar} />,
	document.getElementById('comp-side-navigation')
);

// render top navigation
var compNavigationTop = React.render(
	<TopNavigation sourceUrl="./data/navigation.json" />,
	document.getElementById('comp-top-navigation')
);