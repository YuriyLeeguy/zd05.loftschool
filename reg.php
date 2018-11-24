<?php
function getConnect(){

}

function getUser(){
    return $userId;
}

function addUser(){

}

function addOrder(){

}
function sendMail(){

}

function main(){

}

main();

$mySqli = new mysqli('localhost', 'root', '', 'bdvp1'); // подключаемся к БД "bdvp1"
// Запрос информации Клиента по форме из файла "Index.html"
$userName = $_POST['name'];
$userPhone = $_POST['phone'];
$userMail = $_POST['email'];
$userStreet = $_POST['street'];
$userHome = $_POST['home'];
$userPart = $_POST['part'];
$userappt = $_POST['appt'];
$userFloor = $_POST['floor'];
$userComment = $_POST['comment'];
// Объеденяю данные по адресу
$address = "Улица: " . $userStreet . "<BR>"
    . "Дом № " . $userHome . "<BR>"
    . "Корпус № " . $userPart . "<BR>"
    . "Квартира № " . $userappt . "<BR>"
    . "Этаж № " . $userFloor;
// Дату и время делаю так чтобы можно было дать имя файлу
$time = date('d.m.Y h:i');
$d = str_replace(".", "_", $time);
$t = str_replace(" ", "-", $d);
$timeResult = str_replace(":","_",$t);

// Запрос для дальнейшей проверки наличии информации в таблице "users"
$choice = 'select * from users';
$searchResult = $mySqli->query($choice);

// Проверка на наличии данных из Таблицы "users"
if ($searchResult->num_rows) {
    $data = $searchResult->fetch_all();
// Записываем данные email в переменную $result из Таблицы "users", для дальнейшей проверки
    for ($i = 0; $i < count($data); $i++) {
        if ($data[$i][3] == $userMail) {
            $result = $data[$i][3]; // Данные email
            $idUser = $data[$i][0]; // Для записи в Таблицу deliveryusers, определяем ID постоянного клиента
        }
    }
}
if (!empty($userName) && !empty($userPhone) && !empty($userMail)) { // проверка на правильность заполнения Формы Заказа
    if ($result == $userMail) { // проверка, явлется ли User постоянным Заказчиком, если да, то
        // Записываем Адрес доставки и 'ID' Клиента из таблицы 'users'
        $delUs = $mySqli->query("INSERT INTO deliveryusers VALUES (NULL, '$idUser', '$address', '$userComment')");
        // Делаем запрос в Таблицу 'deliveryusers' для дальнейшего определения Id Заказа, Адресс, Коментарии
        $idOder = $mySqli->query('select * from deliveryusers');
        $idOderRes = $idOder->fetch_all();
        // Определяем последнию запись Заказа в таблице 'deliveryusers' и записываем в переменную для вывода в Браузер
        $id = $mySqli->query('SELECT id FROM deliveryusers ORDER BY id DESC LIMIT 0 , 1');
        $userId = mysqli_fetch_row($id);
        $userIdResult = $userId[0]; // Записываем в переменную Id Заказ текущего Клиента для вывода в Браузер
// перебераем массив на получение Id Заказа, Адресс и Коментарий из таблицы 'deliveryusers' и выводим в браузере
        for ($k = 0; $k < count($idOderRes); $k++) {
            if ($userId[0] == $idOderRes[$k][0]) { // проверка на совпадение Id существующего клиента
                $idOderResult = $idOderRes[$k][0];
                $addressUser = $idOderRes[$k][2];
                $userComment = $idOderRes[$k][3];
            }
        }
        // Подсчет количество Заказов
        $userOderSql = $mySqli->query("SELECT COUNT(*) as count FROM deliveryusers WHERE user_id = '$idUser'");
        $userOder = mysqli_fetch_array($userOderSql);
        $countOder = $userOder[0]; // Количество Заказов

        $yourNumberOder = "Ваш номер Заказа: $idOderResult <BR>";
        $deliveryInAddress = "<BR>Будет доставлен по адресу: $addressUser<BR>";
        $comment = "Коментарии к Заказу: $userComment <BR>";
        $thx = "<BR>Спасибо за покупку!<BR> Это Ваша $countOder покупка!<BR>";
        $mail = $yourNumberOder . $deliveryInAddress . $comment . $thx . $time;
        $dir = "./mailDelivery/";
        file_put_contents($dir."$timeResult.txt", $mail);
        echo $mail;
        // Отправка или запись в файл

    } else { // Регестрируем Нового Клиента
        $sql = $mySqli->query("INSERT INTO users VALUES (NULL, '$userName', '$userPhone', '$userMail')");
// Выделяем ID Нового Клиента из таблицы users и записываем в переменную $userIdResult
        $id = $mySqli->query('SELECT id FROM users ORDER BY id DESC LIMIT 0 , 1');
        $userId = mysqli_fetch_row($id);
        $userIdResult = $userId[0];
// Добовляем Адресс в Таблицу deliveryusers и присваеваем ID Нового Клиента из Таблицы users
        $delUs = $mySqli->query("INSERT INTO deliveryusers VALUES (NULL, '$userIdResult', '$address', '$userComment')");
        // Делаем запрос для получения Id Заказа, Адресс и Коментарии в таблице 'deliveryusers' и выводим в браузере
        $idOder = $mySqli->query('select * from deliveryusers');
        $idOderRes1 = $idOder->fetch_all();
        for ($l = 0; $l < count($idOderRes1); $l++) {
            if ($userId[0] == $idOderRes1[$l][1]) { // проверка на совпадение нового клиента
                $idOderResult1 = $idOderRes1[$l][0];
                $addressUser1 = $idOderRes1[$l][2];
                $userComment1 = $idOderRes1[$l][3];
            }
        }
        $yourNumberOder1 = "Ваш номер Заказа: $idOderResult1 <BR>";
        $deliveryInAddress1 = "<BR>Будет доставлен по адресу: $addressUser1<BR>";
        $comment1 = "Коментарии к Заказу: $userComment1 <BR>";
        $thx1 = "<BR>Спасибо за Заказ, это Ваша первая покупка<BR>";
        $mail = $yourNumberOder1 . $deliveryInAddress1 . $comment1 . $thx1 . $time;
        $dir = "./mailDelivery/";
        file_put_contents($dir."$timeResult.txt", $mail);
        echo $mail;
    }
} else {
    echo "Ведите пожалуйста: e-mail, телефон и свое имя, а также не забудьте адрес";
    echo '<a href="index.html#6"><BR>Назад на главную страницу </a>';
}
