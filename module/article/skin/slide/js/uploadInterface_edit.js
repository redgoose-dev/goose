// UploadInterface class

/**
 * files queue element
 */
UploadInterface.prototype.$queue = $('<div class="filesQueue"><ul></ul></div>');

/**
 * controller button event
 * 하단 버튼이벤트
 *
 * @return void
 */
UploadInterface.prototype.controllerButton = function()
{
	var self = this;
	var dom = '<button type="button" data-action="selectAll" class="gs-button size-small">모두선택</button>';
	dom += '<button type="button" data-action="deleteSelect" class="gs-button size-small">선택삭제</button>';
	dom += '<button type="button" data-action="deleteAll" class="gs-button size-small">모두삭제</button>';

	this.$controller
		.append($(dom))
		.children('button').on('click', function(){
			switch($(this).attr('data-action'))
			{
				// select all items
				case 'selectAll':
					self.selectAllQueue();
					break;

				// delete item
				case 'deleteSelect':
					self.deleteQueue();
					break;

				// delete all item
				case 'deleteAll':
					self.deleteAllQueue();
					break;
			}
		})
	;
};

/**
 * Events init
 */
UploadInterface.prototype.events = function()
{
	var self = this;

	// file input change
	if (self.settings.auto)
	{
		$el.on('change', function(){
			self.upload();
		});
	}

	// keyboard event
	$(window).on('keydown', function(e){
		if (e.which == 91 || e.which == 17)
		{
			self.key = true;
		}
	});
	$(window).on('keyup', function(){
		self.key = false;
	});

	// drop files event
	if (self.$drop)
	{
		self.$drop.on('dragover', false);
		self.$drop.on('dragenter', function(e){
			e.preventDefault();
			e.stopPropagation();
			$(this).addClass('drag')
		});
		self.$drop.on('dragleave', function(e){
			e.preventDefault();
			e.stopPropagation();
			$(this).removeClass('drag')
		});
		self.$drop.on('drop', function(e){
			if (e.originalEvent.dataTransfer)
			{
				if (e.originalEvent.dataTransfer.files.length)
				{
					e.preventDefault();
					e.stopPropagation();
					$(this).removeClass('drag');
					self.upload(e.originalEvent.dataTransfer.files);
				}
			}
		});
	}

	// Drag event
	self.queue.$index.dragsort({
		dragSelector : 'li'
		,dragSelectorExclude : 'span[contenteditable], button'
		,dragBetween: true
		,placeHolderTemplate: '<li class="placeHolder"><div></div></li>'
		,dragEnd: function() {}
	});
};

