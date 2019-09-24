<?php


namespace skyliker;

use CURLFile;

use vendor\config;
use lib\VKFunctions;


/**
 * Класс по отправке картинки в запись
 *
 * Class sendToVK
 *
 */
class sendToVK{


	public static function run(): void{
		$photo = new CURLFile(realpath(__DIR__ . "/" . WorkPhoto::DIR_SAVE . WorkPhoto::NAME_PHOTO_LYCOMER));

		$upload = self::uploadFileServer();
		if(isset($upload['response'])) {
			$upload = $upload['response'];

			$load = self::loadFileServer($upload["upload_url"], ['photo' => $photo]);
			$save = self::saveFileServer($load);

			if(isset($save["response"])) {
				$save = $save["response"];
				$id = self::createWall($save[0])["response"]["post_id"];
				echo date("[H:i:s]", time()) . " Запись с id " . $id . " успешно опубликована в сообществе group_id которой = " . config::GROUP_ID . "!" . PHP_EOL;
			} else {
				echo date("[H:i:s]", time()) . " Произошла ошибка при работе метода pphotos.saveWallPhoto!" . PHP_EOL;
				var_dump($save);
			}
		} else {
			echo date("[H:i:s]", time()) . " Произошла ошибка при работе метода photos.getWallUploadServer!" . PHP_EOL;
			var_dump($upload);
		}
	}

	/**
	 * Получение адреса сервера, куда заливать картинку
	 *
	 * @return array
	 */
	private static function uploadFileServer(): array{
		$data = [
			"v"            => config::VERSION_VKAPI,
			"group_id"     => config::GROUP_ID,
			"access_token" => config::getUserToken()
		];

		$url = Config::curl_get_contents(Config::BASE_VK_REQUEST . "photos.getWallUploadServer?" . http_build_query($data));

		return json_decode($url, true);
	}

	/**
	 * Заливает файл на сервер
	 *
	 * @param string $url
	 * @param array $args
	 *
	 * @return array
	 */
	private static function loadFileServer(string $url, array $args): array{
		$url = config::curl_get_contents($url, true, $args);

		return json_decode($url, true);
	}

	/**
	 * Загрузка фотографий на стену
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	private static function saveFileServer(array $args): array{
		$args = array_merge($args, [
			"v"            => config::VERSION_VKAPI,
			"group_id"     => config::GROUP_ID,
			"access_token" => config::getUserToken()
		]);

		$url = Config::curl_get_contents(Config::BASE_VK_REQUEST . "photos.saveWallPhoto?" . http_build_query($args));
		return json_decode($url, true);
	}

	private static function createWall(array $args): array{
		return VKFunctions::sendPost(self::returnTextWall(), "photo" . $args["owner_id"] . "_" . $args["id"], "-" . config::GROUP_ID, 1, config::getUserToken());
	}

	/**
	 * Возвращает текст для поста
	 *
	 * @return string
	 */
	private static function returnTextWall(): string{
		$result = "» Вашему вниманию предоставляются ежемесячные итоги лайкомера. Поздравляем всех участников авто-конкурса!

Победителями становятся -

🥉 @id".($id = self::getElementbyIndex(2)[0])." (".self::getNamebyName($id).");
🥈 @id".($id = self::getElementbyIndex(1)[0])." (".self::getNamebyName($id).");
🥇 @id".($id = self::getElementbyIndex(0)[0])." (".self::getNamebyName($id).").

Счастливчики забирают приз — привилегию на 1 выше, чем у них имеется. Выигравших попросим указать свои никнеймы в комментарии или сообщить о получении награды в информационный отдел - @greenium.

А для новичков, объясняем условия этого самого \"Лайкомера\":
- Каждый месяц, в 20:00 по МСК подводится подсчет лайкнутых вами записей. Те, у кого лайков окажется больше, а именно ТОП - 3, получают вознаграждение: +1 к привилегии на любом из мини-режимов нашего проекта.

Особые требования к участвующему:

- запрещается использовать программы авто-лайков. При несоблюдении данного принципа, пользователь исключается с конкурса и мы не будем подсчитывать его активность;
- запрещено лайкать записи, нарушающие правила группы, сервера (оскорбление администрации, спам, флуд, провокации, посты с жалобами, пиаром, дезинформацией, нецензурной лексикой, продажей аккаунтов, продажей игрового баланса и подобного). За каждый лайк похожего поста, при его удалении, система запоминает тех, кто нарушил это правило и участник, в результате, получает -5 баллов к сумме лайков за каждое нарушение;
- проявлять активность в группе, оставляя комментарии и сообщения на стене сообщества;
- не зарабатывать наказание в виде блокировки аккаунта за все время проведения подсчета.";
		return $result;
	}

	/**
	 * @param int $index
	 *
	 * @return null|array [key, value]
	 */
	private static function getElementbyIndex(int $index): ?array{
		$i = 0;
		foreach(WorkPhoto::$userLikes as $id => $likes) {
			if($index === $i) {
				return [$id, $likes];
			}
			$i++;
		}
		return null;
	}

	/**
	 * Возвращает имя по id, если есть в массиве
	 *
	 * @param int $id
	 *
	 * @return string|null
	 */
	private static function getNamebyName(int $id): ?string{
		return WorkPhoto::$userInfo[$id]["name"] ?? null;
	}

}