<?php
function getConnect()
{
    $mySqli = new mysqli('localhost', 'root', '', 'bdvp1');
    return $mySqli;
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
    $data = func_get_args();
    $userName = $data[0]['Name'];
    $userPhone = $data[0]['Phone'];
    $userMail = $data[0]['Mail'];
    $choiceResult = getConnect()->query('select * from users');
    $dataMail = $choiceResult->fetch_all();
    for ($i = 0; $i < count($dataMail); $i++) {
        if ($dataMail[$i][3] == $userMail) {
            $result = $dataMail[$i][3];
        }
    }
    if ($result != $userMail) {
        getConnect()->query("INSERT INTO users VALUES (NULL, '$userName', '$userPhone', '$userMail')");
    }
}

function addOrder()
{
    $data = func_get_args();
    $userMail = $data[0]['Mail'];
    $address = $data[0]['Address'];
    $userComment = $data[0]['Comment'];
    $choice = getConnect()->query('select * from users');
    $dataMail = $choice->fetch_all();
    for ($i = 0; $i < count($dataMail); $i++) {
        if ($dataMail[$i][3] == $userMail) {
            $idUser = $dataMail[$i][0];
        }
    }
    getConnect()->query("INSERT INTO deliveryusers VALUES (NULL, '$idUser', '$address', '$userComment')");
}

function sendMail()
{
    $data = func_get_args();
    $userMail = $data[0]['Mail'];
    $userAddress = $data[0]['Address'];
    $userComment = $data[0]['Comment'];
    $id = getConnect()->query('SELECT id FROM deliveryusers ORDER BY id DESC LIMIT 0 , 1');
    $idOrder = mysqli_fetch_row($id);
    $idOrderResult = $idOrder[0];
    $time = date('d.m.Y h:i');

    $choice = getConnect()->query('select * from users');
    $dataMail = $choice->fetch_all();
    for ($i = 0; $i < count($dataMail); $i++) {
        if ($dataMail[$i][3] == $userMail) {
            $idUser = $dataMail[$i][0];
        }
    }
    $userOderSql = getConnect()->query("SELECT COUNT(*) as count FROM deliveryusers WHERE user_id = '$idUser'");
    $userOder = mysqli_fetch_array($userOderSql);
    $countOder = $userOder[0];

    if ($countOder > 1) {
        $thx = "<BR>Спасибо за покупку!<BR> Это Ваша $countOder покупка!<BR>";
    } else {
        $thx = "<BR>Спасибо за Заказ, это Ваша первая покупка<BR>";
    }

    $yourNumberOder1 = "Ваш номер Заказа: $idOrderResult <BR>";
    $deliveryInAddress1 = "<BR>Будет доставлен по адресу: $userAddress<BR>";
    $comment1 = "Коментарии к Заказу: $userComment <BR>";

    $mail = $yourNumberOder1 . $deliveryInAddress1 . $comment1 . $thx . $time;
    $dir = "./mailDelivery/";
    file_put_contents($dir . "$idOrderResult.txt", $mail);
    return $mail;

}

function error()
{
    $fail = "Ведите пожалуйста: e-mail, телефон и свое имя, а также не забудьте адрес <BR>";
    $fail .= '<a href="index.html#6"><BR>Назад на главную страницу </a>';
    echo $fail;
}

function main()
{
//    getConnect();
    $users = getUser();
    if (($users)) {
        addUser(getUser());
        addOrder(getUser());
        sendMail(getUser());
    } else {
        error();
    }
}

echo '<PRE>';
print_r(main());
die();
