<!doctype html>
<html lang="ko">
<head>
<meta charset="UTF-8" />
<title>Install Goose</title>
</head>
<body>
<main>
	<header>
		<h1>Install Goose</h1>
	</header>
	<form action="<?=ROOT?>/install/" method="post">
		<fieldset>
			<legend>데이터베이스 정보</legend>
			<label>
				<strong>DB 아이디</strong>
				<span><input type="text" name="dbId" maxlength="20" /></span>
			</label>
			<label>
				<strong>DB 비밀번호</strong>
				<span><input type="password" name="dbPassword" maxlength="20" /></span>
			</label>
			<label>
				<strong>DB 비밀번호 확인</strong>
				<span><input type="password" name="dbPassword2" maxlength="20" /></span>
			</label>
			<label>
				<strong>DB 이름</strong>
				<span><input type="text" name="dbName" maxlength="20" /></span>
			</label>
			<hr />
			<label>
				<strong>DB 호스트이름</strong>
				<span><input type="text" name="dbHost" maxlength="40" value="localhost" /></span>
			</label>
			<label>
				<strong>테이블이름 prefix</strong>
				<span><input type="text" name="dbPrefix" maxlength="20" value="GOOSE_" /></span>
			</label>
		</fieldset>
		<fieldset>
			<legend>관리자 정보</legend>
			<label>
				<strong>이메일</strong>
				<span><input type="email" name="email" maxlength="40" /></span>
			</label>
			<label>
				<strong>이름</strong>
				<span><input type="text" name="name" maxlength="20" /></span>
			</label>
			<label>
				<strong>비밀번호</strong>
				<span><input type="password" name="password" maxlength="20" /></span>
			</label>
			<label>
				<strong>비밀번호 확인</strong>
				<span><input type="password" name="password2" maxlength="20" /></span>
			</label>
		</fieldset>
		<fieldset>
			<legend>API</legend>
			<label>
				<strong>API KEY prefix</strong>
				<span><input type="text" name="apiPrefix" maxlength="20" value="__@_goOsE_*__" /></span>
			</label>
		</fieldset>
		<nav>
			<button type="submit">설치하기</button>
		</nav>
	</form>
</main>
</body>
</html>