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


/**
 * Navigation
 *
 * @param {String} url navigation map file url
 */
function Navigation(url)
{
	var self = this;
	this.data = null;
	this.page = null;
	this.$sideNavigation = $('#comp-side-navigation');
	this.$topNavigation = $('#comp-top-navigation');


	/**
	 * COMPONENT AREA
	 */

	// top
	this.CompTop = React.createClass({displayName: "CompTop",
		getInitialState : function()
		{
			return { navData : null };
		},
		componentDidMount : function()
		{
			var top = this;

			// load navigation data
			$.get(this.props.sourceUrl, function(data){
				self.data = data;
				top.setState({ navData : objectToArray(data) });
				$(window).trigger('hashchange');
			});
		},

		render : function()
		{
			return (
				React.createElement("nav", {className: "lay-top-navigation"}, 
					React.createElement(self.CompTopList, {data: this.state.navData})
				)
			);
		}
	});

	// top list
	this.CompTopList = React.createClass({displayName: "CompTopList",
		render : function()
		{
			if (!this.props.data) return React.createElement("ul", null);
			var items = this.props.data.map(function(data){
				return React.createElement("li", {key: data.name}, React.createElement("a", {href: data.url, target: data.target}, data.name));
			});
			return React.createElement("ul", null, items);
		}
	});

	// side
	this.CompSide = React.createClass({displayName: "CompSide",
		getInitialState : function()
		{
			return { items : null };
		},
		update : function(obj)
		{
			this.setState({ items : obj });
		},
		componentDidMount : function() {},
		render : function(){
			return (
				React.createElement("nav", {className: "lay-side-navigation"}, 
					React.createElement("h1", null, self.page), 
					React.createElement(self.CompSideList, {data: this.state.items})
				)
			);
		}
	});

	// side list
	this.CompSideList = React.createClass({displayName: "CompSideList",
		render : function(){
			if (!this.props.data) return React.createElement("ul", null);
			var items = this.props.data.map(function(data){
				return React.createElement("li", {key: data.name}, React.createElement("a", {href: '#' + data.url}, data.name));
			});
			return React.createElement("ul", null, items);
		}
	});


	/**
	 * EVENT AREA
	 */

	// change hash event
	$(window).on('hashchange.goose', function() {
		var params = (location.hash) ? location.hash.replace(/^#/gi, '').split('/') : ['Introduce'];
		if (self.data[params[0]] && (params[0] !== self.page))
		{
			var item = self.data[params[0]];
			var children = null;
			self.page = params[0];

			// set children from navigation data
			if (item['child'])
			{
				children = item['child'];
			}

			// load page
			var url = './pages/' + self.page + '.html';
			$('#contents').load(url, function(res, state, req){
				if (state == 'error')
				{
					// not found
					log(state + ': ' + req.statusText);
				}
				else
				{
					// get side navigation data
					var $contentsGroup = $(this).find('[data-content-group]');
					if ($contentsGroup.length)
					{
						children = [];
						$contentsGroup.find('[data-content]').each(function(){
							var id = $(this).attr('id');
							var name = $(this).children('h1').text();
							children.push({
								name : name,
								url : self.page + '/' + id
							});
						});
					}

					// update side navigation
					self.side.update(children);

					// go to scroll
					if (params[1])
					{
						contents.gotoScroll(params[1]);
					}
				}
			});
		}
		else if (params[1])
		{
			contents.gotoScroll(params[1]);
		}
	});

	// scroll event
	$(window).on('scroll.sideNavigation', function(){
		var st = $(this).scrollTop();
		var ot = self.$sideNavigation.offset().top;

		if (ot > st)
		{
			self.$sideNavigation.removeClass('fixed');
		}
		else
		{
			self.$sideNavigation.addClass('fixed');
		}
	});

	/**
	 * RENDER AREA
	 */
	if (url)
	{
		// top
		this.top = React.render(
			React.createElement(this.CompTop, {sourceUrl: url}),
			this.$topNavigation.get(0)
		);

		// side
		this.side = React.render(
			React.createElement(this.CompSide, {key: "side"}),
			this.$sideNavigation.get(0)
		);
	}
}


/**
 * Contents
 */
function Contents()
{
	this.firstTime = true;

	this.gotoScroll = function(target)
	{
		var speed = 0;
		if (target)
		{
			speed = (!this.firstTime) ? 400 : 0;
			this.firstTime = (this.firstTime) ? false : this.firstTime;
			$('html, body').animate({ scrollTop: $('#' + target).offset().top }, speed);
		}
	}
}

var navigation = new Navigation('./data/navigation.json');
var contents = new Contents();