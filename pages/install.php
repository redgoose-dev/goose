<?
$root = preg_replace('/\/index.php$/', '', $_SERVER['PHP_SELF']);
?>

<!doctype html>
<html lang="ko">
<head>
<meta charset="UTF-8" />
<title>Install Goose</title>
<link rel="stylesheet" href="<?=$root?>/pages/src/css/install.css" />
</head>
<body>
<main>
	<header>
		<h1>Install Goose</h1>
	</header>
	<form action="./" method="post" id="regsterForm">
		<fieldset>
			<legend>데이터베이스 정보</legend>
			<label class="first">
				<strong>DB 아이디</strong>
				<span><input type="text" name="dbId" id="dbId" size="15" maxlength="20" /></span>
			</label>
			<label>
				<strong>DB 비밀번호</strong>
				<span><input type="password" name="dbPassword" size="15" id="dbPassword" maxlength="20" /></span>
			</label>
			<label>
				<strong>DB 비밀번호 확인</strong>
				<span><input type="password" name="dbPassword2" size="15" id="dbPassword2" maxlength="20" /></span>
			</label>
			<label>
				<strong>DB 이름</strong>
				<span><input type="text" name="dbName" id="dbName" size="15" maxlength="20" /></span>
			</label>
			<hr />
			<label>
				<strong>DB 호스트이름</strong>
				<span><input type="text" name="dbHost" id="dbHost" size="12" maxlength="40" value="localhost" /></span>
			</label>
			<label>
				<strong>테이블이름 prefix</strong>
				<span><input type="text" name="dbPrefix" id="dbPrefix" size="12" maxlength="20" value="GOOSE_" /></span>
			</label>
		</fieldset>

		<fieldset>
			<legend>관리자 정보</legend>
			<label class="first">
				<strong>이메일</strong>
				<span><input type="email" name="email" id="email" size="28" maxlength="40" /></span>
			</label>
			<label>
				<strong>닉네임</strong>
				<span><input type="text" name="name" id="name" size="18" maxlength="20" /></span>
			</label>
			<label>
				<strong>비밀번호</strong>
				<span><input type="password" name="password" id="password" size="15" maxlength="20" /></span>
			</label>
			<label>
				<strong>비밀번호 확인</strong>
				<span><input type="password" name="password2" id="password2" size="15" maxlength="20" /></span>
			</label>
		</fieldset>

		<fieldset>
			<legend>API</legend>
			<label class="first">
				<strong>API KEY prefix</strong>
				<span><input type="text" name="apiPrefix" id="apiPrefix" size="28" maxlength="20" value="__@_goOsE_*__" /></span>
			</label>
		</fieldset>
		<nav>
			<button type="submit">설치하기</button>
		</nav>
	</form>
</main>

<script src="<?=$root?>/libs/ext/jQuery/jquery-1.11.1.min.js"></script>
<script src="<?=$root?>/libs/ext/validation/jquery.validate.min.js"></script>
<script src="<?=$root?>/libs/ext/validation/localization/messages_ko.js"></script>
<script>
jQuery(function($){
	$('#regsterForm').validate({
		rules : {
			dbId : { required: true, minlength: 3 }
			,dbPassword : { required: true }
			,dbPassword2 : { equalTo: '#dbPassword' }
			,dbName : { required: true }
			,dbHost : { required: true }
			,dbPrefix : { required: true }
			,email : { required: true }
			,name : { required: true }
			,password : { required: true }
			,password2 : { equalTo: '#password' }
			,apiPrefix : { required: true }
		}
	});
});
</script>
</body>
</html>