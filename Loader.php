<?php

include_once 'vendor/include_all.php';

use utils\Config;
use utils\VKFunctions;

date_default_timezone_set('Europe/Moscow');

/**
 * Лайкомер - сервис который поможет анализировать активность в сообществе
 * Выводит топ пользователей, которые больше всего пролайкали записи пользователей или группы в сообществе
 */
class Loader {

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

    /** @var Config */
    private static $config;

    /**
     * Функция запуска программы
     *
     * @throws JsonException
     */
    public static function run(): void{
        if(!extension_loaded("gd")){
            echo "[CRITICAL] Unable to find the GD extension." . PHP_EOL;
            exit(1);
        }
        if (!function_exists('imagettftext')) {
            echo "[CRITICAL] FreeType not found." . PHP_EOL;
            exit(1);
        }

        echo date("[H:i:s]", time()) . " SkyLiker - RUN!!!" . PHP_EOL . PHP_EOL;

        self::$config = new Config("resources/config.json");

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
                    usleep(500000 * ($i * 2));

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
                if($post["date"] < $endUnix && !isset($post["is_pinned"])) {
                    arsort(self::$userLikes);

                    WorkPhoto::$userLikes = self::$userLikes;
                    WorkPhoto::run();
                    WorkPhoto::removeLycomerPhoto();
                    break 2;
                }

                if(($diff = $post["date"] - $endUnix) > 0 && !isset($post["is_pinned"])) {
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
     * Возвращает лайки используя массив постов
     *
     * @param array $posts
     *
     * @return \Generator
     * @throws JsonException
     */
    private static function getLikes(array $posts): \Generator{
        foreach($posts as $post) {
            $i = 1;

            repeat:
            $data = VKFunctions::executeMethod(
                "likes.getList",
                self::$config->getUserToken(),
                [
                    'type' => 'post',
                    'owner_id' => "-" . self::$config->getParseGroupId(),
                    'item_id' => $post,
                    'count' => 1000,
                    'offset' => 0
                ]
            );

            if(isset($data['error']['error_code'])){
                //echo date("[H:i:s]", time()) . " Уснул на полсек getLikeList" . PHP_EOL;
                usleep(500000 * ($i * 2));

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
     * @return array|null
     * @throws JsonException
     */
    private static function getWall(int $offset = 0): ?array{
        return VKFunctions::executeMethod(
            "wall.get",
            self::$config->getUserToken(),
            [
                'owner_id' => "-".self::$config->getParseGroupId(),
                'count' => 100,
                'offset' => $offset
            ]
        );
    }

    /**
     * Получение unix time прошлого месяца
     *
     * @return int
     */
    private static function getUnixLastMonth(): int{
        $year = date("Y");
        $month = date("n");

        if($month == 1) { // если месяц январь
            $year--;
            $month = 12;
        } else {
            $month--;
        }

        return mktime(date("H"), date("i"), date("s"), $month, date("j"), $year);
    }

    /**
     * @return Config
     */
    public static function getConfig(): Config{
        return self::$config;
    }

}

Loader::run();