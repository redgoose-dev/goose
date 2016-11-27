import React from 'react';
import { objectToArray } from '../../util';
import Item from './Item';


class TopNavigator extends React.Component {

	constructor(props) {
		super(props);

		this.state = {
			items : null,
			active: null,
		};
	}

	componentDidMount() {

		const { sourceUrl } = this.props;

		$.get(sourceUrl, (data) => {
			this.setState({ items : objectToArray(data) });

			$(window).trigger('hashchange');
		});
	}

	render() {

		const { items, active } = this.state;

		let compItems = items && items.map((item) => {
			let { name, url, target } = item;
			let itemActive = (active == name);

			return (
				<Item
					key={name}
					name={name}
					url={url}
					target={target}
					active={itemActive} />
			);
		});

		return (
			<nav className='lay-top-navigation'>
				<ul>
					{compItems}
				</ul>
			</nav>
		);
	}
}

export default TopNavigator;