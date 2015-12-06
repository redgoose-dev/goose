const Router = window.ReactRouter.Router;
const Link = window.ReactRouter.Link;
const Route = window.ReactRouter.Route;
const IndexRoute = window.ReactRouter.IndexRoute;
const createHashHistory = window.History.createHashHistory;


// render router
ReactDOM.render(
	(
		<Router history={createHashHistory({ queryKey: false })}>
			<Route path="/" component={App}>
				<IndexRoute component={Index}/>
				<Route path="nest/:nest_id/" component={Index} />
				<Route path="article/:srl/" component={View} />
				<Route path="setting/" component={Setting} />
			</Route>
		</Router>
	),
	document.getElementById('resourceApp')
);
