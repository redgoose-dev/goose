const Router = window.ReactRouter.Router;
const Link = window.ReactRouter.Link;
const Route = window.ReactRouter.Route;
const IndexRoute = window.ReactRouter.IndexRoute;
const createHashHistory = window.History.createHashHistory;


// Set API urls
const apiUrls = {
	nest : '//api.goose-dev.com/nest/items/',
	category : '//api.goose-dev.com/category/items/',
	article : '//api.goose-dev.com/article/items/',
	nav : '//api.goose-dev.com/json/item/?srl=2&format=json'
};

// render router
ReactDOM.render(
	(
		<Router history={createHashHistory({ queryKey: false })}>
			<Route path="/" component={App}>
				<IndexRoute component={Index}/>
				<Route path="read/:srl" component={View}/>
				<Route path="nest/:nest_srl" component={Index}/>
			</Route>
		</Router>
	),
	document.getElementById('resourceApp')
);
