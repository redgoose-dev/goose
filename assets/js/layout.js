/**
 * Object to Array
 * object형식의 데이더를 array형식의 데이터로 변환시킨다. (only 1depth)
 *
 * @param {Object} obj
 * @return {Array}
 */
'use strict';

var objectToArray = function objectToArray(obj) {
	var result = [];
	for (var o in obj) {
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
var getLastItem = function getLastItem(arr) {
	return arr[arr.length - 1];
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
var setArrayItem = function setArrayItem(src, out, key_target, key_child) {
	for (var i = 0; i < src.length; i++) {
		if (src[i][key_target]) {
			out.push(src[i][key_target]);
		}
		if (src[i][key_child]) {
			setArrayItem(src[i][key_child], out, key_target, key_child);
		}
	}
};

/**
 * Navigation
 *
 * @param {String} url navigation map file url
 */
function Navigation(url) {
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
		displayName: 'CompTop',

		getInitialState: function getInitialState() {
			return { navData: null };
		},
		componentDidMount: function componentDidMount() {
			var top = this;

			// load navigation data
			$.get(this.props.sourceUrl, function (data) {
				self.data = data;
				top.setState({ navData: objectToArray(data) });
				$(window).trigger('hashchange');
			});
		},
		render: function render() {
			return React.createElement(
				'nav',
				{ className: 'lay-top-navigation' },
				React.createElement(self.CompTopList, { data: this.state.navData, ref: 'list' })
			);
		}
	});

	// top list
	this.CompTopList = React.createClass({
		displayName: 'CompTopList',

		prev: null,
		updateSelected: function updateSelected(name) {
			if (this.refs[name]) {
				if (this.prev) this.prev.className = '';
				this.refs[name].getDOMNode().className = 'active';
				this.prev = this.refs[name].getDOMNode();
			}
		},
		render: function render() {
			if (!this.props.data) return React.createElement('ul', null);
			var top = this;
			var items = this.props.data.map(function (data) {
				return React.createElement(
					'li',
					{ key: data.name, ref: data.name },
					React.createElement(
						'a',
						{ href: data.url, target: data.target },
						data.name
					)
				);
			});
			return React.createElement(
				'ul',
				null,
				items
			);
		}
	});

	// side
	this.CompSide = React.createClass({
		displayName: 'CompSide',

		getInitialState: function getInitialState() {
			return { items: null };
		},
		update: function update(obj) {
			this.setState({ items: obj });
		},
		componentDidMount: function componentDidMount() {},
		render: function render() {
			return React.createElement(
				'nav',
				{ className: 'lay-side-navigation' },
				React.createElement(
					'h1',
					null,
					self.page
				),
				React.createElement(self.CompSideList, { data: this.state.items, ref: 'list' })
			);
		}
	});

	// side list
	this.CompSideList = React.createClass({
		displayName: 'CompSideList',

		$prev: null,
		updateSelected: function updateSelected(name) {
			if (this.$prev) {
				this.$prev.removeClass('active');
				this.$prev.parent().parent().filter('[data-ref]').removeClass('active');
				this.$prev = null;
			}
			this.$prev = $(this.getDOMNode()).find('[data-ref=' + name + ']');
			this.$prev.parent().parent().filter('[data-ref]').addClass('active');
			if (this.$prev) {
				this.$prev.addClass('active');
			}
		},
		render: function render() {
			if (!this.props.data) return React.createElement('ul', null);
			var items = this.props.data.map(function (data) {
				return React.createElement(
					'li',
					{ 'data-ref': data.id },
					React.createElement(
						'a',
						{ href: '#' + data.url },
						data.name
					),
					data.child ? React.createElement(self.CompSideList, { data: data.child }) : null
				);
			});
			return React.createElement(
				'ul',
				null,
				items
			);
		}
	});

	/**
  * EVENT AREA
  */

	// change hash event
	$(window).on('hashchange.goose', function () {
		var params = location.hash ? location.hash.replace(/^#/gi, '').split('/') : ['Introduce'];
		if (self.data[params[0]] && params[0] !== self.page) {
			var item = self.data[params[0]];
			var children = null;
			self.page = params[0];

			// set children from navigation data
			if (item['child']) {
				children = item['child'];
			}

			// load page
			var url = './pages/' + self.page + '.html';
			$('#contents').load(url, function (res, state, req) {
				if (state == 'error') {
					// not found
					log(state + ': ' + req.statusText);
				} else {
					// get side navigation data
					var $contentsGroup = $(this).find('[data-content-group]');
					if ($contentsGroup.length) {
						children = [];
						$contentsGroup.children('section[id]').each(function () {
							var url = self.page + '/' + $(this).attr('id');
							var item = {
								name: $(this).children('h1').children('span').text(),
								id: $(this).attr('id'),
								url: url
							};

							// add hash link
							$(this).children('h1').append(' <a href="#' + url + '">#</a>');

							var child = [];
							$(this).children('section[id]').each(function () {
								var url = self.page + '/' + $(this).attr('id');
								child.push({
									name: $(this).children('h1').text(),
									id: $(this).attr('id'),
									url: url
								});
								$(this).children('h1').append(' <a href="#' + url + '">#</a>');
							});
							if (child.length) {
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
					if (getLastItem(params)) {
						if (params.length > 1) {
							var n = 0;
							var total = $(this).find('img').length - 1;
							$(this).find('img').one('load', function () {
								n++;
								if (total == n) {
									setTimeout(function () {
										contents.firstTime = true;
										contents.gotoScroll(getLastItem(params));
										contents.firstTime = false;
									}, 300);
								}
							}).each(function () {
								if (this.complete) $(this).load();
							});
						} else {
							contents.gotoScroll(getLastItem(params));
						}
					}
				}
				contents.firstTime = false;
			});
		} else if (getLastItem(params)) {
			contents.gotoScroll(getLastItem(params));
			contents.firstTime = false;
		}
	});

	// scroll event
	var current = null;
	var list = null;
	$(window).on('scroll.sideNavigation', function () {
		var st = $(this).scrollTop();
		var ot = self.$sideNavigation.offset().top;

		if (ot > st) {
			self.$sideNavigation.removeClass('fixed');
		} else {
			self.$sideNavigation.addClass('fixed');
		}

		current = $(contents.sectionTree).map(function (o) {
			if ($(this).offset().top - $(window).scrollTop() < 10) {
				return this;
			}
		});
		current = $(current).eq(current.length - 1);
		if (current && current.length) {
			self.side.refs.list.updateSelected(current.attr('id'));
		}
	});

	/**
  * RENDER AREA
  */
	if (url) {
		// top
		this.top = React.render(React.createElement(this.CompTop, { sourceUrl: url }), this.$topNavigation.get(0));

		// side
		this.side = React.render(React.createElement(this.CompSide, { key: 'side' }), this.$sideNavigation.get(0));
	}
}

/**
 * Contents
 */
function Contents() {
	var self = this;
	this.firstTime = true;
	this.sectionTree = null;

	// get section tree
	this.setSectionTree = function (data) {
		var arr = [];
		setArrayItem(data, arr, 'id', 'child');
		self.sectionTree = arr.map(function (o) {
			if ($('#' + o).length) {
				return $('#' + o)[0];
			}
		});
	};

	this.gotoScroll = function (target) {
		if (target && $('#' + target).length) {
			if (this.firstTime) {
				imagesLoaded('#contents', function () {
					$('html, body').scrollTop($('#' + target).offset().top);
				});
			} else {
				this.firstTime = this.firstTime ? false : this.firstTime;
				$('html, body').animate({ scrollTop: $('#' + target).offset().top }, 400);
			}
		}
	};
}

var navigation = new Navigation('./data/navigation.json');
var contents = new Contents();
//# sourceMappingURL=../maps/layout.js.map