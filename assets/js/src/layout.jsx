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
 * Get Last Item
 * 배열에서 마지막 요소를 가져옵니다.
 *
 * @param {Array} arr
 * @return {*}
 */
var getLastItem = function(arr)
{
	return arr[arr.length-1];
};

/**
 * Get Last Item
 * 중첩되어있는 배열의 특정값을 다른 하나의 배열에 나란히 담는다.
 *
 * @param {Array} src 소스배열
 * @param {Array} out 출력되는 배열
 * @param {String} key_target 가져오려는 key 이름
 * @param {String} key_child 자식 key 이름
 * @return {*}
 */
var setArrayItem = function(src, out, key_target, key_child)
{
	for (var i=0; i<src.length; i++)
	{
		if (src[i][key_target])
		{
			out.push(src[i][key_target]);
		}
		if (src[i][key_child])
		{
			setArrayItem(src[i][key_child], out, key_target, key_child);
		}
	}
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
	this.CompTop = React.createClass({
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
				<nav className='lay-top-navigation'>
					<self.CompTopList data={this.state.navData} ref='list' />
				</nav>
			);
		}
	});

	// top list
	this.CompTopList = React.createClass({
		prev : null,
		updateSelected : function(name)
		{
			if (this.refs[name])
			{
				if (this.prev) this.prev.className = '';
				this.refs[name].getDOMNode().className = 'active';
				this.prev = this.refs[name].getDOMNode();
			}
		},
		render : function()
		{
			if (!this.props.data) return <ul/>;
			var top = this;
			var items = this.props.data.map(function(data){
				return <li key={data.name} ref={data.name}><a href={data.url} target={data.target}>{data.name}</a></li>;
			});
			return <ul>{items}</ul>;
		}
	});

	// side
	this.CompSide = React.createClass({
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
				<nav className='lay-side-navigation'>
					<h1>{self.page}</h1>
					<self.CompSideList data={this.state.items} ref='list' />
				</nav>
			);
		}
	});

	// side list
	this.CompSideList = React.createClass({
		$prev : null,
		updateSelected : function(name)
		{
			if (this.$prev)
			{
				this.$prev.removeClass('active');
				this.$prev.parent().parent().filter('[data-ref]').removeClass('active');
				this.$prev = null;
			}
			this.$prev = $(this.getDOMNode()).find('[data-ref=' + name + ']');
			this.$prev.parent().parent().filter('[data-ref]').addClass('active');
			if (this.$prev)
			{
				this.$prev.addClass('active');
			}
		},
		render : function(){
			if (!this.props.data) return <ul/>;
			var items = this.props.data.map(function(data){
				return (
					<li data-ref={data.id}>
						<a href={'#' + data.url}>{data.name}</a>
						{(data.child) ? <self.CompSideList data={data.child} /> : null}
					</li>
				);
			});
			return <ul>{items}</ul>;
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
						$contentsGroup.children('section[id]').each(function(){
							var url = self.page + '/' + $(this).attr('id');
							var item = {
								name : $(this).children('h1').text(),
								id : $(this).attr('id'),
								url : url
							};

							var child = [];
							$(this).children('section[id]').each(function(){
								child.push({
									name : $(this).children('h1').text(),
									id : $(this).attr('id'),
									url : self.page + '/' + $(this).attr('id')
								});
							});
							if (child.length)
							{
								item.child = child;
							}

							children.push(item);
						});

						// set section tree
						contents.setSectionTree(children);
					}

					// update top navigation list
					self.top.refs.list.updateSelected(self.page);

					// update side navigation
					self.side.update(children);

					// go to scroll
					if (getLastItem(params))
					{
						contents.gotoScroll(getLastItem(params));
					}
				}
				contents.firstTime = false;
			});
		}
		else if (getLastItem(params))
		{
			contents.gotoScroll(getLastItem(params));
			contents.firstTime = false;
		}
	});

	// scroll event
	var current = null;
	var list = null;
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

		current = $(contents.sectionTree).map(function(o){
			if (($(this).offset().top - $(window).scrollTop()) < 10)
			{
				return this;
			}
		});
		current = $( current ).eq( current.length - 1 );
		if ( current && current.length )
		{
			self.side.refs.list.updateSelected(current.attr('id'));
		}
	});

	/**
	 * RENDER AREA
	 */
	if (url)
	{
		// top
		this.top = React.render(
			<this.CompTop sourceUrl={url} />,
			this.$topNavigation.get(0)
		);

		// side
		this.side = React.render(
			<this.CompSide key="side" />,
			this.$sideNavigation.get(0)
		);
	}
}


/**
 * Contents
 */
function Contents()
{
	var self = this;
	this.firstTime = true;
	this.sectionTree = null;

	// get section tree
	this.setSectionTree = function(data)
	{
		var arr = [];
		setArrayItem(data, arr, 'id', 'child');
		self.sectionTree = arr.map(function(o){
			if ($('#'+o).length)
			{
				return $('#'+o)[0];
			}
		});
	};

	this.gotoScroll = function(target)
	{
		if (target && $('#' + target).length)
		{
			if (this.firstTime)
			{
				imagesLoaded( '#contents', function() {
					$('html, body').scrollTop($('#' + target).offset().top);
				});
			}
			else
			{
				this.firstTime = (this.firstTime) ? false : this.firstTime;
				$('html, body').animate({ scrollTop: $('#' + target).offset().top }, 400);
			}
		}
	}
}

var navigation = new Navigation('./data/navigation.json');
var contents = new Contents();