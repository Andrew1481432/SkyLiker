<?php

namespace skyliker;

/**
 * Лайкомер - приложение которое поможет анализировать активность в сообществе
 * Выводит топ пользователей, которые больше всего пролайкали записи сообщества
 */

include_once '../vendor/include_all.php';

use lib\VKFunctions;
use vendor\config;

use skyliker\WorkPhoto;

date_default_timezone_set('Europe/Moscow');


class SkyLiker{

	/** @var int */
	private static $group_id;

	/**
	 * key - userId | value - countLikes
	 *
	 * example [
	 *     502921656 => 120,
	 *     474057519 => 111
	 * ]
	 *
	 * @var array
	 */
	private static $userLikes = [];

	/**
	 * Функция запуска программы
	 */
	public static function run(): void{
		echo date("[H:i:s]", time()) . " SkyLiker - RUN!!!" . PHP_EOL . PHP_EOL;

		self::$group_id = config::getConfig()["group_id"];

		$endUnix = self::getUnixLastMonth();

		$checkDay = 0;
		$maxCountDay = 0;

		$offset = 0;

		while(true) {
			$dataWall = self::getWall($offset);

			if(isset($dataWall['error']['error_code'])){
				$i = 1;
				while(true) {
					//echo date("[H:i:s]", time()) . " Уснул на полсек getWall" . PHP_EOL;
					usleep(500000 * ($i * $i));

					$dataWall = self::getWall($offset);
					if(isset($dataWall["response"])) {
						break;
					}

					if($i++ > 4) {
						echo date("[H:i:s]", time()) . " Уже больше 4-х раз ловил капчу в getWall!" . PHP_EOL;
					}
				}
			}

			$idPosts = [];

			$dataWall = $dataWall["response"]["items"];

			foreach($dataWall as $post) {
				if($post["date"] < $endUnix && empty($post["is_pinned"])) {
					arsort(self::$userLikes);

					WorkPhoto::$userLikes = self::$userLikes;
					WorkPhoto::run();

					WorkPhoto::removeLycomerPhoto();


					break 2;
				}

				if(($diff = $post["date"] - $endUnix) > 0) {
					$diff = round($diff / 60 / 60 / 24);

					if($maxCountDay === 0) { // срабатывает один раз ищет макс день
						$maxCountDay = $diff;
					}

					if($diff !== $checkDay) {
						$checkDay = $diff;
						$perc = 100 - (int) round($checkDay * 100 / $maxCountDay);

						if($perc >= 0) {
							echo date("[H:i:s]", time()) . " Загружено на " . $perc . "%!" . PHP_EOL;
						}
					}
				}

				$idPosts[] = $post["id"];
			}

			if($idPosts) {
				$likesPost = self::getLikes($idPosts);

				foreach($likesPost as $likesUser) {
					self::addUserLike($likesUser);
				}
 			}

			$offset += 100;
		}
	}

	private static function addUserLike(array $users): void{
		foreach($users as $user) {
			if(isset(self::$userLikes[$user])) {
				self::$userLikes[$user]++;
			} else {
				self::$userLikes[$user] = 1;
			}
		}
	}

	/**
	 * @param $uid
	 *
	 * @return array
	 */

	/**
	 * Возвращает массив лайков
	 *
	 * @param array $posts
	 *
	 * @return \Generator
	 */
	private static function getLikes(array $posts) {
		foreach($posts as $post) {
			$i = 1;

			repeat:
			$data = VKFunctions::getLikeList("-" . self::$group_id, $post, 1000, 0, config::getUserToken());

			if(isset($data['error']['error_code'])){
				//echo date("[H:i:s]", time()) . " Уснул на полсек getLikeList" . PHP_EOL;
				usleep(500000 * ($i * $i));

				if($i++ > 4) {
					echo date("[H:i:s]", time()) . " Уже больше 4-х раз ловил капчу в getLikeList!" . PHP_EOL;
				}

				goto repeat;
			}

			yield $data["response"]["items"];
		}
	}

	/**
	 * @param int $offset
	 *
	 * @return array|bool
	 */
	private static function getWall(int $offset = 0): ?array{
		return VKFunctions::getPosts("-".self::$group_id, 100, $offset, config::getUserToken());
	}

	/**
	 * Получение unix time прошлого месяца
	 *
	 * @return int
	 */
	private static function getUnixLastMonth(): int{
		$year = date("o");
		$month = date("n");

		if($month == 1) { //  если месяц январь
			$year--;
			$month = 12;
		} else {
			$month--;
		}

		return mktime(date("H"), date("i"), date("s"), $month, date("j"), $year);
	}

}

SkyLiker::run();



