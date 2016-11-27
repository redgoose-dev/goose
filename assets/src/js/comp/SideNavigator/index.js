import React from 'react';
import ReactDOM from 'react-dom';
import Items from './Items';


class SideNavigator extends React.Component {

	constructor(props) {
		super(props);

		this.state = {
			title : '',
			items : null,
		};

		this.$list = null;
		this.$prev = null;
	}

	update(items, title) {
		this.setState({
			items: items,
			title: title,
		});
	}

	updateSelected(id) {
		if (!this.$list) this.$list = $(ReactDOM.findDOMNode(this.refs.list));
		if (this.$prev && this.$prev.length)
		{
			this.$prev.removeClass('active');
			this.$prev.parent().parent().filter('[data-id]').removeClass('active');
		}
		this.$prev = this.$list.find(`li[data-id=${id}]`);
		this.$prev.addClass('active');
		this.$prev.parent().parent().filter('[data-id]').addClass('active');
	}

	render() {
		const { title, items } = this.state;
		let compItems = (items && items.length) ? <Items items={items} ref="list"/> : null;

		return (
			<nav className='lay-side-navigation'>
				<h1>{title}</h1>
				{compItems}
			</nav>
		);
	}
}

export default SideNavigator;