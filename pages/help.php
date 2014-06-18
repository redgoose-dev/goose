<section class="Documents">
	<div class="hgroup">
		<h1>도움말</h1>
	</div>

	<section class="first" id="HelpFirst">
		<h1>About Goose</h1>
		<p>
			Goose는 mysql 데이터베이스와 연계하여 싱글유저 사이트를 지원하는 데이터 관리 프로그램입니다.<br/>
			많은 기능보다 심플한 형태의 프로그램을 추구하기 때문에 많은 기능을 보유하지 않고 개인 블로그, 갤러리, 포트폴리오 사이트에 필요한 데이터를 제공하는 기능만 갖추고 있습니다. 그래서 회원관리나 그밖의 기능은 넣지않을 계획입니다.<br/>
		</p>
		<p>Goose에서 제공하는 컨텐츠는 다음과 같습니다.</p>
		<ul>
			<li>
				<strong>nests</strong><br/>
				Goose 관리 프로그램의 핵심적인 요소입니다. 글이나 사진을 올릴 수 있거나 수정, 삭제할 수 있습니다. 이 데이터를 통하여 외부 프로그램에서 출력할 수 있습니다.
			</li>
			<li>
				<strong>JSON</strong><br/>
				다목적으로 사용할 수 있는 json 데이터를 관리합니다.
			</li>
			<li>
				<strong>API</strong><br/>
				nests 컨텐츠에 있는 데이터를 외부 어플리케이션에서 가져올 수 있게 지원할 수 있는 도구를 제공합니다.
			</li>
		</ul>
	</section>

	<section id="HelpInstall">
		<h1>Install</h1>
		<p>
			Goose 프로그램은 별도의 인스톨 프로그램은 없습니다. 그래서 DB세팅과 파일설치를 수동으로 해야합니다.<br/>
			인스톨에 관한 자세한 내용은 이 화면을 보기전의 단계이기 때문에 Goose 프로그램 경로안에 install.txt파일을 참고해주세요.
		</p>
	</section>
	
	<section id="HelpAdmin">
		<h1>Admin</h1>
		<p>
			Goose 프로그램 관리 프로그램을 통해서 홈페이지에 사용되는 데이터를 관리할 수 있는 공간입니다. 이 데이터를 사용하여 외부 웹 프로그램에서 데이터를 출력할 수 있습니다.<br/>
			이 프로그램 관리자는 다음과 같은 주요 메뉴가 있습니다.
		</p>
		<ul>
			<li>
				<strong>nests</strong><br/>
				이 메뉴는 사이트 앱의 데이터를 보관하고 관리하는 공간으로 하나의 게시물이나 포스트의 집합이라고 할 수 있습니다.<br/>
				nestGroup > nest > article 구조로 구성되어 있으며 article 테이블이 포스팅 데이터가 들어갑니다.
			</li>
			<li>
				<strong>JSON</strong><br/>
				다목적으로 사용할 수 있는 json데이터를 만들어서 db에 저장하여 관리하는 공간입니다.
			</li>
			<li>
				<strong>API</strong><br/>
				goose 프로그램은 외부 프로그램을 위한 api를 제공합니다. 자세한 내용은 <a href="<?=ROOT?>/api/">Goose API 안내</a>에서 참고하세요.
			</li>
		</ul>
	</section>

	<section id="HelpDatabase">
		<h1>Goose Database</h1>
		<p>
			이 프로그램은 mysql 데이터베이스를 사용하고 있습니다.<br/>
			db 테이블 이름변경이 생길 수 있기 때문에 이름 관리를 /goose/config/variable.php 파일의 $tablesName변수에서 관리하고 있습니다.<br/>
			api를 활용하기 위하여 꼭 참고해야하는 부분입니다.<br/>
			Goose 프로그램의 db 테이블의 자세한 설명은 다음과 같습니다.
		</p>
		<section>
			<h1>articles</h1>
			<p>포스팅 내용이 담겨있는 가장 중요하고 데이터가 많이 쌓이는 테이블입니다.</p>
			<table class="ui-table">
				<caption>article 테이블 필드목록</caption>
				<thead>
					<tr>
						<th scope="col">field</th>
						<th scope="col">type</th>
						<th scope="col">info</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>srl</th>
						<td class="center nowrap">number</td>
						<td>고유번호</td>
					</tr>
					<tr>
						<th>group_srl</th>
						<td class="center nowrap">number</td>
						<td>nestGroups 테이블 고유번호</td>
					</tr>
					<tr>
						<th>nest_srl</th>
						<td class="center nowrap">number</td>
						<td>nests 테이블 고유번호</td>
					</tr>
					<tr>
						<th>category_srl</th>
						<td class="center nowrap">number</td>
						<td>categories 테이블 고유번호</td>
					</tr>
					<tr>
						<th>thumnail_srl</th>
						<td class="center nowrap">number</td>
						<td>썸네일 이미지가 되는 files 테이블 srl 필드값</td>
					</tr>
					<tr>
						<th>title</th>
						<td class="center nowrap">string</td>
						<td>제목</td>
					</tr>
					<tr>
						<th>content</th>
						<td class="center nowrap">string</td>
						<td>내용</td>
					</tr>
					<tr>
						<th>thumnail_url</th>
						<td class="center nowrap">string</td>
						<td>썸네일 이미지 경로</td>
					</tr>
					<tr>
						<th>thumnail_coords</th>
						<td class="center nowrap">string</td>
						<td>원본 이미지에서 썸네일 이미지 크롭할때의 주소 좌표값</td>
					</tr>
					<tr>
						<th>regdate</th>
						<td class="center nowrap">number</td>
						<td>등록일</td>
					</tr>
					<tr>
						<th>update</th>
						<td class="center nowrap">number</td>
						<td>수정일</td>
					</tr>
					<tr>
						<th>ipAddress</th>
						<td class="center nowrap">string</td>
						<td>등록자 ip address</td>
					</tr>
				</tbody>
			</table>
		</section>
		<section>
			<h1>categories</h1>
			<p>categories에 대한 분류이름들을 저장하는 테이블입니다.</p>
			<table class="ui-table">
				<caption>article 테이블 필드목록</caption>
				<thead>
					<tr>
						<th scope="col">field</th>
						<th scope="col">type</th>
						<th scope="col">info</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>srl</th>
						<td class="center nowrap">number</td>
						<td>고유번호</td>
					</tr>
					<tr>
						<th>nest_srl</th>
						<td class="center nowrap">number</td>
						<td>nests 테이블 고유번호</td>
					</tr>
					<tr>
						<th>turn</th>
						<td class="center nowrap">number</td>
						<td>순서</td>
					</tr>
					<tr>
						<th>name</th>
						<td class="center nowrap">string</td>
						<td>분류이름</td>
					</tr>
					<tr>
						<th>regdate</th>
						<td class="center nowrap">number</td>
						<td>등록날짜</td>
					</tr>
				</tbody>
			</table>
		</section>
		<section>
			<h1>files</h1>
			<p>첨부파일들의 정보를 담아둔 테이블입니다.</p>
			<table class="ui-table">
				<caption>files 테이블 필드목록</caption>
				<thead>
					<tr>
						<th scope="col">field</th>
						<th scope="col">type</th>
						<th scope="col">info</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>srl</th>
						<td class="center nowrap">number</td>
						<td>고유번호</td>
					</tr>
					<tr>
						<th>article_srl</th>
						<td class="center nowrap">number</td>
						<td>articles 테이블 고유번호</td>
					</tr>
					<tr>
						<th>name</th>
						<td class="center nowrap">string</td>
						<td>파일이름</td>
					</tr>
					<tr>
						<th>loc</th>
						<td class="center nowrap">string</td>
						<td>저장된 파일경로</td>
					</tr>
				</tbody>
			</table>
		</section>
		<section>
			<h1>nestGroups</h1>
			<p>
				둥지의 그룹정보를 담아둔 테이블입니다.<br/>
				이 테이블을 만든 이유는 여러 둥지를 사용하는 앱을 만들때 묶어줄 수 있는 장치가 필요했기 때문입니다.
			</p>
			<table class="ui-table">
				<caption>nestGroups 테이블 필드목록</caption>
				<thead>
					<tr>
						<th scope="col">field</th>
						<th scope="col">type</th>
						<th scope="col">info</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>srl</th>
						<td class="center nowrap">number</td>
						<td>고유번호</td>
					</tr>
					<tr>
						<th>name</th>
						<td class="center nowrap">string</td>
						<td>이름</td>
					</tr>
					<tr>
						<th>regdate</th>
						<td class="center nowrap">number</td>
						<td>등록날짜</td>
					</tr>
				</tbody>
			</table>
		</section>
		<section>
			<h1>nests</h1>
			<p>article 데이터의 그룹이 되는 테이블입니다. 이 테이블은 썸네일 이미지 사이즈나 출력갯수, 분류나 확장변수를 사용할것인지의 여부를 저장하는 테이블이 됩니다.</p>
			<table class="ui-table">
				<caption>nests 테이블 필드목록</caption>
				<thead>
					<tr>
						<th scope="col">field</th>
						<th scope="col">type</th>
						<th scope="col">info</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>srl</th>
						<td class="center nowrap">number</td>
						<td>고유번호</td>
					</tr>
					<tr>
						<th>group_srl</th>
						<td class="center nowrap">number</td>
						<td>group 테이블 고유번호</td>
					</tr>
					<tr>
						<th>id</th>
						<td class="center nowrap">string</td>
						<td>문자 형식으로 된 고유 ID값</td>
					</tr>
					<tr>
						<th>name</th>
						<td class="center nowrap">string</td>
						<td>이름</td>
					</tr>
					<tr>
						<th>thumnailSize</th>
						<td class="center nowrap">string</td>
						<td>섬네일 이미지 사이즈 (예:100*100)</td>
					</tr>
					<tr>
						<th>thumnailType</th>
						<td class="center nowrap">string</td>
						<td>
							섬네일 이미지로 축소하는 방식<br/>
							crop, resizeWidth, resizeHeight
						</td>
					</tr>
					<tr>
						<th>listCount</th>
						<td class="center nowrap">number</td>
						<td>페이지당 출력갯수</td>
					</tr>
					<tr>
						<th>useCategory</th>
						<td class="center nowrap">number</td>
						<td>분류기능을 사용할지에 대한 여부 (1|0)</td>
					</tr>
					<tr>
						<th>useExtraVar</th>
						<td class="center nowrap">number</td>
						<td>확장변수 기능을 사용할지에 대한 여부 (1|0)</td>
					</tr>
					<tr>
						<th>regdate</th>
						<td class="center nowrap">number</td>
						<td>등록날짜</td>
					</tr>
				</tbody>
			</table>
		</section>
		<section>
			<h1>jsons</h1>
			<p>다목적으로 사용할 수 있는 json데이터를 관리하는 테이블입니다.</p>
			<table class="ui-table">
				<caption>jsons 테이블 필드목록</caption>
				<thead>
					<tr>
						<th scope="col">field</th>
						<th scope="col">type</th>
						<th scope="col">info</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>srl</th>
						<td class="center nowrap">number</td>
						<td>고유번호</td>
					</tr>
					<tr>
						<th>name</th>
						<td class="center nowrap">string</td>
						<td>이름</td>
					</tr>
					<tr>
						<th>json</th>
						<td class="center nowrap">string</td>
						<td>json 데이터</td>
					</tr>
					<tr>
						<th>regdate</th>
						<td class="center nowrap">number</td>
						<td>등록날짜</td>
					</tr>
				</tbody>
			</table>
		</section>
	</section>

	<section id="HelpClasses">
		<h1>Goose에서 제공하는 Classes</h1>
		<p>Goose 프로그램은 관리자에서 사용하는 몇가지 유용한 클래스들을 사용할 수 있습니다.</p>
		<p>클래스 경로 : /goose/libs/ClassName.class.php</p>
		<section>
			<h1>Util.class.php</h1>
			<p>사이트를 만들때 유용한 작은 메서드들을 모았습니다. Util 클래스 초기화 설정과 예제는 아래 소스를 확인하세요.</p>
<pre>
require_once('/goose/libs/Util.class.php');
$util = new Util();

// print console.log
$util->console("Hello world");
</pre>
			<table class="ui-table">
				<caption>Util 클래스 메서드 기능설명</caption>
				<thead>
					<tr>
						<th scope="col">메서드</th>
						<th scope="col">설명</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>console</th>
						<td>
<pre style="margin-top:0">
$util->console("Hello world");
$util->console(array(1,2,3));
</pre>
							자바스크립트 console.log() 메서드를 사용하여 브라우저 개발자 도구 콘솔에 값을 출력합니다.
						</td>
					</tr>
					<tr>
						<th>back</th>
						<td>
<pre style="margin-top:0">
$util->back();
$util->back("go to Back");
</pre>
							자바스크립트 history.back() 메서드를 사용하여 페이지 뒤로가기를 할 수 있습니다.<br/>
							인자값을 넣으면 alert 메세지를 띄우고 뒤로가기 합니다.
						</td>
					</tr>
					<tr>
						<th>redirect</th>
						<td>
<pre style="margin-top:0">
$util->redirect("http://google.com", "Go to Google.com");
</pre>
							자바스크립트 location.href() 메서드를 사용하여 페이지 이동합니다. 인자값은 두개 사용되며 이동주소, 메세지 값입니다.
						</td>
					</tr>
					<tr>
						<th>convertDate</th>
						<td>
<pre style="margin-top:0">
echo $util->convertDate("20141215054324"); // result '2014-12-15'
</pre>
							데이터베이스 날짜 데이터를 알기쉬운 형태의 날짜로 바꿔줍니다.
						</td>
					</tr>
					<tr>
						<th>convertTime</th>
						<td>
<pre style="margin-top:0">
echo $util->convertTime("20141215054324"); // result '05:43'
</pre>
							데이터베이스 날짜 데이터를 알기쉬운 형태의 시간으로 바꿔줍니다.
						</td>
					</tr>
					<tr>
						<th>out</th>
						<td>
<pre style="margin-top:0">
echo $util->out(true);
</pre>
							php의 exit함수를 사용하여 중간에 종료시킬 수 있습니다.<br/>
							exit함수와 다른점은 database를 disconnect시키고, 메서드 인자에서 true값을 넣으면 로딩타임을 체크할 수 있습니다.
						</td>
					</tr>
				</tbody>
			</table>
		</section>
		<section>
			<h1>Database.class.php</h1>
			<p>
				이 클래스는 mysql 데이터베이스를 접속하고, 데이터를 가져오거나 입력, 수정, 삭제같은 행동을 할 수 있는 메서드를 제공합니다.<br/>
				메서드들은 <a href="http://kr1.php.net/manual/en/book.pdo.php" target="_blank">PDO</a>클래스를 이용하여 sql접근합니다.<br/>
				주력으로 사용하는 DB 데이터 호출은 Spawn클래스로 사용하기 때문에 간단히 설명은 간소화 하겠습니다.
			</p>
<pre>
require_once('/goose/config/db.config.php');
$db = new Database($dbConfig);
</pre>
			<table class="ui-table">
				<caption>Database 클래스 메서드 기능설명</caption>
				<thead>
					<tr>
						<th scope="col">메서드</th>
						<th scope="col">설명</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>Database</th>
						<td>
							생성자 메서드<br/>
							위의 예제는 클래스 생성자로 db를 접속하고 $db변수로 반환합니다.
<pre style="margin-bottom:0">
$db = new Database(
	array(
		'mysql:dbname=DB_USERNAME;host=localhost',
		'DB_USERNAME',
		'DB_PASSWORD'
	)
);
</pre>
						</td>
					</tr>
					<tr>
						<th>disconnect</th>
						<td>
							db 접속종료
							<pre style="margin-bottom:0">$db->disconnect();</pre>
						</td>
					</tr>
					<tr>
						<th>count</th>
						<td>
							요청한 아이템 총 갯수를 반환해줍니다.
<pre style="margin-bottom:0">
$result = $db->count("select count(*) from TABLE_NAME where KEY=VALUE");
// $result : 10
</pre>
						</td>
					</tr>
					<tr>
						<th>action</th>
						<td>
							insert, update, delete 같은 명령 실행에 사용되는 메서드입니다.
<pre style="margin-bottom:0">
$result = $db->delete("delete from TABLE_NAME where KEY=VALUE");
// $result : success
</pre>
						</td>
					</tr>
					<tr>
						<th>getMultiData</th>
						<td>
							다수의 아이템을 가져옵니다.
<pre style="margin-bottom:0">
$result = $db->getMultiData("select * from TABLE_NAME");
// $result : array()
</pre>
						</td>
					</tr>
					<tr>
						<th>getSingleData</th>
						<td>
							하나의 아이템을 가져옵니다.
<pre style="margin-bottom:0">
$result = $db->getSingleData("select * from TABLE_NAME where KEY=VALUE");
// $result : array()
</pre>
						</td>
					</tr>
				</tbody>
			</table>
		</section>
		<section>
			<h1>Spawn.class.php</h1>
			<p>
				이 클래스는 DB에서 데이터를 가져오는 과정에서 좀더 쉬운 인터페이스를 제공합니다. Spawn 클래스는 규칙에 맞춰진 배열데이터를 해석하여 Database 클래스에 쿼리를 요청하는 동작 프로세스를 가지고 있습니다.<br/>
				Database 클래스를 상속받아 사용하기 때문에 Database 클래스파일을 불러야합니다. 자세한 내용은 아래 소스에서 확인할 수 있습니다.<br/>
			</p>
<pre>
require_once('/goose/libs/Database.class.php');
require_once('/goose/libs/Spawn.class.php');

$spawn = new Spawn(DB_INFOMATION_ARRAY);
</pre>
			<p>아래 메서드 도표에서 확인할 수 있겠지만 대부분 메서드의 인자값은 배열 데이터를 필요로 합니다.</p>
			<table class="ui-table">
				<caption>Database 클래스 메서드 기능설명</caption>
				<thead>
					<tr>
						<th scope="col">메서드</th>
						<th scope="col">설명</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>Spawn</th>
						<td>
							클래스 생성자 메서드
							<pre style="margin-bottom:0">$spawn = new Spawn(DB_INFOMATION_ARRAY);</pre>
						</td>
					</tr>
					<tr>
						<th>disconnect</th>
						<td>
							데이터베이스 접속을 종료합니다.
							<pre style="margin-bottom:0">$spawn->disconnect();</pre>
						</td>
					</tr>
					<tr>
						<th>getQuery</th>
						<td>
							sql 쿼리문으로 출력해줍니다.
<pre style="margin-bottom:0">
$result = $spawn->getQuery(array(
	table => 'TABLE_NAME',
	act => 'select'
));
// $result : select * from TABLE_NAME
</pre>
						</td>
					</tr>
					<tr>
						<th>insert</th>
						<td>
							데이터를 입력합니다. DB테이블 필드를 확인하고 사용하세요.
<pre style="margin-bottom:0">
$result = $spawn->insert(array(
	table => 'TABLE_NAME',
	data => array(
		"KEY=VALUE",
		...
	)
));
// $result : success
</pre>
						</td>
					</tr>
					<tr>
						<th>update</th>
						<td>
							데이터를 수정합니다. DB테이블 필드를 확인하고 사용하세요.
<pre style="margin-bottom:0">
$result = $spawn->update(array(
	table => 'TABLE_NAME',
	where => 'KEY=VALUE',
	data => array(
		"KEY=VALUE",
		...
	)
));
// $result : success
</pre>
						</td>
					</tr>
					<tr>
						<th>delete</th>
						<td>
							데이터를 삭제합니다. DB테이블 필드를 확인하고 사용하세요.
<pre style="margin-bottom:0">
$result = $spawn->delete(array(
	table => 'TABLE_NAME',
	where => 'KEY=VALUE'
));
// $result : success
</pre>
						</td>
					</tr>
					<tr>
						<th>getItems</th>
						<td>
							다수의 데이터 목록을 가져옵니다.
<pre style="margin-bottom:0">
$result = $spawn->getItems(array(
	'table' => 'TABLE_NAME',
	'where' => 'KEY=VALUE',
	'order' => 'srl',
	'sort' => 'desc'
));
</pre>
						</td>
					</tr>
					<tr>
						<th>getItem</th>
						<td>
							하나의 데이터를 가져옵니다.
<pre style="margin-bottom:0">
$result = $spawn->getItem(array(
	table => 'TABLE_NAME',
	where => 'KEY=VALUE'
));
</pre>
						</td>
					</tr>
					<tr>
						<th>getCount</th>
						<td>
							데이터의 갯수를 가져옵니다.
<pre style="margin-bottom:0">
$result = $spawn->getCount(array(
	table => 'TABLE_NAME',
	where => 'KEY=VALUE'
));
</pre>
						</td>
					</tr>
				</tbody>
			</table>
		</section>
		<section>
			<h1>Router.class.php</h1>
			<p>이 클래스는 웹 어플리케이션을 접근하는 주소를 컨트롤하는 역할을 합니다. 자세한 내용은 준비중입니다.</p>
		</section>
		<section>
			<h1>Paginate.class.php</h1>
			<p>이 클래스는 페이지에 따른 데이터 갯수를 잘라 가져오거나 페이지 네비게이션을 만들어줍니다. 자세한 내용은 준비중입니다.</p>
		</section>
	</section>

	<section id="HelpAppGuide">
		<h1>앱 제작 가이드</h1>
		<p>
			goose 프로그램을 이용하여 앱 제작에 필요한 기본적인 세팅과 데이터를 가져오는 도구들을 사용하는 방법에 대하여 설명합니다.<br/>
			좀더 자세한 사항은 샘플 앱 소스파일을 열어서 확인하시길 바라며 이 내용은 api를 이용한 개발에는 필요하지 않습니다.
		</p>
		<section>
			<h1>GOOSE값 정의</h1>
			<p>goose 프로그램 대부분의 파일에서 GOOSE값이 있는지 확인하는 파일이 많습니다. 그래서 아래와 같이 상수값을 정의합니다.</p>
			<pre>define('GOOSE', true);</pre>
		</section>
		<section>
			<h1>데이터베이스 접속</h1>
			<p>
				Spawn 클래스를 통하여 데이터를 관리할 수 있습니다.<br/>
				아래와 같은 소스를 참고하여 Spawn 클래스를 초기화하여 db접속과 객체를 만듭니다.
			</p>
<pre>
require_once('../goose/config/db.config.php');
require_once('../goose/config/variable.php');
require_once('../goose/libs/Database.class.php');
require_once('/goose/libs/Spawn.class.php');

$spawn = new Spawn($dbConfig);
</pre>
			<p>이정도 정의하면 db에 접속하여 $spawn변수로 데이터를 접근할 수 있습니다.</p>
		</section>
		<section>
			<h1>특정 테이블 데이터 출력하기</h1>
			<p>Spawn 클래스를 활용하여 데이터를 출력할 수 있습니다. 위의 Spawn.class.php 기능설명을 참고하여 nests 테이블의 내용을 출력하도록 하겠습니다.</p>
<pre>
// 데이터 총합
$count = $spawn->getCount(array(
	table => $tablesName[nests],
	where => $itemParameter
));

// 데이터 가져오기
$nests = $spawn->getItems(array(
	'table' => $tablesName[nests],
	'order' => 'srl',
	'sort' => 'desc'
));

// 데이터 목록 출력하기
if ($count > 0)
{
	echo "&lt;ul>";
	foreach ($nests as $k=>$v)
	{
		echo "&lt;li>$v[name]&lt;/li>";
	}
	echo "&lt;/ul>";
}
</pre>
		</section>
	</section>
</section>
