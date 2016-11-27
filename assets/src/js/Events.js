import { objectToArray, getLastItem, setArrayItem } from './util';
import { setSectionTree, gotoScroll } from './Contents';

let current = null;


function onHashChange()
{
	const $content = $('#contents');
	const ID = (location.hash) ? location.hash.replace(/^#/gi, '').split('/') : ['Introduce'];

	let isChangePage = !(window.Goose.top.state.active == ID[0]);

	// update top navigator
	window.Goose.top.setState({
		active: ID[0]
	});

	if (isChangePage)
	{
		$content.load(`./pages/${ID[0]}.html`, function(res, state, req){
			if (state == 'error')
			{
				console.log(`${state} : ${req.statusText}`);
				return false;
			}

			const $contentsGroup = $(this).find('[data-content-group]');
			let children = sectionToArray($contentsGroup, ID[0]);

			// set section tree
			window.Goose.sectionTree = setSectionTree(children);

			// update side nav
			window.Goose.side.update(children, ID[0]);

			imagesLoaded($(this), function() {
				let $target = $(`#${getLastItem(ID)}`);
				if ($target.length)
				{
					$('html, body').scrollTop($target.offset().top);
				}
			});
		});
	}
	else
	{
		let $target = $(`#${getLastItem(ID)}`);
		let top = ($target.length) ? $target.offset().top : 0;
		$('html, body').animate({ scrollTop: top }, 400);
	}
}

function onScroll()
{
	const $side = $(`#${window.Goose.sideElementID}`);
	let st = $(this).scrollTop();
	let ot = $side.offset().top;

	if (ot > st)
	{
		$side.removeClass('fixed');
	}
	else
	{
		$side.addClass('fixed');
	}

	current = $(window.Goose.sectionTree).map(function(o){
		if (($(this).offset().top - $(window).scrollTop()) < 10)
		{
			return this;
		}
	});
	current = $( current ).eq( current.length - 1 );
	if ( current && current.length )
	{
		window.Goose.side.updateSelected(current.get(0).id);
	}
}


function sectionToArray($wrap, ID)
{
	const $groups = $wrap.children('section[id]');
	if (!$groups.length) return null;
	let result = [];

	$groups.each(function(){
		let url = `${ID}/${$(this).attr('id')}`;
		let item = {
			name : $(this).children('h1').children('span').text(),
			id : $(this).attr('id'),
			url : url
		};

		// add hash link
		$(this).children('h1').append(' <a href="#'+url+'">#</a>');

		let child = [];
		$(this).children('section[id]').each(function(){
			let url = `${ID}/${$(this).attr('id')}`;
			child.push({
				name : $(this).children('h1').text(),
				id : $(this).attr('id'),
				url : url
			});
			$(this).children('h1').append(`<a href="#${url}">#</a>`);
		});
		if (child.length)
		{
			item.child = child;
		}

		result.push(item);
	});

	return result;
}


export {
	onHashChange,
	onScroll
};