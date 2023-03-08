
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
		 * ������� �������� ����� canvas.
		 * @param {String} file ���� �� ����� ��� base64 ��� ��������
		 * @param {Object} settings ���������:
		 * @param {Array} settings.size - ������� �����������
		 * @param {String} settings.resize - ������ ������� ����� �����. ���� �������� ����� ��������������� ���������:
		 * <ul>
		 *     <li>-EXACT - ����� ��� � settings.size.</li>
		 *     <li>-PROPORTIONAL - ��� ������� �����������</li>
		 * </ul>
		 * @param {Function} callback
		 */
		resize: function (file = '', settings = {}, callback) {
			if (!file) return;
			
			if (settings.resize && Array.isArray(settings.size) && settings.size.length === 2) {
				// �������� ������ �����������
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
					
					// �������� ����������� ���������������
					// ���� ������ ��������� ���������
					if (Array.isArray(settings.size) && settings.size.length &&
						(resizeWidth > settings.size[0] || resizeHeight > settings.size[1])) {
						
						// �������� ������
						if (resizeWidth > settings.size[0]) {
							// ������� �������� ������ ���������������
							resizeHeight = resizeHeight * settings.size[0]/resizeWidth;
							resizeWidth = settings.size[0];
						}
						
						// �������� ������
						if (resizeHeight > settings.size[1]) {
							// ������� �������� ������ ���������������
							resizeWidth = resizeWidth * settings.size[1]/resizeHeight;
							resizeHeight = settings.size[1]
						}
					}
					
					// �������� ������ canvas
					// ���� EXACT �� ����������� ����� �������������� �������
					if (settings.resize === 'EXACT') {
						canvasWidth = settings.size[0];
						canvasHeight = settings.size[1];
					} else {
						// ����� ��� ������ ��������
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