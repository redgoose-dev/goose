import React from 'react';


class TopNavigatorItem extends React.Component {

	constructor(props) {
		super(props);
	}

	render() {

		const { name, url, target, active } = this.props;

		return (
			<li className={active && 'active'}>
				<a href={url} target={target}>{name}</a>
			</li>
		);

	}
}

export default TopNavigatorItem;