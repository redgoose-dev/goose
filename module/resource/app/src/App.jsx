const App = React.createClass({

	displayName : 'App',

	getInitialState()
	{
		return {

		}
	},

	getPath(path)
	{
		path = path.replace(/^\/|\/$/g, '');
		return path.split('/');
	},

	render()
	{
		return (
			<div className="mod-resource">
				<Header
					apiUrl={userData.apiUrls.nav}
					path={this.getPath(this.props.location.pathname)}
					ref="header"
					/>
				{React.cloneElement(
					this.props.children, {
						ref : 'child',
						parent : this
					}
				)}
			</div>
		)
	}
});