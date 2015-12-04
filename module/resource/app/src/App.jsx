
const App = React.createClass({

	getInitialState()
	{
		return {
			nest: []
		}
	},

	componentDidMount()
	{
		this.getNests(apiUrls.nest);
	},

	getNests(url)
	{
		var self = this;
		// apiUrls
		//jQuery.get(url + '?format=json', function(response){
		//	log(response);
		//	//self.setState()
		//});
	},

	render() {
		return (
			<div>
				<Header apiUrl={apiUrls.nav} />
				<h1>Hello App</h1>
				{this.props.children}
			</div>
		)
	}
});