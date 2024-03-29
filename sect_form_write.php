<? $APPLICATION->IncludeComponent(
	"citrus.forms:iblock.element",
	"simple",
	array(
		"AFTER_FORM_TOOLTIP" => "",
		"ANCHOR_ID" => "",
		"BEFORE_FORM_TOOLTIP" => "",
		"BUTTON_POSITION" => "CENTER",
		"BUTTON_TITLE" => "Отправить сообщение",
		"EDIT_ELEMENT" => "N",
		"ELEMENT_ID" => "",
		"ERROR_TEXT" => "",
		"FORM_STYLE" => "WHITE",
		"FORM_TITLE" => "",
		"IBLOCK_ID" => "",
		"IBLOCK_TYPE" => "feedback",
		"JQUERY_VALID" => "Y",
		"NOT_CREATE_ELEMENT" => "N",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"REDIRECT_AFTER_SUCCESS" => "N",
		"SEND_MESSAGE" => "Y",
		"MAIL_EVENT" => "CITRUS_REALTY_NEW_REQUEST",
		"SUB_TEXT" => "",
		"SUCCESS_TEXT" => "Сообщение успешно отправлено",
		"USER_SERVER_VALIDATE" => "N",
		"COMPONENT_TEMPLATE" => ".default",
		"FIELDS" => array(
			"PROPERTY_href" => array(
				"TITLE" => "Отправлено со страницы",
				"IS_REQUIRED" => "N",
				"HIDE_FIELD" => "Y",
				"ORIGINAL_TITLE" => "[54] Отправлено со страницы",
				"DEFAULT" => Citrus\Arealty\Helper::getPath(),
			),
			"group_1493898921502" => array(
				"ORIGINAL_TITLE" => "Новая группа (group_1493898921502)",
				"TITLE" => "",
				"GROUP_FIELD" => "Y",
				"DEPTH_LAVEL" => "1",
				"CLASS" => "row-ib",
				"IS_REQUIRED" => "N",
				"HIDE_FIELD" => "N",
			),
			"group_1493898928264" => array(
				"ORIGINAL_TITLE" => "Новая группа (group_1493898928264)",
				"TITLE" => "",
				"GROUP_FIELD" => "Y",
				"DEPTH_LAVEL" => "2",
				"CLASS" => "col-md-6",
				"IS_REQUIRED" => "N",
				"HIDE_FIELD" => "N",
			),
			"NAME" => array(
				"ORIGINAL_TITLE" => "Название",
				"TITLE" => "Ваше имя",
				"IS_REQUIRED" => "N",
				"HIDE_FIELD" => "N",
			),
			"PROPERTY_email" => array(
				"TITLE" => "Email",
				"IS_REQUIRED" => "N",
				"HIDE_FIELD" => "N",
				"ORIGINAL_TITLE" => "[56] Email",
				"VALIDRULE" => "email",
			),
			"PROPERTY_phone" => array(
				"TITLE" => "Телефон",
				"IS_REQUIRED" => "N",
				"HIDE_FIELD" => "N",
				"ORIGINAL_TITLE" => "[55] Телефон",
				"TEMPLATE_ID" => "phone",
				"VALIDRULE" => "phone",
			),
			"group_1493898937831" => array(
				"ORIGINAL_TITLE" => "Новая группа (group_1493898937831)",
				"TITLE" => "",
				"GROUP_FIELD" => "Y",
				"DEPTH_LAVEL" => "2",
				"CLASS" => "col-md-6",
				"IS_REQUIRED" => "N",
				"HIDE_FIELD" => "N",
			),
			"PREVIEW_TEXT" => array(
				"ORIGINAL_TITLE" => "Описание для анонса",
				"TITLE" => "Ваше сообщение",
				"IS_REQUIRED" => "N",
				"HIDE_FIELD" => "N",
			),
		),
		"SAVE_SESSION" => "Y",
		"FORM_ID" => "68fe236acbcb7d2e4034a68d2fc9bc76",
		"USE_SERVER_VALIDATE" => "N",
		"AJAX" => "Y",
		"IBLOCK_CODE" => "requests",
		"USE_GOOGLE_RECAPTCHA" => "N",
		"HIDDEN_ANTI_SPAM" => "Y",
		"AGREEMENT_LINK" => "/agreement/",
	),
	false
); ?>