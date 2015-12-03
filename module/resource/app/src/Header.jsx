/**
 * Header component
 */
const Header = React.createClass({

	componentDidMount()
	{
		let aa = this.getNavigations(this.props.apiUrl);
	},

	getNavigations(url)
	{
		jQuery.get(url, function(response){
			if (!response) return;
			try {
				response = JSON.parse(response);
				log(response.result);
			} catch(o) {

			}
		});

	},

	render() {
		return (
			<nav>
				tabs
			</nav>
		);
	}
});