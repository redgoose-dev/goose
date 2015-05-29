var navigationData = null;

var TopNavigationList = React.createClass({displayName: "TopNavigationList",
	render : function()
	{
		if (this.props.data)
		{
			// 변환된 배열로 출력하기
			return React.createElement("ul", null);
		}
		else
		{
			return React.createElement("ul", null);
		}

		//var items = this.props.data.map(function(data){
		//	return <li key={data.name}><a href={data.url} target={data.target}>{data.name}</a></li>;
		//});
		//return <ul>{items}</ul>;
	}
});

var TopNavigation = React.createClass({displayName: "TopNavigation",
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
			React.createElement("nav", {className: "lay-top-navigation"}, 
				React.createElement(TopNavigationList, {data: this.state.navData})
			)
		);
	}
});


//{this.props.source.map(function(item){
//	return <li key={item.name}><a href={item.url} target={item.target}>{item.name}</a></li>;
//})}

var SideNavigation = React.createClass({displayName: "SideNavigation",
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
			React.createElement("nav", {className: "lay-side-navigation"}, 
				React.createElement("ul", null, this.state.items)
			)
		);
	}
});


/*
 * Render components
 */

// render side navigation
var compNavigationSide = React.render(
	React.createElement(SideNavigation, {key: "side", foo: this.bar}),
	document.getElementById('comp-side-navigation')
);

// render top navigation
var compNavigationTop = React.render(
	React.createElement(TopNavigation, {sourceUrl: "./data/navigation.json"}),
	document.getElementById('comp-top-navigation')
);