<?php
function getConnect()
{
    $dsn = 'mysql:host = localhost;dbname=bdvp1;charset=utf8';
    $connection = new PDO($dsn, 'root', '');
    return $connection;
}


function getUser()
{
    $userName = $_GET['name'];
    $userPhone = $_GET['phone'];
    $userMail = $_GET['email'];
    $userStreet = $_GET['street'];
    $userHome = $_GET['home'];
    $userPart = $_GET['part'];
    $userappt = $_GET['appt'];
    $userFloor = $_GET['floor'];
    $userComment = $_GET['comment'];
    $address = "Улица: " . $userStreet . "<BR>"
        . "Дом № " . $userHome . "<BR>"
        . "Корпус № " . $userPart . "<BR>"
        . "Квартира № " . $userappt . "<BR>"
        . "Этаж № " . $userFloor;
    if (!empty($userName) && !empty($userPhone) && !empty($userMail)) {
        $date = [
            'Name' => $userName,
            'Phone' => $userPhone,
            'Mail' => $userMail,
            'Address' => $address,
            'Comment' => $userComment];
        return $date;
    } else {
        return false;
    }
}

function addUser()
{
    $date = getUser();
    $userName = $date['Name'];
    $userPhone = $date['Phone'];
    $userMail = $date['Mail'];
    $choiceResult = getConnect()->prepare('select * from users');
    $choiceResult->execute();
    $dataMail = $choiceResult->fetchall(PDO::FETCH_ASSOC);

    for ($i = 0; $i < count($dataMail); $i++) {
        if ($dataMail[$i]['Mail'] == $userMail) {
            $result = $dataMail[$i]['Mail'];
        }
    }
    if ($result != $userMail) {
        getConnect()->query("INSERT INTO users VALUES (NULL, '$userName', '$userPhone', '$userMail')");
    }
}

function addOrder()
{
    $date = getUser();
    $userMail = $date['Mail'];
    $address = $date['Address'];
    $userComment = $date['Comment'];
    $idUser = checkIdOrder($userMail);
    getConnect()->query("INSERT INTO deliveryusers VALUES (NULL, '$idUser', '$address', '$userComment')");
}

function sendMail()
{
    $date = getUser();
    $userMail = $date['Mail'];
    $address = $date['Address'];
    $userComment = $date['Comment'];
    $id = getConnect()->prepare('SELECT id FROM deliveryusers ORDER BY id DESC LIMIT 0 , 1');
    $id->execute([0]);
    $idOrder = $id->fetchColumn();
    $time = date('d.m.Y h:i');
    $idUser = checkIdOrder($userMail);
    $userOderSql = getConnect()->prepare("SELECT COUNT(*) as count FROM deliveryusers WHERE user_id = '$idUser'");
    $userOderSql->execute([0]);
    $countOder = $userOderSql->fetchColumn();

    if ($countOder > 1) {
        $thx = "<BR>Спасибо за покупку!<BR>Это Ваша $countOder покупка!<BR>";
    } else {
        $thx = "<BR>Спасибо за Заказ, это Ваша первая покупка<BR>";
    }

    $yourNumberOder1 = "Ваш номер Заказа: $idOrder <BR>";
    $deliveryInAddress1 = "<BR>Будет доставлен по адресу: $address<BR>";
    $comment1 = "Коментарии к Заказу: $userComment <BR>";

    $mail = $yourNumberOder1 . $deliveryInAddress1 . $comment1 . $thx . $time;

    $dir = "./mailDelivery/";
    file_put_contents($dir . "$idOrder.txt", $mail);
    return $mail;
}

function checkIdOrder($userMail)
{
    // Функция проверяет схожесть на email Клиента
    // и возвращает ID клиента
    $choice = getConnect()->prepare('select * from users');
    $choice->execute();
    $dataMail = $choice->fetchall(PDO::FETCH_ASSOC);

    for ($i = 0; $i < count($dataMail); $i++) {
        if ($dataMail[$i]['Mail'] == $userMail) {
            $idUser = $dataMail[$i]['id'];
        }
    }
    return $idUser;
}

function error()
{
    $fail = "Ведите пожалуйста: e-mail, телефон и свое имя, а также не забудьте адрес <BR>";
    $fail .= '<a href="index.html#6"><BR>Назад на главную страницу </a>';
    echo $fail;
}

function main()
{
    $users = getUser();
    if (($users)) {
        addUser();
        addOrder();
        echo sendMail();
    } else {
        error();
    }
}

main();
