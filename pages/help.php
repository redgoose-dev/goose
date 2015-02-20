<section class="Documents">
	<div class="hgroup">
		<h1>Help</h1>
	</div>

	<!-- Introduce -->
	<section class="first" id="HelpIntroduce">
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
	<!-- // Introduce -->

	<!-- Install -->
	<section id="HelpInstall">
		<h1>Install</h1>
		<p>
			Goose 프로그램은 별도의 인스톨 프로그램은 없습니다. 그래서 DB세팅과 파일설치를 수동으로 해야합니다.<br/>
			인스톨에 관한 자세한 내용은 <a href="https://github.com/RedgooseDev/goose" target="_blank">https://github.com/RedgooseDev/goose</a> 페이지를 참고해주세요.
		</p>
	</section>
	<!-- // Install -->

	<!-- Menu -->
	<section id="HelpMenu">
		<h1>Menu</h1>
		<p>
			Goose 프로그램 관리 프로그램을 통해서 홈페이지에 사용되는 데이터를 관리할 수 있는 공간입니다. 이 데이터를 사용하여 외부 웹 프로그램에서 데이터를 출력할 수 있습니다.<br/>
			이 프로그램 관리자는 다음과 같은 주요 메뉴가 있습니다.
		</p>
		<ul>
			<li>
				<strong>Nests</strong><br/>
				이 메뉴는 사이트 앱의 데이터를 보관하고 관리하는 공간으로 하나의 게시물이나 포스트의 집합이라고 할 수 있습니다.<br/>
				nestGroup > nest > article 구조로 구성되어 있으며 article 테이블이 포스팅 데이터가 들어갑니다.
			</li>
			<li>
				<strong>Users</strong><br />
				회원목록입니다. 필요성을 느끼지 못하여 아직 제대로된 개발은 하지 않았지만 기초적인 컨텐츠는 마련해 두었습니다.
			</li>
			<li>
				<strong>JSON</strong><br/>
				다목적으로 사용할 수 있는 json데이터를 만들어서 db에 저장하여 관리하는 공간입니다.
			</li>
			<li>
				<strong>API</strong><br/>
				goose 프로그램은 외부 프로그램을 위한 api를 제공합니다. 자세한 내용은 <a href="<?=GOOSE_ROOT?>/api/">Goose API 안내</a>에서 참고하세요.
			</li>
			<li>
				<strong>Help</strong><br/>
				현재 보고있는 도움말 페이지입니다.
			</li>
		</ul>
	</section>
	<!-- // Menu -->

	<!-- Database -->
	<section id="HelpDatabase">
		<h1>Goose Database</h1>
		<p>
			이 프로그램은 mysql 데이터베이스를 사용하고 있습니다.<br/>
			db 테이블 이름은 설치할 때 지정하는 prefix 문자와 합쳐진 형태입니다. 예) "GOOSE_" + "nests"<br/>
			db 테이블 이름변경이 생길 수 있기 때문에 이름 관리를 <code>/{goose}/data/config/user.php</code> 파일의 <code>$tablesName</code>변수에서 관리하고 있습니다.<br/>
			api를 활용하기 위하여 꼭 참고해야하는 부분입니다.<br/>
			<br/>
			Goose 프로그램의 db 테이블의 자세한 설명은 다음과 같습니다.
		</p>
		<hr class="space" />
		<section>
			<h1>articles</h1>
			<p>포스팅 내용이 담겨있는 가장 중요하고 데이터가 많이 쌓이는 테이블입니다.</p>
			<table class="ui-table">
				<caption class="blind">article 테이블 필드목록</caption>
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
						<th>regdate</th>
						<td class="center nowrap">string</td>
						<td>등록일</td>
					</tr>
					<tr>
						<th>modate</th>
						<td class="center nowrap">string</td>
						<td>수정일</td>
					</tr>
					<tr>
						<th>hit</th>
						<td class="center nowrap">number</td>
						<td>조회수</td>
					</tr>
					<tr>
						<th>json</th>
						<td class="center nowrap">string</td>
						<td>추가변수에 대한 json데이터</td>
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
				<caption class="blind">article 테이블 필드목록</caption>
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
						<td class="center nowrap">string</td>
						<td>등록날짜</td>
					</tr>
				</tbody>
			</table>
		</section>
		<section>
			<h1>files</h1>
			<p>첨부파일들의 정보를 담아둔 테이블입니다.</p>
			<table class="ui-table">
				<caption class="blind">files 테이블 필드목록</caption>
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
				<caption class="blind">nestGroups 테이블 필드목록</caption>
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
						<td class="center nowrap">string</td>
						<td>등록날짜</td>
					</tr>
				</tbody>
			</table>
		</section>
		<section>
			<h1>nests</h1>
			<p>article 데이터의 그룹이 되는 테이블입니다. 이 테이블은 썸네일 이미지 사이즈나 출력갯수, 분류를 사용할것인지의 여부를 저장하는 테이블이 됩니다.</p>
			<table class="ui-table">
				<caption class="blind">nests 테이블 필드목록</caption>
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
							섬네일 이미지로 축소하는 방식 (crop | resizeWidth | resizeHeight)
							
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
						<td>분류기능을 사용할지에 대한 여부 (1 | 0)</td>
					</tr>
					<tr>
						<th>json</th>
						<td class="center nowrap">string</td>
						<td>추가변수에 대한 json데이터</td>
					</tr>
					<tr>
						<th>regdate</th>
						<td class="center nowrap">string</td>
						<td>등록날짜</td>
					</tr>
				</tbody>
			</table>
		</section>
		<section>
			<h1>jsons</h1>
			<p>다목적으로 사용할 수 있는 json데이터를 관리하는 테이블입니다.</p>
			<table class="ui-table">
				<caption class="blind">jsons 테이블 필드목록</caption>
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
						<td class="center nowrap">string</td>
						<td>등록날짜</td>
					</tr>
				</tbody>
			</table>
		</section>
	</section>
	<!-- // Database -->

	<!-- Classes -->
	<section id="HelpClasses">
		<h1>Goose에서 제공하는 Classes</h1>
		<p>Goose 프로그램은 관리자에서 사용하는 몇가지 유용한 클래스들을 사용할 수 있습니다.</p>
		<p>클래스 경로 : <code>/goose/libs/ClassName.class.php</code></p>
		<hr class="space" />
		<section>
			<h1>Util.class.php</h1>
			<p>사이트를 만들때 유용한 작은 메서드들을 모았습니다. Util 클래스 초기화 설정과 예제는 아래 소스를 확인하세요.</p>
<pre class="code"><code>require_once('/goose/libs/Util.class.php');
$util = new Util();

// print console.log
$util->console("Hello world");
</code></pre>
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
<pre class="code" style="margin-top:0"><code>$util->console("Hello world");
$util->console(array(1,2,3));
</code></pre>
							자바스크립트 console.log() 메서드를 사용하여 브라우저 개발자 도구 콘솔에 값을 출력합니다.
						</td>
					</tr>
					<tr>
						<th>back</th>
						<td>
<pre class="code" style="margin-top:0"><code>$util->back();
$util->back("go to Back");
</code></pre>
							자바스크립트 history.back() 메서드를 사용하여 페이지 뒤로가기를 할 수 있습니다.<br/>
							인자값을 넣으면 alert 메세지를 띄우고 뒤로가기 합니다.
						</td>
					</tr>
					<tr>
						<th>redirect</th>
						<td>
							<pre class="code" style="margin-top:0"><code>$util->redirect("http://google.com", "Go to Google.com");</code></pre>
							자바스크립트 location.href() 메서드를 사용하여 페이지 이동합니다. 인자값은 두개 사용되며 이동주소, 메세지 값입니다.
						</td>
					</tr>
					<tr>
						<th>convertDate</th>
						<td>
							<pre class="code" style="margin-top:0"><code>echo $util->convertDate("20141215054324"); // result '2014-12-15'</code></pre>
							데이터베이스 날짜 데이터를 알기쉬운 형태의 날짜로 바꿔줍니다.
						</td>
					</tr>
					<tr>
						<th>convertTime</th>
						<td>
							<pre class="code" style="margin-top:0"><code>echo $util->convertTime("20141215054324"); // result '05:43'</code></pre>
							데이터베이스 날짜 데이터를 알기쉬운 형태의 시간으로 바꿔줍니다.
						</td>
					</tr>
					<tr>
						<th>out</th>
						<td>
							<pre class="code" style="margin-top:0"><code>echo $util->out(true);</code></pre>
							php의 exit함수를 사용하여 중간에 종료시킬 수 있습니다.<br/>
							exit함수와 다른점은 database를 disconnect시키고, 메서드 인자에서 true값을 넣으면 로딩타임을 체크할 수 있습니다.
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
<pre class="code"><code>require_once('/goose/libs/Database.class.php');
require_once('/goose/libs/Spawn.class.php');

$spawn = new Spawn(array('mysql:dbname=DBNAME;host=localhost', 'USERID', 'PASSWORD'));
</code></pre>
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
<pre class="code" style="margin-top:0"><code>$spawn = new Spawn(
	array('mysql:dbname=DBNAME;host=localhost', 'USERID', 'PASSWORD')
);
</code></pre>
						</td>
					</tr>
					<tr>
						<th>disconnect</th>
						<td>
							데이터베이스 접속을 종료합니다.
							<pre class="code" style="margin-top:0"><code>$spawn->disconnect();</code></pre>
						</td>
					</tr>
					<tr>
						<th>getQuery</th>
						<td>
							sql 쿼리문으로 출력해줍니다.
<pre class="code" style="margin-top:0"><code>$result = $spawn->getQuery(array(
	'table' => 'TABLE_NAME',
	'act' => 'select'
));
// $result : select * from TABLE_NAME
</code></pre>
						</td>
					</tr>
					<tr>
						<th>action</th>
						<td>
							순수 sql쿼리문으로 실행합니다.
							<pre style="margin-bottom:0"><code>$result = $spawn->action('select * from nests');</code></pre>
						</td>
					</tr>
					<tr>
						<th>insert</th>
						<td>
							데이터를 입력합니다. DB테이블 필드를 확인하고 사용하세요.
<pre class="code" style="margin-top:0"><code>$result = $spawn->insert(array(
	'table' => 'TABLE_NAME',
	'data' => array(
		"KEY=VALUE",
		...
	)
));
// $result : success
</code></pre>
						</td>
					</tr>
					<tr>
						<th>update</th>
						<td>
							데이터를 수정합니다. DB테이블 필드를 확인하고 사용하세요.
<pre class="code" style="margin-top:0"><code>$result = $spawn->update(array(
	'table' => 'TABLE_NAME',
	'where' => 'KEY=VALUE',
	'data' => array(
		"KEY=VALUE",
		...
	)
));
// $result : success
</code></pre>
						</td>
					</tr>
					<tr>
						<th>delete</th>
						<td>
							데이터를 삭제합니다. DB테이블 필드를 확인하고 사용하세요.
<pre class="code" style="margin-top:0"><code>$result = $spawn->delete(array(
	'table' => 'TABLE_NAME',
	'where' => 'KEY=VALUE'
));
// $result : success
</code></pre>
						</td>
					</tr>
					<tr>
						<th>getItems</th>
						<td>
							다수의 데이터 목록을 가져옵니다.
<pre class="code" style="margin-top:0"><code>
$result = $spawn->getItems(array(
	'table' => 'TABLE_NAME',
	'where' => 'KEY=VALUE',
	'order' => 'srl',
	'sort' => 'desc'
));
</code></pre>
						</td>
					</tr>
					<tr>
						<th>getItem</th>
						<td>
							하나의 데이터를 가져옵니다.
<pre class="code" style="margin-top:0"><code>$result = $spawn->getItem(array(
	'table' => 'TABLE_NAME',
	'where' => 'KEY=VALUE'
));
</code></pre>
						</td>
					</tr>
					<tr>
						<th>getCount</th>
						<td>
							데이터의 갯수를 가져옵니다.
<pre class="code" style="margin-top:0"><code>$result = $spawn->getCount(array(
	'table' => 'TABLE_NAME',
	'where' => 'KEY=VALUE'
));
</code></pre>
						</td>
					</tr>
				</tbody>
			</table>
		</section>
		<section>
			<h1>Router.class.php</h1>
			<p>이 클래스는 웹 어플리케이션을 접근하는 주소를 컨트롤하는 역할을 합니다.</p>
		</section>
		<section>
			<h1>Paginate.class.php</h1>
			<p>이 클래스는 페이지에 따른 데이터 갯수를 잘라 가져오거나 페이지 네비게이션을 만들어줍니다.</p>
		</section>
	</section>
	<!-- // Classes -->

	<!-- App guide -->
	<section id="HelpAppGuide">
		<h1>앱 제작 가이드</h1>
		<p>
			goose 프로그램을 이용하여 앱 제작에 필요한 기본적인 세팅과 데이터를 가져오는 도구들을 사용하는 방법에 대하여 설명합니다.<br/>
			좀더 자세한 사항은 샘플 앱 소스파일을 열어서 확인하시길 바라며 이 내용은 api를 이용한 개발에는 필요하지 않습니다.
		</p>
		<hr class="space" />
		<section>
			<h1>GOOSE값 정의</h1>
			<p>goose 프로그램은 파일 단독으로 실행되는것을 막기 위하여 GOOSE 상수값 확인을 합니다. 그래서 index파일에 아래와 같이 상수값을 정의합니다.</p>
			<pre class="code"><code>define('GOOSE', true);</code></pre>
		</section>
		<section>
			<h1>데이터베이스 접속</h1>
			<p>
				Spawn 클래스를 통하여 데이터를 관리할 수 있습니다.<br/>
				아래와 같은 소스를 참고하여 Spawn 클래스를 초기화하여 db접속과 객체를 만듭니다.
			</p>
<pre class="code"><code>require_once('{goose}/data/config/user.php');
require_once('{goose}/libs/variable.php');
require_once('{goose}/libs/Database.class.php');
require_once('{goose}/libs/Spawn.class.php');

$spawn = new Spawn($dbConfig);
</code></pre>
			<p>이정도 정의하면 db에 접속하여 $spawn변수로 데이터를 접근할 수 있습니다.</p>
		</section>

		<section>
			<h1>특정 테이블 데이터 출력하기</h1>
			<p>Spawn 클래스를 활용하여 데이터를 출력할 수 있습니다. 위의 Spawn.class.php 기능설명을 참고하여 nests 테이블의 내용을 출력하도록 하겠습니다.</p>
<pre class="code"><code>// 데이터 총합
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
</code></pre>
		</section>
	</section>
	<!-- // App guide -->

	<!-- Goose class -->
	<section id="HelpGoose">
		<h1>Goose class</h1>
		<p>
			Goose프로그램의 Util, Database, Spawn 클래스들을 Goose클래스를 통하여 사용할 수 있게 도와주는 Class입니다.<br />
			오로지 Goose클래스를 통하여 다른 클래스를 사용할 수 있기때문에 전역변수가 그만큼 더 줄어들지만 Goose클래스를 통하여 접근해야하기 때문에 호출하는 코드가 길어지는 단점이 있습니다.
		</p>

		<section>
			<h1>Load classes</h1>
			<p>
				Goose 클래스와 그에 필요한 하위 클래스들을 불러옵니다.
			</p>
<pre class="code"><code>// load program files
require_once(PWD.'/libs/Goose.class.php');
require_once(PWD.'/libs/Util.class.php');
require_once(PWD.'/libs/Database.class.php');
require_once(PWD.'/libs/Spawn.class.php');
</code></pre>
		</section>

		<section>
			<h1>init Goose class</h1>
			<p>Goose클래스를 초기화하여 $goose 인스턴스 변수로 만듭니다.</p>
<pre class="code"><code>// init goose class
$goose = Goose::getInstance();
$goose->init(GOOSEPWD);
</code></pre>
		</section>

		<section>
			<h1>public variables</h1>
			<p>사용할 수 있는 변수들의 목록</p>
			<table class="ui-table">
				<caption class="blind">Goose class 내부변수 목록</caption>
				<thead>
					<tr>
						<th>Name</th>
						<th>Type</th>
						<th>Description</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th class="nowrap">util</th>
						<td class="nowrap center">Class</td>
						<td>
							Util Class
							<pre class="code" style="margin:0"><code>$goose->util->console("hello world");</code></pre>
						</td>
					</tr>
					<tr>
						<th class="nowrap">spawn</th>
						<td class="nowrap center">Class</td>
						<td>
							Spawn Class
							<pre class="code" style="margin:0"><code>$goose->spawn->getItems(Array);</code></pre>
						</td>
					</tr>
					<tr>
						<th class="nowrap">tablesName</th>
						<td class="nowrap center">Array</td>
						<td>
							db 테이블목록
							<pre class="code" style="margin:0"><code>$goose->spawn->tablesName[key];</code></pre>
						</td>
					</tr>
					<tr>
						<th class="nowrap">api_key</th>
						<td class="nowrap center">String</td>
						<td>
							api key value
							<pre class="code" style="margin:0"><code>$goose->spawn->api_key;</code></pre>
						</td>
					</tr>
					<tr>
						<th class="nowrap">adminLevel</th>
						<td class="nowrap center">Number</td>
						<td>
							관리자 레벨
							<pre class="code" style="margin:0"><code>$goose->spawn->adminLevel;</code></pre>
						</td>
					</tr>
					<tr>
						<th class="nowrap">out()</th>
						<td class="nowrap center">Method</td>
						<td>
							프로그램 종료시 호출하는 메서드
							<pre class="code" style="margin:0"><code>$goose->spawn->out();</code></pre>
						</td>
					</tr>
					<tr>
						<th class="nowrap">error()</th>
						<td class="nowrap center">Method</td>
						<td>
							프로그램 에러 메서드
							<pre class="code" style="margin:0"><code>$goose->spawn->error();</code></pre>
						</td>
					</tr>
				</tbody>
			</table>
		</section>
	</section>
	<!-- // Goose class -->
</section>
