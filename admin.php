<?php
$mysql2 = new mysqli('localhost', 'root', '', 'bdvp1');

$cho = 'select * from users';
$table1 = $mysql2->query($cho);
$choicedelivery = 'select * from deliveryusers';
$table2 = $mysql2->query($choicedelivery);

if ($table1->num_rows) {
    $dataUser = $table1->fetch_all();
}
if ($table2->num_rows) {
    $dataDelivery = $table2->fetch_all();
};

if (isset($dataUser)) {
    $table = "<table align='left' border='1'>";
    $table .= "<tr><td>" . "Клиенты: <BR><BR>";
    for ($i = 0; $i < count($dataUser); $i++) {
        if ($dataUser[$i][0]) {
            $result .= "Id: " . $dataUser[$i][0] . "<BR>";
        }
        if ($dataUser[$i][1]) {
            $result .= "Имя: " . $dataUser[$i][1] . "<BR>";
        }
        if ($dataUser[$i][2]) {
            $result .= "Телефон: " . $dataUser[$i][2] . "<BR>";
        }
        if ($dataUser[$i][2]) {
            $result .= "E-mail: " . $dataUser[$i][2] . "<BR>";
        }
        $result .= "<BR>";
    }
    $table .= $result;
    $table .= "</tr></td></table>";
    echo $table;
} else {
    echo "База Пуста!";
}

if (isset($dataDelivery)) {
    $table1 = "<table float='left' border='1'>";
    $table1 .= "<tr><td>" . "Доставка: <BR>";
    for ($i = 0; $i < count($dataDelivery); $i++) {
        if ($dataDelivery[$i][0]) {
            $result1 .= "Номер заказа: " . $dataDelivery[$i][0] . "<BR>";
        }
        if ($dataDelivery[$i][1]) {
            $result1 .= "Id Клиента: " . $dataDelivery[$i][1] . "<BR>";
        }
        if ($dataDelivery[$i][2]) {
            $result1 .= "Адрес доставки: " . $dataDelivery[$i][2] . "<BR>";
        }
        if ($dataDelivery[$i][3]) {
            $result1 .= "Комментарии: " . $dataDelivery[$i][3] . "<BR><BR>";
        }
    }

    $table1 .= $result1;
    $table1 .= "</tr></td></table>";
    echo $table1;
} else {
    echo "База пуста!";
}








//$writeJason = json_encode($array, JSON_UNESCAPED_UNICODE);
//                    file_put_contents('output2.json', $writeJason);