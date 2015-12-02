
var App = React.createClass({
	render : function() {
		log('sdgsdgsd')
		return (
			<div>
				sdmgipsdgmpsdgmdsgsdgsdg
			</div>
		);
	}
});

var About = React.createClass({
	render : function()
	{
		return (
			<div>
				ABOUT
			</div>
		);
	}
});

var Inbox = React.createClass({
	render : function()
	{
		return (
			<div>
				INBOX
			</div>
		);
	}
});


var Router = ReactRouter;
var Route = ReactRouter.Route;
var Link = ReactRouter.Link;

//ReactDOM.render((
//	<Router>
//		<Route path="/" component={App} />
//	</Router>
//), document.getElementById('resourceApp'));

//ReactDOM.render(
//	<App
//		urlNests="//api.goose-dev.com/nest/items/"
//		urlCategory="//api.goose-dev.com/category/items/"
//		urlArticle="//api.goose-dev.com/article/items/"
//	/>,
//	document.getElementById('resourceApp')
//);


log(ReactRouter);

