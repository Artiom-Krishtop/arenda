
export default new Vue({
	data:{
		avatar: '',
		fields: {},
		changedFieldCodes: [],
		loading: false
	},
	methods: {
		getFieldByCode: function(code){
			return this.fields.filter(function (field) {
				return field.code === code;
			})[0];
		},
		/**
		 * Сжимает картинку через canvas.
		 * @param {String} file Путь до файла или base64 код картинки
		 * @param {Object} settings Параметры:
		 * @param {Array} settings.size - Размеры изображения
		 * @param {String} settings.resize - Какого размера будет холст. Сама картинка будет пропорционально уменьшена:
		 * <ul>
		 *     <li>-EXACT - точно как в settings.size.</li>
		 *     <li>-PROPORTIONAL - под размера изображения</li>
		 * </ul>
		 * @param {Function} callback
		 */
		resize: function (file = '', settings = {}, callback) {
			if (!file) return;
			
			if (settings.resize && Array.isArray(settings.size) && settings.size.length === 2) {
				// вычислим размер изображения
				let img = new Image();
				img.src = file;
				img.onload = () => {
					let resizeWidth = img.width,
						resizeHeight = img.height,
						top = 0,
						left = 0,
						canvasWidth,
						canvasHeight,
						canvas = document.createElement('canvas'),
						imgBase64;
					
					// ресайзим изображение пропорционально
					// если размер превышает параметры
					if (Array.isArray(settings.size) && settings.size.length &&
						(resizeWidth > settings.size[0] || resizeHeight > settings.size[1])) {
						
						// ресайзим ширину
						if (resizeWidth > settings.size[0]) {
							// сначала ресайзим высоту пропорционально
							resizeHeight = resizeHeight * settings.size[0]/resizeWidth;
							resizeWidth = settings.size[0];
						}
						
						// ресайзим высоту
						if (resizeHeight > settings.size[1]) {
							// сначала ресайзим ширину пропорционально
							resizeWidth = resizeWidth * settings.size[1]/resizeHeight;
							resizeHeight = settings.size[1]
						}
					}
					
					// вычислим размер canvas
					// Если EXACT то изображение будет фиксированного размера
					if (settings.resize === 'EXACT') {
						canvasWidth = settings.size[0];
						canvasHeight = settings.size[1];
					} else {
						// Иначе под размер картинки
						canvasWidth = resizeWidth;
						canvasHeight = resizeHeight;
					}
					canvas.width = canvasWidth;
					canvas.height = canvasHeight;
					
					if (canvasWidth > resizeWidth ) left = canvasWidth/2 - resizeWidth/2;
					if (canvasHeight > resizeHeight ) top = canvasHeight/2 - resizeHeight/2;
					
					/*let logTable = {
						'img.width': img.width,
						'img.height': img.height,
						'canvasWidth': canvasWidth,
						'canvasHeight': canvasHeight,
						'left': left,
						'top': top,
						'resizeWidth': resizeWidth,
						'resizeHeight': resizeHeight,
					};
					console.table(logTable);*/
					
					let ctx = canvas.getContext("2d");
					ctx.drawImage(img, left, top, resizeWidth, resizeHeight);
					imgBase64 = canvas.toDataURL("image/png");
					
					callback(imgBase64);
				};
			}
		}
	}
});