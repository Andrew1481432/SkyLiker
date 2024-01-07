<?php

use utils\Internet;
use utils\InternetStatics;
use utils\VKFunctions;


/**
 * Класс по отправке картинки в сообщество в ВКонтакте
 *
 * Class sendToVK
 *
 */
class sendToVK {

    private const PUBLISH_DELAYED_WALL = 60*3;

    /**
     * @throws JsonException
     */
    public static function run(): void{
		$i = 0; // счетчик срабатывания error code 6
		$maxI = 10;
		$photo = new CURLFile(realpath(__DIR__ . "/" . WorkPhoto::DIR_SAVE . WorkPhoto::NAME_PHOTO_LYCOMER));

		upload:
		$upload = self::uploadFileServer();
		if(isset($upload['response'])) {
			$upload = $upload['response'];

			$load = self::loadFileServer($upload["upload_url"], [
                'photo' => $photo
            ]);

			usleep(500000);
			save:
			$save = self::saveFileServer($load);
			if(isset($save["response"])) {
				$save = $save["response"];

                $responseWallPost = self::createWall($save[0]);
                if(isset($responseWallPost["response"])) {
                    $id = $responseWallPost["response"]["post_id"];

                    $publishGroupId = Loader::getConfig()->getPublishGroupId();
                    echo date("[H:i:s]", time()) . " Запись с id " . $id . " успешно опубликована в сообществе group_id -> " . $publishGroupId . "!" . PHP_EOL;
                } else {
                    echo "Произошла ошибка в момент публикации поста!" . PHP_EOL;
                    var_dump($responseWallPost);
                }
			} else {
				if(isset($save["error"]) && $save["error"]["error_code"] == 6 && ++$i <= $maxI) {
					usleep(500000);
					goto save;
				} else {
					echo date("[H:i:s]", time()) . " Произошла ошибка при работе метода photos.saveWallPhoto!" . PHP_EOL;
					var_dump($save);
				}
			}
		} else {
			if(isset($upload["error"]) && $upload["error"]["error_code"] == 6 && ++$i <= $maxI) {
				usleep(500000);
				goto upload;
			} else {
				echo date("[H:i:s]", time()) . " Произошла ошибка при работе метода photos.getWallUploadServer!" . PHP_EOL;
				var_dump($upload);
			}
		}
	}

    /**
     * Получение адреса сервера, куда заливать картинку
     *
     * @return array
     * @throws JsonException
     */
	private static function uploadFileServer(): array{
        $publishGroupId = Loader::getConfig()->getPublishGroupId();
        $userToken = Loader::getConfig()->getUserToken();

		return VKFunctions::executeMethod("photos.getWallUploadServer", $userToken,
            [
                "group_id" => $publishGroupId
            ]
        );
	}

	/**
	 * Заливает файл на сервер
	 *
	 * @param string $url
	 * @param array $params
	 *
	 * @return array
	 */
	private static function loadFileServer(string $url, array $params): array{
        $raw = Internet::requestURL($url, InternetStatics::CUSTOM_REQUEST_POST,
            InternetStatics::CONTENT_TYPE_MULTIPART_FORM_DATA, [], $params);

		return json_decode($raw, true);
	}

    /**
     * Загрузка фотографий на стену
     *
     * @param array $params
     *
     * @return array
     * @throws JsonException
     */
	private static function saveFileServer(array $params): array{
        $publishGroupId = Loader::getConfig()->getPublishGroupId();
        $userToken = Loader::getConfig()->getUserToken();

        $params = array_merge($params, [
			"group_id" => $publishGroupId
		]);

		return VKFunctions::executeMethod("photos.saveWallPhoto", $userToken, $params);
	}

    /**
     * @param array $params
     *
     * @return array
     * @throws JsonException
     */
    private static function createWall(array $params): array{
        $publishGroupId = Loader::getConfig()->getPublishGroupId();
        $userToken = Loader::getConfig()->getUserToken();

		return VKFunctions::executeMethod("wall.post", $userToken,
            [
                'message' => self::returnTextWall(),
                'attachments' => rawurlencode("photo".$params["owner_id"]."_".$params["id"]),
                'owner_id' => rawurlencode( "-".$publishGroupId),
                'from_group' => 1,
                'publish_date' => time()+self::PUBLISH_DELAYED_WALL
            ]);
	}

	/**
	 * Возвращает текст для поста
	 *
	 * @return string
	 */
	private static function returnTextWall(): string{
        return "» ЛАЙКОМЕР - ИТОГИ МЕСЯЦА.

#likemer

Победителями становятся -

🥇 @id".($id = self::getElementByIndex(0)[0])." (".self::getNameByName($id).");
🥈 @id".($id = self::getElementByIndex(1)[0])." (".self::getNameByName($id).");
🥉 @id".($id = self::getElementByIndex(2)[0])." (".self::getNameByName($id).").";
	}

	/**
	 * @param int $index
	 *
	 * @return null|array [key, value]
	 */
	private static function getElementByIndex(int $index): ?array{
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
	private static function getNameByName(int $id): ?string{
		return WorkPhoto::$userInfo[$id]["name"] ?? null;
	}

}