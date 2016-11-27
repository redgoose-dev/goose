import React from 'react';
import Items from './Items';

class Item extends React.Component {

	constructor(props) {
		super(props);
	}

	render() {

		const { id, name, url, child } = this.props;

		return (
			<li data-id={id}>
				<a href={`#${url}`}>{name}</a>
				{(child) && <Items items={child} />}
			</li>
		);
	}
}

export default Item;