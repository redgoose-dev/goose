goose
=====

개인용 CMS Goose입니다.
거위라는 프로그램의 사용 용도는 글이나 사진이 담긴 포스트를 관리하는 프로그램이라고 볼 수 있습니다. 대략적으로 작은 워드프레스로 생각해도 되겠습니다.
주요 포스트 데이터는 둥지(Nest)라는 장소에서 관리하고 그 데이터들은 다른 공간에서 불러서 사용할 수 있습니다.


##설치환경
----------
Goose 프로그램을 설치할 수 있는 환경은 XE나 워드프레스를 설치할 수 있는 아래 조건을 충족하는 환경이라면 쉽게 설치할 수 있습니다.

* Apache 2.0
* PHP 5.3
* MYSQL 5.5

##설치
------
###AMP(Apache, MySQL, PHP) 설치
####Ubuntu
* Apache
  - `sudo apt-get install apache2`
  - 웹브라우져 주소란에 `http://127.0.0.1:80` 접속 후, It works! 메세지가 나오면 정상
* MySQL 
  - `sudo apt-get install mysql-server mysql-client`
  - 설치 중, MySQL의 root 계정의 패스워드를 설정하게된다.
  - `sudo apt-get install libapache2-mod-auth-mysql` MySQL 인증 모듈 설치
  - `sudo /usr/bin/mysql_secure_installation` 기본 보안 정책 설정 
    * 패스워드와 간단한 질문. 
* PHP
  - `sudo apt-get install php5`
  - `sudo apt-get install libapache2-mod-php5` 아파치와 PHP 연동 모듈 설치
  - `sudo apt-get install php5-mysql` MySQL과 PHP 연동 모듈 설치
  - `sudo apt-get install php5-mcrypt` 암호화 모듈 설치
  - `sudo apt-get install phpmyadmin` MySQL 데이터베이스 웹형 관리 서비스 설치


- 파일을 받아서 서버에 압축풉니다. `/www/goose/`
- 웹브라우저에서 goose파일을 설치한곳을 주소로 적어서 페이지 이동합니다. `http://domainaddress.com/goose/`
- **Install Goose**설치화면이 나옵니다.
- db정보와 관리자 정보를 입력합니다. 관리자 정보 섹션에서 **이메일**과 **비밀번호**는 로그인에 사용됩니다.
- 설치하기 버튼을 누르면  alert창에서 "Complete install"라는 메세지가 나오면서 설치완료됩니다.
- 로그인화면으로 이동하는데 아까 적었던 관리자 이메일주소와 비밀번호를 입력합니다.
- 이제 씁니다.