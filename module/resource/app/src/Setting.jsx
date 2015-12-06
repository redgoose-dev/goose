var Setting = React.createClass({

	displayName : 'Setting',

	getInitialState()
	{
		return {
			title : ''
		};
	},

	componentDidMount()
	{
		this.setState({
			title : this.props.parent.refs.header.getTitle()
		});
	},

	render()
	{
		return (
			<section>
				<h1>{this.state.title}</h1>
				<p>setting form</p>
			</section>
		);
	}
});