import React from 'react';
import Item from './Item';


class Items extends React.Component {

	constructor(props) {
		super(props);
	}

	render() {
		return (
			<ul>
				{this.props.items.map(function(item){
					let { id, name, url, child } = item;
					return (
						<Item
							key={id}
							id={id}
							name={name}
							url={url}
							child={child}/>
					);
				})}
			</ul>
		);
	}
}

export default Items;