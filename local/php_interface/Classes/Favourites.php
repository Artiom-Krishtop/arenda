<?
namespace ITG\Custom;

/**
 * ����� ��� ������ � ���������
 * @package Citrus\Arealty
 */
class Favourites
{
	// nook use cookies or session storage to store favourites for longer period
	// nook spread current state to all open tabs
	static private $list = array();

	/**
	 * ���������� � ���������
	 * @param int $element ID ����������� (�������� ���������)
	 */
	public static function add($element)
	{
		self::init();
		self::check($element);
		if (!self::isInList($element))
		{
			self::$list[$element] = true;
			self::setFavoriteValue(self::$list);
		}
	}

	protected static function init()
	{
		global $USER;

		if($USER->isAuthorized()){
			self::$list = self::getFavoriteFromUser($USER);
		}else{
			self::$list = self::getFavoriteFromCookie();
		}
	}

	protected static function getFavoriteFromUser(\CUser $USER)
	{
		$dbUser = $USER->GetByID($USER->GetId());

		if($user = $dbUser->Fetch()){
			return json_decode($user['UF_FAVORITES_ANN'], true);
		}

		return array();
	}

	protected static function getFavoriteFromCookie()
	{
		if (isset($_COOKIE["citrus_realestate_favourites"]) && strlen($_COOKIE["citrus_realestate_favourites"]) > 0){
			return json_decode($_COOKIE["citrus_realestate_favourites"], true);
		}

		return array();
	}

	protected static function setFavoriteToUser(\CUser $USER, $value)
	{
		return $USER->Update($USER->GetID(), array('UF_FAVORITES_ANN' => json_encode($value, JSON_UNESCAPED_UNICODE)));
	}

	protected static function setFavoriteToCookie($value)
	{
		return setcookie("citrus_realestate_favourites", json_encode($value, JSON_UNESCAPED_UNICODE), time()+60*60*24*30, '/');
	}

	protected static function check($element)
	{
		$element = intval($element);
		if ($element <= 0)
		{
			throw new \Exception("element must be a positive int");
		}
	}

	protected static function setFavoriteValue(array $value = [])
	{
		global $USER;

		if($USER->isAuthorized()){
			self::setFavoriteToUser($USER, $value);
		}else{
			self::setFavoriteToCookie($value);
		}
	}

	/**
	 * ���������� true, ���� ��������� ����������� ��� ��������� � ���������
	 * @param int $element ID ����������� (�������� ���������)
	 * @return bool
	 */
	public static function isInList($element)
	{
		return array_key_exists($element, self::$list);
	}

	/**
	 * �������� �� ����������
	 * @param int $element ID ����������� (�������� ���������)
	 */
	public static function remove($element)
	{
		self::init();
		self::check($element);
		
		if (self::isInList($element))
		{
			unset(self::$list[$element]);
			self::setFavoriteValue(self::$list);
		}
	}

	/**
	 * ���������� ���������� ��������� � ���������
	 * @return int
	 */
	public static function getCount()
	{
		self::init();

		return count(self::$list);
	}

	/**
	 * ���������� ������ ���������, ����������� � ���������
	 * @return array ������ ����������
	 */
	public static function getList()
	{
		self::init();

		return self::$list;
	}

}