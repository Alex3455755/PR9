<?php
	include("ajax/check_token.php");

	if(isset($_COOKIE['JWT'])) {
	    $data = verifyJWT($_COOKIE['JWT']);

	    if($data) {
	        if($data['userRole'] == 0) {
	            header("Location: user.php");
	            exit;
	        } else if($data['userRole'] == 1) {
	            header("Location: admin.php");
	            exit;
	        }
	    }
	}
?>
<html>
	<head> 
		<meta charset="utf-8">
		<title> Авторизация </title>
		
		<script src="https://code.jquery.com/jquery-1.8.3.js"></script>
		<link rel="stylesheet" href="style.css">
	</head>
	<body>
		<div class="top-menu">
			<a href=#><img src = "img/logo1.png"/></a>
			<div class="name">
				<a href="index.php">
					<div class="subname">БЗОПАСНОСТЬ  ВЕБ-ПРИЛОЖЕНИЙ</div>
					Пермский авиационный техникум им. А. Д. Швецова
				</a>
			</div>
		</div>
		<div class="space"> </div>
		<div class="main">
			<div class="content">
				<div class = "login">
					<div class="name">Авторизация</div>
				
					<div class = "sub-name">Логин:</div>
					<input name="_login" type="text" placeholder="" onkeypress="return PressToEnter(event)"/>
					<div class = "sub-name">Пароль:</div>
					<input name="_password" type="password" placeholder="" onkeypress="return PressToEnter(event)"/>
					
					<a href="regin.php">Регистрация</a>
					<br><a href="recovery.php">Забыли пароль?</a>
					<input type="button" class="button" value="Войти" onclick="LogIn()"/>
					<img src = "img/loading.gif" class="loading"/>
				</div>
				
				<div class="footer">
					© КГАПОУ "Авиатехникум", 2020
					<a href=#>Конфиденциальность</a>
					<a href=#>Условия</a>
				</div>
			</div>
		</div>
		
		<script>
			function LogIn() {
				var loading = document.getElementsByClassName("loading")[0];
				var button = document.getElementsByClassName("button")[0];
				
				var _login = document.getElementsByName("_login")[0].value;
				var _password = document.getElementsByName("_password")[0].value;
				loading.style.display = "block";
				button.className = "button_diactive";
				
				var data = new FormData();
				data.append("login", _login);
				data.append("password", _password);


				fetchJWT();

				console.log(document.cookie);
				// AJAX запрос
				$.ajax({
					url         : 'ajax/login_user.php',
					type        : 'POST', // важно!
					data        : data,
					cache       : false,
					dataType    : 'html',
					// отключаем обработку передаваемых данных, пусть передаются как есть
					processData : false,
					// отключаем установку заголовка типа запроса. Так jQuery скажет серверу что это строковой запрос
					contentType : false, 
					// функция успешного ответа сервера
					success: function (_data) {
						console.log("Авторизация прошла успешно, id: " +_data);
						if(_data == "") {
							loading.style.display = "none";
							button.className = "button";
							alert("Логин или пароль не верный.");
						} else {
							localStorage.setItem("token", _data);
							location.reload();
							loading.style.display = "none";
							button.className = "button";
						}
					},
					// функция ошибки
					error: function( ){
						console.log('Системная ошибка!');
						loading.style.display = "none";
						button.className = "button";
					}
				});
			}
			
			function PressToEnter(e) {
				if (e.keyCode == 13) {
					var _login = document.getElementsByName("_login")[0].value;
					var _password = document.getElementsByName("_password")[0].value;
					
					if(_password != "") {
						if(_login != "") {
							LogIn();
						}
					}
				}
			}

			function fetchJWT() {
    var _login = document.getElementsByName("_login")[0].value;
    var _password = document.getElementsByName("_password")[0].value;

    var data = new FormData();
    data.append("login", _login);
    data.append("password", _password);
    
    $.ajax({
        url: 'auth.permaviat.ru',
        type: 'POST',
        data: data,
        cache: false,
        processData: false,
        contentType: false,
        beforeSend: function(xhr) {
            // Отправляем Basic Auth с теми же логином и паролем
            var token = _login + ":" + _password;
            var hash = btoa(token);
            xhr.setRequestHeader("Authorization", "Basic " + hash);
        },
        success: function(response, textStatus, xhr) {
            
            // Проверяем, что ответ не пустой
            if (xhr.getResponseHeader("token") === "") {
                alert("Логин или пароль не верный.");
                return;
            }
            
            // Если ответ пришел как строка (JWT токен)
            if (response === "" || response === null || response === undefined) {
                // Проверяем, не является ли ответ HTML ошибкой
                if (response.includes("<br") || response.includes("<b>Warning")) {
                    console.error("Сервер вернул HTML ошибку:", response);
                    alert("Ошибка на сервере. Проверьте консоль.");
                    return;
                }
                
                // Сохраняем токен
				document.cookie = `JWT= ${xhr.getResponseHeader("token")}; path=/`
                
                // Перенаправление или обновление страницы
                // window.location.href = "dashboard.html";
            } else {
                console.error("Неожиданный формат ответа:", response);
                alert("Ошибка авторизации");
            }
        },
        error: function(xhr, status, error) {
            console.log('Ошибка AJAX:');
            console.log('Статус:', status);
            console.log('HTTP статус:', xhr.status);
            console.log('Ответ:', xhr.responseText);
            
            if (xhr.status === 401) {
                alert("Логин или пароль не верный.");
            } else {
                alert("Ошибка соединения с сервером (код: " + xhr.status + ")");
            }
        }
    });
}
			
		</script>
	</body>
</html>