<?php

use utils\Internet;
use utils\VKFunctions;

/**
 * Class WorkPhoto
 *
 * Класс по работе с картинкой
 */
class WorkPhoto{

    private const WIDTH_TEMPLATE = 1000;
    private const HEIGHT_TEMPLATE = 600;

    private const WIDTH_AVA = 50;
    private const HEIGHT_AVA = 50;

    private const DIR_TEMP_AVA = "load_temp_ava/";
    private const DIR_TEMPLATE = "template/";
    public const DIR_SAVE = "save_picture/";

    private const NAME_PHOTO_TEMPLATE = "template.png";
    public const NAME_PHOTO_LYCOMER = "lycomer_results.png";

    /**
     * Координаты на картинке
     * @var array
     */
    private static $coordsPhoto = [
        [293, 22],
        [293, 92],
        [293, 162]
    ];

    private const Y_TEMPLATE = [
        51,
        121,
        191,
        248,
        299,
        352,
        400,
        448,
        498,
        549
    ];

    /**
     * key - userId | value - countLikes
     *
     * example [
     *     502921656 => 120,
     *     474057519 => 111
     * ]
     *
     * @var array
     **/
    public static $userLikes = [ // тестовые значения
        308397063 => 3360, // 1
        493360939 => 3345, // 2
        550698373 => 3329, // 3
        289555434 => 3310, // 4
        233775001 => 3309, // 5
        359260178 => 3305, // 6
        361534087 => 3301, // 7
        283080910 => 3299, // 8
        166379386 => 3105, // 9
        377525473 => 3000, // 10
    ];

    public static $userInfo = [];

    private static function implode_key(string $glue, array $arr, int $numOfOut = -1): string{
        $result = "";
        $count = 0;
        foreach($arr as $name => $id) {
            $result .= ($count === 0 ? "" : $glue) . $name;
            $count++;
            if($numOfOut !== -1 && $count === $numOfOut) {
                break;
            }
        }

        return $result;
    }

    public static function run(): void{
        copy(self::DIR_TEMPLATE . self::NAME_PHOTO_TEMPLATE, self::DIR_SAVE . self::NAME_PHOTO_LYCOMER); // копируем файл

        try {
            self::initInfo();
            self::downPhotoThreePlaces();

            $cut = imagecreatetruecolor(self::WIDTH_TEMPLATE, self::HEIGHT_TEMPLATE);
            self::setPhoto($cut);
            self::setText($cut);

            imagepng($cut, self::DIR_SAVE . self::NAME_PHOTO_LYCOMER);

            self::removeTempPhoto();
            echo date("[H:i:s]", time()) . " Картинка успешно создана!" . PHP_EOL;

            sendToVK::run();
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }

    /**
     * Удаляет временные авы юзеров с машины
     */
    private static function removeTempPhoto(): void{
        for($i = 1; $i < 4; $i++) {
            $jpg = ".jpg";
            $png = ".png";

            $dir = self::DIR_TEMP_AVA . "photo_" . $i;

            unlink(file_exists($dir.$jpg) ? $dir.$jpg : $dir.$png);
        }
    }

    /**
     * Удаляет результирующую фотографию
     */
    public static function removeLycomerPhoto(): void{
        unlink(self::DIR_SAVE . self::NAME_PHOTO_LYCOMER);
        echo date("[H:i:s]", time()) . " Картинка была успешно удалена с директории save_picture!" . PHP_EOL;
    }

    /**
     * Инициализация информации
     *
     * @throws \Exception
     */
    private static function initInfo(): void{
        $i = 0; // счетчик срабатывания error code 6
        $maxI = 10; // макс кол-во срабатываний

        data:

        $userToken = Loader::getConfig()->getUserToken();
        $userIds = self::implode_key(",", self::$userLikes, 10);

        $data = VKFunctions::executeMethod("users.get", $userToken, [
            'user_ids' => $userIds,
            'fields' => 'photo_50'
        ]);

        if(isset($data["response"])) {
            $data = $data["response"];

            $j = 0;
            foreach($data as $value) {
                self::$userInfo[$value["id"]] = [];
                self::$userInfo[$value["id"]]["name"] = $value["first_name"] . " " . $value["last_name"];

                if($j < 3) {
                    self::$userInfo[$value["id"]]["url_photo"] = $value["photo_50"];
                }

                $j++;
            }
        } else {
            if(isset($data["error"]) && $data["error"]["error_code"] == 6 && ++$i <= $maxI) {
                usleep(500000 * ($i * 2));
                goto data;
            } else {
                var_dump($data);

                throw new \Exception(date("[H:i:s]", time()) . " Произошла ошибка при работе метода users.get в классе WorkPhoto!" . PHP_EOL);
            }
        }
    }

    /**
     * Скачивает фотки победителей в папку load_temp_ava
     */
    private static function downPhotoThreePlaces(): void{
        $i = 0;
        foreach(self::$userInfo as $value) {
            $i++;
            if(isset($value["url_photo"])) {
                $url = $value["url_photo"];

                $dataPhoto = Internet::requestURL($url);
                file_put_contents(self::DIR_TEMP_AVA . "photo_" . $i . "." . (strpos($url, "jpg") !== false ? "jpg" : "png"), $dataPhoto);
            }
        }
    }

    private static function setPhoto(&$cut): void{
        $i = 0;
        foreach(self::$coordsPhoto as $coord) {
            $jpg = ".jpg";
            $png = ".png";

            $dir = self::DIR_TEMP_AVA . "photo_" . ++$i;

            imagecopy($cut, ($image=(file_exists($dir.$jpg)?imagecreatefromjpeg($dir.$jpg):imagecreatefrompng($dir.$png))), $coord[0], $coord[1], 0, 0, self::WIDTH_AVA, self::HEIGHT_AVA);
            imagedestroy($image);
        }
        imagecopy($cut, imagecreatefrompng(self::DIR_SAVE . self::NAME_PHOTO_LYCOMER), 0, 0, 0, 0, self::WIDTH_TEMPLATE, self::HEIGHT_TEMPLATE);
    }

    private static function setText(&$cut): void{
        $colorBlack = imagecolorallocate($cut, 0x00, 0x00, 0x00);
        $colorGrey = imagecolorallocate($cut, 0xc0, 0xc0, 0xc0);
        $colorYellow = imagecolorallocate($cut, 0xc3, 0x8c, 0x4d);

        $i = 0;
        foreach(self::$userInfo as $id => $value) {
            $data = imagettftext($cut, 18, 0, 371, self::Y_TEMPLATE[$i], $colorBlack, "fonts/fontName.ttf", $value["name"] . " - ");
            $data = imagettftext($cut, 20, 0, $data[2], $data[3] - 1, $i < 3 ? $colorYellow : $colorGrey, "fonts/fontLike.ttf", self::$userLikes[$id]);

            imagecopy($cut, ($image = imagecreatefrompng(self::DIR_TEMPLATE . "icon_like.png")), $data[2] + 3, $data[5] - 5, 0, 0, self::WIDTH_AVA, self::HEIGHT_AVA);
            imagedestroy($image);

            $i++;
        }
    }
}