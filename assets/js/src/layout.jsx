var navigationData = null;

var TopNavigationList = React.createClass({
	render : function()
	{
		if (this.props.data)
		{
			// 변환된 배열로 출력하기
			return <ul></ul>;
		}
		else
		{
			return <ul/>;
		}

		//var items = this.props.data.map(function(data){
		//	return <li key={data.name}><a href={data.url} target={data.target}>{data.name}</a></li>;
		//});
		//return <ul>{items}</ul>;
	}
});

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
			// 1dep메뉴 데이터는 배열로 변환하기
			self.setState({ navData : data });

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


//{this.props.source.map(function(item){
//	return <li key={item.name}><a href={item.url} target={item.target}>{item.name}</a></li>;
//})}

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
		//log(key);
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