import { objectToArray, getLastItem, setArrayItem } from './util';


const setSectionTree = function(data)
{
	let arr = [];

	setArrayItem(data, arr, 'id', 'child');

	return arr.map(function(o){
		if ($('#'+o).length)
		{
			return $('#'+o)[0];
		}
	});
};

const gotoScroll = function(target)
{
	if (target && $('#' + target).length)
	{
		if (window.Goose.firstTime)
		{
			imagesLoaded( '#contents', function() {
				$('html, body').scrollTop($('#' + target).offset().top);
			});
		}
		else
		{
			window.Goose.firstTime = (window.Goose.firstTime) ? false : window.Goose.firstTime;
			$('html, body').animate({ scrollTop: $('#' + target).offset().top }, 400);
		}
	}
};


export {
	setSectionTree,
	gotoScroll
};