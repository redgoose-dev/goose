import React from 'react';
import ReactDOM from 'react-dom';

import { onHashChange, onScroll } from './Events'
import { objectToArray, getLastItem, setArrayItem } from './util';

import TopNavigator from './comp/TopNavigator';
import SideNavigator from './comp/SideNavigator';


// set global variables
window.Goose = {
	topElementID: 'comp-top-navigation',
	sideElementID: 'comp-side-navigation',
};


// events
$(window).on('hashchange.goose', onHashChange);
$(window).on('scroll.sideNavigation', onScroll);


// render Components
window.Goose.top = ReactDOM.render(
	<TopNavigator sourceUrl="./assets/model/navigation.json"/>,
	document.getElementById(window.Goose.topElementID)
);

window.Goose.side = ReactDOM.render(
	<SideNavigator/>,
	document.getElementById(window.Goose.sideElementID)
);