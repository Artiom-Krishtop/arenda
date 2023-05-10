
;(function () {
	window.fileUploadLight = function ($container) {
		var self = this;

		self.$container = $container;
		self.$fileInput = $container.find('[type="file"]'),
		self.$preview = $container.find('.file-upload-light__preview'),
		self.nodeInput = self.$fileInput.get(0);

		self.updateFilesView = function () {
			if (self.nodeInput.files.length) {
				var file = self.nodeInput.files[0];
				var reader = new FileReader();
				reader.readAsDataURL(file);
				reader.onload = function(e) {
					var src = e.target.result;
					self.$preview.css('background-image', 'url('+src+')');
					self.$preview.removeClass('_empty');
				}
			} else {
				self.removeFileView();
			}
		};

		self.removeFileView = function () {
			self.$preview.removeAttr('style')
							.addClass('_empty');
		};
		self.$fileInput
			.on('validError', function(event) {
				self.removeFileView();
			})
			.on('validSucess', function(event) {
				self.updateFilesView();
			})
			.on('change', function(event) {
			    event.preventDefault();

			    //если добавлена валидация то обрабатываем события валидации
			    if ($(this).val() && $(this).data('validator-handlers-attached')) return;
				self.updateFilesView();
			})
			.on('reset', function(event) {
				self.removeFileView();
			});
	};
})();