jQuery(function($){

	// assign json
	if (userData.articleData)
	{
		if (userData.articleData.thumbnail)
		{
			userData.thumbnail = userData.articleData.thumbnail || {};
		}
	}


	// insert source to content form
	var insertSourceToContentForm = function(items)
	{
		// get cursor position
		var getCursorPosition = function($el)
		{
			var el = $el.get(0);
			var pos = 0;
			if ('selectionStart' in el)
			{
				pos = el.selectionStart;
			}
			else if ('selection' in document)
			{
				el.focus();
				var Sel = document.selection.createRange();
				var SelLength = document.selection.createRange().text.length;
				Sel.moveStart('character', -el.value.length);
				pos = Sel.text.length - SelLength;
			}
			return pos;
		};

		var str = '';
		for (var i=0; i<items.length; i++)
		{
			if (/^image/.test(items[i].type))
			{
				str += '![](' + items[i].src + ')\n';
			}
			else
			{
				str += '[' + items[i].name + '](' + items[i].src + ')\n';
			}
		}

		var pos = getCursorPosition($(userData.form.content));
		var val = userData.form.content.value;
		var newContent = val.substr(0, pos) + str + val.substr(pos);
		userData.form.content.value = newContent;
	};

	// get preview data
	var getPreviewData = function(complete)
	{
		var req = $.ajax({
			url : userData.root + '/script/run/markdown_preview/'
			,type : 'post'
			,data : {
				title : '...'
				,nest_srl : userData.form.nest_srl.value
				,content : userData.form.content.value
			}
		});
		req.done(function(str){
			complete(str);
		});
	};

	// check thumbnail image
	var checkThumbnailImage = function()
	{
		var files = uploader.queue.items.files;
		var inImage = false;

		// check thumbnail queue
		if (userData.form.article_srl.value && uploader.queue.$queue.children('.is-thumbnail').length)
		{
			return false;
		}

		// check image type in queues
		for (var i=0; i<files.length; i++)
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
	};

	// get thumbnail size
	var getThumbnailSize = function(setting, file)
	{
		var defer = $.Deferred();
		var image = new Image();

		// get size
		function getSize(type, set_w, set_h, img_w, img_h)
		{
			var size = {};
			switch(type)
			{
				case 'resize':
					if (img_w < img_h)
					{
						size.width = parseInt((img_w / img_h) * set_h);
						size.height = parseInt(set_h);
					}
					else
					{
						size.width = parseInt(set_w);
						size.height = parseInt((img_h / img_w) * set_w);
					}
					break;
				case 'resizeWidth':
					size.width = parseInt(set_w);
					size.height = parseInt((img_h / img_w) * set_w);
					break;
				case 'resizeHeight':
					size.width = parseInt((img_w / img_h) * set_h);
					size.height = parseInt(set_h);
					break;
				default:
					size.width = parseInt(set_w);
					size.height = parseInt(set_h);
					break;
			}
			return size;
		}

		image.onload = function(e)
		{
			var size = getSize(setting.type, setting.size.width, setting.size.height, image.width, image.height);
			defer.resolve(size);
		};
		image.src = userData.root + '/' + file.src;

		return defer.promise();
	};


	// init uploader
	window.uploader = new RGUploader($('#queuesManager'), {
		autoUpload : true,
		allowFileTypes : ['jpeg', 'png', 'gif'],
		limitSize : 3000000,
		limitSizeTotal : 10000000,
		uploadScript : userData.root + '/file/upload/',
		removeScript : userData.root + '/file/remove/',
		uploadParams : {
			table : 'file_tmp',
			upload_loc : userData.originalPath
		},
		srcPrefixName : userData.url,
		queue : {
			style : 'list',
			height : 150,
			limit : 10,
			datas : userData.pushDatas,
			buttons : [
				{
					name : 'open file',
					iconName : 'open_in_new',
					action : function(app, file) {
						window.open(file.fullSrc);
					}
				},
				{
					name : 'make thumbnail image',
					iconName : 'apps',
					className : 'btn-make-thumbnail',
					show : function(file) {
						return (file.type.split('/')[0] == 'image');
					},
					action : function(app, file) {
						if (!app.plugin.child.thumbnail) return false;
						var plugin = app.plugin.child.thumbnail;
						var option = {};

						// set option
						if (file.srl == userData.thumbnail.srl)
						{
							option.points = userData.thumbnail.points;
							option.zoom = userData.thumbnail.zoom;
						}

						if ((userData.thumbnail.type && userData.thumbnail.type == 'crop') || !userData.thumbnail.type)
						{
							plugin.open(file, option);
						}
						else
						{
							var ready = getThumbnailSize(userData.thumbnailSet, file);
							ready.done(function(size){
								plugin.assignOption({
									output : {
										size : { width : size.width, height : size.height }
									},
									croppie : {
										viewport : { width: size.width, height: size.height }
									}
								});
								plugin.open(file, option);
							});
						}
					}
				},
				{
					name : 'insert editor',
					iconName : 'center_focus_strong',
					action : function(app, file) {
						insertSourceToContentForm([{
							src : file.fullSrc,
							name : file.name,
							type : file.type
						}]);
					}
				},
				{
					name : 'remove queue',
					iconName : 'close',
					action : function(app, file) {
						app.queue.removeQueue(file.srl, false, true);
					}
				}
			]
		},
		plugin : [
			{ name : 'preview', obj : new RG_Preview() },
			{ name : 'sizeinfo', obj : new RG_Sizeinfo() },
			{ name : 'dnd', obj : new RG_DragAndDrop() },
			{
				name : 'thumbnail',
				obj : new RG_Thumbnail({
					width : 640,
					height : 480,
					mobileSize : 640,
					url_croppieCSS : userData.root + '/vendor/rg-Uploader/vendor/Croppie/croppie.css',
					url_croppieJS : userData.root + '/vendor/rg-Uploader/vendor/Croppie/croppie.min.js',
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
					doneCallback : function(res, app, file) {
						var classBtnMakeThumbnail = '.btn-make-thumbnail';

						// input thumbnail image source
						userData.thumbnail_image = res.src;

						// update on class name
						var $btns = app.queue.$queue.find(classBtnMakeThumbnail);
						$btns.removeClass('on is-thumbnail');
						app.queue.selectQueueElement(file.id)
							.addClass('is-thumbnail')
							.find(classBtnMakeThumbnail).addClass('on');

						// set thumbnail data
						var croppie = app.plugin.child.thumbnail.croppie.get();
						var options = app.plugin.child.thumbnail.options;
						userData.thumbnail.srl = file.srl;
						userData.thumbnail.points = croppie.points;
						userData.thumbnail.zoom = croppie.zoom;
						userData.thumbnail.size = options.output.size;
					}
				})
			}
		],
		uploadDataFilter : function(res) {
			return {
				state : res[0].state,
				response : {
					src : res[0].loc,
					srl : res[0].srl,
					name : res[0].name,
					table : 'file_tmp'
				}
			}
		},
		removeParamsFilter : function(res) {
			return {
				data : JSON.stringify([{ table : res.table, srl : res.srl }])
			};
		},
		removeDataFilter : function(res) {},
		uploadComplete : function(file) {
			userData.addQueue.push(file.srl);
		},
		init : function(app) {
			// add queue srls
			for(var i=0; i<userData.pushDatas.length; i++)
			{
				if (userData.pushDatas[i].table !== 'file_tmp') continue;
				userData.addQueue.push(userData.pushDatas[i].srl);
			}

			// set thumbnail queue
			if (userData.articleData.thumbnail)
			{
				var $queue = app.queue.selectQueueElement(userData.articleData.thumbnail.srl);
				$queue
					.addClass('is-thumbnail')
					.find('.btn-make-thumbnail').addClass('on');
			}
		}
	});

	// toggle edit/preview
	var $mkEditor = $('div.mk-editor');
	var $mkEditorButtons = $mkEditor.find('a[data-control]');
	$mkEditorButtons.on('click', function(){

		var mode = $(this).attr('data-control');
		var $target = $mkEditor.find('[data-target=' + mode + ']');

		if (!$(this).hasClass('active'))
		{
			$mkEditorButtons.removeClass('active');
			$(this).addClass('active');
			$mkEditor.find('[data-target]').removeClass('show');
			$target.addClass('show');

			// load preview data
			if (mode == 'preview' && userData.form.content.value)
			{
				var result = getPreviewData(function(result){
					$target.html(result);
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
		var json = userData.articleData;

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

		//return false;
	});

});
