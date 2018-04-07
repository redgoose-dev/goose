jQuery(function($){

	/**
	 * @var {Object} userData
	 */

	let userData = window.userData;

	// assign json
	if (userData.articleData)
	{
		if (userData.articleData.thumbnail)
		{
			userData.thumbnail = userData.articleData.thumbnail || {};
		}
	}


	// insert source to content form
	function insertSourceToContentForm(items)
	{
		// get cursor position
		function getCursorPosition($el)
		{
			const el = $el.get(0);
			let pos = 0;
			if ('selectionStart' in el)
			{
				pos = el.selectionStart;
			}
			else if ('selection' in document)
			{
				el.focus();
				const Sel = document.selection.createRange();
				const SelLength = document.selection.createRange().text.length;
				Sel.moveStart('character', -el.value.length);
				pos = Sel.text.length - SelLength;
			}
			return pos;
		}

		let str = '';
		items.forEach((item) => {
			if (/^image/.test(item.type))
			{
				str += '![](' + item.src + ')\n';
			}
			else
			{
				str += '[' + item.name + '](' + item.src + ')\n';
			}
		});

		const pos = getCursorPosition($(userData.form.content));
		const val = userData.form.content.value;
		userData.form.content.value = val.substr(0, pos) + str + val.substr(pos);
	}

	// get preview markdown data
	function getPreviewMarkdownData()
	{
		const defer = $.Deferred();
		const req = $.ajax({
			url : userData.previewScriptPath,
			type : 'post',
			data : {
				content : userData.form.content.value
			}
		});
		req.done(function(str){
			defer.resolve(str);
		});

		return defer.promise();
	}

	// check thumbnail image
	function checkThumbnailImage()
	{
		const files = uploader.queue.items.files;
		let inImage = false;

		// check thumbnail queue
		if (userData.form.article_srl.value && uploader.queue.$queue.children('.is-thumbnail').length)
		{
			return false;
		}

		// check image type in queues
		for (let i=0; i<files.length; i++)
		{
			if (/^image/i.test(files[i].type))
			{
				inImage = true;
				break;
			}
		}

		// check thumbnail_image value
		if (inImage && !userData.thumbnail_image)
		{
			alert('not make thumbnail image');
			uploader.queue.$queue.find('.btn-make-thumbnail').eq(0).focus();
			return true;
		}
		else
		{
			return false;
		}
	}

	// get thumbnail size
	function getThumbnailSize(setting, file)
	{
		const defer = $.Deferred();
		const image = new Image();

		/**
		 * get size
		 *
		 * @param {String} type
		 * @param {Number} set_w
		 * @param {Number} set_h
		 * @param {Number} img_w
		 * @param {Number} img_h
		 * @return {Number}
		 */
		function getSize(type, set_w, set_h, img_w, img_h)
		{
			let size = {};
			switch(type)
			{
				case 'resize':
					if (img_w < img_h)
					{
						size.width = (img_w / img_h) * set_h;
						size.height = set_h;
					}
					else
					{
						size.width = set_w;
						size.height = (img_h / img_w) * set_w;
					}
					break;
				case 'resizeWidth':
					size.width = set_w;
					size.height = (img_h / img_w) * set_w;
					break;
				case 'resizeHeight':
					size.width = (img_w / img_h) * set_h;
					size.height = set_h;
					break;
				default:
					size.width = set_w;
					size.height = set_h;
					break;
			}
			return size;
		}

		image.onload = function(e)
		{
			const size = getSize(
				setting.type,
				Number(setting.size.width),
				Number(setting.size.height),
				Number(image.width),
				Number(image.height)
			);
			defer.resolve(size);
		};
		image.src = userData.root + '/' + file.src;

		return defer.promise();
	}


	// init uploader
	window.uploader = new RG_Uploader(document.getElementById('queuesManager'), {
		autoUpload: true,
		allowFileTypes: ['jpeg', 'png', 'gif', 'zip', 'pdf'],
		limitSize: userData.uploader.limitSize,
		limitSizeTotal: userData.uploader.limitSizeTotal,
		uploadScript: userData.root + '/File/upload/',
		removeScript: userData.root + '/File/remove/',
		srcPrefixName: userData.url,
		queue: {
			style: 'list',
			height: 150,
			limit: userData.uploader.queueLimitCount,
			datas: userData.pushDatas,
			buttons: [
				{
					name: 'open file',
					iconName: 'open_in_new',
					action: (app, file) => window.open(file.fullSrc)
				},
				{
					name: 'make thumbnail image',
					iconName: 'apps',
					className: 'btn-make-thumbnail',
					show: (file) => (file.type.split('/')[0] === 'image'),
					action: function(app, file) {
						if (!app.plugin.child.thumbnail) return false;

						const plugin = app.plugin.child.thumbnail;
						let option = {};

						// set option
						if (file.srl === userData.thumbnail.srl)
						{
							option.points = userData.thumbnail.points;
							option.zoom = userData.thumbnail.zoom;
						}

						if ((userData.thumbnailSet.type && userData.thumbnailSet.type === 'crop') || !userData.thumbnailSet.type)
						{
							plugin.open(file, option);
						}
						else
						{
							const ready = getThumbnailSize(userData.thumbnailSet, file);
							ready.done(function(size){
								plugin.assignOption({
									output: {
										size: { width: size.width, height: size.height }
									},
									croppie: {
										viewport: { width: size.width, height: size.height }
									}
								});
								plugin.open(file, option);
							});
						}
					}
				},
				{
					name: 'insert editor',
					iconName: 'center_focus_strong',
					action: (app, file) => insertSourceToContentForm([{
						src : file.fullSrc,
						name : file.name,
						type : file.type
					}])
				},
				{
					name: 'remove queue',
					iconName: 'close',
					action: (app, file) => app.queue.removeQueue(file.id, false, true)
				}
			]
		},
		plugin: [
			{ name : 'preview', obj : new RG_Preview() },
			{ name : 'sizeinfo', obj : new RG_SizeInfo() },
			{ name : 'dnd', obj : new RG_DragAndDrop() },
			{
				name : 'thumbnail',
				obj : new RG_Thumbnail({
					width : 680,
					height : 540,
					mobileSize : 640,
					output : {
						type : 'canvas',
						quality : .85,
						format : 'jpeg',
						size : {
							width : userData.thumbnailSet.size.width,
							height : userData.thumbnailSet.size.height
						}
					},
					croppie : {
						viewport : {
							width: userData.thumbnailSet.size.width,
							height: userData.thumbnailSet.size.height,
							type: 'square'
						}
					},
					doneCallback: (res, app, file) => {
						const classBtnMakeThumbnail = '.btn-make-thumbnail';
						// input thumbnail image source
						userData.thumbnail_image = res.src;
						// update on class name
						app.queue.$queue.children().removeClass('is-thumbnail');
						app.queue.$queue.find(classBtnMakeThumbnail).removeClass('on');
						app.queue.selectQueueElement(file.id).find(classBtnMakeThumbnail).addClass('on');
						// set thumbnail data
						const croppie = app.plugin.child.thumbnail.croppie.get();
						const options = app.plugin.child.thumbnail.options;
						userData.thumbnail.srl = file.srl;
						userData.thumbnail.points = croppie.points;
						userData.thumbnail.zoom = croppie.zoom;
						userData.thumbnail.size = options.output.size;
					}
				})
			}
		],
		uploadParamsFilter: () => ({ ready : 1 }),
		uploadDataFilter: (res) => {
			return {
				state: res[0].state,
				response: {
					src: res[0].loc,
					srl: res[0].srl,
					name: res[0].name,
					ready: res[0].ready
				}
			}
		},
		removeParamsFilter: (res) => ({ data: JSON.stringify([{ srl: res.srl }]) }),
		removeDataFilter: (res) => {},
		uploadComplete: (file) => userData.addQueue.push(file.srl),
		uploadFail: (o) => console.error(o),
		init: (app) => {
			// push ready queue to addQueue
			userData.addQueue = $.map(app.queue.items.files, function(file){
				return (file.ready === 1) ? file.srl : null;
			});
			// set thumbnail queue
			if (userData.articleData.thumbnail)
			{
				const $queue = app.queue.selectQueueElement(userData.articleData.thumbnail.srl);
				$queue
					.addClass('is-thumbnail')
					.find('.btn-make-thumbnail').addClass('on');
			}
			// toggle select queues
			app.$container.find('[data-element=toggle-queues]').on('click', function() {
				app.queue.selectQueue();
			});
			// attach files to editor
			app.$container.find('[data-element=attach-files]').on('click', function() {
				let $selectItems = app.queue.$queue.children('.selected');
				if (!$selectItems.length)
				{
					alert('선택된 파일이 없습니다.');
					return;
				}
				$selectItems = $selectItems.toArray();
				$selectItems.forEach((item) => {
					const index = app.queue.findItem(parseInt(item.dataset.id));
					const file = app.queue.items.files[index];
					if (file)
					{
						insertSourceToContentForm([{
							src : file.fullSrc,
							name : file.name,
							type : file.type
						}]);
					}
				});
			});
		}
	});

	// toggle edit/preview
	const $mkEditor = $('div.mk-editor');
	const $mkEditorButtons = $mkEditor.find('a[data-control]');
	$mkEditorButtons.on('click', function(){
		const mode = $(this).attr('data-control');
		const $target = $mkEditor.find('[data-target=' + mode + ']');

		if (!$(this).hasClass('active'))
		{
			$mkEditorButtons.removeClass('active');
			$(this).addClass('active');
			$mkEditor.find('[data-target]').removeClass('show');
			$target.addClass('show');

			// load preview data
			if (mode === 'preview' && userData.form.content.value)
			{
				const preview = getPreviewMarkdownData();
				preview.done(function(res){
					$target.html(res);
				});
			}
		}

		return false;
	});

	// init submit
	$(userData.form).on('submit', function(){
		// check thumbnail image
		if (checkThumbnailImage())
		{
			return false;
		}

		// set json and get article json
		let json = userData.articleData;

		// set thumbnail data
		if (userData.thumbnail_image)
		{
			userData.form.thumbnail_image.value = userData.thumbnail_image;
		}

		// set thumbnail
		json.thumbnail = userData.thumbnail;

		// set add queue srl
		userData.form.addQueue.value = userData.addQueue.toString();

		// json object to hidden string
		json = encodeURIComponent(JSON.stringify(json));
		userData.form.json.value = json;

		// return false;
	});

});
